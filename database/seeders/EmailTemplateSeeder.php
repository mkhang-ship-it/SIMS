<?php
// [2024-07-20] Seeder các mẫu email động cho 3 module: hóa đơn, chi phí, giải thưởng. Mỗi mẫu có code, name, default_title, default_content (tiếng Việt, dùng biến động {{...}}), available_keywords đúng nghiệp vụ.

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Hóa đơn
        EmailTemplate::updateOrCreate([
            'code' => 'INVOICE',
        ], [
            'name' => 'Thông báo hóa đơn',
            'default_title' => 'Thông báo hóa đơn {{invoice_code}} từ {{club_name}}',
            'default_content' =>
            'Xin chào {{user_name}},\n\n'
                . 'Bạn nhận được hóa đơn mới từ {{club_name}}.\n\n'
                . 'Thông tin hóa đơn:\n'
                . '- Mã hóa đơn: {{invoice_code}}\n'
                . '- Tên hóa đơn: {{invoice_name}}\n'
                . '- Số tiền: {{total_amount}}\n'
                . '- Hạn thanh toán: {{due_date}}\n'
                . '- Trạng thái: {{status}}\n'
                . '- Ghi chú: {{note}}\n\n'
                . 'Vui lòng thanh toán trước hạn để tránh gián đoạn quyền lợi.\n\n'
                . 'Trân trọng,\n{{club_name}}',
            'available_keywords' => [
                'club_name',
                'club_email',
                'club_phone',
                'club_address',
                'user_name',
                'user_email',
                'invoice_code',
                'invoice_name',
                'total_amount',
                'due_date',
                'status',
                'semester_name',
                'note',
                'paid_amount',
                'remaining_amount',
                'payment_history'
            ],
        ]);

        // 2. Chi phí
        EmailTemplate::updateOrCreate([
            'code' => 'EXPENSE',
        ], [
            'name' => 'Thông báo chi phí',
            'default_title' => 'Thông báo chi phí {{expense_name}} từ {{club_name}}',
            'default_content' =>
            'Xin chào {{requested_by_name}},\n\n'
                . 'Yêu cầu chi phí của bạn đã được cập nhật.\n\n'
                . 'Thông tin chi phí:\n'
                . '- Tên chi phí: {{expense_name}}\n'
                . '- Số tiền: {{amount}}\n'
                . '- Trạng thái: {{status}}\n'
                . '- Người phê duyệt: {{approved_by_name}}\n'
                . '- Ngày phê duyệt: {{approved_at}}\n'
                . '- Ghi chú: {{description}}\n\n'
                . 'Vui lòng kiểm tra chi tiết trong hệ thống.\n\n'
                . 'Trân trọng,\n{{club_name}}',
            'available_keywords' => [
                'club_name',
                'club_email',
                'club_phone',
                'club_address',
                'expense_name',
                'amount',
                'requested_by_name',
                'requested_by_email',
                'approved_by_name',
                'approved_by_email',
                'approved_at',
                'requested_at',
                'status',
                'semester_name',
                'description',
                'paid_amount',
                'remaining_amount',
                'payment_history'
            ],
        ]);

        // 3. Giải thưởng
        EmailTemplate::updateOrCreate([
            'code' => 'AWARD',
        ], [
            'name' => 'Thông báo nhận giải thưởng',
            'default_title' => 'Chúc mừng {{recipient_name}} nhận giải thưởng {{award_title}}',
            'default_content' =>
            'Xin chúc mừng {{recipient_name}},\n\n'
                . 'Bạn đã được nhận giải thưởng: {{award_title}}\n'
                . '- Mã giải thưởng: {{award_code}}\n'
                . '- Ngày trao: {{award_date}}\n'
                . '- Học kỳ: {{semester_name}}\n'
                . '- Vai trò: {{recipient_role}}\n\n'
                . 'Ghi chú: {{notes}}\n\n'
                . 'Vui lòng liên hệ Ban Chủ Nhiệm {{club_name}} để nhận phần thưởng (nếu có).\n\n'
                . 'Trân trọng,\n{{club_name}}',
            'available_keywords' => [
                'club_name',
                'club_email',
                'club_phone',
                'club_address',
                'award_code',
                'award_title',
                'award_date',
                'semester_name',
                'recipient_name',
                'recipient_email',
                'recipient_role',
                'notes',
                'attachment'
            ],
        ]);
    }
}
