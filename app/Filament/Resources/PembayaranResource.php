<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembayaranResource\Pages;
use App\Models\Pembayaran;
use App\Models\Penjualan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Carbon\Carbon;

class PembayaranResource extends Resource
{
    protected static ?string $model = Pembayaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Pembayaran';

    protected static ?string $modelLabel = 'Pembayaran';

    protected static ?string $pluralModelLabel = 'Pembayaran';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $recordTitleAttribute = 'no_kwitansi';

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'no_kwitansi',
            'keterangan',
            'no_referensi',
            'penjualan.no_faktur',
            'penjualan.pelanggan.nama_lengkap',
        ];
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Pembayaran')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('penjualan_id')
                                    ->label('Penjualan')
                                    ->relationship('penjualan', 'no_faktur')
                                    ->getOptionLabelFromRecordUsing(fn(Penjualan $record): string => "{$record->no_faktur} - {$record->pelanggan->nama_lengkap}")
                                    ->searchable(['no_faktur'])
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $penjualan = Penjualan::find($state);
                                            if ($penjualan) {
                                                $totalBayar = $penjualan->pembayarans()->sum('jumlah');
                                                $sisaBayar = $penjualan->total - $totalBayar;
                                                $set('jumlah', $sisaBayar > 0 ? $sisaBayar : 0);
                                            }
                                        }
                                    }),

                                Forms\Components\TextInput::make('no_kwitansi')
                                    ->label('No. Kwitansi')
                                    ->placeholder('Auto generate jika kosong')
                                    ->maxLength(255),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('jumlah')
                                    ->label('Jumlah Pembayaran')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->step(0.01)
                                    ->minValue(0),

                                Forms\Components\Select::make('jenis')
                                    ->label('Jenis Pembayaran')
                                    ->options(Pembayaran::JENIS)
                                    ->required()
                                    ->reactive(),

                                Forms\Components\DatePicker::make('tanggal_bayar')
                                    ->label('Tanggal Bayar')
                                    ->required()
                                    ->default(now()),
                            ]),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Metode Pembayaran')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('metode')
                                    ->label('Metode Pembayaran')
                                    ->options(Pembayaran::METODE)
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if (!in_array($state, ['transfer', 'debit', 'kredit'])) {
                                            $set('bank', null);
                                        }
                                    }),

                                Forms\Components\Select::make('bank')
                                    ->label('Bank')
                                    ->options(Pembayaran::BANKS)
                                    ->visible(fn(callable $get) => in_array($get('metode'), ['transfer', 'debit', 'kredit']))
                                    ->required(fn(callable $get) => in_array($get('metode'), ['transfer', 'debit', 'kredit'])),

                                Forms\Components\TextInput::make('no_referensi')
                                    ->label('No. Referensi')
                                    ->maxLength(255)
                                    ->visible(fn(callable $get) => in_array($get('metode'), ['transfer', 'debit', 'kredit', 'ewallet'])),
                            ]),
                    ]),

                Section::make('Bukti & Catatan')
                    ->schema([
                        Forms\Components\FileUpload::make('bukti_bayar')
                            ->label('Bukti Pembayaran')
                            ->image()
                            ->imageEditor()
                            ->directory('bukti-pembayaran')
                            ->visibility('private')
                            ->downloadable(),

                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan')
                            ->rows(3),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_kwitansi')
                    ->label('No. Kwitansi')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('penjualan.no_faktur')
                    ->label('No. Faktur')
                    ->searchable()
                    ->sortable()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('penjualan.pelanggan.nama_lengkap')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->formatStateUsing(fn(string $state): string => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable()
                    ->alignEnd()
                    ->weight('bold'),

                Tables\Columns\BadgeColumn::make('jenis')
                    ->label('Jenis')
                    ->formatStateUsing(fn(string $state): string => Pembayaran::JENIS[$state] ?? $state)
                    ->colors([
                        'warning' => 'dp',
                        'info' => 'cicilan',
                        'success' => 'pelunasan',
                        'gray' => 'tambahan',
                    ]),

                Tables\Columns\BadgeColumn::make('metode')
                    ->label('Metode')
                    ->formatStateUsing(fn(string $state): string => Pembayaran::METODE[$state] ?? $state)
                    ->colors([
                        'success' => 'tunai',
                        'info' => 'transfer',
                        'primary' => 'debit',
                        'warning' => 'kredit',
                        'purple' => 'ewallet',
                        'gray' => 'cek',
                    ]),

                Tables\Columns\TextColumn::make('bank')
                    ->label('Bank')
                    ->formatStateUsing(fn(?string $state): string => $state ? (Pembayaran::BANKS[$state] ?? $state) : '-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('tanggal_bayar')
                    ->label('Tanggal Bayar')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\IconColumn::make('bukti_bayar')
                    ->label('Bukti')
                    ->boolean()
                    ->trueIcon('heroicon-o-document-text')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('jenis')
                    ->label('Jenis Pembayaran')
                    ->options(Pembayaran::JENIS)
                    ->multiple(),

                SelectFilter::make('metode')
                    ->label('Metode Pembayaran')
                    ->options(Pembayaran::METODE)
                    ->multiple(),

                SelectFilter::make('bank')
                    ->label('Bank')
                    ->options(Pembayaran::BANKS)
                    ->multiple(),

                Filter::make('tanggal_bayar')
                    ->form([
                        DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_bayar', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_bayar', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['dari_tanggal'] ?? null) {
                            $indicators['dari_tanggal'] = 'Dari: ' . Carbon::parse($data['dari_tanggal'])->format('d/m/Y');
                        }
                        if ($data['sampai_tanggal'] ?? null) {
                            $indicators['sampai_tanggal'] = 'Sampai: ' . Carbon::parse($data['sampai_tanggal'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([

                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('60s');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Pembayaran')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('no_kwitansi')
                                    ->label('No. Kwitansi')
                                    ->copyable(),

                                Infolists\Components\TextEntry::make('penjualan.no_faktur')
                                    ->label('No. Faktur')
                                    ->color('primary'),

                                Infolists\Components\TextEntry::make('penjualan.pelanggan.nama_lengkap')
                                    ->label('Pelanggan'),

                                Infolists\Components\TextEntry::make('jumlah')
                                    ->label('Jumlah Pembayaran')
                                    ->formatStateUsing(fn(string $state): string => 'Rp ' . number_format($state, 0, ',', '.'))
                                    ->weight('bold')
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('jenis')
                                    ->label('Jenis Pembayaran')
                                    ->formatStateUsing(fn(string $state): string => Pembayaran::JENIS[$state] ?? $state)
                                    ->badge()
                                    ->color(fn(Pembayaran $record): string => $record->jenis_badge_color),

                                Infolists\Components\TextEntry::make('tanggal_bayar')
                                    ->label('Tanggal Bayar')
                                    ->date('d/m/Y'),
                            ]),

                        Infolists\Components\TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->columnSpanFull()
                            ->placeholder('Tidak ada keterangan'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Metode Pembayaran')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('metode')
                                    ->label('Metode Pembayaran')
                                    ->formatStateUsing(fn(string $state): string => Pembayaran::METODE[$state] ?? $state)
                                    ->badge()
                                    ->color(fn(Pembayaran $record): string => $record->metode_badge_color),

                                Infolists\Components\TextEntry::make('bank')
                                    ->label('Bank')
                                    ->formatStateUsing(fn(?string $state): string => $state ? (Pembayaran::BANKS[$state] ?? $state) : '-')
                                    ->visible(fn(Pembayaran $record): bool => !empty($record->bank)),

                                Infolists\Components\TextEntry::make('no_referensi')
                                    ->label('No. Referensi')
                                    ->placeholder('Tidak ada referensi')
                                    ->copyable()
                                    ->visible(fn(Pembayaran $record): bool => !empty($record->no_referensi)),
                            ]),
                    ]),

                Infolists\Components\Section::make('Bukti & Catatan')
                    ->schema([
                        Infolists\Components\ImageEntry::make('bukti_bayar')
                            ->label('Bukti Pembayaran')
                            ->visible(fn(Pembayaran $record): bool => !empty($record->bukti_bayar))
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('catatan')
                            ->label('Catatan')
                            ->placeholder('Tidak ada catatan')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn(Pembayaran $record): bool => !empty($record->bukti_bayar) || !empty($record->catatan)),

                Infolists\Components\Section::make('Informasi Sistem')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Dibuat Pada')
                                    ->dateTime('d/m/Y H:i:s'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Diperbarui Pada')
                                    ->dateTime('d/m/Y H:i:s'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
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
            'index' => Pages\ListPembayarans::route('/'),
            'create' => Pages\CreatePembayaran::route('/create'),
            'view' => Pages\ViewPembayaran::route('/{record}'),
            'edit' => Pages\EditPembayaran::route('/{record}/edit'),
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
        return static::getModel()::count();
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['penjualan.pelanggan']);
    }


    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Pelanggan' => $record->penjualan->pelanggan->nama_lengkap ?? '-',
            'Jumlah' => 'Rp ' . number_format($record->jumlah, 0, ',', '.'),
            'Tanggal' => $record->tanggal_bayar->format('d/m/Y'),
        ];
    }
}