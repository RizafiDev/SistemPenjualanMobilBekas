<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RiwayatServisResource\Pages;
use App\Models\RiwayatServis;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RiwayatServisResource extends Resource
{
    protected static ?string $model = RiwayatServis::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Riwayat Servis';

    protected static ?string $pluralLabel = 'Riwayat Servis';

    protected static ?string $navigationGroup = 'Data Produk';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Servis')
                    ->schema([
                        Forms\Components\Select::make('stok_mobil_id')
                            ->relationship('stokMobil', 'no_rangka')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Mobil')
                            ->getOptionLabelFromRecordUsing(fn($record) =>
                                "{$record->mobil->nama} - {$record->no_rangka}"),

                        Forms\Components\DatePicker::make('tanggal_servis')
                            ->required()
                            ->maxDate(now()),

                        Forms\Components\TextInput::make('jenis_servis')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('tempat_servis')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detail Servis')
                    ->schema([
                        Forms\Components\Textarea::make('deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('biaya')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->maxValue(999999999999999),

                        Forms\Components\TextInput::make('kilometer_servis')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->label('Kilometer'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Dokumen')
                    ->schema([
                        Forms\Components\FileUpload::make('foto_bukti')
                            ->multiple()
                            ->image()
                            ->directory('servis/bukti')
                            ->maxSize(2048)
                            ->maxFiles(5)
                            ->columnSpanFull(),

                        Forms\Components\Repeater::make('sparepart')
                            ->schema([
                                Forms\Components\TextInput::make('nama')
                                    ->required(),
                                Forms\Components\TextInput::make('kode')
                                    ->required(),
                                Forms\Components\TextInput::make('jumlah')
                                    ->required()
                                    ->numeric(),
                                Forms\Components\TextInput::make('harga')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp'),
                            ])
                            ->columns(4)
                            ->defaultItems(0)
                            ->reorderable()
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('stokMobil.mobil.nama')
                    ->label('Mobil')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record->stokMobil->no_rangka),

                Tables\Columns\TextColumn::make('tanggal_servis')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jenis_servis')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tempat_servis')
                    ->searchable(),

                Tables\Columns\TextColumn::make('kilometer_servis')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('biaya')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),

                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_servis', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_servis', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada data riwayat servis')
            ->emptyStateDescription('Mulai dengan menambahkan riwayat servis pertama.')
            ->emptyStateIcon('heroicon-o-wrench-screwdriver');
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
            'index' => Pages\ListRiwayatServis::route('/'),
            'create' => Pages\CreateRiwayatServis::route('/create'),
            'view' => Pages\ViewRiwayatServis::route('/{record}'),
            'edit' => Pages\EditRiwayatServis::route('/{record}/edit'),
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
