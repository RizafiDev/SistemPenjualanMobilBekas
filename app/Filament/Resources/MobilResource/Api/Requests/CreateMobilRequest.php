<?php

namespace App\Filament\Resources\MobilResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMobilRequest extends FormRequest
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
			'merek_id' => 'required',
			'kategori_id' => 'required',
			'tahun_mulai' => 'required',
			'tahun_akhir' => 'required',
			'kapasitas_penumpang' => 'required',
			'tipe_bodi' => 'required',
			'status' => 'required',
			'deskripsi' => 'required|string',
			'fitur_unggulan' => 'required|string',
			'deleted_at' => 'required'
		];
    }
}
