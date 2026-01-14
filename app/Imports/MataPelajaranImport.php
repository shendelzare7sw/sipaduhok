<?php

namespace App\Imports;

use App\Models\MataPelajaran;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MataPelajaranImport implements ToCollection, WithHeadingRow
{
    private $skippedCount = 0;
    private $importedCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $row = $row->toArray();

            // Skip empty rows
            if (empty($row['nama_mapel']) || trim($row['nama_mapel']) === '') {
                continue;
            }

            // Validate jenjang
            $jenjang = strtoupper(trim($row['jenjang'] ?? ''));
            if (!in_array($jenjang, ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'])) {
                $this->skippedCount++;
                continue;
            }

            // Skip if already exists (by kode_mapel or nama_mapel+jenjang)
            $exists = false;
            if (!empty($row['kode_mapel'])) {
                $exists = MataPelajaran::where('kode_mapel', $row['kode_mapel'])->exists();
            }
            if (!$exists) {
                $exists = MataPelajaran::where('nama_mapel', $row['nama_mapel'])
                    ->where('jenjang', $jenjang)
                    ->exists();
            }

            if ($exists) {
                $this->skippedCount++;
                continue;
            }

            try {
                MataPelajaran::create([
                    'kode_mapel' => $row['kode_mapel'] ?? null,
                    'nama_mapel' => $row['nama_mapel'],
                    'jenjang' => $jenjang,
                    'deskripsi' => $row['deskripsi'] ?? null,
                ]);

                $this->importedCount++;
            } catch (\Exception $e) {
                $this->skippedCount++;
                \Log::error('MataPelajaran Import Error: ' . $e->getMessage() . ' | Row: ' . json_encode($row));
            }
        }
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
}
