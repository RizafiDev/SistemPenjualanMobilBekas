<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanPenjualanResource\Pages;
use App\Models\LaporanPenjualan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;

class LaporanPenjualanResource extends Resource
{
    protected static ?string $model = LaporanPenjualan::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Laporan Penjualan';

    protected static ?string $modelLabel = 'Laporan Penjualan';

    protected static ?string $pluralModelLabel = 'Laporan Penjualan';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 1;

    public static ?string $recordTitleAttribute = 'tanggal';

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'tanggal',
            'tahun',
            'bulan',
            'minggu',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Periode')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal')
                            ->required()
                            ->default(now())
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $date = \Carbon\Carbon::parse($state);
                                    $set('tahun', $date->year);
                                    $set('bulan', $date->month);
                                    $set('minggu', ceil($date->day / 7));
                                }
                            }),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('tahun')
                                    ->numeric()
                                    ->required()
                                    ->minValue(2020)
                                    ->maxValue(2030),

                                Forms\Components\Select::make('bulan')
                                    ->options([
                                        1 => 'Januari',
                                        2 => 'Februari',
                                        3 => 'Maret',
                                        4 => 'April',
                                        5 => 'Mei',
                                        6 => 'Juni',
                                        7 => 'Juli',
                                        8 => 'Agustus',
                                        9 => 'September',
                                        10 => 'Oktober',
                                        11 => 'November',
                                        12 => 'Desember'
                                    ])
                                    ->required(),

                                Forms\Components\Select::make('minggu')
                                    ->options([
                                        1 => 'Minggu 1',
                                        2 => 'Minggu 2',
                                        3 => 'Minggu 3',
                                        4 => 'Minggu 4',
                                        5 => 'Minggu 5'
                                    ])
                                    ->required(),
                            ]),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Statistik Penjualan')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('total_penjualan')
                                    ->label('Total Penjualan (Unit)')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('unit'),

                                Forms\Components\TextInput::make('total_nilai_penjualan')
                                    ->label('Total Nilai Penjualan')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('rata_rata_penjualan')
                                    ->label('Rata-rata Penjualan')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0),

                                Forms\Components\TextInput::make('penjualan_tunai')
                                    ->label('Penjualan Tunai')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('unit'),

                                Forms\Components\TextInput::make('penjualan_kredit')
                                    ->label('Penjualan Kredit')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('unit'),
                            ]),
                    ]),

                Forms\Components\Section::make('Top Performers')
                    ->schema([
                        Forms\Components\Repeater::make('top_merek')
                            ->label('Top Merek')
                            ->schema([
                                Forms\Components\TextInput::make('nama')
                                    ->required(),
                                Forms\Components\TextInput::make('jumlah')
                                    ->numeric()
                                    ->required(),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->defaultItems(0),

                        Forms\Components\Repeater::make('top_model')
                            ->label('Top Model')
                            ->schema([
                                Forms\Components\TextInput::make('nama')
                                    ->required(),
                                Forms\Components\TextInput::make('jumlah')
                                    ->numeric()
                                    ->required(),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->defaultItems(0),

                        Forms\Components\Repeater::make('top_kategori')
                            ->label('Top Kategori')
                            ->schema([
                                Forms\Components\TextInput::make('nama')
                                    ->required(),
                                Forms\Components\TextInput::make('jumlah')
                                    ->numeric()
                                    ->required(),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->defaultItems(0),

                        Forms\Components\Repeater::make('top_sales')
                            ->label('Top Sales')
                            ->schema([
                                Forms\Components\TextInput::make('nama')
                                    ->required(),
                                Forms\Components\TextInput::make('jumlah')
                                    ->numeric()
                                    ->required(),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->defaultItems(0),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('nama_bulan')
                    ->label('Bulan')
                    ->sortable(['bulan']),

                Tables\Columns\TextColumn::make('tahun')
                    ->sortable(),

                Tables\Columns\TextColumn::make('minggu')
                    ->label('Minggu Ke')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_penjualan')
                    ->label('Total Unit')
                    ->numeric()
                    ->suffix(' unit')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_nilai_penjualan')
                    ->label('Total Nilai')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('rata_rata_penjualan')
                    ->label('Rata-rata')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('penjualan_tunai')
                    ->label('Tunai')
                    ->numeric()
                    ->suffix(' unit')
                    ->sortable(),

                Tables\Columns\TextColumn::make('penjualan_kredit')
                    ->label('Kredit')
                    ->numeric()
                    ->suffix(' unit')
                    ->sortable(),

                Tables\Columns\TextColumn::make('persentase_tunai')
                    ->label('% Tunai')
                    ->suffix('%')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tahun')
                    ->options(function () {
                        $years = [];
                        for ($i = 2020; $i <= 2030; $i++) {
                            $years[$i] = $i;
                        }
                        return $years;
                    }),

                SelectFilter::make('bulan')
                    ->options([
                        1 => 'Januari',
                        2 => 'Februari',
                        3 => 'Maret',
                        4 => 'April',
                        5 => 'Mei',
                        6 => 'Juni',
                        7 => 'Juli',
                        8 => 'Agustus',
                        9 => 'September',
                        10 => 'Oktober',
                        11 => 'November',
                        12 => 'Desember'
                    ]),

                Filter::make('periode')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

                Action::make('generate_laporan')
                    ->label('Generate Ulang')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->action(function ($record) {
                        // Logika untuk generate ulang laporan
                        LaporanPenjualan::generateLaporan($record->tanggal);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Generate Ulang Laporan')
                    ->modalDescription('Apakah Anda yakin ingin generate ulang laporan untuk tanggal ini?'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Informasi Periode')
                    ->schema([
                        Components\Grid::make(4)
                            ->schema([
                                Components\TextEntry::make('tanggal')
                                    ->date('d F Y'),
                                Components\TextEntry::make('nama_bulan')
                                    ->label('Bulan'),
                                Components\TextEntry::make('tahun'),
                                Components\TextEntry::make('minggu')
                                    ->label('Minggu Ke'),
                            ]),
                    ]),

                Components\Section::make('Statistik Penjualan')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('total_penjualan')
                                    ->label('Total Penjualan')
                                    ->suffix(' unit')
                                    ->weight(FontWeight::Bold)
                                    ->color('primary'),
                                Components\TextEntry::make('total_nilai_penjualan')
                                    ->label('Total Nilai')
                                    ->money('IDR')
                                    ->weight(FontWeight::Bold)
                                    ->color('success'),
                                Components\TextEntry::make('rata_rata_penjualan')
                                    ->label('Rata-rata')
                                    ->money('IDR'),
                            ]),

                        Components\Grid::make(4)
                            ->schema([
                                Components\TextEntry::make('penjualan_tunai')
                                    ->label('Penjualan Tunai')
                                    ->suffix(' unit'),
                                Components\TextEntry::make('penjualan_kredit')
                                    ->label('Penjualan Kredit')
                                    ->suffix(' unit'),
                                Components\TextEntry::make('persentase_tunai')
                                    ->label('% Tunai')
                                    ->suffix('%'),
                                Components\TextEntry::make('persentase_kredit')
                                    ->label('% Kredit')
                                    ->suffix('%'),
                            ]),
                    ]),

                Components\Section::make('Top Performers')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('top_merek_formatted')
                                    ->label('Top Merek')
                                    ->html(),
                                Components\TextEntry::make('top_model_formatted')
                                    ->label('Top Model')
                                    ->html(),
                            ]),

                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('top_kategori_formatted')
                                    ->label('Top Kategori')
                                    ->html(),
                                Components\TextEntry::make('top_sales_formatted')
                                    ->label('Top Sales')
                                    ->html(),
                            ]),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanPenjualans::route('/'),
            'create' => Pages\CreateLaporanPenjualan::route('/create'),
            'view' => Pages\ViewLaporanPenjualan::route('/{record}'),
            'edit' => Pages\EditLaporanPenjualan::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}