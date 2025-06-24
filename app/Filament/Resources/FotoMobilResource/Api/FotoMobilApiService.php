<?php
namespace App\Filament\Resources\FotoMobilResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\FotoMobilResource;
use Illuminate\Routing\Router;


class FotoMobilApiService extends ApiService
{
    protected static string | null $resource = FotoMobilResource::class;

    public static function handlers() : array
    {
        return [
            Handlers\CreateHandler::class,
            Handlers\UpdateHandler::class,
            Handlers\DeleteHandler::class,
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class
        ];

    }
}
