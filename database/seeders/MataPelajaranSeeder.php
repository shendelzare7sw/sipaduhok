<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MataPelajaran;

class MataPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        $mataPelajaranData = [
            // PAUD
            ['kode_mapel' => 'PAUD-001', 'nama_mapel' => 'Bahasa Indonesia', 'jenjang' => 'PAUD', 'deskripsi' => 'Pengenalan bahasa dasar'],
            ['kode_mapel' => 'PAUD-002', 'nama_mapel' => 'Matematika Dasar', 'jenjang' => 'PAUD', 'deskripsi' => 'Pengenalan angka dan berhitung'],
            ['kode_mapel' => 'PAUD-003', 'nama_mapel' => 'Seni dan Kreativitas', 'jenjang' => 'PAUD', 'deskripsi' => 'Menggambar dan berkreasi'],

            // SD
            ['kode_mapel' => 'SD-001', 'nama_mapel' => 'Bahasa Indonesia', 'jenjang' => 'SD', 'deskripsi' => 'Mata pelajaran Bahasa Indonesia tingkat SD'],
            ['kode_mapel' => 'SD-002', 'nama_mapel' => 'Matematika', 'jenjang' => 'SD', 'deskripsi' => 'Mata pelajaran Matematika tingkat SD'],
            ['kode_mapel' => 'SD-003', 'nama_mapel' => 'IPA', 'jenjang' => 'SD', 'deskripsi' => 'Ilmu Pengetahuan Alam'],
            ['kode_mapel' => 'SD-004', 'nama_mapel' => 'IPS', 'jenjang' => 'SD', 'deskripsi' => 'Ilmu Pengetahuan Sosial'],
            ['kode_mapel' => 'SD-005', 'nama_mapel' => 'PKN', 'jenjang' => 'SD', 'deskripsi' => 'Pendidikan Kewarganegaraan'],

            // SMP
            ['kode_mapel' => 'SMP-001', 'nama_mapel' => 'Bahasa Indonesia', 'jenjang' => 'SMP', 'deskripsi' => 'Mata pelajaran Bahasa Indonesia tingkat SMP'],
            ['kode_mapel' => 'SMP-002', 'nama_mapel' => 'Matematika', 'jenjang' => 'SMP', 'deskripsi' => 'Mata pelajaran Matematika tingkat SMP'],
            ['kode_mapel' => 'SMP-003', 'nama_mapel' => 'IPA', 'jenjang' => 'SMP', 'deskripsi' => 'Ilmu Pengetahuan Alam'],
            ['kode_mapel' => 'SMP-004', 'nama_mapel' => 'IPS', 'jenjang' => 'SMP', 'deskripsi' => 'Ilmu Pengetahuan Sosial'],
            ['kode_mapel' => 'SMP-005', 'nama_mapel' => 'Bahasa Inggris', 'jenjang' => 'SMP', 'deskripsi' => 'Mata pelajaran Bahasa Inggris'],
            ['kode_mapel' => 'SMP-006', 'nama_mapel' => 'PKN', 'jenjang' => 'SMP', 'deskripsi' => 'Pendidikan Kewarganegaraan'],

            // SMA
            ['kode_mapel' => 'SMA-001', 'nama_mapel' => 'Bahasa Indonesia', 'jenjang' => 'SMA', 'deskripsi' => 'Mata pelajaran Bahasa Indonesia tingkat SMA'],
            ['kode_mapel' => 'SMA-002', 'nama_mapel' => 'Matematika', 'jenjang' => 'SMA', 'deskripsi' => 'Mata pelajaran Matematika tingkat SMA'],
            ['kode_mapel' => 'SMA-003', 'nama_mapel' => 'Fisika', 'jenjang' => 'SMA', 'deskripsi' => 'Mata pelajaran Fisika'],
            ['kode_mapel' => 'SMA-004', 'nama_mapel' => 'Kimia', 'jenjang' => 'SMA', 'deskripsi' => 'Mata pelajaran Kimia'],
            ['kode_mapel' => 'SMA-005', 'nama_mapel' => 'Biologi', 'jenjang' => 'SMA', 'deskripsi' => 'Mata pelajaran Biologi'],
            ['kode_mapel' => 'SMA-006', 'nama_mapel' => 'Bahasa Inggris', 'jenjang' => 'SMA', 'deskripsi' => 'Mata pelajaran Bahasa Inggris'],
            ['kode_mapel' => 'SMA-007', 'nama_mapel' => 'Sejarah', 'jenjang' => 'SMA', 'deskripsi' => 'Mata pelajaran Sejarah'],
            ['kode_mapel' => 'SMA-008', 'nama_mapel' => 'Geografi', 'jenjang' => 'SMA', 'deskripsi' => 'Mata pelajaran Geografi'],
            ['kode_mapel' => 'SMA-009', 'nama_mapel' => 'Ekonomi', 'jenjang' => 'SMA', 'deskripsi' => 'Mata pelajaran Ekonomi'],
        ];

        foreach ($mataPelajaranData as $mataPelajaran) {
            MataPelajaran::create($mataPelajaran);
        }
    }
}