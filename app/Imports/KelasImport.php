<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\Cabang;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KelasImport implements ToCollection, WithHeadingRow
{
    private $skippedCount = 0;
    private $importedCount = 0;
    private $cabangs;
    private $tahunAjarans;
    private $waliKelas;
    private $forcedCabangId;

    // Tracking missing entities
    private $missingCabang = [];
    private $missingWaliKelas = [];
    private $warnings = [];

    public function __construct(?int $forcedCabangId = null)
    {
        $this->forcedCabangId = $forcedCabangId;
        $this->cabangs = Cabang::when($forcedCabangId, fn($query) => $query->where('id', $forcedCabangId))
            ->pluck('id', 'nama_cabang')
            ->toArray();
        $this->tahunAjarans = TahunAjaran::pluck('id', 'nama_tahun_ajaran')->toArray();
        $this->waliKelas = TenagaPendidik::when($forcedCabangId, function ($query) use ($forcedCabangId) {
            $query->whereHas('user', fn($userQuery) => $userQuery->where('cabang_id', $forcedCabangId));
        })->pluck('id', 'nama_lengkap')->toArray();
    }

    public function collection(Collection $rows)
    {
        $rowNumber = 1;

        foreach ($rows as $row) {
            $rowNumber++;
            $row = $row->toArray();

            // Skip empty rows
            if (empty($row['nama_kelas']) || trim($row['nama_kelas']) === '') {
                continue;
            }

            // Validate jenjang
            $jenjang = strtoupper(trim($row['jenjang'] ?? ''));
            if (!in_array($jenjang, ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'])) {
                $this->warnings[] = "Baris {$rowNumber}: Jenjang '{$row['jenjang']}' tidak valid";
                $this->skippedCount++;
                continue;
            }

            // Lookup cabang (optional - kelas tetap dibuat)
            $cabangId = $this->forcedCabangId;
            if (!$cabangId && !empty($row['nama_cabang'])) {
                $cabangId = $this->findCabang($row['nama_cabang']);
                if (!$cabangId) {
                    $cabangName = trim($row['nama_cabang']);
                    if (!in_array($cabangName, $this->missingCabang)) {
                        $this->missingCabang[] = $cabangName;
                    }
                    $this->warnings[] = "Baris {$rowNumber}: Cabang '{$cabangName}' tidak ditemukan";
                }
            }

            // Lookup tahun ajaran
            $tahunAjaranId = null;
            if (!empty($row['nama_tahun_ajaran'])) {
                $tahunAjaranId = $this->findTahunAjaran($row['nama_tahun_ajaran']);
            }
            if (!$tahunAjaranId) {
                $activeTahun = TahunAjaran::where('is_active', true)->first();
                $tahunAjaranId = $activeTahun ? $activeTahun->id : null;
            }

            // Lookup wali kelas (optional - kelas tetap dibuat)
            $waliKelasId = null;
            if (!empty($row['nama_wali_kelas'])) {
                $waliKelasId = $this->findWaliKelas($row['nama_wali_kelas']);
                if (!$waliKelasId) {
                    $waliName = trim($row['nama_wali_kelas']);
                    if (!in_array($waliName, $this->missingWaliKelas)) {
                        $this->missingWaliKelas[] = $waliName;
                    }
                    $this->warnings[] = "Baris {$rowNumber}: Wali Kelas '{$waliName}' tidak ditemukan, kelas dibuat tanpa wali";
                }
            }

            // Skip if class already exists
            $exists = Kelas::where('nama_kelas', $row['nama_kelas'])
                ->where('jenjang', $jenjang)
                ->where('tahun_ajaran_id', $tahunAjaranId)
                ->exists();

            if ($exists) {
                $this->skippedCount++;
                continue;
            }

            try {
                Kelas::create([
                    'nama_kelas' => $row['nama_kelas'],
                    'kode_kelas' => $row['kode_kelas'] ?? null,
                    'jenjang' => $jenjang,
                    'kuota_siswa' => $row['kuota_siswa'] ?? 30,
                    'cabang_id' => $cabangId,
                    'tahun_ajaran_id' => $tahunAjaranId,
                    'wali_kelas_id' => $waliKelasId,
                ]);

                $this->importedCount++;
            } catch (\Exception $e) {
                $this->skippedCount++;
                $this->warnings[] = "Baris {$rowNumber}: Error - " . $e->getMessage();
            }
        }
    }

    private function findCabang($name)
    {
        $name = trim($name);
        if (isset($this->cabangs[$name]))
            return $this->cabangs[$name];
        foreach ($this->cabangs as $n => $id) {
            if (strtolower(trim($n)) === strtolower($name))
                return $id;
        }
        return null;
    }

    private function findTahunAjaran($name)
    {
        $name = trim($name);
        if (isset($this->tahunAjarans[$name]))
            return $this->tahunAjarans[$name];
        foreach ($this->tahunAjarans as $n => $id) {
            if (strtolower(trim($n)) === strtolower($name))
                return $id;
        }
        return null;
    }

    private function findWaliKelas($name)
    {
        $name = trim($name);
        if (isset($this->waliKelas[$name]))
            return $this->waliKelas[$name];
        foreach ($this->waliKelas as $n => $id) {
            if (strtolower(trim($n)) === strtolower($name))
                return $id;
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
    public function getMissingCabang(): array
    {
        return $this->missingCabang;
    }
    public function getMissingWaliKelas(): array
    {
        return $this->missingWaliKelas;
    }
    public function getWarnings(): array
    {
        return $this->warnings;
    }
}
