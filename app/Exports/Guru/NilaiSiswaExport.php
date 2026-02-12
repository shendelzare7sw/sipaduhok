<?php

namespace App\Exports\Guru;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class NilaiSiswaExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithMapping
{
    protected $nilaiList;
    protected $kelas;
    protected $mapel;
    protected $semester;
    protected $isKelasAkhir;

    public function __construct($nilaiList, $kelas, $mapel, $semester)
    {
        $this->nilaiList = $nilaiList;
        $this->kelas = $kelas;
        $this->mapel = $mapel;
        $this->semester = $semester;
        
        // Determine if kelas akhir logic applies
        $namaKelas = strtolower($kelas->nama_kelas);
        $this->isKelasAkhir = str_contains($namaKelas, '9') || 
                             str_contains($namaKelas, '12') ||
                             str_contains($namaKelas, 'ix') ||
                             str_contains($namaKelas, 'xii');
    }

    public function collection()
    {
        return $this->nilaiList;
    }

    public function headings(): array
    {
        $headings = [
            'No',
            'Nama Siswa',
            'NIS/NISN',
            'Tugas 1', 'Tugas 2', 'Tugas 3', 'Tugas 4', 'Tugas 5', 'Rata Tugas',
            'Latihan 1', 'Latihan 2', 'Latihan 3', 'Latihan 4', 'Latihan 5', 'Rata Latihan',
            'UH 1', 'UH 2', 'UH 3', 'UH 4', 'UH 5', 'Rata UH',
            'PTS',
            'PAS',
            'Nilai Akhir',
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

    public function map($nilai): array
    {
        static $no = 0;
        $no++;

        $row = [
            $no,
            $nilai->siswa->nama_lengkap ?? '-',
            $nilai->siswa->nis ?? $nilai->siswa->nisn ?? '-',
            
            // Tugas
            $nilai->tugas_1, $nilai->tugas_2, $nilai->tugas_3, $nilai->tugas_4, $nilai->tugas_5, 
            $nilai->rata_tugas,
            
            // Latihan
            $nilai->latihan_1, $nilai->latihan_2, $nilai->latihan_3, $nilai->latihan_4, $nilai->latihan_5, 
            $nilai->rata_latihan,
            
            // UH
            $nilai->uh_1, $nilai->uh_2, $nilai->uh_3, $nilai->uh_4, $nilai->uh_5, 
            $nilai->rata_uh,
            
            // Exam
            $nilai->pts,
            $nilai->pas,
            $nilai->nilai_akhir,
        ];

        if ($this->isKelasAkhir) {
            $row = array_merge($row, [
                $nilai->to_1,
                $nilai->to_2,
                $nilai->to_3,
                $nilai->upk,
                $nilai->ujian_praktek,
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
            // Tugas (D-I)
            'D' => 8, 'E' => 8, 'F' => 8, 'G' => 8, 'H' => 8, 'I' => 10,
            // Latihan (J-O)
            'J' => 8, 'K' => 8, 'L' => 8, 'M' => 8, 'N' => 8, 'O' => 10,
            // UH (P-U)
            'P' => 8, 'Q' => 8, 'R' => 8, 'S' => 8, 'T' => 8, 'U' => 10,
            // Exam (V-X)
            'V' => 8, 'W' => 8, 'X' => 10,
        ];

        if ($this->isKelasAkhir) {
            // TO (Y-AA), UPK (AB), Praktek (AC)
            $widths = array_merge($widths, [
                'Y' => 8, 'Z' => 8, 'AA' => 8,
                'AB' => 8,
                'AC' => 12,
            ]);
        }

        return $widths;
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = $this->isKelasAkhir ? 'AC' : 'X';
        
        // Header styling
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4E73DF'], // Primary Blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Center align numerical columns (D onwards)
        $sheet->getStyle("A2:A1000")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C2:{$lastColumn}1000")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }
}
