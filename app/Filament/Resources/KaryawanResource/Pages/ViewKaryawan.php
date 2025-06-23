<?php

namespace App\Filament\Resources\KaryawanResource\Pages;

use App\Filament\Resources\KaryawanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\Card;

class ViewKaryawan extends ViewRecord
{
    protected static string $resource = KaryawanResource::class;

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
                Section::make('👤 Informasi Personal')
                    ->description('Data pribadi karyawan')
                    ->icon('heroicon-o-user')
                    ->iconColor('primary')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Card::make()
                                    ->schema([
                                        ImageEntry::make('foto')
                                            ->label('Foto Profil')
                                            ->circular()
                                            ->size(120)
                                            ->defaultImageUrl(url('/images/default-avatar.png'))
                                            ->extraAttributes(['class' => 'mx-auto border-4 border-primary-200']),

                                        TextEntry::make('nama_lengkap')
                                            ->label('')
                                            ->weight('bold')
                                            ->size('lg')
                                            ->alignment('center')
                                            ->color('primary'),

                                        TextEntry::make('jabatan')
                                            ->label('')
                                            ->alignment('center')
                                            ->color('gray')
                                            ->extraAttributes(['class' => 'italic']),
                                    ])
                                    ->columnSpan(1)
                                    ->extraAttributes(['class' => 'text-center p-6']),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('nip')
                                            ->label('🆔 NIP')
                                            ->copyable()
                                            ->copyMessage('NIP berhasil disalin!')
                                            ->weight('medium')
                                            ->badge()
                                            ->color('info'),

                                        TextEntry::make('nik')
                                            ->label('🆔 NIK')
                                            ->copyable()
                                            ->copyMessage('NIK berhasil disalin!')
                                            ->weight('medium')
                                            ->badge()
                                            ->color('info'),

                                        TextEntry::make('email')
                                            ->label('📧 Email')
                                            ->copyable()
                                            ->copyMessage('Email berhasil disalin!')
                                            ->icon('heroicon-o-envelope')
                                            ->color('primary'),

                                        TextEntry::make('no_telepon')
                                            ->label('📱 No. Telepon')
                                            ->copyable()
                                            ->copyMessage('Nomor telepon berhasil disalin!')
                                            ->icon('heroicon-o-phone')
                                            ->color('success'),

                                        TextEntry::make('jenis_kelamin')
                                            ->label('⚧ Jenis Kelamin')
                                            ->badge()
                                            ->color(
                                                fn(string $state): string =>
                                                $state === 'L' ? 'blue' : 'pink'
                                            )
                                            ->formatStateUsing(
                                                fn(string $state): string =>
                                                $state === 'L' ? 'Laki-laki' : 'Perempuan'
                                            ),

                                        TextEntry::make('tanggal_lahir')
                                            ->label('🎂 Tanggal Lahir')
                                            ->date('d F Y')
                                            ->icon('heroicon-o-calendar')
                                            ->color('warning'),

