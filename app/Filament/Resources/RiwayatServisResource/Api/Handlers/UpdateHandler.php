<?php
namespace App\Filament\Resources\RiwayatServisResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\RiwayatServisResource;
use App\Filament\Resources\RiwayatServisResource\Api\Requests\UpdateRiwayatServisRequest;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = RiwayatServisResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update RiwayatServis
     *
     * @param UpdateRiwayatServisRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateRiwayatServisRequest $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Resource");
    }
}


