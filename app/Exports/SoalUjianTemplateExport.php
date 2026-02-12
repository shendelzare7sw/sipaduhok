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
        return new Collection([
            [1, 'pilihan_ganda', '', 'Ibu kota Indonesia adalah...', 'Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Semarang', 'A', 2],
            [2, 'pilihan_ganda_kompleks', '', 'Planet yang memiliki satelit adalah...', 'Bumi', 'Mars', 'Venus', 'Jupiter', 'Merkurius', 'A,B,D', 4],
            [3, 'benar_salah', '', 'Matahari berputar mengelilingi Bumi.', '', '', '', '', '', 'salah', 1],
            [4, 'isian_singkat', '', 'Negara terluas di dunia adalah...', '', '', '', '', '', 'Rusia', 2],
            [5, 'uraian', '', 'Jelaskan proses fotosintesis pada tumbuhan!', '', '', '', '', '', '', 10],
            [6, 'pilihan_ganda', 'Bacalah teks berikut! Indonesia merupakan negara kepulauan terbesar di dunia dengan lebih dari 17.000 pulau.', 'Indonesia disebut negara kepulauan terbesar karena...', 'Memiliki >17.000 pulau', 'Terletak di jalur perdagangan', 'Letak geografis strategis', 'Negara terbesar', '', 'A', 2],
            [7, 'pilihan_ganda', 'Bacalah teks berikut! Indonesia merupakan negara kepulauan terbesar di dunia dengan lebih dari 17.000 pulau.', 'Keuntungan letak geografis Indonesia menurut teks?', 'Memiliki banyak pulau', 'Jalur perdagangan internasional', 'Negara terbesar', 'Banyak penduduk', '', 'B', 2],
        ]);
    }

    public function headings(): array
    {
        return [
            'No', 'Tipe Soal', 'Narasi', 'Pertanyaan',
            'Pilihan A', 'Pilihan B', 'Pilihan C', 'Pilihan D', 'Pilihan E',
            'Jawaban Benar', 'Poin',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5, 'B' => 22, 'C' => 50, 'D' => 50,
            'E' => 20, 'F' => 20, 'G' => 20, 'H' => 20, 'I' => 20,
            'J' => 15, 'K' => 8,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '165FAC'],
            ],
        ]);

        $sheet->getComment('B1')->getText()->createTextRun(
            "Nilai yang diizinkan:\n- pilihan_ganda\n- pilihan_ganda_kompleks\n- benar_salah\n- isian_singkat\n- uraian"
        );

        $sheet->getComment('C1')->getText()->createTextRun(
            "Narasi / Teks Bacaan (opsional):\n- Isi dengan teks bacaan untuk soal berbasis narasi\n- Soal dengan narasi SAMA akan dikelompokkan\n- Kosongkan jika soal tidak berbasis narasi"
        );

        $sheet->getComment('J1')->getText()->createTextRun(
            "Format Jawaban:\n- PG: A, B, C, D, atau E\n- PG Kompleks: A,B,D (pisah koma)\n- Benar/Salah: benar atau salah\n- Isian: teks jawaban\n- Uraian: kosongkan"
        );

        return [];
    }
}
