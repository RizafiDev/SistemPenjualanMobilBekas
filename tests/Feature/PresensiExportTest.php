<?php

namespace Tests\Feature;

use App\Models\Presensi;
use App\Models\Karyawan;
use App\Exports\PresensiExport;
use App\Exports\PresensiAdvancedExport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;
use Carbon\Carbon;

class PresensiExportTest extends TestCase
{
    use RefreshDatabase;

    protected $karyawan;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test karyawan
        $this->karyawan = Karyawan::factory()->create([
            'nama_lengkap' => 'Test User',
            'nip' => '12345',
            'jabatan' => 'Developer',
            'departemen' => 'IT'
        ]);

        // Create test presensi data
        Presensi::factory()->count(5)->create([
            'karyawan_id' => $this->karyawan->id,
            'tanggal' => now()->subDays(rand(1, 30)),
            'status' => Presensi::STATUS_HADIR,
        ]);
    }

    /** @test */
    public function can_export_presensi_basic()
    {
        Excel::fake();

        $export = new PresensiExport(
            now()->startOfMonth(),
            now()->endOfMonth()
        );

        Excel::download($export, 'test_export.xlsx');

        Excel::assertDownloaded('test_export.xlsx', function (PresensiExport $export) {
            return true;
        });
    }

    /** @test */
    public function can_export_presensi_with_filters()
    {
        Excel::fake();

        $export = new PresensiExport(
            now()->startOfMonth(),
            now()->endOfMonth(),
            [$this->karyawan->id],
            [Presensi::STATUS_HADIR]
        );

        Excel::download($export, 'filtered_export.xlsx');

        Excel::assertDownloaded('filtered_export.xlsx');
    }

    /** @test */
    public function can_export_advanced_with_multiple_sheets()
    {
        Excel::fake();

        $export = new PresensiAdvancedExport(
            now()->startOfMonth(),
            now()->endOfMonth(),
            null,
            null,
            true // include summary
        );

        Excel::download($export, 'advanced_export.xlsx');

        Excel::assertDownloaded('advanced_export.xlsx', function (PresensiAdvancedExport $export) {
            // Check if multiple sheets are included
            $sheets = $export->sheets();
            return count($sheets) >= 2; // Data sheet + Summary sheet
        });
    }

    /** @test */
    public function export_has_correct_headings()
    {
        $export = new PresensiExport();
        $headings = $export->headings();

        $expectedHeadings = [
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

        $this->assertEquals($expectedHeadings, $headings);
    }

    /** @test */
    public function export_filters_by_date_range()
    {
        // Create presensi with specific dates
        $oldPresensi = Presensi::factory()->create([
            'karyawan_id' => $this->karyawan->id,
            'tanggal' => now()->subMonths(2),
        ]);

        $recentPresensi = Presensi::factory()->create([
            'karyawan_id' => $this->karyawan->id,
            'tanggal' => now(),
        ]);

        $export = new PresensiExport(
            now()->startOfMonth(),
            now()->endOfMonth()
        );

        $query = $export->query();
        $results = $query->get();

        // Should only include recent presensi within date range
        $this->assertTrue($results->contains('id', $recentPresensi->id));
        $this->assertFalse($results->contains('id', $oldPresensi->id));
    }

    /** @test */
    public function export_title_includes_period()
    {
        $startDate = '2025-01-01';
        $endDate = '2025-01-31';

        $export = new PresensiExport($startDate, $endDate);
        $title = $export->title();

        $this->assertStringContainsString('01/01/2025', $title);
        $this->assertStringContainsString('31/01/2025', $title);
    }

    /** @test */
    public function export_data_mapping_is_correct()
    {
        $presensi = Presensi::factory()->create([
            'karyawan_id' => $this->karyawan->id,
            'tanggal' => '2025-01-15',
            'jam_masuk' => '08:00:00',
            'jam_pulang' => '17:00:00',
            'status' => Presensi::STATUS_HADIR,
            'menit_terlambat' => 0,
        ]);

        $export = new PresensiExport();
        $mappedData = $export->map($presensi);

        // Check basic mapping
        $this->assertEquals($this->karyawan->nip, $mappedData[1]); // NIP
        $this->assertEquals($this->karyawan->nama_lengkap, $mappedData[2]); // Nama
        $this->assertEquals('15/01/2025', $mappedData[5]); // Tanggal
        $this->assertEquals('08:00:00', $mappedData[7]); // Jam Masuk
        $this->assertEquals('17:00:00', $mappedData[8]); // Jam Pulang
    }
}

/**
 * Example Usage Documentation
 * 
 * 1. Basic Export:
 *    $export = new PresensiExport(now()->startOfMonth(), now()->endOfMonth());
 *    return Excel::download($export, 'presensi.xlsx');
 * 
 * 2. Filtered Export:
 *    $export = new PresensiExport(
 *        '2025-01-01',
 *        '2025-01-31',
 *        [1, 2, 3], // karyawan IDs
 *        ['hadir', 'terlambat'] // status filter
 *    );
 *    return Excel::download($export, 'presensi_filtered.xlsx');
 * 
 * 3. Advanced Export with Multiple Sheets:
 *    $export = new PresensiAdvancedExport(
 *        '2025-01-01',
 *        '2025-01-31',
 *        null, // all karyawan
 *        null, // all status
 *        true  // include summary sheet
 *    );
 *    return Excel::download($export, 'presensi_advanced.xlsx');
 * 
 * 4. Quick Export (Current Month):
 *    $export = new PresensiExport(now()->startOfMonth(), now()->endOfMonth());
 *    return Excel::download($export, 'presensi_bulan_ini.xlsx');
 */
