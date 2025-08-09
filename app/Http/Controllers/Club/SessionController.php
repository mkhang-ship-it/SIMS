<?php

namespace App\Http\Controllers\Club;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class SessionController extends Controller
{
    public function index()
    {
        $currentUser = Auth::user();
        $clubId = session('club_id');

        // Lấy danh sách user IDs của các thành viên trong club
        $clubUserIds = DB::table('user_club_roles')
            ->where('club_id', $clubId)
            ->where('status', 'active')
            ->pluck('user_id');

        // Lấy tất cả phiên của các user trong club
        $sessions = DB::table('sessions')
            ->leftJoin('users', 'sessions.user_id', '=', 'users.id')
            ->leftJoin('user_club_roles', 'users.id', '=', 'user_club_roles.user_id')
            ->leftJoin('roles', 'user_club_roles.role_id', '=', 'roles.id')
            ->select([
                'sessions.id',
                'sessions.user_id',
                'sessions.ip_address',
                'sessions.user_agent',
                'sessions.last_activity',
                'users.name as user_name',
                'users.email as user_email',
                'users.avatar as user_avatar',
                'roles.name as role_name'
            ])
            ->whereIn('sessions.user_id', $clubUserIds)
            ->where('user_club_roles.club_id', $clubId)
            ->where('user_club_roles.status', 'active')
            ->orderBy('sessions.last_activity', 'desc')
            ->get();

        // Thống kê cho tất cả user trong club
        $totalSessions = DB::table('sessions')
            ->whereIn('user_id', $clubUserIds)
            ->count();

        $activeSessions = DB::table('sessions')
            ->whereIn('user_id', $clubUserIds)
            ->where('last_activity', '>=', time() - (config('session.lifetime') * 60))
            ->count();

        $expiredSessions = $totalSessions - $activeSessions;

        return view('clubs.sessions.index', compact('sessions', 'totalSessions', 'activeSessions', 'expiredSessions'));
    }

    public function destroy($sessionId)
    {
        $currentUser = Auth::user();
        $clubId = session('club_id');

        // Lấy danh sách user IDs của các thành viên trong club
        $clubUserIds = DB::table('user_club_roles')
            ->where('club_id', $clubId)
            ->where('status', 'active')
            ->pluck('user_id');

        // Chỉ cho phép xóa phiên của thành viên trong club
        $session = DB::table('sessions')
            ->where('id', $sessionId)
            ->whereIn('user_id', $clubUserIds)
            ->first();

        if (!$session) {
            return back()->with('error', 'Không tìm thấy phiên hoặc không có quyền xóa!');
        }

        DB::table('sessions')->where('id', $sessionId)->delete();

        return back()->with('success', 'Đã xóa phiên thành công!');
    }

    public function destroyAll()
    {
        $currentUser = Auth::user();
        $clubId = session('club_id');

        // Lấy danh sách user IDs của các thành viên trong club
        $clubUserIds = DB::table('user_club_roles')
            ->where('club_id', $clubId)
            ->where('status', 'active')
            ->pluck('user_id');

        // Xóa tất cả phiên của các thành viên trong club
        DB::table('sessions')->whereIn('user_id', $clubUserIds)->delete();

        return back()->with('success', 'Đã xóa tất cả phiên thành công!');
    }

    public function destroyExpired()
    {
        $currentUser = Auth::user();
        $clubId = session('club_id');

        // Lấy danh sách user IDs của các thành viên trong club
        $clubUserIds = DB::table('user_club_roles')
            ->where('club_id', $clubId)
            ->where('status', 'active')
            ->pluck('user_id');

        // Xóa các phiên đã hết hạn của các thành viên trong club
        $expiredTime = time() - (config('session.lifetime') * 60);
        DB::table('sessions')
            ->whereIn('user_id', $clubUserIds)
            ->where('last_activity', '<', $expiredTime)
            ->delete();

        return back()->with('success', 'Đã xóa các phiên hết hạn thành công!');
    }

    public function destroyUserSessions($userId)
    {
        $currentUser = Auth::user();
        $clubId = session('club_id');

        // Kiểm tra xem user có phải là thành viên của club không
        $isClubMember = DB::table('user_club_roles')
            ->where('club_id', $clubId)
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->exists();

        if (!$isClubMember) {
            return back()->with('error', 'Không tìm thấy thành viên hoặc không có quyền xóa!');
        }

        // Xóa tất cả phiên của user này
        DB::table('sessions')->where('user_id', $userId)->delete();

        return back()->with('success', 'Đã xóa tất cả phiên của thành viên thành công!');
    }
}
