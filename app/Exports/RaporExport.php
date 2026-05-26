<?php

namespace App\Exports;

use App\Models\Rapor;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * Excel export untuk rapor — sekaligus jadi TEMPLATE yang bisa di-import balik
 * via App\Imports\WaliKelas\RaporImport.
 *
 * Layout tetap mirror format export sebelumnya (Kelompok A/B, Ekstra, Kehadiran, Catatan),
 * tapi struktur sekarang tabular & ada metadata identifier untuk parser import.
 *
 * Section markers di kolom A (kapital + ">>>") = anchor untuk parser:
 *   ">>> NILAI MATA PELAJARAN <<<"
 *   ">>> KEHADIRAN <<<"
 *   ">>> CATATAN WALI KELAS <<<"
 *   ">>> KEGIATAN EKSTRAKURIKULER <<<"
 *
 * Cell A3 = metadata "RAPOR_ID:X|JENIS:Y|SEMESTER:Z" — wajib match saat import.
 */
class RaporExport implements FromArray, WithEvents, WithTitle
{
    use Exportable;

    public const METADATA_CELL = 'A3';
    public const SECTION_NILAI = '>>> NILAI MATA PELAJARAN <<<';
    public const SECTION_KEHADIRAN = '>>> KEHADIRAN <<<';
    public const SECTION_CATATAN = '>>> CATATAN WALI KELAS <<<';
    public const SECTION_KEGIATAN = '>>> KEGIATAN EKSTRAKURIKULER <<<';
    public const HEADER_BG = 'FF4E73DF';
    public const READONLY_BG = 'FFF8F9FC';
    public const SECTION_BG = 'FFE3F2FD';

    public function __construct(public Rapor $rapor) {}

