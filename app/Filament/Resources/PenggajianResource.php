<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenggajianResource\Pages;
use App\Models\Penggajian;
use App\Models\Karyawan;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class PenggajianResource extends Resource
{
    protected static ?string $model = Penggajian::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Penggajian';

    protected static ?string $modelLabel = 'Penggajian';

    protected static ?string $pluralModelLabel = 'Penggajian';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 2;

    public static ?string $recordTitleAttribute = 'karyawan.nama_lengkap';

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'karyawan.nama_lengkap',
            'karyawan.nip',
            'periode',
            'status',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Karyawan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('karyawan_id')
                                    ->label('Karyawan')
                                    ->relationship('karyawan', 'nama_lengkap')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $karyawan = Karyawan::find($state);
                                            if ($karyawan) {
                                                $set('gaji_pokok', $karyawan->gaji_pokok);
                                            }
                                        }
                                    }),

                                TextInput::make('periode')
                                    ->label('Periode (YYYY-MM)')
                                    ->placeholder('2024-01')
                                    ->required()
                                    ->default(now()->format('Y-m'))
                                    ->regex('/^\d{4}-\d{2}$/')
                                    ->helperText('Format: YYYY-MM (contoh: 2024-01)'),

                                DatePicker::make('tanggal_gaji')
                                    ->label('Tanggal Gaji')
                                    ->required()
                                    ->default(now()),
                            ])
                    ]),

                Section::make('Komponen Gaji')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('gaji_pokok')
                                    ->label('Gaji Pokok')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn($state, callable $set, callable $get) =>
                                        self::calculateTotals($set, $get)),

                                TextInput::make('tunjangan')
                                    ->label('Tunjangan')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->reactive()
                                    ->afterStateUpdated(fn($state, callable $set, callable $get) =>
                                        self::calculateTotals($set, $get)),

                                TextInput::make('bonus')
                                    ->label('Bonus')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->reactive()
                                    ->afterStateUpdated(fn($state, callable $set, callable $get) =>
                                        self::calculateTotals($set, $get)),

                                TextInput::make('lembur')
                                    ->label('Lembur')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->reactive()
                                    ->afterStateUpdated(fn($state, callable $set, callable $get) =>
                                        self::calculateTotals($set, $get)),

                                TextInput::make('insentif')
                                    ->label('Insentif')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->reactive()
                                    ->afterStateUpdated(fn($state, callable $set, callable $get) =>
                                        self::calculateTotals($set, $get)),
                            ])
                    ]),

                Section::make('Potongan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('potongan_terlambat')
                                    ->label('Potongan Terlambat')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->reactive()
                                    ->afterStateUpdated(fn($state, callable $set, callable $get) =>
                                        self::calculateTotals($set, $get)),

                                TextInput::make('potongan_absensi')
                                    ->label('Potongan Absensi')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->reactive()
                                    ->afterStateUpdated(fn($state, callable $set, callable $get) =>
                                        self::calculateTotals($set, $get)),

                                TextInput::make('potongan_lainnya')
                                    ->label('Potongan Lainnya')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->reactive()
                                    ->afterStateUpdated(fn($state, callable $set, callable $get) =>
                                        self::calculateTotals($set, $get)),
                            ])
                    ]),

                Section::make('Total & Status')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('total_gaji')
                                    ->label('Total Gaji')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('total_potongan')
                                    ->label('Total Potongan')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('gaji_bersih')
                                    ->label('Gaji Bersih')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(false),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->label('Status')
                                    ->options(Penggajian::getStatusOptions())
                                    ->default(Penggajian::STATUS_DRAFT)
                                    ->required(),

                                Textarea::make('catatan')
                                    ->label('Catatan')
                                    ->rows(3)
                                    ->columnSpan('full'),
                            ])
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('karyawan.nama_lengkap')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('karyawan.nip')
                    ->label('NIP')
                    ->searchable(),

                TextColumn::make('periode')
                    ->label('Periode')
                    ->sortable()
                    ->formatStateUsing(fn($state) => Carbon::createFromFormat('Y-m', $state)->format('F Y')),

                TextColumn::make('tanggal_gaji')
                    ->label('Tanggal Gaji')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('gaji_pokok')
                    ->label('Gaji Pokok')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('total_gaji')
                    ->label('Total Gaji')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('total_potongan')
                    ->label('Total Potongan')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('gaji_bersih')
                    ->label('Gaji Bersih')
                    ->money('IDR')
                    ->sortable()
                    ->weight('bold'),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'secondary' => Penggajian::STATUS_DRAFT,
                        'success' => Penggajian::STATUS_DIBAYAR,
                        'danger' => Penggajian::STATUS_BATAL,
                    ])
                    ->formatStateUsing(fn($state) => ucfirst($state)),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(Penggajian::getStatusOptions()),

                SelectFilter::make('karyawan')
                    ->label('Karyawan')
                    ->relationship('karyawan', 'nama_lengkap')
                    ->searchable()
                    ->preload(),

                Filter::make('periode')
                    ->form([
                        Select::make('tahun')
                            ->label('Tahun')
                            ->options(function () {
                                $currentYear = now()->year;
                                $years = [];
                                for ($i = $currentYear - 5; $i <= $currentYear + 1; $i++) {
                                    $years[$i] = $i;
                                }
                                return $years;
                            })
                            ->default(now()->year),

                        Select::make('bulan')
                            ->label('Bulan')
                            ->options([
                                '01' => 'Januari',
                                '02' => 'Februari',
                                '03' => 'Maret',
                                '04' => 'April',
                                '05' => 'Mei',
                                '06' => 'Juni',
                                '07' => 'Juli',
                                '08' => 'Agustus',
                                '09' => 'September',
                                '10' => 'Oktober',
                                '11' => 'November',
                                '12' => 'Desember',
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['tahun'],
                                fn(Builder $query, $tahun): Builder => $query->where('periode', 'like', $tahun . '%')
                            )
                            ->when(
                                $data['bulan'] && $data['tahun'],
                                fn(Builder $query) => $query->where('periode', $data['tahun'] . '-' . $data['bulan'])
                            );
                    })
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),

                    Action::make('mark_as_paid')
                        ->label('Tandai Dibayar')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn(Penggajian $record) => $record->isDraft())
                        ->requiresConfirmation()
                        ->action(function (Penggajian $record) {
                            $record->markAsDibayar();
                            Notification::make()
                                ->title('Penggajian berhasil ditandai sebagai dibayar')
                                ->success()
                                ->send();
                        }),
                    Action::make('print')
                        ->label('Cetak Slip Gaji')
                        ->icon('heroicon-o-printer')
                        ->color('gray')
                        ->visible(fn(Penggajian $record) => $record->isDibayar())
                        ->url(fn(Penggajian $record) => route('penggajian.print', $record))
                        ->openUrlInNewTab(),
                    Action::make('cancel')
                        ->label('Batalkan')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn(Penggajian $record) => $record->isDraft())
                        ->requiresConfirmation()
                        ->action(function (Penggajian $record) {
                            $record->markAsBatal();
                            Notification::make()
                                ->title('Penggajian berhasil dibatalkan')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('mark_as_paid')
                        ->label('Tandai Dibayar')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                if ($record->isDraft()) {
                                    $record->markAsDibayar();
                                }
                            });
                            Notification::make()
                                ->title('Penggajian yang dipilih berhasil ditandai sebagai dibayar')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('tanggal_gaji', 'desc');
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
            'index' => Pages\ListPenggajians::route('/'),
            'create' => Pages\CreatePenggajian::route('/create'),
            'view' => Pages\ViewPenggajian::route('/{record}'),
            'edit' => Pages\EditPenggajian::route('/{record}/edit'),
        ];
    }

    protected static function calculateTotals(callable $set, callable $get): void
    {
        $gajiPokok = (float) $get('gaji_pokok') ?? 0;
        $tunjangan = (float) $get('tunjangan') ?? 0;
        $bonus = (float) $get('bonus') ?? 0;
        $lembur = (float) $get('lembur') ?? 0;
        $insentif = (float) $get('insentif') ?? 0;

        $potonganTerlambat = (float) $get('potongan_terlambat') ?? 0;
        $potonganAbsensi = (float) $get('potongan_absensi') ?? 0;
        $potonganLainnya = (float) $get('potongan_lainnya') ?? 0;

        $totalGaji = $gajiPokok + $tunjangan + $bonus + $lembur + $insentif;
        $totalPotongan = $potonganTerlambat + $potonganAbsensi + $potonganLainnya;
        $gajiBersih = $totalGaji - $totalPotongan;

        $set('total_gaji', $totalGaji);
        $set('total_potongan', $totalPotongan);
        $set('gaji_bersih', $gajiBersih);
    }
}