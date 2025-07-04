<?php

namespace App\Filament\Resources\ApiKeyResource\Pages;

use App\Filament\Resources\ApiKeyResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewApiKey extends ViewRecord
{
    protected static string $resource = ApiKeyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('revoke')
                ->label('Revoke Key')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Revoke API Key')
                ->modalDescription('Are you sure you want to revoke this API key? This action cannot be undone.')
                ->action(function () {
                    $this->getRecord()->update(['is_active' => false]);
                    $this->refreshFormData(['is_active']);
                })
                ->visible(fn() => $this->getRecord()->is_active),
        ];
    }
}
