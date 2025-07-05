<?php

namespace App\Filament\Resources\ArticleResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $articleId = $this->route('id');

        return [
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:articles,slug,' . $articleId,
            'content' => 'sometimes|required|string',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|string|max:255',
            'status' => 'nullable|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Title is required',
            'title.max' => 'Title cannot exceed 255 characters',
            'slug.required' => 'Slug is required',
            'slug.unique' => 'This slug is already taken',
            'content.required' => 'Content is required',
            'excerpt.max' => 'Excerpt cannot exceed 500 characters',
            'status.in' => 'Status must be one of: draft, published, archived',
            'meta_title.max' => 'Meta title cannot exceed 60 characters',
            'meta_description.max' => 'Meta description cannot exceed 160 characters',
        ];
    }
}
