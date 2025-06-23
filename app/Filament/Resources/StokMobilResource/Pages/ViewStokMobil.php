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
                // Hero Section - Car Overview
                Infolists\Components\Section::make('🚗 Informasi Mobil')
                    ->description('Detail lengkap spesifikasi kendaraan')
                    ->icon('heroicon-o-truck')
                    ->iconColor('primary')
                    ->schema([
                        Infolists\Components\Split::make([
                            Infolists\Components\Grid::make(2)
                                ->schema([
                                    Infolists\Components\TextEntry::make('mobil.nama')
                                        ->label('Nama Mobil')
                                        ->icon('heroicon-m-identification')
                                        ->iconColor('success')
                                        ->weight('bold')
                                        ->size('xl')
                                        ->color('primary'),

                                    Infolists\Components\TextEntry::make('varian.nama')
                                        ->label('Varian')
                                        ->icon('heroicon-m-star')
                                        ->iconColor('warning')
                                        ->badge()
                                        ->color('info'),

                                    Infolists\Components\TextEntry::make('warna')
                                        ->label('Warna')
                                        ->icon('heroicon-m-paint-brush')
                                        ->iconColor('purple')
                                        ->badge()
                                        ->color('purple'),

                                    Infolists\Components\TextEntry::make('tahun')
                                        ->label('Tahun Produksi')
                                        ->icon('heroicon-m-calendar-days')
                                        ->iconColor('indigo')
                                        ->badge()
                                        ->color('indigo'),

                                    Infolists\Components\TextEntry::make('no_rangka')
                                        ->label('Nomor Rangka')
                                        ->icon('heroicon-m-hashtag')
                                        ->iconColor('gray')
                                        ->fontFamily('mono')
                                        ->copyable()
                                        ->copyMessage('Nomor rangka berhasil disalin!')
                                        ->copyMessageDuration(1500),

                                    Infolists\Components\TextEntry::make('no_mesin')
                                        ->label('Nomor Mesin')
                                        ->icon('heroicon-m-cog-6-tooth')
                                        ->iconColor('orange')
                                        ->fontFamily('mono')
                                        ->copyable()
                                        ->copyMessage('Nomor mesin berhasil disalin!')
                                        ->copyMessageDuration(1500),
                                ]),

                            Infolists\Components\ImageEntry::make('foto_kondisi')
                                ->label('Foto Kendaraan')
                                ->circular()
                                ->size(140)
                                ->ring(4)
                                ->visible(fn($record) => !empty($record->foto_kondisi))
                                ->grow(false)
                                ->extraAttributes(['class' => 'shadow-xl border-4 border-white']),
                        ])->from('lg'),
                    ]),

                // Status & Condition Section
                Infolists\Components\Section::make('📊 Kondisi & Status')
                    ->description('Informasi kondisi dan status terkini kendaraan')
                    ->icon('heroicon-o-chart-bar')
                    ->iconColor('success')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('kilometer')
                                    ->label('Odometer')
                                    ->icon('heroicon-m-arrow-trending-up')
                                    ->iconColor('blue')
                                    ->numeric()
                                    ->suffix(' KM')
                                    ->weight('bold')
                                    ->size('lg')
                                    ->color('blue')
                                    ->suffixAction(
                                        Infolists\Components\Actions\Action::make('viewHistory')
                                            ->label('Riwayat')
                                            ->icon('heroicon-m-clock')
                                            ->tooltip('Lihat riwayat perjalanan')
                                    ),

                                Infolists\Components\TextEntry::make('kondisi')
                                    ->label('Kondisi Kendaraan')
                                    ->icon('heroicon-m-shield-check')
                                    ->badge()
                                    ->size('lg')
                                    ->weight('bold')
                                    ->formatStateUsing(fn($state) => str($state)->replace('_', ' ')->title())
                                    ->color(fn(string $state): string => match ($state) {
                                        'sangat_baik' => 'success',
                                        'baik' => 'info',
                                        'cukup' => 'warning',
                                        'butuh_perbaikan' => 'danger',
                                        default => 'gray',
                                    }),

                                Infolists\Components\TextEntry::make('status')
                                    ->label('Status Ketersediaan')
                                    ->icon('heroicon-m-flag')
                                    ->badge()
                                    ->size('lg')
                                    ->weight('bold')
                                    ->formatStateUsing(fn($state) => match ($state) {
                                        'tersedia' => '✅ Tersedia',
                                        'terjual' => '✅ Terjual',
                                        'booking' => '⏳ Booking',
                                        'indent' => '📋 Indent',
                                        'dalam_perbaikan' => '🔧 Dalam Perbaikan',
                                        default => str($state)->replace('_', ' ')->title(),
                                    })
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

                // Pricing Section
                Infolists\Components\Section::make('💰 Analisis Keuangan')
                    ->description('Informasi lengkap harga dan analisis profit')
                    ->icon('heroicon-o-banknotes')
                    ->iconColor('green')
                    ->schema([
                        // Harga Pokok
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('harga_beli')
                                    ->label('💸 Harga Pembelian')
                                    ->icon('heroicon-m-arrow-down-tray')
                                    ->iconColor('red')
                                    ->money('IDR')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color('danger'),

                                Infolists\Components\TextEntry::make('harga_jual')
                                    ->label('💵 Harga Penjualan')
                                    ->icon('heroicon-m-arrow-up-tray')
                                    ->iconColor('green')
                                    ->money('IDR')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color('success'),
                            ]),

                        // Profit Analysis
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('laba_kotor')
                                    ->label('📈 Laba Kotor')
                                    ->icon('heroicon-m-arrow-trending-up')
                                    ->iconColor('green')
                                    ->money('IDR')
                                    ->color('success')
                                    ->weight('bold')
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('total_biaya_service')
                                    ->label('🔧 Total Biaya Service')
                                    ->icon('heroicon-m-wrench-screwdriver')
                                    ->iconColor('orange')
                                    ->money('IDR')
                                    ->color('warning')
                                    ->weight('bold'),

                                Infolists\Components\TextEntry::make('laba_bersih')
                                    ->label('💎 Laba Bersih')
                                    ->icon('heroicon-m-sparkles')
                                    ->iconColor('purple')
                                    ->money('IDR')
                                    ->size('xl')
                                    ->weight('bold')
                                    ->color(fn($state) => $state > 0 ? 'success' : 'danger')
                                    ->formatStateUsing(
                                        fn($state) => $state > 0
                                        ? '🎉 ' . number_format($state, 0, ',', '.') . ' (PROFIT)'
                                        : '😞 ' . number_format($state, 0, ',', '.') . ' (LOSS)'
                                    ),
                            ]),

                        // ROI Indicator
                        Infolists\Components\TextEntry::make('roi_percentage')
                            ->label('📊 Return on Investment (ROI)')
                            ->state(function ($record) {
                                if ($record->harga_beli > 0) {
                                    $roi = (($record->laba_bersih / $record->harga_beli) * 100);
                                    return number_format($roi, 2) . '%';
                                }
                                return '0%';
                            })
                            ->badge()
                            ->size('lg')
                            ->weight('bold')
                            ->color(fn($state) => (float) $state > 0 ? 'success' : 'danger')
                            ->columnSpanFull(),
                    ]),

                // Timeline Section
                Infolists\Components\Section::make('📅 Timeline & Lokasi')
                    ->description('Riwayat waktu dan informasi lokasi')
                    ->icon('heroicon-o-map-pin')
                    ->iconColor('blue')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('tanggal_masuk')
                                    ->label('📥 Tanggal Masuk')
                                    ->icon('heroicon-m-arrow-right-circle')
                                    ->iconColor('green')
                                    ->date('d F Y')
                                    ->badge()
                                    ->color('success'),

                                Infolists\Components\TextEntry::make('tanggal_keluar')
                                    ->label('📤 Tanggal Keluar')
                                    ->icon('heroicon-m-arrow-left-circle')
                                    ->iconColor('red')
                                    ->date('d F Y')
                                    ->badge()
                                    ->color('info')
                                    ->placeholder('Belum keluar'),

                                Infolists\Components\TextEntry::make('lokasi')
                                    ->label('📍 Lokasi')
                                    ->icon('heroicon-m-map-pin')
                                    ->iconColor('purple')
                                    ->badge()
                                    ->color('purple'),
                            ]),

                        // Duration calculation
                        Infolists\Components\TextEntry::make('durasi_hari')
                            ->label('⏱️ Durasi di Showroom')
                            ->state(function ($record) {
                                if ($record->tanggal_masuk) {
                                    $endDate = $record->tanggal_keluar
                                        ? \Carbon\Carbon::parse($record->tanggal_keluar)
                                        : now();
                                    $startDate = \Carbon\Carbon::parse($record->tanggal_masuk);
                                    $duration = $startDate->diffInDays($endDate);
                                    return $duration . ' hari';
                                }
                                return '-';
                            })
                            ->badge()
                            ->color('info')
                            ->columnSpanFull(),
                    ]),

                // Feature Condition Section
                Infolists\Components\Section::make('⚙️ Kondisi Fitur')
                    ->description('Status kondisi fitur-fitur kendaraan')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->iconColor('indigo')
                    ->schema([
                        // Feature Status Overview
                        Infolists\Components\TextEntry::make('persentase_kondisi_fitur')
                            ->label('🎯 Persentase Fitur Berfungsi')
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
                            ->size('xl')
                            ->weight('bold')
                            ->color(fn($state) => match (true) {
                                (float) $state >= 90 => 'success',
                                (float) $state >= 70 => 'info',
                                (float) $state >= 50 => 'warning',
                                default => 'danger',
                            })
                            ->columnSpanFull(),

                        // Feature Details
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('kondisi_fitur.keamanan')
                                    ->label('🛡️ Fitur Keamanan')
                                    ->icon('heroicon-m-shield-check')
                                    ->iconColor('green')
                                    ->listWithLineBreaks()
                                    ->bulleted()
                                    ->placeholder('Tidak ada fitur keamanan yang berfungsi'),

                                Infolists\Components\TextEntry::make('kondisi_fitur.kenyamanan')
                                    ->label('🪑 Fitur Kenyamanan')
                                    ->icon('heroicon-m-heart')
                                    ->iconColor('pink')
                                    ->listWithLineBreaks()
                                    ->bulleted()
                                    ->placeholder('Tidak ada fitur kenyamanan yang berfungsi'),

                                Infolists\Components\TextEntry::make('kondisi_fitur.hiburan')
                                    ->label('🎵 Fitur Hiburan')
                                    ->icon('heroicon-m-musical-note')
                                    ->iconColor('purple')
                                    ->listWithLineBreaks()
                                    ->bulleted()
                                    ->placeholder('Tidak ada fitur hiburan yang berfungsi'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(false), // Show by default since it's important

                // Notes & Completeness
                Infolists\Components\Section::make('📝 Catatan & Kelengkapan')
                    ->description('Informasi tambahan dan kelengkapan dokumen')
                    ->icon('heroicon-o-document-text')
                    ->iconColor('amber')
                    ->schema([
                        Infolists\Components\TextEntry::make('catatan')
                            ->label('📋 Catatan Khusus')
                            ->icon('heroicon-m-pencil-square')
                            ->iconColor('blue')
                            ->columnSpanFull()
                            ->prose()
                            ->placeholder('Tidak ada catatan khusus'),

                        Infolists\Components\TextEntry::make('kelengkapan')
                            ->label('✅ Kelengkapan')
                            ->icon('heroicon-m-check-circle')
                            ->iconColor('green')
                            ->badge()
                            ->separator(',')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),

                // Service History
                Infolists\Components\Section::make('🔧 Riwayat Perbaikan')
                    ->description('Histori service dan perbaikan kendaraan')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->iconColor('orange')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('riwayat_perbaikan')
                            ->schema([
                                Infolists\Components\TextEntry::make('tanggal')
                                    ->label('📅 Tanggal')
                                    ->date('d M Y')
                                    ->badge()
                                    ->color('info'),

                                Infolists\Components\TextEntry::make('jenis_perbaikan')
                                    ->label('🔧 Jenis Perbaikan')
                                    ->badge()
                                    ->color('warning'),

                                Infolists\Components\TextEntry::make('biaya')
                                    ->label('💰 Biaya')
                                    ->money('IDR')
                                    ->color('danger'),

                                Infolists\Components\TextEntry::make('keterangan')
                                    ->label('📝 Keterangan')
                                    ->prose()
                                    ->limit(100),
                            ])
                            ->columns(4)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),

                // Documents
                Infolists\Components\Section::make('📄 Dokumen')
                    ->description('Dokumen-dokumen terkait kendaraan')
                    ->icon('heroicon-o-folder')
                    ->iconColor('gray')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('dokumen')
                            ->schema([
                                Infolists\Components\TextEntry::make('jenis')
                                    ->label('📋 Jenis Dokumen')
                                    ->badge()
                                    ->color('indigo'),

                                Infolists\Components\TextEntry::make('nomor')
                                    ->label('🔢 Nomor')
                                    ->fontFamily('mono')
                                    ->copyable(),

                                Infolists\Components\TextEntry::make('masa_berlaku')
                                    ->label('⏰ Masa Berlaku')
                                    ->date('d M Y')
                                    ->badge()
                                    ->color(
                                        fn($state) =>
                                        $state && \Carbon\Carbon::parse($state)->isPast()
                                        ? 'danger'
                                        : 'success'
                                    ),

                                Infolists\Components\ImageEntry::make('file')
                                    ->label('🖼️ Preview')
                                    ->size(120)
                                    ->square(),
                            ])
                            ->columns(4)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),

                // System Information
                Infolists\Components\Section::make('⚙️ Informasi Sistem')
                    ->description('Metadata dan informasi sistem')
                    ->icon('heroicon-o-server')
                    ->iconColor('slate')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('➕ Dibuat')
                                    ->icon('heroicon-m-plus-circle')
                                    ->iconColor('green')
                                    ->dateTime('d M Y, H:i')
                                    ->badge()
                                    ->color('success'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('✏️ Terakhir Diperbarui')
                                    ->icon('heroicon-m-pencil')
                                    ->iconColor('blue')
                                    ->dateTime('d M Y, H:i')
                                    ->badge()
                                    ->color('info'),

                                Infolists\Components\TextEntry::make('deleted_at')
                                    ->label('🗑️ Dihapus')
                                    ->icon('heroicon-m-trash')
                                    ->iconColor('red')
                                    ->dateTime('d M Y, H:i')
                                    ->badge()
                                    ->color('danger')
                                    ->visible(fn($record) => $record->trashed()),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}