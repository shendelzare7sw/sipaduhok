<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class OrangTuaSeeder extends Seeder
{
    /**
     * Seed orang tua (parents) dan hubungkan dengan siswa
     * Format: 1 orang tua bisa punya banyak anak (many-to-many via student_parents)
     */
    public function run(): void
    {
        // Ambil role orang_tua
        $roleOrangTua = Role::where('name', 'orang_tua')->first();

        if (!$roleOrangTua) {
            $this->command->error('Role orang_tua tidak ditemukan. Jalankan RoleSeeder terlebih dahulu.');
            return;
        }

        // Ambil semua siswa untuk dipasangkan dengan orang tua
        $allSiswa = Siswa::all();

        if ($allSiswa->isEmpty()) {
            $this->command->warn('Tidak ada data siswa. Jalankan SiswaSeeder terlebih dahulu.');
            return;
        }

        // Data orang tua sample
        $orangTuaData = [
            // Orang tua untuk siswa 1-2 (1 orang tua, 2 anak)
            [
                'name' => 'Bapak Agus Suryanto',
                'email' => 'agus.suryanto@parent.com',
                'username' => 'agus_suryanto',
                'password' => Hash::make('password'),
                'role_id' => $roleOrangTua->id,
                'cabang_id' => 1,
                'is_active' => true,
                'children' => [1, 2], // Memiliki 2 anak
                'relationships' => [
                    1 => ['relationship' => 'ayah_kandung', 'is_primary' => true, 'is_financial_responsible' => true],
                    2 => ['relationship' => 'ayah_kandung', 'is_primary' => true, 'is_financial_responsible' => true],
                ]
            ],

            // Orang tua untuk siswa 3
            [
                'name' => 'Ibu Ratna Dewi',
                'email' => 'ratna.dewi@parent.com',
                'username' => 'ratna_dewi',
                'password' => Hash::make('password'),
                'role_id' => $roleOrangTua->id,
                'cabang_id' => 1,
                'is_active' => true,
                'children' => [3],
                'relationships' => [
                    3 => ['relationship' => 'ibu_kandung', 'is_primary' => true, 'is_financial_responsible' => true],
                ]
            ],

            // Orang tua untuk siswa 4-5 (1 orang tua, 2 anak)
            [
                'name' => 'Bapak Hendra Wijaya',
                'email' => 'hendra.wijaya@parent.com',
                'username' => 'hendra_wijaya',
                'password' => Hash::make('password'),
                'role_id' => $roleOrangTua->id,
                'cabang_id' => 1,
                'is_active' => true,
                'children' => [4, 5],
                'relationships' => [
                    4 => ['relationship' => 'ayah_kandung', 'is_primary' => true, 'is_financial_responsible' => true],
                    5 => ['relationship' => 'ayah_kandung', 'is_primary' => true, 'is_financial_responsible' => true],
                ]
            ],

            // Orang tua untuk siswa 6
            [
                'name' => 'Ibu Sari Indah',
                'email' => 'sari.indah@parent.com',
                'username' => 'sari_indah',
                'password' => Hash::make('password'),
                'role_id' => $roleOrangTua->id,
                'cabang_id' => 1,
                'is_active' => true,
                'children' => [6],
                'relationships' => [
                    6 => ['relationship' => 'ibu_kandung', 'is_primary' => true, 'is_financial_responsible' => true],
                ]
            ],

            // Orang tua untuk siswa 7-8 (Cabang PAUD - 1 orang tua, 2 anak)
            [
                'name' => 'Bapak Rizal Hidayat',
                'email' => 'rizal.hidayat@parent.com',
                'username' => 'rizal_hidayat',
                'password' => Hash::make('password'),
                'role_id' => $roleOrangTua->id,
                'cabang_id' => 2,
                'is_active' => true,
                'children' => [7, 8],
                'relationships' => [
                    7 => ['relationship' => 'ayah_kandung', 'is_primary' => true, 'is_financial_responsible' => true],
                    8 => ['relationship' => 'ayah_kandung', 'is_primary' => true, 'is_financial_responsible' => true],
                ]
            ],

            // Orang tua untuk siswa 9
            [
                'name' => 'Ibu Nur Aini',
                'email' => 'nur.aini@parent.com',
                'username' => 'nur_aini',
                'password' => Hash::make('password'),
                'role_id' => $roleOrangTua->id,
                'cabang_id' => 2,
                'is_active' => true,
                'children' => [9],
                'relationships' => [
                    9 => ['relationship' => 'ibu_kandung', 'is_primary' => true, 'is_financial_responsible' => true],
                ]
            ],

            // Orang tua untuk siswa 10-11 (Cabang PAUD - 1 orang tua, 2 anak)
            [
                'name' => 'Bapak Bambang Sutrisno',
                'email' => 'bambang.sutrisno@parent.com',
                'username' => 'bambang_sutrisno',
                'password' => Hash::make('password'),
                'role_id' => $roleOrangTua->id,
                'cabang_id' => 2,
                'is_active' => true,
                'children' => [10, 11],
                'relationships' => [
                    10 => ['relationship' => 'ayah_kandung', 'is_primary' => true, 'is_financial_responsible' => true],
                    11 => ['relationship' => 'ayah_kandung', 'is_primary' => true, 'is_financial_responsible' => true],
                ]
            ],

            // Orang tua untuk siswa 12
            [
                'name' => 'Ibu Lina Marlina',
                'email' => 'lina.marlina@parent.com',
                'username' => 'lina_marlina',
                'password' => Hash::make('password'),
                'role_id' => $roleOrangTua->id,
                'cabang_id' => 2,
                'is_active' => true,
                'children' => [12],
                'relationships' => [
                    12 => ['relationship' => 'ibu_kandung', 'is_primary' => true, 'is_financial_responsible' => true],
                ]
            ],

            // Orang tua untuk siswa 13-14 (Cabang Cimanggis - 1 orang tua, 2 anak)
            [
                'name' => 'Bapak Yudi Prasetyo',
                'email' => 'yudi.prasetyo@parent.com',
                'username' => 'yudi_prasetyo',
                'password' => Hash::make('password'),
                'role_id' => $roleOrangTua->id,
                'cabang_id' => 3,
                'is_active' => true,
                'children' => [13, 14],
                'relationships' => [
                    13 => ['relationship' => 'ayah_kandung', 'is_primary' => true, 'is_financial_responsible' => true],
                    14 => ['relationship' => 'ayah_kandung', 'is_primary' => true, 'is_financial_responsible' => true],
                ]
            ],

            // Orang tua untuk siswa 15
            [
                'name' => 'Ibu Devi Anggraini',
                'email' => 'devi.anggraini@parent.com',
                'username' => 'devi_anggraini',
                'password' => Hash::make('password'),
                'role_id' => $roleOrangTua->id,
                'cabang_id' => 3,
                'is_active' => true,
                'children' => [15],
                'relationships' => [
                    15 => ['relationship' => 'ibu_kandung', 'is_primary' => true, 'is_financial_responsible' => true],
                ]
            ],

            // Orang tua untuk siswa 16-17 (Cabang Cimanggis - 1 orang tua, 2 anak)
            [
                'name' => 'Bapak Wahyu Firmansyah',
                'email' => 'wahyu.firmansyah@parent.com',
                'username' => 'wahyu_firmansyah',
                'password' => Hash::make('password'),
                'role_id' => $roleOrangTua->id,
                'cabang_id' => 3,
                'is_active' => true,
                'children' => [16, 17],
                'relationships' => [
                    16 => ['relationship' => 'ayah_kandung', 'is_primary' => true, 'is_financial_responsible' => true],
                    17 => ['relationship' => 'ayah_kandung', 'is_primary' => true, 'is_financial_responsible' => true],
                ]
            ],

            // Orang tua untuk siswa 18
            [
                'name' => 'Ibu Maya Kartika',
                'email' => 'maya.kartika@parent.com',
                'username' => 'maya_kartika',
                'password' => Hash::make('password'),
                'role_id' => $roleOrangTua->id,
                'cabang_id' => 3,
                'is_active' => true,
                'children' => [18],
                'relationships' => [
                    18 => ['relationship' => 'ibu_kandung', 'is_primary' => true, 'is_financial_responsible' => true],
                ]
            ],
        ];

        foreach ($orangTuaData as $parentData) {
            // Extract children and relationships before creating user
            $childrenIds = $parentData['children'];
            $relationships = $parentData['relationships'];
            unset($parentData['children'], $parentData['relationships']);

            // Create parent user
            $parent = User::create($parentData);

            // Attach children with relationships using student_parents pivot
            foreach ($childrenIds as $siswaId) {
                $siswa = $allSiswa->where('id', $siswaId)->first();

                if ($siswa) {
                    DB::table('student_parents')->insert([
                        'siswa_id' => $siswa->id,
                        'parent_id' => $parent->id,
                        'relationship' => $relationships[$siswaId]['relationship'],
                        'is_primary' => $relationships[$siswaId]['is_primary'],
                        'is_financial_responsible' => $relationships[$siswaId]['is_financial_responsible'],
                        'can_access_academic' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            $this->command->info("✓ Created parent: {$parent->name} with " . count($childrenIds) . " child(ren)");
        }

        $this->command->info('Orang tua seeded successfully!');
    }
}
