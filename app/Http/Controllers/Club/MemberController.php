<?php
// Updated: Added avatar file upload functionality with validation, storage, and deletion
// Features: Full CRUD for members with file upload support for avatars
// Fields: name, email, password, avatar (file upload), gender, birthday, identity_card_code, phone, student_code, major_id, course_id, role_id, status
// Modified: Only show child majors (specific majors) in dropdown selections, added file upload handling

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Major;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $members = User::with(['role', 'major', 'course'])
            ->where('club_id', $user->club_id)
            ->paginate(10);

        $roles = Role::where('club_id', $user->club_id)
            ->where('status', 'active')
            ->get();

        $majors = Major::where('status', 'active')
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get();
        $courses = Course::where('status', 'active')->get();

        return view('clubs.members.index', compact('members', 'roles', 'majors', 'courses'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'gender' => 'nullable|in:male,female',
            'birthday' => 'nullable|date',
            'identity_card_code' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'student_code' => 'nullable|string|max:255',
            'major_id' => 'nullable|exists:majors,id',
            'course_id' => 'nullable|exists:courses,id',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive'
        ]);

        $role = Role::where('id', $validatedData['role_id'])
            ->where('club_id', $user->club_id)
            ->first();

        if (!$role) {
            return redirect()->back()->withErrors(['role_id' => 'Vai trò không hợp lệ.']);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validatedData['avatar'] = $avatarPath;
        }

        $validatedData['password'] = Hash::make($validatedData['password']);
        $validatedData['club_id'] = $user->club_id;

        User::create($validatedData);

        return redirect()->route('backend.club.members.index')
            ->with('success', 'Thành viên đã được thêm thành công.');
    }

    public function update(Request $request, User $member)
    {
        $user = Auth::user();

        if ($member->club_id !== $user->club_id) {
            return redirect()->route('backend.club.members.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa thành viên này.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($member->id)
            ],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'gender' => 'nullable|in:male,female',
            'birthday' => 'nullable|date',
            'identity_card_code' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'student_code' => 'nullable|string|max:255',
            'major_id' => 'nullable|exists:majors,id',
            'course_id' => 'nullable|exists:courses,id',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive'
        ]);

        $role = Role::where('id', $validatedData['role_id'])
            ->where('club_id', $user->club_id)
            ->first();

        if (!$role) {
            return redirect()->back()->withErrors(['role_id' => 'Vai trò không hợp lệ.']);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($member->avatar && Storage::disk('public')->exists($member->avatar)) {
                Storage::disk('public')->delete($member->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validatedData['avatar'] = $avatarPath;
        }

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8']);
            $validatedData['password'] = Hash::make($request->password);
        }

        $member->update($validatedData);

        return redirect()->route('backend.club.members.index')
            ->with('success', 'Thành viên đã được cập nhật thành công.');
    }

    public function destroy(User $member)
    {
        $user = Auth::user();

        if ($member->club_id !== $user->club_id) {
            return redirect()->route('backend.club.members.index')
                ->with('error', 'Bạn không có quyền xóa thành viên này.');
        }

        if ($member->role->is_admin == true) {
            return redirect()->route('backend.club.members.index')
                ->with('error', 'Bạn không thể xóa thành viên có vai trò là quản trị viên.');
        }

        if ($member->id === $user->id) {
            return redirect()->route('backend.club.members.index')
                ->with('error', 'Bạn không thể xóa chính mình.');
        }

        // Delete avatar file if exists
        if ($member->avatar && Storage::disk('public')->exists($member->avatar)) {
            Storage::disk('public')->delete($member->avatar);
        }

        $member->delete();

        return redirect()->route('backend.club.members.index')
            ->with('success', 'Thành viên đã được xóa thành công.');
    }
}
