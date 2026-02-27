<?php

namespace App\Exports\WaliKelas;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class NilaiPerSiswaTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithMapping
{
    protected $mapelList;
    protected $siswa;
    protected $kelas;
    protected $semester;
    protected $isKelasAkhir;

    public function __construct($mapelList, $siswa, $kelas, $semester, $isKelasAkhir)
    {
        $this->mapelList    = $mapelList;
        $this->siswa        = $siswa;
        $this->kelas        = $kelas;
        $this->semester     = $semester;
        $this->isKelasAkhir = $isKelasAkhir;
    }

    public function collection()
    {
        return $this->mapelList;
    }

    public function headings(): array
    {
        $headings = [
            'kode_mapel',
            'Nama Mapel',
            'Tugas 1', 'Tugas 2', 'Tugas 3', 'Tugas 4', 'Tugas 5',
            'Latihan 1', 'Latihan 2', 'Latihan 3', 'Latihan 4', 'Latihan 5',
            'UH 1', 'UH 2', 'UH 3', 'UH 4', 'UH 5',
            'PTS',
            'PAS',
        ];

        if ($this->isKelasAkhir) {
            $headings = array_merge($headings, ['TO 1', 'TO 2', 'TO 3', 'UPK', 'Ujian Praktek']);
        }

        return $headings;
    }

    public function map($mapel): array
    {
        $row = [
            $mapel->kode_mapel,
            $mapel->nama_mapel,
            // Tugas 1-5 (empty for user to fill)
            '', '', '', '', '',
            // Latihan 1-5
            '', '', '', '', '',
            // UH 1-5
            '', '', '', '', '',
            // PTS, PAS
            '', '',
        ];

        if ($this->isKelasAkhir) {
            $row = array_merge($row, ['', '', '', '', '']);
        }

        return $row;
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 14,  // kode_mapel
            'B' => 30,  // Nama Mapel
            // Tugas (C-G)
            'C' => 10, 'D' => 10, 'E' => 10, 'F' => 10, 'G' => 10,
            // Latihan (H-L)
            'H' => 10, 'I' => 10, 'J' => 10, 'K' => 10, 'L' => 10,
            // UH (M-Q)
            'M' => 10, 'N' => 10, 'O' => 10, 'P' => 10, 'Q' => 10,
            // PTS, PAS
            'R' => 10, 'S' => 10,
        ];

        if ($this->isKelasAkhir) {
            $widths = array_merge($widths, [
                'T' => 10, 'U' => 10, 'V' => 10, // TO 1-3
                'W' => 10,                         // UPK
                'X' => 15,                         // Ujian Praktek
            ]);
        }

        return $widths;
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn    = $this->isKelasAkhir ? 'X' : 'S';
        $rowCount      = $this->mapelList->count() + 1;

        // Header row styling
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4E73DF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Shade locked columns (kode_mapel + nama_mapel)
        $sheet->getStyle("A2:B{$rowCount}")->applyFromArray([
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F0F4FF'],
            ],
        ]);

        // Center all cells
        $sheet->getStyle("A1:{$lastColumn}{$rowCount}")
              ->getAlignment()
              ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Left-align nama_mapel
        $sheet->getStyle("B2:B{$rowCount}")
              ->getAlignment()
              ->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Borders
        $sheet->getStyle("A1:{$lastColumn}{$rowCount}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        // Freeze first two columns
        $sheet->freezePane('C2');

        // Instruction comment on first data cell
        if ($rowCount > 1) {
            $sheet->getComment('C2')->getText()->createTextRun(
                "Isi nilai 0-100.\nKosongkan jika tidak ada nilai.\nJangan ubah kolom A (kode_mapel)."
            );
        }

        return [];
    }
}
