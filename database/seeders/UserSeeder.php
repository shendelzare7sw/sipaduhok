<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Get role IDs
        $roles = Role::pluck('id', 'name');

        // Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@sipaduhok.com',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'role_id' => $roles['admin'],
            'cabang_id' => 1,
            'is_active' => true,
        ]);

        // Ketua PKBM
        User::create([
            'name' => 'Dr. Budi Santoso',
            'email' => 'ketua@sipaduhok.com',
            'username' => 'ketua_pkbm',
            'password' => Hash::make('password'),
            'role' => 'ketua_pkbm',
            'role_id' => $roles['ketua_pkbm'],
            'cabang_id' => 1,
            'is_active' => true,
        ]);

        // Wakil Kepala Sekolah
        User::create([
            'name' => 'Drs. Harianto Wijaya, M.Pd',
            'email' => 'waka@sipaduhok.com',
            'username' => 'wakil_kepala',
            'password' => Hash::make('password'),
            'role' => 'wakil_kepala_sekolah',
            'role_id' => $roles['wakil_kepala_sekolah'],
            'cabang_id' => 1,
            'is_active' => true,
        ]);

        // Sekretaris
        User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'sekretaris@sipaduhok.com',
            'username' => 'sekretaris',
            'password' => Hash::make('password'),
            'role' => 'sekretaris',
            'role_id' => $roles['sekretaris'],
            'cabang_id' => 1,
            'is_active' => true,
        ]);

        // Bendahara
        User::create([
            'name' => 'Ahmad Dahlan',
            'email' => 'bendahara@sipaduhok.com',
            'username' => 'bendahara',
            'password' => Hash::make('password'),
            'role' => 'bendahara',
            'role_id' => $roles['bendahara'],
            'cabang_id' => 1,
            'is_active' => true,
        ]);

        // Wali Kelas (12 orang) - Format: wali.namaguru@sipaduhok.com (TIDAK TERIKAT JENJANG)
        $waliKelasData = [
            ['name' => 'Hendra Kusuma, S.Pd', 'email' => 'wali.hendrakusuma@sipaduhok.com', 'username' => 'wali_hendrakusuma', 'cabang_id' => 1],
            ['name' => 'Nurul Hidayah, S.Paud', 'email' => 'wali.nurulhidayah@sipaduhok.com', 'username' => 'wali_nurulhidayah', 'cabang_id' => 2],
            ['name' => 'Arif Budiman, S.Pd', 'email' => 'wali.arifbudiman@sipaduhok.com', 'username' => 'wali_arifbudiman', 'cabang_id' => 3],
            ['name' => 'Linda Permata, S.Pd', 'email' => 'wali.lindapermata@sipaduhok.com', 'username' => 'wali_lindapermata', 'cabang_id' => 3],
            ['name' => 'Rudi Hartono, S.Pd', 'email' => 'wali.rudihartono@sipaduhok.com', 'username' => 'wali_rudihartono', 'cabang_id' => 1],
            ['name' => 'Mega Puspita, S.Paud', 'email' => 'wali.megapuspita@sipaduhok.com', 'username' => 'wali_megapuspita', 'cabang_id' => 2],
            ['name' => 'Faisal Rahman, S.Pd', 'email' => 'wali.faisalrahman@sipaduhok.com', 'username' => 'wali_faisalrahman', 'cabang_id' => 1],
            ['name' => 'Dian Anggraini, S.Pd', 'email' => 'wali.diananggraini@sipaduhok.com', 'username' => 'wali_diananggraini', 'cabang_id' => 3],
            ['name' => 'Dewi Lestari, S.Pd', 'email' => 'wali.dewilestari@sipaduhok.com', 'username' => 'wali_dewilestari', 'cabang_id' => 1],
            ['name' => 'Lilis Suryani, S.Paud', 'email' => 'wali.lilissuryani@sipaduhok.com', 'username' => 'wali_lilissuryani', 'cabang_id' => 2],
            ['name' => 'Yoga Pratama, S.Pd', 'email' => 'wali.yogapratama@sipaduhok.com', 'username' => 'wali_yogapratama', 'cabang_id' => 1],
            ['name' => 'Sri Mulyani, S.Pd', 'email' => 'wali.srimulyani@sipaduhok.com', 'username' => 'wali_srimulyani', 'cabang_id' => 1],
        ];

        foreach ($waliKelasData as $wali) {
            User::create([
                'name' => $wali['name'],
                'email' => $wali['email'],
                'username' => $wali['username'],
                'password' => Hash::make('password'),
                'role' => 'wali_kelas',
                'role_id' => $roles['wali_kelas'],
                'cabang_id' => $wali['cabang_id'],
                'is_active' => true,
            ]);
        }

        // Guru Pengajar (10 orang) - Format: guru.namaguru@sipaduhok.com
        $guruData = [
            ['name' => 'Bambang Sutrisno, S.Si', 'email' => 'guru.bambangsutrisno@sipaduhok.com', 'username' => 'guru_bambangsutrisno', 'cabang_id' => 2],
            ['name' => 'Ratna Sari, S.Pd', 'email' => 'guru.ratnasari@sipaduhok.com', 'username' => 'guru_ratnasari', 'cabang_id' => 3],
            ['name' => 'Agus Setiawan, M.Si', 'email' => 'guru.agussetiawan@sipaduhok.com', 'username' => 'guru_agussetiawan', 'cabang_id' => 1],
            ['name' => 'Wulan Dari, S.Pd', 'email' => 'guru.wulandari@sipaduhok.com', 'username' => 'guru_wulandari', 'cabang_id' => 1],
            ['name' => 'Hendro Wijaya, S.Si', 'email' => 'guru.hendrowijaya@sipaduhok.com', 'username' => 'guru_hendrowijaya', 'cabang_id' => 3],
            ['name' => 'Sinta Dewi, S.Pd', 'email' => 'guru.sintadewi@sipaduhok.com', 'username' => 'guru_sintadewi', 'cabang_id' => 1],
            ['name' => 'Yudi Santoso, M.Pd', 'email' => 'guru.yudisantoso@sipaduhok.com', 'username' => 'guru_yudisantoso', 'cabang_id' => 3],
            ['name' => 'Ani Susanti, S.Pd', 'email' => 'guru.anisusanti@sipaduhok.com', 'username' => 'guru_anisusanti', 'cabang_id' => 1],
            ['name' => 'Budi Prasetyo, S.E', 'email' => 'guru.budiprasetyo@sipaduhok.com', 'username' => 'guru_budiprasetyo', 'cabang_id' => 3],
            ['name' => 'Maya Kartika, S.Pd', 'email' => 'guru.mayakartika@sipaduhok.com', 'username' => 'guru_mayakartika', 'cabang_id' => 1],
        ];

        foreach ($guruData as $guru) {
            User::create([
                'name' => $guru['name'],
                'email' => $guru['email'],
                'username' => $guru['username'],
                'password' => Hash::make('password'),
                'role' => 'guru_pengajar',
                'role_id' => $roles['guru_pengajar'],
                'cabang_id' => $guru['cabang_id'],
                'is_active' => true,
            ]);
        }

        // Siswa (18 orang - 6 per cabang)
        $siswaData = [
            // Cabang Ruko (6 siswa)
            ['name' => 'Andi Wijaya', 'email' => 'andi.wijaya@student.com', 'username' => 'andi_wijaya', 'cabang_id' => 1],
            ['name' => 'Putri Maharani', 'email' => 'putri.maharani@student.com', 'username' => 'putri_maharani', 'cabang_id' => 1],
            ['name' => 'Dimas Saputra', 'email' => 'dimas.saputra@student.com', 'username' => 'dimas_saputra', 'cabang_id' => 1],
            ['name' => 'Lina Rahmawati', 'email' => 'lina.rahmawati@student.com', 'username' => 'lina_rahmawati', 'cabang_id' => 1],
            ['name' => 'Fahmi Hidayat', 'email' => 'fahmi.hidayat@student.com', 'username' => 'fahmi_hidayat', 'cabang_id' => 1],
            ['name' => 'Nabila Azzahra', 'email' => 'nabila.azzahra@student.com', 'username' => 'nabila_azzahra', 'cabang_id' => 1],

            // Cabang PAUD (6 siswa)
            ['name' => 'Rizki Ramadhan', 'email' => 'rizki.ramadhan@student.com', 'username' => 'rizki_ramadhan', 'cabang_id' => 2],
            ['name' => 'Aisyah Zahra', 'email' => 'aisyah.zahra@student.com', 'username' => 'aisyah_zahra', 'cabang_id' => 2],
            ['name' => 'Farhan Ahmad', 'email' => 'farhan.ahmad@student.com', 'username' => 'farhan_ahmad', 'cabang_id' => 2],
            ['name' => 'Zahra Amalia', 'email' => 'zahra.amalia@student.com', 'username' => 'zahra_amalia', 'cabang_id' => 2],
            ['name' => 'Raffi Akbar', 'email' => 'raffi.akbar@student.com', 'username' => 'raffi_akbar', 'cabang_id' => 2],
            ['name' => 'Nayla Putri', 'email' => 'nayla.putri@student.com', 'username' => 'nayla_putri', 'cabang_id' => 2],

            // Cabang Cimanggis (6 siswa)
            ['name' => 'Siti Fatimah', 'email' => 'siti.fatimah@student.com', 'username' => 'siti_fatimah', 'cabang_id' => 3],
            ['name' => 'Rahman Hakim', 'email' => 'rahman.hakim@student.com', 'username' => 'rahman_hakim', 'cabang_id' => 3],
            ['name' => 'Intan Permata', 'email' => 'intan.permata@student.com', 'username' => 'intan_permata', 'cabang_id' => 3],
            ['name' => 'Akbar Maulana', 'email' => 'akbar.maulana@student.com', 'username' => 'akbar_maulana', 'cabang_id' => 3],
            ['name' => 'Salsabila Nur', 'email' => 'salsabila.nur@student.com', 'username' => 'salsabila_nur', 'cabang_id' => 3],
            ['name' => 'Irfan Maulana', 'email' => 'irfan.maulana@student.com', 'username' => 'irfan_maulana', 'cabang_id' => 3],
        ];

        foreach ($siswaData as $siswa) {
            User::create([
                'name' => $siswa['name'],
                'email' => $siswa['email'],
                'username' => $siswa['username'],
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'role_id' => $roles['siswa'],
                'cabang_id' => $siswa['cabang_id'],
                'is_active' => true,
            ]);
        }

        $this->command->info('Users seeded successfully with role_id!');
    }
}
