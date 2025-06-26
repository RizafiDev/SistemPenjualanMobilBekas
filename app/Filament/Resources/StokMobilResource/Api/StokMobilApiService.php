<?php
namespace App\Filament\Resources\StokMobilResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\StokMobilResource;
use Illuminate\Routing\Router;


class StokMobilApiService extends ApiService
{
    protected static string|null $resource = StokMobilResource::class;

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
