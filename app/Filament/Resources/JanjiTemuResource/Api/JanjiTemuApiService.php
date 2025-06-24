<?php
namespace App\Filament\Resources\JanjiTemuResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\JanjiTemuResource;
use Illuminate\Routing\Router;


class JanjiTemuApiService extends ApiService
{
    protected static string | null $resource = JanjiTemuResource::class;

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
