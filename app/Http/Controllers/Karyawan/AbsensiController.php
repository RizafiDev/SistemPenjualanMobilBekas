<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Karyawan;
use App\Models\PengaturanKantor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function index()
    {
        $karyawan = auth()->guard('karyawan')->user();
        $presensiHariIni = Presensi::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', today())
            ->first();

        $riwayatPresensi = Presensi::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', '>=', now()->subDays(7))
            ->orderBy('tanggal', 'desc')
            ->get();

        $pengaturanKantor = PengaturanKantor::aktif()->first();

        $statistikBulan = $this->getStatistikBulan($karyawan->id);

        return view('karyawan.absensi.index', compact(
            'karyawan',
            'presensiHariIni',
            'riwayatPresensi',
            'pengaturanKantor',
            'statistikBulan'
        ));
    }

    public function absenMasuk(Request $request)
    {
        try {
            $karyawan = auth()->guard('karyawan')->user();

            // Validasi input
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // Cek apakah sudah absen hari ini
            $presensiHariIni = Presensi::where('karyawan_id', $karyawan->id)
                ->whereDate('tanggal', today())
                ->first();

            if ($presensiHariIni && $presensiHariIni->jam_masuk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan absen masuk hari ini.'
                ], 400);
            }

            // Validasi lokasi jika ada pengaturan kantor
            $pengaturanKantor = PengaturanKantor::aktif()->first();
            if ($pengaturanKantor && $pengaturanKantor->latitude && $pengaturanKantor->longitude) {
                $distance = $this->calculateDistance(
                    $request->latitude,
                    $request->longitude,
                    $pengaturanKantor->latitude,
                    $pengaturanKantor->longitude
                );

                if ($distance > $pengaturanKantor->radius_meter) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda berada di luar jangkauan lokasi kantor.'
                    ], 400);
                }
            }

            // Upload foto ke folder yang diinginkan
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $fileName = 'masuk_' . $karyawan->id . '_' . date('Y-m-d_H-i-s') . '.' . $foto->getClientOriginalExtension();

                // Pastikan folder ada
                $uploadPath = public_path('storage/presensi/masuk');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Simpan file
                $foto->move($uploadPath, $fileName);
                $fotoPath = 'storage/presensi/masuk/' . $fileName;

                // Log untuk debugging
                Log::info('Foto absen masuk berhasil disimpan', [
                    'karyawan_id' => $karyawan->id,
                    'file_path' => $fotoPath,
                    'full_path' => public_path($fotoPath)
                ]);
            }

            // Simpan atau update presensi
            $presensi = $presensiHariIni ?: new Presensi();
            $presensi->karyawan_id = $karyawan->id;
            $presensi->tanggal = today();
            $presensi->jam_masuk = now();
            $presensi->foto_masuk = $fotoPath;
            $presensi->latitude_masuk = $request->latitude;
            $presensi->longitude_masuk = $request->longitude;
            $presensi->status = Presensi::STATUS_HADIR;
            $presensi->save();

            return response()->json([
                'success' => true,
                'message' => 'Absen masuk berhasil dicatat!',
                'data' => [
                    'jam_masuk' => $presensi->jam_masuk->format('H:i:s'),
                    'foto_url' => $fotoPath ? asset($fotoPath) : null,
                    'status' => $presensi->status_label
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error absen masuk: ' . $e->getMessage(), [
                'karyawan_id' => auth()->guard('karyawan')->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencatat absen masuk.'
            ], 500);
        }
    }

    public function absenPulang(Request $request)
    {
        try {
            $karyawan = auth()->guard('karyawan')->user();

            // Validasi input
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // Cek presensi hari ini
            $presensiHariIni = Presensi::where('karyawan_id', $karyawan->id)
                ->whereDate('tanggal', today())
                ->first();

            if (!$presensiHariIni || !$presensiHariIni->jam_masuk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum melakukan absen masuk hari ini.'
                ], 400);
            }

            if ($presensiHariIni->jam_pulang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan absen pulang hari ini.'
                ], 400);
            }

            // Upload foto ke folder yang diinginkan
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $fileName = 'keluar_' . $karyawan->id . '_' . date('Y-m-d_H-i-s') . '.' . $foto->getClientOriginalExtension();

                // Pastikan folder ada
                $uploadPath = public_path('storage/presensi/keluar');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Simpan file
                $foto->move($uploadPath, $fileName);
                $fotoPath = 'storage/presensi/keluar/' . $fileName;

                // Log untuk debugging
                Log::info('Foto absen pulang berhasil disimpan', [
                    'karyawan_id' => $karyawan->id,
                    'file_path' => $fotoPath,
                    'full_path' => public_path($fotoPath)
                ]);
            }

            // Update presensi
            $presensiHariIni->jam_pulang = now();
            $presensiHariIni->foto_pulang = $fotoPath;
            $presensiHariIni->latitude_pulang = $request->latitude;
            $presensiHariIni->longitude_pulang = $request->longitude;

            if ($request->has('keterangan')) {
                $presensiHariIni->keterangan = $request->keterangan;
            }

            $presensiHariIni->save();

            return response()->json([
                'success' => true,
                'message' => 'Absen pulang berhasil dicatat!',
                'data' => [
                    'jam_pulang' => $presensiHariIni->jam_pulang->format('H:i:s'),
                    'foto_url' => $fotoPath ? asset($fotoPath) : null,
                    'jam_kerja' => $presensiHariIni->jam_kerja_formatted
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error absen pulang: ' . $e->getMessage(), [
                'karyawan_id' => auth()->guard('karyawan')->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencatat absen pulang.'
            ], 500);
        }
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    private function getStatistikBulan($karyawanId)
    {
        $bulanIni = Presensi::where('karyawan_id', $karyawanId)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->get();

        return [
            'hadir' => $bulanIni->where('status', Presensi::STATUS_HADIR)->count(),
            'terlambat' => $bulanIni->where('status', Presensi::STATUS_TERLAMBAT)->count(),
            'tidak_hadir' => $bulanIni->where('status', Presensi::STATUS_TIDAK_HADIR)->count(),
            'izin' => $bulanIni->where('status', Presensi::STATUS_IZIN)->count(),
            'sakit' => $bulanIni->where('status', Presensi::STATUS_SAKIT)->count(),
            'cuti' => $bulanIni->where('status', Presensi::STATUS_CUTI)->count(),
        ];
    }
}
