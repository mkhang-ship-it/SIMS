<?php
// Created SlotSeeder - Seeds slot data with 7 time slots from 7:15-22:30
// All slots set to active status by default
// Time format stored as HH:MM for start_time and end_time fields

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Slot;

class SlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $slots = [
            [
                'name' => 'Slot 1',
                'start_time' => '07:15',
                'end_time' => '09:15',
                'status' => 'active'
            ],
            [
                'name' => 'Slot 2',
                'start_time' => '09:25',
                'end_time' => '11:25',
                'status' => 'active'
            ],
            [
                'name' => 'Slot 3',
                'start_time' => '12:00',
                'end_time' => '14:00',
                'status' => 'active'
            ],
            [
                'name' => 'Slot 4',
                'start_time' => '14:10',
                'end_time' => '16:10',
                'status' => 'active'
            ],
            [
                'name' => 'Slot 5',
                'start_time' => '16:20',
                'end_time' => '18:20',
                'status' => 'active'
            ],
            [
                'name' => 'Slot 6',
                'start_time' => '18:30',
                'end_time' => '20:30',
                'status' => 'active'
            ],
            [
                'name' => 'Slot 7',
                'start_time' => '20:30',
                'end_time' => '22:30',
                'status' => 'active'
            ]
        ];

        foreach ($slots as $slot) {
            Slot::create($slot);
        }
    }
}
