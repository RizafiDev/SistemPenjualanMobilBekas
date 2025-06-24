<?php
namespace App\Filament\Resources\VarianResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\VarianResource;
use Illuminate\Routing\Router;


class VarianApiService extends ApiService
{
    protected static string | null $resource = VarianResource::class;

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
