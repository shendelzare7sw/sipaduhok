<?php

namespace Tests\Feature;

use App\Services\AiQuestionGeneratorService;
use Illuminate\Support\Facades\DB;
use ReflectionMethod;
use Tests\TestCase;

/**
 * Regresi AI Question Generator.
 *
 * Balasan model AI tidak pernah benar-benar seragam: huruf kunci kadang kecil
 * ("d"), ditulis lengkap ("A. Ekonomi pasar"), dikirim sebagai array, satu soal
 * kadang tidak dibungkus array, atau dibungkus key sendiri ({"soal_1": {...}}).
 * Dulu semua ragam itu bikin soal ditolak diam-diam dan guru cuma melihat
 * "No valid questions generated" atau "Array to string conversion".
 *
 * Test ini mengunci perilaku toleran tersebut, TANPA memanggil API sungguhan.
 *
 * Menjalankan: php artisan test --filter=AiGeneratorSoalTidakRusakTest
 */
class AiGeneratorSoalTidakRusakTest extends TestCase
{
    /**
     * Service membaca pengaturan model dari tabel app_settings, jadi test ini
     * memakai koneksi MySQL seperti test lain di proyek ini (hanya membaca,
     * tidak menulis apa pun).
     */
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => '127.0.0.1',
            'database.connections.mysql.port' => '3306',
            'database.connections.mysql.database' => 'db_sipaduhok',
            'database.connections.mysql.username' => 'root',
            'database.connections.mysql.password' => '',
        ]);
        DB::purge('mysql');
    }

    private function parse(string $json, string $tipe): array
    {
        $m = new ReflectionMethod(AiQuestionGeneratorService::class, 'parseQuestions');
        $m->setAccessible(true);

        return $m->invoke(new AiQuestionGeneratorService(), $json, $tipe);
    }

    public function test_kunci_huruf_kecil_dijadikan_kapital(): void
    {
        // Radio pilihan di form bernilai "A".."E" dan pencocokannya
        // case-sensitive; kunci "d" dari AI dulu bikin kunci tidak tersimpan.
        $hasil = $this->parse(json_encode([[
            'pertanyaan' => 'Soal', 'tipe_soal' => 'pilihan_ganda',
            'pilihan_a' => 'A1', 'pilihan_b' => 'B1', 'pilihan_c' => 'C1',
            'pilihan_d' => 'D1', 'pilihan_e' => 'E1',
            'kunci_jawaban' => 'd', 'bobot' => 10,
        ]]), 'pilihan_ganda');

        $this->assertSame('D', $hasil[0]['kunci_jawaban']);
    }

    public function test_kunci_ditulis_lengkap_diambil_hurufnya_saja(): void
    {
        $hasil = $this->parse(json_encode([[
            'pertanyaan' => 'Soal', 'tipe_soal' => 'pilihan_ganda',
            'pilihan_a' => 'Ekonomi pasar', 'pilihan_b' => 'B1', 'pilihan_c' => 'C1',
            'pilihan_d' => 'D1', 'pilihan_e' => 'E1',
            'kunci_jawaban' => 'A. Ekonomi pasar', 'bobot' => 10,
        ]]), 'pilihan_ganda');

        $this->assertSame('A', $hasil[0]['kunci_jawaban']);
    }

    public function test_soal_dengan_empat_opsi_tetap_diterima(): void
    {
        // Dulu WAJIB kelima opsi ada; kalau AI melewatkan opsi E (sering pada
        // soal tingkat sulit) seluruh hasil generate ditolak.
        $hasil = $this->parse(json_encode([[
            'pertanyaan' => 'Soal sulit', 'tipe_soal' => 'pilihan_ganda',
            'pilihan_a' => 'A1', 'pilihan_b' => 'B1', 'pilihan_c' => 'C1', 'pilihan_d' => 'D1',
            'kunci_jawaban' => 'B', 'bobot' => 10,
        ]]), 'pilihan_ganda');

        $this->assertCount(1, $hasil);
        $this->assertSame('B', $hasil[0]['kunci_jawaban']);
    }

    public function test_satu_soal_tanpa_dibungkus_array_tetap_terbaca(): void
    {
        // Mode json_object Groq membuat AI kerap membalas satu objek soal saja.
        $hasil = $this->parse(json_encode([
            'pertanyaan' => 'Pernyataan X benar?', 'tipe_soal' => 'benar_salah',
            'kunci_jawaban' => 'benar', 'bobot' => 5,
        ]), 'benar_salah');

        $this->assertCount(1, $hasil);
        $this->assertSame('benar', $hasil[0]['kunci_jawaban']);
    }

    public function test_soal_yang_dibungkus_key_sendiri_tetap_terbaca(): void
    {
        // Bentuk nyata dari Groq: [{"soal_1": {...}}, {"soal_2": {...}}]
        $hasil = $this->parse(json_encode([
            ['soal_1' => [
                'pertanyaan' => 'Pernyataan A', 'tipe_soal' => 'benar_salah',
                'kunci_jawaban' => 'benar', 'bobot' => 5,
            ]],
            ['soal_2' => [
                'pertanyaan' => 'Pernyataan B', 'tipe_soal' => 'benar_salah',
                'kunci_jawaban' => 'salah', 'bobot' => 5,
            ]],
        ]), 'benar_salah');

        $this->assertCount(2, $hasil);
        $this->assertSame('salah', $hasil[1]['kunci_jawaban']);
    }

    public function test_kunci_benar_salah_berupa_boolean_diterima(): void
    {
        $hasil = $this->parse(json_encode([[
            'pertanyaan' => 'Pernyataan', 'tipe_soal' => 'benar_salah',
            'kunci_jawaban' => true, 'bobot' => 5,
        ]]), 'benar_salah');

        $this->assertSame('benar', $hasil[0]['kunci_jawaban']);
    }

    public function test_kunci_pgk_berupa_array_digabung_jadi_huruf_kapital(): void
    {
        $hasil = $this->parse(json_encode([[
            'pertanyaan' => 'Pilih semua yang benar', 'tipe_soal' => 'pilihan_ganda_kompleks',
            'pilihan_a' => 'A1', 'pilihan_b' => 'B1', 'pilihan_c' => 'C1',
            'pilihan_d' => 'D1', 'pilihan_e' => 'E1',
            'kunci_jawaban' => ['a', 'c'], 'bobot' => 10,
        ]]), 'pilihan_ganda_kompleks');

        $this->assertSame('A,C', $hasil[0]['kunci_jawaban']);
    }

    public function test_soal_yang_benar_benar_rusak_tetap_ditolak(): void
    {
        // Longgar bukan berarti asal terima: kunci yang menunjuk opsi kosong
        // harus tetap gagal, supaya soal cacat tidak sampai ke siswa.
        $this->expectException(\Exception::class);

        $this->parse(json_encode([[
            'pertanyaan' => 'Soal', 'tipe_soal' => 'pilihan_ganda',
            'pilihan_a' => 'A1', 'pilihan_b' => 'B1', 'pilihan_c' => 'C1',
            'kunci_jawaban' => 'E', 'bobot' => 10,
        ]]), 'pilihan_ganda');
    }

    public function test_tidak_ada_lagi_interpolasi_count_yang_bikin_array_to_string(): void
    {
        // Penjaga struktural. Pola "{count($x)}" di dalam string kutip-ganda
        // BUKAN pemanggilan fungsi: PHP menganggap "{count(" teks biasa lalu
        // menyisipkan $x (array) -> Warning "Array to string conversion" yang
        // di Laravel jadi ErrorException, sehingga generate gagal total tepat
        // saat mekanisme retry hendak jalan.
        $isi = file_get_contents(app_path('Services/AiQuestionGeneratorService.php'));

        $this->assertSame(
            0,
            preg_match_all('/\{count\(\$/', $isi),
            'Ada interpolasi {count($...)} di dalam string - pakai concat: \' . count($x) . \''
        );
    }

    public function test_model_ai_yang_sudah_pensiun_dialihkan_ke_pengganti(): void
    {
        // Model yang dimatikan penyedia harus otomatis dialihkan, kalau tidak
        // guru hanya melihat "Gagal terhubung ke Groq API. Periksa API Key"
        // padahal API key-nya sehat.
        $this->assertSame(
            'llama-3.3-70b-versatile',
            ai_model_aktif('qwen/qwen3-32b', 'groq'),
            'Model teks pensiun harus dialihkan'
        );

        $this->assertSame(
            'qwen/qwen3.6-27b',
            ai_model_aktif('meta-llama/llama-4-scout-17b-16e-instruct', 'groq', true),
            'Model vision pensiun harus dialihkan'
        );

        // Model yang masih hidup tidak boleh diubah-ubah.
        $this->assertSame(
            'llama-3.3-70b-versatile',
            ai_model_aktif('llama-3.3-70b-versatile', 'groq')
        );
    }

    public function test_daftar_model_pilihan_tidak_memuat_model_pensiun(): void
    {
        $pensiun = array_keys(config('ai-models.retired'));

        foreach (['groq', 'gemini'] as $provider) {
            foreach (array_keys(config("ai-models.available.{$provider}")) as $id) {
                $this->assertNotContains(
                    $id,
                    $pensiun,
                    "Model '{$id}' sudah pensiun tapi masih ditawarkan di menu Pengaturan AI"
                );
            }
        }
    }
}
