<?php

namespace App\Imports\Guru;

use App\Models\Nilai;
use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class NilaiSiswaImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsEmptyRows,
    SkipsOnError,
    SkipsOnFailure
{
    protected $kelasId;
    protected $mapelId;
    protected $tahunAjaranId;
    protected $semester;
    protected $guruId;
    protected $errors = [];
    protected $failures = [];

    public function __construct($kelasId, $mapelId, $tahunAjaranId, $semester, $guruId)
    {
        $this->kelasId = $kelasId;
        $this->mapelId = $mapelId;
        $this->tahunAjaranId = $tahunAjaranId;
        $this->semester = $semester;
        $this->guruId = $guruId;
    }

    public function model(array $row)
    {
        // Find siswa by NIS/NISN
        $nisNisn = $row['nisnisn'] ?? null;

        if (!$nisNisn) {
            return null;
        }

        $siswa = Siswa::where('nis', $nisNisn)
            ->orWhere('nisn', $nisNisn)
            ->where('kelas_id', $this->kelasId)
            ->first();

        if (!$siswa) {
            $this->errors[] = "Siswa dengan NIS/NISN {$nisNisn} tidak ditemukan di kelas ini";
            return null;
        }

        // Find or create nilai record
        $nilai = Nilai::firstOrNew([
            'siswa_id' => $siswa->id,
            'mata_pelajaran_id' => $this->mapelId,
            'kelas_id' => $this->kelasId,
            'tahun_ajaran_id' => $this->tahunAjaranId,
            'semester' => $this->semester,
            'guru_id' => $this->guruId,
        ]);

        // Collect data to update - only update fields that have values in Excel
        $dataToUpdate = [];

        // Tugas 1-5
        for ($i = 1; $i <= 5; $i++) {
            $key = "tugas_{$i}";
            if ($this->hasValue($row, $key)) {
                $dataToUpdate[$key] = $this->parseNilai($row[$key]);
            }
        }

        // Latihan 1-5
        for ($i = 1; $i <= 5; $i++) {
            $key = "latihan_{$i}";
            if ($this->hasValue($row, $key)) {
                $dataToUpdate[$key] = $this->parseNilai($row[$key]);
            }
        }

        // UH 1-5
        for ($i = 1; $i <= 5; $i++) {
            $key = "uh_{$i}";
            if ($this->hasValue($row, $key)) {
                $dataToUpdate[$key] = $this->parseNilai($row[$key]);
            }
        }

        // PTS & PAS
        if ($this->hasValue($row, 'pts')) {
            $dataToUpdate['pts'] = $this->parseNilai($row['pts']);
        }
        if ($this->hasValue($row, 'pas')) {
            $dataToUpdate['pas'] = $this->parseNilai($row['pas']);
        }

        // Tingkat akhir (if exists)
        if ($this->hasValue($row, 'to_1')) {
            $dataToUpdate['to_1'] = $this->parseNilai($row['to_1']);
        }
        if ($this->hasValue($row, 'to_2')) {
            $dataToUpdate['to_2'] = $this->parseNilai($row['to_2']);
        }
        if ($this->hasValue($row, 'to_3')) {
            $dataToUpdate['to_3'] = $this->parseNilai($row['to_3']);
        }
        if ($this->hasValue($row, 'upk')) {
            $dataToUpdate['upk'] = $this->parseNilai($row['upk']);
        }
        if ($this->hasValue($row, 'ujian_praktek')) {
            $dataToUpdate['ujian_praktek'] = $this->parseNilai($row['ujian_praktek']);
        }

        // Only update if there are values to update
        if (!empty($dataToUpdate)) {
            foreach ($dataToUpdate as $field => $value) {
                $nilai->$field = $value;
            }
            $nilai->save();
        } else {
            // If no values to update but record is new, still save it
            if (!$nilai->exists) {
                $nilai->save();
            }
        }

        // Calculate averages and final score
        $nilai->hitungSemuaRata();
        $nilai->hitungNilaiAkhir();

        return $nilai;
    }

    /**
     * Custom heading row mapping to match template headers
     */
    public function headingRow(): int
    {
        return 1;
    }

    public function rules(): array
    {
        return [
            'nisnisn' => 'required',
            'tugas_1' => 'nullable|numeric|min:0|max:100',
            'tugas_2' => 'nullable|numeric|min:0|max:100',
            'tugas_3' => 'nullable|numeric|min:0|max:100',
            'tugas_4' => 'nullable|numeric|min:0|max:100',
            'tugas_5' => 'nullable|numeric|min:0|max:100',
            'latihan_1' => 'nullable|numeric|min:0|max:100',
            'latihan_2' => 'nullable|numeric|min:0|max:100',
            'latihan_3' => 'nullable|numeric|min:0|max:100',
            'latihan_4' => 'nullable|numeric|min:0|max:100',
            'latihan_5' => 'nullable|numeric|min:0|max:100',
            'uh_1' => 'nullable|numeric|min:0|max:100',
            'uh_2' => 'nullable|numeric|min:0|max:100',
            'uh_3' => 'nullable|numeric|min:0|max:100',
            'uh_4' => 'nullable|numeric|min:0|max:100',
            'uh_5' => 'nullable|numeric|min:0|max:100',
            'pts' => 'nullable|numeric|min:0|max:100',
            'pas' => 'nullable|numeric|min:0|max:100',
            'to_1' => 'nullable|numeric|min:0|max:100',
            'to_2' => 'nullable|numeric|min:0|max:100',
            'to_3' => 'nullable|numeric|min:0|max:100',
            'upk' => 'nullable|numeric|min:0|max:100',
            'ujian_praktek' => 'nullable|numeric|min:0|max:100',
        ];
    }

    /**
     * Check if a row has a valid value for a specific field
     */
    private function hasValue(array $row, string $key): bool
    {
        // Check if key exists in row
        if (!isset($row[$key])) {
            return false;
        }

        $value = $row[$key];

        // Check if value is empty or placeholder
        if ($value === null || $value === '' || $value === '-') {
            return false;
        }

        // Check if it's a valid number
        if (!is_numeric($value)) {
            return false;
        }

        return true;
    }

    /**
     * Parse nilai - handle empty values, null, or strings
     */
    private function parseNilai($value)
    {
        if ($value === null || $value === '' || $value === '-') {
            return null;
        }

        $parsed = floatval($value);

        // Validate range
        if ($parsed < 0 || $parsed > 100) {
            return null;
        }

        return $parsed;
    }

    /**
     * Get import errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get validation failures
     */
    public function getFailures(): array
    {
        return $this->failures;
    }

    /**
     * Called when an error occurs during import
     */
    public function onError(\Throwable $error)
    {
        $this->errors[] = $error->getMessage();
        Log::error('Import Nilai Error: ' . $error->getMessage());
    }

    /**
     * Called when validation fails
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->failures[] = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ];
        }
    }
}
