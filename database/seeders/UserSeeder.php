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

        // Admin (2 orang) - dengan email_verified_at
        $adminData = [
            ['name' => 'Charoline Revlecya S. Mat.', 'email' => 'admincharol@sipaduhok.com', 'username' => 'admin1', 'phone' => '081234567890'],
            ['name' => 'Linawati Rozali', 'email' => 'adminlinawati@sipaduhok.com', 'username' => 'admin2', 'phone' => '081234567891'],
        ];

        foreach ($adminData as $admin) {
            User::updateOrCreate(
                ['email' => $admin['email']],
                [
                    'name' => $admin['name'],
                    'email' => $admin['email'],
                    'username' => $admin['username'],
                    'password' => Hash::make('password'),
                    'phone' => $admin['phone'],
                    'role' => 'admin',
                    'role_id' => $roles['admin'],
                    'cabang_id' => 1,
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
        }

        // Ketua PKBM
        User::create([
            'name' => 'Fransisda Tiodora Ferdiansyah S. Psi., M. M., Psikolog',
            'email' => 'ketuafransisda@sipaduhok.com',
            'username' => 'fransisdatiodora',
            'password' => Hash::make('password'),
            'role' => 'ketua_pkbm',
            'role_id' => $roles['ketua_pkbm'],
            'cabang_id' => 1,
            'is_active' => true,
        ]);

        // Wakil Kepala Sekolah (4 orang)
        $wakaData = [
            ['name' => 'Charoline Revlecya S. Mat', 'email' => 'wakacharol@sipaduhok.com', 'username' => 'charolinerev'],
            ['name' => 'Delia Parsauliani S.K.M.', 'email' => 'wakadelia@sipaduhok.com', 'username' => 'deliapar'],
            ['name' => 'Meini', 'email' => 'wakameini@sipaduhok.com', 'username' => 'meini'],
            ['name' => 'Eka Nurul', 'email' => 'wakanurul@sipaduhok.com', 'username' => 'nuruleka'],
        ];

        foreach ($wakaData as $waka) {
            User::create([
                'name' => $waka['name'],
                'email' => $waka['email'],
                'username' => $waka['username'],
                'password' => Hash::make('password'),
                'role' => 'wakil_kepala_sekolah',
                'role_id' => $roles['wakil_kepala_sekolah'],
                'cabang_id' => 1,
                'is_active' => true,
            ]);
        }

        // Sekretaris
        User::create([
            'name' => 'Delia Parsauliani S. K. M.',
            'email' => 'sekredelia@sipaduhok.com',
            'username' => 'deliaparsa',
            'password' => Hash::make('password'),
            'role' => 'sekretaris',
            'role_id' => $roles['sekretaris'],
            'cabang_id' => 1,
            'is_active' => true,
        ]);

        // Bendahara
        User::create([
            'name' => 'Linawati Rozali',
            'email' => 'bendaharalinawati@sipaduhok.com',
            'username' => 'linawatitu',
            'password' => Hash::make('password'),
            'role' => 'bendahara',
            'role_id' => $roles['bendahara'],
            'cabang_id' => 1,
            'is_active' => true,
        ]);

        // Wali Kelas - 4 data asli + 8 dummy
        $waliKelasData = [
            // Data asli (4 orang)
            ['name' => 'Delia Parsauliani S.K.M.', 'email' => 'walidelia@sipaduhok.com', 'username' => 'deliapars', 'cabang_id' => 1],
            ['name' => 'Charoline Revlecya S. Mat.', 'email' => 'walicharol@sipaduhok.com', 'username' => 'charolinerevc', 'cabang_id' => 1],
            ['name' => 'Dyah Yossie S. I. Kom,', 'email' => 'walidyah@sipaduhok.com', 'username' => 'dyahyoss', 'cabang_id' => 1],
            ['name' => 'Hiskia Oktaviandri', 'email' => 'walihiskia@sipaduhok.com', 'username' => 'hiskiaov', 'cabang_id' => 1],
            // Data dummy (sisa)
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

        // Guru Pengajar - 5 data asli + 5 dummy
        $guruData = [
            // Data asli (5 orang)
            ['name' => 'IrenT Berliana Agustin', 'email' => 'guruberliana@sipaduhok.com', 'username' => 'iberliana', 'cabang_id' => 1],
            ['name' => 'Delia Parsauliani S.K.M.', 'email' => 'gurudelia@sipaduhok.com', 'username' => 'deliaparsau', 'cabang_id' => 1],
            ['name' => 'Charoline Revlecya S. Mat.', 'email' => 'gurucharol@sipaduhok.com', 'username' => 'charolinerevcy', 'cabang_id' => 1],
            ['name' => 'Dyah Yossie S. I. Kom,', 'email' => 'gurudyah@sipaduhok.com', 'username' => 'dyahyos', 'cabang_id' => 1],
            ['name' => 'Hiskia Oktaviandri', 'email' => 'guruhiskia@sipaduhok.com', 'username' => 'hiskiao', 'cabang_id' => 1],
            // Data dummy (sisa)
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

        // Siswa - 6 data asli + 18 dummy
        $siswaData = [
            // Data asli Gedung Utama (6 orang)
            // 9A
            ['name' => 'Jacqueline Key', 'email' => 'jacquelinekey@sipaduhok.sch.id', 'username' => 'keyy', 'cabang_id' => 1],
            ['name' => 'Daniel Setiabudi', 'email' => 'danielsetiabudi@sipaduhok.sch.id', 'username' => 'daniels', 'cabang_id' => 1],
            // 8A
            ['name' => 'Zeaklyn Sean', 'email' => 'zeaklynsean@sipaduhok.sch.id', 'username' => 'seann', 'cabang_id' => 1],
            ['name' => 'Kevin Imanuel', 'email' => 'kevinimanuel@sipaduhok.sch.id', 'username' => 'kevini', 'cabang_id' => 1],
            // 7A
            ['name' => 'Rikza', 'email' => 'Rikza@sipaduhok.sch.id', 'username' => 'rikzaa', 'cabang_id' => 1],
            ['name' => 'Rakha', 'email' => 'rakhaa@sipaduhok.sch.id', 'username' => 'rakhaa', 'cabang_id' => 1],

            // Data dummy Cabang Ruko (6 siswa)
            ['name' => 'Andi Wijaya', 'email' => 'andi.wijaya@student.com', 'username' => 'andi_wijaya', 'cabang_id' => 1],
            ['name' => 'Putri Maharani', 'email' => 'putri.maharani@student.com', 'username' => 'putri_maharani', 'cabang_id' => 1],
            ['name' => 'Dimas Saputra', 'email' => 'dimas.saputra@student.com', 'username' => 'dimas_saputra', 'cabang_id' => 1],
            ['name' => 'Lina Rahmawati', 'email' => 'lina.rahmawati@student.com', 'username' => 'lina_rahmawati', 'cabang_id' => 1],
            ['name' => 'Fahmi Hidayat', 'email' => 'fahmi.hidayat@student.com', 'username' => 'fahmi_hidayat', 'cabang_id' => 1],
            ['name' => 'Nabila Azzahra', 'email' => 'nabila.azzahra@student.com', 'username' => 'nabila_azzahra', 'cabang_id' => 1],

            // Data dummy Cabang PAUD (6 siswa)
            ['name' => 'Rizki Ramadhan', 'email' => 'rizki.ramadhan@student.com', 'username' => 'rizki_ramadhan', 'cabang_id' => 2],
            ['name' => 'Aisyah Zahra', 'email' => 'aisyah.zahra@student.com', 'username' => 'aisyah_zahra', 'cabang_id' => 2],
            ['name' => 'Farhan Ahmad', 'email' => 'farhan.ahmad@student.com', 'username' => 'farhan_ahmad', 'cabang_id' => 2],
            ['name' => 'Zahra Amalia', 'email' => 'zahra.amalia@student.com', 'username' => 'zahra_amalia', 'cabang_id' => 2],
            ['name' => 'Raffi Akbar', 'email' => 'raffi.akbar@student.com', 'username' => 'raffi_akbar', 'cabang_id' => 2],
            ['name' => 'Nayla Putri', 'email' => 'nayla.putri@student.com', 'username' => 'nayla_putri', 'cabang_id' => 2],

            // Data dummy Cabang Cimanggis (6 siswa)
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

        // Orang Tua - 2 data asli
        $orangTuaData = [
            ['name' => 'Rina Arsy', 'email' => 'orturinaarsy@sipaduhok.parent.id', 'username' => 'orturinaarsy'],
            ['name' => 'Sara Elmira', 'email' => 'ortusarael@sipaduhok.parent.id', 'username' => 'ortusarael'],
        ];

        foreach ($orangTuaData as $ortu) {
            User::create([
                'name' => $ortu['name'],
                'email' => $ortu['email'],
                'username' => $ortu['username'],
                'password' => Hash::make('password'),
                'role' => 'orang_tua',
                'role_id' => $roles['orang_tua'],
                'cabang_id' => 1,
                'is_active' => true,
            ]);
        }

        $this->command->info('Users seeded successfully with role_id!');
        $this->command->warn('Admin credentials:');
        $this->command->warn('  Email: admincharol@sipaduhok.com / adminlinawati@sipaduhok.com');
        $this->command->warn('  Password: password');
        $this->command->warn('Please change these passwords in production!');
    }
}
