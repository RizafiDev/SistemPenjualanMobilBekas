<?php

namespace App\Filament\Resources\JanjiTemuResource\Pages;

use App\Filament\Resources\JanjiTemuResource;
use Filament\Resources\Pages\CreateRecord;

class CreateJanjiTemu extends CreateRecord
{
    protected static string $resource = JanjiTemuResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tanggal_request'] = now();

        return $data;
    }
}