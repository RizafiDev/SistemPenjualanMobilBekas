<?php
namespace App\Filament\Resources\MobilResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\MobilResource;
use Illuminate\Routing\Router;


class MobilApiService extends ApiService
{
    protected static string|null $resource = MobilResource::class;

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
