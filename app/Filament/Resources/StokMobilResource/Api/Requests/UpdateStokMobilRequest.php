<?php

namespace App\Filament\Resources\StokMobilResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStokMobilRequest extends FormRequest
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
			'mobil_id' => 'required|exists:mobils,id',
			'varian_id' => 'nullable|exists:varians,id',
			'warna' => 'required|string|max:255',
			'no_rangka' => 'required|string|max:255|unique:stok_mobils,no_rangka,' . $this->route('stokMobil'),
			'no_mesin' => 'required|string|max:255|unique:stok_mobils,no_mesin,' . $this->route('stokMobil'),
			'no_polisi' => 'nullable|string|max:15|unique:stok_mobils,no_polisi,' . $this->route('stokMobil'),
			'tahun' => 'required|integer|min:1900|max:' . date('Y'),
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
