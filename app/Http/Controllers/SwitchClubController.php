<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SwitchClubController extends Controller
{
    public function switch(Request $request)
    {
        $user = Auth::user();
        $clubId = $request->input('default_club_id');

        $userClubRole = $user->userClubRoles()
            ->where('club_id', $clubId)
            ->first();

        if (!$userClubRole) {
            return redirect()->back()->with('error', 'Bạn không thuộc câu lạc bộ này!');
        }

        if ($userClubRole->status !== 'active') {
            return redirect()->back()->with('error', 'Bạn không thể chuyển sang câu lạc bộ này vì tài khoản chưa được kích hoạt!');
        }

        $user->default_club_id = $clubId;
        $user->save();
        $request->session()->put('club_id', $clubId);
        return redirect()->back();
    }
}
