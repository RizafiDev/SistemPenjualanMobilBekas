<?php
namespace App\Filament\Resources\KategoriResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\KategoriResource;
use Illuminate\Routing\Router;


class KategoriApiService extends ApiService
{
    protected static string | null $resource = KategoriResource::class;

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
