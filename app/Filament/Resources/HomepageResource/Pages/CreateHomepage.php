<?php

namespace App\Filament\Resources\HomepageResource\Pages;

use App\Filament\Resources\HomepageResource;
use App\Models\Homepage;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateHomepage extends CreateRecord
{
    protected static string $resource = HomepageResource::class;

    public function mount(): void
    {
        // Redirect jika sudah ada data
        if (Homepage::count() > 0) {
            Notification::make()
                ->title('Peringatan!')
                ->body('Hanya boleh ada satu data homepage. Silakan edit data yang sudah ada.')
                ->warning()
                ->send();

            redirect()->to(HomepageResource::getUrl('index'));
        }

        parent::mount();
    }
}
