<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'key',
        'is_active',
        'permissions',
        'last_used_at',
        'expires_at',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected $hidden = [
        'key',
    ];

    /**
     * Generate a new API key
     */
    public static function generateKey(): string
    {
        return 'sk_' . Str::random(48);
    }

    /**
     * Create a new API key
     */
    public static function createKey(string $name, array $permissions = null, $expiresAt = null): array
    {
        $plainKey = self::generateKey();

        $apiKey = self::create([
            'name' => $name,
            'key' => Hash::make($plainKey),
            'permissions' => $permissions,
            'expires_at' => $expiresAt,
        ]);

        return [
            'id' => $apiKey->id,
            'key' => $plainKey, // Return plain key only once
            'name' => $apiKey->name,
        ];
    }

    /**
     * Verify an API key
     */
    public static function verify(string $key): ?self
    {
        $apiKeys = self::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->get();

        foreach ($apiKeys as $apiKey) {
            if (Hash::check($key, $apiKey->key)) {
                // Update last used timestamp
                $apiKey->update(['last_used_at' => now()]);
                return $apiKey;
            }
        }

        return null;
    }

    /**
     * Check if the API key has specific permission
     */
    public function hasPermission(string $permission): bool
    {
        if (empty($this->permissions)) {
            return true; // No restrictions
        }

        return in_array($permission, $this->permissions) || in_array('*', $this->permissions);
    }

    /**
     * Check if the API key has access to specific resource
     */
    public function hasResourceAccess(string $resource): bool
    {
        if (empty($this->permissions)) {
            return true; // No restrictions
        }

        return $this->hasPermission($resource) || $this->hasPermission('*');
    }

    /**
     * Check if the API key is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at < now();
    }

    /**
     * Check if the API key is valid
     */
    public function isValid(): bool
    {
        return $this->is_active && !$this->isExpired();
    }
}
