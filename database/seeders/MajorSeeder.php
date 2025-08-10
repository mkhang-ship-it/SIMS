<?php
// Updated: Bổ sung tiếng Anh cho tên chuyên ngành bằng cách mở ngoặc hoặc ra ngoài.

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

        // Create parent majors first, bổ sung tiếng Anh bằng cách mở ngoặc hoặc ra ngoài
        $parentMajors = [
            'Lập trình máy tính' => [
                'id' => Str::uuid(),
                'name' => 'Lập trình máy tính (Computer Programming)',
                'parent_id' => null,
                'status' => 'active',
                'children' => [
                    'Kỹ thuật phần mềm (Software Engineering)'
                ]
            ],
            'Quản trị kinh doanh' => [
                'id' => Str::uuid(),
                'name' => 'Quản trị kinh doanh (Business Administration)',
                'parent_id' => null,
                'status' => 'active',
                'children' => [
                    'Quản trị kinh doanh (Business Administration)',
                    'Logistics - Mua sắm và quản lý cung ứng (Logistics - Procurement and Supply Chain Management)',
                    'Marketing (Marketing)'
                ]
            ],
            'Thiết kế đồ hoạ' => [
                'id' => Str::uuid(),
                'name' => 'Thiết kế đồ hoạ (Graphic Design)',
                'parent_id' => null,
                'status' => 'active',
                'children' => [
                    'Thiết kế đồ họa (Graphic Design)'
                ]
            ],
            'Kỹ thuật - Công nghệ bán dẫn' => [
                'id' => Str::uuid(),
                'name' => 'Kỹ thuật - Công nghệ bán dẫn (Semiconductor Engineering & Technology)',
                'parent_id' => null,
                'status' => 'active',
                'children' => [
                    'Công nghệ bán dẫn (Semiconductor Technology)'
                ]
            ],
            'Thể thao điện tử - Esports (Dự kiến)' => [
                'id' => Str::uuid(),
                'name' => 'Thể thao điện tử - Esports (Dự kiến) (Esports - Expected)',
                'parent_id' => null,
                'status' => 'active',
                'children' => [
                    'Thể thao điện tử (Tổng hợp) (Esports - General)',
                    'Thể thao điện tử (Hỗ trợ và đào tạo vận động viên) (Esports - Athlete Support & Training)',
                    'Thể thao điện tử (Sản xuất) (Esports - Production)',
                    'Thể thao điện tử (Quản trị vận hành) (Esports - Operations Management)'
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
