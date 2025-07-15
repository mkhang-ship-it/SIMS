<?php
// Updated ClassRoomSeeder - Seeds classroom data with room names P103, P201-P208, and Semiconductor Practice
// All classrooms set to active status by default
// Code field now uses random CR-XXX format for uniqueness, and ensures no duplicate code is generated
// Longitude and latitude stored as string values for flexible coordinate input

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ClassRoom;

class ClassRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classrooms = [
            [
                'name' => 'P103',
                'status' => 'active',
                'longitude' => '105.851067',
                'latitude' => '21.034888'
            ],
            [
                'name' => 'P201',
                'status' => 'active',
                'longitude' => '105.851067',
                'latitude' => '21.034888'
            ],
            [
                'name' => 'P202',
                'status' => 'active',
                'longitude' => '105.851067',
                'latitude' => '21.034888'
            ],
            [
                'name' => 'P203',
                'status' => 'active',
                'longitude' => '105.851067',
                'latitude' => '21.034888'
            ],
            [
                'name' => 'P204',
                'status' => 'active',
                'longitude' => '105.851067',
                'latitude' => '21.034888'
            ],
            [
                'name' => 'P205',
                'status' => 'active',
                'longitude' => '105.851067',
                'latitude' => '21.034888'
            ],
            [
                'name' => 'P206',
                'status' => 'active',
                'longitude' => '105.851067',
                'latitude' => '21.034888'
            ],
            [
                'name' => 'P207',
                'status' => 'active',
                'longitude' => '105.851067',
                'latitude' => '21.034888'
            ],
            [
                'name' => 'P208',
                'status' => 'active',
                'longitude' => '105.851067',
                'latitude' => '21.034888'
            ],
            [
                'name' => 'Semiconductor Practice',
                'status' => 'active',
                'longitude' => '105.851067',
                'latitude' => '21.034888'
            ]
        ];

        foreach ($classrooms as $classroom) {
            // Generate random code for each classroom, ensure uniqueness
            do {
                $code = 'CR-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            } while (ClassRoom::where('code', $code)->exists());

            ClassRoom::create([
                'name' => $classroom['name'],
                'code' => $code,
                'status' => $classroom['status'],
                'longitude' => $classroom['longitude'],
                'latitude' => $classroom['latitude']
            ]);
        }
    }
}
