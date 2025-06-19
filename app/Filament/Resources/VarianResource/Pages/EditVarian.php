<?php

namespace App\Filament\Resources\VarianResource\Pages;

use App\Filament\Resources\VarianResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVarian extends EditRecord
{
    protected static string $resource = VarianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'Edit Varian: ' . $this->record->nama;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Varian berhasil diperbarui';
    }
}
