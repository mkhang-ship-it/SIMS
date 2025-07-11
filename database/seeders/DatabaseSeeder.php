<?php

/**
 * DatabaseSeeder - Updated to include MajorSeeder, SemesterSeeder, CourseSeeder, and QuoteSeeder
 * MajorSeeder: Seeds major data with parent-child relationships for Computer Programming,
 * Business Administration, Graphic Design, Semiconductor Technology, and Esports majors
 * SemesterSeeder: Seeds academic semester data with Fall, Spring, and Summer semesters
 * CourseSeeder: Seeds course data related to various academic programs
 * QuoteSeeder: Seeds patriotic Vietnamese quotes from Hồ Chí Minh and other sources
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
        // Seed modules and permissions first
        $this->call([
            ModuleSeeder::class,
            ClassRoomSeeder::class,
            SlotSeeder::class,
            MajorSeeder::class,
            SemesterSeeder::class,
            CourseSeeder::class,
            QuoteSeeder::class,
        ]);

        // Create roles first
        $adminRole = Role::create([
            'name' => 'Admin',
            'type' => 'central',
            'club_id' => null,
            'status' => 'active',
            'is_default' => true
        ]);

        $managerRole = Role::create([
            'name' => 'Manager',
            'type' => 'central',
            'club_id' => null,
            'status' => 'active',
            'is_default' => false
        ]);

        $userRole = Role::create([
            'name' => 'User',
            'type' => 'central',
            'club_id' => null,
            'status' => 'active',
            'is_default' => false
        ]);

        // Assign permissions to roles
        $allPermissions = Permission::all();
        $adminRole->permissions()->attach($allPermissions->pluck('id'));

        $managerPermissions = Permission::whereIn('name', ['view', 'edit'])->get();
        $managerRole->permissions()->attach($managerPermissions->pluck('id'));

        $viewPermissions = Permission::where('name', 'view')->get();
        $userRole->permissions()->attach($viewPermissions->pluck('id'));

        // Create users after roles are created
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'), // Default password: 123456
            'role_id' => $adminRole->id,
        ]);

        User::create([
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => bcrypt('12345678'), // Default password: 123456
            'role_id' => $userRole->id,
        ]);
    }
}
