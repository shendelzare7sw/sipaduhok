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
        $this->roleList = Role::pluck('id', 'name')->toArray();
    }

    public function collection(Collection $rows)
    {
        $rowNumber = 1;

        foreach ($rows as $row) {
            $rowNumber++;
            $row = $row->toArray();

            // Skip empty rows - check nama_lengkap
            if (empty($row['nama_lengkap']) || trim($row['nama_lengkap']) === '') {
                continue;
            }

            // Check for duplicate NIP or email in TenagaPendidik
            $exists = false;
            if (!empty($row['nip'])) {
                $exists = TenagaPendidik::where('nip', $row['nip'])->exists();
            }
            if (!$exists && !empty($row['email'])) {
                $exists = TenagaPendidik::where('email', $row['email'])->exists();
            }

            if ($exists) {
                $this->skippedCount++;
                $this->warnings[] = "Baris {$rowNumber}: Tenaga Pendidik dilewati karena NIP '{$row['nip']}' atau Email '{$row['email']}' sudah ada.";
                continue;
            }

            // Lookup cabang
            $cabangId = null;
            if (!empty($row['nama_cabang'])) {
                $cabangId = $this->cabangList[$row['nama_cabang']] ?? null;
                if (!$cabangId) {
                    foreach ($this->cabangList as $name => $id) {
                        if (strtolower(trim($name)) === strtolower(trim($row['nama_cabang']))) {
                            $cabangId = $id;
                            break;
                        }
                    }
                }
            }

            // Lookup role
            $roleName = strtolower(trim($row['role'] ?? 'guru_pengajar'));
            $roleId = $this->roleList[$roleName] ?? $this->roleList['guru_pengajar'] ?? null;

            // Prepare User data
            $username = !empty($row['nip']) ? $row['nip'] : Str::slug($row['nama_lengkap']) . '-' . rand(100, 999);
            $email = !empty($row['email']) ? $row['email'] : $username . '@guru.sipaduhok.com';

            DB::beginTransaction();
            try {
                // Check if user already exists
                $user = User::where('email', $email)->orWhere('username', $username)->first();

                if (!$user) {
                    $user = User::create([
                        'name' => $row['nama_lengkap'],
                        'email' => $email,
                        'username' => $username,
                        'password' => Hash::make('password'),
                        'role' => $roleName,
                        'role_id' => $roleId,
                        'cabang_id' => $cabangId,
                        'is_active' => true,
                    ]);
                }

                // Create TenagaPendidik
                TenagaPendidik::create([
                    'user_id' => $user->id,
                    'nip' => $row['nip'] ?? null,
                    'nama_lengkap' => $row['nama_lengkap'],
                    'email' => $email,
                    'telepon' => $row['telepon'] ?? null,
                    'jenis_kelamin' => strtoupper($row['jenis_kelamin'] ?? 'L') === 'P' ? 'P' : 'L',
                    'tempat_lahir' => $row['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $this->parseDate($row['tanggal_lahir'] ?? null),
                    'alamat' => $row['alamat'] ?? null,
                    'pendidikan_terakhir' => $row['pendidikan_terakhir'] ?? null,
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
    public function getWarnings(): array
    {
        return $this->warnings;
    }
}
