<?php

// LoginController.php - Updated to provide specific error messages, account status checking, GitHub and Microsoft OAuth
// Methods: showLoginForm(), login(), logout() for handling user authentication
// Added specific error messages for email not found vs wrong password, removed remember functionality
// Added account status validation to prevent inactive users from logging in
// Added GitHub OAuth methods: redirectToGitHub(), handleGitHubCallback() for social authentication
// Added Microsoft OAuth methods: redirectToMicrosoft(), handleMicrosoftCallback() for Microsoft Azure authentication

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

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
                'email' => ['Email đã nhập không tồn tại trong hệ thống. Vui lòng liên hệ ban chủ nhiệm câu lạc bộ hoặc Phòng Công tác Sinh viên để được hỗ trợ.'],
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

    /**
     * Redirect the user to GitHub authentication page
     */
    public function redirectToGitHub()
    {
        // Check if GitHub OAuth is enabled
        $githubOAuthEnabled = Setting::getValue(null, 'github_oauth_enabled');
        if ($githubOAuthEnabled !== '1') {
            return redirect()->route('login')->with('error', 'Đăng nhập GitHub không khả dụng.');
        }

        // Get GitHub OAuth settings
        $githubClientId = Setting::getValue(null, 'github_client_id');
        $githubClientSecret = Setting::getValue(null, 'github_client_secret');

        if (empty($githubClientId) || empty($githubClientSecret)) {
            return redirect()->route('login')->with('error', 'Cấu hình GitHub OAuth không hợp lệ.');
        }

        // Configure Socialite with dynamic settings
        config([
            'services.github.client_id' => $githubClientId,
            'services.github.client_secret' => $githubClientSecret,
            'services.github.redirect' => route('auth.github.callback'),
        ]);

        return Socialite::driver('github')->redirect();
    }

    /**
     * Handle GitHub OAuth callback
     */
    public function handleGitHubCallback(Request $request)
    {
        try {
            // Check if GitHub OAuth is enabled
            $githubOAuthEnabled = Setting::getValue(null, 'github_oauth_enabled');
            if ($githubOAuthEnabled !== '1') {
                return redirect()->route('login')->with('error', 'Đăng nhập GitHub không khả dụng.');
            }

            // Get GitHub OAuth settings
            $githubClientId = Setting::getValue(null, 'github_client_id');
            $githubClientSecret = Setting::getValue(null, 'github_client_secret');

            if (empty($githubClientId) || empty($githubClientSecret)) {
                return redirect()->route('login')->with('error', 'Cấu hình GitHub OAuth không hợp lệ.');
            }

            // Configure Socialite with dynamic settings
            config([
                'services.github.client_id' => $githubClientId,
                'services.github.client_secret' => $githubClientSecret,
                'services.github.redirect' => route('auth.github.callback'),
            ]);

            $githubUser = Socialite::driver('github')->user();

            // Check if user exists with this GitHub ID
            $user = User::where('github_id', $githubUser->id)->first();

            if (!$user) {
                // Check if user exists with this email
                $user = User::where('email', $githubUser->email)->first();

                if (!$user) {
                    // User doesn't exist, redirect to login with error
                    return redirect()->route('login')->with('error', 'Không tìm thấy tài khoản với email GitHub này. Vui lòng liên hệ ban chủ nhiệm câu lạc bộ hoặc Phòng Công tác Sinh viên để được hỗ trợ.');
                }

                // Link GitHub ID to existing user
                $user->update(['github_id' => $githubUser->id]);
            }

            // Check if account is active
            if ($user->status !== 'active') {
                return redirect()->route('login')->with('error', 'Tài khoản của bạn đã bị vô hiệu hóa. Vui lòng liên hệ ban chủ nhiệm câu lạc bộ hoặc Phòng Công tác Sinh viên để được hỗ trợ.');
            }

            // Authentication successful
            Auth::login($user);
            $request->session()->regenerate();

            // Redirect based on user's default club
            if ($user->default_club_id) {
                $request->session()->put('club_id', $user->default_club_id);
                return redirect()->intended(route('backend.club.dashboard'))->with('success', 'Đăng nhập GitHub thành công!');
            } else {
                $request->session()->forget('club_id');
                return redirect()->intended(route('backend.dashboard'))->with('success', 'Đăng nhập GitHub thành công!');
            }
        } catch (\Exception $e) {
            Log::error('GitHub OAuth callback error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Có lỗi xảy ra trong quá trình đăng nhập GitHub. Vui lòng thử lại.');
        }
    }

    /**
     * Redirect the user to Microsoft authentication page
     */
    public function redirectToMicrosoft()
    {
        // Check if Microsoft OAuth is enabled
        $microsoftOAuthEnabled = Setting::getValue(null, 'microsoft_oauth_enabled');
        if ($microsoftOAuthEnabled !== '1') {
            return redirect()->route('login')->with('error', 'Đăng nhập Microsoft không khả dụng.');
        }

        // Get Microsoft OAuth settings
        $microsoftClientId = Setting::getValue(null, 'microsoft_client_id');
        $microsoftClientSecret = Setting::getValue(null, 'microsoft_client_secret');

        if (empty($microsoftClientId) || empty($microsoftClientSecret)) {
            return redirect()->route('login')->with('error', 'Cấu hình Microsoft OAuth không hợp lệ.');
        }

        // Configure Socialite with dynamic settings
        config([
            'services.microsoft.client_id' => $microsoftClientId,
            'services.microsoft.client_secret' => $microsoftClientSecret,
            'services.microsoft.redirect' => route('auth.microsoft.callback'),
            'services.microsoft.tenant' => Setting::getValue(null, 'microsoft_tenant_id') ?: 'common',
        ]);

        return Socialite::driver('microsoft')->redirect();
    }

    /**
     * Handle Microsoft OAuth callback
     */
    public function handleMicrosoftCallback(Request $request)
    {
        try {
            // Check if Microsoft OAuth is enabled
            $microsoftOAuthEnabled = Setting::getValue(null, 'microsoft_oauth_enabled');
            if ($microsoftOAuthEnabled !== '1') {
                return redirect()->route('login')->with('error', 'Đăng nhập Microsoft không khả dụng.');
            }

            // Get Microsoft OAuth settings
            $microsoftClientId = Setting::getValue(null, 'microsoft_client_id');
            $microsoftClientSecret = Setting::getValue(null, 'microsoft_client_secret');

            if (empty($microsoftClientId) || empty($microsoftClientSecret)) {
                return redirect()->route('login')->with('error', 'Cấu hình Microsoft OAuth không hợp lệ.');
            }

            // Configure Socialite with dynamic settings
            config([
                'services.microsoft.client_id' => $microsoftClientId,
                'services.microsoft.client_secret' => $microsoftClientSecret,
                'services.microsoft.redirect' => route('auth.microsoft.callback'),
                'services.microsoft.tenant' => Setting::getValue(null, 'microsoft_tenant_id') ?: 'common',
            ]);

            $microsoftUser = Socialite::driver('microsoft')->user();

            // Check if user exists with this Microsoft ID
            $user = User::where('microsoft_id', $microsoftUser->id)->first();

            if (!$user) {
                // Check if user exists with this email
                $user = User::where('email', $microsoftUser->email)->first();

                if (!$user) {
                    // User doesn't exist, redirect to login with error
                    return redirect()->route('login')->with('error', 'Không tìm thấy tài khoản với email Microsoft này. Vui lòng liên hệ ban chủ nhiệm câu lạc bộ hoặc Phòng Công tác Sinh viên để được hỗ trợ.');
                }

                // Link Microsoft ID to existing user
                $user->update(['microsoft_id' => $microsoftUser->id]);
            }

            // Check if account is active
            if ($user->status !== 'active') {
                return redirect()->route('login')->with('error', 'Tài khoản của bạn đã bị vô hiệu hóa. Vui lòng liên hệ ban chủ nhiệm câu lạc bộ hoặc Phòng Công tác Sinh viên để được hỗ trợ.');
            }

            // Authentication successful
            Auth::login($user);
            $request->session()->regenerate();

            // Redirect based on user's default club
            if ($user->default_club_id) {
                $request->session()->put('club_id', $user->default_club_id);
                return redirect()->intended(route('backend.club.dashboard'))->with('success', 'Đăng nhập Microsoft thành công!');
            } else {
                $request->session()->forget('club_id');
                return redirect()->intended(route('backend.dashboard'))->with('success', 'Đăng nhập Microsoft thành công!');
            }
        } catch (\Exception $e) {
            Log::error('Microsoft OAuth callback error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Có lỗi xảy ra trong quá trình đăng nhập Microsoft. Vui lòng thử lại.');
        }
    }
}
