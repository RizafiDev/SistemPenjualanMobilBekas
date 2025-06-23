<?php

// app/Observers/PresensiObserver.php

namespace App\Observers;

use App\Models\Presensi;
use App\Models\PengaturanKantor;
use Illuminate\Support\Facades\Storage;

class PresensiObserver
{
    public function creating(Presensi $presensi): void
    {
        // Hitung terlambat saat jam_masuk ada
        if ($presensi->jam_masuk) {
            $this->hitungTerlambat($presensi);
        }

        // Hitung jam kerja saat jam_masuk dan jam_pulang ada
        if ($presensi->jam_masuk && $presensi->jam_pulang) {
            $presensi->hitungJamKerja();
        }
    }

    public function updating(Presensi $presensi): void
    {
        // Hitung ulang terlambat jika jam_masuk berubah
        if ($presensi->isDirty('jam_masuk') && $presensi->jam_masuk) {
            $this->hitungTerlambat($presensi);
        }

        // Hitung ulang jam kerja jika jam_masuk atau jam_pulang berubah
        if ($presensi->isDirty(['jam_masuk', 'jam_pulang'])) {
            if ($presensi->jam_masuk && $presensi->jam_pulang) {
                $presensi->hitungJamKerja();
            } else {
                $presensi->jam_kerja = null;
            }
        }
    }

    public function deleting(Presensi $presensi): void
    {
        // Hapus foto saat presensi dihapus
        if ($presensi->foto_masuk && Storage::exists('public/' . $presensi->foto_masuk)) {
            Storage::delete('public/' . $presensi->foto_masuk);
        }

        if ($presensi->foto_pulang && Storage::exists('public/' . $presensi->foto_pulang)) {
            Storage::delete('public/' . $presensi->foto_pulang);
        }
    }

    private function hitungTerlambat(Presensi $presensi): void
    {
        $pengaturanKantor = PengaturanKantor::aktif()->first();
        $jamMasukStandar = '08:00:00'; // Default
        $toleransiMenit = 0; // Default

        if ($pengaturanKantor) {
            if ($pengaturanKantor->jam_masuk) {
                $jamMasukStandar = $pengaturanKantor->jam_masuk->format('H:i:s');
            }
            if (isset($pengaturanKantor->toleransi_terlambat)) {
                $toleransiMenit = (int) $pengaturanKantor->toleransi_terlambat;
            }
        }

        $presensi->hitungTerlambat($jamMasukStandar, $toleransiMenit);
    }
}