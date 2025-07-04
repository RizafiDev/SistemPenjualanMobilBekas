<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use Illuminate\Console\Command;

class GenerateApiKey extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'api:generate-key 
                            {name : The name/description of the API key}
                            {--permissions=* : Specific permissions for this key (optional)}
                            {--expires= : Expiration date in Y-m-d format (optional)}';

    /**
     * The console command description.
     */
    protected $description = 'Generate a new API key for accessing protected endpoints';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $permissions = $this->option('permissions');
        $expiresAt = $this->option('expires');

        // Parse expiration date
        $expirationDate = null;
        if ($expiresAt) {
            try {
                $expirationDate = \Carbon\Carbon::createFromFormat('Y-m-d', $expiresAt)->endOfDay();
            } catch (\Exception $e) {
                $this->error('Invalid expiration date format. Please use Y-m-d format (e.g., 2024-12-31)');
                return 1;
            }
        }

        // Create the API key
        $result = ApiKey::createKey($name, $permissions ?: null, $expirationDate);

        $this->info('API Key generated successfully!');
        $this->line('');
        $this->line('API Key Details:');
        $this->line('================');
        $this->line("ID: {$result['id']}");
        $this->line("Name: {$result['name']}");
        $this->line("Key: {$result['key']}");

        if ($permissions) {
            $this->line("Permissions: " . implode(', ', $permissions));
        } else {
            $this->line("Permissions: All resources (no restrictions)");
        }

        if ($expirationDate) {
            $this->line("Expires: {$expirationDate->format('Y-m-d H:i:s')}");
        } else {
            $this->line("Expires: Never");
        }

        $this->line('');
        $this->warn('⚠️  IMPORTANT: Save this API key securely. It will not be shown again!');
        $this->line('');
        $this->info('Usage examples:');
        $this->line('- Header: Authorization: Bearer ' . $result['key']);
        $this->line('- Header: X-API-Key: ' . $result['key']);
        $this->line('- Query: ?api_key=' . $result['key']);

        return 0;
    }
}
