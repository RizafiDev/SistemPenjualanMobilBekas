<?php
namespace App\Filament\Resources\HomepageResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\HomepageResource;
use Illuminate\Routing\Router;


class HomepageApiService extends ApiService
{
    protected static string | null $resource = HomepageResource::class;

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
