<?php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OrangTuaTemplate implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [
            ['Budi Santoso', 'budi.santoso', 'budi@email.com', '081234567890', '12345, 12346', 'Ayah'],
            ['Siti Aminah', 'siti.aminah', 'siti@email.com', '089876543210', '12345', 'Ibu'],
        ];
    }

    public function headings(): array
    {
        return ['nama', 'username', 'email', 'telepon', 'nis_anak', 'hubungan'];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EA580C']],
        ]);
        $sheet->getStyle('A2:F3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF7ED']],
            'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']],
        ]);

        $sheet->setCellValue('A5', 'PETUNJUK:');
        $sheet->setCellValue('A6', '1. nama WAJIB diisi');
        $sheet->setCellValue('A7', '2. username & email opsional (auto-generate jika kosong)');
        $sheet->setCellValue('A8', '3. nis_anak: NIS siswa yang akan dihubungkan (pisah koma jika >1)');
        $sheet->setCellValue('A9', '4. hubungan diisi jika nis_anak diisi: Ayah, Ibu, Wali, dll');
        $sheet->setCellValue('A10', '5. Password default: password');

        $sheet->getStyle('A5')->getFont()->setBold(true);

        return [];
    }

    public function columnWidths(): array
    {
        return ['A' => 25, 'B' => 18, 'C' => 25, 'D' => 15, 'E' => 20, 'F' => 12];
    }
}
