<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use Illuminate\Console\Command;

class RevokeApiKey extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'api:revoke-key {id : The ID of the API key to revoke}';

    /**
     * The console command description.
     */
    protected $description = 'Revoke an API key';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $keyId = $this->argument('id');

        $apiKey = ApiKey::find($keyId);

        if (!$apiKey) {
            $this->error("API key with ID {$keyId} not found.");
            return 1;
        }

        if (!$apiKey->is_active) {
            $this->warn("API key '{$apiKey->name}' is already inactive.");
            return 0;
        }

        $apiKey->update(['is_active' => false]);

        $this->info("API key '{$apiKey->name}' (ID: {$apiKey->id}) has been revoked successfully.");

        return 0;
    }
}
