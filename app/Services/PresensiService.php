<?php

// app/Services/PresensiService.php

namespace App\Services;

use App\Models\Presensi;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PresensiService
{
    public function generateLaporanHarian(Carbon $tanggal): Collection
    {
        $presensi = Presensi::with('karyawan')
            ->whereDate('tanggal', $tanggal)
            ->get();

        $karyawanAktif = Karyawan::where('aktif', 'aktif')->get();

        return $karyawanAktif->map(function ($karyawan) use ($presensi, $tanggal) {
            $presensiKaryawan = $presensi->where('karyawan_id', $karyawan->id)->first();

            return [
                'karyawan' => $karyawan,
                'presensi' => $presensiKaryawan,
                'status' => $presensiKaryawan
                    ? $presensiKaryawan->status
                    : Presensi::STATUS_TIDAK_HADIR,
                'jam_masuk' => $presensiKaryawan?->jam_masuk,
                'jam_pulang' => $presensiKaryawan?->jam_pulang,
                'jam_kerja' => $presensiKaryawan?->jam_kerja,
                'menit_terlambat' => $presensiKaryawan?->menit_terlambat ?? 0,
            ];
        });
    }

    public function generateLaporanBulanan(int $bulan, int $tahun): Collection
    {
        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $presensi = Presensi::with('karyawan')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->groupBy('karyawan_id');

        $karyawanAktif = Karyawan::where('aktif', 'aktif')->get();

        return $karyawanAktif->map(function ($karyawan) use ($presensi, $startDate, $endDate) {
            $presensiKaryawan = $presensi->get($karyawan->id, collect());

            $totalHadir = $presensiKaryawan->where('status', Presensi::STATUS_HADIR)->count();
            $totalTerlambat = $presensiKaryawan->where('status', Presensi::STATUS_TERLAMBAT)->count();
            $totalTidakHadir = $presensiKaryawan->where('status', Presensi::STATUS_TIDAK_HADIR)->count();
            $totalSakit = $presensiKaryawan->where('status', Presensi::STATUS_SAKIT)->count();
            $totalIzin = $presensiKaryawan->where('status', Presensi::STATUS_IZIN)->count();
            $totalCuti = $presensiKaryawan->where('status', Presensi::STATUS_CUTI)->count();

            $totalJamKerja = $presensiKaryawan->sum('jam_kerja');
            $totalMenitTerlambat = $presensiKaryawan->sum('menit_terlambat');

            $jumlahHariKerja = $this->hitungHariKerja($startDate, $endDate);
            $persentaseKehadiran = $jumlahHariKerja > 0
                ? round((($totalHadir + $totalTerlambat) / $jumlahHariKerja) * 100, 2)
                : 0;

            return [
                'karyawan' => $karyawan,
                'total_hadir' => $totalHadir,
                'total_terlambat' => $totalTerlambat,
                'total_tidak_hadir' => $totalTidakHadir,
                'total_sakit' => $totalSakit,
                'total_izin' => $totalIzin,
                'total_cuti' => $totalCuti,
                'total_jam_kerja' => $totalJamKerja,
                'total_menit_terlambat' => $totalMenitTerlambat,
                'persentase_kehadiran' => $persentaseKehadiran,
                'jumlah_hari_kerja' => $jumlahHariKerja,
            ];
        });
    }

    public function hitungStatistikHarian(Carbon $tanggal): array
    {
        $totalKaryawan = Karyawan::where('aktif', 'aktif')->count();

        $statistik = Presensi::whereDate('tanggal', $tanggal)
            ->selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status')
            ->toArray();

        $hadir = ($statistik[Presensi::STATUS_HADIR] ?? 0) + ($statistik[Presensi::STATUS_TERLAMBAT] ?? 0);
        $tidakHadir = $totalKaryawan - $hadir;

        return [
            'total_karyawan' => $totalKaryawan,
            'hadir' => $statistik[Presensi::STATUS_HADIR] ?? 0,
            'terlambat' => $statistik[Presensi::STATUS_TERLAMBAT] ?? 0,
            'tidak_hadir' => $tidakHadir,
            'sakit' => $statistik[Presensi::STATUS_SAKIT] ?? 0,
            'izin' => $statistik[Presensi::STATUS_IZIN] ?? 0,
            'cuti' => $statistik[Presensi::STATUS_CUTI] ?? 0,
            'libur' => $statistik[Presensi::STATUS_LIBUR] ?? 0,
            'persentase_kehadiran' => $totalKaryawan > 0 ? round(($hadir / $totalKaryawan) * 100, 2) : 0,
        ];
    }

    private function hitungHariKerja(Carbon $startDate, Carbon $endDate): int
    {
        $hariKerja = 0;
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            // Hitung hari kerja (Senin-Jumat)
            if (!$current->isWeekend()) {
                $hariKerja++;
            }
            $current->addDay();
        }

        return $hariKerja;
    }

    public function generateRekapPresensi(int $karyawanId, Carbon $startDate, Carbon $endDate): array
    {
        $presensi = Presensi::where('karyawan_id', $karyawanId)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal')
            ->get();

        $karyawan = Karyawan::find($karyawanId);

        $rekap = [];
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            $presensiHari = $presensi->where('tanggal', $current->toDateString())->first();

            $rekap[] = [
                'tanggal' => $current->copy(),
                'hari' => $current->dayName,
                'presensi' => $presensiHari,
                'status' => $presensiHari
                    ? $presensiHari->status
                    : ($current->isWeekend() ? 'libur' : Presensi::STATUS_TIDAK_HADIR),
                'jam_masuk' => $presensiHari?->jam_masuk,
                'jam_pulang' => $presensiHari?->jam_pulang,
                'jam_kerja' => $presensiHari?->jam_kerja,
                'menit_terlambat' => $presensiHari?->menit_terlambat ?? 0,
                'keterangan' => $presensiHari?->keterangan,
            ];

            $current->addDay();
        }

        // Hitung statistik
        $totalHadir = collect($rekap)->where('status', Presensi::STATUS_HADIR)->count();
        $totalTerlambat = collect($rekap)->where('status', Presensi::STATUS_TERLAMBAT)->count();
        $totalTidakHadir = collect($rekap)->where('status', Presensi::STATUS_TIDAK_HADIR)->count();
        $totalSakit = collect($rekap)->where('status', Presensi::STATUS_SAKIT)->count();
        $totalIzin = collect($rekap)->where('status', Presensi::STATUS_IZIN)->count();
        $totalCuti = collect($rekap)->where('status', Presensi::STATUS_CUTI)->count();
        $totalLibur = collect($rekap)->where('status', 'libur')->count();

        $totalJamKerja = collect($rekap)->sum('jam_kerja');
        $totalMenitTerlambat = collect($rekap)->sum('menit_terlambat');

        $jumlahHariKerja = $this->hitungHariKerja($startDate, $endDate);
        $persentaseKehadiran = $jumlahHariKerja > 0
            ? round((($totalHadir + $totalTerlambat) / $jumlahHariKerja) * 100, 2)
            : 0;

        return [
            'karyawan' => $karyawan,
            'periode' => [
                'start' => $startDate,
                'end' => $endDate,
                'jumlah_hari' => $startDate->diffInDays($endDate) + 1,
                'jumlah_hari_kerja' => $jumlahHariKerja,
            ],
            'rekap_harian' => $rekap,
            'statistik' => [
                'total_hadir' => $totalHadir,
                'total_terlambat' => $totalTerlambat,
                'total_tidak_hadir' => $totalTidakHadir,
                'total_sakit' => $totalSakit,
                'total_izin' => $totalIzin,
                'total_cuti' => $totalCuti,
                'total_libur' => $totalLibur,
                'total_jam_kerja' => $totalJamKerja,
                'total_menit_terlambat' => $totalMenitTerlambat,
                'persentase_kehadiran' => $persentaseKehadiran,
            ],
        ];
    }

    public function getPresensiByKaryawan(int $karyawanId, Carbon $tanggal): ?Presensi
    {
        return Presensi::where('karyawan_id', $karyawanId)
            ->whereDate('tanggal', $tanggal)
            ->first();
    }

    public function cekPresensiGanda(int $karyawanId, Carbon $tanggal): bool
    {
        return Presensi::where('karyawan_id', $karyawanId)
            ->whereDate('tanggal', $tanggal)
            ->exists();
    }

    public function createPresensiMasuk(array $data): Presensi
    {
        return Presensi::create([
            'karyawan_id' => $data['karyawan_id'],
            'tanggal' => $data['tanggal'] ?? now()->toDateString(),
            'jam_masuk' => $data['jam_masuk'] ?? now()->toTimeString(),
            'status' => $data['status'] ?? Presensi::STATUS_HADIR,
            'foto_masuk' => $data['foto_masuk'] ?? null,
            'latitude_masuk' => $data['latitude_masuk'] ?? null,
            'longitude_masuk' => $data['longitude_masuk'] ?? null,
            'keterangan' => $data['keterangan'] ?? null,
        ]);
    }

    public function updatePresensiPulang(Presensi $presensi, array $data): Presensi
    {
        $presensi->update([
            'jam_pulang' => $data['jam_pulang'] ?? now()->toTimeString(),
            'foto_pulang' => $data['foto_pulang'] ?? null,
            'latitude_pulang' => $data['latitude_pulang'] ?? null,
            'longitude_pulang' => $data['longitude_pulang'] ?? null,
        ]);

        return $presensi->fresh();
    }

    public function hitungJamKerjaEfektif(Carbon $jamMasuk, Carbon $jamPulang): float
    {
        // Hitung total jam kerja
        $totalJam = $jamPulang->diffInMinutes($jamMasuk) / 60;

        // Kurangi jam istirahat (misal 1 jam)
        $jamIstirahat = 1;

        // Jam kerja efektif minimal 0
        return max(0, $totalJam - $jamIstirahat);
    }

    public function hitungMenitTerlambat(Carbon $jamMasuk, string $jamKerjaMulai = '08:00'): int
    {
        $jamKerjaStart = Carbon::createFromFormat('H:i', $jamKerjaMulai, $jamMasuk->timezone);
        $jamKerjaStart->setDate($jamMasuk->year, $jamMasuk->month, $jamMasuk->day);

        if ($jamMasuk->gt($jamKerjaStart)) {
            return $jamMasuk->diffInMinutes($jamKerjaStart);
        }

        return 0;
    }

    public function generateLaporanTerlambat(Carbon $startDate, Carbon $endDate): Collection
    {
        return Presensi::with('karyawan')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->where('menit_terlambat', '>', 0)
            ->orderBy('tanggal', 'desc')
            ->orderBy('menit_terlambat', 'desc')
            ->get()
            ->map(function ($presensi) {
                return [
                    'karyawan' => $presensi->karyawan,
                    'tanggal' => $presensi->tanggal,
                    'jam_masuk' => $presensi->jam_masuk,
                    'menit_terlambat' => $presensi->menit_terlambat,
                    'keterangan_terlambat' => $presensi->keterangan_terlambat,
                    'status' => $presensi->status,
                ];
            });
    }

    public function generateLaporanAbsensi(Carbon $startDate, Carbon $endDate): Collection
    {
        $karyawanAktif = Karyawan::where('aktif', 'aktif')->get();
        $presensi = Presensi::whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->groupBy('karyawan_id');

        return $karyawanAktif->map(function ($karyawan) use ($presensi, $startDate, $endDate) {
            $presensiKaryawan = $presensi->get($karyawan->id, collect());

            $absensi = [];
            $current = $startDate->copy();

            while ($current->lte($endDate)) {
                if (!$current->isWeekend()) {
                    $presensiHari = $presensiKaryawan->where('tanggal', $current->toDateString())->first();

                    if (!$presensiHari) {
                        $absensi[] = [
                            'tanggal' => $current->copy(),
                            'hari' => $current->dayName,
                            'status' => 'tidak_hadir',
                        ];
                    } elseif (
                        in_array($presensiHari->status, [
                            Presensi::STATUS_TIDAK_HADIR,
                            Presensi::STATUS_SAKIT,
                            Presensi::STATUS_IZIN,
                            Presensi::STATUS_CUTI
                        ])
                    ) {
                        $absensi[] = [
                            'tanggal' => $current->copy(),
                            'hari' => $current->dayName,
                            'status' => $presensiHari->status,
                            'keterangan' => $presensiHari->keterangan,
                        ];
                    }
                }
                $current->addDay();
            }

            return [
                'karyawan' => $karyawan,
                'absensi' => $absensi,
                'total_absensi' => count($absensi),
            ];
        })->filter(function ($item) {
            return $item['total_absensi'] > 0;
        });
    }

    public function exportToExcel(Collection $data, string $filename): string
    {
        // Implementation untuk export Excel bisa menggunakan package seperti Laravel Excel
        // Ini adalah placeholder untuk implementasi export

        // Contoh struktur data yang akan di-export
        $exportData = $data->map(function ($item) {
            return [
                'Nama' => $item['karyawan']->nama_lengkap ?? '',
                'NIP' => $item['karyawan']->nip ?? '',
                'Tanggal' => $item['tanggal'] ?? '',
                'Jam Masuk' => $item['jam_masuk'] ?? '',
                'Jam Pulang' => $item['jam_pulang'] ?? '',
                'Jam Kerja' => $item['jam_kerja'] ?? '',
                'Status' => $item['status'] ?? '',
                'Terlambat (menit)' => $item['menit_terlambat'] ?? 0,
            ];
        });

        // Return path file yang sudah di-generate
        return storage_path("app/exports/{$filename}");
    }

    public function hitungLembur(Presensi $presensi, string $jamKerjaBerakhir = '17:00'): float
    {
        if (!$presensi->jam_pulang) {
            return 0;
        }

        $jamPulang = Carbon::parse($presensi->jam_pulang);
        $jamKerjaEnd = Carbon::createFromFormat('H:i', $jamKerjaBerakhir, $jamPulang->timezone);
        $jamKerjaEnd->setDate($jamPulang->year, $jamPulang->month, $jamPulang->day);

        if ($jamPulang->gt($jamKerjaEnd)) {
            return $jamPulang->diffInMinutes($jamKerjaEnd) / 60;
        }

        return 0;
    }

    public function getPerformanceKaryawan(int $karyawanId, int $bulan, int $tahun): array
    {
        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $presensi = Presensi::where('karyawan_id', $karyawanId)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();

        $karyawan = Karyawan::find($karyawanId);
        $jumlahHariKerja = $this->hitungHariKerja($startDate, $endDate);

        $totalHadir = $presensi->whereIn('status', [Presensi::STATUS_HADIR, Presensi::STATUS_TERLAMBAT])->count();
        $totalTerlambat = $presensi->where('status', Presensi::STATUS_TERLAMBAT)->count();
        $totalJamKerja = $presensi->sum('jam_kerja');
        $totalMenitTerlambat = $presensi->sum('menit_terlambat');

        $persentaseKehadiran = $jumlahHariKerja > 0 ? ($totalHadir / $jumlahHariKerja) * 100 : 0;
        $persentaseKeterlambatan = $totalHadir > 0 ? ($totalTerlambat / $totalHadir) * 100 : 0;
        $rataRataTerlambat = $totalTerlambat > 0 ? $totalMenitTerlambat / $totalTerlambat : 0;

        // Scoring performance (0-100)
        $scoreKehadiran = min(100, $persentaseKehadiran);
        $scoreKedisiplinan = max(0, 100 - $persentaseKeterlambatan);
        $scoreOverall = ($scoreKehadiran * 0.6) + ($scoreKedisiplinan * 0.4);

        return [
            'karyawan' => $karyawan,
            'periode' => "{$startDate->format('F Y')}",
            'statistik' => [
                'jumlah_hari_kerja' => $jumlahHariKerja,
                'total_hadir' => $totalHadir,
                'total_terlambat' => $totalTerlambat,
                'total_jam_kerja' => $totalJamKerja,
                'total_menit_terlambat' => $totalMenitTerlambat,
                'rata_rata_terlambat' => round($rataRataTerlambat, 2),
            ],
            'persentase' => [
                'kehadiran' => round($persentaseKehadiran, 2),
                'keterlambatan' => round($persentaseKeterlambatan, 2),
            ],
            'score' => [
                'kehadiran' => round($scoreKehadiran, 2),
                'kedisiplinan' => round($scoreKedisiplinan, 2),
                'overall' => round($scoreOverall, 2),
            ],
            'grade' => $this->getPerformanceGrade($scoreOverall),
        ];
    }

    private function getPerformanceGrade(float $score): string
    {
        if ($score >= 90)
            return 'A';
        if ($score >= 80)
            return 'B';
        if ($score >= 70)
            return 'C';
        if ($score >= 60)
            return 'D';
        return 'E';
    }
}