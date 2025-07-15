<?php
// EventSeeder - Seed các sự kiện cho tất cả các club, mỗi club 2-3 sự kiện từ 01/07/2025 đến 30/07/2025
// Tạo mới file này để phục vụ việc seed dữ liệu mẫu cho bảng events

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Club;
use App\Models\Event;
use App\Models\Semester;
use App\Models\Slot;
use Illuminate\Support\Str;
use App\Models\ClassRoom;

class EventSeeder extends Seeder
{
    public function run()
    {
        $clubs = Club::all();
        $semester = Semester::orderByDesc('id')->first(); // Lấy semester mới nhất
        $slots = Slot::all();
        $classRooms = ClassRoom::all();
        $eventNames = [
            'Hội thảo Kỹ năng Giao tiếp Chuyên nghiệp',
            'Cuộc thi Sáng tạo Robot Mini',
            'Giao lưu CLB Âm nhạc & CLB Nhiếp ảnh',
            'Workshop Lập trình Web với Laravel',
            'Chuyến đi Thiện nguyện tại Mái ấm Hoa Hồng',
            'Ngày hội Thể thao Sinh viên 2025',
            'Talkshow Định hướng Nghề nghiệp CNTT',
            'Cuộc thi Ảnh "Khoảnh khắc tuổi trẻ"',
            'Teambuilding: Vượt thử thách cùng CLB',
            'Hội thảo Khởi nghiệp & Đổi mới sáng tạo',
            'Workshop Thiết kế Đồ họa 3D',
            'Giải Bóng đá Giao hữu các CLB',
            'Ngày hội Sách và Văn hóa đọc',
            'Cuộc thi Hùng biện tiếng Anh',
            'Gala Tổng kết năm học 2025',
        ];
        $dateStart = strtotime('2025-07-01');
        $dateEnd = strtotime('2025-07-30');
        foreach ($clubs as $club) {
            $eventCount = rand(2, 3);
            for ($i = 0; $i < $eventCount; $i++) {
                $eventDate = date('Y-m-d', rand($dateStart, $dateEnd));
                $slot = $slots->random();
                $classRoom = $classRooms->random();
                Event::create([
                    'name' => $eventNames[array_rand($eventNames)],
                    'description' => 'Sự kiện mẫu cho club ' . $club->name,
                    'club_id' => $club->id,
                    'semester_id' => $semester ? $semester->id : null,
                    'slot_id' => $slot->id,
                    'event_date' => $eventDate,
                    'class_room_id' => $classRoom->id,
                    'status' => 'pending',
                ]);
            }
        }
    }
}
