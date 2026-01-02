<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MataPelajaran;
use Illuminate\Support\Facades\DB;

class FixMataPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus semua data mata pelajaran yang ada
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        MataPelajaran::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $mataPelajaranData = [
            // KB (Kelompok Bermain)
            ['kode_mapel' => 'KB-01', 'nama_mapel' => 'Bahasa Inggris', 'jenjang' => 'KB', 'deskripsi' => 'Pengenalan Bahasa Inggris dasar'],
            ['kode_mapel' => 'KB-02', 'nama_mapel' => 'Bahasa Indonesia', 'jenjang' => 'KB', 'deskripsi' => 'Pengenalan Bahasa Indonesia'],
            ['kode_mapel' => 'KB-03', 'nama_mapel' => 'Agama Kristen', 'jenjang' => 'KB', 'deskripsi' => 'Pendidikan Agama Kristen'],
            ['kode_mapel' => 'KB-04', 'nama_mapel' => 'Agama Islam', 'jenjang' => 'KB', 'deskripsi' => 'Pendidikan Agama Islam'],
            ['kode_mapel' => 'KB-05', 'nama_mapel' => 'Bahasa Mandarin', 'jenjang' => 'KB', 'deskripsi' => 'Pengenalan Bahasa Mandarin'],
            ['kode_mapel' => 'KB-06', 'nama_mapel' => 'Menulis Bentuk', 'jenjang' => 'KB', 'deskripsi' => 'Belajar menulis bentuk dasar'],
            ['kode_mapel' => 'KB-07', 'nama_mapel' => 'Menulis Angka', 'jenjang' => 'KB', 'deskripsi' => 'Belajar menulis angka'],
            ['kode_mapel' => 'KB-08', 'nama_mapel' => 'Mewarnai', 'jenjang' => 'KB', 'deskripsi' => 'Kreativitas mewarnai'],
            ['kode_mapel' => 'KB-09', 'nama_mapel' => 'Menggambar', 'jenjang' => 'KB', 'deskripsi' => 'Kreativitas menggambar'],
            ['kode_mapel' => 'KB-10', 'nama_mapel' => 'Keterampilan', 'jenjang' => 'KB', 'deskripsi' => 'Pengembangan keterampilan motorik'],
            ['kode_mapel' => 'KB-11', 'nama_mapel' => 'Cooking Class', 'jenjang' => 'KB', 'deskripsi' => 'Pengenalan kegiatan memasak sederhana'],
            ['kode_mapel' => 'KB-12', 'nama_mapel' => 'Matematika', 'jenjang' => 'KB', 'deskripsi' => 'Pengenalan angka dan berhitung'],

            // TKA (Taman Kanak-Kanak A)
            ['kode_mapel' => 'TKA-01', 'nama_mapel' => 'Bahasa Inggris', 'jenjang' => 'TKA', 'deskripsi' => 'Pengenalan Bahasa Inggris dasar'],
            ['kode_mapel' => 'TKA-02', 'nama_mapel' => 'Bahasa Indonesia', 'jenjang' => 'TKA', 'deskripsi' => 'Pengenalan Bahasa Indonesia'],
            ['kode_mapel' => 'TKA-03', 'nama_mapel' => 'Agama Kristen', 'jenjang' => 'TKA', 'deskripsi' => 'Pendidikan Agama Kristen'],
            ['kode_mapel' => 'TKA-04', 'nama_mapel' => 'Agama Islam', 'jenjang' => 'TKA', 'deskripsi' => 'Pendidikan Agama Islam'],
            ['kode_mapel' => 'TKA-05', 'nama_mapel' => 'Bahasa Mandarin', 'jenjang' => 'TKA', 'deskripsi' => 'Pengenalan Bahasa Mandarin'],
            ['kode_mapel' => 'TKA-06', 'nama_mapel' => 'Menulis Bentuk', 'jenjang' => 'TKA', 'deskripsi' => 'Belajar menulis bentuk dasar'],
            ['kode_mapel' => 'TKA-07', 'nama_mapel' => 'Menulis Angka', 'jenjang' => 'TKA', 'deskripsi' => 'Belajar menulis angka'],
            ['kode_mapel' => 'TKA-08', 'nama_mapel' => 'Mewarnai', 'jenjang' => 'TKA', 'deskripsi' => 'Kreativitas mewarnai'],
            ['kode_mapel' => 'TKA-09', 'nama_mapel' => 'Menggambar', 'jenjang' => 'TKA', 'deskripsi' => 'Kreativitas menggambar'],
            ['kode_mapel' => 'TKA-10', 'nama_mapel' => 'Keterampilan', 'jenjang' => 'TKA', 'deskripsi' => 'Pengembangan keterampilan motorik'],
            ['kode_mapel' => 'TKA-11', 'nama_mapel' => 'Cooking Class', 'jenjang' => 'TKA', 'deskripsi' => 'Pengenalan kegiatan memasak sederhana'],
            ['kode_mapel' => 'TKA-12', 'nama_mapel' => 'Matematika', 'jenjang' => 'TKA', 'deskripsi' => 'Pengenalan angka dan berhitung'],

            // TKB (Taman Kanak-Kanak B)
            ['kode_mapel' => 'TKB-01', 'nama_mapel' => 'Bahasa Inggris', 'jenjang' => 'TKB', 'deskripsi' => 'Pengenalan Bahasa Inggris dasar'],
            ['kode_mapel' => 'TKB-02', 'nama_mapel' => 'Bahasa Indonesia', 'jenjang' => 'TKB', 'deskripsi' => 'Pengenalan Bahasa Indonesia'],
            ['kode_mapel' => 'TKB-03', 'nama_mapel' => 'Agama Kristen', 'jenjang' => 'TKB', 'deskripsi' => 'Pendidikan Agama Kristen'],
            ['kode_mapel' => 'TKB-04', 'nama_mapel' => 'Agama Islam', 'jenjang' => 'TKB', 'deskripsi' => 'Pendidikan Agama Islam'],
            ['kode_mapel' => 'TKB-05', 'nama_mapel' => 'Bahasa Mandarin', 'jenjang' => 'TKB', 'deskripsi' => 'Pengenalan Bahasa Mandarin'],
            ['kode_mapel' => 'TKB-06', 'nama_mapel' => 'Menulis Bentuk', 'jenjang' => 'TKB', 'deskripsi' => 'Belajar menulis bentuk dasar'],
            ['kode_mapel' => 'TKB-07', 'nama_mapel' => 'Menulis Angka', 'jenjang' => 'TKB', 'deskripsi' => 'Belajar menulis angka'],
            ['kode_mapel' => 'TKB-08', 'nama_mapel' => 'Mewarnai', 'jenjang' => 'TKB', 'deskripsi' => 'Kreativitas mewarnai'],
            ['kode_mapel' => 'TKB-09', 'nama_mapel' => 'Menggambar', 'jenjang' => 'TKB', 'deskripsi' => 'Kreativitas menggambar'],
            ['kode_mapel' => 'TKB-10', 'nama_mapel' => 'Keterampilan', 'jenjang' => 'TKB', 'deskripsi' => 'Pengembangan keterampilan motorik'],
            ['kode_mapel' => 'TKB-11', 'nama_mapel' => 'Cooking Class', 'jenjang' => 'TKB', 'deskripsi' => 'Pengenalan kegiatan memasak sederhana'],
            ['kode_mapel' => 'TKB-12', 'nama_mapel' => 'Matematika', 'jenjang' => 'TKB', 'deskripsi' => 'Pengenalan angka dan berhitung'],
            ['kode_mapel' => 'TKB-13', 'nama_mapel' => 'Menulis Sambung', 'jenjang' => 'TKB', 'deskripsi' => 'Belajar menulis sambung'],

            // SD Kelas 1-4
            ['kode_mapel' => 'SD14-01', 'nama_mapel' => 'Agama Islam', 'jenjang' => 'SD', 'deskripsi' => 'Pendidikan Agama Islam untuk SD kelas 1-4'],
            ['kode_mapel' => 'SD14-02', 'nama_mapel' => 'Agama Kristen', 'jenjang' => 'SD', 'deskripsi' => 'Pendidikan Agama Kristen untuk SD kelas 1-4'],
            ['kode_mapel' => 'SD14-03', 'nama_mapel' => 'IPA', 'jenjang' => 'SD', 'deskripsi' => 'Ilmu Pengetahuan Alam'],
            ['kode_mapel' => 'SD14-04', 'nama_mapel' => 'IPS', 'jenjang' => 'SD', 'deskripsi' => 'Ilmu Pengetahuan Sosial'],
            ['kode_mapel' => 'SD14-05', 'nama_mapel' => 'Matematika', 'jenjang' => 'SD', 'deskripsi' => 'Matematika dasar SD'],
            ['kode_mapel' => 'SD14-06', 'nama_mapel' => 'Menggambar', 'jenjang' => 'SD', 'deskripsi' => 'Seni menggambar'],
            ['kode_mapel' => 'SD14-07', 'nama_mapel' => 'Pengembangan Karakter', 'jenjang' => 'SD', 'deskripsi' => 'Pembentukan karakter siswa'],
            ['kode_mapel' => 'SD14-08', 'nama_mapel' => 'Bahasa Indonesia', 'jenjang' => 'SD', 'deskripsi' => 'Bahasa Indonesia'],
            ['kode_mapel' => 'SD14-09', 'nama_mapel' => 'Bahasa Inggris', 'jenjang' => 'SD', 'deskripsi' => 'Bahasa Inggris dasar'],
            ['kode_mapel' => 'SD14-10', 'nama_mapel' => 'Bahasa Mandarin', 'jenjang' => 'SD', 'deskripsi' => 'Bahasa Mandarin dasar'],
            ['kode_mapel' => 'SD14-11', 'nama_mapel' => 'PKN', 'jenjang' => 'SD', 'deskripsi' => 'Pendidikan Kewarganegaraan'],
            ['kode_mapel' => 'SD14-12', 'nama_mapel' => 'PJOK', 'jenjang' => 'SD', 'deskripsi' => 'Pendidikan Jasmani Olahraga dan Kesehatan'],
            ['kode_mapel' => 'SD14-13', 'nama_mapel' => 'Musik', 'jenjang' => 'SD', 'deskripsi' => 'Seni Musik'],
            ['kode_mapel' => 'SD14-14', 'nama_mapel' => 'Life Skill', 'jenjang' => 'SD', 'deskripsi' => 'Keterampilan hidup'],
            ['kode_mapel' => 'SD14-15', 'nama_mapel' => 'Tata Boga', 'jenjang' => 'SD', 'deskripsi' => 'Keterampilan memasak'],
            ['kode_mapel' => 'SD14-16', 'nama_mapel' => 'Menulis Sambung', 'jenjang' => 'SD', 'deskripsi' => 'Menulis sambung'],
            ['kode_mapel' => 'SD14-17', 'nama_mapel' => 'Menari', 'jenjang' => 'SD', 'deskripsi' => 'Seni Tari'],
            ['kode_mapel' => 'SD14-18', 'nama_mapel' => 'Menyanyi', 'jenjang' => 'SD', 'deskripsi' => 'Seni Vokal'],

            // SD Kelas 5-6
            ['kode_mapel' => 'SD56-01', 'nama_mapel' => 'Agama Islam', 'jenjang' => 'SD', 'deskripsi' => 'Pendidikan Agama Islam untuk SD kelas 5-6'],
            ['kode_mapel' => 'SD56-02', 'nama_mapel' => 'Agama Kristen', 'jenjang' => 'SD', 'deskripsi' => 'Pendidikan Agama Kristen untuk SD kelas 5-6'],
            ['kode_mapel' => 'SD56-03', 'nama_mapel' => 'IPA', 'jenjang' => 'SD', 'deskripsi' => 'Ilmu Pengetahuan Alam lanjutan'],
            ['kode_mapel' => 'SD56-04', 'nama_mapel' => 'IPS', 'jenjang' => 'SD', 'deskripsi' => 'Ilmu Pengetahuan Sosial lanjutan'],
            ['kode_mapel' => 'SD56-05', 'nama_mapel' => 'Matematika', 'jenjang' => 'SD', 'deskripsi' => 'Matematika lanjutan'],
            ['kode_mapel' => 'SD56-06', 'nama_mapel' => 'Menggambar', 'jenjang' => 'SD', 'deskripsi' => 'Seni menggambar lanjutan'],
            ['kode_mapel' => 'SD56-07', 'nama_mapel' => 'Pengembangan Karakter', 'jenjang' => 'SD', 'deskripsi' => 'Pembentukan karakter lanjutan'],
            ['kode_mapel' => 'SD56-08', 'nama_mapel' => 'Bahasa Indonesia', 'jenjang' => 'SD', 'deskripsi' => 'Bahasa Indonesia lanjutan'],
            ['kode_mapel' => 'SD56-09', 'nama_mapel' => 'Bahasa Inggris', 'jenjang' => 'SD', 'deskripsi' => 'Bahasa Inggris lanjutan'],
            ['kode_mapel' => 'SD56-10', 'nama_mapel' => 'Bahasa Mandarin', 'jenjang' => 'SD', 'deskripsi' => 'Bahasa Mandarin lanjutan'],
            ['kode_mapel' => 'SD56-11', 'nama_mapel' => 'PKN', 'jenjang' => 'SD', 'deskripsi' => 'Pendidikan Kewarganegaraan lanjutan'],
            ['kode_mapel' => 'SD56-12', 'nama_mapel' => 'PJOK', 'jenjang' => 'SD', 'deskripsi' => 'Pendidikan Jasmani lanjutan'],
            ['kode_mapel' => 'SD56-13', 'nama_mapel' => 'TIK', 'jenjang' => 'SD', 'deskripsi' => 'Teknologi Informasi dan Komunikasi'],
            ['kode_mapel' => 'SD56-14', 'nama_mapel' => 'Life Skill', 'jenjang' => 'SD', 'deskripsi' => 'Keterampilan hidup lanjutan'],
            ['kode_mapel' => 'SD56-15', 'nama_mapel' => 'Tata Boga', 'jenjang' => 'SD', 'deskripsi' => 'Keterampilan memasak lanjutan'],
            ['kode_mapel' => 'SD56-16', 'nama_mapel' => 'Menulis Sambung', 'jenjang' => 'SD', 'deskripsi' => 'Menulis sambung lanjutan'],
            ['kode_mapel' => 'SD56-17', 'nama_mapel' => 'Musik', 'jenjang' => 'SD', 'deskripsi' => 'Seni Musik lanjutan'],
            ['kode_mapel' => 'SD56-18', 'nama_mapel' => 'Menari', 'jenjang' => 'SD', 'deskripsi' => 'Seni Tari lanjutan'],
            ['kode_mapel' => 'SD56-19', 'nama_mapel' => 'Menyanyi', 'jenjang' => 'SD', 'deskripsi' => 'Seni Vokal lanjutan'],

            // SMP (Paket B)
            ['kode_mapel' => 'SMP-01', 'nama_mapel' => 'Agama Islam', 'jenjang' => 'SMP', 'deskripsi' => 'Pendidikan Agama Islam SMP'],
            ['kode_mapel' => 'SMP-02', 'nama_mapel' => 'Agama Kristen', 'jenjang' => 'SMP', 'deskripsi' => 'Pendidikan Agama Kristen SMP'],
            ['kode_mapel' => 'SMP-03', 'nama_mapel' => 'IPA', 'jenjang' => 'SMP', 'deskripsi' => 'Ilmu Pengetahuan Alam SMP'],
            ['kode_mapel' => 'SMP-04', 'nama_mapel' => 'IPS', 'jenjang' => 'SMP', 'deskripsi' => 'Ilmu Pengetahuan Sosial SMP'],
            ['kode_mapel' => 'SMP-05', 'nama_mapel' => 'Matematika', 'jenjang' => 'SMP', 'deskripsi' => 'Matematika SMP'],
            ['kode_mapel' => 'SMP-06', 'nama_mapel' => 'Menggambar', 'jenjang' => 'SMP', 'deskripsi' => 'Seni Rupa'],
            ['kode_mapel' => 'SMP-07', 'nama_mapel' => 'Pengembangan Karakter', 'jenjang' => 'SMP', 'deskripsi' => 'Pembentukan karakter SMP'],
            ['kode_mapel' => 'SMP-08', 'nama_mapel' => 'Bahasa Indonesia', 'jenjang' => 'SMP', 'deskripsi' => 'Bahasa Indonesia SMP'],
            ['kode_mapel' => 'SMP-09', 'nama_mapel' => 'Bahasa Inggris', 'jenjang' => 'SMP', 'deskripsi' => 'Bahasa Inggris SMP'],
            ['kode_mapel' => 'SMP-10', 'nama_mapel' => 'Bahasa Mandarin', 'jenjang' => 'SMP', 'deskripsi' => 'Bahasa Mandarin SMP'],
            ['kode_mapel' => 'SMP-11', 'nama_mapel' => 'PKN', 'jenjang' => 'SMP', 'deskripsi' => 'Pendidikan Kewarganegaraan SMP'],
            ['kode_mapel' => 'SMP-12', 'nama_mapel' => 'PJOK', 'jenjang' => 'SMP', 'deskripsi' => 'Pendidikan Jasmani SMP'],
            ['kode_mapel' => 'SMP-13', 'nama_mapel' => 'TIK', 'jenjang' => 'SMP', 'deskripsi' => 'Teknologi Informasi dan Komunikasi SMP'],
            ['kode_mapel' => 'SMP-14', 'nama_mapel' => 'Life Skill', 'jenjang' => 'SMP', 'deskripsi' => 'Keterampilan hidup SMP'],
            ['kode_mapel' => 'SMP-15', 'nama_mapel' => 'Tata Boga', 'jenjang' => 'SMP', 'deskripsi' => 'Keterampilan memasak SMP'],
            ['kode_mapel' => 'SMP-16', 'nama_mapel' => 'Musik', 'jenjang' => 'SMP', 'deskripsi' => 'Seni Musik SMP'],

            // SMA (Paket C)
            ['kode_mapel' => 'SMA-01', 'nama_mapel' => 'Agama Islam', 'jenjang' => 'SMA', 'deskripsi' => 'Pendidikan Agama Islam SMA'],
            ['kode_mapel' => 'SMA-02', 'nama_mapel' => 'Agama Kristen', 'jenjang' => 'SMA', 'deskripsi' => 'Pendidikan Agama Kristen SMA'],
            ['kode_mapel' => 'SMA-03', 'nama_mapel' => 'IPAS', 'jenjang' => 'SMA', 'deskripsi' => 'Ilmu Pengetahuan Alam dan Sosial'],
            ['kode_mapel' => 'SMA-04', 'nama_mapel' => 'Sejarah', 'jenjang' => 'SMA', 'deskripsi' => 'Sejarah Indonesia dan Dunia'],
            ['kode_mapel' => 'SMA-05', 'nama_mapel' => 'Matematika', 'jenjang' => 'SMA', 'deskripsi' => 'Matematika SMA'],
            ['kode_mapel' => 'SMA-06', 'nama_mapel' => 'Menggambar', 'jenjang' => 'SMA', 'deskripsi' => 'Seni Rupa SMA'],
            ['kode_mapel' => 'SMA-07', 'nama_mapel' => 'Pengembangan Karakter', 'jenjang' => 'SMA', 'deskripsi' => 'Pembentukan karakter SMA'],
            ['kode_mapel' => 'SMA-08', 'nama_mapel' => 'Bahasa Indonesia', 'jenjang' => 'SMA', 'deskripsi' => 'Bahasa Indonesia SMA'],
            ['kode_mapel' => 'SMA-09', 'nama_mapel' => 'Bahasa Inggris', 'jenjang' => 'SMA', 'deskripsi' => 'Bahasa Inggris SMA'],
            ['kode_mapel' => 'SMA-10', 'nama_mapel' => 'Bahasa Mandarin', 'jenjang' => 'SMA', 'deskripsi' => 'Bahasa Mandarin SMA'],
            ['kode_mapel' => 'SMA-11', 'nama_mapel' => 'PKN', 'jenjang' => 'SMA', 'deskripsi' => 'Pendidikan Kewarganegaraan SMA'],
            ['kode_mapel' => 'SMA-12', 'nama_mapel' => 'PJOK', 'jenjang' => 'SMA', 'deskripsi' => 'Pendidikan Jasmani SMA'],
            ['kode_mapel' => 'SMA-13', 'nama_mapel' => 'TIK', 'jenjang' => 'SMA', 'deskripsi' => 'Teknologi Informasi dan Komunikasi SMA'],
            ['kode_mapel' => 'SMA-14', 'nama_mapel' => 'Life Skill', 'jenjang' => 'SMA', 'deskripsi' => 'Keterampilan hidup SMA'],
            ['kode_mapel' => 'SMA-15', 'nama_mapel' => 'Tata Boga', 'jenjang' => 'SMA', 'deskripsi' => 'Keterampilan memasak SMA'],
            ['kode_mapel' => 'SMA-16', 'nama_mapel' => 'Sosiologi', 'jenjang' => 'SMA', 'deskripsi' => 'Sosiologi'],
            ['kode_mapel' => 'SMA-17', 'nama_mapel' => 'Geografi', 'jenjang' => 'SMA', 'deskripsi' => 'Geografi'],
            ['kode_mapel' => 'SMA-18', 'nama_mapel' => 'Ekonomi', 'jenjang' => 'SMA', 'deskripsi' => 'Ekonomi'],
            ['kode_mapel' => 'SMA-19', 'nama_mapel' => 'Musik', 'jenjang' => 'SMA', 'deskripsi' => 'Seni Musik SMA'],
        ];

        foreach ($mataPelajaranData as $mataPelajaran) {
            MataPelajaran::create($mataPelajaran);
        }

        $this->command->info('Mata pelajaran berhasil diperbaiki!');
    }
}
