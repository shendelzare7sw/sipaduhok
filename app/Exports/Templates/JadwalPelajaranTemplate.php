<?php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TenagaPendidik;

class JadwalPelajaranTemplate implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [
            ['Utama', 'X IPA 1', 'Matematika Wajib', 'Budi Santoso', 'Senin', '07:00', '08:30', ''],
            ['Utama', 'X IPA 1, X IPA 2', 'Bahasa Indonesia', 'Budi Santoso', 'Senin', '08:30', '10:00', 'Kelas gabungan'],
            ['Utama', 'VII A, VIII A', 'Agama Islam', 'Ahmad Fauzi', 'Selasa', '07:00', '08:00', 'Beda jenjang = otomatis dipisah per jenjang'],
            ['Cabang B', 'TK A1', 'Motorik Kasar', 'Siti Aminah', 'Senin', '08:00', '08:30', ''],
            ['Cabang B', 'VII A', 'Bahasa Indonesia', '', 'Selasa', '07:00', '08:20', 'Guru kosong → status "kosong"'],
        ];
    }

    public function headings(): array
    {
        return ['nama_cabang', 'nama_kelas', 'nama_mapel', 'nama_guru', 'hari', 'jam_mulai', 'jam_selesai', 'keterangan'];
    }

    public function styles(Worksheet $sheet)
    {
        // Header style
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Example rows style
        $lastExampleRow = 6; // 5 data rows + 1 header
        $sheet->getStyle("A2:H{$lastExampleRow}")->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']],
            'font' => ['italic' => true, 'color' => ['rgb' => '92400E']],
        ]);

        $row = $lastExampleRow + 2; // skip 1 row

        // === PETUNJUK UMUM ===
        $sheet->setCellValue("A{$row}", 'PETUNJUK PENGISIAN:');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle("A{$row}")->getFont()->getColor()->setRGB('059669');
        $row++;

        $sheet->setCellValue("A{$row}", '1. Hapus semua baris contoh (baris 2-6 berwarna kuning) sebelum mengisi data Anda.');
        $row++;
        $sheet->setCellValue("A{$row}", '2. Kolom WAJIB: nama_cabang, nama_kelas, nama_mapel, hari, jam_mulai, jam_selesai');
        $row++;
        $sheet->setCellValue("A{$row}", '3. Kolom OPSIONAL: nama_guru (jika kosong → jadwal dibuat dengan status "kosong"), keterangan');
        $row++;
        $sheet->setCellValue("A{$row}", '4. hari: Senin, Selasa, Rabu, Kamis, Jumat, Sabtu');
        $row++;
        $sheet->setCellValue("A{$row}", '5. Format jam: HH:MM (contoh: 07:30, 13:00)');
        $row++;
        $sheet->setCellValue("A{$row}", '6. nama_guru harus PERSIS sama dengan nama di data Tenaga Pendidik.');
        $row++;
        $sheet->setCellValue("A{$row}", '7. nama_cabang harus PERSIS sama dengan nama cabang di sistem.');
        $row++;

        $row += 1;

        // === PETUNJUK MULTI-KELAS ===
        $sheet->setCellValue("A{$row}", 'FITUR MULTI-KELAS (KELAS GABUNGAN):');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle("A{$row}")->getFont()->getColor()->setRGB('1E40AF');
        $row++;

        $sheet->setCellValue("A{$row}", '• Untuk menjadwalkan 1 mapel ke beberapa kelas sekaligus, pisahkan nama kelas dengan koma.');
        $row++;
        $sheet->setCellValue("A{$row}", '  Contoh: "X IPA 1, X IPA 2" → 1 jadwal untuk 2 kelas bersamaan.');
        $row++;
        $sheet->setCellValue("A{$row}", '• Jika kelas yang digabung berbeda jenjang (misal VII A + VIII A), sistem otomatis memisah jadwal per jenjang.');
        $row++;
        $sheet->setCellValue("A{$row}", '  Setiap jenjang akan mendapat mapel yang sesuai jenjangnya secara otomatis.');
        $row++;
        $sheet->setCellValue("A{$row}", '• Guru yang sama boleh mengajar beberapa kelas di jam yang sama jika mapelnya SAMA dan cabangnya SAMA.');
        $row++;

        $row += 1;

        // === ATURAN BENTROK ===
        $sheet->setCellValue("A{$row}", 'ATURAN BENTROK:');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle("A{$row}")->getFont()->getColor()->setRGB('DC2626');
        $row++;

        $sheet->setCellValue("A{$row}", '• 1 kelas TIDAK boleh punya 2 jadwal di waktu yang sama (kecuali mapel Agama berbeda denominasi).');
        $row++;
        $sheet->setCellValue("A{$row}", '• 1 guru TIDAK boleh mengajar di 2 tempat berbeda di waktu yang sama.');
        $row++;
        $sheet->setCellValue("A{$row}", '• BOLEH bentrok: guru sama + mapel sama + cabang sama = kelas gabungan.');
        $row++;
        $sheet->setCellValue("A{$row}", '• BOLEH bentrok: mapel Agama berbeda (misal Agama Islam & Agama Kristen) di kelas & jam yang sama.');
        $row++;
        $sheet->setCellValue("A{$row}", '• Jadwal yang duplikat (kelas + waktu sama persis) akan dilewati.');
        $row++;

        $row += 1;

        // === DATA REFERENSI ===
        $sheet->setCellValue("A{$row}", 'DATA REFERENSI:');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle("A{$row}")->getFont()->getColor()->setRGB('7C3AED');
        $row++;

        $sheet->setCellValue("A{$row}", 'CABANG:');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        $sheet->setCellValue("B{$row}", \App\Models\Cabang::pluck('nama_cabang')->implode(', ') ?: '(belum ada)');
        $row++;

        $sheet->setCellValue("A{$row}", 'KELAS:');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        $kelasStr = Kelas::with('cabang')->orderBy('jenjang')->get()->map(function ($k) {
            return $k->nama_kelas . ' [' . $k->jenjang . '] (' . ($k->cabang->nama_cabang ?? '-') . ')';
        })->unique()->implode(', ');
        $sheet->setCellValue("B{$row}", $kelasStr ?: '(belum ada)');
        $row++;

        $sheet->setCellValue("A{$row}", 'MATA PELAJARAN:');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        $mapelStr = MataPelajaran::orderBy('jenjang')->get()->map(function ($m) {
            return $m->nama_mapel . ($m->jenjang ? " [{$m->jenjang}]" : ' [Umum]');
        })->take(80)->implode(', ');
        $sheet->setCellValue("B{$row}", $mapelStr ?: '(belum ada)');
        $row++;

        $sheet->setCellValue("A{$row}", 'GURU:');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        $guruList = TenagaPendidik::orderBy('nama_lengkap')->pluck('nama_lengkap')->take(80)->implode(', ');
        $sheet->setCellValue("B{$row}", $guruList ?: '(belum ada guru)');

        // Column B wrap text for reference data
        $sheet->getStyle("B1:B{$row}")->getAlignment()->setWrapText(true);

        return [];
    }

    public function columnWidths(): array
    {
        return ['A' => 22, 'B' => 30, 'C' => 25, 'D' => 28, 'E' => 10, 'F' => 12, 'G' => 12, 'H' => 35];
    }
}
