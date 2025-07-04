<?php
namespace App\Filament\Resources\StokMobilResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\StokMobilResource;
use App\Filament\Resources\StokMobilResource\Api\Requests\CreateStokMobilRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = StokMobilResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create StokMobil
     *
     * @param CreateStokMobilRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateStokMobilRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}