                                        TextEntry::make('umur')
                                            ->label('⏳ Umur')
                                            ->suffix(' tahun')
                                            ->badge()
                                            ->color('gray'),
                                    ])
                                    ->columnSpan(2),
                            ]),

                        TextEntry::make('alamat')
                            ->label('🏠 Alamat Lengkap')
                            ->columnSpanFull()
                            ->icon('heroicon-o-map-pin')
                            ->extraAttributes(['class' => 'bg-gray-50 dark:bg-gray-900 p-4 rounded-lg']),
                    ])
                    ->columns(3)
                    ->collapsible()
                    ->persistCollapsed()
                    ->extraAttributes(['class' => 'border-2 border-primary-200 dark:border-primary-800']),

                Section::make('💼 Informasi Pekerjaan')
                    ->description('Detail pekerjaan dan karir karyawan')
                    ->icon('heroicon-o-briefcase')
                    ->iconColor('success')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Card::make()
                                    ->schema([
                                        TextEntry::make('jabatan')
                                            ->label('Jabatan')
                                            ->badge()
                                            ->size('lg')
                                            ->weight('bold')
                                            ->color('primary')
                                            ->alignment('center'),

                                        TextEntry::make('departemen')
                                            ->label('Departemen')
                                            ->badge()
                                            ->color('secondary')
                                            ->alignment('center'),
                                    ])
                                    ->columnSpan(1)
                                    ->extraAttributes(['class' => 'text-center bg-gradient-to-br from-primary-50 to-secondary-50 dark:from-primary-950 dark:to-secondary-950']),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('gaji_pokok')
                                            ->label('💰 Gaji Pokok')
                                            ->money('IDR')
                                            ->weight('bold')
                                            ->color('success')
                                            ->size('lg'),

                                        TextEntry::make('tanggal_masuk')
                                            ->label('📅 Tanggal Bergabung')
                                            ->date('d F Y')
                                            ->icon('heroicon-o-calendar-days')
                                            ->color('info'),

                                        TextEntry::make('masa_kerja')
                                            ->label('⏱️ Masa Kerja')
                                            ->suffix(' tahun')
                                            ->badge()
                                            ->color('warning')
                                            ->weight('bold'),

                                        TextEntry::make('status')
                                            ->label('📋 Status Karyawan')
                                            ->badge()
                                            ->size('lg')
                                            ->weight('bold')
                                            ->color(fn(string $state): string => match ($state) {
                                                'tetap' => 'success',
                                                'kontrak' => 'warning',
                                                'magang' => 'info',
                                            })
                                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                                'tetap' => '✅ Karyawan Tetap',
                                                'kontrak' => '📝 Karyawan Kontrak',
                                                'magang' => '🎓 Karyawan Magang',
                                            }),

                                        TextEntry::make('aktif')
                                            ->label('🔄 Status Keaktifan')
                                            ->badge()
                                            ->size('lg')
                                            ->weight('bold')
                                            ->color(fn(string $state): string => match ($state) {
                                                'aktif' => 'success',
                                                'nonaktif' => 'danger',
                                            })
                                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                                'aktif' => '🟢 Aktif',
                                                'nonaktif' => '🔴 Non-Aktif',
                                            }),
                                    ])
                                    ->columnSpan(2),
                            ]),
                    ])
                    ->columns(3)
                    ->collapsible()
                    ->persistCollapsed()
                    ->extraAttributes(['class' => 'border-2 border-success-200 dark:border-success-800']),

                Section::make('📊 Data Tambahan')
                    ->description('Informasi tambahan yang tersimpan')
                    ->icon('heroicon-o-document-plus')
                    ->iconColor('warning')
                    ->schema([
                        Card::make()
                            ->schema([
                                KeyValueEntry::make('data_tambahan')
                                    ->label('Detail Data Tambahan')
                                    ->keyLabel('Atribut')
                                    ->valueLabel('Nilai'),
                            ])
                            ->extraAttributes(['class' => 'bg-gradient-to-r from-warning-50 to-orange-50 dark:from-warning-950 dark:to-orange-950']),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->persistCollapsed()
                    ->visible(fn($record) => !empty($record->data_tambahan))
                    ->extraAttributes(['class' => 'border-2 border-warning-200 dark:border-warning-800']),

                Section::make('🔧 Informasi Sistem')
                    ->description('Data teknis dan riwayat perubahan')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->iconColor('gray')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('📝 Dibuat Pada')
                                    ->dateTime('d F Y, H:i')
                                    ->icon('heroicon-o-plus-circle')
                                    ->color('success')
                                    ->weight('medium'),

                                TextEntry::make('updated_at')
                                    ->label('✏️ Diperbarui Pada')
                                    ->dateTime('d F Y, H:i')
                                    ->icon('heroicon-o-pencil-square')
                                    ->color('warning')
                                    ->weight('medium'),

                                TextEntry::make('deleted_at')
                                    ->label('🗑️ Dihapus Pada')
                                    ->dateTime('d F Y, H:i')
                                    ->icon('heroicon-o-trash')
                                    ->color('danger')
                                    ->weight('bold')
                                    ->visible(fn($record) => $record->deleted_at),
                            ]),
                    ])
                    ->columns(3)
                    ->collapsible()
                    ->collapsed()
                    ->persistCollapsed()
                    ->extraAttributes(['class' => 'border-2 border-gray-200 dark:border-gray-700']),
            ]);
    }
}