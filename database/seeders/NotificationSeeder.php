<?php
// Created: NotificationSeeder to add sample notifications for testing club notification functionality

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;
use App\Models\Club;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user (first user with admin role)
        $adminUser = User::whereHas('role', function ($query) {
            $query->where('name', 'Admin');
        })->first();

        if (!$adminUser) {
            $adminUser = User::first();
        }

        if (!$adminUser) {
            return;
        }

        // Get some clubs for testing
        $clubs = Club::take(3)->get();
        $clubIds = $clubs->pluck('id')->toArray();

        // Create sample notifications
        $notifications = [
            [
                'title' => 'Chào mừng đến với hệ thống quản lý câu lạc bộ!',
                'content' => 'Chúng tôi rất vui mừng chào đón bạn đến với hệ thống quản lý câu lạc bộ mới. Hệ thống này sẽ giúp bạn quản lý các hoạt động của câu lạc bộ một cách hiệu quả hơn.',
                'target_type' => 'all_clubs',
                'target_club_ids' => null,
                'sender_id' => $adminUser->id,
                'status' => 'active',
            ],
            [
                'title' => 'Hướng dẫn sử dụng hệ thống',
                'content' => 'Để sử dụng hệ thống hiệu quả, vui lòng tham khảo tài liệu hướng dẫn trong phần "Quản Trị Hệ Thống". Nếu có thắc mắc, hãy liên hệ với chúng tôi.',
                'target_type' => 'all_clubs',
                'target_club_ids' => null,
                'sender_id' => $adminUser->id,
                'status' => 'active',
            ],
            [
                'title' => 'Thông báo về sự kiện sắp tới',
                'content' => 'Sẽ có một sự kiện lớn diễn ra vào tuần tới. Các câu lạc bộ vui lòng chuẩn bị và đăng ký tham gia.',
                'target_type' => 'specific_clubs',
                'target_club_ids' => $clubIds,
                'sender_id' => $adminUser->id,
                'status' => 'active',
            ],
            [
                'title' => 'Cập nhật tính năng mới',
                'content' => 'Hệ thống đã được cập nhật với nhiều tính năng mới. Vui lòng kiểm tra và sử dụng các tính năng này để tối ưu hóa hoạt động của câu lạc bộ.',
                'target_type' => 'all_clubs',
                'target_club_ids' => null,
                'sender_id' => $adminUser->id,
                'status' => 'active',
            ],
            [
                'title' => 'Nhắc nhở về báo cáo tháng',
                'content' => 'Vui lòng nộp báo cáo hoạt động tháng này trước ngày 30. Báo cáo cần bao gồm các hoạt động đã thực hiện và kế hoạch cho tháng tới.',
                'target_type' => 'specific_clubs',
                'target_club_ids' => array_slice($clubIds, 0, 2), // Only first 2 clubs
                'sender_id' => $adminUser->id,
                'status' => 'active',
            ],
        ];

        foreach ($notifications as $notificationData) {
            Notification::create($notificationData);
        }

        $this->command->info('Sample notifications created successfully!');
    }
}
