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
            ['12345', '1234567890', 'Ahmad Fajar Maulana', 'L', 'Surabaya', '2010-05-15', 'Jl. Contoh No. 123', 'Budi Santoso', 'Siti Aminah', '081234567890', '1A', 'PKBM HOK', 'aktif', 'Islam'],
            ['12346', '1234567891', 'Putri Rahayu Dewi', 'P', 'Jakarta', '2011-08-20', 'Jl. Sample No. 456', 'Joko Widodo', 'Sri Mulyani', '089876543210', '7A', 'HOK Cimanggis', 'aktif', 'Kristen'],
        ];
    }

    public function headings(): array
    {
        return ['nis', 'nisn', 'nama_lengkap', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'nama_ayah', 'nama_ibu', 'telepon_orangtua', 'nama_kelas', 'nama_cabang', 'status', 'agama'];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:N1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
        ]);
        $sheet->getStyle('A2:N3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
            'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']],
        ]);

        $sheet->setCellValue('A5', 'PETUNJUK:');
        $sheet->setCellValue('A6', '1. Hapus baris contoh (baris 2-3) sebelum mengisi data');
        $sheet->setCellValue('A7', '2. WAJIB: nama_lengkap harus diisi');
        $sheet->setCellValue('A8', '3. jenis_kelamin: L (Laki-laki) atau P (Perempuan)');
        $sheet->setCellValue('A9', '4. tanggal_lahir format: YYYY-MM-DD (contoh: 2010-05-15)');
        $sheet->setCellValue('A10', '5. status: aktif atau nonaktif');
        $sheet->setCellValue('A11', '6. agama: Islam, Kristen, Katolik, Hindu, Buddha, atau Konghucu');
        $sheet->setCellValue('A12', '7. User account dibuat otomatis dengan password: password');
        $sheet->setCellValue('A13', '8. NIS/NISN yang sudah ada akan DILEWATI (tidak duplikat)');

        $sheet->setCellValue('A15', 'DAFTAR CABANG (Pilih salah satu di kolom nama_cabang):');
        $sheet->setCellValue('A16', '1. PKBM HOK');
        $sheet->setCellValue('A17', '2. HOK Cimanggis');
        $sheet->setCellValue('A18', '3. PAUD HOK');
        
        $sheet->setCellValue('A20', 'DAFTAR KELAS (Tulis sesuai format di kolom nama_kelas):');
        // Use user provided list hardcoded as requested, or keep dynamic if preferred. User asked to "UBAH BERDASARKAN KELAS YANG TERSEDIA...".
        // I will display the dynamic list but grouped or formatted cleanly if possible, or just the list.
        // User provided a specific sequence. Let's list the key levels.
        $kelasList = Kelas::pluck('nama_kelas')->toArray();
        $chunked = array_chunk($kelasList, 15); // Split for readability if many
        
        $row = 21;
        foreach ($chunked as $chunk) {
             $sheet->setCellValue('A' . $row, implode(', ', $chunk));
             $row++;
        }

        $sheet->getStyle('A5')->getFont()->setBold(true);
        $sheet->getStyle('A15')->getFont()->setBold(true);
        $sheet->getStyle('A20')->getFont()->setBold(true);

        return [];
    }

    public function columnWidths(): array
    {
        return ['A' => 12, 'B' => 15, 'C' => 25, 'D' => 12, 'E' => 15, 'F' => 14, 'G' => 30, 'H' => 20, 'I' => 20, 'J' => 18, 'K' => 15, 'L' => 15, 'M' => 10, 'N' => 15];
    }
}
