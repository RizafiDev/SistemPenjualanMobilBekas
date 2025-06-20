<?php

namespace App\Filament\Resources\PengaturanKantorResource\Pages;

use App\Filament\Resources\PengaturanKantorResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\Actions\Action;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;

class ViewPengaturanKantor extends ViewRecord
{
    protected static string $resource = PengaturanKantorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Edit Pengaturan')
                ->color('warning')
                ->icon('heroicon-o-pencil-square'),

            Actions\Action::make('google_maps')
                ->label('Buka Maps')
                ->color('success')
                ->icon('heroicon-o-map')
                ->url(fn($record) => "https://maps.google.com/?q={$record->latitude},{$record->longitude}")
                ->openUrlInNewTab(),

            Actions\DeleteAction::make()
                ->label('Hapus')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Hapus Pengaturan Kantor')
                ->modalDescription('Apakah Anda yakin ingin menghapus pengaturan kantor ini? Data yang dihapus dapat dipulihkan.')
                ->modalSubmitActionLabel('Ya, Hapus'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Header Section dengan informasi utama
                Section::make('Informasi Utama')
                    ->key('header_section')
                    ->schema([
                        Split::make([
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('nama_kantor')
                                        ->label('Nama Kantor')
                                        ->size(TextEntry\TextEntrySize::Large)
                                        ->weight(FontWeight::Bold)
                                        ->color('primary')
                                        ->icon('heroicon-o-building-office-2')
                                        ->iconPosition(IconPosition::Before),

                                    TextEntry::make('aktif')
                                        ->label('Status')
                                        ->badge()
                                        ->size(TextEntry\TextEntrySize::Large)
                                        ->color(fn($state): string => $state ? 'success' : 'danger')
                                        ->icon(fn($state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                                        ->formatStateUsing(fn($state): string => $state ? 'AKTIF' : 'TIDAK AKTIF'),
                                ]),

                            TextEntry::make('alamat_kantor')
                                ->label('Alamat Lengkap')
                                ->icon('heroicon-o-map-pin')
                                ->iconPosition(IconPosition::Before)
                                ->copyable()
                                ->copyMessage('Alamat berhasil disalin!')
                                ->columnSpanFull(),
                        ]),
                    ])
                    ->headerActions([
                        Action::make('copy_all_info')
                            ->label('Salin Info')
                            ->icon('heroicon-o-clipboard-document')
                            ->color('gray')
                            ->action(function ($record) {
                                $info = "Kantor: {$record->nama_kantor}\n";
                                $info .= "Alamat: {$record->alamat_kantor}\n";
                                $info .= "Koordinat: {$record->latitude}, {$record->longitude}\n";
                                $info .= "Jam Kerja: {$record->jam_masuk->format('H:i')} - {$record->jam_pulang->format('H:i')}";

                                // Show notification instead of trying to copy to clipboard
                                // You can implement actual clipboard functionality with JavaScript if needed
                                return redirect()->back()->with('success', 'Info: ' . $info);
                            }),
                    ]),

                // Lokasi & Peta
                Section::make('📍 Lokasi & Navigasi')
                    ->key('lokasi_section')
                    ->description('Informasi lokasi dan pengaturan radius kantor')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('latitude')
                                    ->label('Latitude')
                                    ->icon('heroicon-o-globe-alt')
                                    ->copyable()
                                    ->copyMessage('Latitude disalin!')
                                    ->badge()
                                    ->color('info'),

                                TextEntry::make('longitude')
                                    ->label('Longitude')
                                    ->icon('heroicon-o-globe-alt')
                                    ->copyable()
                                    ->copyMessage('Longitude disalin!')
                                    ->badge()
                                    ->color('info'),

                                TextEntry::make('radius_meter')
                                    ->label('Radius Absensi')
                                    ->suffix(' meter')
                                    ->icon('heroicon-o-arrow-path-rounded-square')
                                    ->badge()
                                    ->color('warning'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('koordinat_lengkap')
                                    ->label('Koordinat Lengkap')
                                    ->getStateUsing(fn($record) => "{$record->latitude}, {$record->longitude}")
                                    ->copyable()
                                    ->copyMessage('Koordinat lengkap disalin!')
                                    ->icon('heroicon-o-clipboard')
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('google_maps_link')
                                    ->label('Google Maps')
                                    ->getStateUsing(fn() => 'Buka Lokasi di Google Maps')
                                    ->url(fn($record) => "https://maps.google.com/?q={$record->latitude},{$record->longitude}")
                                    ->openUrlInNewTab()
                                    ->icon('heroicon-o-map')
                                    ->color('success'),
                            ]),
                    ])
                    ->icon('heroicon-o-map')
                    ->collapsible()
                    ->persistCollapsed(),

                // Jam Kerja & Toleransi
                Section::make('⏰ Jadwal Kerja')
                    ->key('jadwal_section')
                    ->description('Pengaturan jam kerja dan toleransi keterlambatan')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('jam_masuk')
                                    ->label('Jam Masuk')
                                    ->time('H:i')
                                    ->icon('heroicon-o-sun')
                                    ->badge()
                                    ->color('success')
                                    ->size(TextEntry\TextEntrySize::Large),

                                TextEntry::make('jam_pulang')
                                    ->label('Jam Pulang')
                                    ->time('H:i')
                                    ->icon('heroicon-o-moon')
                                    ->badge()
                                    ->color('danger')
                                    ->size(TextEntry\TextEntrySize::Large),

                                TextEntry::make('toleransi_terlambat')
                                    ->label('Toleransi')
                                    ->suffix(' menit')
                                    ->icon('heroicon-o-exclamation-triangle')
                                    ->badge()
                                    ->color('warning')
                                    ->size(TextEntry\TextEntrySize::Large),

                                TextEntry::make('durasi_kerja')
                                    ->label('Durasi Kerja')
                                    ->getStateUsing(function ($record) {
                                        if ($record->jam_masuk && $record->jam_pulang) {
                                            $masuk = \Carbon\Carbon::parse($record->jam_masuk);
                                            $pulang = \Carbon\Carbon::parse($record->jam_pulang);
                                            $durasi = $masuk->diffInHours($pulang);
                                            return $durasi . ' jam';
                                        }
                                        return '-';
                                    })
                                    ->icon('heroicon-o-clock')
                                    ->badge()
                                    ->color('info')
                                    ->size(TextEntry\TextEntrySize::Large),
                            ]),

                        TextEntry::make('jadwal_info')
                            ->label('Ringkasan Jadwal')
                            ->getStateUsing(function ($record) {
                                $masuk = $record->jam_masuk ? $record->jam_masuk->format('H:i') : '-';
                                $pulang = $record->jam_pulang ? $record->jam_pulang->format('H:i') : '-';
                                $toleransi = $record->toleransi_terlambat;

                                return "Jam kerja {$masuk} - {$pulang} dengan toleransi keterlambatan {$toleransi} menit";
                            })
                            ->icon('heroicon-o-information-circle')
                            ->color('primary')
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-clock')
                    ->collapsible()
                    ->persistCollapsed(),

                // Informasi Sistem
                Section::make('📋 Informasi Sistem')
                    ->key('sistem_section')
                    ->description('Data audit dan informasi sistem')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('id')
                                    ->label('ID Kantor')
                                    ->badge()
                                    ->color('gray')
                                    ->icon('heroicon-o-hashtag'),

                                TextEntry::make('created_at')
                                    ->label('Dibuat Pada')
                                    ->dateTime('d F Y, H:i')
                                    ->icon('heroicon-o-plus-circle')
                                    ->color('success'),

                                TextEntry::make('updated_at')
                                    ->label('Diperbarui')
                                    ->dateTime('d F Y, H:i')
                                    ->since()
                                    ->icon('heroicon-o-pencil-square')
                                    ->color('warning'),
                            ]),

                        TextEntry::make('deleted_at')
                            ->label('Status Penghapusan')
                            ->getStateUsing(function ($record) {
                                if ($record->deleted_at) {
                                    return 'Dihapus pada ' . $record->deleted_at->format('d F Y, H:i');
                                }
                                return 'Data masih aktif';
                            })
                            ->icon(fn($record) => $record->deleted_at ? 'heroicon-o-trash' : 'heroicon-o-check-circle')
                            ->color(fn($record) => $record->deleted_at ? 'danger' : 'success')
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->collapsed(),

                // Quick Stats
                Section::make('📊 Statistik Cepat')
                    ->key('stats_section')
                    ->description('Informasi ringkas pengaturan kantor')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('radius_km')
                                    ->label('Radius (KM)')
                                    ->getStateUsing(fn($record) => number_format($record->radius_meter / 1000, 2) . ' km')
                                    ->icon('heroicon-o-arrow-path-rounded-square')
                                    ->badge()
                                    ->color('info'),

                                TextEntry::make('jam_buka_hari')
                                    ->label('Jam Buka/Hari')
                                    ->getStateUsing(function ($record) {
                                        if ($record->jam_masuk && $record->jam_pulang) {
                                            $masuk = \Carbon\Carbon::parse($record->jam_masuk);
                                            $pulang = \Carbon\Carbon::parse($record->jam_pulang);
                                            return $masuk->diffInHours($pulang) . ' jam/hari';
                                        }
                                        return '-';
                                    })
                                    ->icon('heroicon-o-calendar-days')
                                    ->badge()
                                    ->color('primary'),

                                TextEntry::make('status_toleransi')
                                    ->label('Level Toleransi')
                                    ->getStateUsing(function ($record) {
                                        $toleransi = $record->toleransi_terlambat;
                                        if ($toleransi <= 5)
                                            return 'Ketat';
                                        if ($toleransi <= 15)
                                            return 'Normal';
                                        if ($toleransi <= 30)
                                            return 'Fleksibel';
                                        return 'Sangat Fleksibel';
                                    })
                                    ->color(function ($record) {
                                        $toleransi = $record->toleransi_terlambat;
                                        if ($toleransi <= 5)
                                            return 'danger';
                                        if ($toleransi <= 15)
                                            return 'warning';
                                        if ($toleransi <= 30)
                                            return 'success';
                                        return 'info';
                                    })
                                    ->icon('heroicon-o-scale')
                                    ->badge(),

                                TextEntry::make('zona_waktu')
                                    ->label('Zona Waktu')
                                    ->getStateUsing(fn() => 'WIB (UTC+7)')
                                    ->icon('heroicon-o-globe-asia-australia')
                                    ->badge()
                                    ->color('gray'),
                            ]),
                    ])
                    ->icon('heroicon-o-chart-bar')
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

}