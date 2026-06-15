<?php

namespace App\Exports\Templates;

use App\Models\Cabang;
use App\Models\TahunAjaran;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KelasTemplate implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    private $cabangId;

    public function __construct(?int $cabangId = null)
    {
        $this->cabangId = $cabangId;
    }

    public function array(): array
    {
        $cabang = $this->cabangId ? Cabang::find($this->cabangId) : Cabang::first();
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();

        return [
            [
                'KB A',
                'KB-A-01',
                'KB',
                20,
                $cabang ? $cabang->nama_cabang : 'Pusat',
                $tahunAjaran ? $tahunAjaran->nama_tahun_ajaran : '2025/2026',
                '',
            ],
            [
                'Kelas 1 SD',
                'SD-1-01',
                'SD',
                30,
                $cabang ? $cabang->nama_cabang : 'Pusat',
                $tahunAjaran ? $tahunAjaran->nama_tahun_ajaran : '2025/2026',
                '',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'nama_kelas',
            'kode_kelas',
            'jenjang',
            'kuota_siswa',
            'nama_cabang',
            'nama_tahun_ajaran',
            'nama_wali_kelas',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->applyFromArray([
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

        $sheet->getStyle('A2:G3')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F3F4F6'],
            ],
            'font' => [
                'italic' => true,
                'color' => ['rgb' => '6B7280'],
            ],
        ]);

        $sheet->setCellValue('A5', 'PETUNJUK:');
        $sheet->setCellValue('A6', '1. Hapus baris contoh (baris 2-3) sebelum mengisi data Anda');
        $sheet->setCellValue('A7', '2. WAJIB: nama_kelas dan jenjang harus diisi');
        $sheet->setCellValue('A8', '3. Jenjang: KB, TKA, TKB, SD, SMP, SMA');

        if ($this->cabangId) {
            $sheet->setCellValue('A9', '4. Untuk Waka: nama_cabang opsional, import otomatis memakai cabang akun Anda');
            $sheet->setCellValue('A10', '5. nama_wali_kelas opsional; kelas tetap dibuat tanpa wali jika tidak ditemukan');
        } else {
            $sheet->setCellValue('A9', '4. OPSIONAL: nama_cabang, nama_wali_kelas (kelas tetap dibuat jika tidak ditemukan)');
            $sheet->setCellValue('A10', '5. Jika cabang/wali kelas tidak ditemukan -> kelas tetap dibuat tanpa data tersebut');
        }

        $sheet->setCellValue('A12', 'REFERENSI DATA:');
        $sheet->getStyle('A12')->getFont()->setBold(true);

        $sheet->setCellValue('A13', 'Cabang yang tersedia:');
        $cabangs = Cabang::when($this->cabangId, fn($query) => $query->where('id', $this->cabangId))
            ->pluck('nama_cabang')
            ->implode(', ');
        $sheet->setCellValue('B13', $cabangs ?: '(belum ada data)');

        $sheet->setCellValue('A14', 'Tahun Ajaran:');
        $tahuns = TahunAjaran::pluck('nama_tahun_ajaran')->implode(', ');
        $sheet->setCellValue('B14', $tahuns ?: '(belum ada data)');

        $sheet->getStyle('A5')->getFont()->setBold(true);
        $sheet->getStyle('A5:A14')->getFont()->setSize(10);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 15,
            'C' => 10,
            'D' => 12,
            'E' => 20,
            'F' => 20,
            'G' => 25,
        ];
    }
}