    public function array(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Rapor ' . substr($this->rapor->siswa->nama_lengkap ?? 'Siswa', 0, 25);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $rapor = $this->rapor->loadMissing([
                    'siswa', 'kelas', 'tahunAjaran',
                    'raporNilai.mataPelajaran',
                    'kegiatanEkstra',
                ]);

                $jenisLabel = $rapor->jenis_rapor === 'tengah_semester'
                    ? 'LAPORAN PENILAIAN TENGAH SEMESTER (PTS)'
                    : 'PENCAPAIAN KOMPETENSI PESERTA DIDIK (PAS)';

                // Row 1-2: Header
                $sheet->setCellValue('A1', $jenisLabel);
                $sheet->mergeCells('A1:I1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::HEADER_BG]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(24);

                $sheet->setCellValue('A2', 'House of Knowledge - The Second Home For Your Children');
                $sheet->mergeCells('A2:I2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'italic' => true, 'size' => 10],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Row 3: Metadata identifier (parser import akan match this)
                $sheet->setCellValue(self::METADATA_CELL, sprintf(
                    'RAPOR_ID:%d|JENIS:%s|SEMESTER:%s',
                    $rapor->id,
                    $rapor->jenis_rapor,
                    $rapor->semester
                ));
                $sheet->mergeCells('A3:I3');
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['size' => 8, 'color' => ['argb' => 'FF999999'], 'italic' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);

                // Row 4-8: Info siswa (readonly)
                $infoRows = [
                    ['Nama Siswa', $rapor->siswa->nama_lengkap ?? '-'],
                    ['Nomor Induk (NIS)', $rapor->siswa->nis ?? '-'],
                    ['Kelas', $rapor->kelas->nama_kelas ?? '-'],
                    ['Tahun Ajaran', $rapor->tahunAjaran->nama_tahun_ajaran ?? '-'],
                    ['Semester', ucfirst($rapor->semester) . ' — ' . ($rapor->jenis_rapor === 'tengah_semester' ? 'PTS' : 'PAS')],
                ];
                $row = 4;
                foreach ($infoRows as [$label, $value]) {
                    $sheet->setCellValue("A{$row}", $label);
                    $sheet->setCellValue("C{$row}", $value);
                    $sheet->mergeCells("A{$row}:B{$row}");
                    $sheet->mergeCells("C{$row}:I{$row}");
                    $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                    $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::READONLY_BG]],
                    ]);
                    $row++;
                }
                $row++; // blank row

                // ── SECTION: NILAI MATA PELAJARAN ──
                $this->writeSectionMarker($sheet, $row, self::SECTION_NILAI);
                $row++;

                $nilaiHeaders = ['No', 'Kelompok', 'Kode Mapel', 'Mata Pelajaran', 'Nilai Angka', 'Nilai Huruf', 'Deskripsi Capaian', 'Visible', 'Override Kelompok'];
                $sheet->fromArray($nilaiHeaders, null, "A{$row}");
                $this->styleHeaderRow($sheet, $row, 'I');
                $row++;

                $nilaiStartRow = $row;
                $nilaiList = $rapor->raporNilai->sortBy(fn($n) => $n->urutan ?? 0)->values();
                if ($nilaiList->isEmpty()) {
                    $sheet->setCellValue("A{$row}", '(Belum ada nilai — generate rapor dulu di /wali/rapor)');
                    $sheet->mergeCells("A{$row}:I{$row}");
                    $sheet->getStyle("A{$row}")->getFont()->setItalic(true)->getColor()->setARGB('FF999999');
                    $row++;
                } else {
                    foreach ($nilaiList as $i => $nilai) {
                        $kelompokAuto = $this->detectKelompok($nilai);
                        $sheet->setCellValue("A{$row}", $i + 1);
                        $sheet->setCellValue("B{$row}", $kelompokAuto);
                        $sheet->setCellValue("C{$row}", $nilai->mataPelajaran->kode_mapel ?? '');
                        $sheet->setCellValue("D{$row}", $nilai->mataPelajaran->nama_mapel ?? '-');
                        $sheet->setCellValue("E{$row}", $nilai->nilai_angka);
                        $sheet->setCellValue("F{$row}", $nilai->nilai_huruf ?? '');
                        $sheet->setCellValue("G{$row}", $nilai->deskripsi ?? '');
                        $sheet->setCellValue("H{$row}", $nilai->is_visible ? 'Y' : 'N');
                        $sheet->setCellValue("I{$row}", $nilai->kelompok_override ?? '');
                        // Readonly columns shade
                        $sheet->getStyle("A{$row}:D{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::READONLY_BG]],
                        ]);
                        $sheet->getStyle("F{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::READONLY_BG]],
                        ]);
                        $sheet->getStyle("G{$row}")->getAlignment()->setWrapText(true);
                        $row++;
                    }
                }
                $this->applyBorder($sheet, "A{$nilaiStartRow}:I" . ($row - 1));
                $row++; // blank

                // ── SECTION: KEHADIRAN ──
                $this->writeSectionMarker($sheet, $row, self::SECTION_KEHADIRAN);
                $row++;
                $sheet->fromArray(['Sakit (hari)', 'Izin (hari)', 'Alpha (hari)'], null, "A{$row}");
                $this->styleHeaderRow($sheet, $row, 'C');
                $row++;
                $sheet->setCellValue("A{$row}", (int) ($rapor->jumlah_sakit ?? 0));
                $sheet->setCellValue("B{$row}", (int) ($rapor->jumlah_izin ?? 0));
                $sheet->setCellValue("C{$row}", (int) ($rapor->jumlah_alpha ?? 0));
                $this->applyBorder($sheet, "A" . ($row - 1) . ":C{$row}");
                $row += 2;

                // ── SECTION: CATATAN WALI KELAS ──
                $this->writeSectionMarker($sheet, $row, self::SECTION_CATATAN);
                $row++;
                $sheet->setCellValue("A{$row}", $rapor->catatan_wali_kelas ?? '');
                $sheet->mergeCells("A{$row}:I" . ($row + 3));
                $sheet->getStyle("A{$row}:I" . ($row + 3))->applyFromArray([
                    'alignment' => ['wrapText' => true, 'vertical' => Alignment::VERTICAL_TOP],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
                ]);
                $row += 5;

                // ── SECTION: KEGIATAN EKSTRAKURIKULER ──
                $this->writeSectionMarker($sheet, $row, self::SECTION_KEGIATAN);
                $row++;
                $sheet->fromArray(['No', 'Nama Kegiatan', 'Predikat (A/B/C)', 'Keterangan'], null, "A{$row}");
                $this->styleHeaderRow($sheet, $row, 'D');
                $row++;

                $kegiatanStartRow = $row;
                foreach ($rapor->kegiatanEkstra as $i => $ekstra) {
                    $sheet->setCellValue("A{$row}", $i + 1);
                    $sheet->setCellValue("B{$row}", $ekstra->kegiatan_nama ?? '');
                    $sheet->setCellValue("C{$row}", $ekstra->predikat ?? '');
                    $sheet->setCellValue("D{$row}", $ekstra->keterangan ?? '');
                    $row++;
                }
                // Tambah 3 blank rows untuk user isi kegiatan baru
                for ($i = 0; $i < 3; $i++) {
                    $sheet->setCellValue("A{$row}", count($rapor->kegiatanEkstra) + $i + 1);
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::READONLY_BG]],
                    ]);
                    $row++;
                }
                $this->applyBorder($sheet, "A{$kegiatanStartRow}:D" . ($row - 1));

                // Column widths
                $widths = ['A' => 6, 'B' => 14, 'C' => 14, 'D' => 28, 'E' => 12, 'F' => 10, 'G' => 50, 'H' => 9, 'I' => 18];
                foreach ($widths as $col => $w) {
                    $sheet->getColumnDimension($col)->setWidth($w);
                }

                // Page setup: clean print (no header/footer, fit horizontally)
                $sheet->getHeaderFooter()
                    ->setOddHeader('')->setOddFooter('')
                    ->setEvenHeader('')->setEvenFooter('')
                    ->setFirstHeader('')->setFirstFooter('');
                $sheet->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT)
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
                    ->setFitToPage(true)
                    ->setFitToWidth(1)
                    ->setFitToHeight(0); // 0 = auto-paginate vertical (sesuai panjang konten)
                $sheet->getPageMargins()
                    ->setTop(0.4)->setBottom(0.4)
                    ->setLeft(0.4)->setRight(0.4)
                    ->setHeader(0)->setFooter(0);
                $sheet->setPrintGridlines(false);
            },
        ];
    }

    private function writeSectionMarker($sheet, int $row, string $marker): void
    {
        $sheet->setCellValue("A{$row}", $marker);
        $sheet->mergeCells("A{$row}:I{$row}");
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FF1565C0']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::SECTION_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(22);
    }

    private function styleHeaderRow($sheet, int $row, string $lastCol): void
    {
        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => self::HEADER_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(20);
    }

    private function applyBorder($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
        ]);
    }

    /**
     * Detect kelompok A/B berdasarkan kelompok_override > kelompok_auto (dari nama mapel).
     * Untuk PTS, return "-" (tidak relevan).
     */
    private function detectKelompok($nilai): string
    {
        if ($this->rapor->jenis_rapor !== 'akhir_semester') {
            return '-';
        }
        if ($nilai->kelompok_override) {
            return strtoupper($nilai->kelompok_override);
        }
        // Auto-detect berdasarkan nama mapel (fallback, mirror logic export lama)
        $kelompokA = ['Pendidikan Agama', 'Agama', 'PPKn', 'Bahasa Indonesia', 'IPA', 'IPS', 'Bahasa Inggris', 'Matematika'];
        $namaMapel = $nilai->mataPelajaran->nama_mapel ?? '';
        foreach ($kelompokA as $keyword) {
            if (stripos($namaMapel, $keyword) !== false) {
                return 'A';
            }
        }
        return 'B';
    }
}
