<?php

namespace App\Filament\Resources\ApiKeyResource\Pages;

use App\Filament\Resources\ApiKeyResource;
use App\Models\ApiKey;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;

class CreateApiKey extends CreateRecord
{
    protected static string $resource = ApiKeyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate the API key
        $keyData = ApiKey::createKey(
            name: $data['name'],
            permissions: $data['permissions'] ?? null,
            expiresAt: $data['expires_at'] ?? null
        );

        // Store the plain key temporarily for display
        session()->put('generated_api_key', $keyData['key']);

        // Remove the plain key from data to save
        unset($data['key']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        $generatedKey = session()->pull('generated_api_key');

        if ($generatedKey) {
            return Notification::make()
                ->title('API Key Created Successfully')
                ->body("Your API key: {$generatedKey}")
                ->success()
                ->persistent()
                ->actions([
                    \Filament\Notifications\Actions\Action::make('copy')
                        ->label('Copy Key')
                        ->button()
                        ->color('primary')
                        ->action(function () use ($generatedKey) {
                            // This would require JavaScript to copy to clipboard
                            // For now, we'll just show the key
                        }),
                ]);
        }

        return parent::getCreatedNotification();
    }
}
