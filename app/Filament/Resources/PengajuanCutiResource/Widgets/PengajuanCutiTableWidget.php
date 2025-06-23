<?php

namespace App\Filament\Resources\PengajuanCutiResource\Widgets;

use App\Models\PengajuanCuti;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PengajuanCutiTableWidget extends BaseWidget
{
    protected static ?string $heading = 'Pengajuan Cuti Terbaru';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PengajuanCuti::query()
                    ->with(['karyawan'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('karyawan.nama_lengkap')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('jenis')
                    ->label('Jenis')
                    ->formatStateUsing(fn(string $state): string => PengajuanCuti::getJenisOptions()[$state] ?? $state)
                    ->colors([
                        'primary' => PengajuanCuti::JENIS_TAHUNAN,
                        'warning' => PengajuanCuti::JENIS_SAKIT,
                        'danger' => PengajuanCuti::JENIS_DARURAT,
                        'secondary' => PengajuanCuti::JENIS_LAINNYA,
                    ]),

                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jumlah_hari')
                    ->label('Hari')
                    ->numeric()
                    ->suffix(' hari')
                    ->alignCenter(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn(string $state): string => PengajuanCuti::getStatusOptions()[$state] ?? $state)
                    ->colors([
                        'warning' => PengajuanCuti::STATUS_MENUNGGU,
                        'success' => PengajuanCuti::STATUS_DISETUJUI,
                        'danger' => PengajuanCuti::STATUS_DITOLAK,
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(
                        fn(PengajuanCuti $record): string =>
                        route('filament.admin.resources.pengajuan-cutis.view', $record)
                    ),
            ]);
    }
}


