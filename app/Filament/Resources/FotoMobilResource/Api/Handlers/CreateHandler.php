<?php
namespace App\Filament\Resources\FotoMobilResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\FotoMobilResource;
use App\Filament\Resources\FotoMobilResource\Api\Requests\CreateFotoMobilRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = FotoMobilResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create FotoMobil
     *
     * @param CreateFotoMobilRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateFotoMobilRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}


