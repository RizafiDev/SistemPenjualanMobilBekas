<?php
namespace App\Filament\Resources\HomepageResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\HomepageResource;
use App\Filament\Resources\HomepageResource\Api\Requests\CreateHomepageRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = HomepageResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create Homepage
     *
     * @param CreateHomepageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateHomepageRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}