<?php

namespace App\Http\Controllers;

use App\Models\LuckyDraw;
use App\Models\Prize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrizeController extends Controller
{
    public function index(LuckyDraw $luckyDraw)
    {
        // Check if lucky draw is central type and belongs to no club
        if ($luckyDraw->type !== 'central' || $luckyDraw->club_id !== null) {
            abort(403, 'Unauthorized access to this lucky draw.');
        }

        $prizes = $luckyDraw->prizes;

        if (request()->expectsJson()) {
            return response()->json(collect($prizes)->map(function ($prize) {
                return [
                    'id' => $prize->id,
                    'name' => $prize->name,
                    'image' => $prize->image_url,
                    'quantity' => $prize->quantity,
                    'remaining_quantity' => $prize->remaining_quantity,
                    'order' => $prize->order,
                    'special_gift' => $prize->special_gift
                ];
            }));
        }

        return view('lucky-draws.prizes.index', compact('luckyDraw', 'prizes'));
    }

    // Updated: Set 'order' for new prize to be the next available order value for the lucky draw
    // Updated: Ensure only one prize can be marked as 'special_gift' per lucky draw. When a prize is set as special, all others are unset.

    public function store(Request $request, LuckyDraw $luckyDraw)
    {
        // Check if lucky draw is central type and belongs to no club
        if ($luckyDraw->type !== 'central' || $luckyDraw->club_id !== null) {
            abort(403, 'Unauthorized access to this lucky draw.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'quantity' => 'required|integer|min:1',
        ]);

        // Determine the next order value
        $maxOrder = $luckyDraw->prizes()->max('order');
        $nextOrder = is_null($maxOrder) ? 1 : $maxOrder + 1;

        $isSpecialGift = $request->has('special_gift');

        $data = [
            'name' => $request->name,
            'quantity' => $request->quantity,
            'lucky_draw_id' => $luckyDraw->id,
            'special_gift' => $isSpecialGift,
            'order' => $nextOrder
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('prizes', 'public');
            $data['image'] = $imagePath;
        }

        // If this prize is set as special_gift, unset special_gift for all other prizes in this lucky draw
        if ($isSpecialGift) {
            Prize::where('lucky_draw_id', $luckyDraw->id)
                ->where('special_gift', true)
                ->update(['special_gift' => false]);
        }

        Prize::create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Giải thưởng đã được thêm thành công!'
            ]);
        }

        return redirect()->route('backend.lucky-draws.prizes.index', $luckyDraw)
            ->with('success', 'Giải thưởng đã được thêm thành công!');
    }

    public function update(Request $request, LuckyDraw $luckyDraw, Prize $prize)
    {
        // Check if lucky draw is central type and belongs to no club
        if ($luckyDraw->type !== 'central' || $luckyDraw->club_id !== null) {
            abort(403, 'Unauthorized access to this lucky draw.');
        }

        // Check if prize belongs to this lucky draw
        if ($prize->lucky_draw_id !== $luckyDraw->id) {
            abort(404, 'Prize not found.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'quantity' => 'required|integer|min:1',
            'order' => 'nullable|integer|min:0',
            'special_gift' => 'nullable|boolean'
        ]);

        $isSpecialGift = $request->has('special_gift');

        $data = [
            'name' => $request->name,
            'quantity' => $request->quantity,
            'order' => $request->order ?? 0,
            'special_gift' => $isSpecialGift
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($prize->image) {
                Storage::disk('public')->delete($prize->image);
            }

            $imagePath = $request->file('image')->store('prizes', 'public');
            $data['image'] = $imagePath;
        }

        // If this prize is set as special_gift, unset special_gift for all other prizes in this lucky draw
        if ($isSpecialGift) {
            Prize::where('lucky_draw_id', $luckyDraw->id)
                ->where('id', '!=', $prize->id)
                ->where('special_gift', true)
                ->update(['special_gift' => false]);
        }

        $prize->update($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Giải thưởng đã được cập nhật thành công!'
            ]);
        }

        return redirect()->route('backend.lucky-draws.prizes.index', $luckyDraw)
            ->with('success', 'Giải thưởng đã được cập nhật thành công!');
    }

    public function destroy(LuckyDraw $luckyDraw, Prize $prize)
    {
        // Check if lucky draw is central type and belongs to no club
        if ($luckyDraw->type !== 'central' || $luckyDraw->club_id !== null) {
            abort(403, 'Unauthorized access to this lucky draw.');
        }

        // Check if prize belongs to this lucky draw
        if ($prize->lucky_draw_id !== $luckyDraw->id) {
            abort(404, 'Prize not found.');
        }

        // Delete image if exists
        if ($prize->image) {
            Storage::disk('public')->delete($prize->image);
        }

        $prize->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Giải thưởng đã được xóa thành công!'
            ]);
        }

        return redirect()->route('backend.lucky-draws.prizes.index', $luckyDraw)
            ->with('success', 'Giải thưởng đã được xóa thành công!');
    }

    public function reorder(Request $request, LuckyDraw $luckyDraw)
    {
        // Check if lucky draw is central type and belongs to no club
        if ($luckyDraw->type !== 'central' || $luckyDraw->club_id !== null) {
            abort(403, 'Unauthorized access to this lucky draw.');
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|string|exists:prizes,id',
            'items.*.order' => 'required|integer|min:1'
        ]);

        try {
            // Update order for each prize
            foreach ($request->items as $item) {
                $prize = Prize::where('id', $item['id'])
                    ->where('lucky_draw_id', $luckyDraw->id)
                    ->first();

                if ($prize) {
                    $prize->update(['order' => $item['order']]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Thứ tự giải thưởng đã được cập nhật thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật thứ tự giải thưởng!'
            ], 500);
        }
    }
}
