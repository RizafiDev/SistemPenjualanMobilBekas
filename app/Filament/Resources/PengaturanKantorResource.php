<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengaturanKantorResource\Pages;
use App\Models\PengaturanKantor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Grid;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry\TextEntrySize;

class PengaturanKantorResource extends Resource
{
    protected static ?string $model = PengaturanKantor::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Pengaturan Kantor';

    protected static ?string $modelLabel = 'Pengaturan Kantor';

    protected static ?string $pluralModelLabel = 'Pengaturan Kantor';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 1;

    public static ?string $recordTitleAttribute = 'nama_kantor';

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'nama_kantor',
            'alamat_kantor',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kantor')
                    ->schema([
                        Forms\Components\TextInput::make('nama_kantor')
                            ->label('Nama Kantor')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('alamat_kantor')
                            ->label('Alamat Kantor')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Lokasi & Radius')
                    ->schema([
                        Forms\Components\TextInput::make('latitude')
                            ->label('Latitude')
                            ->required()
                            ->numeric()
                            ->step(0.00000001)
                            ->placeholder('Contoh: -6.9175000')
                            ->helperText('Koordinat lintang lokasi kantor'),

                        Forms\Components\TextInput::make('longitude')
                            ->label('Longitude')
                            ->required()
                            ->numeric()
                            ->step(0.00000001)
                            ->placeholder('Contoh: 107.6191670')
                            ->helperText('Koordinat bujur lokasi kantor'),

                        Forms\Components\TextInput::make('radius_meter')
                            ->label('Radius (Meter)')
                            ->required()
                            ->numeric()
                            ->default(100)
                            ->minValue(10)
                            ->maxValue(1000)
                            ->suffix('meter')
                            ->helperText('Jarak maksimal untuk absensi'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Jam Kerja')
                    ->schema([
                        Forms\Components\TimePicker::make('jam_masuk')
                            ->label('Jam Masuk')
                            ->required()
                            ->default('08:00:00')
                            ->seconds(false),

                        Forms\Components\TimePicker::make('jam_pulang')
                            ->label('Jam Pulang')
                            ->required()
                            ->default('17:00:00')
                            ->seconds(false),

                        Forms\Components\TextInput::make('toleransi_terlambat')
                            ->label('Toleransi Terlambat')
                            ->required()
                            ->numeric()
                            ->default(15)
                            ->minValue(0)
                            ->maxValue(60)
                            ->suffix('menit')
                            ->helperText('Toleransi keterlambatan dalam menit'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('aktif')
                            ->label('Status Aktif')
                            ->default(true)
                            ->helperText('Aktifkan pengaturan kantor ini'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_kantor')
                    ->label('Nama Kantor')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold),

                Tables\Columns\TextColumn::make('alamat_kantor')
                    ->label('Alamat')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('koordinat')
                    ->label('Koordinat')
                    ->getStateUsing(fn($record) => $record->latitude . ', ' . $record->longitude)
                    ->copyable()
                    ->copyMessage('Koordinat disalin!')
                    ->tooltip('Klik untuk menyalin koordinat'),

                Tables\Columns\TextColumn::make('radius_meter')
                    ->label('Radius')
                    ->sortable()
                    ->suffix(' m')
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('jam_masuk')
                    ->label('Jam Masuk')
                    ->time('H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jam_pulang')
                    ->label('Jam Pulang')
                    ->time('H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('toleransi_terlambat')
                    ->label('Toleransi')
                    ->suffix(' menit')
                    ->alignEnd(),

                Tables\Columns\IconColumn::make('aktif')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('aktif')
                    ->label('Status')
                    ->placeholder('Semua Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Header Section dengan Icon
                Section::make('📍 Informasi Kantor')
                    ->description('Detail informasi dasar kantor')
                    ->schema([
                        TextEntry::make('nama_kantor')
                            ->label('Nama Kantor')
                            ->weight(FontWeight::Bold)
                            ->size(TextEntrySize::Large)
                            ->color('primary')
                            ->icon('heroicon-o-building-office'),

                        TextEntry::make('alamat_kantor')
                            ->label('Alamat Kantor')
                            ->icon('heroicon-o-map-pin')
                            ->copyable()
                            ->copyMessage('Alamat berhasil disalin!')
                            ->copyMessageDuration(1500),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->compact(),

                // Location Section dengan Map Visual
                Section::make('🗺️ Lokasi & Radius')
                    ->description('Koordinat dan area cakupan kantor')
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
                                    ->label('Radius Kantor')
                                    ->suffix(' meter')
                                    ->icon('heroicon-o-arrow-path-rounded-square')
                                    ->badge()
                                    ->color('warning')
                                    ->weight(FontWeight::SemiBold),
                            ]),
                    ])
                    ->collapsible()
                    ->compact(),

                // Working Hours dengan Visual Clock
                Section::make('🕒 Jam Kerja')
                    ->description('Pengaturan waktu kerja dan toleransi')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('jam_masuk')
                                    ->label('Jam Masuk')
                                    ->time('H:i')
                                    ->icon('heroicon-o-clock')
                                    ->badge()
                                    ->color('success')
                                    ->weight(FontWeight::SemiBold),

                                TextEntry::make('jam_pulang')
                                    ->label('Jam Pulang')
                                    ->time('H:i')
                                    ->icon('heroicon-o-clock')
                                    ->badge()
                                    ->color('danger')
                                    ->weight(FontWeight::SemiBold),

                                TextEntry::make('toleransi_terlambat')
                                    ->label('Toleransi Keterlambatan')
                                    ->suffix(' menit')
                                    ->icon('heroicon-o-exclamation-triangle')
                                    ->badge()
                                    ->color('warning')
                                    ->weight(FontWeight::Medium),
                            ]),
                    ])
                    ->collapsible()
                    ->compact(),

                // Status Section dengan Enhanced Badge
                Section::make('⚡ Status & Riwayat')
                    ->description('Status aktif dan catatan waktu')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('aktif')
                                    ->label('Status Kantor')
                                    ->badge()
                                    ->size(TextEntrySize::Large)
                                    ->weight(FontWeight::Bold)
                                    ->color(fn(string $state): string => match ($state) {
                                        '1' => 'success',
                                        '0' => 'danger',
                                    })
                                    ->icon(fn(string $state): string => match ($state) {
                                        '1' => 'heroicon-o-check-circle',
                                        '0' => 'heroicon-o-x-circle',
                                    })
                                    ->formatStateUsing(fn(string $state): string => $state === '1' ? '✅ Aktif' : '❌ Tidak Aktif'),

                                TextEntry::make('created_at')
                                    ->label('Dibuat Pada')
                                    ->dateTime('d M Y, H:i')
                                    ->icon('heroicon-o-calendar-days')
                                    ->color('gray')
                                    ->tooltip('Tanggal pembuatan record'),

                                TextEntry::make('updated_at')
                                    ->label('Terakhir Diperbarui')
                                    ->dateTime('d M Y, H:i')
                                    ->icon('heroicon-o-arrow-path')
                                    ->color('gray')
                                    ->tooltip('Tanggal pembaruan terakhir')
                                    ->since(),
                            ]),
                    ])
                    ->collapsible()
                    ->compact(),

