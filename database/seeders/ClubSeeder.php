<?php
// ClubSeeder.php - Updated to create clubs with 2 default roles and 2 members per club
// Changes: Modified to create 2 roles per club, 2 members with corresponding roles, and run ModuleClubSeeder
// NEW: Each user is now also assigned to 1-2 random other clubs as "Phó chủ nhiệm" if possible

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Club;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\UserClubRole;
use Database\Seeders\ModuleClubSeeder;

class ClubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, run ModuleClubSeeder to create modules and permissions
        $this->call(ModuleClubSeeder::class);

        $clubs = [
            [
                'id' => Str::uuid(),
                'name' => 'CLB Âm nhạc Harmony',
                'email' => 'harmony@example.com',
                'phone' => '0123456789',
                'address' => 'Tầng 3, Tòa nhà A, Trường Đại học',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'CLB Thể thao Champions',
                'email' => 'champions@example.com',
                'phone' => '0123456790',
                'address' => 'Sân vận động trường, Khu thể thao',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'CLB Nhiếp ảnh Lens',
                'email' => 'lens@example.com',
                'phone' => '0123456791',
                'address' => 'Phòng 201, Tòa nhà B, Khu học tập',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'CLB Lập trình CodeCraft',
                'email' => 'codecraft@example.com',
                'phone' => '0123456792',
                'address' => 'Phòng Lab 301, Khoa Công nghệ thông tin',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'CLB Văn học Poetry',
                'email' => 'poetry@example.com',
                'phone' => '0123456793',
                'address' => 'Thư viện trường, Tầng 2',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'CLB Khiêu vũ DanceFlow',
                'email' => 'danceflow@example.com',
                'phone' => '0123456794',
                'address' => 'Phòng múa, Trung tâm văn hóa',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'CLB Môi trường GreenEarth',
                'email' => 'greenearth@example.com',
                'phone' => '0123456795',
                'address' => 'Vườn thực nghiệm, Khuôn viên trường',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'CLB Kinh doanh Startup',
                'email' => 'startup@example.com',
                'phone' => '0123456796',
                'address' => 'Văn phòng 401, Tòa nhà C, Khu nghiên cứu',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'CLB Ngoại ngữ LanguageBridge',
                'email' => 'languagebridge@example.com',
                'phone' => '0123456797',
                'address' => 'Phòng 501, Khoa Ngoại ngữ',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'CLB Tình nguyện HeartShare',
                'email' => 'heartshare@example.com',
                'phone' => '0123456798',
                'address' => 'Văn phòng Đoàn Thanh niên, Tầng 1',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert clubs
        DB::table('clubs')->insert($clubs);

        // Create roles and members for each club
        $allCreatedUsers = [];
        $allCreatedRoles = [];
        foreach ($clubs as $index => $clubData) {
            $clubId = (string)$clubData['id'];
            $clubName = $clubData['name'];

            // Get all permissions for branch type
            $allPermissions = Permission::whereHas('module', function ($query) {
                $query->where('type', 'branch');
            })->get();

            // Create Chủ nhiệm role (Leader) with all permissions
            $chuNhiemRole = Role::create([
                'name' => 'Chủ nhiệm',
                'type' => 'branch',
                'club_id' => $clubId,
                'status' => 'active',
                'is_default' => false,
                'is_leader' => 'leader',
                'is_admin' => true,
            ]);

            // Assign all permissions to Chủ nhiệm
            $chuNhiemRole->permissions()->attach($allPermissions->pluck('id'));

            // Create Phó chủ nhiệm role (Member) with limited permissions
            $phoChuNhiemRole = Role::create([
                'name' => 'Phó chủ nhiệm',
                'type' => 'branch',
                'club_id' => $clubId,
                'status' => 'active',
                'is_default' => false,
                'is_leader' => 'member',
                'is_admin' => false,
            ]);

            // Assign limited permissions to Phó chủ nhiệm (view, create, edit for most modules)
            $limitedPermissions = Permission::whereHas('module', function ($query) {
                $query->where('type', 'branch');
            })->whereIn('name', ['view', 'create', 'edit'])->get();

            $phoChuNhiemRole->permissions()->attach($limitedPermissions->pluck('id'));

            // Create Chủ nhiệm member
            $chuNhiemUser = User::create([
                'name' => 'Chủ nhiệm ' . $clubName,
                'email' => 'chunhiem.' . Str::slug($clubName) . '@example.com',
                'password' => bcrypt('12345678'),
                'phone' => '098765432' . str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                'student_code' => 'SV' . str_pad($index + 1, 4, '0', STR_PAD_LEFT) . '01',
                'type' => 'branch',
                'status' => 'active',
                'default_club_id' => $clubId,
            ]);

            // Create Phó chủ nhiệm member
            $phoChuNhiemUser = User::create([
                'name' => 'Phó chủ nhiệm ' . $clubName,
                'email' => 'phochunhiem.' . Str::slug($clubName) . '@example.com',
                'password' => bcrypt('12345678'),
                'phone' => '098765433' . str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                'student_code' => 'SV' . str_pad($index + 1, 4, '0', STR_PAD_LEFT) . '02',
                'type' => 'branch',
                'status' => 'active',
                'default_club_id' => $clubId,
            ]);

            // Assign Chủ nhiệm to club with Chủ nhiệm role
            UserClubRole::create([
                'user_id' => $chuNhiemUser->id,
                'club_id' => $clubId,
                'role_id' => $chuNhiemRole->id,
                'status' => 'active',
            ]);

            // Assign Phó chủ nhiệm to club with Phó chủ nhiệm role
            UserClubRole::create([
                'user_id' => $phoChuNhiemUser->id,
                'club_id' => $clubId,
                'role_id' => $phoChuNhiemRole->id,
                'status' => 'active',
            ]);

            // Lưu lại user và club chính của họ
            $allCreatedUsers[] = [
                'user' => $chuNhiemUser,
                'main_club_id' => $clubId,
            ];
            $allCreatedUsers[] = [
                'user' => $phoChuNhiemUser,
                'main_club_id' => $clubId,
            ];
            // Lưu role Phó chủ nhiệm cho từng club
            $allCreatedRoles[$clubId] = [
                'pho_chu_nhiem_role_id' => $phoChuNhiemRole->id,
            ];
        }

        // Gán mỗi user vào 1-2 club ngẫu nhiên khác với vai trò Phó chủ nhiệm
        $allClubIds = array_map(function ($club) {
            return (string)$club['id'];
        }, $clubs);
        foreach ($allCreatedUsers as $userData) {
            $user = $userData['user'];
            $mainClubId = $userData['main_club_id'];
            // Lấy danh sách club khác club chính
            $otherClubIds = array_filter($allClubIds, function ($id) use ($mainClubId) {
                return $id !== $mainClubId;
            });
            // Lấy ngẫu nhiên 1 hoặc 2 club
            $randomOtherClubIds = collect($otherClubIds)->shuffle()->take(rand(1, 2));
            foreach ($randomOtherClubIds as $otherClubId) {
                $otherClubId = (string)$otherClubId;
                // Lấy role Phó chủ nhiệm của club đó
                $roleId = $allCreatedRoles[$otherClubId]['pho_chu_nhiem_role_id'] ?? null;
                if ($roleId) {
                    // Kiểm tra nếu chưa có UserClubRole này thì mới tạo
                    $exists = UserClubRole::where('user_id', $user->id)
                        ->where('club_id', $otherClubId)
                        ->where('role_id', $roleId)
                        ->exists();
                    if (!$exists) {
                        UserClubRole::create([
                            'user_id' => $user->id,
                            'club_id' => $otherClubId,
                            'role_id' => $roleId,
                            'status' => 'active',
                        ]);
                    }
                }
            }
        }
    }
}
