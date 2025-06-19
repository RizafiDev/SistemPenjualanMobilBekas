<?php

namespace App\Traits;

use App\Models\FotoMobil;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait BulkUploadTrait
{
    /**
     * Process bulk upload with transaction
     */
    public function processBulkUpload(array $formData): array
    {
        $files = $formData['files'] ?? [];
        $createdRecords = [];
        $failedFiles = [];

        if (empty($files)) {
            return [
                'success' => false,
                'message' => 'No files provided for upload',
                'created_count' => 0,
                'failed_count' => 0
            ];
        }

        DB::beginTransaction();

        try {
            $urutanStart = $formData['urutan_tampil_start'] ?? 1;
            $baseAltText = $formData['teks_alternatif_base'] ?? '';

            // Get next order numbers efficiently
            $orderNumbers = FotoMobil::getNextOrderNumbers(
                $formData['mobil_id'],
                $formData['jenis_media'],
                count($files)
            );

            foreach ($files as $index => $filePath) {
                try {
                    $recordData = [
                        'mobil_id' => $formData['mobil_id'],
                        'path_file' => $filePath,
                        'jenis_media' => $formData['jenis_media'],
                        'jenis_gambar' => $formData['jenis_gambar'] ?? null,
                        'urutan_tampil' => $orderNumbers[$index] ?? ($urutanStart + $index),
                        'keterangan' => $formData['keterangan'] ?? null,
                    ];

                    // Generate alt text dengan nomor urut
                    if (!empty($baseAltText)) {
                        $recordData['teks_alternatif'] = $baseAltText . ' ' . ($index + 1);
                    }

                    $record = FotoMobil::create($recordData);
                    $createdRecords[] = $record;

                } catch (\Exception $e) {
                    Log::error('Failed to create FotoMobil record', [
                        'file' => $filePath,
                        'error' => $e->getMessage(),
                        'data' => $recordData ?? null
                    ]);

                    $failedFiles[] = [
                        'file' => basename($filePath),
                        'error' => $e->getMessage()
                    ];
                }
            }

            DB::commit();

            // Send notification
            $this->sendBulkUploadNotification(
                count($createdRecords),
                count($failedFiles),
                $failedFiles
            );

            return [
                'success' => true,
                'message' => 'Bulk upload completed',
                'created_count' => count($createdRecords),
                'failed_count' => count($failedFiles),
                'created_records' => $createdRecords,
                'failed_files' => $failedFiles
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Bulk upload transaction failed', [
                'error' => $e->getMessage(),
                'form_data' => $formData
            ]);

            Notification::make()
                ->title('Upload Gagal!')
                ->body('Terjadi kesalahan saat mengupload file: ' . $e->getMessage())
                ->danger()
                ->send();

            return [
                'success' => false,
                'message' => 'Transaction failed: ' . $e->getMessage(),
                'created_count' => 0,
                'failed_count' => count($files)
            ];
        }
    }

    /**
     * Send notification for bulk upload result
     */
    private function sendBulkUploadNotification(int $successCount, int $failedCount, array $failedFiles): void
    {
        if ($failedCount === 0) {
            Notification::make()
                ->title('Upload Berhasil!')
                ->body("Berhasil mengupload {$successCount} file.")
                ->success()
                ->duration(5000)
                ->send();
        } elseif ($successCount > 0) {
            $failedFileNames = collect($failedFiles)->pluck('file')->take(3)->join(', ');
            $moreText = count($failedFiles) > 3 ? ' dan ' . (count($failedFiles) - 3) . ' lainnya' : '';

            Notification::make()
                ->title('Upload Sebagian Berhasil')
                ->body("Berhasil: {$successCount} file. Gagal: {$failedCount} file ({$failedFileNames}{$moreText})")
                ->warning()
                ->duration(8000)
                ->send();
        } else {
            Notification::make()
                ->title('Upload Gagal!')
                ->body("Semua {$failedCount} file gagal diupload.")
                ->danger()
                ->duration(8000)
                ->send();
        }
    }

    /**
     * Validate file types and sizes before processing
     */
    public function validateBulkFiles(array $files, array $allowedTypes = [], int $maxSizePerFile = 52428800): array
    {
        $validFiles = [];
        $invalidFiles = [];

        $defaultAllowedTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'video/mp4',
            'video/mov',
            'video/avi',
            'application/pdf'
        ];

        $allowedTypes = !empty($allowedTypes) ? $allowedTypes : $defaultAllowedTypes;

        foreach ($files as $file) {
            // Check if file exists
            $filePath = storage_path('app/public/' . $file);
            if (!file_exists($filePath)) {
                $invalidFiles[] = [
                    'file' => $file,
                    'error' => 'File not found'
                ];
                continue;
            }

            // Check file size
            $fileSize = filesize($filePath);
            if ($fileSize > $maxSizePerFile) {
                $invalidFiles[] = [
                    'file' => $file,
                    'error' => 'File size exceeds limit (' . ($maxSizePerFile / 1024 / 1024) . 'MB)'
                ];
                continue;
            }

            // Check file type
            $mimeType = mime_content_type($filePath);
            if (!in_array($mimeType, $allowedTypes)) {
                $invalidFiles[] = [
                    'file' => $file,
                    'error' => 'Invalid file type: ' . $mimeType
                ];
                continue;
            }

            $validFiles[] = $file;
        }

        return [
            'valid_files' => $validFiles,
            'invalid_files' => $invalidFiles
        ];
    }

    /**
     * Clean up orphaned files
     */
    public function cleanupOrphanedFiles(): int
    {
        $cleanedCount = 0;

        try {
            // Get all file paths from database
            $dbFiles = FotoMobil::whereNotNull('path_file')
                ->pluck('path_file')
                ->toArray();

            // Get all files in storage directory
            $storageFiles = \Storage::disk('public')->files('mobil-media');

            // Find orphaned files
            $orphanedFiles = array_diff($storageFiles, $dbFiles);

            foreach ($orphanedFiles as $orphanedFile) {
                if (\Storage::disk('public')->delete($orphanedFile)) {
                    $cleanedCount++;
                }
            }

            if ($cleanedCount > 0) {
                Log::info("Cleaned up {$cleanedCount} orphaned files");
            }

        } catch (\Exception $e) {
            Log::error('Failed to cleanup orphaned files: ' . $e->getMessage());
        }

        return $cleanedCount;
    }
}