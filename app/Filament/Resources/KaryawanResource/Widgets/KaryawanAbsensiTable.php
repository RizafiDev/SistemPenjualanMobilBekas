<?php

namespace App\Filament\Resources\KaryawanResource\Widgets;

use App\Models\Karyawan;
use App\Models\Presensi;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class KaryawanAbsensiTable extends BaseWidget
{
    public ?Karyawan $record = null;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Presensi::query()
                    ->where('karyawan_id', $this->record?->id)
                    ->with('karyawan')
                    ->latest('tanggal')
            )
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('jam_masuk')
                    ->label('Jam Masuk')
                    ->time('H:i')
                    ->placeholder('-')
                    ->color(fn($state, $record) => $record->menit_terlambat > 0 ? 'warning' : 'success'),

                TextColumn::make('jam_pulang')
                    ->label('Jam Pulang')
                    ->time('H:i')
                    ->placeholder('-'),

                TextColumn::make('jam_kerja_formatted')
                    ->label('Jam Kerja')
                    ->placeholder('-'),

                TextColumn::make('keterangan_terlambat')
                    ->label('Keterlambatan')
                    ->placeholder('-')
                    ->color('warning'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        Presensi::STATUS_HADIR => 'success',
                        Presensi::STATUS_TERLAMBAT => 'warning',
                        Presensi::STATUS_TIDAK_HADIR => 'danger',
                        Presensi::STATUS_SAKIT => 'info',
                        Presensi::STATUS_IZIN => 'gray',
                        Presensi::STATUS_CUTI => 'primary',
                        Presensi::STATUS_LIBUR => 'secondary',
                        default => 'gray'
                    })
                    ->formatStateUsing(fn(string $state): string => Presensi::getStatusOptions()[$state] ?? $state),

                ImageColumn::make('foto_masuk')
                    ->label('Foto Masuk')
                    ->circular()
                    ->size(40)
                    ->placeholder('Tidak ada foto')
                    ->toggleable(),

                ImageColumn::make('foto_pulang')
                    ->label('Foto Pulang')
                    ->circular()
                    ->size(40)
                    ->placeholder('Tidak ada foto')
                    ->toggleable(),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->placeholder('-')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(Presensi::getStatusOptions()),

                Filter::make('bulan_ini')
                    ->label('Bulan Ini')
                    ->query(fn(Builder $query): Builder => $query->whereMonth('tanggal', now()->month))
                    ->toggle(),

                Filter::make('tanggal')
                    ->label('Periode')
                    ->form([
                        DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal')
                            ->default(now()->startOfMonth()),
                        DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal')
                            ->default(now()->endOfMonth()),
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
            ->defaultSort('tanggal', 'desc')
            ->paginated([10, 25, 50])
            ->heading('Riwayat Absensi')
            ->description('Daftar riwayat absensi karyawan');
    }
}