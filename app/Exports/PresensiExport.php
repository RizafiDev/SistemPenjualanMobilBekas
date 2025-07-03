<?php

namespace App\Exports;

use App\Models\Presensi;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class PresensiExport implements FromQuery, WithHeadings, WithMapping, WithColumnWidths, WithStyles, WithTitle
{
    protected $startDate;
    protected $endDate;
    protected $karyawanId;
    protected $status;

    public function __construct($startDate = null, $endDate = null, $karyawanId = null, $status = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->karyawanId = $karyawanId;
        $this->status = $status;
    }

    public function query()
    {
        $query = Presensi::query()
            ->with(['karyawan'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('karyawan_id');

        if ($this->startDate) {
            $query->whereDate('tanggal', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('tanggal', '<=', $this->endDate);
        }

        if ($this->karyawanId) {
            $query->where('karyawan_id', $this->karyawanId);
        }

        if ($this->status) {
            if (is_array($this->status)) {
                $query->whereIn('status', $this->status);
            } else {
                $query->where('status', $this->status);
            }
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'No',
            'NIP',
            'Nama Karyawan',
            'Jabatan',
            'Departemen',
            'Tanggal',
            'Hari',
            'Jam Masuk',
            'Jam Pulang',
            'Durasi Kerja (Jam:Menit)',
            'Jam Kerja Efektif (Jam)',
            'Status',
            'Terlambat (Menit)',
            'Keterangan Terlambat',
            'Lokasi Masuk (Lat, Long)',
            'Lokasi Pulang (Lat, Long)',
            'Keterangan',
            'Dicatat Pada',
        ];
    }

    public function map($presensi): array
    {
        static $no = 1;

        $tanggal = Carbon::parse($presensi->tanggal);
        $jamMasuk = $presensi->jam_masuk ? Carbon::parse($presensi->jam_masuk)->format('H:i:s') : '-';
        $jamPulang = $presensi->jam_pulang ? Carbon::parse($presensi->jam_pulang)->format('H:i:s') : '-';

        // Hitung durasi kerja
        $durasiKerja = '-';
        if ($presensi->jam_masuk && $presensi->jam_pulang) {
            $masuk = Carbon::parse($presensi->jam_masuk);
            $pulang = Carbon::parse($presensi->jam_pulang);

            // Handle overnight shifts
            if ($pulang->lt($masuk)) {
                $pulang->addDay();
            }

            $durasiMenit = $pulang->diffInMinutes($masuk);
            $jam = floor($durasiMenit / 60);
            $menit = $durasiMenit % 60;
            $durasiKerja = sprintf('%02d:%02d', $jam, $menit);
        }

        // Format lokasi
        $lokasiMasuk = '-';
        if ($presensi->latitude_masuk && $presensi->longitude_masuk) {
            $lokasiMasuk = $presensi->latitude_masuk . ', ' . $presensi->longitude_masuk;
        }

        $lokasiPulang = '-';
        if ($presensi->latitude_pulang && $presensi->longitude_pulang) {
            $lokasiPulang = $presensi->latitude_pulang . ', ' . $presensi->longitude_pulang;
        }

        // Format keterangan terlambat
        $keteranganTerlambat = '-';
        if ($presensi->menit_terlambat > 0) {
            $jamTerlambat = floor($presensi->menit_terlambat / 60);
            $menitTerlambat = $presensi->menit_terlambat % 60;
            if ($jamTerlambat > 0) {
                $keteranganTerlambat = "{$jamTerlambat} jam {$menitTerlambat} menit";
            } else {
                $keteranganTerlambat = "{$menitTerlambat} menit";
            }
        }

        return [
            $no++,
            $presensi->karyawan->nip ?? '-',
            $presensi->karyawan->nama_lengkap ?? '-',
            $presensi->karyawan->jabatan ?? '-',
            $presensi->karyawan->departemen ?? '-',
            $tanggal->format('d/m/Y'),
            $tanggal->format('l'),
            $jamMasuk,
            $jamPulang,
            $durasiKerja,
            $presensi->jam_kerja ?? '-',
            Presensi::getStatusOptions()[$presensi->status] ?? $presensi->status,
            $presensi->menit_terlambat ?? 0,
            $keteranganTerlambat,
            $lokasiMasuk,
            $lokasiPulang,
            $presensi->keterangan ?? '-',
            $presensi->created_at ? $presensi->created_at->format('d/m/Y H:i') : '-',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 15,  // NIP
            'C' => 25,  // Nama
            'D' => 20,  // Jabatan
            'E' => 20,  // Departemen
            'F' => 12,  // Tanggal
            'G' => 12,  // Hari
            'H' => 12,  // Jam Masuk
            'I' => 12,  // Jam Pulang
            'J' => 18,  // Durasi Kerja
            'K' => 18,  // Jam Kerja Efektif
            'L' => 15,  // Status
            'M' => 15,  // Terlambat
            'N' => 20,  // Keterangan Terlambat
            'O' => 25,  // Lokasi Masuk
            'P' => 25,  // Lokasi Pulang
            'Q' => 30,  // Keterangan
            'R' => 18,  // Dicatat Pada
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        // Style header
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Style data rows
        if ($lastRow > 1) {
            $sheet->getStyle('A2:' . $lastColumn . $lastRow)->applyFromArray([
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ]);

            // Center align for specific columns
            $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No
            $sheet->getStyle('F2:L' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Date columns
        }

        // Auto-fit row heights
        for ($row = 1; $row <= $lastRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(-1);
        }

        return [];
    }

    public function title(): string
    {
        $period = '';
        if ($this->startDate && $this->endDate) {
            $start = Carbon::parse($this->startDate)->format('d/m/Y');
            $end = Carbon::parse($this->endDate)->format('d/m/Y');
            $period = " ({$start} - {$end})";
        } elseif ($this->startDate) {
            $start = Carbon::parse($this->startDate)->format('d/m/Y');
            $period = " (dari {$start})";
        } elseif ($this->endDate) {
            $end = Carbon::parse($this->endDate)->format('d/m/Y');
            $period = " (sampai {$end})";
        }

        return 'Rekap Presensi' . $period;
    }
}
