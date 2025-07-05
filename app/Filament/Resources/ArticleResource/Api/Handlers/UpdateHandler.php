<?php
namespace App\Filament\Resources\ArticleResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\ArticleResource;
use App\Filament\Resources\ArticleResource\Api\Requests\UpdateArticleRequest;

class UpdateHandler extends Handlers
{
    public static string|null $uri = '/{id}';
    public static string|null $resource = ArticleResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel()
    {
        return static::$resource::getModel();
    }

    /**
     * Update Article
     *
     * @param UpdateArticleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateArticleRequest $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model)
            return static::sendNotFoundResponse();

        $data = $request->all();

        // Auto-generate slug if title is updated and slug is not provided
        if (!empty($data['title']) && empty($data['slug'])) {
            $data['slug'] = str()->slug($data['title']);
        }

        $model->fill($data);
        $model->save();

        return static::sendSuccessResponse($model, "Article updated successfully");
    }
}
