<?php

// LoginController.php - Updated to provide specific error messages and account status checking
// Methods: showLoginForm(), login(), logout() for handling user authentication
// Added specific error messages for email not found vs wrong password, removed remember functionality
// Added account status validation to prevent inactive users from logging in

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Validate the form data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Check if user exists with this email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Email not found
            throw ValidationException::withMessages([
                'email' => ['Email đã nhập không tồn tại trong hệ thống.'],
            ]);
        }

        // Check if password is correct
        if (!Hash::check($request->password, $user->password)) {
            // Wrong password
            throw ValidationException::withMessages([
                'password' => ['Mật khẩu đã nhập không chính xác.'],
            ]);
        }

        // Check if account is active
        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => ['Tài khoản của bạn đã bị vô hiệu hóa. Vui lòng liên hệ ban chủ nhiệm câu lạc bộ hoặc Phòng Công tác Sinh viên để được hỗ trợ.'],
            ]);
        }

        // Authentication successful
        Auth::login($user);
        $request->session()->regenerate();

        // [AI CHANGE 2024-07-14] Ưu tiên club mặc định
        if ($user->default_club_id) {
            $request->session()->put('club_id', $user->default_club_id);
            return redirect()->intended(route('backend.club.dashboard'))->with('success', 'Đăng nhập thành công!');
        } else {
            $request->session()->forget('club_id');
            return redirect()->intended(route('backend.dashboard'))->with('success', 'Đăng nhập thành công!');
        }
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
