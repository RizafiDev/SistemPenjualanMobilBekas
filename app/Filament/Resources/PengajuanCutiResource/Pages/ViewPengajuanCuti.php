<?php

namespace App\Filament\Resources\PengajuanCutiResource\Pages;

use App\Filament\Resources\PengajuanCutiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Models\PengajuanCuti;
use Filament\Notifications\Notification;

class ViewPengajuanCuti extends ViewRecord
{
    protected static string $resource = PengajuanCutiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label('Setujui')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn(): bool => $this->record->status === PengajuanCuti::STATUS_MENUNGGU)
                ->action(function (): void {
                    $this->record->approve(auth()->id());

                    Notification::make()
                        ->title('Pengajuan cuti berhasil disetujui')
                        ->success()
                        ->send();

                    $this->refreshFormData(['status', 'disetujui_oleh', 'tanggal_persetujuan']);
                }),

            Actions\Action::make('reject')
                ->label('Tolak')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn(): bool => $this->record->status === PengajuanCuti::STATUS_MENUNGGU)
                ->form([
                    \Filament\Forms\Components\Textarea::make('alasan_penolakan')
                        ->label('Alasan Penolakan')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $this->record->reject($data['alasan_penolakan'], auth()->id());

                    Notification::make()
                        ->title('Pengajuan cuti berhasil ditolak')
                        ->success()
                        ->send();

                    $this->refreshFormData(['status', 'disetujui_oleh', 'tanggal_persetujuan', 'alasan_penolakan']);
                }),

            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\ForceDeleteAction::make(),
        ];
    }
}