<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JanjiTemuResource\Pages;
use App\Models\JanjiTemu;
use App\Models\Karyawan;
use App\Models\StokMobil;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Enums\FiltersLayout;

class JanjiTemuResource extends Resource
{
    protected static ?string $model = JanjiTemu::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Janji Temu';

    protected static ?string $modelLabel = 'Janji Temu';

    protected static ?string $pluralModelLabel = 'Janji Temu';

    protected static ?string $navigationGroup = 'Penjualan';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Pelanggan')
                    ->schema([
                        Forms\Components\TextInput::make('nama_pelanggan')
                            ->label('Nama Pelanggan')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email_pelanggan')
                            ->label('Email Pelanggan')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('telepon_pelanggan')
                            ->label('Telepon Pelanggan')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('alamat_pelanggan')
                            ->label('Alamat Pelanggan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Detail Janji Temu')
                    ->schema([
                        Forms\Components\Select::make('stok_mobil_id')
                            ->label('Mobil yang Diminati')
                            ->options(function () {
                                return \App\Models\StokMobil::with(['mobil.merek', 'varian'])
                                    ->where('status', 'tersedia')
                                    ->get()
                                    ->mapWithKeys(function ($stok) {
                                        $mobil = $stok->mobil;
                                        $varian = $stok->varian;
                                        $merek = $mobil?->merek?->nama ?? '-';
                                        $namaMobil = $mobil?->nama ?? '-';
                                        $varianNama = $varian?->nama ? ' - ' . $varian->nama : '';
                                        $warna = $stok->warna ? ' - ' . $stok->warna : '';
                                        $label = "{$merek} - {$namaMobil}{$varianNama}{$warna}";
                                        return [$stok->id => $label];
                                    });
                            })
                            ->searchable()
                            ->nullable()
                            ->placeholder('Pilih mobil yang diminati'),
                        Forms\Components\Select::make('jenis')
                            ->label('Jenis Janji Temu')
                            ->options([
                                'test_drive' => 'Test Drive',
                                'konsultasi' => 'Konsultasi',
                                'negosiasi' => 'Negosiasi',
                                'survey_mobil' => 'Survey Mobil',
                                'lainnya' => 'Lainnya',
                            ])
                            ->required(),
                        Forms\Components\Select::make('metode')
                            ->label('Metode')
                            ->options([
                                'offline' => 'Offline',
                                'online' => 'Online',
                            ])
                            ->default('offline')
                            ->required(),
                        Forms\Components\TextInput::make('lokasi')
                            ->label('Lokasi')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Waktu & Jadwal')
                    ->schema([
                        Forms\Components\DateTimePicker::make('waktu_mulai')
                            ->label('Waktu Mulai')
                            ->required()
                            ->native(false),
                        Forms\Components\DateTimePicker::make('waktu_selesai')
                            ->label('Waktu Selesai')
                            ->required()
                            ->native(false)
                            ->after('waktu_mulai'),
                        Forms\Components\Repeater::make('waktu_alternatif')
                            ->label('Waktu Alternatif')
                            ->schema([
                                Forms\Components\DateTimePicker::make('waktu')
                                    ->label('Waktu')
                                    ->native(false)
                                    ->required(),
                            ])
                            ->columnSpanFull()
                            ->collapsible()
                            ->collapsed(),
                    ])->columns(2),

                Forms\Components\Section::make('Pesan & Catatan')
                    ->schema([
                        Forms\Components\Textarea::make('tujuan')
                            ->label('Tujuan')
                            ->rows(3),
                        Forms\Components\Textarea::make('pesan_tambahan')
                            ->label('Pesan Tambahan dari Pelanggan')
                            ->rows(3),
                        Forms\Components\Textarea::make('catatan_internal')
                            ->label('Catatan Internal')
                            ->rows(3)
                            ->helperText('Catatan internal untuk staff (tidak terlihat oleh pelanggan)'),
                    ])->columns(1),

                Forms\Components\Section::make('Status & Assignment')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Menunggu Konfirmasi',
                                'dikonfirmasi' => 'Dikonfirmasi',
                                'terjadwal' => 'Terjadwal',
                                'selesai' => 'Selesai',
                                'batal' => 'Dibatalkan',
                                'tidak_hadir' => 'Tidak Hadir',
                            ])
                            ->default('pending')
                            ->required(),
                        Forms\Components\Select::make('karyawan_id')
                            ->label('Sales yang Menangani')
                            ->relationship('karyawan', 'nama_lengkap')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\Select::make('dikonfirmasi_oleh')
                            ->label('Dikonfirmasi Oleh')
                            ->relationship('dikonfirmasiOleh', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\DateTimePicker::make('tanggal_konfirmasi')
                            ->label('Tanggal Konfirmasi')
                            ->native(false)
                            ->nullable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_request')
                    ->label('Tgl Request')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_pelanggan')
                    ->label('Pelanggan')
                    ->searchable()
                    ->weight(FontWeight::Medium),
                Tables\Columns\TextColumn::make('telepon_pelanggan')
                    ->label('Telepon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'test_drive' => 'success',
                        'konsultasi' => 'info',
                        'negosiasi' => 'warning',
                        'survey_mobil' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'test_drive' => 'Test Drive',
                        'konsultasi' => 'Konsultasi',
                        'negosiasi' => 'Negosiasi',
                        'survey_mobil' => 'Survey Mobil',
                        'lainnya' => 'Lainnya',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('waktu_mulai')
                    ->label('Jadwal')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'dikonfirmasi' => 'info',
                        'terjadwal' => 'success',
                        'selesai' => 'success',
                        'batal' => 'danger',
                        'tidak_hadir' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'dikonfirmasi' => 'Dikonfirmasi',
                        'terjadwal' => 'Terjadwal',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal',
                        'tidak_hadir' => 'Tidak Hadir',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('karyawan.nama_lengkap')
                    ->label('Sales')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('metode')
                    ->label('Metode')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'online' => 'info',
                        'offline' => 'success',
                        default => 'gray',
                    })
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu Konfirmasi',
                        'dikonfirmasi' => 'Dikonfirmasi',
                        'terjadwal' => 'Terjadwal',
                        'selesai' => 'Selesai',
                        'batal' => 'Dibatalkan',
                        'tidak_hadir' => 'Tidak Hadir',
                    ]),
                Tables\Filters\SelectFilter::make('jenis')
                    ->label('Jenis')
                    ->options([
                        'test_drive' => 'Test Drive',
                        'konsultasi' => 'Konsultasi',
                        'negosiasi' => 'Negosiasi',
                        'survey_mobil' => 'Survey Mobil',
                        'lainnya' => 'Lainnya',
                    ]),
                Tables\Filters\SelectFilter::make('karyawan_id')
                    ->label('Sales')
                    ->relationship('karyawan', 'nama_lengkap')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('hari_ini')
                    ->label('Hari Ini')
                    ->query(fn(Builder $query): Builder => $query->whereDate('waktu_mulai', today())),
                Tables\Filters\Filter::make('minggu_ini')
                    ->label('Minggu Ini')
                    ->query(fn(Builder $query): Builder => $query->whereBetween('waktu_mulai', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ])),
                Tables\Filters\TrashedFilter::make(),
            ], )
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('konfirmasi')
                        ->label('Konfirmasi')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn(JanjiTemu $record): bool => $record->status === 'pending')
                        ->requiresConfirmation()
                        ->action(function (JanjiTemu $record) {
                            $record->update([
                                'status' => 'dikonfirmasi',
                                'tanggal_konfirmasi' => now(),
                                'dikonfirmasi_oleh' => auth()->id(),
                            ]);
                        }),
                    Tables\Actions\Action::make('selesai')
                        ->label('Selesai')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->visible(fn(JanjiTemu $record): bool => $record->status === 'terjadwal')
                        ->requiresConfirmation()
                        ->action(fn(JanjiTemu $record) => $record->update(['status' => 'selesai'])),
                    Tables\Actions\Action::make('batal')
                        ->label('Batal')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn(JanjiTemu $record): bool => in_array($record->status, ['pending', 'dikonfirmasi', 'terjadwal']))
                        ->requiresConfirmation()
                        ->action(fn(JanjiTemu $record) => $record->update(['status' => 'batal'])),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal_request', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Data Pelanggan')
                    ->schema([
                        Infolists\Components\TextEntry::make('nama_pelanggan')
                            ->label('Nama'),
                        Infolists\Components\TextEntry::make('email_pelanggan')
                            ->label('Email'),
                        Infolists\Components\TextEntry::make('telepon_pelanggan')
                            ->label('Telepon'),
                        Infolists\Components\TextEntry::make('alamat_pelanggan')
                            ->label('Alamat')
                            ->columnSpanFull(),
                    ])->columns(2),

                Infolists\Components\Section::make('Detail Janji Temu')
                    ->schema([
                        Infolists\Components\TextEntry::make('stokMobil')
                            ->label('Mobil yang Diminati')
                            ->state(function ($record) {
                                $stok = $record->stokMobil;
                                if (!$stok)
                                    return '-';
                                $mobil = $stok->mobil;
                                $varian = $stok->varian;
                                $merek = $mobil?->merek?->nama ?? '-';
                                $namaMobil = $mobil?->nama ?? '-';
                                $varianNama = $varian?->nama ? ' - ' . $varian->nama : '';
                                $warna = $stok->warna ? ' - ' . $stok->warna : '';
                                return "{$merek} - {$namaMobil}{$varianNama}{$warna}";
                            }),
                        Infolists\Components\TextEntry::make('jenis')
                            ->label('Jenis')
                            ->badge()
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                'test_drive' => 'Test Drive',
                                'konsultasi' => 'Konsultasi',
                                'negosiasi' => 'Negosiasi',
                                'survey_mobil' => 'Survey Mobil',
                                'lainnya' => 'Lainnya',
                                default => $state,
                            }),
                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                'pending' => 'Menunggu Konfirmasi',
                                'dikonfirmasi' => 'Dikonfirmasi',
                                'terjadwal' => 'Terjadwal',
                                'selesai' => 'Selesai',
                                'batal' => 'Dibatalkan',
                                'tidak_hadir' => 'Tidak Hadir',
                                default => $state,
                            }),
                        Infolists\Components\TextEntry::make('waktu_mulai')
                            ->label('Waktu Mulai')
                            ->dateTime('d F Y, H:i'),
                        Infolists\Components\TextEntry::make('waktu_selesai')
                            ->label('Waktu Selesai')
                            ->dateTime('d F Y, H:i'),
                        Infolists\Components\TextEntry::make('metode')
                            ->label('Metode')
                            ->badge(),
                        Infolists\Components\TextEntry::make('lokasi')
                            ->label('Lokasi'),
                    ])->columns(2),

                Infolists\Components\Section::make('Pesan & Catatan')
                    ->schema([
                        Infolists\Components\TextEntry::make('tujuan')
                            ->label('Tujuan'),
                        Infolists\Components\TextEntry::make('pesan_tambahan')
                            ->label('Pesan Tambahan'),
                        Infolists\Components\TextEntry::make('catatan_internal')
                            ->label('Catatan Internal'),
                    ])->columns(1),

                Infolists\Components\Section::make('Informasi Sistem')
                    ->schema([
                        Infolists\Components\TextEntry::make('tanggal_request')
                            ->label('Tanggal Request')
                            ->dateTime('d F Y, H:i'),
                        Infolists\Components\TextEntry::make('tanggal_konfirmasi')
                            ->label('Tanggal Konfirmasi')
                            ->dateTime('d F Y, H:i'),
                        Infolists\Components\TextEntry::make('karyawan.nama_lengkap')
                            ->label('Sales yang Menangani'),
                        Infolists\Components\TextEntry::make('karyawan.email')
                            ->label('Email Sales')
                            ->visible(fn($record) => !empty($record->karyawan?->email)),
                        Infolists\Components\TextEntry::make('karyawan.telepon')
                            ->label('Telepon Sales')
                            ->visible(fn($record) => !empty($record->karyawan?->telepon)),
                        Infolists\Components\TextEntry::make('dikonfirmasiOleh.name')
                            ->label('Dikonfirmasi Oleh'),
                        Infolists\Components\TextEntry::make('tanggal_konfirmasi')
                            ->label('Tanggal Konfirmasi')
                            ->dateTime('d F Y, H:i'),
                    ])->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJanjiTemus::route('/'),
            'create' => Pages\CreateJanjiTemu::route('/create'),
            'view' => Pages\ViewJanjiTemu::route('/{record}'),
            'edit' => Pages\EditJanjiTemu::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}