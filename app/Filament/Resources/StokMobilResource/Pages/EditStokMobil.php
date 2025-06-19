<?php

namespace App\Filament\Resources\StokMobilResource\Pages;

use App\Filament\Resources\StokMobilResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStokMobil extends EditRecord
{
    protected static string $resource = StokMobilResource::class;

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
        return 'Edit Stok Mobil: ' . $this->record->mobil->nama;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Stok mobil berhasil diperbarui';
    }
}
