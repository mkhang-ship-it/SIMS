<?php
// Updated: Hiển thị thành viên theo bảng UserClubRole dựa vào session('club_id'), eager load user, major, course, role
// Features: Full CRUD for members với file upload avatar, hiển thị thành viên theo club qua bảng user_club_roles
// Update: Khi chỉnh sửa thành viên, cập nhật đồng thời bảng users và user_club_roles (role, status)
// Fields: name, email, password, avatar (file upload), gender, birthday, identity_card_code, phone, student_code, major_id, course_id, role_id, status
// Modified: Chỉ hiển thị chuyên ngành con trong dropdown, thêm xử lý file upload, lấy thành viên qua UserClubRole

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Major;
use App\Models\Course;
use App\Models\UserClubRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function index()
    {
        $clubId = session('club_id');
        $user = Auth::user();

        // Lấy danh sách UserClubRole theo club_id, eager load user, major, course, role
        $members = UserClubRole::with(['user.major', 'user.course', 'role'])
            ->where('club_id', $clubId)
            ->get();

        $roles = Role::where('club_id', $clubId)
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

    public function update(Request $request, $userId)
    {
        $clubId = session('club_id');
        $user = Auth::user();

        $member = User::findOrFail($userId);
        $userClubRole = UserClubRole::where('user_id', $userId)->where('club_id', $clubId)->first();
        if (!$userClubRole) {
            return redirect()->route('backend.club.members.index')
                ->with('error', 'Không tìm thấy thành viên trong câu lạc bộ này.');
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
            ->where('club_id', $clubId)
            ->first();
        if (!$role) {
            return redirect()->back()->withErrors(['role_id' => 'Vai trò không hợp lệ.']);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            if ($member->avatar && Storage::disk('public')->exists($member->avatar)) {
                Storage::disk('public')->delete($member->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $member->avatar = $avatarPath;
        }

        // Update user fields
        $member->name = $validatedData['name'];
        $member->email = $validatedData['email'];
        $member->gender = $validatedData['gender'] ?? null;
        $member->birthday = $validatedData['birthday'] ?? null;
        $member->identity_card_code = $validatedData['identity_card_code'] ?? null;
        $member->phone = $validatedData['phone'] ?? null;
        $member->student_code = $validatedData['student_code'] ?? null;
        $member->major_id = $validatedData['major_id'] ?? null;
        $member->course_id = $validatedData['course_id'] ?? null;
        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8']);
            $member->password = Hash::make($request->password);
        }
        $member->save();

        // Update UserClubRole fields
        $userClubRole->role_id = $validatedData['role_id'];
        $userClubRole->status = $validatedData['status'];
        $userClubRole->save();

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
