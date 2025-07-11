<?php
// Created SlotController based on CourseController template
// Added proper validation for slot fields: name, start_time, end_time, status

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slot;

class SlotController extends Controller
{
    public function index()
    {
        $slots = Slot::orderBy('created_at', 'desc')->get();
        return view('slots.index', compact('slots'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'status' => 'required|string|in:active,inactive',
        ]);

        $slot = Slot::create([
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => $request->status,
        ]);

        return redirect()->route('backend.slots.index')->with('success', 'Ca học đã được tạo thành công!');
    }

    public function update(Request $request, Slot $slot)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'status' => 'required|string|in:active,inactive',
        ]);

        $slot->update([
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => $request->status,
        ]);

        return redirect()->route('backend.slots.index')->with('success', 'Ca học đã được cập nhật thành công!');
    }

    public function destroy(Slot $slot)
    {
        $slot->delete();

        return redirect()->route('backend.slots.index')->with('success', 'Ca học đã được xóa thành công!');
    }
}
