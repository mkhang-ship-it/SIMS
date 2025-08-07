<?php
// ModuleClubSeeder - Updated to prevent duplicate entries when creating multiple clubs
// Uses firstOrCreate() to avoid integrity constraint violations
// Ensures modules and permissions are created only once and reused for multiple clubs

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Permission;

class ModuleClubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'module_code' => 'role_management',
                'name' => 'Quản Lý Vai Trò',
                'permissions' => [
                    ['name' => 'view', 'display_name' => 'Xem'],
                    ['name' => 'create', 'display_name' => 'Tạo mới'],
                    ['name' => 'edit', 'display_name' => 'Chỉnh sửa'],
                    ['name' => 'delete', 'display_name' => 'Xóa']
                ]
            ],
            [
                'module_code' => 'member_management',
                'name' => 'Quản Lý Thành Viên',
                'permissions' => [
                    ['name' => 'view', 'display_name' => 'Xem'],
                    ['name' => 'create', 'display_name' => 'Tạo mới'],
                    ['name' => 'edit', 'display_name' => 'Chỉnh sửa'],
                    ['name' => 'delete', 'display_name' => 'Xóa'],
                    ['name' => 'my_profile', 'display_name' => 'Hồ sơ cá nhân']
                ]
            ],
            [
                'module_code' => 'award_management',
                'name' => 'Quản Lý Giải Thưởng',
                'permissions' => [
                    ['name' => 'view', 'display_name' => 'Xem'],
                    ['name' => 'create', 'display_name' => 'Tạo mới'],
                    ['name' => 'edit', 'display_name' => 'Chỉnh sửa'],
                    ['name' => 'delete', 'display_name' => 'Xóa']
                ]
            ],
            [
                'module_code' => 'event_management',
                'name' => 'Quản Lý Sự Kiện',
                'permissions' => [
                    ['name' => 'view', 'display_name' => 'Xem'],
                    ['name' => 'create', 'display_name' => 'Tạo mới'],
                    ['name' => 'edit', 'display_name' => 'Chỉnh sửa'],
                    ['name' => 'delete', 'display_name' => 'Xóa'],
                    ['name' => 'checkin', 'display_name' => 'Check-in'],
                    ['name' => 'view_timetable', 'display_name' => 'Xem lịch sự kiện'],
                    ['name' => 'my_checkin', 'display_name' => 'Xem sự kiện đã check-in']
                ]
            ],
            [
                'module_code' => 'event_plan_management',
                'name' => 'Quản Lý Kế Hoạch Sự Kiện',
                'permissions' => [
                    ['name' => 'view', 'display_name' => 'Xem'],
                    ['name' => 'create', 'display_name' => 'Tạo mới'],
                    ['name' => 'edit', 'display_name' => 'Chỉnh sửa'],
                    ['name' => 'delete', 'display_name' => 'Xóa']
                ]
            ],
            [
                'module_code' => 'event_image_management',
                'name' => 'Quản Lý Ảnh Sự Kiện',
                'permissions' => [
                    ['name' => 'view', 'display_name' => 'Xem'],
                    ['name' => 'create', 'display_name' => 'Tạo mới'],
                    ['name' => 'delete', 'display_name' => 'Xóa'],
                    ['name' => 'view_image', 'display_name' => 'Xem ảnh']
                ]
            ],
            [
                'module_code' => 'invoice_management',
                'name' => 'Quản Lý Hóa Đơn',
                'permissions' => [
                    ['name' => 'view', 'display_name' => 'Xem'],
                    ['name' => 'create', 'display_name' => 'Tạo mới'],
                    ['name' => 'edit', 'display_name' => 'Chỉnh sửa'],
                    ['name' => 'delete', 'display_name' => 'Xóa'],
                    ['name' => 'make_payment', 'display_name' => 'Thanh toán'],
                    ['name' => 'payment_history', 'display_name' => 'Lịch sử thanh toán'],
                    ['name' => 'my_invoices', 'display_name' => 'Hóa đơn của tôi'],
                    ['name' => 'mark_as_paid', 'display_name' => 'Ghi nhận thanh toán']
                ]
            ],
            [
                'module_code' => 'expense_management',
                'name' => 'Quản Lý Chi Phí',
                'permissions' => [
                    ['name' => 'view', 'display_name' => 'Xem'],
                    ['name' => 'create', 'display_name' => 'Tạo mới'],
                    ['name' => 'edit', 'display_name' => 'Chỉnh sửa'],
                    ['name' => 'delete', 'display_name' => 'Xóa'],
                    ['name' => 'approve', 'display_name' => 'Phê duyệt'],
                    ['name' => 'reject', 'display_name' => 'Từ chối'],
                    ['name' => 'mark_as_paid', 'display_name' => 'Đánh dấu đã thanh toán'],
                    ['name' => 'my_create', 'display_name' => 'Yêu cầu chi phí'],
                    ['name' => 'my_expenses', 'display_name' => 'Chi phí của tôi'],
                    ['name' => 'my_edit', 'display_name' => 'Chỉnh sửa chi phí'],
                    ['name' => 'my_delete', 'display_name' => 'Xóa chi phí']
                ]
            ],
            [
                'module_code' => 'setting_management',
                'name' => 'Quản Lý Cài Đặt',
                'permissions' => [
                    ['name' => 'view', 'display_name' => 'Xem'],
                    ['name' => 'general', 'display_name' => 'Thông tin chung'],
                    ['name' => 'word_template', 'display_name' => 'Mẫu word'],
                    ['name' => 'payment', 'display_name' => 'Thanh toán'],
                    ['name' => 'api_key', 'display_name' => 'API Keys'],
                    ['name' => 'mail', 'display_name' => 'Email']
                ]
            ],
            [
                'module_code' => 'dashboard',
                'name' => 'Dashboard',
                'permissions' => [
                    ['name' => 'view', 'display_name' => 'Xem']
                ]
            ],
            [
                'module_code' => 'notification_management',
                'name' => 'Quản Lý Thông Báo',
                'permissions' => [
                    ['name' => 'view', 'display_name' => 'Xem'],
                ]
            ],
            [
                'module_code' => 'lucky_draw_management',
                'name' => 'Quản Lý Lucky Draw',
                'permissions' => [
                    ['name' => 'view', 'display_name' => 'Xem'],
                    ['name' => 'create', 'display_name' => 'Tạo mới'],
                    ['name' => 'edit', 'display_name' => 'Chỉnh sửa'],
                    ['name' => 'delete', 'display_name' => 'Xóa']
                ]
            ]
        ];

        foreach ($modules as $moduleData) {
            // Use firstOrCreate to avoid duplicate entries
            $module = Module::firstOrCreate(
                [
                    'module_code' => $moduleData['module_code'],
                    'type' => 'branch'
                ],
                [
                    'name' => $moduleData['name']
                ]
            );

            foreach ($moduleData['permissions'] as $permissionData) {
                // Use firstOrCreate for permissions as well to avoid duplicates
                Permission::firstOrCreate(
                    [
                        'module_id' => $module->id,
                        'name' => $permissionData['name']
                    ],
                    [
                        'display_name' => $permissionData['display_name']
                    ]
                );
            }
        }
    }
}
