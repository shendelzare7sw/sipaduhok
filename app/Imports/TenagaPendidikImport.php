<?php

namespace App\Imports;

use App\Models\TenagaPendidik;
use App\Models\User;
use App\Models\Cabang;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class TenagaPendidikImport implements ToCollection, WithHeadingRow
{
    private $skippedCount = 0;
    private $importedCount = 0;
    private $cabangList;
    private $roleList;

    private $warnings = [];

    public function __construct()
    {
        $this->cabangList = Cabang::pluck('id', 'nama_cabang')->toArray();
        $this->roleList = Role::pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [strtolower(trim((string) $name)) => $id])
            ->toArray();
    }

    public function collection(Collection $rows)
    {
        $rowNumber = 1;

        foreach ($rows as $row) {
            $rowNumber++;
            $row = $row->toArray();
            $namaLengkap = trim((string) ($row['nama_lengkap'] ?? ''));

            if ($namaLengkap === '') {
                continue;
            }

            $requiredFields = [
                'email',
                'jenis_kelamin',
                'tempat_lahir',
                'tanggal_lahir',
                'alamat',
                'telepon',
                'pendidikan_terakhir',
                'role',
                'nama_cabang',
            ];

            $missingFields = [];
            foreach ($requiredFields as $field) {
                if (trim((string) ($row[$field] ?? '')) === '') {
                    $missingFields[] = $field;
                }
            }

            if (! empty($missingFields)) {
                $this->skippedCount++;
                $this->warnings[] = "Baris {$rowNumber}: Tenaga pendidik dilewati karena kolom wajib kosong: ".implode(', ', $missingFields).'.';
                continue;
            }

            $jenisKelamin = strtoupper(trim((string) $row['jenis_kelamin']));
            if (! in_array($jenisKelamin, ['L', 'P'], true)) {
                $this->skippedCount++;
                $this->warnings[] = "Baris {$rowNumber}: Tenaga pendidik dilewati karena jenis_kelamin harus L atau P.";
                continue;
            }

            $tanggalLahir = $this->parseDate($row['tanggal_lahir'] ?? null);
            if (! $tanggalLahir) {
                $this->skippedCount++;
                $this->warnings[] = "Baris {$rowNumber}: Tenaga pendidik dilewati karena tanggal_lahir tidak valid. Gunakan format YYYY-MM-DD.";
                continue;
            }

            $cabangId = $this->findCabang($row['nama_cabang']);
            if (! $cabangId) {
                $this->skippedCount++;
                $this->warnings[] = "Baris {$rowNumber}: Tenaga pendidik dilewati karena Cabang '{$row['nama_cabang']}' tidak ditemukan.";
                continue;
            }

            $roleName = strtolower(trim((string) $row['role']));
            $roleId = $this->roleList[$roleName] ?? null;
            if (! $roleId) {
                $this->skippedCount++;
                $this->warnings[] = "Baris {$rowNumber}: Tenaga pendidik dilewati karena role '{$row['role']}' tidak tersedia.";
                continue;
            }

            $nip = trim((string) ($row['nip'] ?? ''));
            $email = trim((string) $row['email']);

            $exists = false;
            if ($nip !== '') {
                $exists = TenagaPendidik::where('nip', $nip)->exists();
            }
            if (! $exists) {
                $exists = TenagaPendidik::where('email', $email)->exists();
            }

            if ($exists) {
                $this->skippedCount++;
                $this->warnings[] = "Baris {$rowNumber}: Tenaga pendidik dilewati karena NIP '{$nip}' atau Email '{$email}' sudah ada.";
                continue;
            }

            $username = $nip !== '' ? $nip : Str::slug($namaLengkap).'-'.rand(100, 999);

            DB::beginTransaction();
            try {
                $user = User::where('email', $email)->orWhere('username', $username)->first();

                if (! $user) {
                    $user = User::create([
                        'name' => $namaLengkap,
                        'email' => $email,
                        'username' => $username,
                        'password' => Hash::make('password'),
                        'role' => $roleName,
                        'role_id' => $roleId,
                        'cabang_id' => $cabangId,
                        'phone' => $row['telepon'],
                        'is_active' => true,
                    ]);
                }

                TenagaPendidik::create([
                    'user_id' => $user->id,
                    'nip' => $nip !== '' ? $nip : null,
                    'nama_lengkap' => $namaLengkap,
                    'email' => $email,
                    'telepon' => $row['telepon'],
                    'jenis_kelamin' => $jenisKelamin,
                    'tempat_lahir' => $row['tempat_lahir'],
                    'tanggal_lahir' => $tanggalLahir,
                    'alamat' => $row['alamat'],
                    'pendidikan_terakhir' => $row['pendidikan_terakhir'],
                ]);

                DB::commit();
                $this->importedCount++;

            } catch (\Exception $e) {
                DB::rollBack();
                $this->skippedCount++;
                $this->warnings[] = "Baris {$rowNumber}: Error - ".$e->getMessage();
            }
        }
    }

    private function findCabang($name)
    {
        $searchName = trim((string) $name);
        if ($searchName === '') {
            return null;
        }

        if (isset($this->cabangList[$searchName])) {
            return $this->cabangList[$searchName];
        }

        $normalizedSearch = strtolower($searchName);
        $normalizedSearch = str_replace(['pkbm hok', 'hok'], ['pkbm house of knowledge', 'house of knowledge'], $normalizedSearch);

        foreach ($this->cabangList as $dbName => $id) {
            $normalizedDb = strtolower(trim((string) $dbName));

            if ($normalizedDb === strtolower($searchName)) {
                return $id;
            }

            if ($normalizedDb === $normalizedSearch) {
                return $id;
            }

            if (str_contains($normalizedDb, $normalizedSearch)) {
                return $id;
            }
        }

        return null;
    }

    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

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

    public function getWarnings(): array
    {
        return $this->warnings;
    }
}
