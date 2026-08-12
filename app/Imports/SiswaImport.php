<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Cabang;
use App\Models\Role;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class SiswaImport implements ToCollection, WithHeadingRow
{
    private $skippedCount = 0;
    private $importedCount = 0;
    private $kelasList;
    private $cabangList;

    // Tracking
    private $missingKelas = [];
    private $warnings = [];

    public function __construct()
    {
        // Kelas dibatasi ke tahun ajaran aktif saja. Nama kelas (mis. "7A") dipakai
        // ulang tiap tahun ajaran & tiap cabang, jadi tanpa pembatasan ini import bisa
        // "nyangkut" ke kelas cabang/tahun lain yang kebetulan namanya sama.
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $this->kelasList = Kelas::when($tahunAjaranAktif, fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id))
            ->get(['id', 'nama_kelas', 'cabang_id']);
        $this->cabangList = Cabang::pluck('id', 'nama_cabang')->toArray();
    }

    public function collection(Collection $rows)
    {
        $rowNumber = 1;

        foreach ($rows as $row) {
            $rowNumber++;
            $row = $row->toArray();

            // Skip empty rows
            if (empty($row['nama_lengkap']) || trim($row['nama_lengkap']) === '') {
                continue;
            }

            // Skip instruction/header rows
            $namaLower = strtolower(trim($row['nama_lengkap']));
            if (in_array($namaLower, ['contoh', 'petunjuk', 'instruksi', 'nama lengkap', 'nama siswa'])) {
                continue;
            }
            if (str_contains($namaLower, 'contoh') || str_contains($namaLower, 'petunjuk')) {
                continue;
            }

            // Check for duplicate NIS or NISN
            $exists = false;
            if (!empty($row['nis'])) {
                $exists = Siswa::where('nis', $row['nis'])->exists();
            }
            if (!$exists && !empty($row['nisn'])) {
                $exists = Siswa::where('nisn', $row['nisn'])->exists();
            }

            if ($exists) {
                $this->skippedCount++;
                $this->warnings[] = "Baris {$rowNumber}: Siswa dilewati karena NIS '{$row['nis']}' atau NISN '{$row['nisn']}' sudah ada di sistem.";
                continue;
            }

            // Field wajib (konsisten dgn form manual dan kebutuhan LMS).
            $jenisKelamin = strtoupper(trim((string) ($row['jenis_kelamin'] ?? '')));
            $tanggalLahir = $this->parseDate($row['tanggal_lahir'] ?? null);
            $agama = $this->normalizeAgama($row['agama'] ?? null);

            $wajibKosong = [];
            if (empty($row['nama_kelas'])) $wajibKosong[] = 'nama_kelas';
            if (empty($row['jenis_kelamin'])) $wajibKosong[] = 'jenis_kelamin';
            if (empty($row['tempat_lahir'])) $wajibKosong[] = 'tempat_lahir';
            if (empty($row['tanggal_lahir'])) $wajibKosong[] = 'tanggal_lahir';
            if (empty($row['alamat'])) $wajibKosong[] = 'alamat';
            if (empty($row['agama'])) $wajibKosong[] = 'agama';
            if (!empty($wajibKosong)) {
                $this->skippedCount++;
                $this->warnings[] = "Baris {$rowNumber}: dilewati karena kolom wajib kosong: " . implode(', ', $wajibKosong) . ".";
                continue;
            }

            if (! in_array($jenisKelamin, ['L', 'P'], true)) {
                $this->skippedCount++;
                $this->warnings[] = "Baris {$rowNumber}: dilewati karena jenis_kelamin harus L atau P.";
                continue;
            }

            if (! $tanggalLahir) {
                $this->skippedCount++;
                $this->warnings[] = "Baris {$rowNumber}: dilewati karena tanggal_lahir tidak valid. Gunakan format YYYY-MM-DD.";
                continue;
            }

            if (! $agama) {
                $this->skippedCount++;
                $this->warnings[] = "Baris {$rowNumber}: dilewati karena agama harus salah satu: Islam, Kristen, Katolik, Hindu, Buddha, Konghucu.";
                continue;
            }

            // Lookup cabang dulu (kelas dicari di bawah dibatasi ke cabang ini, supaya
            // nama kelas yang sama di cabang lain tidak ikut cocok).
            $cabangId = null;
            if (!empty($row['nama_cabang'])) {
                $cabangId = $this->findCabang($row['nama_cabang']);
                if (! $cabangId) {
                    $this->skippedCount++;
                    $this->warnings[] = "Baris {$rowNumber}: Siswa dilewati karena Cabang '{$row['nama_cabang']}' tidak ditemukan.";
                    continue;
                }
            }

            // Lookup kelas wajib agar data siswa konsisten dengan form manual.
            $kelasId = null;
            if (!empty($row['nama_kelas'])) {
                $kelasId = $this->findKelas($row['nama_kelas'], $cabangId);
                if (!$kelasId) {
                    $kelasName = trim($row['nama_kelas']);
                    if (!in_array($kelasName, $this->missingKelas)) {
                        $this->missingKelas[] = $kelasName;
                    }
                    $this->skippedCount++;
                    $this->warnings[] = "Baris {$rowNumber}: Siswa dilewati karena kelas '{$kelasName}' tidak ditemukan atau ambigu. Isi nama_cabang jika nama kelas ada di beberapa cabang.";
                    continue;
                }
            }

            // Default cabang from kelas if not specified
            if (!$cabangId && $kelasId) {
                $kelas = $this->kelasList->firstWhere('id', $kelasId);
                $cabangId = $kelas?->cabang_id;
            }

            // Validation: Cabang is required
            if (!$cabangId) {
                $this->skippedCount++;
                $this->warnings[] = "Baris {$rowNumber}: Siswa dilewati karena Cabang tidak ditemukan (isi kolom nama_cabang atau pastikan nama_kelas valid).";
                continue;
            }

            // Prepare User data
            $username = !empty($row['username']) ? $row['username'] : (!empty($row['nis']) ? $row['nis'] : Str::slug($row['nama_lengkap']) . '-' . rand(100, 999));
            $email = !empty($row['email']) ? $row['email'] : $username . '@siswa.sipaduhok.com';

            // Normalisasi status. Template menawarkan "aktif/nonaktif", sedangkan enum
            // siswa.status = aktif|lulus|pindah|keluar (TIDAK ada 'nonaktif'). Menulis
            // 'nonaktif' langsung => nilai enum invalid => baris gagal/broken data.
            // "nonaktif" diterjemahkan sbg akun dinonaktifkan (is_active=false), status
            // akademik tetap 'aktif'. Nilai enum asli tetap diterima apa adanya.
            $rawStatus = strtolower(trim((string)($row['status'] ?? 'aktif')));
            if (in_array($rawStatus, ['aktif', 'lulus', 'pindah', 'keluar'], true)) {
                $siswaStatus = $rawStatus;
                // ALUMNI ('lulus') akunnya sengaja TETAP AKTIF - mereka masih harus bisa
                // login untuk mengecek/melunasi tunggakan, dan tagihannya wajib tetap
                // terlihat Bendahara (fitur "Alumni Menunggak"). Ini menyamakan perilaku
                // dengan PromotionService::executeStudentPromotion() yang saat meluluskan
                // siswa juga memaksa is_active = true. Sebelumnya import menonaktifkan
                // akun alumni, sehingga alumni + tunggakannya raib dari menu Bendahara.
                // 'pindah'/'keluar' memang sudah tidak bersekolah -> akun dinonaktifkan.
                $isActive = in_array($rawStatus, ['aktif', 'lulus'], true);
            } elseif (in_array($rawStatus, ['nonaktif', 'non-aktif', 'non aktif', 'tidak aktif', 'inactive', '0', 'false'], true)) {
                $siswaStatus = 'aktif';
                $isActive = false;
            } else {
                $siswaStatus = 'aktif';
                $isActive = true;
            }

            DB::beginTransaction();
            try {
                $user = User::where('email', $email)->orWhere('username', $username)->first();

                if (!$user) {
                    $siswaRole = Role::where('name', 'siswa')->first();
                    $user = User::create([
                        'name' => $row['nama_lengkap'],
                        'email' => $email,
                        'username' => $username,
                        'password' => Hash::make('password'),
                        'role' => 'siswa',
                        'role_id' => $siswaRole ? $siswaRole->id : null,
                        'cabang_id' => $cabangId,
                        'is_active' => $isActive,
                    ]);
                }

                Siswa::create([
                    'user_id' => $user->id,
                    'cabang_id' => $cabangId,
                    'kelas_id' => $kelasId,
                    'nis' => $row['nis'] ?? null,
                    'nisn' => $row['nisn'] ?? null,
                    'nama_lengkap' => $row['nama_lengkap'],
                    'jenis_kelamin' => $jenisKelamin,
                    'tempat_lahir' => $row['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $tanggalLahir,
                    'alamat' => $row['alamat'] ?? null,
                    'nama_ayah' => $row['nama_ayah'] ?? null,
                    'nama_ibu' => $row['nama_ibu'] ?? null,
                    'telepon_orangtua' => $row['telepon_orangtua'] ?? null,
                    'status' => $siswaStatus,
                    'tanggal_masuk' => $this->parseDate($row['tanggal_masuk'] ?? null) ?? now()->toDateString(),
                    'agama' => $agama,
                ]);

                DB::commit();
                $this->importedCount++;

            } catch (\Exception $e) {
                DB::rollBack();
                $this->skippedCount++;
                $this->warnings[] = "Baris {$rowNumber}: Error - " . $e->getMessage();
            }
        }
    }

    private function findKelas($name, $cabangId = null)
    {
        $name = strtolower(trim($name));

        $candidates = $this->kelasList->filter(
            fn ($k) => strtolower(trim($k->nama_kelas)) === $name
        );

        if ($cabangId) {
            $inCabang = $candidates->firstWhere('cabang_id', $cabangId);
            if ($inCabang) {
                return $inCabang->id;
            }
            // Cabang diketahui tapi tidak ada kelas dgn nama ini di cabang tsb -> jangan
            // jatuh ke kelas cabang lain yg kebetulan namanya sama.
            return null;
        }

        if ($candidates->count() > 1) {
            return null;
        }

        return $candidates->first()?->id;
    }

    private function findCabang($name)
    {
        $name = trim($name);
        if (isset($this->cabangList[$name]))
            return $this->cabangList[$name];

        // Normalization for matching
        $normalizedSearch = strtolower($name);
        $normalizedSearch = str_replace(['pkbm hok', 'hok'], ['pkbm house of knowledge', 'house of knowledge'], $normalizedSearch);

        foreach ($this->cabangList as $n => $id) {
            $normalizedDb = strtolower(trim($n));
            
            // Exact match
            if ($normalizedDb === strtolower($name))
                return $id;
            
            // Match with expanded abbreviations
            if ($normalizedDb === $normalizedSearch)
                return $id;

            // Partial match (be careful) - only if "PKBM HOK" matches start of DB name
            if (str_contains($normalizedDb, $normalizedSearch) || str_contains($normalizedSearch, $normalizedDb)) {
                // Prefer specific matches, but if we have "PKBM House of Knowledge" and user typed "PKBM HOK" converted to "PKBM House of Knowledge", it matches above.
                // If user typed "PKBM HOK" and db is "PKBM House Of Knowledge (Gedung Utama)", the expanded search is "pkbm house of knowledge" which is contained in db name.
                return $id;
            }
        }
        return null;
    }

    private function parseDate($value)
    {
        if (empty($value))
            return null;
        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
        try {
            $timestamp = strtotime((string) $value);
            if ($timestamp === false) {
                return null;
            }

            return date('Y-m-d', $timestamp);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
    public function getMissingKelas(): array
    {
        return $this->missingKelas;
    }
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    /**
     * Normalize agama value from import to one of 6 official religions.
     */
    private function normalizeAgama(?string $agama): ?string
    {
        if (!$agama) return null;

        $agama = strtolower(trim($agama));

        $map = [
            'islam' => 'Islam',
            'muslim' => 'Islam',
            'kristen' => 'Kristen',
            'kristen protestan' => 'Kristen',
            'protestan' => 'Kristen',
            'katolik' => 'Katolik',
            'katholik' => 'Katolik',
            'kristen katolik' => 'Katolik',
            'hindu' => 'Hindu',
            'buddha' => 'Buddha',
            'budha' => 'Buddha',
            'buddhis' => 'Buddha',
            'konghucu' => 'Konghucu',
            'khonghucu' => 'Konghucu',
            'kong hu cu' => 'Konghucu',
            'konfusius' => 'Konghucu',
        ];

        // Exact match first
        if (isset($map[$agama])) {
            return $map[$agama];
        }

        // Partial match fallback
        foreach ($map as $keyword => $standardized) {
            if (str_contains($agama, $keyword)) {
                return $standardized;
            }
        }

        // Return null if unrecognized; caller will show a row warning.
        return null;
    }
}
