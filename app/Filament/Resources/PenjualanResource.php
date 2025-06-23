<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenjualanResource\Pages;
use App\Models\Penjualan;
use App\Models\StokMobil;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\PenjualanResource\RelationManagers\PembayaransRelationManager;

class PenjualanResource extends Resource
{
    protected static ?string $model = Penjualan::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Penjualan';

    protected static ?string $pluralModelLabel = 'Penjualan';

    protected static ?string $modelLabel = 'Penjualan';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 1;

    public static ?string $recordTitleAttribute = 'no_faktur';

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'no_faktur',
            'pelanggan.nama_lengkap',
            'stokMobil.mobil.nama',
            'karyawan.nama_lengkap',
            'metode_pembayaran',
            'status',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Faktur')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('no_faktur')
                                    ->label('No. Faktur')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->default(fn() => (new Penjualan())->generateNoFaktur())
                                    ->disabled(fn(?Penjualan $record) => $record?->exists),

                                Forms\Components\DatePicker::make('tanggal_penjualan')
                                    ->label('Tanggal Penjualan')
                                    ->required()
                                    ->default(now()),
                            ]),
                    ]),

                Forms\Components\Section::make('Data Transaksi')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('stok_mobil_id')
                                    ->label('Mobil')
                                    ->options(function () {
                                        return StokMobil::with(['mobil.merek', 'varian'])
                                            ->where('status', 'tersedia')
                                            ->get()
                                            ->mapWithKeys(function ($stok) {
                                                $mobil = $stok->mobil;
                                                $varian = $stok->varian;
                                                $merek = $mobil?->merek?->nama ?? '';
                                                $namaMobil = $mobil?->nama ?? '';
                                                $varianNama = $varian?->nama ? ' ' . $varian->nama : '';
                                                $warna = $stok->warna ? ' - ' . $stok->warna : '';
                                                $label = trim("{$merek} {$namaMobil}{$varianNama}{$warna}");
                                                return [$stok->id => $label];
                                            });
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if ($state) {
                                            $stokMobil = \App\Models\StokMobil::find($state);
                                            if ($stokMobil) {
                                                $set('harga_jual', $stokMobil->harga_jual);
                                            }
                                        }
                                    }),

                                Forms\Components\Select::make('pelanggan_id')
                                    ->label('Pelanggan')
                                    ->relationship('pelanggan', 'nama_lengkap')
                                    ->searchable(['nama_lengkap', 'no_telepon', 'email'])
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('nama_lengkap')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('nik')
                                            ->label('NIK')
                                            ->maxLength(20),
                                        Forms\Components\TextInput::make('no_telepon')
                                            ->tel()
                                            ->maxLength(20),
                                        Forms\Components\TextInput::make('email')
                                            ->email()
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('alamat')
                                            ->rows(3),
                                    ])
                                    ->createOptionAction(function (Action $action) {
                                        return $action->modalHeading('Tambah Pelanggan Baru');
                                    }),

                                Forms\Components\Select::make('karyawan_id')
                                    ->label('Sales')
                                    ->relationship('karyawan', 'nama_lengkap')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Pilih Sales'),

                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options(Penjualan::STATUS)
                                    ->default('draft')
                                    ->required()
                                    ->live(),
                            ]),
                    ]),

                Forms\Components\Section::make('Detail Harga')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('harga_jual')
                                    ->label('Harga Jual')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($state, Forms\Set $set, Forms\Get $get) =>
                                        self::calculateTotal($get, $set)),

                                Forms\Components\TextInput::make('diskon')
                                    ->label('Diskon')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($state, Forms\Set $set, Forms\Get $get) =>
                                        self::calculateTotal($get, $set)),

                                Forms\Components\TextInput::make('biaya_tambahan')
                                    ->label('Biaya Tambahan')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($state, Forms\Set $set, Forms\Get $get) =>
                                        self::calculateTotal($get, $set)),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('ppn')
                                    ->label('PPN (11%)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($state, Forms\Set $set, Forms\Get $get) =>
                                        self::calculateTotal($get, $set))
                                    ->suffixAction(
                                        Forms\Components\Actions\Action::make('calculatePpn')
                                            ->icon('heroicon-m-calculator')
                                            ->action(function (Forms\Set $set, Forms\Get $get) {
                                                $hargaJual = $get('harga_jual') ?? 0;
                                                $diskon = $get('diskon') ?? 0;
                                                $subtotal = $hargaJual - $diskon;
                                                $ppn = $subtotal * 0.11;
                                                $set('ppn', $ppn);
                                                self::calculateTotal($get, $set);
                                            })
                                    ),

                                Forms\Components\TextInput::make('total')
                                    ->label('Total')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->readOnly()
                                    ->extraAttributes(['class' => 'font-bold text-primary-600']),
                            ]),
                    ]),

                Forms\Components\Section::make('Metode Pembayaran')
                    ->schema([
                        Forms\Components\Select::make('metode_pembayaran')
                            ->label('Metode Pembayaran')
                            ->options(Penjualan::METODE_PEMBAYARAN)
                            ->required()
                            ->live(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('leasing_bank')
                                    ->label('Bank Leasing')
                                    ->options(Penjualan::LEASING_BANKS)
                                    ->visible(fn(Forms\Get $get) => in_array($get('metode_pembayaran'), ['leasing', 'kredit'])),

                                Forms\Components\TextInput::make('tenor_bulan')
                                    ->label('Tenor (Bulan)')
                                    ->numeric()
                                    ->visible(fn(Forms\Get $get) => in_array($get('metode_pembayaran'), ['leasing', 'kredit'])),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('uang_muka')
                                    ->label('Uang Muka')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->visible(fn(Forms\Get $get) => in_array($get('metode_pembayaran'), ['leasing', 'kredit']))
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        $total = $get('total') ?? 0;
                                        $uangMuka = $state ?? 0;
                                        $tenor = $get('tenor_bulan') ?? 1;
                                        if ($tenor > 0) {
                                            $sisaBayar = $total - $uangMuka;
                                            $cicilan = $sisaBayar / $tenor;
                                            $set('cicilan_bulanan', $cicilan);
                                        }
                                    }),

                                Forms\Components\TextInput::make('cicilan_bulanan')
                                    ->label('Cicilan Bulanan')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->visible(fn(Forms\Get $get) => in_array($get('metode_pembayaran'), ['leasing', 'kredit']))
                                    ->readOnly(),
                            ]),
                    ]),

                Forms\Components\Section::make('Trade In')
                    ->schema([
                        Forms\Components\Repeater::make('trade_in')
                            ->label('Data Trade In')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('merk')
                                            ->required(),
                                        Forms\Components\TextInput::make('model')
                                            ->required(),
                                        Forms\Components\TextInput::make('tahun')
                                            ->numeric()
                                            ->required(),
                                    ]),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('no_rangka')
                                            ->label('No. Rangka'),
                                        Forms\Components\TextInput::make('kondisi')
                                            ->required(),
                                    ]),
                                Forms\Components\TextInput::make('nilai_trade_in')
                                    ->label('Nilai Trade In')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required(),
                                Forms\Components\Textarea::make('catatan_trade_in')
                                    ->label('Catatan Trade In')
                                    ->rows(2),
                            ])
                            ->visible(fn(Forms\Get $get) => $get('metode_pembayaran') === 'trade_in')
                            ->collapsed()
                            ->itemLabel(
                                fn(array $state): ?string =>
                                isset($state['merk'], $state['model']) ?
                                "{$state['merk']} {$state['model']}" : null
                            ),
                    ]),

                Forms\Components\Section::make('Dokumen & Catatan')
                    ->schema([
                        Forms\Components\FileUpload::make('dokumen')
                            ->label('Dokumen')
                            ->multiple()
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->helperText('Upload KTP, KK, dan dokumen lainnya'),

                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan')
                            ->rows(3),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_faktur')
                    ->label('No. Faktur')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('tanggal_penjualan')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('pelanggan.nama_lengkap')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                // FIXED: Menggunakan format yang benar dengan nested relationship
                Tables\Columns\TextColumn::make('stokMobil.nama_lengkap_penjualan')
                    ->label('Mobil')
                    ->searchable()
                    ->wrap()
                    ->limit(30),

                Tables\Columns\TextColumn::make('karyawan.nama_lengkap')
                    ->label('Sales')
                    ->searchable()
                    ->placeholder('-')
                    ->wrap(),

                Tables\Columns\TextColumn::make('metode_pembayaran')
                    ->label('Pembayaran')
                    ->badge()
                    ->formatStateUsing(fn($state) => Penjualan::METODE_PEMBAYARAN[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'tunai' => 'success',
                        'kredit' => 'warning',
                        'leasing' => 'info',
                        'trade_in' => 'gray',
                        default => 'gray'
                    }),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn($state) => Penjualan::STATUS[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'draft' => 'gray',
                        'booking' => 'warning',
                        'lunas' => 'success',
                        'kredit' => 'info',
                        'batal' => 'danger',
                        default => 'gray'
                    }),
            ])
            ->filters([

                Tables\Filters\SelectFilter::make('status')
                    ->options(Penjualan::STATUS)
                    ->multiple(),

                Tables\Filters\SelectFilter::make('metode_pembayaran')
                    ->label('Metode Pembayaran')
                    ->options(Penjualan::METODE_PEMBAYARAN)
                    ->multiple(),

                Tables\Filters\Filter::make('tanggal_penjualan')
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
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_penjualan', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_penjualan', '<=', $date),
                            );
                    }),

                Tables\Filters\SelectFilter::make('karyawan_id')
                    ->label('Sales')
                    ->relationship('karyawan', 'nama_lengkap')
                    ->searchable()
                    ->multiple(),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\RestoreAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('print_invoice')
                        ->label('Print')
                        ->icon('heroicon-o-printer')
                        ->color('info')
                        ->url(fn(Penjualan $record): string => route('penjualan.print', $record))
                        ->openUrlInNewTab(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal_penjualan', 'desc')
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Informasi Faktur')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('no_faktur')
                                    ->label('No. Faktur')
                                    ->weight(FontWeight::Bold),

                                Components\TextEntry::make('tanggal_penjualan')
                                    ->label('Tanggal Penjualan')
                                    ->date('d F Y'),
                            ]),
                    ]),

                Components\Section::make('Data Transaksi')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                // FIXED: Menggunakan accessor yang benar
                                Components\TextEntry::make('stokMobil.nama_lengkap_penjualan')
                                    ->label('Mobil'),

                                Components\TextEntry::make('pelanggan.nama_lengkap')
                                    ->label('Pelanggan'),

                                Components\TextEntry::make('karyawan.nama_lengkap')
                                    ->label('Sales')
                                    ->placeholder('-'),

                                Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->formatStateUsing(fn($state) => Penjualan::STATUS[$state] ?? $state)
                                    ->color(fn($state) => match ($state) {
                                        'draft' => 'gray',
                                        'booking' => 'warning',
                                        'lunas' => 'success',
                                        'kredit' => 'info',
                                        'batal' => 'danger',
                                        default => 'gray'
                                    }),
                            ]),
                    ]),

                Components\Section::make('Detail Harga')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('harga_jual')
                                    ->label('Harga Jual')
                                    ->money('IDR'),

                                Components\TextEntry::make('diskon')
                                    ->label('Diskon')
                                    ->money('IDR'),

                                Components\TextEntry::make('ppn')
                                    ->label('PPN')
                                    ->money('IDR'),

                                Components\TextEntry::make('biaya_tambahan')
                                    ->label('Biaya Tambahan')
                                    ->money('IDR'),

                                Components\TextEntry::make('total')
                                    ->label('Total')
                                    ->money('IDR')
                                    ->weight(FontWeight::Bold)
                                    ->color('primary'),
                            ]),
                    ]),

                Components\Section::make('Metode Pembayaran')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('metode_pembayaran')
                                    ->label('Metode Pembayaran')
                                    ->badge()
                                    ->formatStateUsing(fn($state) => Penjualan::METODE_PEMBAYARAN[$state] ?? $state),

                                Components\TextEntry::make('leasing_bank')
                                    ->label('Bank Leasing')
                                    ->formatStateUsing(fn($state) => $state ? (Penjualan::LEASING_BANKS[$state] ?? $state) : '-')
                                    ->placeholder('-'),

                                Components\TextEntry::make('tenor_bulan')
                                    ->label('Tenor')
                                    ->suffix(' bulan')
                                    ->placeholder('-'),

                                Components\TextEntry::make('uang_muka')
                                    ->label('Uang Muka')
                                    ->money('IDR')
                                    ->placeholder('-'),

                                Components\TextEntry::make('cicilan_bulanan')
                                    ->label('Cicilan Bulanan')
                                    ->money('IDR')
                                    ->placeholder('-'),
                            ]),
                    ]),

                Components\Section::make('Trade In')
                    ->schema([
                        Components\RepeatableEntry::make('trade_in')
                            ->label('Data Trade In')
                            ->schema([
                                Components\Grid::make(3)
                                    ->schema([
                                        Components\TextEntry::make('merk')
                                            ->label('Merk'),
                                        Components\TextEntry::make('model')
                                            ->label('Model'),
                                        Components\TextEntry::make('tahun')
                                            ->label('Tahun'),
                                    ]),
                                Components\Grid::make(2)
                                    ->schema([
                                        Components\TextEntry::make('no_rangka')
                                            ->label('No. Rangka')
                                            ->placeholder('-'),
                                        Components\TextEntry::make('kondisi')
                                            ->label('Kondisi'),
                                    ]),
                                Components\TextEntry::make('nilai_trade_in')
                                    ->label('Nilai Trade In')
                                    ->money('IDR'),
                                Components\TextEntry::make('catatan_trade_in')
                                    ->label('Catatan')
                                    ->placeholder('-'),
                            ])
                            ->contained(false),
                    ])
                    ->visible(fn(Penjualan $record) => !empty($record->trade_in)),

                Components\Section::make('Informasi Tambahan')
                    ->schema([
                        Components\TextEntry::make('catatan')
                            ->label('Catatan')
                            ->placeholder('-')
                            ->html(),

                        Components\TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime('d F Y H:i'),

                        Components\TextEntry::make('updated_at')
                            ->label('Diperbarui')
                            ->dateTime('d F Y H:i'),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PembayaransRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenjualans::route('/'),
            'create' => Pages\CreatePenjualan::route('/create'),
            'view' => Pages\ViewPenjualan::route('/{record}'),
            'edit' => Pages\EditPenjualan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'stokMobil.mobil.merek',
                'stokMobil.varian',
                'pelanggan',
                'karyawan'
            ])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 10 ? 'warning' : 'primary';
    }

    // Helper method untuk menghitung total
    private static function calculateTotal(Forms\Get $get, Forms\Set $set): void
    {
        $hargaJual = $get('harga_jual') ?? 0;
        $diskon = $get('diskon') ?? 0;
        $ppn = $get('ppn') ?? 0;
        $biayaTambahan = $get('biaya_tambahan') ?? 0;

        $subtotal = $hargaJual - $diskon;
        $total = $subtotal + $ppn + $biayaTambahan;

        $set('total', $total);
    }

    // Widget untuk dashboard
    // public static function getWidgets(): array
    // {
    //     return [
    //         PenjualanResource\Widgets\PenjualanOverview::class,
    //         PenjualanResource\Widgets\PenjualanChart::class,
    //     ];
    // }
}