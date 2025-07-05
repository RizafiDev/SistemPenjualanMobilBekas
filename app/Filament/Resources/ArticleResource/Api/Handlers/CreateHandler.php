<?php
namespace App\Filament\Resources\ArticleResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\ArticleResource;
use App\Filament\Resources\ArticleResource\Api\Requests\CreateArticleRequest;

class CreateHandler extends Handlers
{
    public static string|null $uri = '/';
    public static string|null $resource = ArticleResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel()
    {
        return static::$resource::getModel();
    }

    /**
     * Create Article
     *
     * @param CreateArticleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateArticleRequest $request)
    {
        $model = new (static::getModel());

        $data = $request->all();

        // Auto-generate slug if not provided
        if (empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = str()->slug($data['title']);
        }

        // Set default status if not provided
        if (empty($data['status'])) {
            $data['status'] = 'draft';
        }

        $model->fill($data);
        $model->save();

        return static::sendSuccessResponse($model, "Article created successfully");
    }
}
