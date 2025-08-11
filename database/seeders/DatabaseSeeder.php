<?php

/**
 * DatabaseSeeder - Updated to include MajorSeeder, SemesterSeeder, CourseSeeder, QuoteSeeder, ProvinceSeeder, CommuneSeeder, ClubSeeder, và EventSeeder
 * MajorSeeder: Seeds major data with parent-child relationships for Computer Programming,
 * Business Administration, Graphic Design, Semiconductor Technology, and Esports majors
 * SemesterSeeder: Seeds academic semester data with Fall, Spring, and Summer semesters
 * CourseSeeder: Seeds course data related to various academic programs
 * QuoteSeeder: Seeds patriotic Vietnamese quotes from Hồ Chí Minh and other sources
 * ProvinceSeeder: Seeds province data from communes.json file
 * CommuneSeeder: Seeds commune data from communes.json file
 * ClubSeeder: Seeds 10 diverse clubs with sample data
 * EventSeeder: Seeds events cho tất cả các club, mỗi club 2-3 sự kiện từ 01/07/2025 đến 30/07/2025
 */

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (env('APP_ENV') !== 'production') {
            // Seed modules and permissions first
            $this->call([
                ClassRoomSeeder::class,
                SlotSeeder::class,
                MajorSeeder::class,
                SemesterSeeder::class,
                CourseSeeder::class,
                QuoteSeeder::class,
                ProvinceSeeder::class,
                CommuneSeeder::class,
                ClubSeeder::class,
                AwardSeeder::class,
                EventSeeder::class,
                NotificationSeeder::class,
            ]);
        }
        $this->call([
            ModuleSeeder::class,
            ClassRoomSeeder::class,
            SlotSeeder::class,
            MajorSeeder::class,
            QuoteSeeder::class,
            ProvinceSeeder::class,
            CommuneSeeder::class,
            CourseSeeder::class,
        ]);

        // Create roles first
        $adminRole = Role::create([
            'name' => 'Admin',
            'type' => 'central',
            'club_id' => null,
            'status' => 'active',
            'is_default' => true
        ]);

        // Assign permissions to roles
        $allPermissions = Permission::all();
        $adminRole->permissions()->attach($allPermissions->pluck('id'));


        // Create users after roles are created
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'), // Default password: 123456
            'role_id' => $adminRole->id,
        ]);
    }
}
