<?php
namespace App\Filament\Resources\HomepageResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\HomepageResource;
use App\Filament\Resources\HomepageResource\Api\Requests\UpdateHomepageRequest;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = HomepageResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update Homepage
     *
     * @param UpdateHomepageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateHomepageRequest $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Resource");
    }
}