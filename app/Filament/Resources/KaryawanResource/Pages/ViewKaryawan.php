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
                Section::make('Informasi Personal')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                ImageEntry::make('foto')
                                    ->label('Foto')
                                    ->circular()
                                    ->defaultImageUrl(url('/images/default-avatar.png'))
                                    ->columnSpan(1),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('nip')
                                            ->label('NIP')
                                            ->copyable(),

                                        TextEntry::make('nik')
                                            ->label('NIK')
                                            ->copyable(),

                                        TextEntry::make('nama_lengkap')
                                            ->label('Nama Lengkap')
                                            ->weight('bold'),

                                        TextEntry::make('email')
                                            ->label('Email')
                                            ->copyable(),

                                        TextEntry::make('no_telepon')
                                            ->label('No. Telepon')
                                            ->copyable(),

                                        TextEntry::make('jenis_kelamin')
                                            ->label('Jenis Kelamin')
                                            ->formatStateUsing(
                                                fn(string $state): string =>
                                                $state === 'L' ? 'Laki-laki' : 'Perempuan'
                                            ),

                                        TextEntry::make('tanggal_lahir')
                                            ->label('Tanggal Lahir')
                                            ->date('d F Y'),

                                        TextEntry::make('umur')
                                            ->label('Umur')
                                            ->suffix(' tahun'),
                                    ])
                                    ->columnSpan(2),
                            ]),

                        TextEntry::make('alamat')
                            ->label('Alamat')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make('Informasi Pekerjaan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('jabatan')
                                    ->label('Jabatan')
                                    ->badge(),

                                TextEntry::make('departemen')
                                    ->label('Departemen')
                                    ->badge(),

                                TextEntry::make('gaji_pokok')
                                    ->label('Gaji Pokok')
                                    ->money('IDR'),

                                TextEntry::make('tanggal_masuk')
                                    ->label('Tanggal Masuk')
                                    ->date('d F Y'),

                                TextEntry::make('masa_kerja')
                                    ->label('Masa Kerja')
                                    ->suffix(' tahun'),

                                TextEntry::make('status')
                                    ->label('Status Karyawan')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'tetap' => 'success',
                                        'kontrak' => 'warning',
                                        'magang' => 'info',
                                    })
                                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),

                                TextEntry::make('aktif')
                                    ->label('Status Aktif')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'aktif' => 'success',
                                        'nonaktif' => 'danger',
                                    })
                                    ->formatStateUsing(fn(string $state): string => match ($state) {
                                        'aktif' => 'Aktif',
                                        'nonaktif' => 'Non-Aktif',
                                    }),
                            ]),
                    ])
                    ->columns(2),

                Section::make('Data Tambahan')
                    ->schema([
                        KeyValueEntry::make('data_tambahan')
                            ->label('Data Tambahan'),
                    ])
                    ->collapsible()
                    ->visible(fn($record) => !empty($record->data_tambahan)),

                Section::make('Informasi Sistem')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Dibuat Pada')
                                    ->dateTime('d F Y, H:i'),

                                TextEntry::make('updated_at')
                                    ->label('Diperbarui Pada')
                                    ->dateTime('d F Y, H:i'),

                                TextEntry::make('deleted_at')
                                    ->label('Dihapus Pada')
                                    ->dateTime('d F Y, H:i')
                                    ->visible(fn($record) => $record->deleted_at),
                            ]),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}