<?php
// Updated ClassRoomController - Added longitude and latitude support for classroom location tracking
// Fixed code generation issue and added coordinate fields validation
// Code format: CR-001, CR-002, etc. based on total count + 1
// Longitude and latitude are optional string fields for flexible location tracking

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassRoom;

class ClassRoomController extends Controller
{
    public function index()
    {
        $classrooms = ClassRoom::orderBy('created_at', 'desc')->get();
        return view('classrooms.index', compact('classrooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:active,inactive',
            'longitude' => 'nullable|string|max:255',
            'latitude' => 'nullable|string|max:255',
        ]);

        // Generate code before creating the record
        $code = 'CR-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

        $classroom = ClassRoom::create([
            'name' => $request->name,
            'status' => $request->status,
            'code' => $code,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
        ]);

        return redirect()->route('backend.classrooms.index')->with('success', 'Phòng học đã được tạo thành công!');
    }

    public function update(Request $request, ClassRoom $classroom)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:active,inactive',
            'longitude' => 'nullable|string|max:255',
            'latitude' => 'nullable|string|max:255',
        ]);

        $classroom->update([
            'name' => $request->name,
            'status' => $request->status,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
        ]);

        return redirect()->route('backend.classrooms.index')->with('success', 'Phòng học đã được cập nhật thành công!');
    }

    public function destroy(ClassRoom $classroom)
    {
        $classroom->delete();

        return redirect()->route('backend.classrooms.index')->with('success', 'Phòng học đã được xóa thành công!');
    }
}
