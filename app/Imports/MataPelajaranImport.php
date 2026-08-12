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
    private $warnings = [];

    public function collection(Collection $rows)
    {
        $rowNumber = 1;

        foreach ($rows as $row) {
            $rowNumber++;
            $row = $row->toArray();

            $kodeMapel = trim((string) ($row['kode_mapel'] ?? ''));
            $namaMapel = trim((string) ($row['nama_mapel'] ?? ''));
            $jenjang = strtoupper(trim((string) ($row['jenjang'] ?? '')));
            $deskripsi = trim((string) ($row['deskripsi'] ?? ''));

            if ($namaMapel === '') {
                continue;
            }

            if ($kodeMapel === '') {
                $this->skippedCount++;
                $this->warnings[] = "Baris {$rowNumber}: Mata pelajaran '{$namaMapel}' dilewati karena kode_mapel wajib diisi.";
                continue;
            }

            if (! in_array($jenjang, ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'], true)) {
                $this->skippedCount++;
                $this->warnings[] = "Baris {$rowNumber}: Mata pelajaran '{$namaMapel}' dilewati karena jenjang harus KB, TKA, TKB, SD, SMP, atau SMA.";
                continue;
            }

            $filterAgama = $this->normalizeFilterAgama($row['filter_agama'] ?? null);
            if (! empty($row['filter_agama']) && ! $filterAgama) {
                $this->skippedCount++;
                $this->warnings[] = "Baris {$rowNumber}: Mata pelajaran '{$namaMapel}' dilewati karena filter_agama harus Islam, Kristen, Katolik, Hindu, Buddha, atau Konghucu.";
                continue;
            }

            if (MataPelajaran::where('kode_mapel', $kodeMapel)->exists()) {
                $this->skippedCount++;
                $this->warnings[] = "Baris {$rowNumber}: Mata pelajaran '{$namaMapel}' dilewati karena kode_mapel '{$kodeMapel}' sudah ada.";
                continue;
            }

            if (MataPelajaran::where('nama_mapel', $namaMapel)->where('jenjang', $jenjang)->exists()) {
                $this->skippedCount++;
                $this->warnings[] = "Baris {$rowNumber}: Mata pelajaran '{$namaMapel}' jenjang {$jenjang} dilewati karena sudah ada.";
                continue;
            }

            try {
                MataPelajaran::create([
                    'kode_mapel' => $kodeMapel,
                    'nama_mapel' => $namaMapel,
                    'jenjang' => $jenjang,
                    'filter_agama' => $filterAgama,
                    'deskripsi' => $deskripsi !== '' ? $deskripsi : null,
                ]);

                $this->importedCount++;
            } catch (\Exception $e) {
                $this->skippedCount++;
                $this->warnings[] = "Baris {$rowNumber}: Error - ".$e->getMessage();
                \Log::error('MataPelajaran Import Error: '.$e->getMessage().' | Row: '.json_encode($row));
            }
        }
    }

    private function normalizeFilterAgama($value): ?string
    {
        $value = trim((string) ($value ?? ''));
        if ($value === '') {
            return null;
        }

        foreach (MataPelajaran::AGAMA_FILTERS as $agama) {
            if (strcasecmp($value, $agama) === 0) {
                return $agama;
            }
        }

        return null;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getWarnings(): array
    {
        return $this->warnings;
    }
}
