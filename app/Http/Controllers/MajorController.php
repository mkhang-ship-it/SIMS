<?php
// Changed: Modified to display all majors in table view instead of only child majors

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Major;

class MajorController extends Controller
{
    public function index()
    {
        // Get all majors for main display
        $majors = Major::with('parent')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all majors for parent selection dropdowns
        $allMajors = Major::orderBy('name')->get();

        return view('majors.index', compact('majors', 'allMajors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:majors,id',
            'status' => 'required|string|in:active,inactive',
        ]);

        $major = Major::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'status' => $request->status,
        ]);

        return redirect()->route('backend.majors.index')->with('success', 'Chuyên ngành đã được tạo thành công!');
    }

    public function update(Request $request, Major $major)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:majors,id',
            'status' => 'required|string|in:active,inactive',
        ]);

        // Prevent self-reference
        if ($request->parent_id === $major->id) {
            return redirect()->back()->with('error', 'Chuyên ngành không thể là cha của chính nó!');
        }

        $major->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'status' => $request->status,
        ]);

        return redirect()->route('backend.majors.index')->with('success', 'Chuyên ngành đã được cập nhật thành công!');
    }

    public function destroy(Major $major)
    {
        // Check if major has children
        if ($major->children()->count() > 0) {
            return redirect()->route('backend.majors.index')->with('error', 'Không thể xóa chuyên ngành có chuyên ngành con!');
        }

        $major->delete();

        return redirect()->route('backend.majors.index')->with('success', 'Chuyên ngành đã được xóa thành công!');
    }
}
