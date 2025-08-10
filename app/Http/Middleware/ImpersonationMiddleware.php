<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\ImpersonationController;

class ImpersonationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Kiểm tra xem có đang trong phiên mạo danh không
        if (ImpersonationController::isImpersonating()) {
            // Kiểm tra xem người mạo danh gốc vẫn tồn tại không
            $impersonatorId = Session::get('impersonator_id');
            $impersonator = \App\Models\User::find($impersonatorId);

            if (!$impersonator) {
                // Người mạo danh gốc không tồn tại, kết thúc phiên mạo danh
                Session::forget(['impersonator_id', 'impersonator_name', 'impersonator_type']);
                return redirect()->route('login')->with('error', 'Phiên mạo danh đã hết hạn. Vui lòng đăng nhập lại.');
            }

            // Kiểm tra xem người được mạo danh vẫn tồn tại không
            $currentUser = Auth::user();
            if (!$currentUser) {
                // Người được mạo danh không tồn tại, kết thúc phiên mạo danh
                Session::forget(['impersonator_id', 'impersonator_name', 'impersonator_type']);
                Auth::login($impersonator);
                return redirect()->route('backend.dashboard')->with('error', 'Tài khoản được mạo danh không còn tồn tại. Đã quay lại tài khoản gốc.');
            }

            // Kiểm tra quyền mạo danh vẫn còn hợp lệ không
            if (!$this->canStillImpersonate($impersonator, $currentUser)) {
                // Không còn quyền mạo danh, kết thúc phiên
                Session::forget(['impersonator_id', 'impersonator_name', 'impersonator_type']);
                Auth::login($impersonator);
                return redirect()->route('backend.dashboard')->with('error', 'Bạn không còn quyền mạo danh người dùng này. Đã quay lại tài khoản gốc.');
            }
        }

        return $next($request);
    }

    /**
     * Kiểm tra xem vẫn còn quyền mạo danh không
     */
    private function canStillImpersonate($impersonator, $target)
    {
        // Admin central có thể mạo danh tất cả
        if ($impersonator->type === 'central') {
            return true;
        }

        // Admin club chỉ có thể mạo danh thành viên trong club của mình
        if ($impersonator->type === 'branch') {
            $clubId = Session::get('club_id');
            if (!$clubId) {
                return false;
            }

            // Kiểm tra xem impersonator có phải admin của club không
            $impersonatorClubRole = $impersonator->userClubRoles()
                ->where('club_id', $clubId)
                ->with('role')
                ->first();

            if (!$impersonatorClubRole || !$impersonatorClubRole->role || !$impersonatorClubRole->role->is_leader) {
                return false;
            }

            // Kiểm tra xem target có trong club của impersonator không
            $targetInClub = $target->userClubRoles()
                ->where('club_id', $clubId)
                ->exists();

            return $targetInClub;
        }

        return false;
    }
}

