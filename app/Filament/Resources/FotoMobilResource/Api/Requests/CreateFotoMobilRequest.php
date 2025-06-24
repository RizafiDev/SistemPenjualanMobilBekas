<?php

namespace App\Filament\Resources\FotoMobilResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateFotoMobilRequest extends FormRequest
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
			'mobil_id' => 'required',
			'path_file' => 'required',
			'jenis_media' => 'required',
			'jenis_gambar' => 'required',
			'urutan_tampil' => 'required',
			'teks_alternatif' => 'required',
			'keterangan' => 'required',
			'deleted_at' => 'required'
		];
    }
}
