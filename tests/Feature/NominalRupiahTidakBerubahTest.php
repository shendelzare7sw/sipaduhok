<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Regresi bug UANG paling berbahaya di aplikasi ini.
 *
 * Form nominal memakai pemisah ribuan TITIK ("200.000"), sedangkan PHP membaca
 * titik sebagai pemisah DESIMAL: is_numeric("200.000") bernilai true dan
 * (int)"200.000" menghasilkan 200. Akibatnya tagihan Rp 200.000 tersimpan
 * diam-diam sebagai Rp 200 - lolos validasi 'numeric', tanpa pesan error, dan
 * baru ketahuan setelah datanya terlanjur salah.
 *
 * Pembersihan titik memang sudah dilakukan JavaScript sebelum submit, tapi
 * nominal uang tidak boleh bergantung pada sisi klien: cukup satu kegagalan JS
 * dan angkanya berubah tanpa jejak. Karena itu normalisasi wajib ada di server,
 * dan test ini menjaganya.
 *
 * Menjalankan: php artisan test --filter=NominalRupiahTidakBerubahTest
 */
class NominalRupiahTidakBerubahTest extends TestCase
{
    public static function nominalProvider(): array
    {
        return [
            'ribuan'          => ['200.000', 200000],
            'jutaan'          => ['1.500.000', 1500000],
            'tanpa pemisah'   => ['200000', 200000],
            'berawalan Rp'    => ['Rp 750.000', 750000],
            'ada spasi'       => [' 25.000 ', 25000],
            'nol'             => ['0', 0],
            'ribuan kecil'    => ['1.500', 1500],
        ];
    }

    /**
     * @dataProvider nominalProvider
     */
    public function test_nominal_berformat_indonesia_tidak_menyusut(string $input, int $harapan): void
    {
        $this->assertSame(
            (float) $harapan,
            (float) rupiah_to_number($input),
            "Input \"{$input}\" seharusnya bernilai {$harapan}"
        );
    }

    public function test_koma_diperlakukan_sebagai_desimal(): void
    {
        // Kebiasaan Indonesia: titik = ribuan, koma = desimal.
        $this->assertSame(50000.5, (float) rupiah_to_number('50.000,50'));
    }

    public function test_nilai_kosong_dan_null_tidak_dipaksa_jadi_nol(): void
    {
        // Field opsional harus tetap kosong, bukan berubah jadi 0 - supaya
        // aturan validasi 'nullable' tetap berperilaku benar.
        $this->assertNull(rupiah_to_number(null));
        $this->assertSame('', rupiah_to_number(''));
    }

    public function test_angka_asli_tidak_diubah(): void
    {
        $this->assertSame(200000, rupiah_to_number(200000));
        $this->assertSame(1500.75, rupiah_to_number(1500.75));
    }

    public function test_normalisasi_request_mendukung_array_bertanda_bintang(): void
    {
        $request = \Illuminate\Http\Request::create('/x', 'POST', [
            'tagihan' => ['uang_pangkal' => '200.000', 'spp' => '1.500.000'],
            'jumlah' => '75.000',
        ]);

        normalisasi_input_rupiah($request, ['tagihan.*', 'jumlah']);

        $this->assertSame('200000', $request->input('tagihan.uang_pangkal'));
        $this->assertSame('1500000', $request->input('tagihan.spp'));
        $this->assertSame('75000', $request->input('jumlah'));
    }

    public function test_semua_endpoint_uang_menormalisasi_sebelum_validasi(): void
    {
        // Penjaga struktural: kalau ada endpoint uang baru yang lupa memanggil
        // normalisasi, test ini gagal sebelum bug-nya sampai ke produksi.
        $wajib = [
            app_path('Http/Controllers/Bendahara/TagihanController.php') => 4,
            app_path('Http/Controllers/Bendahara/PembayaranController.php') => 2,
        ];

        foreach ($wajib as $file => $minimal) {
            $isi = file_get_contents($file);
            $this->assertGreaterThanOrEqual(
                $minimal,
                substr_count($isi, 'normalisasi_input_rupiah'),
                basename($file).' kehilangan pemanggilan normalisasi_input_rupiah'
            );
        }
    }
}
