<?php

// File: app/Observers/PengajuanCutiObserver.php

namespace App\Observers;

use App\Models\PengajuanCuti;
use Illuminate\Support\Facades\Log;

class PengajuanCutiObserver
{
    /**
     * Handle the PengajuanCuti "creating" event.
     */
    public function creating(PengajuanCuti $pengajuanCuti): void
    {
        // Auto calculate jumlah_hari jika belum diset
        if (!$pengajuanCuti->jumlah_hari && $pengajuanCuti->tanggal_mulai && $pengajuanCuti->tanggal_selesai) {
            $startDate = \Carbon\Carbon::parse($pengajuanCuti->tanggal_mulai);
            $endDate = \Carbon\Carbon::parse($pengajuanCuti->tanggal_selesai);
            $pengajuanCuti->jumlah_hari = $startDate->diffInDays($endDate) + 1;
        }

        // Set default status jika belum ada
        if (!$pengajuanCuti->status) {
            $pengajuanCuti->status = PengajuanCuti::STATUS_MENUNGGU;
        }
    }

    /**
     * Handle the PengajuanCuti "created" event.
     */
    public function created(PengajuanCuti $pengajuanCuti): void
    {
        Log::info('Pengajuan cuti baru dibuat', [
            'id' => $pengajuanCuti->id,
            'karyawan_id' => $pengajuanCuti->karyawan_id,
            'jenis' => $pengajuanCuti->jenis,
            'tanggal_mulai' => $pengajuanCuti->tanggal_mulai,
            'jumlah_hari' => $pengajuanCuti->jumlah_hari,
        ]);

        // Kirim notifikasi ke admin/manager (implementasi sesuai kebutuhan)
        // $this->notifyAdmins($pengajuanCuti);
    }

    /**
     * Handle the PengajuanCuti "updating" event.
     */
    public function updating(PengajuanCuti $pengajuanCuti): void
    {
        // Recalculate jumlah_hari jika tanggal berubah
        if ($pengajuanCuti->isDirty(['tanggal_mulai', 'tanggal_selesai'])) {
            if ($pengajuanCuti->tanggal_mulai && $pengajuanCuti->tanggal_selesai) {
                $startDate = \Carbon\Carbon::parse($pengajuanCuti->tanggal_mulai);
                $endDate = \Carbon\Carbon::parse($pengajuanCuti->tanggal_selesai);
                $pengajuanCuti->jumlah_hari = $startDate->diffInDays($endDate) + 1;
            }
        }

        // Set tanggal_persetujuan jika status berubah
        if (
            $pengajuanCuti->isDirty('status') &&
            in_array($pengajuanCuti->status, [PengajuanCuti::STATUS_DISETUJUI, PengajuanCuti::STATUS_DITOLAK]) &&
            !$pengajuanCuti->tanggal_persetujuan
        ) {
            $pengajuanCuti->tanggal_persetujuan = now();
        }
    }

    /**
     * Handle the PengajuanCuti "updated" event.
     */
    public function updated(PengajuanCuti $pengajuanCuti): void
    {
        if ($pengajuanCuti->wasChanged('status')) {
            Log::info('Status pengajuan cuti diubah', [
                'id' => $pengajuanCuti->id,
                'status_lama' => $pengajuanCuti->getOriginal('status'),
                'status_baru' => $pengajuanCuti->status,
                'disetujui_oleh' => $pengajuanCuti->disetujui_oleh,
            ]);

            // Kirim notifikasi ke karyawan (implementasi sesuai kebutuhan)
            // $this->notifyKaryawan($pengajuanCuti);
        }
    }

    /**
     * Handle the PengajuanCuti "deleted" event.
     */
    public function deleted(PengajuanCuti $pengajuanCuti): void
    {
        Log::info('Pengajuan cuti dihapus', [
            'id' => $pengajuanCuti->id,
            'karyawan_id' => $pengajuanCuti->karyawan_id,
        ]);
    }

    /**
     * Handle the PengajuanCuti "restored" event.
     */
    public function restored(PengajuanCuti $pengajuanCuti): void
    {
        Log::info('Pengajuan cuti dipulihkan', [
            'id' => $pengajuanCuti->id,
            'karyawan_id' => $pengajuanCuti->karyawan_id,
        ]);
    }

    /**
     * Handle the PengajuanCuti "force deleted" event.
     */
    public function forceDeleted(PengajuanCuti $pengajuanCuti): void
    {
        Log::info('Pengajuan cuti dihapus permanen', [
            'id' => $pengajuanCuti->id,
            'karyawan_id' => $pengajuanCuti->karyawan_id,
        ]);
    }
}