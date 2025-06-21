<?php

namespace App\Filament\Resources\PenjualanResource\Pages;

use App\Filament\Resources\PenjualanResource;
use App\Models\Penjualan;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreatePenjualan extends CreateRecord
{
    protected static string $resource = PenjualanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Penjualan berhasil dibuat')
            ->body('Data penjualan telah berhasil disimpan.');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate nomor faktur jika belum ada
        if (empty($data['no_faktur'])) {
            $data['no_faktur'] = (new Penjualan())->generateNoFaktur();
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // Update status stok mobil
        $penjualan = $this->getRecord();
        $stokMobil = $penjualan->stokMobil;

        if ($stokMobil && in_array($penjualan->status, ['booking', 'lunas', 'kredit'])) {
            $newStatus = match ($penjualan->status) {
                'booking' => 'booking',
                'lunas', 'kredit' => 'terjual',
                default => $stokMobil->status
            };

            $stokMobil->update(['status' => $newStatus]);
        }
    }
}