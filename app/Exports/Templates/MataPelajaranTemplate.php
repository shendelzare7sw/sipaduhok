<?php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MataPelajaranTemplate implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * Return sample data rows
     */
    public function array(): array
    {
        return [
            ['MTK-SD', 'Matematika', 'SD', '', 'Pelajaran matematika untuk jenjang SD'],
            ['PAI-SD', 'Pendidikan Agama Islam', 'SD', 'Islam', 'Pelajaran agama Islam untuk jenjang SD'],
            ['BIN-SMP', 'Bahasa Indonesia', 'SMP', '', 'Pelajaran bahasa Indonesia untuk jenjang SMP'],
        ];
    }

    /**
     * Return column headings
     */
    public function headings(): array
    {
        return [
            'kode_mapel',
            'nama_mapel',
            'jenjang',
            'filter_agama',
            'deskripsi',
        ];
    }

    /**
     * Style the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Sample data styling (light gray background)
        $sheet->getStyle('A2:E4')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F3F4F6'],
            ],
            'font' => [
                'italic' => true,
                'color' => ['rgb' => '6B7280'],
            ],
        ]);

        // Add border to all cells
        $sheet->getStyle('A1:E4')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'],
                ],
            ],
        ]);

        // Add instruction comment
        $sheet->setCellValue('A6', 'PETUNJUK:');
        $sheet->setCellValue('A7', '1. Hapus baris contoh (baris 2-4) sebelum mengisi data Anda');
        $sheet->setCellValue('A8', '2. Kolom "kode_mapel", "nama_mapel", dan "jenjang" wajib diisi');
        $sheet->setCellValue('A9', '3. Jenjang harus salah satu dari: KB, TKA, TKB, SD, SMP, SMA');
        $sheet->setCellValue('A10', '4. Kolom "filter_agama" dan "deskripsi" opsional');
        $sheet->setCellValue('A11', '5. filter_agama hanya diisi untuk mapel agama: Islam, Kristen, Katolik, Hindu, Buddha, Konghucu');
        $sheet->setCellValue('A12', '6. Kosongkan filter_agama untuk mapel umum yang boleh dilihat semua siswa');

        $sheet->getStyle('A6')->getFont()->setBold(true);
        $sheet->getStyle('A6:A12')->getFont()->setSize(10);
        $sheet->getStyle('A7:A12')->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('6B7280'));

        return [];
    }

    /**
     * Set column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 30,
            'C' => 12,
            'D' => 18,
            'E' => 50,
        ];
    }
}
