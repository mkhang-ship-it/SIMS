<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationSettingsController extends Controller
{
    /**
     * Update registration settings for a club
     */
    public function update(Request $request)
    {
        $clubId = session('club_id');
        $club = Club::findOrFail($clubId);

        $request->validate([
            'registration_enabled' => 'boolean',
            'registration_opened_at' => 'nullable|date',
            'registration_closed_at' => 'nullable|date|after:registration_opened_at',
            'default_role_id' => 'nullable|exists:roles,id',
        ]);

        try {
            DB::beginTransaction();

            $data = [
                'registration_enabled' => $request->has('registration_enabled'),
                'registration_opened_at' => $request->registration_opened_at ?: null,
                'registration_closed_at' => $request->registration_closed_at ?: null,
                'default_role_id' => $request->default_role_id ?: null,
            ];

            // Nếu bật đăng ký và chưa có token, tự động tạo token
            if ($data['registration_enabled'] && !$club->registration_token) {
                $data['registration_token'] = $club->generateRegistrationToken();
            }

            // Nếu tắt đăng ký, xóa token
            if (!$data['registration_enabled']) {
                $data['registration_token'] = null;
                $data['registration_opened_at'] = null;
                $data['registration_closed_at'] = null;
                $data['default_role_id'] = null;
            }

            $club->update($data);

            DB::commit();

            return redirect()->back()->with('success', 'Cài đặt đăng ký đã được cập nhật thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật cài đặt: ' . $e->getMessage());
        }
    }
}
