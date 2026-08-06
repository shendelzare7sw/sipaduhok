<?php

namespace Tests\Feature;

use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\SoalUjian;
use App\Models\TenagaPendidik;
use App\Models\Ujian;
use App\Models\UjianSiswa;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Bukti end-to-end: halaman siswa mengerjakan ujian TIDAK membocorkan kunci
 * jawaban ke HTML yang dikirim ke browser (setara isu "correctAnswer bocor").
 *
 * Memakai DB nyata (mysql / db_sipaduhok) yang sudah di-seed. Semua record yang
 * dibuat dibungkus transaksi dan di-rollback, sehingga DB tidak berubah.
 */
class UjianTidakBocorKunciTest extends TestCase
{
    public function test_halaman_ujian_siswa_tidak_membocorkan_kunci_jawaban(): void
    {
        // Test harness default-nya sqlite :memory: kosong; arahkan ke DB nyata yang ter-seed.
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => '127.0.0.1',
            'database.connections.mysql.port' => '3306',
            'database.connections.mysql.database' => 'db_sipaduhok',
            'database.connections.mysql.username' => 'root',
            'database.connections.mysql.password' => '',
        ]);
        DB::purge('mysql');
        $this->withoutVite();

        DB::connection('mysql')->beginTransaction();

        try {
            $siswa = Siswa::whereNotNull('kelas_id')->first();
            $mapel = MataPelajaran::first();
            $guru = TenagaPendidik::first();
            $this->assertNotNull($siswa, 'Butuh data siswa hasil seeder');
            $this->assertNotNull($mapel, 'Butuh data mata pelajaran');
            $this->assertNotNull($guru, 'Butuh data tenaga pendidik');

            $ujian = new Ujian();
            $ujian->kelas_id = $siswa->kelas_id;
            $ujian->mata_pelajaran_id = $mapel->id;
            $ujian->guru_id = $guru->id;
            $ujian->judul_ujian = 'Uji Kebocoran Kunci';
            $ujian->tipe_ujian = 'ulangan_harian'; // tidak butuh validasi akses
            $ujian->tanggal_mulai = now()->subMinute();
            $ujian->tanggal_selesai = now()->addHours(2);
            $ujian->durasi_menit = 90;
            $ujian->is_active = true;
            $ujian->save();

            // Pilihan ganda kompleks: jawaban benar tersimpan di key 'jawaban_benar'
            $s1 = new SoalUjian();
            $s1->ujian_id = $ujian->id;
            $s1->pertanyaan = 'Soal kompleks';
            $s1->tipe_soal = 'pilihan_ganda_kompleks';
            $s1->pilihan_jawaban = ['A' => 'OPSIALPHA', 'B' => 'OPSIBETA', 'C' => 'OPSIGAMMA', 'jawaban_benar' => ['A', 'C']];
            $s1->kunci_jawaban = '["A","C"]';
            $s1->bobot_nilai = 1;
            $s1->save();

            // Isian: jawaban benar tersimpan di key 'jawaban_benar' + kolom kunci_jawaban
            $s2 = new SoalUjian();
            $s2->ujian_id = $ujian->id;
            $s2->pertanyaan = 'Soal isian';
            $s2->tipe_soal = 'isian_singkat';
            $s2->pilihan_jawaban = ['jawaban_benar' => ['KUNCIISIANRAHASIA']];
            $s2->kunci_jawaban = 'KUNCIISIANRAHASIA';
            $s2->bobot_nilai = 1;
            $s2->save();

            // Benar/salah: kunci tersimpan sebagai flag 'benar' tiap pernyataan
            $s3 = new SoalUjian();
            $s3->ujian_id = $ujian->id;
            $s3->pertanyaan = 'Soal benar salah';
            $s3->tipe_soal = 'benar_salah';
            $s3->pilihan_jawaban = ['pernyataan' => [
                ['text' => 'PERNYATAANSATU', 'benar' => true],
                ['text' => 'PERNYATAANDUA', 'benar' => false],
            ]];
            $s3->bobot_nilai = 1;
            $s3->save();

            // Siswa dalam kondisi sedang mengerjakan (agar soal dirender)
            $us = new UjianSiswa();
            $us->ujian_id = $ujian->id;
            $us->siswa_id = $siswa->id;
            $us->status = 'sedang_mengerjakan';
            $us->waktu_mulai = now();
            $us->save();

            $user = User::find($siswa->user_id);

            $response = $this->actingAs($user)
                ->withoutMiddleware()
                ->get(route('siswa.lms.mapel.ujian.show', [
                    'mapelId' => $mapel->id,
                    'ujianId' => $ujian->id,
                ]));

            $response->assertOk();

            // Rendering benar: opsi & pernyataan tetap tampil untuk siswa
            $response->assertSee('OPSIALPHA');
            $response->assertSee('PERNYATAANSATU');

            // BUKTI UTAMA: tidak ada kunci jawaban yang ikut terkirim ke HTML
            $response->assertDontSee('jawaban_benar');
            $response->assertDontSee('kunci_jawaban');
            $response->assertDontSee('KUNCIISIANRAHASIA');
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }
}
