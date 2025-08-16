<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Club;
use App\Models\Setting;
use App\Models\Province;
use App\Models\Commune;
use Illuminate\Support\Facades\Storage;

use App\Models\Role;

class SettingController extends Controller
{
    public function index()
    {
        $clubId = session('club_id');
        $club = Club::with('address.province', 'address.commune')->findOrFail($clubId);
        $provinces = Province::all();
        $roles = Role::where('club_id', $clubId)->get();
        $bankSettings = Setting::getBankInfo($clubId);
        $mailSettings = Setting::getMany($clubId, [
            'mail_mailer',
            'mail_host',
            'mail_port',
            'mail_username',
            'mail_password',
            'mail_encryption',
            'mail_from_address',
            'mail_from_name',
            'mail_outlook_client_id',
            'mail_outlook_secret_id',
            'mail_outlook_tenant_id',
        ]);

        return view('clubs.settings.index', compact('club', 'provinces', 'bankSettings', 'mailSettings', 'roles'));
    }

    public function updateGeneralSettings(Request $request)
    {
        $clubId = session('club_id');
        $club = Club::findOrFail($clubId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'province_id' => 'required|exists:provinces,id',
            'commune_id' => 'required|exists:communes,id',
            'address' => 'required|string|max:500',
            'image_horizontal_light' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_horizontal_dark' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_square_light' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_square_dark' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ];

        // Xử lý upload cho từng loại logo
        $logoTypes = [
            'image_horizontal_light',
            'image_horizontal_dark',
            'image_square_light',
            'image_square_dark',
        ];
        foreach ($logoTypes as $type) {
            if ($request->hasFile($type)) {
                // Xóa file cũ nếu có
                if ($club->$type && Storage::disk('public')->exists($club->$type)) {
                    Storage::disk('public')->delete($club->$type);
                }
                // Lưu file mới
                $logoPath = $request->file($type)->store('clubs/logos', 'public');
                $data[$type] = $logoPath;
            }
        }

        $club->update($data);

        // Cập nhật hoặc tạo mới địa chỉ
        $address = $club->address()->firstOrNew();
        $address->fill([
            'province_id' => $validated['province_id'],
            'commune_id' => $validated['commune_id'],
            'address' => $validated['address'],
            'type' => 'central',
        ]);
        $address->club_id = $clubId;
        $address->save();

        return redirect()->back()->with('success', 'Thông tin câu lạc bộ đã được cập nhật thành công.');
    }

    // Lấy danh sách xã theo tỉnh (AJAX)
    public function getCommunes(Request $request)
    {
        $provinceId = $request->province_id;
        $communes = Commune::where('province_id', $provinceId)->get();
        return response()->json($communes);
    }

    /**
     * Get banks from local JSON file
     */
    public function getBanksFromAPI()
    {
        try {
            $jsonPath = public_path('list-banks.json');
            if (!file_exists($jsonPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File danh sách ngân hàng không tồn tại'
                ], 404);
            }
            $jsonContent = file_get_contents($jsonPath);
            $data = json_decode($jsonContent, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi định dạng file JSON'
                ], 500);
            }
            if (isset($data['data']) && is_array($data['data'])) {
                return response()->json([
                    'success' => true,
                    'banks' => $data['data']
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'Cấu trúc dữ liệu không hợp lệ'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi đọc file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update bank settings (still using Setting model for club)
     */
    public function updateBankSettings(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'bank_code' => 'required|string|max:10',
            'bank_bin' => 'required|string|max:10',
            'bank_short_name' => 'required|string|max:50',
            // 'bank_swift_code' => 'required|string|max:20', // Đã xoá
            'bank_account_name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:50',
        ]);
        $clubId = session('club_id');
        $bankSettings = [
            'bank_name' => $request->bank_name,
            'bank_code' => $request->bank_code,
            'bank_bin' => $request->bank_bin,
            'bank_short_name' => $request->bank_short_name,
            // 'bank_swift_code' => $request->bank_swift_code, // Đã xoá
            'bank_account_name' => $request->bank_account_name,
            'bank_account_number' => $request->bank_account_number,
        ];
        foreach ($bankSettings as $key => $value) {
            Setting::updateOrCreate(
                ['club_id' => $clubId, 'key' => $key],
                ['value' => $value]
            );
        }
        return redirect()->back()->with('success', 'Thông tin ngân hàng đã được cập nhật thành công.');
    }

    /**
     * Update mail settings (SMTP/Outlook) for club
     */
    public function updateMailSettings(Request $request)
    {
        $request->validate([
            'mail_mailer' => 'required|string|max:50',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255',
        ]);
        $clubId = session('club_id');
        $mailSettings = [
            'mail_mailer' => $request->mail_mailer,
            'mail_from_address' => $request->mail_from_address,
            'mail_from_name' => $request->mail_from_name,
        ];
        if ($request->mail_mailer === 'smtp') {
            $request->validate([
                'mail_host' => 'required|string|max:255',
                'mail_port' => 'required|numeric',
                'mail_username' => 'required|string|max:255',
                'mail_password' => 'required|string|max:255',
                'mail_encryption' => 'required|string|max:20',
            ]);
            $mailSettings = array_merge($mailSettings, [
                'mail_host' => $request->mail_host,
                'mail_port' => $request->mail_port,
                'mail_username' => $request->mail_username,
                'mail_password' => $request->mail_password,
                'mail_encryption' => $request->mail_encryption,
                // Xoá các trường outlook nếu có
                'mail_outlook_client_id' => null,
                'mail_outlook_secret_id' => null,
                'mail_outlook_tenant_id' => null,
            ]);
        } elseif ($request->mail_mailer === 'outlook') {
            $request->validate([
                'mail_outlook_client_id' => 'required|string|max:255',
                'mail_outlook_secret_id' => 'required|string|max:255',
                'mail_outlook_tenant_id' => 'required|string|max:255',
            ]);
            $mailSettings = array_merge($mailSettings, [
                'mail_outlook_client_id' => $request->mail_outlook_client_id,
                'mail_outlook_secret_id' => $request->mail_outlook_secret_id,
                'mail_outlook_tenant_id' => $request->mail_outlook_tenant_id,
                // Xoá các trường smtp nếu có
                'mail_host' => null,
                'mail_port' => null,
                'mail_username' => null,
                'mail_password' => null,
                'mail_encryption' => null,
            ]);
        }
        foreach ($mailSettings as $key => $value) {
            Setting::updateOrCreate(
                ['club_id' => $clubId, 'key' => $key],
                ['value' => $value]
            );
        }
        return redirect()->back()->with('success', 'Cấu hình gửi mail đã được cập nhật thành công.');
    }

    /**
     * Test gửi mail theo cấu hình club (SMTP hoặc Microsoft Graph API)
     */
    public function testMail(Request $request)
    {
        $request->validate([
            'to' => 'required|email',
        ]);
        $clubId = session('club_id');
        $to = $request->to;
        $subject = 'Test gửi mail từ hệ thống';
        $body = '<p>Đây là email test gửi từ hệ thống Tezonew.</p>';
        $result = \App\Services\MailService::sendClubMail($clubId, $to, $subject, $body);
        if ($result === true) {
            return back()->with('success', 'Đã gửi mail test thành công!');
        } else {
            return back()->with('error', 'Gửi mail thất bại: ' . $result);
        }
    }
}
