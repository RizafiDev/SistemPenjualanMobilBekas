<?php

namespace App\Exports;

use App\Models\Presensi;
use App\Models\Karyawan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PresensiSummarySheet implements FromCollection, WithHeadings, WithColumnWidths, WithStyles, WithTitle
{
    protected $startDate;
    protected $endDate;
    protected $karyawanIds;
    protected $statusFilter;

    public function __construct($startDate = null, $endDate = null, $karyawanIds = null, $statusFilter = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->karyawanIds = $karyawanIds;
        $this->statusFilter = $statusFilter;
    }

    public function collection()
    {
        $query = Presensi::query()->with(['karyawan']);

        // Apply filters
        if ($this->startDate) {
            $query->whereDate('tanggal', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('tanggal', '<=', $this->endDate);
        }
        if ($this->karyawanIds) {
            $query->whereIn('karyawan_id', $this->karyawanIds);
        }
        if ($this->statusFilter) {
            $query->whereIn('status', $this->statusFilter);
        }

        $presensiData = $query->get();

        // Group by karyawan untuk membuat summary
        $summary = $presensiData->groupBy('karyawan_id')->map(function ($presensis, $karyawanId) {
            $karyawan = $presensis->first()->karyawan;

            $totalHadir = $presensis->where('status', Presensi::STATUS_HADIR)->count();
            $totalTerlambat = $presensis->where('status', Presensi::STATUS_TERLAMBAT)->count();
            $totalTidakHadir = $presensis->where('status', Presensi::STATUS_TIDAK_HADIR)->count();
            $totalSakit = $presensis->where('status', Presensi::STATUS_SAKIT)->count();
            $totalIzin = $presensis->where('status', Presensi::STATUS_IZIN)->count();
            $totalCuti = $presensis->where('status', Presensi::STATUS_CUTI)->count();

            $totalKehadiran = $totalHadir + $totalTerlambat;
            $totalPresensi = $presensis->count();
            $persentaseKehadiran = $totalPresensi > 0 ? round(($totalKehadiran / $totalPresensi) * 100, 2) : 0;

            // Hitung total jam kerja
            $totalJamKerja = $presensis->where('jam_kerja', '>', 0)->sum('jam_kerja');

            // Hitung rata-rata keterlambatan
            $totalMenitTerlambat = $presensis->sum('menit_terlambat');
            $rataRataTerlambat = $totalTerlambat > 0 ? round($totalMenitTerlambat / $totalTerlambat, 0) : 0;

            return [
                'nip' => $karyawan->nip ?? '-',
                'nama' => $karyawan->nama_lengkap ?? '-',
                'jabatan' => $karyawan->jabatan ?? '-',
                'departemen' => $karyawan->departemen ?? '-',
                'total_presensi' => $totalPresensi,
                'hadir' => $totalHadir,
                'terlambat' => $totalTerlambat,
                'tidak_hadir' => $totalTidakHadir,
                'sakit' => $totalSakit,
                'izin' => $totalIzin,
                'cuti' => $totalCuti,
                'total_kehadiran' => $totalKehadiran,
                'persentase_kehadiran' => $persentaseKehadiran . '%',
                'total_jam_kerja' => round($totalJamKerja, 2),
                'rata_rata_terlambat' => $rataRataTerlambat . ' menit',
            ];
        });

        // Add overall summary at the end
        $overallSummary = [
            'nip' => '',
            'nama' => 'TOTAL KESELURUHAN',
            'jabatan' => '',
            'departemen' => '',
            'total_presensi' => $summary->sum('total_presensi'),
            'hadir' => $summary->sum('hadir'),
            'terlambat' => $summary->sum('terlambat'),
            'tidak_hadir' => $summary->sum('tidak_hadir'),
            'sakit' => $summary->sum('sakit'),
            'izin' => $summary->sum('izin'),
            'cuti' => $summary->sum('cuti'),
            'total_kehadiran' => $summary->sum('total_kehadiran'),
            'persentase_kehadiran' => $summary->sum('total_presensi') > 0
                ? round(($summary->sum('total_kehadiran') / $summary->sum('total_presensi')) * 100, 2) . '%'
                : '0%',
            'total_jam_kerja' => round($summary->sum('total_jam_kerja'), 2),
            'rata_rata_terlambat' => $summary->avg('rata_rata_terlambat') . ' menit',
        ];

        return collect($summary->values())->push($overallSummary);
    }

    public function headings(): array
    {
        return [
            'NIP',
            'Nama Karyawan',
            'Jabatan',
            'Departemen',
            'Total Presensi',
            'Hadir',
            'Terlambat',
            'Tidak Hadir',
            'Sakit',
            'Izin',
            'Cuti',
            'Total Kehadiran',
            'Persentase Kehadiran',
            'Total Jam Kerja',
            'Rata-rata Terlambat',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // NIP
            'B' => 25, // Nama
            'C' => 20, // Jabatan
            'D' => 20, // Departemen
            'E' => 15, // Total Presensi
            'F' => 10, // Hadir
            'G' => 12, // Terlambat
            'H' => 12, // Tidak Hadir
            'I' => 10, // Sakit
            'J' => 10, // Izin
            'K' => 10, // Cuti
            'L' => 15, // Total Kehadiran
            'M' => 18, // Persentase Kehadiran
            'N' => 15, // Total Jam Kerja
            'O' => 18, // Rata-rata Terlambat
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
                'startColor' => ['rgb' => '059669'], // Green header
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
            $sheet->getStyle('A2:' . $lastColumn . ($lastRow - 1))->applyFromArray([
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

            // Style total row (last row)
            $sheet->getStyle('A' . $lastRow . ':' . $lastColumn . $lastRow)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1F2937'], // Dark background for total
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THICK,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);

            // Center align numeric columns
            $sheet->getStyle('E2:O' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
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
        }

        return 'Ringkasan Presensi' . $period;
    }
}
