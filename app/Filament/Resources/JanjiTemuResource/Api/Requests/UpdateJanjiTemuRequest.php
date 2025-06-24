<?php

namespace App\Filament\Resources\JanjiTemuResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJanjiTemuRequest extends FormRequest
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
			'nama_pelanggan' => 'required',
			'email_pelanggan' => 'required',
			'telepon_pelanggan' => 'required',
			'alamat_pelanggan' => 'required|string',
			'stok_mobil_id' => 'required',
			'karyawan_id' => 'required',
			'waktu_mulai' => 'required',
			'waktu_selesai' => 'required',
			'jenis' => 'required',
			'tujuan' => 'required|string',
			'pesan_tambahan' => 'required|string',
			'status' => 'required',
			'catatan_internal' => 'required|string',
			'lokasi' => 'required',
			'metode' => 'required',
			'waktu_alternatif' => 'required',
			'tanggal_request' => 'required',
			'tanggal_konfirmasi' => 'required',
			'dikonfirmasi_oleh' => 'required',
			'deleted_at' => 'required'
		];
    }
}
