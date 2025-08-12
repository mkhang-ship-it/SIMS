<?php
// Đã cập nhật: Lấy và cập nhật đầy đủ các trường thông tin cá nhân (avatar, name, email, gender, birthday, identity_card_code, phone, student_code, major_id, course_id, role_id). Truyền thêm majors (chỉ chuyên ngành con), courses, roles cho view. Thêm chức năng quản lý thông tin ngân hàng của user. Thêm chức năng quản lý địa chỉ user với chọn tỉnh và xã. Truyền userClubRoles để hiển thị đúng role theo club.
// Sửa: Truyền userClubRoles cho view để hiển thị role theo club.

namespace App\Http\Controllers\Club;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Major;
use App\Models\Course;
use App\Models\Role;
use App\Models\Province;
use App\Models\Commune;

class MyProfileController extends Controller
{
    // Hiển thị trang hồ sơ cá nhân
    public function index()
    {
        $user = Auth::user();
        $majors = Major::whereNotNull('parent_id')->get(); // chỉ lấy chuyên ngành con
        $courses = Course::all();
        $roles = Role::all();
        $provinces = Province::all();
        $bankAccount = $user->bankAccount;
        $address = $user->address;
        $clubs = $user->clubs;
        $userClubRoles = $user->userClubRoles()->with('role')->get();
        return view('clubs.members.my_profile', compact('user', 'majors', 'courses', 'roles', 'provinces', 'bankAccount', 'address', 'clubs', 'userClubRoles'));
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

    // Cập nhật thông tin ngân hàng
    public function updateBankAccount(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'bank_name' => 'required|string|max:255',
            'bank_code' => 'required|string|max:50',
            'bank_bin' => 'required|string|max:50',
            'bank_short_name' => 'required|string|max:100',
            'bank_swift_code' => 'required|string|max:50',
            'bank_account_name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:50',
        ]);

        // Tìm hoặc tạo mới bank account
        $bankAccount = $user->bankAccount()->firstOrNew();

        $bankAccount->fill([
            'bank_name' => $request->bank_name,
            'bank_code' => $request->bank_code,
            'bank_bin' => $request->bank_bin,
            'bank_short_name' => $request->bank_short_name,
            'bank_swift_code' => $request->bank_swift_code,
            'bank_account_name' => $request->bank_account_name,
            'bank_account_number' => $request->bank_account_number,
        ]);

        $bankAccount->user_id = $user->id;
        $bankAccount->save();

        return back()->with('success', 'Cập nhật thông tin ngân hàng thành công!');
    }

    // Cập nhật địa chỉ
    public function updateAddress(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'province_id' => 'required|exists:provinces,id',
            'commune_id' => 'required|exists:communes,id',
            'address' => 'required|string|max:500',
        ]);

        // Tìm hoặc tạo mới address
        $address = $user->address()->firstOrNew();

        $address->fill([
            'province_id' => $request->province_id,
            'commune_id' => $request->commune_id,
            'address' => $request->address,
        ]);

        $address->user_id = $user->id;
        $address->save();

        return back()->with('success', 'Cập nhật địa chỉ thành công!');
    }

    // Lấy danh sách xã theo tỉnh (AJAX)
    public function getCommunes(Request $request)
    {
        $provinceId = $request->province_id;
        $communes = Commune::where('province_id', $provinceId)->get();
        return response()->json($communes);
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
