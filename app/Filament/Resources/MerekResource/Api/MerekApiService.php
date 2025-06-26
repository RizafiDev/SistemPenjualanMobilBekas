<?php
namespace App\Filament\Resources\MerekResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\MerekResource;
use Illuminate\Routing\Router;


class MerekApiService extends ApiService
{
    protected static string|null $resource = MerekResource::class;

    public static function handlers(): array
    {
        return [
                // Handlers\CreateHandler::class,
                // Handlers\UpdateHandler::class,
                // Handlers\DeleteHandler::class,
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class
        ];

    }
}
