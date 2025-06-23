<?php

namespace App\Http\Controllers;

use App\Models\Penggajian;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PenggajianController extends Controller
{
    public function print(Penggajian $penggajian)
    {
        if (!$penggajian->isDibayar()) {
            abort(403, 'Slip gaji hanya dapat dicetak untuk penggajian yang sudah dibayar.');
        }

        $pdf = Pdf::loadView('penggajian.slip', compact('penggajian'));

        $filename = "slip-gaji-{$penggajian->karyawan->nip}-{$penggajian->periode}.pdf";

        return $pdf->download($filename);
    }
}