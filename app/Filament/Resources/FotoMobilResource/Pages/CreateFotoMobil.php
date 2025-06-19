<?php

namespace App\Filament\Resources\FotoMobilResource\Pages;

use App\Filament\Resources\FotoMobilResource;
use App\Models\FotoMobil;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateFotoMobil extends CreateRecord
{
    protected static string $resource = FotoMobilResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Simpan data files untuk diproses nanti
        $this->uploadedFiles = $data['files'] ?? [];

        // Hapus field files dari data utama karena tidak ada di database
        unset($data['files']);
        unset($data['urutan_tampil_start']);

        return $data;
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Jika tidak ada file yang diupload, buat record kosong
        if (empty($this->uploadedFiles)) {
            return FotoMobil::create($data);
        }

        $createdRecords = [];
        $urutanStart = $this->data['urutan_tampil_start'] ?? 0;

        // Loop untuk setiap file yang diupload
        foreach ($this->uploadedFiles as $index => $filePath) {
            $recordData = $data;
            $recordData['path_file'] = $filePath;
            $recordData['urutan_tampil'] = $urutanStart + $index;

            // Tambahkan nomor urut ke alt text jika ada
            if (!empty($data['teks_alternatif'])) {
                $recordData['teks_alternatif'] = $data['teks_alternatif'] . ' ' . ($index + 1);
            }

            $record = FotoMobil::create($recordData);
            $createdRecords[] = $record;
        }

        // Kirim notifikasi sukses
        Notification::make()
            ->title('Berhasil!')
            ->body('Berhasil menambahkan ' . count($createdRecords) . ' foto mobil.')
            ->success()
            ->send();

        // Return record pertama untuk keperluan redirect
        return $createdRecords[0] ?? new FotoMobil();
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return null; // Disable default notification karena kita pakai custom
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    private $uploadedFiles = [];
}