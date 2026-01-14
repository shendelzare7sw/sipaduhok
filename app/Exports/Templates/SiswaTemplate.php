<?php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\Kelas;
use App\Models\Cabang;

class SiswaTemplate implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [
            ['12345', '1234567890', 'Ahmad Fajar Maulana', 'L', 'Surabaya', '2010-05-15', 'Jl. Contoh No. 123', 'Budi Santoso', 'Siti Aminah', '081234567890', 'Kelas 1 SD', '', 'aktif'],
            ['12346', '1234567891', 'Putri Rahayu Dewi', 'P', 'Jakarta', '2011-08-20', 'Jl. Sample No. 456', 'Joko Widodo', 'Sri Mulyani', '089876543210', 'Kelas 2 SD', '', 'aktif'],
        ];
    }

    public function headings(): array
    {
        return ['nis', 'nisn', 'nama_lengkap', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'nama_ayah', 'nama_ibu', 'telepon_orangtua', 'nama_kelas', 'nama_cabang', 'status'];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
        ]);
        $sheet->getStyle('A2:M3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
            'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']],
        ]);

        // Instructions
        $sheet->setCellValue('A5', 'PETUNJUK:');
        $sheet->setCellValue('A6', '1. Hapus baris contoh (baris 2-3) sebelum mengisi data');
        $sheet->setCellValue('A7', '2. WAJIB: nama_lengkap harus diisi');
        $sheet->setCellValue('A8', '3. jenis_kelamin: L (Laki-laki) atau P (Perempuan)');
        $sheet->setCellValue('A9', '4. tanggal_lahir format: YYYY-MM-DD (contoh: 2010-05-15)');
        $sheet->setCellValue('A10', '5. OPSIONAL: nama_kelas (jika tidak ditemukan → siswa dibuat tanpa kelas)');
        $sheet->setCellValue('A11', '6. status: aktif atau nonaktif');
        $sheet->setCellValue('A12', '7. User account dibuat otomatis dengan password: password');
        $sheet->setCellValue('A13', '8. NIS/NISN yang sudah ada akan DILEWATI (tidak duplikat)');

        $sheet->setCellValue('A15', 'KELAS TERSEDIA:');
        $kelasList = Kelas::pluck('nama_kelas')->implode(', ');
        $sheet->setCellValue('B15', $kelasList ?: '(belum ada data)');

        $sheet->getStyle('A5')->getFont()->setBold(true);
        $sheet->getStyle('A15')->getFont()->setBold(true);

        return [];
    }

    public function columnWidths(): array
    {
        return ['A' => 12, 'B' => 15, 'C' => 25, 'D' => 12, 'E' => 15, 'F' => 14, 'G' => 30, 'H' => 20, 'I' => 20, 'J' => 18, 'K' => 15, 'L' => 15, 'M' => 10];
    }
}
