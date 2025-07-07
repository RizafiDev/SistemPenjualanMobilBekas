<?php

namespace App\Filament\Resources\HomepageResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHomepageRequest extends FormRequest
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
        return [
			'pelanggan_puas' => 'required',
			'rating_puas' => 'required',
			'foto_homepage' => 'required'
		];
    }
}
