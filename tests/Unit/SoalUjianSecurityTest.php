<?php

namespace Tests\Unit;

use App\Models\SoalUjian;
use Tests\TestCase;

/**
 * Menjaga agar kunci jawaban tidak bocor ke siswa lewat serialisasi model
 * maupun lewat pilihan_jawaban. Regresi-guard untuk hardening kebocoran ujian.
 */
class SoalUjianSecurityTest extends TestCase
{
    public function test_kunci_jawaban_disembunyikan_dari_serialisasi(): void
    {
        $soal = new SoalUjian([
            'pertanyaan' => 'Contoh soal',
            'tipe_soal' => 'pilihan_ganda',
            'pilihan_jawaban' => ['A' => 'x', 'B' => 'y'],
            'jawaban_benar' => 'B',
            'kunci_jawaban' => 'B',
        ]);

        $array = $soal->toArray();
        $this->assertArrayNotHasKey('kunci_jawaban', $array);
        $this->assertArrayNotHasKey('jawaban_benar', $array);

        $json = $soal->toJson();
        $this->assertStringNotContainsString('kunci_jawaban', $json);
        $this->assertStringNotContainsString('jawaban_benar', $json);
    }

    public function test_makevisible_membuka_kunci_untuk_guru(): void
    {
        $soal = new SoalUjian(['kunci_jawaban' => 'B', 'jawaban_benar' => 'B']);

        $array = $soal->makeVisible(['kunci_jawaban', 'jawaban_benar'])->toArray();

        $this->assertSame('B', $array['kunci_jawaban']);
        $this->assertSame('B', $array['jawaban_benar']);
    }

    public function test_koleksi_makevisible_untuk_guru_tetap_memuat_kunci(): void
    {
        // Meniru persis ekspresi di manage_soal.blade: @json($soalList->makeVisible([...]))
        $soal = new SoalUjian(['kunci_jawaban' => 'B', 'jawaban_benar' => 'B']);
        $collection = new \Illuminate\Database\Eloquent\Collection([$soal]);

        $json = $collection->makeVisible(['kunci_jawaban', 'jawaban_benar'])->toJson();

        $this->assertStringContainsString('kunci_jawaban', $json);
        $this->assertStringContainsString('jawaban_benar', $json);
    }

    public function test_pilihan_jawaban_siswa_membuang_key_kompleks(): void
    {
        $soal = new SoalUjian([
            'tipe_soal' => 'pilihan_ganda_kompleks',
            'pilihan_jawaban' => ['A' => 'x', 'B' => 'y', 'C' => 'z', 'jawaban_benar' => ['A', 'C']],
        ]);

        $safe = $soal->pilihanJawabanForSiswa();

        $this->assertArrayNotHasKey('jawaban_benar', $safe);
        $this->assertSame(['A' => 'x', 'B' => 'y', 'C' => 'z'], $safe);
    }

    public function test_pilihan_jawaban_siswa_membuang_key_isian(): void
    {
        $soal = new SoalUjian([
            'tipe_soal' => 'isian_singkat',
            'pilihan_jawaban' => ['jawaban_benar' => ['sel', 'Sel', 'SEL']],
        ]);

        $this->assertArrayNotHasKey('jawaban_benar', $soal->pilihanJawabanForSiswa());
    }

    public function test_pilihan_jawaban_siswa_membuang_flag_benar_pada_pernyataan(): void
    {
        $soal = new SoalUjian([
            'tipe_soal' => 'benar_salah',
            'pilihan_jawaban' => ['pernyataan' => [
                ['text' => 'Air adalah H2O', 'benar' => true],
                ['text' => 'Bumi itu datar', 'benar' => false],
            ]],
        ]);

        $safe = $soal->pilihanJawabanForSiswa();

        $this->assertCount(2, $safe['pernyataan']);
        foreach ($safe['pernyataan'] as $item) {
            $this->assertArrayNotHasKey('benar', $item);
            $this->assertArrayHasKey('text', $item);
        }
    }
}
