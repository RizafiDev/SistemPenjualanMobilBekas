<?php

namespace App\Filament\Resources\KaryawanResource\Widgets;

use App\Models\Karyawan;
use App\Models\PengajuanCuti;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class KaryawanCutiTable extends BaseWidget
{
    public ?Karyawan $record = null;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PengajuanCuti::query()
                    ->where('karyawan_id', $this->record?->id)
                    ->with(['karyawan', 'disetujuiOleh'])
                    ->latest('created_at')
            )
            ->columns([
                TextColumn::make('jenis')
                    ->label('Jenis Cuti')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        PengajuanCuti::JENIS_TAHUNAN => 'primary',
                        PengajuanCuti::JENIS_SAKIT => 'warning',
                        PengajuanCuti::JENIS_DARURAT => 'danger',
                        PengajuanCuti::JENIS_LAINNYA => 'gray',
                        default => 'gray'
                    })
                    ->formatStateUsing(fn(string $state): string => PengajuanCuti::getJenisOptions()[$state] ?? $state),

                TextColumn::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('tanggal_selesai')
                    ->label('Tanggal Selesai')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('jumlah_hari')
                    ->label('Jumlah Hari')
                    ->numeric()
                    ->suffix(' hari')
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        PengajuanCuti::STATUS_MENUNGGU => 'warning',
                        PengajuanCuti::STATUS_DISETUJUI => 'success',
                        PengajuanCuti::STATUS_DITOLAK => 'danger',
                        default => 'gray'
                    })
                    ->formatStateUsing(fn(string $state): string => PengajuanCuti::getStatusOptions()[$state] ?? $state),

                TextColumn::make('alasan')
                    ->label('Alasan')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                TextColumn::make('disetujuiOleh.name')
                    ->label('Disetujui Oleh')
                    ->placeholder('Belum diproses')
                    ->toggleable(),

                TextColumn::make('tanggal_persetujuan')
                    ->label('Tanggal Persetujuan')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Belum diproses')
                    ->toggleable(),

                TextColumn::make('alasan_penolakan')
                    ->label('Alasan Penolakan')
                    ->limit(50)
                    ->placeholder('-')
                    ->color('danger')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(PengajuanCuti::getStatusOptions()),

                SelectFilter::make('jenis')
                    ->label('Jenis Cuti')
                    ->options(PengajuanCuti::getJenisOptions()),

                Filter::make('tahun_ini')
                    ->label('Tahun Ini')
                    ->query(fn(Builder $query): Builder => $query->whereYear('tanggal_mulai', now()->year))
                    ->toggle(),

                Filter::make('tanggal')
                    ->label('Periode')
                    ->form([
                        DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal')
                            ->default(now()->startOfYear()),
                        DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal')
                            ->default(now()->endOfYear()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_mulai', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_selesai', '<=', $date),
                            );
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50])
            ->heading('Riwayat Pengajuan Cuti')
            ->description('Daftar pengajuan cuti karyawan');
    }
}