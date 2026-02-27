<?php

namespace App\Imports\WaliKelas;

use App\Models\Nilai;
use App\Models\MataPelajaran;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Log;

/**
 * Import nilai satu siswa lintas semua mata pelajaran.
 * Setiap baris = satu mata pelajaran (diidentifikasi via kode_mapel).
 */
class NilaiPerSiswaImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsEmptyRows,
    SkipsOnError,
    SkipsOnFailure
{
    protected $siswaId;
    protected $kelasId;
    protected $tahunAjaranId;
    protected $semester;
    protected $guruId;

    /** kode_mapel => MataPelajaran */
    protected $mapelMap;

    protected $errors   = [];
    protected $failures = [];

    public function __construct($siswaId, $kelasId, $tahunAjaranId, $semester, $guruId, $mapelCollection)
    {
        $this->siswaId       = $siswaId;
        $this->kelasId       = $kelasId;
        $this->tahunAjaranId = $tahunAjaranId;
        $this->semester      = $semester;
        $this->guruId        = $guruId;

        // Build lookup map: kode_mapel (lowercase) => MataPelajaran
        $this->mapelMap = $mapelCollection->keyBy(fn($m) => strtolower(trim($m->kode_mapel)));
    }

    public function model(array $row)
    {
        // Identify subject by kode_mapel (column heading "kode_mapel")
        $kodeMapel = strtolower(trim($row['kode_mapel'] ?? ''));

        if (empty($kodeMapel)) {
            return null;
        }

        $mapel = $this->mapelMap[$kodeMapel] ?? null;

        if (!$mapel) {
            $this->errors[] = "Mata pelajaran dengan kode '{$kodeMapel}' tidak ditemukan di kelas ini.";
            return null;
        }

        // Find or initialize nilai record
        $nilai = Nilai::firstOrNew([
            'siswa_id'        => $this->siswaId,
            'mata_pelajaran_id' => $mapel->id,
            'kelas_id'        => $this->kelasId,
            'tahun_ajaran_id' => $this->tahunAjaranId,
            'semester'        => $this->semester,
        ]);

        if (!$nilai->guru_id) {
            $nilai->guru_id = $this->guruId;
        }

        // Only overwrite fields that have a real value in the Excel row
        $fields = [
            'tugas_1', 'tugas_2', 'tugas_3', 'tugas_4', 'tugas_5',
            'latihan_1', 'latihan_2', 'latihan_3', 'latihan_4', 'latihan_5',
            'uh_1', 'uh_2', 'uh_3', 'uh_4', 'uh_5',
            'pts', 'pas',
            'to_1', 'to_2', 'to_3', 'upk', 'ujian_praktek',
        ];

        // Map Excel heading → field key  (e.g. "tugas_1" heading → 'tugas_1')
        $headingMap = [
            'tugas_1' => 'tugas_1', 'tugas_2' => 'tugas_2', 'tugas_3' => 'tugas_3',
            'tugas_4' => 'tugas_4', 'tugas_5' => 'tugas_5',
            'latihan_1' => 'latihan_1', 'latihan_2' => 'latihan_2', 'latihan_3' => 'latihan_3',
            'latihan_4' => 'latihan_4', 'latihan_5' => 'latihan_5',
            'uh_1' => 'uh_1', 'uh_2' => 'uh_2', 'uh_3' => 'uh_3',
            'uh_4' => 'uh_4', 'uh_5' => 'uh_5',
            'pts' => 'pts', 'pas' => 'pas',
            'to_1' => 'to_1', 'to_2' => 'to_2', 'to_3' => 'to_3',
            'upk' => 'upk', 'ujian_praktek' => 'ujian_praktek',
        ];

        $hasData = false;
        foreach ($headingMap as $excelKey => $dbField) {
            if ($this->hasValue($row, $excelKey)) {
                $nilai->$dbField = $this->parseNilai($row[$excelKey]);
                $hasData = true;
            }
        }

        if ($hasData || !$nilai->exists) {
            $nilai->save();
            $nilai->hitungNilaiAkhir();
        }

        return $nilai;
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function rules(): array
    {
        $rules = ['kode_mapel' => 'required'];
        foreach (['tugas', 'latihan', 'uh'] as $prefix) {
            for ($i = 1; $i <= 5; $i++) {
                $rules["{$prefix}_{$i}"] = 'nullable|numeric|min:0|max:100';
            }
        }
        foreach (['pts', 'pas', 'to_1', 'to_2', 'to_3', 'upk', 'ujian_praktek'] as $f) {
            $rules[$f] = 'nullable|numeric|min:0|max:100';
        }
        return $rules;
    }

    private function hasValue(array $row, string $key): bool
    {
        if (!isset($row[$key])) return false;
        $v = $row[$key];
        if ($v === null || $v === '' || $v === '-') return false;
        return is_numeric($v);
    }

    private function parseNilai($value): ?float
    {
        if ($value === null || $value === '' || $value === '-') return null;
        $parsed = floatval($value);
        return ($parsed >= 0 && $parsed <= 100) ? $parsed : null;
    }

    public function getErrors(): array   { return $this->errors; }
    public function getFailures(): array { return $this->failures; }

    public function onError(\Throwable $e): void
    {
        $this->errors[] = $e->getMessage();
        Log::error('WaliKelas Import Nilai Error: ' . $e->getMessage());
    }

    public function onFailure(Failure ...$failures): void
    {
        foreach ($failures as $failure) {
            $this->failures[] = [
                'row'       => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors'    => $failure->errors(),
            ];
        }
    }
}