                // Additional Info Section (Optional)
                Section::make('📊 Informasi Tambahan')
                    ->description('Data pendukung dan statistik')
                    ->schema([
                        Fieldset::make('Koordinat Lengkap')
                            ->schema([
                                TextEntry::make('full_coordinates')
                                    ->label('Koordinat Lengkap')
                                    ->state(fn($record) => $record->latitude . ', ' . $record->longitude)
                                    ->copyable()
                                    ->copyMessage('Koordinat lengkap disalin!')
                                    ->icon('heroicon-o-map')
                                    ->badge()
                                    ->color('primary'),
                            ]),

                        Fieldset::make('Durasi Kerja')
                            ->schema([
                                TextEntry::make('durasi_kerja')
                                    ->label('Total Jam Kerja')
                                    ->state(function ($record) {
                                        $masuk = \Carbon\Carbon::createFromFormat('H:i:s', $record->jam_masuk);
                                        $pulang = \Carbon\Carbon::createFromFormat('H:i:s', $record->jam_pulang);
                                        $diff = $pulang->diff($masuk);
                                        return $diff->format('%h jam %i menit');
                                    })
                                    ->icon('heroicon-o-clock')
                                    ->badge()
                                    ->color('info'),
                            ]),
                    ])
                    ->collapsed()
                    ->compact(),
            ])
            ->columns(1);
    }



    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengaturanKantors::route('/'),
            'create' => Pages\CreatePengaturanKantor::route('/create'),
            'view' => Pages\ViewPengaturanKantor::route('/{record}'),
            'edit' => Pages\EditPengaturanKantor::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                \Illuminate\Database\Eloquent\SoftDeletingScope::class,
            ]);
    }
}