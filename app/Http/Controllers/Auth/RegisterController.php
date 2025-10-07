<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\User;
use App\Models\Major;
use App\Models\Course;
use App\Models\UserClubRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    public function showRegistrationForm($token)
    {
        $club = Club::where('registration_token', $token)->first();

        if (!$club) {
            return redirect()->route('login')->with('error', 'Liên kết đăng ký không hợp lệ.');
        }

        if (!$club->isRegistrationOpen()) {
            return redirect()->route('login')->with('error', 'Trang đăng ký đã đóng.');
        }

        $majors = Major::whereNotNull('parent_id')->orderBy('name')->get();
        $courses = Course::orderBy('name')->get();

        return view('auth.register', compact('club', 'majors', 'courses'));
    }

    public function register(Request $request, $token)
    {
        $club = Club::where('registration_token', $token)->first();

        if (!$club) {
            return redirect()->route('login')->with('error', 'Liên kết đăng ký không hợp lệ.');
        }

        if (!$club->isRegistrationOpen()) {
            return redirect()->route('login')->with('error', 'Trang đăng ký đã đóng.');
        }

        // Kiểm tra xem user đã tồn tại chưa
        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser) {
            // User đã tồn tại, kiểm tra xem đã tham gia club này chưa
            $existingMembership = UserClubRole::where('user_id', $existingUser->id)
                ->where('club_id', $club->id)
                ->first();

            if ($existingMembership) {
                return redirect()->back()
                    ->with('error', 'Bạn đã tham gia câu lạc bộ này rồi.')
                    ->withInput($request->except('password', 'password_confirmation'));
            }

            // User tồn tại nhưng chưa tham gia club này, chỉ thêm vào club
            try {
                UserClubRole::create([
                    'user_id' => $existingUser->id,
                    'club_id' => $club->id,
                    'role_id' => $club->default_role_id,
                    'joined_at' => now(),
                    'status' => 'pending',
                ]);

                return redirect()->route('login')->with('success', 'Đã thêm bạn vào câu lạc bộ! Vui lòng đợi phê duyệt từ ban chủ nhiệm câu lạc bộ.');
            } catch (\Exception $e) {
                Log::error($e);
                return redirect()->back()
                    ->with('error', 'Có lỗi xảy ra trong quá trình thêm vào câu lạc bộ. Vui lòng thử lại.')
                    ->withInput($request->except('password', 'password_confirmation'));
            }
        }

        // User chưa tồn tại, tạo tài khoản mới
        // Validate dữ liệu cho user mới
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'gender' => ['required', 'in:male,female'],
            'birthday' => ['required', 'date', 'before:today'],
            'phone' => ['required', 'string', 'max:20'], // Removed unique validation for phone
            'student_code' => ['required', 'string', 'max:50', 'unique:users'],
            'major_id' => ['required', 'exists:majors,id'],
            'course_id' => ['required', 'exists:courses,id'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'name.max' => 'Họ và tên không được vượt quá 255 ký tự.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã được sử dụng.',
            'gender.required' => 'Vui lòng chọn giới tính.',
            'gender.in' => 'Giới tính không hợp lệ.',
            'birthday.required' => 'Vui lòng nhập ngày sinh.',
            'birthday.date' => 'Ngày sinh không đúng định dạng.',
            'birthday.before' => 'Ngày sinh phải trước ngày hiện tại.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'student_code.required' => 'Vui lòng nhập mã sinh viên.',
            'student_code.unique' => 'Mã sinh viên đã được sử dụng.',
            'major_id.required' => 'Vui lòng chọn chuyên ngành.',
            'major_id.exists' => 'Chuyên ngành không tồn tại.',
            'course_id.required' => 'Vui lòng chọn khóa học.',
            'course_id.exists' => 'Khóa học không tồn tại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        try {
            // Tạo user mới
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'gender' => $request->gender,
                'birthday' => $request->birthday,
                'phone' => $request->phone,
                'student_code' => $request->student_code,
                'major_id' => $request->major_id,
                'course_id' => $request->course_id,
                'password' => Hash::make($request->password),
                'type' => 'branch',
                'status' => 'active',
                'default_club_id' => $club->id,
            ]);

            // Tạo user_club_role để user tham gia club
            UserClubRole::create([
                'user_id' => $user->id,
                'club_id' => $club->id,
                'role_id' => $club->default_role_id,
                'joined_at' => now(),
                'status' => 'pending',
            ]);

            return redirect()->route('login')->with('success', 'Đăng ký thành công! Vui lòng đợi phê duyệt từ ban chủ nhiệm câu lạc bộ.');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra trong quá trình đăng ký. Vui lòng thử lại.')
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }
}