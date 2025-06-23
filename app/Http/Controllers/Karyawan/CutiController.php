<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengajuanCuti;
use App\Models\SaldoCuti;
use Illuminate\Support\Facades\Auth;

class CutiController extends Controller
{
    public function ajukan(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:tahunan,sakit,darurat,lainnya',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|max:255',
        ]);

        $karyawan = Auth::guard('karyawan')->user();
        $jumlahHari = (new \Carbon\Carbon($request->tanggal_mulai))->diffInDays($request->tanggal_selesai) + 1;


        PengajuanCuti::create([
            'karyawan_id' => $karyawan->id,
            'jenis' => $request->jenis,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'jumlah_hari' => $jumlahHari,
            'alasan' => $request->alasan,
            'status' => PengajuanCuti::STATUS_MENUNGGU,
        ]);

        return back()->with('success', 'Pengajuan cuti berhasil dikirim.');
    }
}