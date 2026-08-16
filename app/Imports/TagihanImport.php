<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TagihanImport implements ToCollection, WithHeadingRow
{
    private $skippedCount = 0;

    private $importedCount = 0;

    private $siswaList;

    private $tahunAjaranId;

    private TahunAjaran $tahunAjaran;

    // Tracking
    private $missingSiswa = [];

    private $warnings = [];

    public function __construct($tahunAjaranId = null)
    {
        $this->tahunAjaranId = $tahunAjaranId ?? TahunAjaran::where('is_active', true)->first()?->id;
        $this->tahunAjaran = TahunAjaran::findOrFail($this->tahunAjaranId);
        $this->siswaList = Siswa::pluck('id', 'nis')->toArray();
    }

    public function collection(Collection $rows)
    {
        $rowNumber = 1;

        foreach ($rows as $row) {
            $rowNumber++;
            $row = $row->toArray();

            // Skip empty rows
            if (empty($row['jenis_tagihan']) || trim($row['jenis_tagihan']) === '') {
                continue;
            }

            // Lookup siswa by NIS or NISN or Nama
            $siswaId = null;
            $searchTerm = '';

            if (! empty($row['nis'])) {
                $searchTerm = $row['nis'];
                $siswaId = $this->siswaList[$row['nis']] ?? null;
            }
            if (! $siswaId && ! empty($row['nisn'])) {
                $searchTerm = $row['nisn'];
                $siswa = Siswa::where('nisn', $row['nisn'])->first();
                $siswaId = $siswa ? $siswa->id : null;
            }
            if (! $siswaId && ! empty($row['nama_siswa'])) {
                $searchTerm = $row['nama_siswa'];
                $siswa = Siswa::where('nama_lengkap', 'like', '%'.trim($row['nama_siswa']).'%')->first();
                $siswaId = $siswa ? $siswa->id : null;
            }

            if (! $siswaId) {
                if (! in_array($searchTerm, $this->missingSiswa)) {
                    $this->missingSiswa[] = $searchTerm;
                }
                $this->warnings[] = "Baris {$rowNumber}: Siswa '{$searchTerm}' tidak ditemukan";
                $this->skippedCount++;

                continue;
            }

            // Check duplicate
            $exists = Tagihan::where('siswa_id', $siswaId)
                ->where('tahun_ajaran_id', $this->tahunAjaranId)
                ->where('jenis_tagihan', $row['jenis_tagihan'])
                ->exists();

            if ($exists) {
                $this->skippedCount++;

                continue;
            }

            try {
                Tagihan::create([
                    'siswa_id' => $siswaId,
                    'tahun_ajaran_id' => $this->tahunAjaranId,
                    'jenis_tagihan' => $row['jenis_tagihan'],
                    'jumlah' => floatval($row['jumlah'] ?? 0),
                    'tanggal_jatuh_tempo' => $this->parseDate($row['tanggal_jatuh_tempo'] ?? null),
                    'status' => 'belum_bayar',
                ]);

                $this->importedCount++;
            } catch (\Exception $e) {
                $this->skippedCount++;
                $this->warnings[] = "Baris {$rowNumber}: Error - ".$e->getMessage();
            }
        }
    }

    private function parseDate($value)
    {
        if (empty($value)) {
            return $this->tahunAjaran->getDefaultTagihanDueDate()->toDateString();
        }

        if (is_numeric($value)) {
            try {
                $value = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            } catch (\Throwable) {
                return $this->tahunAjaran->getDefaultTagihanDueDate()->toDateString();
            }
        }

        try {
            return $this->tahunAjaran->normalizeTagihanDueDate($value)->toDateString();
        } catch (\Throwable) {
            return $this->tahunAjaran->getDefaultTagihanDueDate()->toDateString();
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

    public function getMissingSiswa(): array
    {
        return $this->missingSiswa;
    }

    public function getWarnings(): array
    {
        return $this->warnings;
    }
}
