<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SessionController extends Controller
{
    public function index()
    {
        // Lấy tất cả phiên từ database
        $sessions = DB::table('sessions')
            ->leftJoin('users', 'sessions.user_id', '=', 'users.id')
            ->select([
                'sessions.id',
                'sessions.user_id',
                'sessions.ip_address',
                'sessions.user_agent',
                'sessions.last_activity',
                'users.name as user_name',
                'users.email as user_email',
                'users.avatar as user_avatar'
            ])
            ->orderBy('sessions.last_activity', 'desc')
            ->get();

        // Thống kê
        $totalSessions = DB::table('sessions')->count();
        $activeSessions = DB::table('sessions')
            ->where('last_activity', '>=', time() - (config('session.lifetime') * 60))
            ->count();
        $expiredSessions = $totalSessions - $activeSessions;

        return view('sessions.index', compact('sessions', 'totalSessions', 'activeSessions', 'expiredSessions'));
    }

    public function destroy($sessionId)
    {
        // Xóa phiên cụ thể
        DB::table('sessions')->where('id', $sessionId)->delete();

        return back()->with('success', 'Đã xóa phiên thành công!');
    }

    public function destroyAll()
    {
        // Xóa tất cả phiên
        DB::table('sessions')->truncate();

        return back()->with('success', 'Đã xóa tất cả phiên thành công!');
    }

    public function destroyExpired()
    {
        // Xóa các phiên đã hết hạn
        $expiredTime = time() - (config('session.lifetime') * 60);
        DB::table('sessions')->where('last_activity', '<', $expiredTime)->delete();

        return back()->with('success', 'Đã xóa các phiên hết hạn thành công!');
    }

    public function destroyUserSessions($userId)
    {
        // Xóa tất cả phiên của user cụ thể
        DB::table('sessions')->where('user_id', $userId)->delete();

        return back()->with('success', 'Đã xóa tất cả phiên của người dùng thành công!');
    }
}
