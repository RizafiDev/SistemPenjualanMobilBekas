<?php

namespace App\Filament\Resources\PenjualanResource\Pages;

use App\Filament\Resources\PenjualanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Actions\Action;

class ViewPenjualan extends ViewRecord
{
    protected static string $resource = PenjualanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn() => $this->getRecord()->status !== 'batal'),

            Actions\Action::make('print_invoice')
                ->label('Print Invoice')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn() => route('penjualan.print', $this->getRecord()))
                ->openUrlInNewTab(),

            Actions\Action::make('update_status')
                ->label('Update Status')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\Select::make('status')
                        ->label('Status Baru')
                        ->options(function () {
                            $currentStatus = $this->getRecord()->status;
                            $allowedTransitions = $this->getAllowedStatusTransitions($currentStatus);
                            return collect(\App\Models\Penjualan::STATUS)
                                ->filter(fn($label, $value) => in_array($value, $allowedTransitions))
                                ->toArray();
                        })
                        ->required(),
                    \Filament\Forms\Components\Textarea::make('catatan_perubahan')
                        ->label('Catatan Perubahan')
                        ->placeholder('Alasan perubahan status...')
                ])
                ->action(function (array $data) {
                    $record = $this->getRecord();
                    $oldStatus = $record->status;

                    $record->update([
                        'status' => $data['status'],
                        'catatan' => $record->catatan . "\n\n" .
                            "Status diubah dari " . \App\Models\Penjualan::STATUS[$oldStatus] .
                            " ke " . \App\Models\Penjualan::STATUS[$data['status']] .
                            " pada " . now()->format('d/m/Y H:i') .
                            ($data['catatan_perubahan'] ? "\nCatatan: " . $data['catatan_perubahan'] : "")
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Status berhasil diperbarui')
                        ->send();
                })
                ->visible(fn() => $this->getRecord()->status !== 'batal'),

            Actions\DeleteAction::make()
                ->visible(fn() => in_array($this->getRecord()->status, ['draft', 'booking'])),
        ];
    }

    private function getAllowedStatusTransitions(string $currentStatus): array
    {
        return match ($currentStatus) {
            'draft' => ['booking', 'lunas', 'kredit', 'batal'],
            'booking' => ['lunas', 'kredit', 'batal'],
            'lunas' => [], // Status final
            'kredit' => ['lunas', 'batal'],
            'batal' => [], // Status final
            default => []
        };
    }
}