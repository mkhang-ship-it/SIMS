<?php

// Changes made: Created CommuneSeeder to import commune data from communes.json file

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Commune;
use App\Models\Province;
use Illuminate\Support\Facades\File;

class CommuneSeeder extends Seeder
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

        // Get all provinces for mapping
        $provinces = Province::all()->keyBy('code');

        $communes = collect($data['communes'])->map(function ($commune) use ($provinces) {
            $province = $provinces->get($commune['provinceCode']);

            if (!$province) {
                $this->command->warn("Province with code {$commune['provinceCode']} not found for commune {$commune['name']}");
                return null;
            }

            return [
                'code' => $commune['code'],
                'name' => $commune['name'],
                'province_id' => $province->id,
            ];
        })->filter()->toArray();

        // Insert communes in batches to avoid memory issues
        $batchSize = 1000;
        $totalCommunes = count($communes);

        for ($i = 0; $i < $totalCommunes; $i += $batchSize) {
            $batch = array_slice($communes, $i, $batchSize);

            foreach ($batch as $commune) {
                Commune::updateOrCreate(
                    ['code' => $commune['code']],
                    [
                        'name' => $commune['name'],
                        'province_id' => $commune['province_id']
                    ]
                );
            }
        }
    }
}
