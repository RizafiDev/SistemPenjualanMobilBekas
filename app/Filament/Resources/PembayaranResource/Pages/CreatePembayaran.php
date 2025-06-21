<?php

namespace App\Filament\Resources\PembayaranResource\Pages;

use App\Filament\Resources\PembayaranResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreatePembayaran extends CreateRecord
{
    protected static string $resource = PembayaranResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Pembayaran berhasil dibuat')
            ->body('Data pembayaran telah disimpan dan status penjualan telah diperbarui.');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Jika no_kwitansi kosong, akan di-generate otomatis oleh model
        if (empty($data['no_kwitansi'])) {
            unset($data['no_kwitansi']);
        }

        return $data;
    }
}