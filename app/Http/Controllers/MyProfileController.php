<?php
// Đã cập nhật: Lấy và cập nhật đầy đủ các trường thông tin cá nhân (avatar, name, email, gender, birthday, identity_card_code, phone, student_code, major_id, course_id, role_id). Truyền thêm majors (chỉ chuyên ngành con), courses, roles cho view.

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Major;
use App\Models\Course;
use App\Models\Role;

class MyProfileController extends Controller
{
    // Hiển thị trang hồ sơ cá nhân
    public function index()
    {
        $user = Auth::user();
        $majors = Major::whereNotNull('parent_id')->get(); // chỉ lấy chuyên ngành con
        $courses = Course::all();
        $roles = Role::all();
        $clubs = $user->clubs; // <--- thêm dòng này
        return view('users.my_profile', compact('user', 'majors', 'courses', 'roles', 'clubs'));
    }

    // Cập nhật thông tin cá nhân
    public function update(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'gender' => 'nullable|in:male,female',
            'birthday' => 'nullable|date',
            'identity_card_code' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'student_code' => 'nullable|string|max:50|unique:users,student_code,' . $user->id,
            'major_id' => 'nullable|exists:majors,id',
            'course_id' => 'nullable|exists:courses,id',
            'role_id' => 'required|exists:roles,id',
            'avatar' => 'nullable|image|max:2048',
        ]);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->gender = $request->gender;
        $user->birthday = $request->birthday;
        $user->identity_card_code = $request->identity_card_code;
        $user->phone = $request->phone;
        $user->student_code = $request->student_code;
        $user->major_id = $request->major_id;
        $user->course_id = $request->course_id;
        $user->role_id = $request->role_id;
        if ($request->has('default_club_id')) {
            $user->default_club_id = $request->default_club_id;
        }
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }
        $user->save();
        return back()->with('success', 'Cập nhật thông tin thành công!');
    }

    // Đổi mật khẩu
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng']);
        }
        $user->password = Hash::make($request->password);
        $user->save();
        return back()->with('success', 'Đổi mật khẩu thành công!');
    }

    // Xóa phiên cụ thể
    public function destroySession($sessionId)
    {
        $user = Auth::user();

        // Chỉ cho phép xóa phiên của chính mình
        $session = DB::table('sessions')
            ->where('id', $sessionId)
            ->where('user_id', $user->id)
            ->first();

        if (!$session) {
            return back()->with('error', 'Không tìm thấy phiên hoặc không có quyền xóa!');
        }

        DB::table('sessions')->where('id', $sessionId)->delete();

        return back()->with('success', 'Đã xóa phiên thành công!');
    }

    // Xóa tất cả phiên
    public function destroyAllSessions()
    {
        $user = Auth::user();

        // Xóa tất cả phiên của user hiện tại
        DB::table('sessions')->where('user_id', $user->id)->delete();

        return back()->with('success', 'Đã xóa tất cả phiên thành công!');
    }
}
