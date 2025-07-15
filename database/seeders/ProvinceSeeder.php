<?php

// Changes made: Created ProvinceSeeder to import province data from communes.json file

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Province;
use Illuminate\Support\Facades\File;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Read the JSON file
        $jsonPath = public_path('communes.json');

        if (!File::exists($jsonPath)) {
            $this->command->error('communes.json file not found in public directory');
            return;
        }

        $jsonData = File::get($jsonPath);
        $data = json_decode($jsonData, true);

        if (!isset($data['communes'])) {
            $this->command->error('Invalid JSON structure. Expected "communes" key.');
            return;
        }

        // Extract unique provinces
        $provinces = collect($data['communes'])
            ->groupBy('provinceCode')
            ->map(function ($communes, $provinceCode) {
                $firstCommune = $communes->first();
                return [
                    'code' => $provinceCode,
                    'name' => $firstCommune['provinceName'],
                ];
            })
            ->values()
            ->toArray();

        // Insert provinces
        foreach ($provinces as $province) {
            Province::updateOrCreate(
                ['code' => $province['code']],
                [
                    'name' => $province['name'],
                ]
            );
        }

    }
}
