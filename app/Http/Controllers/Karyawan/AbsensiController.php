<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\PengaturanKantor;
use App\Models\PengajuanCuti;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $karyawan = Auth::guard('karyawan')->user();
        $pengaturanKantor = PengaturanKantor::aktif()->first();

        $presensiHariIni = Presensi::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', today())
            ->first();

        $statistikBulan = $this->getStatistikBulan($karyawan->id);

        $riwayatPresensi = Presensi::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', '>=', now()->subDays(7))
            ->orderBy('tanggal', 'desc')
            ->get();

        $pengajuanCutiList = PengajuanCuti::where('karyawan_id', $karyawan->id)
            ->orderByDesc('created_at')
            ->get();

        return view('karyawan.absensi.index', compact(
            'karyawan',
            'presensiHariIni',
            'statistikBulan',
            'riwayatPresensi',
            'pengaturanKantor',
            'pengajuanCutiList'
        ));
    }

    private function getStatistikBulan($karyawanId)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $today = Carbon::today(); // Stats up to today within the current month

        $presensiRecords = Presensi::where('karyawan_id', $karyawanId)
            ->whereBetween('tanggal', [$startOfMonth, $today])
            ->get();

        $stats = [
            'hadir' => 0,
            'terlambat' => 0,
            'tidak_hadir_calculated' => 0,
            'sakit' => 0,
            'izin' => 0,
            'cuti' => 0,
            'libur' => 0, // 'libur' from records
            'total_jam_kerja' => 0,
            'total_hari_kerja_efektif' => 0,
        ];

        foreach ($presensiRecords as $p) {
            if (array_key_exists($p->status, $stats)) {
                $stats[$p->status]++;
            }
            $stats['total_jam_kerja'] += $p->jam_kerja ?? 0;
        }

        // Count explicit 'tidak_hadir' from records (if any, though usually it's an absence of record)
        // This is already handled by the loop if 'tidak_hadir' is a valid status in $stats array.
        // For clarity, ensure 'tidak_hadir' is a key in $stats if it's a possible status.
        // $stats['tidak_hadir'] = $presensiRecords->where('status', Presensi::STATUS_TIDAK_HADIR)->count();


        $currentDate = $startOfMonth->copy();
        while ($currentDate->lte($today)) {
            if (!$currentDate->isWeekend()) { // Assuming Mon-Fri are workdays. Consider holidays.
                $stats['total_hari_kerja_efektif']++;
                $presensiForDay = $presensiRecords->first(function ($p) use ($currentDate) {
                    return $p->tanggal->isSameDay($currentDate);
                });

                if (!$presensiForDay) {
                    // No record for this workday, count as 'tidak_hadir_calculated'
                    // This needs to be more robust by checking approved leaves (e.g., from PengajuanCuti)
                    // For now, this is a basic count of workdays with no attendance record.
                    $stats['tidak_hadir_calculated']++;
                }
            }
            $currentDate->addDay();
        }

        // The view expects 'tidak_hadir', so let's use the calculated one.
        // If there's a system where 'tidak_hadir' is explicitly recorded, merge logic.
        $stats['tidak_hadir'] = $stats['tidak_hadir_calculated'];
        unset($stats['tidak_hadir_calculated']);


        $stats['total_jam_kerja'] = round($stats['total_jam_kerja'], 2);
        return $stats;
    }

    public function absenMasuk(Request $request)
    {
        return $this->handleAbsen($request, 'masuk');
    }

    public function absenPulang(Request $request)
    {
        return $this->handleAbsen($request, 'pulang');
    }

    private function handleAbsen(Request $request, string $type)
    {
        $rules = [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
        $messages = [
            'latitude.required' => 'Lokasi diperlukan untuk absen.',
            'longitude.required' => 'Lokasi diperlukan untuk absen.',
            'foto.required' => 'Foto diperlukan untuk absen.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format foto harus jpeg, png, jpg, atau webp.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ];

        if ($type === 'pulang') {
            $rules['keterangan'] = 'nullable|string|max:500';
            $messages['keterangan.string'] = 'Keterangan harus berupa teks.';
            $messages['keterangan.max'] = 'Keterangan tidak boleh lebih dari 500 karakter.';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $pengaturanKantor = PengaturanKantor::aktif()->first();
        if (!$pengaturanKantor) {
            return response()->json(['success' => false, 'message' => 'Pengaturan kantor aktif tidak ditemukan.'], 500);
        }

        if ($pengaturanKantor->latitude && $pengaturanKantor->longitude && $pengaturanKantor->radius_meter) {
            $locationValidationResponse = $this->validateLocation($request->latitude, $request->longitude, $pengaturanKantor);
            if ($locationValidationResponse)
                return $locationValidationResponse;
        } else {
            Log::warning("Absen {$type}: Pengaturan lokasi kantor (lat/long/radius) tidak lengkap. Validasi lokasi dilewati.");
        }

        $karyawan = Auth::guard('karyawan')->user();
        $tanggal = today();
        $now = now();

        $presensi = Presensi::firstOrNew([
            'karyawan_id' => $karyawan->id,
            'tanggal' => $tanggal,
        ]);

        if ($type === 'masuk') {
            if ($presensi->exists && $presensi->jam_masuk) {
                return response()->json(['success' => false, 'message' => 'Anda sudah melakukan absen masuk hari ini.'], 400);
            }
            $presensi->jam_masuk = $now;
            $presensi->latitude_masuk = $request->latitude;
            $presensi->longitude_masuk = $request->longitude;
            if (!$presensi->status) { // Only set initial status if not already set (e.g. from admin)
                $presensi->status = Presensi::STATUS_HADIR; // Model's saving event will adjust if late
            }
        } elseif ($type === 'pulang') {
            if (!$presensi->exists || !$presensi->jam_masuk) {
                return response()->json(['success' => false, 'message' => 'Anda belum melakukan absen masuk hari ini.'], 400);
            }
            if ($presensi->jam_pulang) {
                return response()->json(['success' => false, 'message' => 'Anda sudah melakukan absen pulang hari ini.'], 400);
            }
            $presensi->jam_pulang = $now;
            $presensi->latitude_pulang = $request->latitude;
            $presensi->longitude_pulang = $request->longitude;

            // Simpan keterangan jika ada (untuk pulang cepat)
            if ($request->filled('keterangan')) {
                $existingKeterangan = $presensi->keterangan ? $presensi->keterangan . "\n" : "";
                $presensi->keterangan = $existingKeterangan . "Alasan Pulang Cepat: " . $request->keterangan;
            }
        }

        $foto = $request->file('foto');
        $fotoFieldName = "foto_{$type}";
        $namaFoto = "absen_{$type}_{$karyawan->id}_{$now->timestamp}.{$foto->getClientOriginalExtension()}";
        // Store in 'public/absensi/masuk' or 'public/absensi/pulang'
        $fotoPath = $foto->storeAs("public/absensi/{$type}", $namaFoto);

        if (!$fotoPath) {
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan foto.'], 500);
        }
        // Remove 'public/' prefix for database storage if Storage::url() or asset() is used later
        $presensi->{$fotoFieldName} = "absensi/{$type}/" . $namaFoto;


        try {
            $presensi->save();
            $presensi->refresh();

            return response()->json([
                'success' => true,
                'message' => "Absen {$type} berhasil.",
                'data' => $presensi
            ], 200);

        } catch (\Exception $e) {
            Log::error("Absen {$type} gagal: " . $e->getMessage(), ['exception' => $e]);
            if (isset($fotoPath) && Storage::exists($fotoPath)) { // Check if $fotoPath was set
                Storage::delete($fotoPath); // Attempt to delete stored photo on error
            }
            return response()->json(['success' => false, 'message' => "Absen {$type} gagal: Terjadi kesalahan sistem."], 500);
        }
    }

    private function validateLocation($latitude, $longitude, PengaturanKantor $pengaturanKantor)
    {
        if (!$pengaturanKantor->isWithinRadius($latitude, $longitude)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda berada di luar radius kantor yang diizinkan (' . $pengaturanKantor->radius_meter . 'm).'
            ], 422);
        }
        return null;
    }
}
