<?php

namespace App\Http\Controllers;

use App\Models\Penggajian;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PenggajianController extends Controller
{
    /**
     * Get company information from config
     */
    private function getCompanyInfo(): array
    {
        return [
            'name' => config('app.company_name', 'AUTO DEALER SYSTEM'),
            'address' => config('app.company_address', 'Jl. Raya No. 123, Kota, Provinsi'),
            'phone' => config('app.company_phone', '(021) 1234-5678'),
            'email' => config('app.company_email', 'info@autodealer.com'),
            'website' => config('app.company_website', 'www.autodealer.com'),
            'logo' => config('app.company_logo', null),
        ];
    }

    /**
     * Preview payslip
     */
    public function preview(Penggajian $penggajian)
    {
        if (!$penggajian->isDibayar()) {
            abort(403, 'Slip gaji hanya dapat dicetak untuk penggajian yang sudah dibayar.');
        }

        $penggajian->load('karyawan');

        $data = [
            'penggajian' => $penggajian,
            'company' => $this->getCompanyInfo(),
            'printDate' => now()->format('d F Y H:i:s'),
            'isPreview' => true,
        ];

        return view('penggajian.slip', $data);
    }

    /**
     * Print payslip
     */
    public function print(Penggajian $penggajian)
    {
        if (!$penggajian->isDibayar()) {
            abort(403, 'Slip gaji hanya dapat dicetak untuk penggajian yang sudah dibayar.');
        }

        $penggajian->load('karyawan');

        $data = [
            'penggajian' => $penggajian,
            'company' => $this->getCompanyInfo(),
            'printDate' => now()->format('d F Y H:i:s'),
        ];

        return view('penggajian.slip', $data);
    }

    /**
     * Download payslip as PDF
     */
    public function download(Penggajian $penggajian)
    {
        if (!$penggajian->isDibayar()) {
            abort(403, 'Slip gaji hanya dapat dicetak untuk penggajian yang sudah dibayar.');
        }

        $penggajian->load('karyawan');

        $data = [
            'penggajian' => $penggajian,
            'company' => $this->getCompanyInfo(),
            'printDate' => now()->format('d F Y H:i:s'),
        ];

        $pdf = Pdf::loadView('penggajian.slip', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = "slip-gaji-{$penggajian->karyawan->nip}-{$penggajian->periode}.pdf";

        return $pdf->download($filename);
    }
}