<?php
// Updated: AwardSeeder - Fix table name typo (award_recipients, not award_recipient) by using AwardRecipient::query()
// Mỗi user trong club đều có ít nhất 1 giải thưởng (Award), gán cho club, user_club_role, semester (active mới nhất), ngày trao hôm nay, status 'awarded'.

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Award;
use App\Models\Club;
use App\Models\User;
use App\Models\Semester;
use App\Models\UserClubRole;
use App\Models\AwardRecipient;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AwardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy học kỳ active mới nhất
        $semester = Semester::where('status', 'active')->orderByDesc('start_date')->first();
        if (!$semester) {
            $this->command->warn('Không tìm thấy học kỳ active. Bỏ qua seed giải thưởng.');
            return;
        }

        // Lặp qua từng club
        $clubs = Club::all();
        foreach ($clubs as $club) {
            // Lấy tất cả user_club_roles thuộc club này
            $userClubRoles = UserClubRole::with('user')->where('club_id', $club->id)->get();
            foreach ($userClubRoles as $ucr) {
                // Kiểm tra user_club_role đã có giải thưởng trong club này chưa (dùng AwardRecipient)
                $hasAward = AwardRecipient::query()
                    ->where('user_club_role_id', $ucr->id)
                    ->whereHas('award', function ($q) use ($club) {
                        $q->where('club_id', $club->id);
                    })->exists();
                if (!$hasAward) {
                    // Tạo giải thưởng cho user này trong club
                    $award = Award::create([
                        'title' => 'Giải thưởng thành viên tích cực',
                        'club_id' => $club->id,
                        'semester_id' => $semester->id,
                        'award_date' => now(),
                        'status' => 'awarded',
                        'notes' => 'Tự động tạo bởi AwardSeeder',
                    ]);
                    // Gán user_club_role là recipient
                    $award->recipients()->attach($ucr->id, ['attachment' => null]);
                }
            }
        }
    }
}
