<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShortUrl;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ShortUrlController extends Controller
{
    public function index()
    {
        $shortUrls = ShortUrl::with(['visits'])
            ->ofType('central')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('short-urls.index', compact('shortUrls'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'original_url' => 'required|url|max:2048',
            'title' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date|after:now',
        ]);

        // Tạo short code ngẫu nhiên
        do {
            $shortCode = Str::random(12);
        } while (ShortUrl::where('short_code', $shortCode)->exists());

        $shortUrl = ShortUrl::create([
            'type' => 'central',
            'original_url' => $request->original_url,
            'short_code' => $shortCode,
            'title' => $request->title,
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->route('backend.short-urls.index')
            ->with('success', 'URL rút gọn đã được tạo thành công!');
    }

    public function update(Request $request, ShortUrl $shortUrl)
    {
        $request->validate([
            'original_url' => 'required|url|max:2048',
            'title' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $shortUrl->update([
            'original_url' => $request->original_url,
            'title' => $request->title,
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->route('backend.short-urls.index')
            ->with('success', 'URL rút gọn đã được cập nhật thành công!');
    }

    public function destroy(ShortUrl $shortUrl)
    {
        $shortUrl->delete();

        return redirect()->route('backend.short-urls.index')
            ->with('success', 'URL rút gọn đã được xóa thành công!');
    }

    public function downloadQr(ShortUrl $shortUrl)
    {
        $qrCode = QrCode::size(300)
            ->format('svg')
            ->generate(route('short-url.redirect', $shortUrl->short_code));

        $filename = 'qr-' . $shortUrl->short_code . '.svg';

        return response($qrCode)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function redirect($shortCode)
    {
        $shortUrl = ShortUrl::where('short_code', $shortCode)->first();

        if (!$shortUrl) {
            abort(404, 'URL không tồn tại.');
        }

        if ($shortUrl->isExpired()) {
            abort(410, 'URL đã hết hạn.');
        }

        // Ghi lại lượt truy cập
        $shortUrl->visits()->create([
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referrer' => request()->header('referer'),
            'visited_at' => now(),
        ]);

        return redirect($shortUrl->original_url);
    }
}
