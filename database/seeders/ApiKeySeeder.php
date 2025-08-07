<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApiKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo API key cho admin với full permission
        $adminApiKey = ApiKey::generate(
            name: 'Admin API Key',
            permissions: ['*'], // Full permission
            description: 'API key for admin with full access to all endpoints',
            createdBy: 'System'
        );

        $this->command->info('Admin API Key created successfully!');
        $this->command->line('API Key: ' . $adminApiKey->plain_key);
        $this->command->line('Please save this key securely as it will not be shown again.');

        // Tạo API key chỉ đọc user
        $readOnlyApiKey = ApiKey::generate(
            name: 'Read Only API Key',
            permissions: ['users.read'],
            description: 'API key with read-only access to user endpoints',
            createdBy: 'System'
        );

        $this->command->info('Read Only API Key created successfully!');
        $this->command->line('API Key: ' . $readOnlyApiKey->plain_key);
        $this->command->line('Please save this key securely as it will not be shown again.');
    }
}