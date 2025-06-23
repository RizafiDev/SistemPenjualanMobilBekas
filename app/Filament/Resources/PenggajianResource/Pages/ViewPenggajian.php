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
                Section::make('👤 Informasi Karyawan')
                    ->description('Detail informasi karyawan')
                    ->icon('heroicon-o-user')
                    ->iconColor('primary')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('karyawan.nama_lengkap')
                                    ->label('Nama Karyawan')
                                    ->icon('heroicon-o-identification')
                                    ->copyable()
                                    ->weight('bold')
                                    ->size('lg'),

                                TextEntry::make('karyawan.nip')
                                    ->label('NIP')
                                    ->icon('heroicon-o-hashtag')
                                    ->copyable()
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('karyawan.jabatan')
                                    ->label('Jabatan')
                                    ->icon('heroicon-o-briefcase')
                                    ->badge()
                                    ->color('info'),

                                TextEntry::make('karyawan.departemen')
                                    ->label('Departemen')
                                    ->icon('heroicon-o-building-office')
                                    ->badge()
                                    ->color('warning'),
                            ])
                    ])
                    ->collapsible(),

                Section::make('📋 Informasi Penggajian')
                    ->description('Detail periode dan status penggajian')
                    ->icon('heroicon-o-calendar-days')
                    ->iconColor('success')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('periode')
                                    ->label('Periode')
                                    ->icon('heroicon-o-calendar')
                                    ->formatStateUsing(fn($state) => \Carbon\Carbon::createFromFormat('Y-m', $state)->format('F Y'))
                                    ->badge()
                                    ->color('primary')
                                    ->size('lg'),

                                TextEntry::make('tanggal_gaji')
                                    ->label('Tanggal Gaji')
                                    ->icon('heroicon-o-clock')
                                    ->date('d F Y')
                                    ->badge()
                                    ->color('info'),

                                TextEntry::make('status')
                                    ->label('Status')
                                    ->icon(fn(string $state): string => match ($state) {
                                        'draft' => 'heroicon-o-pencil',
                                        'dibayar' => 'heroicon-o-check-circle',
                                        'batal' => 'heroicon-o-x-circle',
                                    })
                                    ->badge()
                                    ->size('lg')
                                    ->color(fn(string $state): string => match ($state) {
                                        'draft' => 'gray',
                                        'dibayar' => 'success',
                                        'batal' => 'danger',
                                    })
                                    ->formatStateUsing(fn($state) => ucfirst($state)),
                            ])
                    ]),

                Section::make('💰 Komponen Penghasilan')
                    ->description('Rincian penghasilan karyawan')
                    ->icon('heroicon-o-currency-dollar')
                    ->iconColor('success')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('gaji_pokok')
                                    ->label('Gaji Pokok')
                                    ->icon('heroicon-o-banknotes')
                                    ->money('IDR')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color('success'),

                                TextEntry::make('tunjangan')
                                    ->label('Tunjangan')
                                    ->icon('heroicon-o-gift')
                                    ->money('IDR')
                                    ->color('info'),

                                TextEntry::make('bonus')
                                    ->label('Bonus')
                                    ->icon('heroicon-o-star')
                                    ->money('IDR')
                                    ->color('warning'),

                                TextEntry::make('lembur')
                                    ->label('Lembur')
                                    ->icon('heroicon-o-clock')
                                    ->money('IDR')
                                    ->color('purple'),

                                TextEntry::make('insentif')
                                    ->label('Insentif')
                                    ->icon('heroicon-o-trophy')
                                    ->money('IDR')
                                    ->color('amber'),
                            ])
                    ])
                    ->collapsible(),

                Section::make('📉 Potongan')
                    ->description('Rincian potongan gaji')
                    ->icon('heroicon-o-minus-circle')
                    ->iconColor('danger')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('potongan_terlambat')
                                    ->label('Potongan Terlambat')
                                    ->icon('heroicon-o-clock')
                                    ->money('IDR')
                                    ->color('danger'),

                                TextEntry::make('potongan_absensi')
                                    ->label('Potongan Absensi')
                                    ->icon('heroicon-o-calendar-days')
                                    ->money('IDR')
                                    ->color('danger'),

                                TextEntry::make('potongan_lainnya')
                                    ->label('Potongan Lainnya')
                                    ->icon('heroicon-o-exclamation-triangle')
                                    ->money('IDR')
                                    ->color('danger'),
                            ])
                    ])
                    ->collapsible(),

                Section::make('📊 Ringkasan Gaji')
                    ->description('Total keseluruhan penggajian')
                    ->icon('heroicon-o-calculator')
                    ->iconColor('primary')
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                // Card-like layout for summary
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('total_gaji')
                                            ->label('Total Penghasilan')
                                            ->icon('heroicon-o-arrow-trending-up')
                                            ->money('IDR')
                                            ->weight('bold')
                                            ->size('lg')
                                            ->color('success'),

                                        TextEntry::make('total_potongan')
                                            ->label('Total Potongan')
                                            ->icon('heroicon-o-arrow-trending-down')
                                            ->money('IDR')
                                            ->weight('bold')
                                            ->size('lg')
                                            ->color('danger'),

                                        TextEntry::make('gaji_bersih')
                                            ->label('Gaji Bersih')
                                            ->icon('heroicon-o-check-badge')
                                            ->money('IDR')
                                            ->weight('bold')
                                            ->size('xl')
                                            ->color('primary'),
                                    ])
                            ])
                    ]),

                Section::make('📝 Catatan')
                    ->description('Catatan tambahan untuk penggajian ini')
                    ->icon('heroicon-o-document-text')
                    ->iconColor('gray')
                    ->schema([
                        TextEntry::make('catatan')
                            ->label('Catatan')
                            ->icon('heroicon-o-chat-bubble-left-ellipsis')
                            ->placeholder('Tidak ada catatan')
                            ->columnSpanFull()
                            ->color('gray'),
                    ])
                    ->visible(fn() => !empty($this->record->catatan))
                    ->collapsible(),

                Section::make('⚙️ Informasi Sistem')
                    ->description('Data sistem dan audit trail')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->iconColor('gray')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Dibuat Pada')
                                    ->icon('heroicon-o-plus-circle')
                                    ->dateTime('d F Y H:i')
                                    ->color('info'),

                                TextEntry::make('updated_at')
                                    ->label('Diperbarui Pada')
                                    ->icon('heroicon-o-pencil-square')
                                    ->dateTime('d F Y H:i')
                                    ->color('warning'),
                            ])
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}