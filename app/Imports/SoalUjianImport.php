<?php

namespace App\Imports;

use App\Models\SoalUjian;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Validation\Rule;

class SoalUjianImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    protected $ujianId;
    protected $rowNumber = 0;

    public function __construct($ujianId)
    {
        $this->ujianId = $ujianId;
    }

    public function model(array $row)
    {
        $this->rowNumber++;

        // Parse pilihan jawaban untuk PG
        $pilihanJawaban = null;
        if (in_array($row['tipe_soal'], ['pilihan_ganda', 'pilihan_ganda_kompleks'])) {
            $pilihanJawaban = json_encode([
                'A' => $row['pilihan_a'] ?? '',
                'B' => $row['pilihan_b'] ?? '',
                'C' => $row['pilihan_c'] ?? '',
                'D' => $row['pilihan_d'] ?? '',
                'E' => $row['pilihan_e'] ?? '',
            ]);
        }

        // Parse kunci jawaban
        $kunciJawaban = $row['jawaban_benar'] ?? null;
        if ($row['tipe_soal'] === 'pilihan_ganda_kompleks') {
            // Multiple answers: A,C,D -> ["A","C","D"]
            $kunciJawaban = json_encode(array_map('trim', explode(',', $row['jawaban_benar'])));
        }

        return new SoalUjian([
            'ujian_id' => $this->ujianId,
            'tipe_soal' => $row['tipe_soal'],
            'pertanyaan' => $row['pertanyaan'],
            'pilihan_jawaban' => $pilihanJawaban,
            'kunci_jawaban' => $kunciJawaban,
            'bobot_nilai' => $row['poin'] ?? 1,
            'urutan' => $row['no'] ?? $this->rowNumber,
        ]);
    }

    public function rules(): array
    {
        return [
            'no' => 'nullable|integer',
            'tipe_soal' => [
                'required',
                Rule::in(['pilihan_ganda', 'pilihan_ganda_kompleks', 'benar_salah', 'isian_singkat', 'uraian']),
            ],
            'pertanyaan' => 'required|string|min:5',
            'pilihan_a' => 'required_if:tipe_soal,pilihan_ganda,pilihan_ganda_kompleks',
            'pilihan_b' => 'required_if:tipe_soal,pilihan_ganda,pilihan_ganda_kompleks',
            'pilihan_c' => 'nullable|string',
            'pilihan_d' => 'nullable|string',
            'pilihan_e' => 'nullable|string',
            'jawaban_benar' => 'required_unless:tipe_soal,uraian',
            'poin' => 'nullable|integer|min:1|max:100',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'tipe_soal.required' => 'Kolom Tipe Soal wajib diisi.',
            'tipe_soal.in' => 'Tipe Soal harus: pilihan_ganda, pilihan_ganda_kompleks, benar_salah, isian_singkat, atau uraian.',
            'pertanyaan.required' => 'Kolom Pertanyaan wajib diisi.',
            'pertanyaan.min' => 'Pertanyaan minimal 5 karakter.',
            'pilihan_a.required_if' => 'Pilihan A wajib untuk soal pilihan ganda.',
            'pilihan_b.required_if' => 'Pilihan B wajib untuk soal pilihan ganda.',
            'jawaban_benar.required_unless' => 'Jawaban Benar wajib diisi (kecuali untuk uraian).',
        ];
    }
}
