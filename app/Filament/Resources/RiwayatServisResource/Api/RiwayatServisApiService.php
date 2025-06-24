<?php
namespace App\Filament\Resources\RiwayatServisResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\RiwayatServisResource;
use Illuminate\Routing\Router;


class RiwayatServisApiService extends ApiService
{
    protected static string | null $resource = RiwayatServisResource::class;

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
