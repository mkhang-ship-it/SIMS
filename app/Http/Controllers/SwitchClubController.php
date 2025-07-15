<?php
// [AI CHANGE 2024-07-14] Controller cho phép user đổi club mặc định từ topbar

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SwitchClubController extends Controller
{
    public function switch(Request $request)
    {
        $user = Auth::user();
        $clubId = $request->input('default_club_id');
        // Kiểm tra user có thuộc club này không
        if (!$user->clubs->pluck('id')->contains($clubId)) {
            return redirect()->back()->with('error', 'Bạn không thuộc câu lạc bộ này!');
        }
        $user->default_club_id = $clubId;
        $user->save();
        $request->session()->put('club_id', $clubId);
        return redirect()->back();
    }
}
