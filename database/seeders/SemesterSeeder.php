<?php

/**
 * SemesterSeeder - Creates sample semester data for the academic system
 * Includes Fall, Spring, and Summer semesters with appropriate date ranges
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Semester;
use Illuminate\Support\Str;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $semesters = [
            [
                'id' => Str::uuid(),
                'name' => 'Học kỳ I - 2023-2024',
                'start_date' => '2023-09-01',
                'end_date' => '2024-01-31',
                'status' => 'inactive'
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Học kỳ II - 2023-2024',
                'start_date' => '2024-02-01',
                'end_date' => '2024-06-30',
                'status' => 'inactive'
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Học kỳ hè - 2024',
                'start_date' => '2024-07-01',
                'end_date' => '2024-08-31',
                'status' => 'inactive'
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Học kỳ I - 2024-2025',
                'start_date' => '2024-09-01',
                'end_date' => '2025-01-31',
                'status' => 'active'
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Học kỳ II - 2024-2025',
                'start_date' => '2025-02-01',
                'end_date' => '2025-06-30',
                'status' => 'active'
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Học kỳ hè - 2025',
                'start_date' => '2025-07-01',
                'end_date' => '2025-08-31',
                'status' => 'active'
            ]
        ];

        foreach ($semesters as $semesterData) {
            Semester::create($semesterData);
        }
    }
}
