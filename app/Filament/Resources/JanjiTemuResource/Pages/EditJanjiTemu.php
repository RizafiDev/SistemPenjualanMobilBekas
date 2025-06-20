<?php

namespace App\Filament\Resources\JanjiTemuResource\Pages;

use App\Filament\Resources\JanjiTemuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJanjiTemu extends EditRecord
{
    protected static string $resource = JanjiTemuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}