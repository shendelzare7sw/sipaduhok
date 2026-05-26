<?php

namespace App\Imports\WaliKelas;

use App\Exports\RaporExport;
use App\Models\MataPelajaran;
use App\Models\Rapor;
use App\Models\RaporKegiatanEkstra;
use App\Models\RaporNilai;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use RuntimeException;

/**
 * Parser & importer untuk Excel rapor yang di-export via RaporExport.
 * Format: section-based dengan marker (lihat App\Exports\RaporExport).
 *
 * Usage:
 *   $result = (new RaporImport($rapor))->import($file);
 *   // $result = ['success' => bool, 'errors' => [...], 'updated_count' => int]
 */
class RaporImport
{
    private array $errors = [];
    private int $updatedCount = 0;

    public function __construct(public Rapor $rapor) {}

    public function import(UploadedFile $file): array
    {
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();

            $this->validateMetadata($sheet);

            $sections = $this->findSectionRows($sheet);
            $parsedNilai = $this->parseNilaiSection($sheet, $sections);
            $parsedKehadiran = $this->parseKehadiranSection($sheet, $sections);
            $parsedCatatan = $this->parseCatatanSection($sheet, $sections);
            $parsedKegiatan = $this->parseKegiatanSection($sheet, $sections);

            DB::transaction(function () use ($parsedNilai, $parsedKehadiran, $parsedCatatan, $parsedKegiatan) {
                $this->applyNilai($parsedNilai);
                $this->applyKehadiran($parsedKehadiran);
                $this->applyCatatan($parsedCatatan);
                $this->applyKegiatan($parsedKegiatan);
            });

            return [
                'success' => true,
                'errors' => $this->errors,
                'updated_count' => $this->updatedCount,
            ];
        } catch (RuntimeException $e) {
            return [
                'success' => false,
                'errors' => array_merge($this->errors, [$e->getMessage()]),
                'updated_count' => $this->updatedCount,
            ];
        }
    }

    private function validateMetadata(Worksheet $sheet): void
    {
        $metadata = trim((string) $sheet->getCell(RaporExport::METADATA_CELL)->getValue());
        if (!preg_match('/RAPOR_ID:(\d+)\|JENIS:([^|]+)\|SEMESTER:(\w+)/', $metadata, $m)) {
            throw new RuntimeException('File Excel tidak valid: metadata identifier tidak ditemukan. Download ulang template lewat tombol Export Excel.');
        }
        [, $raporId, $jenis, $semester] = $m;
        if ((int) $raporId !== $this->rapor->id) {
            throw new RuntimeException("File ini bukan template untuk rapor ini (RAPOR_ID di file: {$raporId}, ekspektasi: {$this->rapor->id}). Download template yang sesuai.");
        }
        if ($jenis !== $this->rapor->jenis_rapor || $semester !== $this->rapor->semester) {
            throw new RuntimeException('Jenis rapor atau semester di file tidak match dengan rapor saat ini. Download ulang template.');
        }
    }

    private function findSectionRows(Worksheet $sheet): array
    {
        $markers = [
            'nilai' => RaporExport::SECTION_NILAI,
            'kehadiran' => RaporExport::SECTION_KEHADIRAN,
            'catatan' => RaporExport::SECTION_CATATAN,
            'kegiatan' => RaporExport::SECTION_KEGIATAN,
        ];
        $found = [];
        $highestRow = $sheet->getHighestRow();
        for ($row = 1; $row <= $highestRow; $row++) {
            $val = trim((string) $sheet->getCell("A{$row}")->getValue());
            foreach ($markers as $key => $marker) {
                if ($val === $marker) {
                    $found[$key] = $row;
                }
            }
        }
        foreach ($markers as $key => $_) {
            if (!isset($found[$key])) {
                throw new RuntimeException("Section '{$markers[$key]}' tidak ditemukan di file. Download ulang template.");
            }
        }
        return $found;
    }

    private function parseNilaiSection(Worksheet $sheet, array $sections): array
    {
        $start = $sections['nilai'] + 2; // skip marker + header row
        $end = $sections['kehadiran'] - 1;
        $parsed = [];

        $mapelByCode = MataPelajaran::pluck('id', 'kode_mapel')->mapWithKeys(
            fn($id, $code) => [strtolower(trim((string) $code)) => $id]
        );

        for ($row = $start; $row <= $end; $row++) {
            $kodeMapel = trim((string) $sheet->getCell("C{$row}")->getValue());
            if ($kodeMapel === '') continue;

            $mapelId = $mapelByCode[strtolower($kodeMapel)] ?? null;
            if (!$mapelId) {
                $this->errors[] = "Baris {$row} (Nilai): kode mapel '{$kodeMapel}' tidak ditemukan, dilewati.";
                continue;
            }

            $nilaiAngka = $sheet->getCell("E{$row}")->getValue();
            $deskripsi = trim((string) $sheet->getCell("G{$row}")->getValue());
            $visible = strtoupper(trim((string) $sheet->getCell("H{$row}")->getValue()));
            $kelompokOverride = strtoupper(trim((string) $sheet->getCell("I{$row}")->getValue()));

            if ($nilaiAngka !== null && $nilaiAngka !== '') {
                if (!is_numeric($nilaiAngka) || $nilaiAngka < 0 || $nilaiAngka > 100) {
                    $this->errors[] = "Baris {$row} (Nilai mapel {$kodeMapel}): nilai angka '{$nilaiAngka}' tidak valid (harus 0-100), dilewati.";
                    continue;
                }
                $nilaiAngka = (float) $nilaiAngka;
            } else {
                $nilaiAngka = null;
            }

            if ($kelompokOverride !== '' && !in_array($kelompokOverride, ['A', 'B'], true)) {
                $this->errors[] = "Baris {$row} (Nilai mapel {$kodeMapel}): override kelompok '{$kelompokOverride}' tidak valid (harus A/B/kosong).";
                $kelompokOverride = null;
            }

            $parsed[] = [
                'mata_pelajaran_id' => $mapelId,
                'kode_mapel' => $kodeMapel,
                'nilai_angka' => $nilaiAngka,
                'deskripsi' => $deskripsi !== '' ? $deskripsi : null,
                'is_visible' => $visible === 'N' ? false : true,
                'kelompok_override' => $kelompokOverride === '' ? null : $kelompokOverride,
            ];
        }
        return $parsed;
    }

    private function parseKehadiranSection(Worksheet $sheet, array $sections): array
    {
        $valuesRow = $sections['kehadiran'] + 2; // marker + header + data
        $sakit = $sheet->getCell("A{$valuesRow}")->getValue();
        $izin = $sheet->getCell("B{$valuesRow}")->getValue();
        $alpha = $sheet->getCell("C{$valuesRow}")->getValue();

        return [
            'sakit' => $this->parseIntField($sakit, 'sakit'),
            'izin' => $this->parseIntField($izin, 'izin'),
            'alpha' => $this->parseIntField($alpha, 'alpha'),
        ];
    }

    private function parseIntField($value, string $label): int
    {
        if ($value === null || $value === '') return 0;
        if (!is_numeric($value) || $value < 0) {
            $this->errors[] = "Kehadiran '{$label}': nilai '{$value}' tidak valid (harus integer ≥ 0), default 0.";
            return 0;
        }
        return (int) $value;
    }

    private function parseCatatanSection(Worksheet $sheet, array $sections): ?string
    {
        $catatanRow = $sections['catatan'] + 1;
        $value = (string) $sheet->getCell("A{$catatanRow}")->getValue();
        $trimmed = trim($value);
        return $trimmed === '' ? null : $value;
    }

    private function parseKegiatanSection(Worksheet $sheet, array $sections): array
    {
        $start = $sections['kegiatan'] + 2; // skip marker + header
        $end = $sheet->getHighestRow();
        $parsed = [];

        for ($row = $start; $row <= $end; $row++) {
            $nama = trim((string) $sheet->getCell("B{$row}")->getValue());
            if ($nama === '') continue;

            $predikat = strtoupper(trim((string) $sheet->getCell("C{$row}")->getValue()));
            $keterangan = trim((string) $sheet->getCell("D{$row}")->getValue());

            if ($predikat !== '' && !in_array($predikat, ['A', 'B', 'C'], true)) {
                $this->errors[] = "Baris {$row} (Kegiatan '{$nama}'): predikat '{$predikat}' tidak valid (harus A/B/C/kosong), di-set kosong.";
                $predikat = null;
            }

            $parsed[] = [
                'kegiatan_nama' => $nama,
                'predikat' => $predikat ?: null,
                'keterangan' => $keterangan !== '' ? $keterangan : null,
            ];
        }
        return $parsed;
    }

    private function applyNilai(array $parsed): void
    {
        foreach ($parsed as $row) {
            $raporNilai = RaporNilai::where('rapor_id', $this->rapor->id)
                ->where('mata_pelajaran_id', $row['mata_pelajaran_id'])
                ->first();

            if (!$raporNilai) {
                $this->errors[] = "Mapel kode '{$row['kode_mapel']}' tidak ada di rapor ini (mungkin perlu di-generate dulu), dilewati.";
                continue;
            }

            $updateData = [
                'deskripsi' => $row['deskripsi'],
                'is_visible' => $row['is_visible'],
                'kelompok_override' => $row['kelompok_override'],
            ];
            if ($row['nilai_angka'] !== null) {
                $updateData['nilai_angka'] = $row['nilai_angka'];
                $updateData['nilai_huruf'] = $this->hurufFromAngka($row['nilai_angka']);
            }
            $raporNilai->update($updateData);
            $this->updatedCount++;
        }
    }

    private function applyKehadiran(array $kehadiran): void
    {
        $this->rapor->update([
            'jumlah_sakit' => $kehadiran['sakit'],
            'jumlah_izin' => $kehadiran['izin'],
            'jumlah_alpha' => $kehadiran['alpha'],
        ]);
    }

    private function applyCatatan(?string $catatan): void
    {
        $this->rapor->update(['catatan_wali_kelas' => $catatan]);
    }

    private function applyKegiatan(array $parsed): void
    {
        RaporKegiatanEkstra::where('rapor_id', $this->rapor->id)->delete();
        foreach ($parsed as $row) {
            RaporKegiatanEkstra::create([
                'rapor_id' => $this->rapor->id,
                'kegiatan_nama' => $row['kegiatan_nama'],
                'predikat' => $row['predikat'],
                'keterangan' => $row['keterangan'],
            ]);
        }
    }

    private function hurufFromAngka(?float $angka): ?string
    {
        if ($angka === null) return null;
        if ($angka >= 90) return 'A';
        if ($angka >= 80) return 'B';
        if ($angka >= 70) return 'C';
        if ($angka >= 60) return 'D';
        return 'E';
    }
}
