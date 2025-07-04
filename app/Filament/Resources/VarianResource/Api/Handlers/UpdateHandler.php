<?php
namespace App\Filament\Resources\VarianResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\VarianResource;
use App\Filament\Resources\VarianResource\Api\Requests\UpdateVarianRequest;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = VarianResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update Varian
     *
     * @param UpdateVarianRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateVarianRequest $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Resource");
    }
}


