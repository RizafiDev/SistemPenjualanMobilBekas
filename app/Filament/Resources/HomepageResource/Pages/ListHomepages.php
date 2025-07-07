<?php

namespace App\Filament\Resources\HomepageResource\Pages;

use App\Filament\Resources\HomepageResource;
use App\Models\Homepage;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHomepages extends ListRecords
{
    protected static string $resource = HomepageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(Homepage::count() === 0), // Hanya tampil jika belum ada data
        ];
    }
}
