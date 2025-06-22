<?php

// app/Observers/PresensiObserver.php

namespace App\Observers;

use App\Models\Presensi;
use Illuminate\Support\Facades\Storage;

class PresensiObserver
{
    public function creating(Presensi $presensi): void
    {
        // Auto-calculate jam kerja dan menit terlambat saat membuat presensi baru
        if ($presensi->jam_masuk && $presensi->jam_pulang) {
            $presensi->hitungJamKerja();
        }

        if ($presensi->jam_masuk) {
            $presensi->hitungTerlambat();
        }
    }

    public function updating(Presensi $presensi): void
    {
        // Auto-calculate jam kerja dan menit terlambat saat update
        if ($presensi->isDirty(['jam_masuk', 'jam_pulang'])) {
            $presensi->hitungJamKerja();
        }

        if ($presensi->isDirty('jam_masuk')) {
            $presensi->hitungTerlambat();
        }
    }

    public function deleting(Presensi $presensi): void
    {
        // Hapus foto saat presensi dihapus
        if ($presensi->foto_masuk && Storage::exists($presensi->foto_masuk)) {
            Storage::delete($presensi->foto_masuk);
        }

        if ($presensi->foto_pulang && Storage::exists($presensi->foto_pulang)) {
            Storage::delete($presensi->foto_pulang);
        }
    }
}