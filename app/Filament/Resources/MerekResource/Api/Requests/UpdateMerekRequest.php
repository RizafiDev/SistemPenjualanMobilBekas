<?php

namespace App\Filament\Resources\MerekResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMerekRequest extends FormRequest
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
			'nama' => 'required',
			'slug' => 'required',
			'logo' => 'required',
			'negara_asal' => 'required',
			'deskripsi' => 'required|string',
			'tahun_berdiri' => 'required',
			'aktif' => 'required',
			'deleted_at' => 'required'
		];
    }
}
