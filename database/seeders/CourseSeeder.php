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
                'start_date' => '2024-09-01',
                'end_date' => '2024-12-31',
                'status' => 'active'
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Khóa 7',
                'start_date' => '2024-09-01',
                'end_date' => '2025-01-31',
                'status' => 'active'
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Khóa 8',
                'start_date' => '2025-02-01',
                'end_date' => '2025-06-30',
                'status' => 'active'
            ],
        ];

        foreach ($courses as $courseData) {
            Course::create($courseData);
        }
    }
}
