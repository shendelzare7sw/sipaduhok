<?php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TenagaPendidik;

class JadwalPelajaranTemplate implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [
            ['Utama', 'Kelas 1 SD', 'Matematika', 'Ahmad Wijaya', 'Senin', '07:30', '08:30', ''],
            ['Utama', 'Kelas 1 SD', 'Bahasa Indonesia', 'Siti Nurhaliza', 'Senin', '08:30', '09:15', ''],
            ['Cabang B', 'Kelas 2 SD', 'IPA', '', 'Selasa', '07:30', '08:30', 'Guru belum ditentukan'],
        ];
    }

    public function headings(): array
    {
        return ['nama_cabang', 'nama_kelas', 'nama_mapel', 'nama_guru', 'hari', 'jam_mulai', 'jam_selesai', 'keterangan'];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
        ]);
        $sheet->getStyle('A2:H4')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
            'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']],
        ]);

        $sheet->setCellValue('A6', 'PETUNJUK:');
        $sheet->setCellValue('A7', '1. Hapus baris contoh (baris 2-4) sebelum mengisi data Anda');
        $sheet->setCellValue('A8', '2. nama_cabang dan nama_kelas WAJIB diisi dan harus PERSIS dengan yang ada di database');
        $sheet->setCellValue('A9', '3. hari: Senin, Selasa, Rabu, Kamis, Jumat, Sabtu');
        $sheet->setCellValue('A10', '4. jam_mulai/jam_selesai format: HH:MM (contoh: 07:30)');
        $sheet->setCellValue('A11', '5. nama_guru harus PERSIS dengan nama_lengkap di data Tenaga Pendidik (lihat daftar di bawah)');
        $sheet->setCellValue('A12', '6. Jika guru tidak ditemukan → jadwal dibuat dengan status "kosong"');

        $sheet->setCellValue('A14', 'CABANG TERSEDIA:');
        $sheet->setCellValue('B14', \App\Models\Cabang::pluck('nama_cabang')->implode(', ') ?: '(belum ada)');

        $sheet->setCellValue('A15', 'KELAS TERSEDIA:');
        $kelasStr = Kelas::with('cabang')->get()->map(function($k) {
             return $k->nama_kelas . ' (' . ($k->cabang->nama_cabang ?? '-') . ')';
        })->unique()->implode(', ');
        $sheet->setCellValue('B15', $kelasStr ?: '(belum ada)');

        $sheet->setCellValue('A16', 'MAPEL TERSEDIA:');
        $sheet->setCellValue('B16', MataPelajaran::pluck('nama_mapel')->take(20)->implode(', ') ?: '(belum ada)');

        $sheet->setCellValue('A17', 'GURU TERSEDIA:');
        $guruList = TenagaPendidik::pluck('nama_lengkap')->take(20)->implode(', ');
        $sheet->setCellValue('B17', $guruList ?: '(belum ada guru)');

        $sheet->getStyle('A6')->getFont()->setBold(true);
        $sheet->getStyle('A14:A17')->getFont()->setBold(true);

        return [];
    }

    public function columnWidths(): array
    {
        return ['A' => 20, 'B' => 18, 'C' => 22, 'D' => 25, 'E' => 10, 'F' => 12, 'G' => 12, 'H' => 25];
    }
}
