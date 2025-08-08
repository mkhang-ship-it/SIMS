<?php
// Updated: Added security_code field to LuckyDrawController validation and create/update methods with auto-generation

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\LuckyDraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class LuckyDrawController extends Controller
{

    public function index()
    {
        $luckyDraws = LuckyDraw::where('club_id', session('club_id'))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('clubs.lucky-draws.index', compact('luckyDraws'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'participants' => 'nullable|string',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'security_code' => 'nullable|string|max:50'
        ]);

        $participants = [];
        if ($request->participants) {
            $participantLines = array_filter(explode("\n", trim($request->participants)));
            foreach ($participantLines as $index => $participant) {
                $participants[$index + 1] = trim($participant);
            }
        }

        $backgroundImagePath = null;
        if ($request->hasFile('background_image')) {
            $backgroundImagePath = $request->file('background_image')->store('lucky-draws/backgrounds', 'public');
        }

        // Generate security code if not provided
        $securityCode = $request->security_code ?: Str::random(12);

        $luckyDraw = LuckyDraw::create([
            'club_id' => session('club_id'),
            'title' => $request->title,
            'slug' => Str::random(12),
            'security_code' => $securityCode,
            'participants' => $participants,
            'winners' => [],
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active'),
            'created_by' => Auth::user()->id,
            'background_image' => $backgroundImagePath
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Lucky Draw đã được tạo thành công!',
                'data' => $luckyDraw
            ]);
        }

        return redirect()->route('backend.club.lucky-draws.index')
            ->with('success', 'Lucky Draw đã được tạo thành công!');
    }

    public function show(LuckyDraw $luckyDraw)
    {
        // Check if lucky draw belongs to current club
        if ($luckyDraw->club_id !== session('club_id')) {
            abort(403, 'Unauthorized access to this lucky draw.');
        }

        return view('clubs.lucky-draws.show', compact('luckyDraw'));
    }


    public function update(Request $request, LuckyDraw $luckyDraw)
    {
        // Check if lucky draw belongs to current club
        if ($luckyDraw->club_id !== session('club_id')) {
            abort(403, 'Unauthorized access to this lucky draw.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'participants' => 'nullable|string',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'security_code' => 'nullable|string|max:50'
        ]);

        $participants = [];
        if ($request->participants) {
            $participantLines = array_filter(explode("\n", trim($request->participants)));
            foreach ($participantLines as $index => $participant) {
                $participants[$index + 1] = trim($participant);
            }
        }

        $backgroundImagePath = $luckyDraw->background_image;
        if ($request->hasFile('background_image')) {
            // Delete old image if exists
            if ($luckyDraw->background_image) {
                Storage::disk('public')->delete($luckyDraw->background_image);
            }
            // Store new image
            $backgroundImagePath = $request->file('background_image')->store('lucky-draws/backgrounds', 'public');
        }

        // Generate security code if not provided and doesn't exist
        $securityCode = $request->security_code ?: ($luckyDraw->security_code ?: Str::random(12));

        $luckyDraw->update([
            'title' => $request->title,
            'security_code' => $securityCode,
            'participants' => $participants,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active'),
            'background_image' => $backgroundImagePath
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Lucky Draw đã được cập nhật thành công!',
                'data' => $luckyDraw
            ]);
        }

        return redirect()->route('backend.club.lucky-draws.index')
            ->with('success', 'Lucky Draw đã được cập nhật thành công!');
    }

    public function destroy(LuckyDraw $luckyDraw)
    {
        // Check if lucky draw belongs to current club
        if ($luckyDraw->club_id !== session('club_id')) {
            abort(403, 'Unauthorized access to this lucky draw.');
        }

        // Delete background image if exists
        if ($luckyDraw->background_image) {
            Storage::disk('public')->delete($luckyDraw->background_image);
        }

        $luckyDraw->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Lucky Draw đã được xóa thành công!'
            ]);
        }

        return redirect()->route('backend.club.lucky-draws.index')
            ->with('success', 'Lucky Draw đã được xóa thành công!');
    }

    public function toggleActive($id)
    {
        $luckyDraw = LuckyDraw::find($id);
        // Check if lucky draw belongs to current club
        if ($luckyDraw->club_id !== session('club_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this lucky draw.'
            ], 403);
        }

        $luckyDraw->update(['is_active' => !$luckyDraw->is_active]);

        return response()->json([
            'success' => true,
            'message' => $luckyDraw->is_active ? 'Lucky Draw đã được kích hoạt!' : 'Lucky Draw đã được tạm dừng!',
            'is_active' => $luckyDraw->is_active
        ]);
    }
}
