<?php

namespace App\Filament\Resources\PenjualanResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;

class PembayaransRelationManager extends RelationManager
{
    protected static string $relationship = 'pembayarans';
    protected static ?string $recordTitleAttribute = 'no_kwitansi';



    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                // Tambahkan placeholder untuk menampilkan informasi sisa pembayaran
                Forms\Components\Placeholder::make('info_pembayaran')
                    ->label('Informasi Pembayaran')
                    ->content(function ($livewire) {
                        $penjualan = $livewire->getOwnerRecord();
                        $totalPembayaran = $penjualan->pembayarans()->sum('jumlah');
                        $sisaPembayaran = $penjualan->total - $totalPembayaran;

                        return view('filament.components.info-pembayaran', [
                            'total_harga' => $penjualan->total, // Menggunakan field 'total'
                            'total_pembayaran' => $totalPembayaran,
                            'sisa_pembayaran' => $sisaPembayaran
                        ]);
                    }),

                Forms\Components\TextInput::make('no_kwitansi')
                    ->label('No. Kwitansi')
                    ->placeholder('Kosongkan untuk generate otomatis')
                    ->maxLength(255)
                    ->helperText('Akan di-generate otomatis jika dikosongkan (Format: KWTYYYYMMnnnn)'),

                Forms\Components\TextInput::make('jumlah')
                    ->label('Jumlah')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0)
                    ->live()
                    ->rules([
                        function ($livewire) {
                            return function (string $attribute, $value, \Closure $fail) use ($livewire) {
                                if (!is_numeric($value) || $value <= 0) {
                                    return;
                                }

                                $penjualan = $livewire->getOwnerRecord();
                                $totalPembayaran = $penjualan->pembayarans()->sum('jumlah');

                                // Jika sedang edit, kurangi nilai pembayaran yang sedang diedit
                                if (
                                    property_exists($livewire, 'mountedTableActionRecord') &&
                                    $livewire->mountedTableActionRecord &&
                                    is_object($livewire->mountedTableActionRecord) &&
                                    property_exists($livewire->mountedTableActionRecord, 'jumlah')
                                ) {
                                    $totalPembayaran -= $livewire->mountedTableActionRecord->jumlah;
                                }

                                $sisaPembayaran = $penjualan->total - $totalPembayaran;

                                if ($value > $sisaPembayaran) {
                                    $fail("Jumlah pembayaran tidak boleh melebihi sisa pembayaran: Rp " . number_format($sisaPembayaran, 0, ',', '.'));
                                }
                            };
                        },
                    ])
                    ->helperText(function ($livewire) {
                        $penjualan = $livewire->getOwnerRecord();
                        $totalPembayaran = $penjualan->pembayarans()->sum('jumlah');

                        // Jika sedang edit, kurangi nilai pembayaran yang sedang diedit
                        if (
                            property_exists($livewire, 'mountedTableActionRecord') &&
                            $livewire->mountedTableActionRecord &&
                            is_object($livewire->mountedTableActionRecord) &&
                            property_exists($livewire->mountedTableActionRecord, 'jumlah')
                        ) {
                            $totalPembayaran -= $livewire->mountedTableActionRecord->jumlah;
                        }

                        $sisaPembayaran = $penjualan->total - $totalPembayaran;
                        return "Maksimal: Rp " . number_format($sisaPembayaran, 0, ',', '.');
                    }),

                Forms\Components\Select::make('jenis')
                    ->label('Jenis Pembayaran')
                    ->required()
                    ->options(\App\Models\Pembayaran::JENIS),

                Forms\Components\Select::make('metode')
                    ->label('Metode Pembayaran')
                    ->required()
                    ->options(\App\Models\Pembayaran::METODE)
                    ->live(),

                Forms\Components\Select::make('bank')
                    ->label('Bank')
                    ->options(\App\Models\Pembayaran::BANKS)
                    ->visible(fn(Forms\Get $get) => in_array($get('metode'), ['transfer', 'debit', 'kredit']))
                    ->required(fn(Forms\Get $get) => in_array($get('metode'), ['transfer', 'debit', 'kredit'])),

                Forms\Components\TextInput::make('no_referensi')
                    ->label('No. Referensi')
                    ->maxLength(255)
                    ->visible(fn(Forms\Get $get) => in_array($get('metode'), ['transfer', 'debit', 'kredit']))
                    ->helperText('No. referensi transaksi dari bank/payment gateway'),

                Forms\Components\DatePicker::make('tanggal_bayar')
                    ->label('Tanggal Bayar')
                    ->required()
                    ->default(now())
                    ->native(false),

                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->maxLength(1000)
                    ->rows(3),

                Forms\Components\FileUpload::make('bukti_bayar')
                    ->label('Bukti Bayar')
                    ->image()
                    ->directory('pembayaran')
                    ->visibility('private')
                    ->maxSize(2048)
                    ->helperText('Upload bukti pembayaran (max 2MB)'),

                Forms\Components\Textarea::make('catatan')
                    ->label('Catatan Internal')
                    ->maxLength(1000)
                    ->rows(2)
                    ->helperText('Catatan internal untuk admin'),

                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('isi_sisa')
                        ->label('Bayar Sisa')
                        ->color('success')
                        ->action(function (Forms\Set $set, $livewire) {
                            $penjualan = $livewire->getOwnerRecord();
                            $totalPembayaran = $penjualan->pembayarans()->sum('jumlah');
                            $sisaPembayaran = $penjualan->total - $totalPembayaran;

                            if ($sisaPembayaran > 0) {
                                $set('jumlah', $sisaPembayaran);
                                $set('jenis', 'pelunasan');
                            }
                        })
                        ->visible(function ($livewire) {
                            $penjualan = $livewire->getOwnerRecord();
                            $totalPembayaran = $penjualan->pembayarans()->sum('jumlah');
                            return ($penjualan->total - $totalPembayaran) > 0;
                        }),
                ])
                    ->fullWidth(),
            ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->recordTitleAttribute('no_kwitansi')
            ->columns([
                Tables\Columns\TextColumn::make('no_kwitansi')
                    ->label('No. Kwitansi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('jenis')
                    ->label('Jenis')
                    ->formatStateUsing(fn($state) => \App\Models\Pembayaran::JENIS[$state] ?? $state)
                    ->colors([
                        'warning' => 'dp',
                        'info' => 'cicilan',
                        'success' => 'pelunasan',
                        'gray' => 'tambahan',
                    ]),

                Tables\Columns\BadgeColumn::make('metode')
                    ->label('Metode')
                    ->formatStateUsing(fn($state) => \App\Models\Pembayaran::METODE[$state] ?? $state)
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
                    ->formatStateUsing(fn($state) => $state ? (\App\Models\Pembayaran::BANKS[$state] ?? $state) : '-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('tanggal_bayar')
                    ->label('Tanggal Bayar')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis')
                    ->label('Jenis Pembayaran')
                    ->options(\App\Models\Pembayaran::JENIS),

                Tables\Filters\SelectFilter::make('metode')
                    ->label('Metode Pembayaran')
                    ->options(\App\Models\Pembayaran::METODE),

                Tables\Filters\Filter::make('tanggal_bayar')
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
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_bayar', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_bayar', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}