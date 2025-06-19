<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MobilResource\Pages;
use App\Models\Mobil;
use App\Models\Merek;
use App\Models\Kategori;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class MobilResource extends Resource
{
    protected static ?string $model = Mobil::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Mobil';

    protected static ?string $pluralLabel = 'Mobil';

    protected static ?string $navigationGroup = 'Data Produk';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nama')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                        if ($operation !== 'create') {
                                            return;
                                        }
                                        $set('slug', Str::slug($state));
                                    }),

                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Mobil::class, 'slug', ignoreRecord: true)
                                    ->rules(['alpha_dash'])
                                    ->helperText('URL-friendly version dari nama mobil'),

                                Forms\Components\Select::make('merek_id')
                                    ->label('Merek')
                                    ->relationship('merek', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('nama')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('negara_asal'),
                                        Forms\Components\TextInput::make('tahun_berdiri')
                                            ->numeric(),
                                    ]),

                                Forms\Components\Select::make('kategori_id')
                                    ->label('Kategori')
                                    ->relationship('kategori', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('nama')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('deskripsi'),
                                    ]),
                            ]),
                    ]),

                Forms\Components\Section::make('Spesifikasi')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('tahun_mulai')
                                    ->label('Tahun Mulai Produksi')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1900)
                                    ->maxValue(date('Y') + 2)
                                    ->default(date('Y')),

                                Forms\Components\TextInput::make('tahun_akhir')
                                    ->label('Tahun Akhir Produksi')
                                    ->numeric()
                                    ->minValue(1900)
                                    ->maxValue(date('Y') + 2)
                                    ->helperText('Kosongkan jika masih diproduksi')
                                    ->gte('tahun_mulai'),

                                Forms\Components\TextInput::make('kapasitas_penumpang')
                                    ->label('Kapasitas Penumpang')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(20)
                                    ->suffix('orang'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('tipe_bodi')
                                    ->label('Tipe Bodi')
                                    ->required()
                                    ->options(Mobil::TIPE_BODI)
                                    ->native(false),

                                Forms\Components\Select::make('status')
                                    ->required()
                                    ->options(Mobil::STATUS)
                                    ->default('aktif')
                                    ->native(false),
                            ]),
                    ]),

                Forms\Components\Section::make('Deskripsi & Fitur')
                    ->schema([
                        Forms\Components\Textarea::make('deskripsi')
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder('Deskripsi lengkap tentang mobil ini...'),

                        Forms\Components\Textarea::make('fitur_unggulan')
                            ->label('Fitur Unggulan')
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder('Fitur-fitur unggulan yang dimiliki mobil ini...')
                            ->helperText('Pisahkan setiap fitur dengan enter atau bullet point'),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('merek.nama')
                    ->label('Merek')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('kategori.nama')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('tipe_bodi')
                    ->label('Tipe Bodi')
                    ->formatStateUsing(fn(string $state): string => Mobil::TIPE_BODI[$state])
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'sedan' => 'blue',
                        'hatchback' => 'green',
                        'suv' => 'orange',
                        'mpv' => 'purple',
                        'pickup' => 'red',
                        'coupe' => 'pink',
                        'convertible' => 'yellow',
                        'wagon' => 'indigo',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('rentang_tahun')
                    ->label('Tahun Produksi')
                    ->getStateUsing(function (Mobil $record): string {
                        if ($record->tahun_akhir) {
                            return $record->tahun_mulai . ' - ' . $record->tahun_akhir;
                        }
                        return $record->tahun_mulai . ' - Sekarang';
                    })
                    ->sortable(['tahun_mulai', 'tahun_akhir'])
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('kapasitas_penumpang')
                    ->label('Kapasitas')
                    ->numeric()
                    ->suffix(' orang')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn(string $state): string => Mobil::STATUS[$state])
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'aktif' => 'success',
                        'dihentikan' => 'danger',
                        'akan_datang' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),

                Tables\Filters\SelectFilter::make('merek_id')
                    ->label('Merek')
                    ->relationship('merek', 'nama')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('tipe_bodi')
                    ->label('Tipe Bodi')
                    ->options(Mobil::TIPE_BODI)
                    ->multiple(),

                Tables\Filters\SelectFilter::make('status')
                    ->options(Mobil::STATUS)
                    ->multiple(),

                Tables\Filters\Filter::make('tahun_produksi')
                    ->form([
                        Forms\Components\TextInput::make('dari_tahun')
                            ->label('Dari Tahun')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(date('Y')),
                        Forms\Components\TextInput::make('sampai_tahun')
                            ->label('Sampai Tahun')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(date('Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tahun'],
                                fn(Builder $query, $tahun): Builder => $query->where('tahun_mulai', '>=', $tahun),
                            )
                            ->when(
                                $data['sampai_tahun'],
                                fn(Builder $query, $tahun): Builder => $query->where(function ($q) use ($tahun) {
                                    $q->whereNull('tahun_akhir')
                                        ->orWhere('tahun_akhir', '<=', $tahun);
                                }),
                            );
                    }),

                Tables\Filters\Filter::make('kapasitas_penumpang')
                    ->form([
                        Forms\Components\TextInput::make('min_kapasitas')
                            ->label('Minimum Kapasitas')
                            ->numeric()
                            ->minValue(1),
                        Forms\Components\TextInput::make('max_kapasitas')
                            ->label('Maksimum Kapasitas')
                            ->numeric()
                            ->minValue(1),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_kapasitas'],
                                fn(Builder $query, $kapasitas): Builder => $query->where('kapasitas_penumpang', '>=', $kapasitas),
                            )
                            ->when(
                                $data['max_kapasitas'],
                                fn(Builder $query, $kapasitas): Builder => $query->where('kapasitas_penumpang', '<=', $kapasitas),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),

                    Tables\Actions\Action::make('ubahStatus')
                        ->label('Ubah Status')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Forms\Components\Select::make('status_baru')
                                ->label('Status Baru')
                                ->options(Mobil::STATUS)
                                ->required()
                                ->default(fn(Mobil $record) => $record->status),
                        ])
                        ->action(function (Mobil $record, array $data) {
                            $record->update(['status' => $data['status_baru']]);
                        })
                        ->successNotificationTitle('Status mobil berhasil diubah'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),

                    Tables\Actions\BulkAction::make('ubahStatusMassal')
                        ->label('Ubah Status')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Forms\Components\Select::make('status_baru')
                                ->label('Status Baru')
                                ->options(Mobil::STATUS)
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            foreach ($records as $record) {
                                $record->update(['status' => $data['status_baru']]);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum ada data mobil')
            ->emptyStateDescription('Mulai dengan menambahkan mobil pertama Anda.')
            ->emptyStateIcon('heroicon-o-truck');
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
            'index' => Pages\ListMobils::route('/'),
            'create' => Pages\CreateMobil::route('/create'),
            'view' => Pages\ViewMobil::route('/{record}'),
            'edit' => Pages\EditMobil::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['merek', 'kategori'])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['merek', 'kategori']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nama', 'slug', 'merek.nama', 'kategori.nama'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Merek' => $record->merek->nama,
            'Kategori' => $record->kategori->nama,
            'Tipe Bodi' => Mobil::TIPE_BODI[$record->tipe_bodi],
            'Status' => Mobil::STATUS[$record->status],
            'Tahun' => $record->rentang_tahun,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 50 ? 'warning' : 'primary';
    }
}