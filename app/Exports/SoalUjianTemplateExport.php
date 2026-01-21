<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class SoalUjianTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function collection()
    {
        // Sample data untuk template
        return new Collection([
            [
                'no' => 1,
                'tipe_soal' => 'pilihan_ganda',
                'pertanyaan' => 'Ibu kota Indonesia adalah...',
                'pilihan_a' => 'Jakarta',
                'pilihan_b' => 'Surabaya',
                'pilihan_c' => 'Bandung',
                'pilihan_d' => 'Medan',
                'pilihan_e' => 'Semarang',
                'jawaban_benar' => 'A',
                'poin' => 2,
            ],
            [
                'no' => 2,
                'tipe_soal' => 'pilihan_ganda_kompleks',
                'pertanyaan' => 'Planet di tata surya yang memiliki satelit adalah...',
                'pilihan_a' => 'Bumi',
                'pilihan_b' => 'Mars',
                'pilihan_c' => 'Venus',
                'pilihan_d' => 'Jupiter',
                'pilihan_e' => 'Merkurius',
                'jawaban_benar' => 'A,B,D',
                'poin' => 4,
            ],
            [
                'no' => 3,
                'tipe_soal' => 'benar_salah',
                'pertanyaan' => 'Matahari berputar mengelilingi Bumi.',
                'pilihan_a' => '',
                'pilihan_b' => '',
                'pilihan_c' => '',
                'pilihan_d' => '',
                'pilihan_e' => '',
                'jawaban_benar' => 'salah',
                'poin' => 1,
            ],
            [
                'no' => 4,
                'tipe_soal' => 'isian_singkat',
                'pertanyaan' => 'Negara terluas di dunia adalah...',
                'pilihan_a' => '',
                'pilihan_b' => '',
                'pilihan_c' => '',
                'pilihan_d' => '',
                'pilihan_e' => '',
                'jawaban_benar' => 'Rusia',
                'poin' => 2,
            ],
            [
                'no' => 5,
                'tipe_soal' => 'uraian',
                'pertanyaan' => 'Jelaskan proses fotosintesis pada tumbuhan!',
                'pilihan_a' => '',
                'pilihan_b' => '',
                'pilihan_c' => '',
                'pilihan_d' => '',
                'pilihan_e' => '',
                'jawaban_benar' => '',
                'poin' => 10,
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'No',
            'Tipe Soal',
            'Pertanyaan',
            'Pilihan A',
            'Pilihan B',
            'Pilihan C',
            'Pilihan D',
            'Pilihan E',
            'Jawaban Benar',
            'Poin',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 20,  // Tipe Soal
            'C' => 50,  // Pertanyaan
            'D' => 20,  // Pilihan A
            'E' => 20,  // Pilihan B
            'F' => 20,  // Pilihan C
            'G' => 20,  // Pilihan D
            'H' => 20,  // Pilihan E
            'I' => 15,  // Jawaban Benar
            'J' => 8,   // Poin
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '165FAC'],
            ],
        ]);

        // Add comment/notes for Tipe Soal column
        $sheet->getComment('B1')->getText()->createTextRun(
            "Nilai yang diizinkan:\n" .
            "- pilihan_ganda\n" .
            "- pilihan_ganda_kompleks\n" .
            "- benar_salah\n" .
            "- isian_singkat\n" .
            "- uraian"
        );

        $sheet->getComment('I1')->getText()->createTextRun(
            "Format Jawaban:\n" .
            "- PG: A, B, C, D, atau E\n" .
            "- PG Kompleks: A,B,D (pisah koma)\n" .
            "- Benar/Salah: benar atau salah\n" .
            "- Isian: teks jawaban\n" .
            "- Uraian: kosongkan"
        );

        return [];
    }
}
