<?php

namespace App\Http\Controllers;

use App\Models\LuckyDraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class LuckyDrawController extends Controller
{
    public function index()
    {
        $luckyDraws = LuckyDraw::where('type', 'central')
            ->whereNull('club_id')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('lucky-draws.index', compact('luckyDraws'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'participants' => 'nullable|string',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'modal_background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
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

        $modalBackgroundImagePath = null;
        if ($request->hasFile('modal_background_image')) {
            $modalBackgroundImagePath = $request->file('modal_background_image')->store('lucky-draws/modal-backgrounds', 'public');
        }

        // Generate security code only if field is not submitted, allow empty string to be saved as null
        $securityCode = $request->has('security_code') ? ($request->security_code ?: null) : Str::random(12);

        $luckyDraw = LuckyDraw::create([
            'club_id' => null,
            'type' => 'central',
            'title' => $request->title,
            'slug' => Str::random(12),
            'security_code' => $securityCode,
            'participants' => $participants,
            'winners' => [],
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active'),
            'created_by' => Auth::user()->id,
            'background_image' => $backgroundImagePath,
            'modal_background_image' => $modalBackgroundImagePath
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Lucky Draw đã được tạo thành công!',
                'data' => $luckyDraw
            ]);
        }

        return redirect()->route('backend.lucky-draws.index')
            ->with('success', 'Lucky Draw đã được tạo thành công!');
    }

    public function show(LuckyDraw $luckyDraw)
    {
        // Check if lucky draw is central type and belongs to no club
        if ($luckyDraw->type !== 'central' || $luckyDraw->club_id !== null) {
            abort(403, 'Unauthorized access to this lucky draw.');
        }

        return view('lucky-draws.show', compact('luckyDraw'));
    }

    public function update(Request $request, LuckyDraw $luckyDraw)
    {
        // Check if lucky draw is central type and belongs to no club
        if ($luckyDraw->type !== 'central' || $luckyDraw->club_id !== null) {
            abort(403, 'Unauthorized access to this lucky draw.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'participants' => 'nullable|string',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'modal_background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
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

        $modalBackgroundImagePath = $luckyDraw->modal_background_image;
        if ($request->hasFile('modal_background_image')) {
            // Delete old modal background image if exists
            if ($luckyDraw->modal_background_image) {
                Storage::disk('public')->delete($luckyDraw->modal_background_image);
            }
            // Store new modal background image
            $modalBackgroundImagePath = $request->file('modal_background_image')->store('lucky-draws/modal-backgrounds', 'public');
        }

        // Handle security code: use submitted value (including empty/null), keep existing, or generate if none exist
        $securityCode = $request->has('security_code') ?
            ($request->security_code ?: null) : ($luckyDraw->security_code ?: Str::random(12));

        $luckyDraw->update([
            'title' => $request->title,
            'security_code' => $securityCode,
            'participants' => $participants,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active'),
            'background_image' => $backgroundImagePath,
            'modal_background_image' => $modalBackgroundImagePath
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Lucky Draw đã được cập nhật thành công!',
                'data' => $luckyDraw
            ]);
        }

        return redirect()->route('backend.lucky-draws.index')
            ->with('success', 'Lucky Draw đã được cập nhật thành công!');
    }

    public function destroy(LuckyDraw $luckyDraw)
    {
        // Check if lucky draw is central type and belongs to no club
        if ($luckyDraw->type !== 'central' || $luckyDraw->club_id !== null) {
            abort(403, 'Unauthorized access to this lucky draw.');
        }

        // Delete background image if exists
        if ($luckyDraw->background_image) {
            Storage::disk('public')->delete($luckyDraw->background_image);
        }

        // Delete modal background image if exists
        if ($luckyDraw->modal_background_image) {
            Storage::disk('public')->delete($luckyDraw->modal_background_image);
        }

        $luckyDraw->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Lucky Draw đã được xóa thành công!'
            ]);
        }

        return redirect()->route('backend.lucky-draws.index')
            ->with('success', 'Lucky Draw đã được xóa thành công!');
    }

    public function toggleActive($id)
    {
        $luckyDraw = LuckyDraw::find($id);
        // Check if lucky draw is central type and belongs to no club
        if ($luckyDraw->type !== 'central' || $luckyDraw->club_id !== null) {
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
