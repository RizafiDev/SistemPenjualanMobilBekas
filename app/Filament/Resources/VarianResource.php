<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VarianResource\Pages;
use App\Models\Varian;
use App\Models\Mobil;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VarianResource extends Resource
{
    protected static ?string $model = Varian::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Varian Mobil';

    protected static ?string $modelLabel = 'Varian';

    protected static ?string $pluralModelLabel = 'Varian';

    protected static ?string $navigationGroup = 'Manajemen Mobil';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Dasar')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('mobil_id')
                                    ->label('Mobil')
                                    ->relationship('mobil', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\TextInput::make('nama')
                                    ->label('Nama Varian')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('kode')
                                    ->label('Kode Varian')
                                    ->maxLength(255)
                                    ->placeholder('Contoh: G, V, E'),

                                Forms\Components\TextInput::make('harga_otr')
                                    ->label('Harga OTR')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->placeholder('0'),
                            ]),

                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->rows(3),

                        Forms\Components\Toggle::make('aktif')
                            ->label('Status Aktif')
                            ->default(true),
                    ]),

                Section::make('Spesifikasi Mesin')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('tipe_mesin')
                                    ->label('Tipe Mesin')
                                    ->options(Varian::TIPE_MESIN)
                                    ->required(),

                                Forms\Components\TextInput::make('kapasitas_mesin_cc')
                                    ->label('Kapasitas Mesin (cc)')
                                    ->numeric()
                                    ->suffix('cc'),

                                Forms\Components\TextInput::make('silinder')
                                    ->label('Jumlah Silinder')
                                    ->numeric(),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('transmisi')
                                    ->label('Transmisi')
                                    ->options(Varian::TRANSMISI)
                                    ->required(),

                                Forms\Components\TextInput::make('jumlah_gigi')
                                    ->label('Jumlah Gigi')
                                    ->numeric(),

                                Forms\Components\TextInput::make('daya_hp')
                                    ->label('Daya Maksimal')
                                    ->numeric()
                                    ->suffix('HP'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('torsi_nm')
                                    ->label('Torsi Maksimal')
                                    ->numeric()
                                    ->suffix('Nm'),

                                Forms\Components\TextInput::make('jenis_bahan_bakar')
                                    ->label('Jenis Bahan Bakar')
                                    ->required()
                                    ->placeholder('Contoh: Pertamax, Solar, Listrik'),

                                Forms\Components\TextInput::make('konsumsi_bahan_bakar_kota')
                                    ->label('Konsumsi BB Kota')
                                    ->numeric()
                                    ->suffix('km/L'),
                            ]),

                        Forms\Components\TextInput::make('konsumsi_bahan_bakar_jalan')
                            ->label('Konsumsi BB Jalan Raya')
                            ->numeric()
                            ->suffix('km/L'),
                    ]),

                Section::make('Dimensi & Kapasitas')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('panjang_mm')
                                    ->label('Panjang')
                                    ->numeric()
                                    ->suffix('mm'),

                                Forms\Components\TextInput::make('lebar_mm')
                                    ->label('Lebar')
                                    ->numeric()
                                    ->suffix('mm'),

                                Forms\Components\TextInput::make('tinggi_mm')
                                    ->label('Tinggi')
                                    ->numeric()
                                    ->suffix('mm'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('jarak_sumbu_roda_mm')
                                    ->label('Jarak Sumbu Roda')
                                    ->numeric()
                                    ->suffix('mm'),

                                Forms\Components\TextInput::make('ground_clearance_mm')
                                    ->label('Ground Clearance')
                                    ->numeric()
                                    ->suffix('mm'),

                                Forms\Components\TextInput::make('berat_kosong_kg')
                                    ->label('Berat Kosong')
                                    ->numeric()
                                    ->suffix('kg'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('berat_isi_kg')
                                    ->label('Berat Isi')
                                    ->numeric()
                                    ->suffix('kg'),

                                Forms\Components\TextInput::make('kapasitas_bagasi_l')
                                    ->label('Kapasitas Bagasi')
                                    ->numeric()
                                    ->suffix('L'),

                                Forms\Components\TextInput::make('kapasitas_tangki_l')
                                    ->label('Kapasitas Tangki')
                                    ->numeric()
                                    ->suffix('L'),
                            ]),
                    ]),

                Section::make('Performa')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('akselerasi_0_100_kmh')
                                    ->label('Akselerasi 0-100 km/h')
                                    ->numeric()
                                    ->suffix('detik'),

                                Forms\Components\TextInput::make('kecepatan_maksimal_kmh')
                                    ->label('Kecepatan Maksimal')
                                    ->numeric()
                                    ->suffix('km/h'),
                            ]),
                    ]),

                Section::make('Fitur')
                    ->schema([
                        Forms\Components\Section::make('Fitur Keamanan')
                            ->schema([
                                Forms\Components\CheckboxList::make('fitur_keamanan')
                                    ->options([
                                        'ABS' => 'ABS (Anti-lock Braking System)',
                                        'EBD' => 'EBD (Electronic Brake Distribution)',
                                        'BA' => 'BA (Brake Assist)',
                                        'VSC' => 'VSC (Vehicle Stability Control)',
                                        'TCS' => 'TCS (Traction Control System)',
                                        'Hill Start Assist' => 'Hill Start Assist',
                                        'Airbag Pengemudi' => 'Airbag Pengemudi',
                                        'Airbag Depan' => 'Airbag Depan',
                                        'Airbag Samping' => 'Airbag Samping',
                                        'Airbag Tirai' => 'Airbag Tirai',
                                        'Alarm & Immobilizer' => 'Alarm & Immobilizer',
                                        'ISOFIX' => 'ISOFIX',
                                        'Sabuk Pengaman 3 Titik' => 'Sabuk Pengaman 3 Titik',
                                        'Kamera Parkir' => 'Kamera Parkir',
                                        'Sensor Parkir' => 'Sensor Parkir',
                                    ])
                                    ->columns(2)
                                    ->searchable(),
                            ])
                            ->collapsible(),

                        Forms\Components\Section::make('Fitur Kenyamanan')
                            ->schema([
                                Forms\Components\CheckboxList::make('fitur_kenyamanan')
                                    ->options([
                                        'AC' => 'AC (Air Conditioner)',
                                        'AC Dual Zone' => 'AC Dual Zone',
                                        'Power Steering' => 'Power Steering',
                                        'Electric Mirror' => 'Spion Elektrik',
                                        'Power Window' => 'Power Window',
                                        'Central Lock' => 'Central Lock',
                                        'Keyless Entry' => 'Keyless Entry',
                                        'Push Start Button' => 'Push Start Button',
                                        'Cruise Control' => 'Cruise Control',
                                        'Auto Rain Sensor' => 'Sensor Hujan',
                                        'Auto Light' => 'Lampu Otomatis',
                                        'Electric Seat' => 'Jok Elektrik',
                                        'Leather Seat' => 'Jok Kulit',
                                        'Tilt Steering' => 'Kemudi Tilt',
                                        'Telescopic Steering' => 'Kemudi Telescopic',
                                        'Adjustable Steering' => 'Setir Dapat Diatur',
                                        'Armrest' => 'Sandaran Tangan',
                                    ])
                                    ->columns(2)
                                    ->searchable(),
                            ])
                            ->collapsible(),

                        Forms\Components\Section::make('Fitur Hiburan')
                            ->schema([
                                Forms\Components\CheckboxList::make('fitur_hiburan')
                                    ->options([
                                        'Radio' => 'Radio AM/FM',
                                        'CD Player' => 'CD Player',
                                        'DVD Player' => 'DVD Player',
                                        'TV' => 'TV',
                                        'GPS Navigation' => 'GPS Navigation',
                                        'USB Port' => 'USB Port',
                                        'Bluetooth' => 'Bluetooth',
                                        'Apple CarPlay' => 'Apple CarPlay',
                                        'Android Auto' => 'Android Auto',
                                        'Aux Input' => 'Aux Input',
                                        'Touchscreen Head Unit' => 'Head Unit Touchscreen',
                                        'Speaker' => 'Speaker',
                                        'Subwoofer' => 'Subwoofer',
                                        'Steering Switch' => 'Kontrol Audio di Setir',
                                    ])
                                    ->columns(2)
                                    ->searchable(),
                            ])
                            ->collapsible(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('mobil.nama')
                    ->label('Mobil')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Varian')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('kode')
                    ->label('Kode')
                    ->searchable(),

                Tables\Columns\TextColumn::make('harga_otr')
                    ->label('Harga OTR')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tipe_mesin')
                    ->label('Tipe Mesin')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'bensin' => 'success',
                        'diesel' => 'warning',
                        'hybrid' => 'info',
                        'listrik' => 'primary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('transmisi')
                    ->label('Transmisi')
                    ->badge(),

                Tables\Columns\TextColumn::make('kapasitas_mesin_cc')
                    ->label('CC')
                    ->suffix(' cc')
                    ->sortable(),

                Tables\Columns\IconColumn::make('aktif')
                    ->label('Status')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('mobil_id')
                    ->label('Mobil')
                    ->relationship('mobil', 'nama')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('tipe_mesin')
                    ->label('Tipe Mesin')
                    ->options(Varian::TIPE_MESIN),

                SelectFilter::make('transmisi')
                    ->label('Transmisi')
                    ->options(Varian::TRANSMISI),

                TernaryFilter::make('aktif')
                    ->label('Status Aktif'),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(function ($record) {
                return static::getUrl('view', ['record' => $record]);
            });
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
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVarians::route('/'),
            'create' => Pages\CreateVarian::route('/create'),
            'edit' => Pages\EditVarian::route('/{record}/edit'),
            'view' => Pages\ViewVarian::route('/{record}'),
        ];
    }
}