<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Permission;

class ModuleSeeder extends Seeder
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
                'module_code' => 'course_management',
                'name' => 'Quản Lý Khóa Học',
                'permissions' => [
                    ['name' => 'view', 'display_name' => 'Xem'],
                    ['name' => 'create', 'display_name' => 'Tạo mới'],
                    ['name' => 'edit', 'display_name' => 'Chỉnh sửa'],
                    ['name' => 'delete', 'display_name' => 'Xóa']
                ]
            ],
            [
                'module_code' => 'semester_management',
                'name' => 'Quản Lý Học Kỳ',
                'permissions' => [
                    ['name' => 'view', 'display_name' => 'Xem'],
                    ['name' => 'create', 'display_name' => 'Tạo mới'],
                    ['name' => 'edit', 'display_name' => 'Chỉnh sửa'],
                    ['name' => 'delete', 'display_name' => 'Xóa']
                ]
            ],
            [
                'module_code' => 'club_management',
                'name' => 'Quản Lý Câu Lạc Bộ',
                'permissions' => [
                    ['name' => 'view', 'display_name' => 'Xem'],
                    ['name' => 'create', 'display_name' => 'Tạo mới'],
                    ['name' => 'edit', 'display_name' => 'Chỉnh sửa'],
                    ['name' => 'delete', 'display_name' => 'Xóa']
                ]
            ],
            [
                'module_code' => 'slot_management',
                'name' => 'Quản Lý Slot',
                'permissions' => [
                    ['name' => 'view', 'display_name' => 'Xem'],
                    ['name' => 'create', 'display_name' => 'Tạo mới'],
                    ['name' => 'edit', 'display_name' => 'Chỉnh sửa'],
                    ['name' => 'delete', 'display_name' => 'Xóa']
                ]
            ],
            [
                'module_code' => 'classroom_management',
                'name' => 'Quản Lý Phòng Học',
                'permissions' => [
                    ['name' => 'view', 'display_name' => 'Xem'],
                    ['name' => 'create', 'display_name' => 'Tạo mới'],
                    ['name' => 'edit', 'display_name' => 'Chỉnh sửa'],
                    ['name' => 'delete', 'display_name' => 'Xóa']
                ]
            ],
            [
                'module_code' => 'major_management',
                'name' => 'Quản Lý Chuyên Ngành',
                'permissions' => [
                    ['name' => 'view', 'display_name' => 'Xem'],
                    ['name' => 'create', 'display_name' => 'Tạo mới'],
                    ['name' => 'edit', 'display_name' => 'Chỉnh sửa'],
                    ['name' => 'delete', 'display_name' => 'Xóa']
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
                'module_code' => 'event_management',
                'name' => 'Quản Lý Sự Kiện',
                'permissions' => [
                    ['name' => 'view', 'display_name' => 'Xem'],
                    ['name' => 'approve', 'display_name' => 'Phê duyệt'],
                    ['name' => 'reject', 'display_name' => 'Từ chối'],
                    ['name' => 'view_image', 'display_name' => 'Xem ảnh'],
                    ['name' => 'view_plan', 'display_name' => 'Xem kế hoạch'],
                ]
            ],
            [
                'module_code' => 'quote_management',
                'name' => 'Quản Lý Châm Ngôn',
                'permissions' => [
                    ['name' => 'view', 'display_name' => 'Xem'],
                    ['name' => 'create', 'display_name' => 'Tạo mới'],
                    ['name' => 'edit', 'display_name' => 'Chỉnh sửa'],
                    ['name' => 'delete', 'display_name' => 'Xóa']
                ]
            ],

            [
                'module_code' => 'user_management',
                'name' => 'Quản Lý Người Dùng',
                'permissions' => [
                    ['name' => 'view', 'display_name' => 'Xem'],
                    ['name' => 'create', 'display_name' => 'Tạo mới'],
                    ['name' => 'edit', 'display_name' => 'Chỉnh sửa'],
                    ['name' => 'delete', 'display_name' => 'Xóa'],
                    ['name' => 'my_profile', 'display_name' => 'Hồ sơ cá nhân']
                ]
            ],
            [
                'module_code' => 'member_management',
                'name' => 'Quản Lý Thành Viên',
                'permissions' => [
                    ['name' => 'view', 'display_name' => 'Xem'],
                    ['name' => 'create', 'display_name' => 'Tạo mới'],
                    ['name' => 'edit', 'display_name' => 'Chỉnh sửa'],
                    ['name' => 'delete', 'display_name' => 'Xóa']
                ]
            ]

        ];

        foreach ($modules as $moduleData) {
            $module = Module::create([
                'module_code' => $moduleData['module_code'],
                'name' => $moduleData['name'],
                'type' => 'central'
            ]);

            foreach ($moduleData['permissions'] as $permissionData) {
                Permission::create([
                    'module_id' => $module->id,
                    'name' => $permissionData['name'],
                    'display_name' => $permissionData['display_name']
                ]);
            }
        }
    }
}
