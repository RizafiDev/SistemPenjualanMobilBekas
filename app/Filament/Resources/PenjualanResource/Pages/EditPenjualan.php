<?php

namespace App\Filament\Resources\PenjualanResource\Pages;

use App\Filament\Resources\PenjualanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditPenjualan extends EditRecord
{
    protected static string $resource = PenjualanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn() => in_array($this->getRecord()->status, ['draft', 'booking'])),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Penjualan berhasil diperbarui')
            ->body('Data penjualan telah berhasil disimpan.');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Cek apakah status berubah dan update log
        $record = $this->getRecord();
        if ($record->status !== $data['status']) {
            $oldStatus = \App\Models\Penjualan::STATUS[$record->status] ?? $record->status;
            $newStatus = \App\Models\Penjualan::STATUS[$data['status']] ?? $data['status'];

            $data['catatan'] = ($data['catatan'] ?? '') . "\n\n" .
                "Status diubah dari {$oldStatus} ke {$newStatus} pada " . now()->format('d/m/Y H:i');
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Update status stok mobil jika diperlukan
        $penjualan = $this->getRecord();
        $stokMobil = $penjualan->stokMobil;

        if ($stokMobil) {
            $newStokStatus = match ($penjualan->status) {
                'booking' => 'booking',
                'lunas', 'kredit' => 'terjual',
                'batal' => 'tersedia',
                default => $stokMobil->status
            };

            if ($stokMobil->status !== $newStokStatus) {
                $updateData = ['status' => $newStokStatus];

                // Set tanggal keluar jika terjual
                if (in_array($penjualan->status, ['lunas', 'kredit'])) {
                    $updateData['tanggal_keluar'] = $penjualan->tanggal_penjualan;
                }

                $stokMobil->update($updateData);
            }
        }
    }
}