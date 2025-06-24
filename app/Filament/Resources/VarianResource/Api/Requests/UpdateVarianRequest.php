<?php

namespace App\Filament\Resources\VarianResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVarianRequest extends FormRequest
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
			'nama' => 'required',
			'kode' => 'required',
			'deskripsi' => 'required|string',
			'harga_otr' => 'required|numeric',
			'tipe_mesin' => 'required',
			'kapasitas_mesin_cc' => 'required',
			'silinder' => 'required',
			'transmisi' => 'required',
			'jumlah_gigi' => 'required',
			'daya_hp' => 'required',
			'torsi_nm' => 'required',
			'jenis_bahan_bakar' => 'required',
			'konsumsi_bahan_bakar_kota' => 'required|numeric',
			'konsumsi_bahan_bakar_jalan' => 'required|numeric',
			'panjang_mm' => 'required',
			'lebar_mm' => 'required',
			'tinggi_mm' => 'required',
			'jarak_sumbu_roda_mm' => 'required',
			'ground_clearance_mm' => 'required',
			'berat_kosong_kg' => 'required',
			'berat_isi_kg' => 'required',
			'kapasitas_bagasi_l' => 'required',
			'kapasitas_tangki_l' => 'required',
			'akselerasi_0_100_kmh' => 'required|numeric',
			'kecepatan_maksimal_kmh' => 'required',
			'fitur_keamanan' => 'required',
			'fitur_kenyamanan' => 'required',
			'fitur_hiburan' => 'required',
			'aktif' => 'required',
			'deleted_at' => 'required'
		];
    }
}
