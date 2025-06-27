<?php

namespace App\Filament\Resources\JanjiTemuResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateJanjiTemuRequest extends FormRequest
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
			// Data pelanggan - yang diisi user
			'nama_pelanggan' => 'required|string|max:255',
			'email_pelanggan' => 'required|email',
			'telepon_pelanggan' => 'required|string|max:20',
			'alamat_pelanggan' => 'nullable|string|max:500', // Made optional

			// Data janji temu - yang diisi user
			'stok_mobil_id' => 'nullable|exists:stok_mobils,id', // Made optional for general consultation
			'waktu_mulai' => 'required|date|after:now',
			'waktu_selesai' => 'nullable|date|after:waktu_mulai', // Added validation for end time
			'jenis' => 'required|in:test_drive,konsultasi,negosiasi',
			'tujuan' => 'nullable|string|max:500',
			'pesan_tambahan' => 'nullable|string|max:1000',
			'waktu_alternatif' => 'nullable|date|after:now',
			'metode' => 'nullable|in:online,offline',
			'lokasi' => 'nullable|in:showroom,rumah_pelanggan',

			// Field yang auto-generated atau default
			// 'karyawan_id' => 'nullable', // akan diassign admin
			// 'waktu_selesai' => 'nullable', // akan diset otomatis
			// 'status' => 'nullable', // default: 'pending'
			// 'catatan_internal' => 'nullable', // untuk admin
			// 'lokasi' => 'nullable', // bisa default atau optional
			// 'metode' => 'nullable', // default atau dari setting
			// 'tanggal_request' => 'nullable', // auto: now()
			// 'tanggal_konfirmasi' => 'nullable', // setelah dikonfirmasi
			// 'dikonfirmasi_oleh' => 'nullable', // admin yang konfirmasi
		];
	}
}
