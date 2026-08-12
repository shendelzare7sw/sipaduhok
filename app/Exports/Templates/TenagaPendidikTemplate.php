<?php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\Role;
use App\Models\Cabang;

class TenagaPendidikTemplate implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        $cabang = Cabang::first();
        return [
            ['123456789', 'Dr. Ahmad Wijaya, M.Pd', 'ahmad.wijaya@email.com', 'L', 'Surabaya', '1985-03-15', 'Jl. Pendidik No. 1', '081234567890', 'S2 Pendidikan', 'guru_pengajar', $cabang ? $cabang->nama_cabang : ''],
            ['987654321', 'Siti Nurhaliza, S.Pd', 'siti.nur@email.com', 'P', 'Jakarta', '1990-07-22', 'Jl. Guru No. 2', '089876543210', 'S1 Pendidikan', 'wali_kelas', $cabang ? $cabang->nama_cabang : ''],
        ];
    }

    public function headings(): array
    {
        return ['nip', 'nama_lengkap', 'email', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'telepon', 'pendidikan_terakhir', 'role', 'nama_cabang'];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '7C3AED']],
        ]);
        $sheet->getStyle('A2:K3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
            'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']],
        ]);

        $sheet->setCellValue('A5', 'PETUNJUK:');
        $sheet->setCellValue('A6', '1. Hapus baris contoh (baris 2-3) sebelum mengisi data');
        $sheet->setCellValue('A7', '2. WAJIB: nama_lengkap, email, jenis_kelamin, tempat_lahir, tanggal_lahir, alamat, telepon, pendidikan_terakhir, role, nama_cabang');
        $sheet->setCellValue('A8', '3. jenis_kelamin: L atau P');
        $sheet->setCellValue('A9', '4. tanggal_lahir format: YYYY-MM-DD');
        $sheet->setCellValue('A10', '5. role dan nama_cabang harus sama dengan daftar tersedia di bawah');
        $sheet->setCellValue('A11', '6. NIP/email yang sudah ada akan DILEWATI (tidak duplikat)');
        $sheet->setCellValue('A12', '7. User account dibuat otomatis. Username dari NIP/nama, password default: password');

        $sheet->setCellValue('A14', 'ROLE TERSEDIA:');
        $roles = Role::pluck('name')->implode(', ');
        $sheet->setCellValue('B14', $roles ?: 'guru_pengajar, wali_kelas, wakil_kepala_sekolah, dll');

        $sheet->setCellValue('A15', 'CABANG TERSEDIA:');
        $cabangs = Cabang::pluck('nama_cabang')->implode(', ');
        $sheet->setCellValue('B15', $cabangs ?: '(belum ada data)');

        $sheet->getStyle('A5')->getFont()->setBold(true);
        $sheet->getStyle('A14:A15')->getFont()->setBold(true);

        return [];
    }

    public function columnWidths(): array
    {
        return ['A' => 15, 'B' => 28, 'C' => 25, 'D' => 12, 'E' => 15, 'F' => 14, 'G' => 30, 'H' => 15, 'I' => 18, 'J' => 18, 'K' => 15];
    }
}
