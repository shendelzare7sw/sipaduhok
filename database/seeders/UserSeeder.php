<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@sipaduhok.com',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
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
            'cabang_id' => 1,
            'is_active' => true,
        ]);

        // Wali Kelas (12 orang)
        $waliKelasData = [
            ['name' => 'Dewi Lestari', 'email' => 'wali.smp.a@sipaduhok.com', 'username' => 'wali_smp_a', 'cabang_id' => 1],
            ['name' => 'Rina Wijaya', 'email' => 'wali.sma.a@sipaduhok.com', 'username' => 'wali_sma_a', 'cabang_id' => 1],
            ['name' => 'Hendra Kusuma', 'email' => 'wali.sd.a@sipaduhok.com', 'username' => 'wali_sd_a', 'cabang_id' => 1],
            ['name' => 'Nurul Hidayah', 'email' => 'wali.paud.a@sipaduhok.com', 'username' => 'wali_paud_a', 'cabang_id' => 2],
            ['name' => 'Arif Budiman', 'email' => 'wali.smp.b@sipaduhok.com', 'username' => 'wali_smp_b', 'cabang_id' => 3],
            ['name' => 'Linda Permata', 'email' => 'wali.sma.b@sipaduhok.com', 'username' => 'wali_sma_b', 'cabang_id' => 3],
            ['name' => 'Rudi Hartono', 'email' => 'wali.sd.b@sipaduhok.com', 'username' => 'wali_sd_b', 'cabang_id' => 1],
            ['name' => 'Mega Puspita', 'email' => 'wali.paud.b@sipaduhok.com', 'username' => 'wali_paud_b', 'cabang_id' => 2],
            ['name' => 'Faisal Rahman', 'email' => 'wali.smp.c@sipaduhok.com', 'username' => 'wali_smp_c', 'cabang_id' => 1],
            ['name' => 'Dian Anggraini', 'email' => 'wali.sma.c@sipaduhok.com', 'username' => 'wali_sma_c', 'cabang_id' => 3],
            ['name' => 'Yoga Pratama', 'email' => 'wali.sd.c@sipaduhok.com', 'username' => 'wali_sd_c', 'cabang_id' => 1],
            ['name' => 'Lilis Suryani', 'email' => 'wali.paud.c@sipaduhok.com', 'username' => 'wali_paud_c', 'cabang_id' => 2],
        ];

        foreach ($waliKelasData as $wali) {
            User::create([
                'name' => $wali['name'],
                'email' => $wali['email'],
                'username' => $wali['username'],
                'password' => Hash::make('password'),
                'role' => 'wali_kelas',
                'cabang_id' => $wali['cabang_id'],
                'is_active' => true,
            ]);
        }

        // Guru Pengajar (12 orang)
        $guruData = [
            ['name' => 'Prof. Andi Prasetyo', 'email' => 'guru.matematika@sipaduhok.com', 'username' => 'guru_matematika', 'cabang_id' => 1],
            ['name' => 'Sri Mulyani', 'email' => 'guru.bahasa@sipaduhok.com', 'username' => 'guru_bahasa', 'cabang_id' => 1],
            ['name' => 'Bambang Sutrisno', 'email' => 'guru.ipa@sipaduhok.com', 'username' => 'guru_ipa', 'cabang_id' => 2],
            ['name' => 'Ratna Sari', 'email' => 'guru.ips@sipaduhok.com', 'username' => 'guru_ips', 'cabang_id' => 3],
            ['name' => 'Agus Setiawan', 'email' => 'guru.fisika@sipaduhok.com', 'username' => 'guru_fisika', 'cabang_id' => 1],
            ['name' => 'Wulan Dari', 'email' => 'guru.kimia@sipaduhok.com', 'username' => 'guru_kimia', 'cabang_id' => 1],
            ['name' => 'Hendro Wijaya', 'email' => 'guru.biologi@sipaduhok.com', 'username' => 'guru_biologi', 'cabang_id' => 3],
            ['name' => 'Sinta Dewi', 'email' => 'guru.inggris@sipaduhok.com', 'username' => 'guru_inggris', 'cabang_id' => 1],
            ['name' => 'Yudi Santoso', 'email' => 'guru.sejarah@sipaduhok.com', 'username' => 'guru_sejarah', 'cabang_id' => 3],
            ['name' => 'Ani Susanti', 'email' => 'guru.geografi@sipaduhok.com', 'username' => 'guru_geografi', 'cabang_id' => 1],
            ['name' => 'Budi Prasetyo', 'email' => 'guru.ekonomi@sipaduhok.com', 'username' => 'guru_ekonomi', 'cabang_id' => 3],
            ['name' => 'Maya Kartika', 'email' => 'guru.pkn@sipaduhok.com', 'username' => 'guru_pkn', 'cabang_id' => 1],
        ];

        foreach ($guruData as $guru) {
            User::create([
                'name' => $guru['name'],
                'email' => $guru['email'],
                'username' => $guru['username'],
                'password' => Hash::make('password'),
                'role' => 'guru_pengajar',
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
                'cabang_id' => $siswa['cabang_id'],
                'is_active' => true,
            ]);
        }
    }
}