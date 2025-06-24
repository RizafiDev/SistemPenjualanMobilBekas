<?php

namespace App\Filament\Resources\KategoriResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKategoriRequest extends FormRequest
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
			'deskripsi' => 'required|string',
			'ikon' => 'required',
			'urutan_tampil' => 'required',
			'unggulan' => 'required',
			'deleted_at' => 'required'
		];
    }
}
