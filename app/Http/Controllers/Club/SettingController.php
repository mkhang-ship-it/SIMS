<?php
// Refactored: Sử dụng session('club_id'), đồng bộ style với MemberController, loại bỏ hoàn toàn trường bank_swift_code khỏi validate và xử lý bank info
// Refactored: Cập nhật thông tin chung trực tiếp trên model Club (không dùng Setting cho general info)
// Refactored: Giữ word_commitment_template trong Setting (theo key)
// Refactored: Giữ thông tin ngân hàng trong Setting (theo key), vì chưa có bảng club_bank_accounts
// Refactored: Chuẩn hóa validate, upload, và trả về view theo chuẩn mới

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Club;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    public function index()
    {
        $clubId = session('club_id');
        $club = Club::findOrFail($clubId);
        $wordCommitmentTemplate = Setting::getValue($clubId, 'word_commitment_template');
        $bankSettings = Setting::getBankInfo($clubId);
        return view('clubs.settings.index', compact('club', 'wordCommitmentTemplate', 'bankSettings'));
    }

    public function updateGeneralSettings(Request $request)
    {
        $clubId = session('club_id');
        $club = Club::findOrFail($clubId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
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
            'address' => $validated['address'],
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

        return redirect()->back()->with('success', 'Thông tin câu lạc bộ đã được cập nhật thành công.');
    }

    public function updateWordCommitmentTemplate(Request $request)
    {
        $request->validate([
            'word_commitment_template' => 'required|mimes:docx|max:5120',
        ]);
        $clubId = session('club_id');
        $currentTemplate = Setting::getValue($clubId, 'word_commitment_template');
        if ($currentTemplate && Storage::exists($currentTemplate)) {
            Storage::delete($currentTemplate);
        }
        $file = $request->file('word_commitment_template');
        $filename = 'commitment_template_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('word_commitment_templates', $filename);
        Setting::updateOrCreate(
            ['club_id' => $clubId, 'key' => 'word_commitment_template'],
            ['value' => $path]
        );
        return back()->with('success', 'Mẫu file cam kết đã được cập nhật thành công.');
    }

    public function downloadWordCommitmentTemplate()
    {
        $clubId = session('club_id');
        $templatePath = Setting::getValue($clubId, 'word_commitment_template');
        if (!$templatePath || !Storage::exists($templatePath)) {
            return back()->with('error', 'File mẫu bản cam kết không tồn tại.');
        }
        return Storage::download($templatePath, 'mau_ban_cam_ket.docx');
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
}
