<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Cabang;
use App\Models\Role;
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
        $this->kelasList = Kelas::pluck('id', 'nama_kelas')->toArray();
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

            // Lookup kelas (optional - siswa tetap dibuat)
            $kelasId = null;
            if (!empty($row['nama_kelas'])) {
                $kelasId = $this->findKelas($row['nama_kelas']);
                if (!$kelasId) {
                    $kelasName = trim($row['nama_kelas']);
                    if (!in_array($kelasName, $this->missingKelas)) {
                        $this->missingKelas[] = $kelasName;
                    }
                    $this->warnings[] = "Baris {$rowNumber}: Kelas '{$kelasName}' tidak ditemukan, siswa dibuat tanpa kelas";
                }
            }

            // Lookup cabang  
            $cabangId = null;
            if (!empty($row['nama_cabang'])) {
                $cabangId = $this->findCabang($row['nama_cabang']);
            }
            // Default cabang from kelas if not specified
            if (!$cabangId && $kelasId) {
                $kelas = Kelas::find($kelasId);
                $cabangId = $kelas ? $kelas->cabang_id : null;
            }

            // Prepare User data
            $username = !empty($row['nis']) ? $row['nis'] : Str::slug($row['nama_lengkap']) . '-' . rand(100, 999);
            $email = !empty($row['email']) ? $row['email'] : $username . '@siswa.sipaduhok.com';

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
                        'is_active' => true,
                    ]);
                }

                Siswa::create([
                    'user_id' => $user->id,
                    'cabang_id' => $cabangId,
                    'kelas_id' => $kelasId,
                    'nis' => $row['nis'] ?? null,
                    'nisn' => $row['nisn'] ?? null,
                    'nama_lengkap' => $row['nama_lengkap'],
                    'jenis_kelamin' => strtoupper($row['jenis_kelamin'] ?? 'L') === 'P' ? 'P' : 'L',
                    'tempat_lahir' => $row['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $this->parseDate($row['tanggal_lahir'] ?? null),
                    'alamat' => $row['alamat'] ?? null,
                    'nama_ayah' => $row['nama_ayah'] ?? null,
                    'nama_ibu' => $row['nama_ibu'] ?? null,
                    'telepon_orangtua' => $row['telepon_orangtua'] ?? null,
                    'status' => strtolower($row['status'] ?? 'aktif') === 'nonaktif' ? 'nonaktif' : 'aktif',
                    'tanggal_masuk' => $this->parseDate($row['tanggal_masuk'] ?? now()), // Default to now if missing
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

    private function findKelas($name)
    {
        $name = trim($name);
        if (isset($this->kelasList[$name]))
            return $this->kelasList[$name];
        foreach ($this->kelasList as $n => $id) {
            if (strtolower(trim($n)) === strtolower($name))
                return $id;
        }
        return null;
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
            return date('Y-m-d', strtotime($value));
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
}
