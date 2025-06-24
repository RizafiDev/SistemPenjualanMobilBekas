<?php

namespace App\Filament\Resources\RiwayatServisResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRiwayatServisRequest extends FormRequest
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
			'stok_mobil_id' => 'required',
			'tanggal_servis' => 'required|date',
			'jenis_servis' => 'required',
			'tempat_servis' => 'required',
			'deskripsi' => 'required|string',
			'biaya' => 'required|numeric',
			'kilometer_servis' => 'required',
			'foto_bukti' => 'required',
			'sparepart' => 'required',
			'deleted_at' => 'required'
		];
    }
}
