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

class PengaturanKantorResource extends Resource
{
    protected static ?string $model = PengaturanKantor::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Pengaturan Kantor';

    protected static ?string $modelLabel = 'Pengaturan Kantor';

    protected static ?string $pluralModelLabel = 'Pengaturan Kantor';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 1;

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
                Section::make('Informasi Kantor')
                    ->schema([
                        TextEntry::make('nama_kantor')
                            ->label('Nama Kantor')
                            ->weight(FontWeight::SemiBold),

                        TextEntry::make('alamat_kantor')
                            ->label('Alamat Kantor'),
                    ])
                    ->columns(1),

                Section::make('Lokasi & Radius')
                    ->schema([
                        TextEntry::make('latitude')
                            ->label('Latitude')
                            ->copyable(),

                        TextEntry::make('longitude')
                            ->label('Longitude')
                            ->copyable(),

                        TextEntry::make('radius_meter')
                            ->label('Radius')
                            ->suffix(' meter'),
                    ])
                    ->columns(3),

                Section::make('Jam Kerja')
                    ->schema([
                        TextEntry::make('jam_masuk')
                            ->label('Jam Masuk')
                            ->time('H:i'),

                        TextEntry::make('jam_pulang')
                            ->label('Jam Pulang')
                            ->time('H:i'),

                        TextEntry::make('toleransi_terlambat')
                            ->label('Toleransi Terlambat')
                            ->suffix(' menit'),
                    ])
                    ->columns(3),

                Section::make('Status & Waktu')
                    ->schema([
                        TextEntry::make('aktif')
                            ->label('Status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                '1' => 'success',
                                '0' => 'danger',
                            })
                            ->formatStateUsing(fn(string $state): string => $state ? 'Aktif' : 'Tidak Aktif'),

                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime('d/m/Y H:i'),

                        TextEntry::make('updated_at')
                            ->label('Diperbarui')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(3),
            ]);
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