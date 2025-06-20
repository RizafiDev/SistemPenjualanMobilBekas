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
                Section::make('Informasi Pribadi')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('nama_lengkap')
                                    ->label('Nama Lengkap')
                                    ->weight(FontWeight::Bold)
                                    ->size('lg'),

                                TextEntry::make('nik')
                                    ->label('NIK')
                                    ->placeholder('Belum diisi')
                                    ->copyable(),

                                TextEntry::make('jenis_kelamin')
                                    ->label('Jenis Kelamin')
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
                                    ->date('d F Y')
                                    ->placeholder('Belum diisi'),

                                TextEntry::make('umur')
                                    ->label('Umur')
                                    ->state(function ($record): string {
                                        return $record->umur ? $record->umur . ' tahun' : 'Belum diisi';
                                    }),
                            ]),

                        TextEntry::make('alamat')
                            ->label('Alamat Lengkap')
                            ->placeholder('Belum diisi')
                            ->columnSpanFull(),
                    ]),

                Section::make('Informasi Kontak')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('no_telepon')
                                    ->label('Nomor Telepon')
                                    ->placeholder('Belum diisi')
                                    ->copyable()
                                    ->url(fn($state) => $state ? "tel:{$state}" : null),

                                TextEntry::make('email')
                                    ->label('Email')
                                    ->placeholder('Belum diisi')
                                    ->copyable()
                                    ->url(fn($state) => $state ? "mailto:{$state}" : null),
                            ]),
                    ]),

                Section::make('Informasi Pekerjaan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('pekerjaan')
                                    ->label('Pekerjaan')
                                    ->placeholder('Belum diisi'),

                                TextEntry::make('perusahaan')
                                    ->label('Nama Perusahaan')
                                    ->placeholder('Belum diisi'),
                            ]),
                    ]),

                Section::make('Data Tambahan')
                    ->schema([
                        TextEntry::make('data_tambahan')
                            ->label('Data Tambahan')
                            ->listWithLineBreaks()
                            ->placeholder('Tidak ada data tambahan')
                            ->formatStateUsing(function ($state) {
                                if (!$state || !is_array($state)) {
                                    return 'Tidak ada data tambahan';
                                }

                                $formatted = [];
                                foreach ($state as $key => $value) {
                                    $formatted[] = "{$key}: {$value}";
                                }

                                return implode("\n", $formatted);
                            }),
                    ])
                    ->collapsible(),

                Section::make('Informasi Sistem')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('completion_percentage')
                                    ->label('Kelengkapan Data')
                                    ->state(function ($record): string {
                                        return $record->getCompletionPercentage() . '%';
                                    })
                                    ->badge()
                                    ->color(fn($record): string => match (true) {
                                        $record->getCompletionPercentage() >= 80 => 'success',
                                        $record->getCompletionPercentage() >= 60 => 'warning',
                                        default => 'danger',
                                    }),

                                TextEntry::make('created_at')
                                    ->label('Dibuat')
                                    ->dateTime('d F Y, H:i'),

                                TextEntry::make('updated_at')
                                    ->label('Diperbarui')
                                    ->dateTime('d F Y, H:i'),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
}