<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::orderBy('created_at', 'desc')->get();
        return view('courses.index', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|string|in:active,inactive',
        ]);

        $course = Course::create([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
        ]);

        return redirect()->route('backend.courses.index')->with('success', 'Khóa học đã được tạo thành công!');
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|string|in:active,inactive',
        ]);

        $course->update([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
        ]);

        return redirect()->route('backend.courses.index')->with('success', 'Khóa học đã được cập nhật thành công!');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('backend.courses.index')->with('success', 'Khóa học đã được xóa thành công!');
    }
}
