<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use Illuminate\Console\Command;

class ListApiKeys extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'api:list-keys {--show-inactive : Show inactive keys as well}';

    /**
     * The console command description.
     */
    protected $description = 'List all API keys';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $showInactive = $this->option('show-inactive');

        $query = ApiKey::query();

        if (!$showInactive) {
            $query->where('is_active', true);
        }

        $apiKeys = $query->orderBy('created_at', 'desc')->get();

        if ($apiKeys->isEmpty()) {
            $this->info('No API keys found.');
            return 0;
        }

        $headers = ['ID', 'Name', 'Status', 'Permissions', 'Last Used', 'Expires', 'Created'];
        $rows = [];

        foreach ($apiKeys as $apiKey) {
            $status = $apiKey->is_active ? 'Active' : 'Inactive';
            if ($apiKey->isExpired()) {
                $status = 'Expired';
            }

            $permissions = $apiKey->permissions
                ? implode(', ', $apiKey->permissions)
                : 'All resources';

            $lastUsed = $apiKey->last_used_at
                ? $apiKey->last_used_at->format('Y-m-d H:i:s')
                : 'Never';

            $expires = $apiKey->expires_at
                ? $apiKey->expires_at->format('Y-m-d H:i:s')
                : 'Never';

            $rows[] = [
                $apiKey->id,
                $apiKey->name,
                $status,
                $permissions,
                $lastUsed,
                $expires,
                $apiKey->created_at->format('Y-m-d H:i:s'),
            ];
        }

        $this->table($headers, $rows);

        return 0;
    }
}
