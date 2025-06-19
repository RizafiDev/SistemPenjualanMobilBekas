<?php

namespace App\Filament\Resources\StokMobilResource\Pages;

use App\Filament\Resources\StokMobilResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewStokMobil extends ViewRecord
{
    protected static string $resource = StokMobilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'Detail Stok Mobil: ' . $this->record->mobil->nama;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Mobil')
                    ->schema([
                        Infolists\Components\Split::make([
                            Infolists\Components\Grid::make(2)
                                ->schema([
                                    Infolists\Components\TextEntry::make('mobil.nama')
                                        ->label('Nama Mobil')
                                        ->weight('bold')
                                        ->size('lg'),

                                    Infolists\Components\TextEntry::make('varian.nama')
                                        ->label('Varian'),

                                    Infolists\Components\TextEntry::make('warna')
                                        ->label('Warna'),

                                    Infolists\Components\TextEntry::make('no_rangka')
                                        ->label('No. Rangka')
                                        ->fontFamily('mono')
                                        ->copyable(),

                                    Infolists\Components\TextEntry::make('no_mesin')
                                        ->label('No. Mesin')
                                        ->fontFamily('mono')
                                        ->copyable(),

                                    Infolists\Components\TextEntry::make('tahun')
                                        ->label('Tahun'),
                                ]),

                            Infolists\Components\ImageEntry::make('foto_kondisi')
                                ->label('Foto')
                                ->circular()
                                ->size(120)
                                ->visible(fn($record) => !empty($record->foto_kondisi))
                                ->grow(false),
                        ])->from('lg'),
                    ]),

                Infolists\Components\Section::make('Kondisi & Status')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('kilometer')
                                    ->label('Kilometer')
                                    ->numeric()
                                    ->suffixAction(
                                        Infolists\Components\Actions\Action::make('km')
                                            ->label('KM')
                                            ->icon('heroicon-m-arrow-right-circle')
                                    ),

                                Infolists\Components\TextEntry::make('kondisi')
                                    ->label('Kondisi')
                                    ->badge()
                                    ->formatStateUsing(fn($state) => str($state)->replace('_', ' ')->title())
                                    ->color(fn(string $state): string => match ($state) {
                                        'sangat_baik' => 'success',
                                        'baik' => 'info',
                                        'cukup' => 'warning',
                                        'butuh_perbaikan' => 'danger',
                                        default => 'gray',
                                    }),

                                Infolists\Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->formatStateUsing(fn($state) => str($state)->replace('_', ' ')->title())
                                    ->color(fn(string $state): string => match ($state) {
                                        'tersedia' => 'success',
                                        'terjual' => 'info',
                                        'booking' => 'warning',
                                        'indent' => 'gray',
                                        'dalam_perbaikan' => 'danger',
                                        default => 'gray',
                                    }),
                            ]),
                    ]),

                Infolists\Components\Section::make('Informasi Harga')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('harga_beli')
                                    ->label('Harga Beli')
                                    ->money('IDR'),

                                Infolists\Components\TextEntry::make('harga_jual')
                                    ->label('Harga Jual')
                                    ->money('IDR'),
                            ]),

                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('laba_kotor')
                                    ->label('Laba Kotor')
                                    ->money('IDR')
                                    ->color('success'),

                                Infolists\Components\TextEntry::make('total_biaya_service')
                                    ->label('Total Biaya Service')
                                    ->money('IDR')
                                    ->color('danger'),

                                Infolists\Components\TextEntry::make('laba_bersih')
                                    ->label('Laba Bersih')
                                    ->money('IDR')
                                    ->color(fn($state) => $state > 0 ? 'success' : 'danger')
                                    ->formatStateUsing(
                                        fn($state) =>
                                        $state > 0
                                        ? number_format($state, 0, ',', '.') . ' (Profit)'
                                        : number_format($state, 0, ',', '.') . ' (Loss)'
                                    ),

                            ]),
                    ]),

                Infolists\Components\Section::make('Tanggal & Lokasi')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('tanggal_masuk')
                                    ->label('Tanggal Masuk')
                                    ->date(),

                                Infolists\Components\TextEntry::make('tanggal_keluar')
                                    ->label('Tanggal Keluar')
                                    ->date(),

                                Infolists\Components\TextEntry::make('lokasi')
                                    ->label('Lokasi'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Catatan & Kelengkapan')
                    ->schema([
                        Infolists\Components\TextEntry::make('catatan')
                            ->label('Catatan')
                            ->columnSpanFull()
                            ->prose(),

                        Infolists\Components\TextEntry::make('kelengkapan')
                            ->label('Kelengkapan')
                            ->badge()
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Infolists\Components\Section::make('Riwayat Perbaikan')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('riwayat_perbaikan')
                            ->schema([
                                Infolists\Components\TextEntry::make('tanggal')
                                    ->date(),
                                Infolists\Components\TextEntry::make('jenis_perbaikan'),
                                Infolists\Components\TextEntry::make('biaya')
                                    ->money('IDR'),
                                Infolists\Components\TextEntry::make('keterangan')
                                    ->prose(),
                            ])
                            ->columns(4)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Infolists\Components\Section::make('Dokumen')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('dokumen')
                            ->schema([
                                Infolists\Components\TextEntry::make('jenis'),
                                Infolists\Components\TextEntry::make('nomor'),
                                Infolists\Components\TextEntry::make('masa_berlaku')
                                    ->date(),
                                Infolists\Components\ImageEntry::make('file')
                                    ->size(100),
                            ])
                            ->columns(4)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Infolists\Components\Section::make('Informasi Sistem')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Dibuat')
                                    ->dateTime('d M Y H:i'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Diperbarui')
                                    ->dateTime('d M Y H:i'),

                                Infolists\Components\TextEntry::make('deleted_at')
                                    ->label('Dihapus')
                                    ->dateTime('d M Y H:i')
                                    ->visible(fn($record) => $record->trashed()),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Infolists\Components\Section::make('Kondisi Fitur')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('kondisi_fitur.keamanan')
                                    ->label('Fitur Keamanan yang Berfungsi')
                                    ->listWithLineBreaks()
                                    ->bulleted(),

                                Infolists\Components\TextEntry::make('kondisi_fitur.kenyamanan')
                                    ->label('Fitur Kenyamanan yang Berfungsi')
                                    ->listWithLineBreaks()
                                    ->bulleted(),

                                Infolists\Components\TextEntry::make('kondisi_fitur.hiburan')
                                    ->label('Fitur Hiburan yang Berfungsi')
                                    ->listWithLineBreaks()
                                    ->bulleted(),
                            ]),

                        // Tambahkan perhitungan persentase kondisi fitur
                        Infolists\Components\TextEntry::make('persentase_kondisi_fitur')
                            ->label('Persentase Fitur Berfungsi')
                            ->state(function ($record) {
                                $varian = $record->varian;
                                if (!$varian)
                                    return '0%';

                                $totalFitur = count($varian->fitur_keamanan ?? []) +
                                    count($varian->fitur_kenyamanan ?? []) +
                                    count($varian->fitur_hiburan ?? []);

                                if ($totalFitur === 0)
                                    return '0%';

                                $fiturBerfungsi = count($record->kondisi_fitur['keamanan'] ?? []) +
                                    count($record->kondisi_fitur['kenyamanan'] ?? []) +
                                    count($record->kondisi_fitur['hiburan'] ?? []);

                                $persentase = ($fiturBerfungsi / $totalFitur) * 100;
                                return number_format($persentase, 1) . '%';
                            })
                            ->badge()
                            ->color(fn($state) => match (true) {
                                (float) $state >= 80 => 'success',
                                (float) $state >= 60 => 'info',
                                (float) $state >= 40 => 'warning',
                                default => 'danger',
                            }),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}