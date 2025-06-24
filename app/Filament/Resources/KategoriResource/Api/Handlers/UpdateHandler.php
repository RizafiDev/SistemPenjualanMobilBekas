<?php
namespace App\Filament\Resources\KategoriResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\KategoriResource;
use App\Filament\Resources\KategoriResource\Api\Requests\UpdateKategoriRequest;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = KategoriResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update Kategori
     *
     * @param UpdateKategoriRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateKategoriRequest $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Resource");
    }
}