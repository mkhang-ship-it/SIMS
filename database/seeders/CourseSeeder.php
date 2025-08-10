<?php

/**
 * CourseSeeder - Creates sample course data for the academic system
 * Includes various courses related to programming, business, design, semiconductor technology, and esports
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            // Programming courses
            [
                'id' => Str::uuid(),
                'name' => 'Khóa 6',
                'start_date' => '2022-08-01',
                'end_date' => '2025-04-30',
                'status' => 'active'
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Khóa 7',
                'start_date' => '2023-08-01',
                'end_date' => '2026-04-30',
                'status' => 'active'
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Khóa 8',
                'start_date' => '2024-08-01',
                'end_date' => '2027-04-30',
                'status' => 'active'
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Khóa 9',
                'start_date' => '2025-08-01',
                'end_date' => '2028-04-30',
                'status' => 'active'
            ],
        ];

        foreach ($courses as $courseData) {
            Course::create($courseData);
        }
    }
}
