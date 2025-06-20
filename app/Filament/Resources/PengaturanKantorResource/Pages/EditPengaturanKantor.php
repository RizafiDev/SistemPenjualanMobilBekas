<?php

namespace App\Filament\Resources\PengaturanKantorResource\Pages;

use App\Filament\Resources\PengaturanKantorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengaturanKantor extends EditRecord
{
    protected static string $resource = PengaturanKantorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('Lihat')
                ->icon('heroicon-o-eye'),
            Actions\DeleteAction::make()
                ->label('Hapus')
                ->icon('heroicon-o-trash'),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Pengaturan kantor berhasil diperbarui';
    }
}