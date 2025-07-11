<?php
// Created SemesterController based on CourseController template
// Controller for managing semesters with CRUD operations

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Semester;

class SemesterController extends Controller
{
    // Updated SemesterController - Fixed validation rules to match database enum values
    // Removed 'completed' status from validation as it's not in the database enum

    public function index()
    {
        $semesters = Semester::orderBy('created_at', 'desc')->get();
        return view('semesters.index', compact('semesters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|string|in:active,inactive',
        ]);

        $semester = Semester::create([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
        ]);

        return redirect()->route('backend.semesters.index')->with('success', 'Học kỳ đã được tạo thành công!');
    }

    public function update(Request $request, Semester $semester)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|string|in:active,inactive',
        ]);

        $semester->update([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
        ]);

        return redirect()->route('backend.semesters.index')->with('success', 'Học kỳ đã được cập nhật thành công!');
    }

    public function destroy(Semester $semester)
    {
        $semester->delete();

        return redirect()->route('backend.semesters.index')->with('success', 'Học kỳ đã được xóa thành công!');
    }
}
