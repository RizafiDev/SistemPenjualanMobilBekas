<?php

namespace App\Filament\Resources\PenggajianResource\Widgets;

use App\Models\Penggajian;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Carbon\Carbon;

class RecentPenggajianWidget extends BaseWidget
{
    protected static ?string $heading = 'Penggajian Terbaru';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Penggajian::query()
                    ->with(['karyawan'])
                    ->latest()
                    ->limit(10)
            )
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
                    ->formatStateUsing(fn($state) => Carbon::createFromFormat('Y-m', $state)->format('M Y')),

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
                    ->formatStateUsing(fn($state) => match ($state) {
                        Penggajian::STATUS_DRAFT => 'Draft',
                        Penggajian::STATUS_DIBAYAR => 'Dibayar',
                        Penggajian::STATUS_BATAL => 'Batal',
                        default => ucfirst($state),
                    }),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn(Penggajian $record): string => route('filament.admin.resources.penggajians.view', $record)),
            ])
            ->paginated(false);
    }
}