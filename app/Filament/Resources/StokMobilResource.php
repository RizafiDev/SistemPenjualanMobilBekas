<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StokMobilResource\Pages;
use App\Models\StokMobil;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StokMobilResource extends Resource
{
    protected static ?string $model = StokMobil::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Stok Mobil';

    protected static ?string $pluralLabel = 'Stok Mobil';

    protected static ?string $navigationGroup = 'Data Produk';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Mobil')
                    ->schema([
                        Forms\Components\Select::make('mobil_id')
                            ->relationship('mobil', 'nama')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('nama')
                                    ->required(),
                                Forms\Components\Select::make('merek_id')
                                    ->relationship('merek', 'nama')
                                    ->required(),
                            ]),

                        Forms\Components\Select::make('varian_id')
                            ->relationship('varian', 'nama')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('nama')
                                    ->required(),
                                Forms\Components\Select::make('mobil_id')
                                    ->relationship('mobil', 'nama')
                                    ->required(),
                            ]),

                        Forms\Components\TextInput::make('warna')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('no_rangka')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('no_mesin')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Kondisi & Status')
                    ->schema([
                        Forms\Components\TextInput::make('tahun')
                            ->required()
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(date('Y')),

                        Forms\Components\TextInput::make('kilometer')
                            ->required()
                            ->numeric()
                            ->minValue(0),

                        Forms\Components\Select::make('kondisi')
                            ->options(StokMobil::KONDISI)
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->options(StokMobil::STATUS)
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Harga & Tanggal')
                    ->schema([
                        Forms\Components\TextInput::make('harga_beli')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->maxValue(999999999999999),

                        Forms\Components\TextInput::make('harga_jual')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->maxValue(999999999999999),

                        Forms\Components\DatePicker::make('tanggal_masuk')
                            ->required(),

                        Forms\Components\DatePicker::make('tanggal_keluar'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Foto Kondisi')
                    ->schema([
                        Forms\Components\FileUpload::make('foto_kondisi')
                            ->multiple()
                            ->image()
                            ->imageEditor()
                            ->reorderable()
                            ->directory('mobil/kondisi')
                            ->maxSize(2048)
                            ->maxFiles(10)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Informasi Tambahan')
                    ->schema([
                        Forms\Components\TextInput::make('lokasi')
                            ->maxLength(255),

                        Forms\Components\Textarea::make('catatan')
                            ->rows(3),

                        Forms\Components\TagsInput::make('kelengkapan')
                            ->placeholder('Tambahkan kelengkapan')
                            ->splitKeys(['Enter', 'Tab', ',', ' ']),

                        Forms\Components\Repeater::make('riwayat_perbaikan')
                            ->schema([
                                Forms\Components\DatePicker::make('tanggal')
                                    ->required(),
                                Forms\Components\TextInput::make('jenis_perbaikan')
                                    ->required(),
                                Forms\Components\TextInput::make('biaya')
                                    ->numeric()
                                    ->prefix('Rp'),
                                Forms\Components\Textarea::make('keterangan')
                                    ->rows(2),
                            ])
                            ->defaultItems(0)
                            ->reorderable()
                            ->collapsible(),

                        Forms\Components\Repeater::make('dokumen')
                            ->schema([
                                Forms\Components\TextInput::make('jenis')
                                    ->required(),
                                Forms\Components\DatePicker::make('masa_berlaku'),
                                Forms\Components\TextInput::make('nomor')
                                    ->required(),
                                Forms\Components\FileUpload::make('file')
                                    ->directory('mobil/dokumen'),
                            ])
                            ->defaultItems(0)
                            ->reorderable()
                            ->collapsible(),
                    ])
                    ->collapsible()
                    ->columns(2),

                Forms\Components\Section::make('Kondisi Fitur')
                    ->description('Centang fitur yang masih berfungsi dengan baik')
                    ->schema([
                        Forms\Components\Tabs::make('Kondisi Fitur')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('Keamanan')
                                    ->schema([
                                        Forms\Components\CheckboxList::make('kondisi_fitur.keamanan')
                                            ->label('Fitur Keamanan')
                                            ->options(function ($get) {
                                                $varianId = $get('varian_id');
                                                if (!$varianId)
                                                    return [];

                                                $varian = \App\Models\Varian::find($varianId);
                                                if (!$varian)
                                                    return [];

                                                return collect($varian->fitur_keamanan ?? [])
                                                    ->mapWithKeys(fn($item) => [$item => $item])
                                                    ->toArray();
                                            })
                                            ->columns(2)
                                            ->searchable(),
                                    ]),

                                Forms\Components\Tabs\Tab::make('Kenyamanan')
                                    ->schema([
                                        Forms\Components\CheckboxList::make('kondisi_fitur.kenyamanan')
                                            ->label('Fitur Kenyamanan')
                                            ->options(function ($get) {
                                                $varianId = $get('varian_id');
                                                if (!$varianId)
                                                    return [];

                                                $varian = \App\Models\Varian::find($varianId);
                                                if (!$varian)
                                                    return [];

                                                return collect($varian->fitur_kenyamanan ?? [])
                                                    ->mapWithKeys(fn($item) => [$item => $item])
                                                    ->toArray();
                                            })
                                            ->columns(2)
                                            ->searchable(),
                                    ]),

                                Forms\Components\Tabs\Tab::make('Hiburan')
                                    ->schema([
                                        Forms\Components\CheckboxList::make('kondisi_fitur.hiburan')
                                            ->label('Fitur Hiburan')
                                            ->options(function ($get) {
                                                $varianId = $get('varian_id');
                                                if (!$varianId)
                                                    return [];

                                                $varian = \App\Models\Varian::find($varianId);
                                                if (!$varian)
                                                    return [];

                                                return collect($varian->fitur_hiburan ?? [])
                                                    ->mapWithKeys(fn($item) => [$item => $item])
                                                    ->toArray();
                                            })
                                            ->columns(2)
                                            ->searchable(),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('foto_kondisi')
                    ->circular()
                    ->stacked()
                    ->limit(3),

                Tables\Columns\TextColumn::make('mobil.nama')
                    ->searchable()
                    ->sortable()
                    ->description(fn(StokMobil $record): string => $record->varian?->nama ?? '-'),

                Tables\Columns\TextColumn::make('no_rangka')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('tahun')
                    ->sortable(),

                Tables\Columns\TextColumn::make('kilometer')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('kondisi')
                    ->colors([
                        'success' => 'sangat_baik',
                        'info' => 'baik',
                        'warning' => 'cukup',
                        'danger' => 'butuh_perbaikan',
                        'gray' => 'project',
                    ])
                    ->formatStateUsing(fn($state) => str($state)->replace('_', ' ')->title()),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'tersedia',
                        'info' => 'terjual',
                        'warning' => 'booking',
                        'gray' => 'indent',
                        'danger' => 'dalam_perbaikan',
                    ])
                    ->formatStateUsing(fn($state) => str($state)->replace('_', ' ')->title()),

                Tables\Columns\TextColumn::make('harga_jual')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_masuk')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),

                Tables\Filters\SelectFilter::make('kondisi')
                    ->options(StokMobil::KONDISI),

                Tables\Filters\SelectFilter::make('status')
                    ->options(StokMobil::STATUS),

                Tables\Filters\Filter::make('tahun')
                    ->form([
                        Forms\Components\TextInput::make('tahun_dari')
                            ->numeric(),
                        Forms\Components\TextInput::make('tahun_sampai')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['tahun_dari'],
                                fn(Builder $query, $date): Builder => $query->where('tahun', '>=', $date),
                            )
                            ->when(
                                $data['tahun_sampai'],
                                fn(Builder $query, $date): Builder => $query->where('tahun', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada data stok mobil')
            ->emptyStateDescription('Mulai dengan menambahkan stok mobil pertama Anda.')
            ->emptyStateIcon('heroicon-o-truck');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
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
            'index' => Pages\ListStokMobils::route('/'),
            'create' => Pages\CreateStokMobil::route('/create'),
            'view' => Pages\ViewStokMobil::route('/{record}'),
            'edit' => Pages\EditStokMobil::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
