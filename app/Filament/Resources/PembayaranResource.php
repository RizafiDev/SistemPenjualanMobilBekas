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
                // Header Section dengan styling yang menarik
                Infolists\Components\Section::make('Ringkasan Pembayaran')
                    ->schema([
                        Infolists\Components\Grid::make(12)
                            ->schema([
                                // Kartu utama dengan jumlah pembayaran
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('jumlah')
                                        ->label('Total Pembayaran')
                                        ->formatStateUsing(fn(string $state): string => 'Rp ' . number_format($state, 0, ',', '.'))
                                        ->weight('bold')
                                        ->size('xl')
                                        ->color('success'),

                                    Infolists\Components\TextEntry::make('jenis')
                                        ->label('Status')
                                        ->formatStateUsing(fn(string $state): string => Pembayaran::JENIS[$state] ?? $state)
                                        ->badge()
                                        ->size('lg')
                                        ->color(fn(Pembayaran $record): string => $record->jenis_badge_color),
                                ])
                                    ->columnSpan(4),

                                // Info dasar
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('no_kwitansi')
                                        ->label('No. Kwitansi')
                                        ->copyable()
                                        ->icon('heroicon-o-document-text')
                                        ->iconColor('primary'),

                                    Infolists\Components\TextEntry::make('penjualan.no_faktur')
                                        ->label('No. Faktur')
                                        ->color('primary')
                                        ->icon('heroicon-o-receipt-percent')
                                        ->copyable(),

                                    Infolists\Components\TextEntry::make('tanggal_bayar')
                                        ->label('Tanggal Bayar')
                                        ->date('d F Y')
                                        ->icon('heroicon-o-calendar-days')
                                        ->iconColor('warning'),
                                ])
                                    ->columnSpan(4),

                                // Info pelanggan
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('penjualan.pelanggan.nama_lengkap')
                                        ->label('Pelanggan')
                                        ->icon('heroicon-o-user')
                                        ->iconColor('info')
                                        ->weight('semibold'),

                                    Infolists\Components\TextEntry::make('penjualan.pelanggan.telepon')
                                        ->label('Telepon')
                                        ->icon('heroicon-o-phone')
                                        ->visible(fn(Pembayaran $record): bool => !empty($record->penjualan?->pelanggan?->telepon)),

                                    Infolists\Components\TextEntry::make('penjualan.pelanggan.email')
                                        ->label('Email')
                                        ->icon('heroicon-o-envelope')
                                        ->visible(fn(Pembayaran $record): bool => !empty($record->penjualan?->pelanggan?->email)),
                                ])
                                    ->columnSpan(4),
                            ]),

                        // Separator untuk keterangan
                        Infolists\Components\TextEntry::make('keterangan')
                            ->label('Keterangan Pembayaran')
                            ->columnSpanFull()
                            ->placeholder('Tidak ada keterangan khusus')
                            ->icon('heroicon-o-chat-bubble-left-ellipsis')
                            ->visible(fn(Pembayaran $record): bool => !empty($record->keterangan)),
                    ])
                    ->icon('heroicon-o-banknotes')
                    ->iconColor('success'),

                // Detail Metode Pembayaran
                Infolists\Components\Section::make('Detail Metode Pembayaran')
                    ->schema([
                        Infolists\Components\Grid::make(12)
                            ->schema([
                                Infolists\Components\TextEntry::make('metode')
                                    ->label('Metode Pembayaran')
                                    ->formatStateUsing(fn(string $state): string => Pembayaran::METODE[$state] ?? $state)
                                    ->badge()
                                    ->size('lg')
                                    ->color(fn(Pembayaran $record): string => $record->metode_badge_color)
                                    ->columnSpan(4),

                                Infolists\Components\TextEntry::make('bank')
                                    ->label('Bank/Penerbit')
                                    ->formatStateUsing(fn(?string $state): string => $state ? (Pembayaran::BANKS[$state] ?? $state) : 'Tidak ada')
                                    ->icon('heroicon-o-building-library')
                                    ->iconColor('primary')
                                    ->visible(fn(Pembayaran $record): bool => !empty($record->bank))
                                    ->columnSpan(4),

                                Infolists\Components\TextEntry::make('no_referensi')
                                    ->label('No. Referensi/Transaksi')
                                    ->placeholder('Tidak tersedia')
                                    ->copyable()
                                    ->icon('heroicon-o-hashtag')
                                    ->iconColor('warning')
                                    ->visible(fn(Pembayaran $record): bool => !empty($record->no_referensi))
                                    ->columnSpan(4),
                            ]),

                        // Info tambahan untuk transfer/kartu kredit
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('nama_pengirim')
                                    ->label('Nama Pengirim')
                                    ->icon('heroicon-o-user-circle')
                                    ->visible(fn(Pembayaran $record): bool => !empty($record->nama_pengirim)),

                                Infolists\Components\TextEntry::make('rekening_pengirim')
                                    ->label('No. Rekening Pengirim')
                                    ->icon('heroicon-o-credit-card')
                                    ->copyable()
                                    ->visible(fn(Pembayaran $record): bool => !empty($record->rekening_pengirim)),
                            ])
                            ->visible(fn(Pembayaran $record): bool => !empty($record->nama_pengirim) || !empty($record->rekening_pengirim)),
                    ])
                    ->icon('heroicon-o-credit-card')
                    ->iconColor('primary'),

                // Bukti & Dokumentasi
                Infolists\Components\Section::make('Bukti & Dokumentasi')
                    ->schema([
                        Infolists\Components\ImageEntry::make('bukti_bayar')
                            ->label('Bukti Pembayaran')
                            ->disk('public')
                            ->height(300)
                            ->width(400)
                            ->extraAttributes(['class' => 'rounded-lg shadow-md'])
                            ->visible(fn(Pembayaran $record): bool => !empty($record->bukti_bayar))
                            ->columnSpan(6),

                        Infolists\Components\Group::make([
                            Infolists\Components\TextEntry::make('catatan')
                                ->label('Catatan Internal')
                                ->placeholder('Tidak ada catatan tambahan')
                                ->columnSpanFull()
                                ->icon('heroicon-o-document-text'),

                            Infolists\Components\TextEntry::make('verified_by')
                                ->label('Diverifikasi Oleh')
                                ->icon('heroicon-o-shield-check')
                                ->iconColor('success')
                                ->visible(fn(Pembayaran $record): bool => !empty($record->verified_by)),

                            Infolists\Components\TextEntry::make('verified_at')
                                ->label('Waktu Verifikasi')
                                ->dateTime('d F Y, H:i')
                                ->icon('heroicon-o-clock')
                                ->visible(fn(Pembayaran $record): bool => !empty($record->verified_at)),
                        ])
                            ->columnSpan(6),
                    ])
                    ->collapsible()
                    ->persistCollapsed()
                    ->visible(
                        fn(Pembayaran $record): bool =>
                        !empty($record->bukti_bayar) ||
                        !empty($record->catatan) ||
                        !empty($record->verified_by)
                    )
                    ->icon('heroicon-o-camera')
                    ->iconColor('info'),

                // Riwayat & Audit Trail
                Infolists\Components\Section::make('Riwayat & Informasi Sistem')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Dibuat Pada')
                                    ->dateTime('d F Y, H:i:s')
                                    ->icon('heroicon-o-plus-circle')
                                    ->iconColor('success'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Terakhir Diubah')
                                    ->dateTime('d F Y, H:i:s')
                                    ->icon('heroicon-o-pencil-square')
                                    ->iconColor('warning'),

                                Infolists\Components\TextEntry::make('created_by')
                                    ->label('Dibuat Oleh')
                                    ->formatStateUsing(fn(?string $state): string => $state ?? 'Sistem')
                                    ->icon('heroicon-o-user')
                                    ->visible(fn(Pembayaran $record): bool => !empty($record->created_by)),
                            ]),

                        // Status tracking
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('status_history')
                                    ->label('Riwayat Status')
                                    ->listWithLineBreaks()
                                    ->bulleted()
                                    ->visible(fn(Pembayaran $record): bool => !empty($record->status_history)),

                                Infolists\Components\TextEntry::make('payment_gateway_response')
                                    ->label('Response Gateway')
                                    ->formatStateUsing(fn(?string $state): string => $state ? 'Berhasil' : 'Manual')
                                    ->badge()
                                    ->color(fn(?string $state): string => $state ? 'success' : 'gray')
                                    ->visible(fn(Pembayaran $record): bool => isset($record->payment_gateway_response)),
                            ])
                            ->visible(
                                fn(Pembayaran $record): bool =>
                                !empty($record->status_history) ||
                                isset($record->payment_gateway_response)
                            ),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->persistCollapsed()
                    ->icon('heroicon-o-clock')
                    ->iconColor('gray'),

                // Action buttons atau info tambahan
                Infolists\Components\Section::make('')
                    ->schema([
                        Infolists\Components\Actions::make([
                            Infolists\Components\Actions\Action::make('print')
                                ->label('Cetak Kwitansi')
                                ->icon('heroicon-o-printer')
                                ->color('primary')
                                ->url(fn(Pembayaran $record): string => route('pembayaran.print', $record))
                                ->openUrlInNewTab(),

                            Infolists\Components\Actions\Action::make('download')
                                ->label('Download PDF')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->color('success')
                                ->url(fn(Pembayaran $record): string => route('pembayaran.pdf', $record)),

                            Infolists\Components\Actions\Action::make('email')
                                ->label('Kirim Email')
                                ->icon('heroicon-o-envelope')
                                ->color('info')
                                ->visible(fn(Pembayaran $record): bool => !empty($record->penjualan?->pelanggan?->email)),
                        ])
                    ])
                    ->hiddenLabel(),
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