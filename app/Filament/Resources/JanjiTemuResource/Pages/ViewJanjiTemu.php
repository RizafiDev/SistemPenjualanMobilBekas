<?php
namespace App\Filament\Resources\JanjiTemuResource\Pages;

use App\Filament\Resources\JanjiTemuResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewJanjiTemu extends ViewRecord
{
    protected static string $resource = JanjiTemuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('konfirmasi')
                ->label('Konfirmasi')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn(): bool => $this->record->status === 'pending')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'status' => 'dikonfirmasi',
                        'tanggal_konfirmasi' => now(),
                        'dikonfirmasi_oleh' => auth()->id(),
                    ]);

                    $this->refreshFormData(['status', 'tanggal_konfirmasi', 'dikonfirmasi_oleh']);
                }),
            Actions\Action::make('assign_sales')
                ->label('Assign Sales')
                ->icon('heroicon-o-user-plus')
                ->color('info')
                ->visible(fn(): bool => $this->record->status === 'dikonfirmasi' && !$this->record->karyawan_id)
                ->form([
                    \Filament\Forms\Components\Select::make('karyawan_id')
                        ->label('Pilih Sales')
                        ->relationship('karyawan', 'nama_lengkap')
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'karyawan_id' => $data['karyawan_id'],
                        'status' => 'terjadwal',
                    ]);

                    $this->refreshFormData(['karyawan_id', 'status']);
                }),
            Actions\Action::make('selesai')
                ->label('Selesai')
                ->icon('heroicon-o-check')
                ->color('success')
                ->visible(fn(): bool => $this->record->status === 'terjadwal')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['status' => 'selesai']);
                    $this->refreshFormData(['status']);
                }),
            Actions\Action::make('batal')
                ->label('Batalkan')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn(): bool => in_array($this->record->status, ['pending', 'dikonfirmasi', 'terjadwal']))
                ->requiresConfirmation()
                ->modalHeading('Batalkan Janji Temu')
                ->modalDescription('Apakah Anda yakin ingin membatalkan janji temu ini?')
                ->action(function () {
                    $this->record->update(['status' => 'batal']);
                    $this->refreshFormData(['status']);
                }),
            Actions\DeleteAction::make(),
        ];
    }
}