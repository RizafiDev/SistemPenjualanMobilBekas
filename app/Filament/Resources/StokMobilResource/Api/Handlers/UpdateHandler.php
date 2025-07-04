<?php
namespace App\Filament\Resources\StokMobilResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\StokMobilResource;
use App\Filament\Resources\StokMobilResource\Api\Requests\UpdateStokMobilRequest;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = StokMobilResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update StokMobil
     *
     * @param UpdateStokMobilRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateStokMobilRequest $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Resource");
    }
}


