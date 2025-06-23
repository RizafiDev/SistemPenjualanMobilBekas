<?php

namespace App\Filament\Resources\PenggajianResource\Pages;

use App\Filament\Resources\PenggajianResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use App\Models\Penggajian;

class ViewPenggajian extends ViewRecord
{
    protected static string $resource = PenggajianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn() => $this->record->isDraft()),

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

                    $this->refreshFormData([
                        'status'
                    ]);
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

                    $this->refreshFormData([
                        'status'
                    ]);
                }),

            Actions\Action::make('print')
                ->label('Cetak Slip Gaji')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->visible(fn() => $this->record->isDibayar())
                ->url(fn() => route('penggajian.print', $this->record))
                ->openUrlInNewTab(),

            Actions\DeleteAction::make()
                ->visible(fn() => $this->record->isDraft()),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Karyawan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('karyawan.nama_lengkap')
                                    ->label('Nama Karyawan'),

                                TextEntry::make('karyawan.nip')
                                    ->label('NIP'),

                                TextEntry::make('karyawan.jabatan')
                                    ->label('Jabatan'),

                                TextEntry::make('karyawan.departemen')
                                    ->label('Departemen'),
                            ])
                    ]),

                Section::make('Informasi Penggajian')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('periode')
                                    ->label('Periode')
                                    ->formatStateUsing(fn($state) => \Carbon\Carbon::createFromFormat('Y-m', $state)->format('F Y')),

                                TextEntry::make('tanggal_gaji')
                                    ->label('Tanggal Gaji')
                                    ->date('d F Y'),

                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'draft' => 'gray',
                                        'dibayar' => 'success',
                                        'batal' => 'danger',
                                    })
                                    ->formatStateUsing(fn($state) => ucfirst($state)),
                            ])
                    ]),

                Section::make('Komponen Gaji')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('gaji_pokok')
                                    ->label('Gaji Pokok')
                                    ->money('IDR'),

                                TextEntry::make('tunjangan')
                                    ->label('Tunjangan')
                                    ->money('IDR'),

                                TextEntry::make('bonus')
                                    ->label('Bonus')
                                    ->money('IDR'),

                                TextEntry::make('lembur')
                                    ->label('Lembur')
                                    ->money('IDR'),

                                TextEntry::make('insentif')
                                    ->label('Insentif')
                                    ->money('IDR'),
                            ])
                    ]),

                Section::make('Potongan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('potongan_terlambat')
                                    ->label('Potongan Terlambat')
                                    ->money('IDR'),

                                TextEntry::make('potongan_absensi')
                                    ->label('Potongan Absensi')
                                    ->money('IDR'),

                                TextEntry::make('potongan_lainnya')
                                    ->label('Potongan Lainnya')
                                    ->money('IDR'),
                            ])
                    ]),

                Section::make('Ringkasan')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('total_gaji')
                                    ->label('Total Gaji')
                                    ->money('IDR')
                                    ->weight('bold')
                                    ->color('success'),

                                TextEntry::make('total_potongan')
                                    ->label('Total Potongan')
                                    ->money('IDR')
                                    ->weight('bold')
                                    ->color('danger'),

                                TextEntry::make('gaji_bersih')
                                    ->label('Gaji Bersih')
                                    ->money('IDR')
                                    ->weight('bold')
                                    ->size('lg')
                                    ->color('primary'),
                            ])
                    ]),

                Section::make('Catatan')
                    ->schema([
                        TextEntry::make('catatan')
                            ->label('Catatan')
                            ->placeholder('Tidak ada catatan')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn() => !empty($this->record->catatan)),

                Section::make('Informasi Sistem')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Dibuat Pada')
                                    ->dateTime('d F Y H:i'),

                                TextEntry::make('updated_at')
                                    ->label('Diperbarui Pada')
                                    ->dateTime('d F Y H:i'),
                            ])
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}