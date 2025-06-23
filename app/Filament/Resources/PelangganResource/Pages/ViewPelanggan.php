<?php
namespace App\Filament\Resources\PelangganResource\Pages;

use App\Filament\Resources\PelangganResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Support\Enums\FontWeight;

class ViewPelanggan extends ViewRecord
{
    protected static string $resource = PelangganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\ForceDeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('👤 Informasi Pribadi')
                    ->description('Data pribadi pelanggan')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('nama_lengkap')
                                    ->label('Nama Lengkap')
                                    ->icon('heroicon-o-identification')
                                    ->weight(FontWeight::Bold)
                                    ->size('lg')
                                    ->color('primary'),

                                TextEntry::make('nik')
                                    ->label('NIK')
                                    ->icon('heroicon-o-credit-card')
                                    ->placeholder('Belum diisi')
                                    ->copyable()
                                    ->copyMessage('NIK berhasil disalin!')
                                    ->color('gray'),

                                TextEntry::make('jenis_kelamin')
                                    ->label('Jenis Kelamin')
                                    ->icon('heroicon-o-user-group')
                                    ->formatStateUsing(
                                        fn(string $state): string =>
                                        \App\Models\Pelanggan::JENIS_KELAMIN[$state] ?? '-'
                                    )
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'L' => 'blue',
                                        'P' => 'pink',
                                        default => 'gray',
                                    }),

                                TextEntry::make('tanggal_lahir')
                                    ->label('Tanggal Lahir')
                                    ->icon('heroicon-o-calendar-days')
                                    ->date('d F Y')
                                    ->placeholder('Belum diisi')
                                    ->color('success'),

                                TextEntry::make('umur')
                                    ->label('Umur')
                                    ->icon('heroicon-o-clock')
                                    ->state(function ($record): string {
                                        return $record->umur ? $record->umur . ' tahun' : 'Belum diisi';
                                    })
                                    ->badge()
                                    ->color(fn($record): string => match (true) {
                                        !$record->umur => 'gray',
                                        $record->umur < 25 => 'info',
                                        $record->umur >= 25 && $record->umur < 50 => 'success',
                                        default => 'warning',
                                    }),
                            ]),

                        TextEntry::make('alamat')
                            ->label('Alamat Lengkap')
                            ->icon('heroicon-o-map-pin')
                            ->placeholder('Belum diisi')
                            ->columnSpanFull()
                            ->color('indigo'),
                    ])
                    ->collapsible(),

                Section::make('📞 Informasi Kontak')
                    ->description('Cara menghubungi pelanggan')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('no_telepon')
                                    ->label('Nomor Telepon')
                                    ->icon('heroicon-o-device-phone-mobile')
                                    ->placeholder('Belum diisi')
                                    ->copyable()
                                    ->copyMessage('Nomor telepon berhasil disalin!')
                                    ->url(fn($state) => $state ? "tel:{$state}" : null)
                                    ->color('green')
                                    ->weight(FontWeight::Medium),

                                TextEntry::make('email')
                                    ->label('Email')
                                    ->icon('heroicon-o-envelope')
                                    ->placeholder('Belum diisi')
                                    ->copyable()
                                    ->copyMessage('Email berhasil disalin!')
                                    ->url(fn($state) => $state ? "mailto:{$state}" : null)
                                    ->color('blue')
                                    ->weight(FontWeight::Medium),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('💼 Informasi Pekerjaan')
                    ->description('Detail pekerjaan dan karir')
                    ->icon('heroicon-o-briefcase')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('pekerjaan')
                                    ->label('Pekerjaan')
                                    ->icon('heroicon-o-wrench-screwdriver')
                                    ->placeholder('Belum diisi')
                                    ->badge()
                                    ->color('purple'),

                                TextEntry::make('perusahaan')
                                    ->label('Nama Perusahaan')
                                    ->icon('heroicon-o-building-office')
                                    ->placeholder('Belum diisi')
                                    ->weight(FontWeight::Medium)
                                    ->color('orange'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('📋 Data Tambahan')
                    ->description('Informasi pelengkap lainnya')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextEntry::make('data_tambahan')
                            ->label('Data Tambahan')
                            ->icon('heroicon-o-information-circle')
                            ->listWithLineBreaks()
                            ->placeholder('Tidak ada data tambahan')
                            ->formatStateUsing(function ($state) {
                                if (!$state || !is_array($state)) {
                                    return 'Tidak ada data tambahan';
                                }

                                $formatted = [];
                                foreach ($state as $key => $value) {
                                    $formatted[] = "• {$key}: {$value}";
                                }

                                return implode("\n", $formatted);
                            })
                            ->color('slate'),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Section::make('⚙️ Informasi Sistem')
                    ->description('Data sistem dan metadata')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('completion_percentage')
                                    ->label('Kelengkapan Data')
                                    ->icon('heroicon-o-chart-pie')
                                    ->state(function ($record): string {
                                        return $record->getCompletionPercentage() . '%';
                                    })
                                    ->badge()
                                    ->size('lg')
                                    ->color(fn($record): string => match (true) {
                                        $record->getCompletionPercentage() >= 90 => 'success',
                                        $record->getCompletionPercentage() >= 70 => 'warning',
                                        $record->getCompletionPercentage() >= 50 => 'info',
                                        default => 'danger',
                                    }),

                                TextEntry::make('created_at')
                                    ->label('Dibuat')
                                    ->icon('heroicon-o-plus-circle')
                                    ->dateTime('d F Y, H:i')
                                    ->color('emerald')
                                    ->weight(FontWeight::Medium),

                                TextEntry::make('updated_at')
                                    ->label('Diperbarui')
                                    ->icon('heroicon-o-pencil-square')
                                    ->dateTime('d F Y, H:i')
                                    ->color('amber')
                                    ->weight(FontWeight::Medium)
                                    ->since(),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}