<?php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TagihanTemplate implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [
            ['12345', '', 'Ahmad Fajar', 'SPP Bulanan', 500000, '2025-02-28'],
            ['12346', '', 'Putri Rahayu', 'Uang Bangunan', 1500000, '2025-03-15'],
            ['', '1234567890', 'Budi Santoso', 'Seragam', 350000, '2025-02-28'],
        ];
    }

    public function headings(): array
    {
        return ['nis', 'nisn', 'nama_siswa', 'jenis_tagihan', 'jumlah', 'tanggal_jatuh_tempo'];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DC2626']],
        ]);
        $sheet->getStyle('A2:F4')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF2F2']],
            'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']],
        ]);

        $sheet->setCellValue('A6', 'PETUNJUK:');
        $sheet->setCellValue('A7', '1. Hapus baris contoh (baris 2-4) sebelum mengisi data');
        $sheet->setCellValue('A8', '2. WAJIB: Isi NIS atau NISN atau nama_siswa (salah satu) - siswa HARUS sudah ada di database');
        $sheet->setCellValue('A9', '3. WAJIB: jenis_tagihan dan jumlah harus diisi');
        $sheet->setCellValue('A10', '4. jumlah dalam Rupiah tanpa titik/koma (contoh: 500000)');
        $sheet->setCellValue('A11', '5. tanggal_jatuh_tempo format: YYYY-MM-DD');
        $sheet->setCellValue('A12', '6. Status tagihan otomatis: belum_bayar');
        $sheet->setCellValue('A13', '7. Jika siswa tidak ditemukan → baris akan DILEWATI');

        $sheet->setCellValue('A15', 'JENIS TAGIHAN UMUM:');
        $sheet->setCellValue('B15', 'SPP Bulanan, Uang Bangunan, Seragam, Buku, Kegiatan, dll');

        $sheet->getStyle('A6')->getFont()->setBold(true);
        $sheet->getStyle('A15')->getFont()->setBold(true);

        return [];
    }

    public function columnWidths(): array
    {
        return ['A' => 12, 'B' => 15, 'C' => 25, 'D' => 20, 'E' => 15, 'F' => 18];
    }
}
