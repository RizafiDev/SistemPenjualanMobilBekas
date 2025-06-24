<?php
namespace App\Filament\Resources\JanjiTemuResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\JanjiTemuResource;
use App\Filament\Resources\JanjiTemuResource\Api\Requests\CreateJanjiTemuRequest;

class CreateHandler extends Handlers
{
    public static string|null $uri = '/';
    public static string|null $resource = JanjiTemuResource::class;
    public static bool $public = true;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel()
    {
        return static::$resource::getModel();
    }

    /**
     * Create JanjiTemu
     *
     * @param CreateJanjiTemuRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateJanjiTemuRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}