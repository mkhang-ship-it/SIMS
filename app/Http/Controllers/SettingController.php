<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Address;
use App\Models\Province;
use App\Models\Commune;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $mailSettings = Setting::getMany(null, [
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

        $generalSettings = Setting::getMany(null, [
            'site_name',
            'site_phone',
            'site_email',
            'image_horizontal_light',
            'image_horizontal_dark',
            'image_square_light',
            'image_square_dark',
        ]);

        // Load admin address
        $adminAddress = Address::where('club_id', null)->where('user_id', null)->first();

        // Load provinces for address selection
        $provinces = Province::orderBy('name')->get();

        return view('settings.index', compact('mailSettings', 'generalSettings', 'adminAddress', 'provinces'));
    }

    // Updated: Xóa ảnh cũ khi upload ảnh mới cho các trường logo trong updateGeneralSettings
    public function updateGeneralSettings(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_phone' => 'required|string|max:20',
            'site_email' => 'required|email',
            'image_horizontal_light' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_horizontal_dark' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_square_light' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_square_dark' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'province_id' => 'nullable|exists:provinces,id',
            'commune_id' => 'nullable|exists:communes,id',
            'address' => 'nullable|string|max:500',
        ]);

        // Handle image uploads and delete old images if exist
        $imageFields = ['image_horizontal_light', 'image_horizontal_dark', 'image_square_light', 'image_square_dark'];

        // Lấy các giá trị cũ của logo từ DB
        $oldSettings = Setting::getMany(null, $imageFields);

        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                // Xóa ảnh cũ nếu có
                if (!empty($oldSettings[$field])) {
                    Storage::disk('public')->delete($oldSettings[$field]);
                }
                $file = $request->file($field);
                $path = $file->store('admin/settings', 'public');
                $validated[$field] = $path;
            }
        }

        // Update or create settings
        foreach ($validated as $key => $value) {
            if ($value !== null && !in_array($key, ['province_id', 'commune_id', 'address'])) {
                Setting::updateOrCreate(
                    ['club_id' => null, 'key' => $key, 'is_admin' => 'central'],
                    ['value' => $value]
                );
            }
        }

        // Handle address update
        if (isset($validated['province_id']) || isset($validated['commune_id']) || isset($validated['address'])) {
            Address::updateOrCreate(
                ['club_id' => null, 'user_id' => null],
                [
                    'province_id' => $validated['province_id'] ?? null,
                    'commune_id' => $validated['commune_id'] ?? null,
                    'address' => $validated['address'] ?? null,
                ]
            );
        }

        return redirect()->back()->with('success', 'Cấu hình chung đã được cập nhật thành công.');
    }

    public function getCommunes(Request $request)
    {
        $request->validate([
            'province_id' => 'required|exists:provinces,id'
        ]);

        $communes = Commune::where('province_id', $request->province_id)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return response()->json($communes);
    }

    public function updateMailSettings(Request $request)
    {
        $validated = $request->validate([
            'mail_mailer' => 'required|in:smtp,outlook',
            'mail_host' => 'required_if:mail_mailer,smtp',
            'mail_port' => 'required_if:mail_mailer,smtp|numeric',
            'mail_username' => 'required_if:mail_mailer,smtp',
            'mail_password' => 'required_if:mail_mailer,smtp',
            'mail_encryption' => 'required_if:mail_mailer,smtp',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
            'mail_outlook_client_id' => 'required_if:mail_mailer,outlook',
            'mail_outlook_secret_id' => 'required_if:mail_mailer,outlook',
            'mail_outlook_tenant_id' => 'required_if:mail_mailer,outlook',
        ]);

        // Update or create settings
        foreach ($validated as $key => $value) {
            if ($value !== null) {
                Setting::updateOrCreate(
                    ['club_id' => null, 'key' => $key, 'is_admin' => 'central'],
                    ['value' => $value]
                );
            }
        }

        return redirect()->back()->with('success', 'Cấu hình email đã được cập nhật thành công.');
    }

    public function testMail(Request $request)
    {
        $request->validate([
            'to' => 'required|email',
        ]);

        try {
            // Get current mail settings
            $mailSettings = Setting::getMany(null, [
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

            // Configure mail settings dynamically
            config([
                'mail.default' => $mailSettings['mail_mailer'] ?? 'smtp',
                'mail.mailers.smtp.host' => $mailSettings['mail_host'] ?? '',
                'mail.mailers.smtp.port' => $mailSettings['mail_port'] ?? '',
                'mail.mailers.smtp.username' => $mailSettings['mail_username'] ?? '',
                'mail.mailers.smtp.password' => $mailSettings['mail_password'] ?? '',
                'mail.mailers.smtp.encryption' => $mailSettings['mail_encryption'] ?? '',
                'mail.from.address' => $mailSettings['mail_from_address'] ?? '',
                'mail.from.name' => $mailSettings['mail_from_name'] ?? '',
            ]);

            // Send test email
            Mail::raw('Đây là email test từ hệ thống admin. Cấu hình email đã hoạt động bình thường.', function ($message) use ($request, $mailSettings) {
                $message->to($request->to)
                    ->subject('Test Email - Hệ Thống Admin')
                    ->from($mailSettings['mail_from_address'] ?? config('mail.from.address'), $mailSettings['mail_from_name'] ?? config('mail.from.name'));
            });

            return redirect()->back()->with('success', 'Email test đã được gửi thành công đến ' . $request->to);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi gửi email: ' . $e->getMessage());
        }
    }
}
