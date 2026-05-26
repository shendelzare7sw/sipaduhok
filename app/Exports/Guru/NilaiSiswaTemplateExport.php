<?php

namespace App\Exports\Guru;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class NilaiSiswaTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithMapping
{
    protected $siswaList;
    protected $kelas;
    protected $mapel;
    protected $semester;
    protected $isKelasAkhir;

    public function __construct($siswaList, $kelas, $mapel, $semester)
    {
        $this->siswaList = $siswaList;
        $this->kelas = $kelas;
        $this->mapel = $mapel;
        $this->semester = $semester;

        $this->isKelasAkhir = $kelas->isTingkatAkhir();
    }

    public function collection()
    {
        return $this->siswaList;
    }

    public function headings(): array
    {
        $headings = [
            'No',
            'Nama Siswa',
            'NIS/NISN',
            'Tugas 1', 'Tugas 2', 'Tugas 3', 'Tugas 4', 'Tugas 5',
            'Latihan 1', 'Latihan 2', 'Latihan 3', 'Latihan 4', 'Latihan 5',
            'UH 1', 'UH 2', 'UH 3', 'UH 4', 'UH 5',
            'PTS',
            'PAS',
        ];

        if ($this->isKelasAkhir) {
            $headings = array_merge($headings, [
                'TO 1', 'TO 2', 'TO 3',
                'UPK',
                'Ujian Praktek'
            ]);
        }

        return $headings;
    }

    public function map($siswa): array
    {
        static $no = 0;
        $no++;

        $row = [
            $no,
            $siswa->nama_lengkap ?? '-',
            $siswa->nis ?? $siswa->nisn ?? '-',

            // Tugas 1-5 (empty)
            '', '', '', '', '',

            // Latihan 1-5 (empty)
            '', '', '', '', '',

            // UH 1-5 (empty)
            '', '', '', '', '',

            // PTS, PAS (empty)
            '', '',
        ];

        if ($this->isKelasAkhir) {
            $row = array_merge($row, [
                '', '', '', // TO 1-3
                '', // UPK
                '', // Ujian Praktek
            ]);
        }

        return $row;
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 5,   // No
            'B' => 30,  // Nama Siswa
            'C' => 15,  // NIS
            // Tugas (D-H)
            'D' => 10, 'E' => 10, 'F' => 10, 'G' => 10, 'H' => 10,
            // Latihan (I-M)
            'I' => 10, 'J' => 10, 'K' => 10, 'L' => 10, 'M' => 10,
            // UH (N-R)
            'N' => 10, 'O' => 10, 'P' => 10, 'Q' => 10, 'R' => 10,
            // Exam (S-T)
            'S' => 10, 'T' => 10,
        ];

        if ($this->isKelasAkhir) {
            // TO (U-W), UPK (X), Praktek (Y)
            $widths = array_merge($widths, [
                'U' => 10, 'V' => 10, 'W' => 10,
                'X' => 10,
                'Y' => 15,
            ]);
        }

        return $widths;
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = $this->isKelasAkhir ? 'Y' : 'T';
        $rowCount = $this->siswaList->count() + 1; // +1 for header

        // Header styling
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4E73DF'], // Primary Blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Freeze first row and first 3 columns
        $sheet->freezePane('D2');

        // Center align all columns
        $sheet->getStyle("A1:{$lastColumn}{$rowCount}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Left align nama siswa
        $sheet->getStyle("B2:B{$rowCount}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Add borders to all cells
        $sheet->getStyle("A1:{$lastColumn}{$rowCount}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        // Protect fixed columns (No, Nama, NIS)
        $sheet->getStyle("A2:C{$rowCount}")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F8F9FC'],
            ],
        ]);

        // Add instruction comment on first data cell
        if ($rowCount > 1) {
            $sheet->getComment('D2')->getText()->createTextRun(
                "Isi nilai antara 0-100.\nKosongkan jika tidak ada nilai.\nFormat: angka desimal (contoh: 85 atau 87.5)"
            );
        }

        return [];
    }
}
