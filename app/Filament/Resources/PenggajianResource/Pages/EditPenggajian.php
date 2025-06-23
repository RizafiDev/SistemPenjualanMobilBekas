<?php

namespace App\Filament\Resources\PenggajianResource\Pages;

use App\Filament\Resources\PenggajianResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use App\Models\Penggajian;

class EditPenggajian extends EditRecord
{
    protected static string $resource = PenggajianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),

            Actions\Action::make('mark_as_paid')
                ->label('Tandai Dibayar')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn() => $this->record->isDraft())
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->markAsDibayar();

                    Notification::make()
                        ->success()
                        ->title('Status berhasil diubah')
                        ->body('Penggajian berhasil ditandai sebagai dibayar.')
                        ->send();

                    return redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                }),

            Actions\Action::make('cancel')
                ->label('Batalkan')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn() => $this->record->isDraft())
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->markAsBatal();

                    Notification::make()
                        ->success()
                        ->title('Status berhasil diubah')
                        ->body('Penggajian berhasil dibatalkan.')
                        ->send();

                    return redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                }),

            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Penggajian berhasil diperbarui')
            ->body('Data penggajian karyawan berhasil disimpan.');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Calculate totals before saving
        $totalGaji = ($data['gaji_pokok'] ?? 0) +
            ($data['tunjangan'] ?? 0) +
            ($data['bonus'] ?? 0) +
            ($data['lembur'] ?? 0) +
            ($data['insentif'] ?? 0);

        $totalPotongan = ($data['potongan_terlambat'] ?? 0) +
            ($data['potongan_absensi'] ?? 0) +
            ($data['potongan_lainnya'] ?? 0);

        $data['total_gaji'] = $totalGaji;
        $data['total_potongan'] = $totalPotongan;
        $data['gaji_bersih'] = $totalGaji - $totalPotongan;

        return $data;
    }
}
