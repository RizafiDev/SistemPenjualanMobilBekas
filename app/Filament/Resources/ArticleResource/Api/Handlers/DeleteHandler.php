<?php
namespace App\Filament\Resources\ArticleResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\ArticleResource;

class DeleteHandler extends Handlers
{
    public static string|null $uri = '/{id}';
    public static string|null $resource = ArticleResource::class;

    public static function getMethod()
    {
        return Handlers::DELETE;
    }

    public static function getModel()
    {
        return static::$resource::getModel();
    }

    /**
     * Delete Article
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(Request $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model)
            return static::sendNotFoundResponse();

        $model->delete();

        return static::sendSuccessResponse($model, "Article deleted successfully");
    }
}
