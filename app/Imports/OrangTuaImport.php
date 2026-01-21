<?php

namespace App\Imports;

use App\Models\User;
use App\Models\StudentParent;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class OrangTuaImport implements ToCollection, WithHeadingRow
{
    private $skippedCount = 0;
    private $importedCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $row = $row->toArray();

            // Skip empty rows
            if (empty($row['nama']) || trim($row['nama']) === '') {
                continue;
            }

            // Skip instruction/header rows
            $namaLower = strtolower(trim($row['nama']));
            if (in_array($namaLower, ['contoh', 'petunjuk', 'instruksi', 'nama lengkap', 'nama orang tua'])) {
                continue;
            }
            if (str_contains($namaLower, 'contoh') || str_contains($namaLower, 'petunjuk')) {
                continue;
            }

            // Prepare username and email
            $username = !empty($row['username']) ? $row['username'] : Str::slug($row['nama']) . '-' . rand(100, 999);
            $email = !empty($row['email']) ? $row['email'] : $username . '@orangtua.sipaduhok.com';

            // Check if user already exists by email or username
            $exists = User::where('email', $email)->orWhere('username', $username)->exists();

            if ($exists) {
                $this->skippedCount++;
                continue;
            }

            DB::beginTransaction();
            try {
                // Create user account
                $user = User::create([
                    'name' => $row['nama'],
                    'email' => $email,
                    'username' => $username,
                    'password' => Hash::make('password'),
                    'phone' => $row['telepon'] ?? null,
                    'role' => 'orang_tua',
                    'is_active' => true,
                ]);

                // Link to siswa if NIS provided
                if (!empty($row['nis_anak'])) {
                    $nisArray = explode(',', $row['nis_anak']);
                    foreach ($nisArray as $nis) {
                        $nis = trim($nis);
                        $siswa = Siswa::where('nis', $nis)->first();
                        if ($siswa) {
                            StudentParent::create([
                                'siswa_id' => $siswa->id,
                                'parent_id' => $user->id,
                                'relationship' => $row['hubungan'] ?? 'Orang Tua',
                                'is_primary' => true,
                                'is_financial_responsible' => true,
                                'can_access_academic' => true,
                            ]);
                        }
                    }
                }

                DB::commit();
                $this->importedCount++;

            } catch (\Exception $e) {
                DB::rollBack();
                $this->skippedCount++;
                \Log::error('OrangTua Import Error: ' . $e->getMessage() . ' | Row: ' . json_encode($row));
            }
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
}
