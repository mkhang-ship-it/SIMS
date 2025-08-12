<?php
// SettingController - Updated to include permission refresh functionality, image dimension validation, and OAuth settings
// Added refreshPermissions method to handle permission refresh from web interface
// Added custom validation for background images to ensure 360x1000px dimensions
// Added OAuth settings management for GitHub authentication with enable/disable functionality
// Added external background image configuration for external pages with overlay color and opacity settings
// Created: Method to run RefreshPermissions command via web interface
// Created: updateOAuthSettings method to handle GitHub OAuth configuration
// Updated: Added external_bg_image, external_bg_overlay_color, external_bg_overlay_opacity settings support
// Fixed: Mail settings validation to handle nullable fields for different mailer types (SMTP vs Outlook)
// Fixed: Only save relevant fields based on selected mailer type to prevent validation conflicts
// Updated: testMail method to use existing MailService which properly handles both SMTP and Microsoft Graph API

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Address;
use App\Models\Province;
use App\Models\Commune;
use App\Models\ThemeSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use App\Services\MailService;

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
            'external_bg_image',
            'external_bg_overlay_color',
            'external_bg_overlay_opacity',
        ]);

        $maintenanceSettings = Setting::getMany(null, [
            'maintenance_mode',
            'maintenance_bypass_token',
            'maintenance_message',
        ]);

        // Auto-generate bypass token if not exists
        if (empty($maintenanceSettings['maintenance_bypass_token'])) {
            $newToken = \Illuminate\Support\Str::random(32);
            Setting::updateOrCreate(
                ['club_id' => null, 'key' => 'maintenance_bypass_token', 'is_admin' => 'central'],
                ['value' => $newToken]
            );
            $maintenanceSettings['maintenance_bypass_token'] = $newToken;
        }

        // Load admin address
        $adminAddress = Address::where('club_id', null)->where('user_id', null)->first();

        // Load provinces for address selection
        $provinces = Province::orderBy('name')->get();

        // Load theme settings
        $themeSettings = ThemeSetting::getActiveTheme();

        // Load OAuth settings
        $oauthSettings = Setting::getMany(null, [
            'github_oauth_enabled',
            'github_client_id',
            'github_client_secret',
            'microsoft_oauth_enabled',
            'microsoft_client_id',
            'microsoft_client_secret',
            'microsoft_tenant_id',
        ]);

        return view('settings.index', compact('mailSettings', 'generalSettings', 'maintenanceSettings', 'adminAddress', 'provinces', 'themeSettings', 'oauthSettings'));
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
            'external_bg_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'external_bg_overlay_color' => [
                'nullable',
                'string',
                'regex:' . '/^#(?:[A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/'
            ],
            'external_bg_overlay_opacity' => 'nullable|numeric|min:0|max:1',
            'province_id' => 'nullable|exists:provinces,id',
            'commune_id' => 'nullable|exists:communes,id',
            'address' => 'nullable|string|max:500',
        ]);

        // Handle image uploads and delete old images if exist
        $imageFields = ['image_horizontal_light', 'image_horizontal_dark', 'image_square_light', 'image_square_dark', 'external_bg_image'];

        // Lấy các giá trị cũ của logo từ DB
        $oldSettings = Setting::getMany(null, $imageFields);

        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                // Xóa ảnh cũ nếu có
                if (!empty($oldSettings[$field])) {
                    Storage::disk('public')->delete($oldSettings[$field]);
                }
                $file = $request->file($field);

                // Use different storage path for external background image
                if ($field === 'external_bg_image') {
                    $path = $file->store('external/backgrounds', 'public');
                } else {
                    $path = $file->store('admin/settings', 'public');
                }

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
            'mail_host' => 'required_if:mail_mailer,smtp|nullable',
            'mail_port' => 'nullable|numeric|required_if:mail_mailer,smtp',
            'mail_username' => 'required_if:mail_mailer,smtp|nullable',
            'mail_password' => 'required_if:mail_mailer,smtp|nullable',
            'mail_encryption' => 'required_if:mail_mailer,smtp|nullable',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
            'mail_outlook_client_id' => 'required_if:mail_mailer,outlook|nullable',
            'mail_outlook_secret_id' => 'required_if:mail_mailer,outlook|nullable',
            'mail_outlook_tenant_id' => 'required_if:mail_mailer,outlook|nullable',
        ]);

        // Always save mailer type, from_address, from_name
        $alwaysSave = ['mail_mailer', 'mail_from_address', 'mail_from_name'];

        // Define fields for each mailer type
        $smtpFields = ['mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption'];
        $outlookFields = ['mail_outlook_client_id', 'mail_outlook_secret_id', 'mail_outlook_tenant_id'];

        // Determine which fields to save based on selected mailer
        $fieldsToSave = $alwaysSave;
        if ($validated['mail_mailer'] === 'smtp') {
            $fieldsToSave = array_merge($fieldsToSave, $smtpFields);
        } else if ($validated['mail_mailer'] === 'outlook') {
            $fieldsToSave = array_merge($fieldsToSave, $outlookFields);
        }

        // Update or create settings for relevant fields only
        foreach ($fieldsToSave as $key) {
            if (isset($validated[$key]) && $validated[$key] !== null && $validated[$key] !== '') {
                Setting::updateOrCreate(
                    ['club_id' => null, 'key' => $key, 'is_admin' => 'central'],
                    ['value' => $validated[$key]]
                );
            }
        }

        return redirect()->back()->with('success', 'Cấu hình email đã được cập nhật thành công.');
    }

    public function updateMaintenanceSettings(Request $request)
    {
        $validated = $request->validate([
            'maintenance_mode' => 'nullable|in:1',
            'maintenance_bypass_token' => 'nullable|string|max:255',
            'maintenance_message' => 'nullable|string|max:1000',
        ]);

        // Convert checkbox to proper value
        $newMaintenanceMode = $request->has('maintenance_mode') ? '1' : '0';

        // Get current maintenance mode status
        $currentMaintenanceMode = Setting::getValue(null, 'maintenance_mode') ?? '0';

        // Generate new bypass token if maintenance mode is being toggled
        if ($newMaintenanceMode !== $currentMaintenanceMode) {
            $validated['maintenance_bypass_token'] = \Illuminate\Support\Str::random(32);
        } else {
            // Keep existing token if maintenance mode status hasn't changed
            $validated['maintenance_bypass_token'] = $request->input('maintenance_bypass_token');
        }

        $validated['maintenance_mode'] = $newMaintenanceMode;

        // Update or create settings
        foreach ($validated as $key => $value) {
            if ($value !== null) {
                Setting::updateOrCreate(
                    ['club_id' => null, 'key' => $key, 'is_admin' => 'central'],
                    ['value' => $value]
                );
            }
        }

        $message = $newMaintenanceMode === '1' ? 'Chế độ bảo trì đã được bật với token mới.' : 'Chế độ bảo trì đã được tắt.';
        return redirect()->back()->with('success', $message);
    }

    public function testMail(Request $request)
    {
        $request->validate([
            'to' => 'required|email',
        ]);

        try {
            $subject = 'Test Email - ' . Setting::getValue(null, 'site_name');
            $body = '<p>Đây là email test từ hệ thống ' . Setting::getValue(null, 'site_name') . '. Cấu hình email đã hoạt động bình thường.</p>';

            // Use the existing MailService which handles both SMTP and Microsoft Graph API
            $result = MailService::sendClubMail(null, $request->to, $subject, $body);

            if ($result === true) {
                return redirect()->back()->with('success', 'Email test đã được gửi thành công đến ' . $request->to);
            } else {
                throw new \Exception($result);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi gửi email: ' . $e->getMessage());
        }
    }



    /**
     * Refresh permissions for admin and club
     */
    public function refreshPermissions(Request $request)
    {
        $request->validate([
            'type' => 'required|in:admin,club,all'
        ]);

        try {
            $type = $request->type;
            $command = "permissions:refresh --type={$type}";

            // Run the command
            Artisan::call($command);

            $output = Artisan::output();

            return redirect()->back()->with('success', "Đã chạy lại phân quyền thành công cho {$type}!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi chạy lại phân quyền: ' . $e->getMessage());
        }
    }

    /**
     * Update theme settings
     */
    public function updateThemeSettings(Request $request)
    {
        $validated = $request->validate([
            'theme_mode' => 'required|in:light,dark',
            'direction' => 'required|in:ltr,rtl',
            'navigation_style' => 'required|in:vertical,horizontal',
            'navigation_menu_style' => 'nullable|in:menu-click,menu-hover,icon-click,icon-hover',
            'sidemenu_layout' => 'required|in:default,closed,icontext,overlay,detached,doublemenu',
            'page_style' => 'required|in:regular,classic,modern',
            'layout_width' => 'required|in:fullwidth,boxed',
            'menu_position' => 'required|in:fixed,scrollable',
            'header_position' => 'required|in:fixed,scrollable',
            'menu_color' => 'required|in:light,dark,color,gradient,transparent',
            'header_color' => 'required|in:light,dark,color,gradient,transparent',
            'primary_color' => 'nullable|string|max:20',
            'background_color' => 'nullable|string|max:20',
            'background_light_color' => 'nullable|string|max:20',
            'background_image' => 'nullable|in:bgimg1,bgimg2,bgimg3,bgimg4,bgimg5',
            'bg_image_1' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'bg_image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'bg_image_3' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'bg_image_4' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'bg_image_5' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        // Custom validation for image dimensions
        $backgroundImageFields = ['bg_image_1', 'bg_image_2', 'bg_image_3', 'bg_image_4', 'bg_image_5'];
        foreach ($backgroundImageFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $imageInfo = getimagesize($file->getPathname());

                if ($imageInfo === false) {
                    return redirect()->back()->with('error', 'Không thể đọc thông tin ảnh.');
                }

                $width = $imageInfo[0];
                $height = $imageInfo[1];

                if ($width !== 360 || $height !== 1000) {
                    return redirect()->back()->with('error', "Ảnh {$field} phải có kích thước 360x1000px. Ảnh hiện tại: {$width}x{$height}px");
                }
            }
        }

        try {
            // Handle background image uploads
            $backgroundImageFields = ['bg_image_1', 'bg_image_2', 'bg_image_3', 'bg_image_4', 'bg_image_5'];
            $uploadedImages = [];

            foreach ($backgroundImageFields as $field) {
                if ($request->hasFile($field)) {
                    // Extract the number from field name (bg_image_1 -> 1)
                    $imageNumber = substr($field, -1);

                    // Delete old background image if exists
                    $backgroundDir = 'theme/backgrounds';
                    $extensions = ['jpg', 'jpeg', 'png', 'gif'];

                    foreach ($extensions as $ext) {
                        $oldImagePath = "{$backgroundDir}/bgimg{$imageNumber}.{$ext}";
                        if (Storage::disk('public')->exists($oldImagePath)) {
                            Storage::disk('public')->delete($oldImagePath);
                        }
                    }

                    // Store new background image
                    $file = $request->file($field);
                    $extension = $file->getClientOriginalExtension();
                    $filename = "bgimg{$imageNumber}.{$extension}";
                    $path = $file->storeAs('theme/backgrounds', $filename, 'public');

                    // Update CSS file to include new background image
                    $this->updateBackgroundImageCSS($imageNumber, $path);

                    // Mark this image as uploaded
                    $uploadedImages[] = "bgimg{$imageNumber}";
                }
            }

            // If new images were uploaded and no background_image was selected, auto-select the first uploaded image
            if (!empty($uploadedImages) && empty($validated['background_image'])) {
                $validated['background_image'] = $uploadedImages[0];
            }

            // Remove background image uploads from validated data before saving to theme settings
            $themeData = array_diff_key($validated, array_flip($backgroundImageFields));

            // Update theme settings
            ThemeSetting::updateTheme($themeData);

            return redirect()->back()->with('success', 'Cài đặt giao diện đã được cập nhật thành công.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi cập nhật cài đặt giao diện: ' . $e->getMessage());
        }
    }

    /**
     * Update CSS file with new background image
     */
    private function updateBackgroundImageCSS($imageNumber, $imagePath)
    {
        $cssFile = public_path('assets/css/theme-backgrounds.css');

        // Create CSS file if it doesn't exist
        if (!file_exists($cssFile)) {
            file_put_contents($cssFile, "/* Theme Background Images */\n");
        }

        $cssContent = file_get_contents($cssFile);

        // Remove existing CSS for this background image
        $pattern = "/\/\* bgimg{$imageNumber} \*\/.*?\/\* end bgimg{$imageNumber} \*\/\s*/s";
        $cssContent = preg_replace($pattern, '', $cssContent);

        // Add new CSS for this background image
        $newCSS = "\n/* bgimg{$imageNumber} */\n";
        $newCSS .= "[data-bg-img='bgimg{$imageNumber}'] .app-sidebar {\n";
        $newCSS .= "    background-image: url('" . Storage::url($imagePath) . "');\n";
        $newCSS .= "    background-size: cover;\n";
        $newCSS .= "    background-position: center;\n";
        $newCSS .= "    background-repeat: no-repeat;\n";
        $newCSS .= "}\n";
        $newCSS .= "/* end bgimg{$imageNumber} */\n";

        $cssContent .= $newCSS;

        file_put_contents($cssFile, $cssContent);

        // Clear any CSS cache
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($cssFile, true);
        }
    }

    /**
     * Update OAuth settings - preserves saved credentials when disabled
     * Supports both GitHub and Microsoft OAuth configurations
     */
    public function updateOAuthSettings(Request $request)
    {
        $validated = $request->validate([
            'github_oauth_enabled' => 'nullable|in:1',
            'github_client_id' => 'nullable|string|max:255',
            'github_client_secret' => 'nullable|string|max:255',
            'microsoft_oauth_enabled' => 'nullable|in:1',
            'microsoft_client_id' => 'nullable|string|max:255',
            'microsoft_client_secret' => 'nullable|string|max:255',
            'microsoft_tenant_id' => 'nullable|string|max:255',
        ]);

        $errors = [];
        $messages = [];

        // Custom validation: GitHub OAuth
        $githubOAuthEnabled = $request->has('github_oauth_enabled') ? '1' : '0';
        if ($githubOAuthEnabled === '1') {
            $existingGithubClientId = Setting::getValue(null, 'github_client_id');
            $existingGithubClientSecret = Setting::getValue(null, 'github_client_secret');

            // If enabling OAuth and no existing credentials, require them
            if ((empty($existingGithubClientId) && empty($validated['github_client_id'])) ||
                (empty($existingGithubClientSecret) && empty($validated['github_client_secret']))
            ) {
                if (empty($validated['github_client_id']) && empty($existingGithubClientId)) {
                    $errors['github_client_id'] = 'Client ID là bắt buộc khi bật OAuth GitHub.';
                }
                if (empty($validated['github_client_secret']) && empty($existingGithubClientSecret)) {
                    $errors['github_client_secret'] = 'Client Secret là bắt buộc khi bật OAuth GitHub.';
                }
            }
        }

        // Custom validation: Microsoft OAuth
        $microsoftOAuthEnabled = $request->has('microsoft_oauth_enabled') ? '1' : '0';
        if ($microsoftOAuthEnabled === '1') {
            $existingMicrosoftClientId = Setting::getValue(null, 'microsoft_client_id');
            $existingMicrosoftClientSecret = Setting::getValue(null, 'microsoft_client_secret');

            // If enabling OAuth and no existing credentials, require them
            if ((empty($existingMicrosoftClientId) && empty($validated['microsoft_client_id'])) ||
                (empty($existingMicrosoftClientSecret) && empty($validated['microsoft_client_secret']))
            ) {
                if (empty($validated['microsoft_client_id']) && empty($existingMicrosoftClientId)) {
                    $errors['microsoft_client_id'] = 'Client ID là bắt buộc khi bật OAuth Microsoft.';
                }
                if (empty($validated['microsoft_client_secret']) && empty($existingMicrosoftClientSecret)) {
                    $errors['microsoft_client_secret'] = 'Client Secret là bắt buộc khi bật OAuth Microsoft.';
                }
            }
        }

        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        // Convert checkboxes to proper values
        $validated['github_oauth_enabled'] = $githubOAuthEnabled;
        $validated['microsoft_oauth_enabled'] = $microsoftOAuthEnabled;

        // Update GitHub OAuth settings
        Setting::updateOrCreate(
            ['club_id' => null, 'key' => 'github_oauth_enabled', 'is_admin' => 'central'],
            ['value' => $validated['github_oauth_enabled']]
        );

        if ($validated['github_oauth_enabled'] === '1') {
            if (!empty($validated['github_client_id'])) {
                Setting::updateOrCreate(
                    ['club_id' => null, 'key' => 'github_client_id', 'is_admin' => 'central'],
                    ['value' => $validated['github_client_id']]
                );
            }

            if (!empty($validated['github_client_secret'])) {
                Setting::updateOrCreate(
                    ['club_id' => null, 'key' => 'github_client_secret', 'is_admin' => 'central'],
                    ['value' => $validated['github_client_secret']]
                );
            }
        }

        // Update Microsoft OAuth settings
        Setting::updateOrCreate(
            ['club_id' => null, 'key' => 'microsoft_oauth_enabled', 'is_admin' => 'central'],
            ['value' => $validated['microsoft_oauth_enabled']]
        );

        if ($validated['microsoft_oauth_enabled'] === '1') {
            if (!empty($validated['microsoft_client_id'])) {
                Setting::updateOrCreate(
                    ['club_id' => null, 'key' => 'microsoft_client_id', 'is_admin' => 'central'],
                    ['value' => $validated['microsoft_client_id']]
                );
            }

            if (!empty($validated['microsoft_client_secret'])) {
                Setting::updateOrCreate(
                    ['club_id' => null, 'key' => 'microsoft_client_secret', 'is_admin' => 'central'],
                    ['value' => $validated['microsoft_client_secret']]
                );
            }

            if (!empty($validated['microsoft_tenant_id'])) {
                Setting::updateOrCreate(
                    ['club_id' => null, 'key' => 'microsoft_tenant_id', 'is_admin' => 'central'],
                    ['value' => $validated['microsoft_tenant_id']]
                );
            }
        }

        // Prepare success messages
        if ($validated['github_oauth_enabled'] === '1') {
            $messages[] = 'Cấu hình OAuth GitHub đã được bật thành công.';
        } else {
            $messages[] = 'Cấu hình OAuth GitHub đã được tắt.';
        }

        if ($validated['microsoft_oauth_enabled'] === '1') {
            $messages[] = 'Cấu hình OAuth Microsoft đã được bật thành công.';
        } else {
            $messages[] = 'Cấu hình OAuth Microsoft đã được tắt.';
        }

        return redirect()->back()->with('success', implode(' ', $messages));
    }
}
