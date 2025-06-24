<?php

namespace App\Filament\Resources\StokMobilResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateStokMobilRequest extends FormRequest
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
			'varian_id' => 'required',
			'warna' => 'required',
			'no_rangka' => 'required',
			'no_mesin' => 'required',
			'tahun' => 'required',
			'kilometer' => 'required',
			'kondisi' => 'required',
			'status' => 'required',
			'harga_beli' => 'required|numeric',
			'harga_jual' => 'required|numeric',
			'laba_kotor' => 'required|numeric',
			'tanggal_masuk' => 'required|date',
			'tanggal_keluar' => 'required|date',
			'lokasi' => 'required',
			'catatan' => 'required|string',
			'kelengkapan' => 'required',
			'riwayat_perbaikan' => 'required',
			'dokumen' => 'required',
			'foto_kondisi' => 'required',
			'kondisi_fitur' => 'required',
			'deleted_at' => 'required'
		];
    }
}
