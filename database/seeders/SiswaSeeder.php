<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Struktur:
     * - KB, TKA, TKB, SD: 3 siswa per kelas
     * - SMP, SMA: 3-5 siswa per kelas (untuk testing LMS)
     */
    public function run(): void
    {
        // Truncate tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('student_parents')->truncate();
        DB::table('siswa')->truncate();
        DB::statement('DELETE FROM users WHERE role IN ("siswa", "orang_tua")');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Data nama siswa untuk randomisasi
        $namaDepanLaki = [
            'Ahmad', 'Muhammad', 'Budi', 'Andi', 'Rizki', 'Fajar', 'Dimas', 'Reza', 'Arif', 'Hadi',
            'Yusuf', 'Farel', 'Alif', 'Dzaki', 'Rafif', 'Nabil', 'Akbar', 'Ilham', 'Fauzi', 'Fahmi',
            'Bagas', 'Daffa', 'Rayhan', 'Farhan', 'Adit', 'Fikri', 'Galang', 'Haikal', 'Ikhsan', 'Jafar'
        ];

        $namaDepanPerempuan = [
            'Siti', 'Nur', 'Dewi', 'Rina', 'Maya', 'Fitri', 'Ayu', 'Lestari', 'Putri', 'Intan',
            'Zahra', 'Aisyah', 'Nabila', 'Salma', 'Safira', 'Nazwa', 'Alya', 'Rahma', 'Anisa', 'Dina',
            'Clarissa', 'Aulia', 'Bella', 'Citra', 'Elvira', 'Farah', 'Gita', 'Hana', 'Indah', 'Jasmine'
        ];

        $namaBelakang = [
            'Pratama', 'Wijaya', 'Permana', 'Saputra', 'Ramadhan', 'Hidayat', 'Kurniawan', 'Santoso',
            'Nugraha', 'Firmansyah', 'Maulana', 'Rahman', 'Hakim', 'Setiawan', 'Putra', 'Wibowo',
            'Kusuma', 'Handoko', 'Nugroho', 'Syahputra', 'Purnomo', 'Hermawan', 'Suryanto', 'Gunawan',
            'Hartono', 'Sudrajat', 'Mahendra', 'Pradana', 'Adiputra', 'Saputri'
        ];

        // Get all kelas
        $kelasList = DB::table('kelas')->orderBy('id')->get();

        $counter = 1;

        foreach ($kelasList as $kelas) {
            // Tentukan jumlah siswa per kelas berdasarkan jenjang
            $jumlahSiswa = 3; // Default untuk KB, TKA, TKB, SD

            if (in_array($kelas->jenjang, ['SMP', 'SMA'])) {
                // SMP & SMA: 3-5 siswa (random)
                $jumlahSiswa = rand(3, 5);
            }

            for ($i = 1; $i <= $jumlahSiswa; $i++) {
                $jenisKelamin = rand(0, 1) ? 'L' : 'P';

                // Generate nama siswa
                if ($jenisKelamin == 'L') {
                    $namaDepan = $namaDepanLaki[array_rand($namaDepanLaki)];
                } else {
                    $namaDepan = $namaDepanPerempuan[array_rand($namaDepanPerempuan)];
                }
                $namaBelakangPilih = $namaBelakang[array_rand($namaBelakang)];
                $namaSiswa = $namaDepan . ' ' . $namaBelakangPilih;

                // Generate NISN (10 digit)
                $nisn = '30' . str_pad($counter, 8, '0', STR_PAD_LEFT);

                // Generate username dari nama (lowercase, no space)
                $usernameBase = strtolower(str_replace(' ', '', $namaDepan));
                $username = $usernameBase . $counter;

                // Create user siswa
                $userId = DB::table('users')->insertGetId([
                    'name' => $namaSiswa,
                    'username' => $username,
                    'email' => $username . '@siswa.sipaduhok.sch.id',
                    'password' => Hash::make('password123'),
                    'role' => 'siswa',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Create siswa
                $siswaId = DB::table('siswa')->insertGetId([
                    'user_id' => $userId,
                    'kelas_id' => $kelas->id,
                    'cabang_id' => $kelas->cabang_id,
                    'nisn' => $nisn,
                    'nis' => strtoupper(substr($kelas->jenjang, 0, 3)) . '-' . date('Y') . '-' . str_pad($counter, 3, '0', STR_PAD_LEFT),
                    'nama_lengkap' => $namaSiswa,
                    'jenis_kelamin' => $jenisKelamin,
                    'tempat_lahir' => $this->getRandomKota(),
                    'tanggal_lahir' => $this->getRandomBirthdate($kelas->jenjang),
                    'alamat' => 'Jl. ' . $namaBelakangPilih . ' No. ' . rand(1, 100) . ', ' . $this->getRandomKota(),
                    'nama_ayah' => null,
                    'nama_ibu' => null,
                    'telepon_orangtua' => null,
                    'tanggal_masuk' => date('Y') . '-07-01',
                    'status' => 'aktif',
                    'foto' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Create orang tua (parent user)
                $namaOrtu = $namaDepanLaki[array_rand($namaDepanLaki)] . ' ' . $namaBelakangPilih;
                $usernameOrtu = 'ortu' . $counter;

                // Create user orang tua
                $parentUserId = DB::table('users')->insertGetId([
                    'name' => $namaOrtu,
                    'username' => $usernameOrtu,
                    'email' => $usernameOrtu . '@ortu.sipaduhok.sch.id',
                    'password' => Hash::make('password123'),
                    'role' => 'orang_tua',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Link parent to student
                DB::table('student_parents')->insert([
                    'siswa_id' => $siswaId,
                    'parent_id' => $parentUserId,
                    'relationship' => 'ayah_kandung',
                    'is_primary' => true,
                    'is_financial_responsible' => true,
                    'can_access_academic' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $counter++;
            }
        }

        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════');
        $this->command->info('✓ Data siswa berhasil dibuat!');
        $this->command->info('');
        $this->command->info('STATISTIK SISWA PER JENJANG:');
        $this->command->info('═══════════════════════════════════════════════════');

        $statsPerJenjang = DB::table('siswa')
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->select('kelas.jenjang', DB::raw('COUNT(*) as total'))
            ->groupBy('kelas.jenjang')
            ->orderByRaw("FIELD(kelas.jenjang, 'KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA')")
            ->get();

        foreach ($statsPerJenjang as $stat) {
            $icon = in_array($stat->jenjang, ['SMP', 'SMA']) ? '📱' : '📚';
            $note = in_array($stat->jenjang, ['SMP', 'SMA']) ? ' (Ready for LMS)' : '';
            $this->command->info("  {$icon} {$stat->jenjang}: {$stat->total} siswa{$note}");
        }

        $totalSiswa = DB::table('siswa')->count();
        $totalOrtu = DB::table('student_parents')->count();
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════');
        $this->command->info("TOTAL: {$totalSiswa} siswa & {$totalOrtu} orang tua");
        $this->command->info('');
        $this->command->info('📝 Login Credentials:');
        $this->command->info('   Siswa     : username[nomor] / password123');
        $this->command->info('   Orang Tua : ortu[nomor] / password123');
        $this->command->info('   Contoh    : ahmad1 / password123');
        $this->command->info('             : ortu1 / password123');
        $this->command->info('');
    }

    private function getRandomKota()
    {
        $kota = [
            'Jakarta', 'Bandung', 'Surabaya', 'Tangerang', 'Bekasi', 'Depok',
            'Bogor', 'Semarang', 'Yogyakarta', 'Malang', 'Solo', 'Medan',
            'Palembang', 'Makassar', 'Denpasar', 'Batam', 'Pontianak', 'Banjarmasin'
        ];
        return $kota[array_rand($kota)];
    }

    private function getRandomBirthdate($jenjang)
    {
        // Hitung umur berdasarkan jenjang
        $currentYear = date('Y');

        switch ($jenjang) {
            case 'KB':
                $age = rand(3, 4);
                break;
            case 'TKA':
            case 'TKB':
                $age = rand(4, 5);
                break;
            case 'SD':
                $age = rand(6, 12);
                break;
            case 'SMP':
                $age = rand(12, 15);
                break;
            case 'SMA':
                $age = rand(15, 18);
                break;
            default:
                $age = rand(6, 12);
        }

        $birthYear = $currentYear - $age;
        $birthMonth = rand(1, 12);
        $birthDay = rand(1, 28);

        return sprintf('%04d-%02d-%02d', $birthYear, $birthMonth, $birthDay);
    }

    private function getRandomPekerjaan()
    {
        $pekerjaan = [
            'PNS', 'Karyawan Swasta', 'Wiraswasta', 'Pedagang', 'Guru', 'Dokter',
            'Perawat', 'Pengusaha', 'Petani', 'Buruh', 'TNI/Polri', 'Ibu Rumah Tangga',
            'Pegawai BUMN', 'Teknisi', 'Sopir', 'Montir', 'Tukang', 'Kuli Bangunan',
            'Nelayan', 'Pensiunan', 'Freelancer', 'Konsultan', 'Arsitek', 'Akuntan'
        ];
        return $pekerjaan[array_rand($pekerjaan)];
    }
}
