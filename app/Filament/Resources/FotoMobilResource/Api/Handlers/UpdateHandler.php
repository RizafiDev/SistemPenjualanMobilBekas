<?php
namespace App\Filament\Resources\FotoMobilResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\FotoMobilResource;
use App\Filament\Resources\FotoMobilResource\Api\Requests\UpdateFotoMobilRequest;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = FotoMobilResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update FotoMobil
     *
     * @param UpdateFotoMobilRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateFotoMobilRequest $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Resource");
    }
}


