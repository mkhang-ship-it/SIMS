<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ImpersonationController extends Controller
{
    /**
     * Bắt đầu mạo danh người dùng
     */
    public function start(Request $request, $userId)
    {
        $currentUser = Auth::user();
        $targetUser = User::findOrFail($userId);

        // Kiểm tra quyền mạo danh
        if (!$this->canImpersonate($currentUser, $targetUser)) {
            return back()->with('error', 'Bạn không có quyền mạo danh người dùng này.');
        }

        // Lưu thông tin người dùng gốc vào session
        Session::put('impersonator_id', $currentUser->id);
        Session::put('impersonator_name', $currentUser->name);
        Session::put('impersonator_type', $currentUser->type);

        // Đăng nhập với tài khoản được mạo danh
        Auth::login($targetUser);

        // Xử lý club mặc định cho người dùng được mạo danh
        if ($targetUser->default_club_id) {
            Session::put('club_id', $targetUser->default_club_id);
            $redirectRoute = 'backend.club.dashboard';
        } else {
            Session::forget('club_id');
            $redirectRoute = 'backend.dashboard';
        }

        return redirect()->route($redirectRoute)->with('success', 'Đã bắt đầu mạo danh ' . $targetUser->name);
    }

    /**
     * Kết thúc mạo danh và quay lại tài khoản gốc
     */
    public function stop(Request $request)
    {
        if (!Session::has('impersonator_id')) {
            return redirect()->route('backend.dashboard')->with('error', 'Không tìm thấy phiên mạo danh.');
        }

        $impersonatorId = Session::get('impersonator_id');
        $impersonatorType = Session::get('impersonator_type');
        $impersonator = User::findOrFail($impersonatorId);

        // Xóa thông tin mạo danh khỏi session
        Session::forget(['impersonator_id', 'impersonator_name', 'impersonator_type']);

        // Đăng nhập lại với tài khoản gốc
        Auth::login($impersonator);

        // Xử lý club mặc định cho người dùng gốc
        if ($impersonator->default_club_id) {
            Session::put('club_id', $impersonator->default_club_id);
            $redirectRoute = 'backend.club.dashboard';
        } else {
            Session::forget('club_id');
            $redirectRoute = 'backend.dashboard';
        }

        return redirect()->route($redirectRoute)->with('success', 'Đã kết thúc mạo danh và quay lại tài khoản của bạn.');
    }

    /**
     * Kiểm tra quyền mạo danh
     */
    private function canImpersonate(User $impersonator, User $target)
    {
        // Admin central có thể mạo danh tất cả
        if ($impersonator->type === 'central') {
            return true;
        }

        // Admin club chỉ có thể mạo danh thành viên trong club của mình
        if ($impersonator->type === 'branch') {
            // Kiểm tra xem impersonator có phải admin của club không
            $impersonatorClubRole = $impersonator->userClubRoles()
                ->where('club_id', session('club_id'))
                ->with('role')
                ->first();

            if (!$impersonatorClubRole || !$impersonatorClubRole->role->is_admin) {
                return false;
            }

            // Kiểm tra xem target có trong club của impersonator không
            $targetInClub = $target->userClubRoles()
                ->where('club_id', session('club_id'))
                ->exists();

            return $targetInClub;
        }

        return false;
    }

    /**
     * Kiểm tra xem có đang trong phiên mạo danh không
     */
    public static function isImpersonating()
    {
        return Session::has('impersonator_id');
    }

    /**
     * Lấy thông tin người mạo danh
     */
    public static function getImpersonator()
    {
        if (!self::isImpersonating()) {
            return null;
        }

        return [
            'id' => Session::get('impersonator_id'),
            'name' => Session::get('impersonator_name'),
            'type' => Session::get('impersonator_type')
        ];
    }
}
