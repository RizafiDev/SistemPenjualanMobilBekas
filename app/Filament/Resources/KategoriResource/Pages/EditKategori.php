<?php

namespace App\Filament\Resources\KategoriResource\Pages;

use App\Filament\Resources\KategoriResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKategori extends EditRecord
{
    protected static string $resource = KategoriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),

            Actions\Action::make('toggleUnggulan')
                ->label(fn() => $this->record->unggulan ? 'Hapus dari Unggulan' : 'Jadikan Unggulan')
                ->icon(fn() => $this->record->unggulan ? 'heroicon-o-star' : 'heroicon-s-star')
                ->color(fn() => $this->record->unggulan ? 'warning' : 'gray')
                ->action(function () {
                    $this->record->update(['unggulan' => !$this->record->unggulan]);
                    $this->refreshFormData(['unggulan']);
                })
                ->successNotificationTitle('Status unggulan berhasil diubah'),

            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'Edit Kategori: ' . $this->record->nama;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Kategori berhasil diperbarui';
    }
}