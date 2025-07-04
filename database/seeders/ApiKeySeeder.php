<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApiKey;

class ApiKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a default API key for testing
        $defaultKey = ApiKey::createKey(
            name: 'Default API Key',
            permissions: null, // Full access to all resources
            expiresAt: null    // Never expires
        );

        $this->command->info('Default API Key created:');
        $this->command->line('Key: ' . $defaultKey['key']);
        $this->command->warn('Save this key securely!');
    }
}
