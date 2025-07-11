<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Major;
use Illuminate\Support\Str;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Create parent majors first
        $parentMajors = [
            'Lập trình máy tính' => [
                'id' => Str::uuid(),
                'name' => 'Lập trình máy tính',
                'parent_id' => null,
                'status' => 'active',
                'children' => [
                    'Kỹ thuật phần mềm'
                ]
            ],
            'Quản trị kinh doanh' => [
                'id' => Str::uuid(),
                'name' => 'Quản trị kinh doanh',
                'parent_id' => null,
                'status' => 'active',
                'children' => [
                    'Quản trị kinh doanh',
                    'Logistics - Mua sắm và quản lý cung ứng',
                    'Marketing'
                ]
            ],
            'Thiết kế đồ hoạ' => [
                'id' => Str::uuid(),
                'name' => 'Thiết kế đồ hoạ',
                'parent_id' => null,
                'status' => 'active',
                'children' => [
                    'Thiết kế đồ họa (Graphic Design)'
                ]
            ],
            'Kỹ thuật - Công nghệ bán dẫn' => [
                'id' => Str::uuid(),
                'name' => 'Kỹ thuật - Công nghệ bán dẫn',
                'parent_id' => null,
                'status' => 'active',
                'children' => [
                    'Công nghệ bán dẫn'
                ]
            ],
            'Thể thao điện tử - Esports (Dự kiến)' => [
                'id' => Str::uuid(),
                'name' => 'Thể thao điện tử - Esports (Dự kiến)',
                'parent_id' => null,
                'status' => 'active',
                'children' => [
                    'Thể thao điện tử (Tổng hợp)',
                    'Thể thao điện tử (Hỗ trợ và đào tạo vận động viên)',
                    'Thể thao điện tử (Sản xuất)',
                    'Thể thao điện tử (Quản trị vận hành)'
                ]
            ]
        ];

        // Create parent majors
        foreach ($parentMajors as $key => $parentData) {
            Major::create([
                'id' => $parentData['id'],
                'name' => $parentData['name'],
                'parent_id' => $parentData['parent_id'],
                'status' => $parentData['status']
            ]);
        }

        // Create child majors
        foreach ($parentMajors as $key => $parentData) {
            foreach ($parentData['children'] as $childName) {
                Major::create([
                    'id' => Str::uuid(),
                    'name' => $childName,
                    'parent_id' => $parentData['id'],
                    'status' => 'active'
                ]);
            }
        }
    }
}
