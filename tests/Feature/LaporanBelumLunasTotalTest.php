<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Laporan Siswa Belum Lunas menghitung total tagihan & pembayaran dengan dua query berkelompok
 * (dulu tiga query per siswa). Angka harus tetap sama: hanya tagihan TA aktif, hanya pembayaran
 * berstatus disetujui, dan siswa yang sisanya nol tidak ikut tampil.
 */
class LaporanBelumLunasTotalTest extends TestCase
{
    public function test_total_dan_sisa_per_siswa_tetap_akurat(): void
    {
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => '127.0.0.1',
            'database.connections.mysql.port' => '3306',
            'database.connections.mysql.database' => 'db_sipaduhok',
            'database.connections.mysql.username' => 'root',
            'database.connections.mysql.password' => '',
        ]);
        DB::purge('mysql');

        DB::connection('mysql')->beginTransaction();
        try {
            $suffix = substr(md5(uniqid('', true)), 0, 8);
            $cabang = Cabang::first();
            $ta = TahunAjaran::where('is_active', true)->first();
            $taLain = TahunAjaran::where('is_active', false)->first();
            $this->assertNotNull($cabang);
            $this->assertNotNull($ta, 'Butuh tahun ajaran aktif');

            $kelas = new Kelas;
            $kelas->forceFill(['cabang_id' => $cabang->id, 'tahun_ajaran_id' => $ta->id, 'nama_kelas' => 'Kelas '.$suffix,
                'jenjang' => 'SMP', 'kode_kelas' => 'K'.$suffix, 'kuota_siswa' => 30])->save();

            // Menyicil: tagihan 100rb + 50rb, bayar disetujui 40rb, bayar menunggu 30rb (tidak dihitung).
            $cicil = $this->makeSiswa($cabang->id, $kelas->id, "Cicil{$suffix}");
            $t1 = $this->makeTagihan($cicil->id, $ta->id, 100000, 'cicilan');
            $this->makeTagihan($cicil->id, $ta->id, 50000, 'belum_bayar');
            $this->makePembayaran($cicil->id, $t1->id, 40000, 'disetujui', 'A'.$suffix);
            $this->makePembayaran($cicil->id, $t1->id, 30000, 'pending', 'B'.$suffix);

            // Lunas: tagihan 80rb dibayar penuh -> tidak tampil.
            $lunas = $this->makeSiswa($cabang->id, $kelas->id, "Lunas{$suffix}");
            $t2 = $this->makeTagihan($lunas->id, $ta->id, 80000, 'sudah_bayar');
            $this->makePembayaran($lunas->id, $t2->id, 80000, 'disetujui', 'C'.$suffix);

            // Tagihan hanya di TA lain -> tidak dihitung, tidak tampil.
            $lain = $this->makeSiswa($cabang->id, $kelas->id, "TaLain{$suffix}");
            if ($taLain) {
                $this->makeTagihan($lain->id, $taLain->id, 70000, 'belum_bayar');
            }

            $bendahara = new User;
            $bendahara->forceFill(['name' => 'Bendahara '.$suffix, 'email' => "bendahara.$suffix@test.local", 'role' => 'bendahara', 'password' => bcrypt('password')])->save();

            $this->actingAs($bendahara)->withoutMiddleware()
                ->get(route('bendahara.laporan.belum-lunas', ['kelas_id' => $kelas->id]))
                ->assertOk()
                ->assertViewHas('siswaList', function ($list) use ($cicil) {
                    $this->assertSame([$cicil->id], $list->pluck('id')->all());
                    $baris = $list->first();
                    $this->assertEquals(150000, $baris->total_tagihan);
                    $this->assertEquals(40000, $baris->total_bayar);
                    $this->assertEquals(110000, $baris->sisa_tagihan);

                    return true;
                });
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    private function makeSiswa(int $cabangId, int $kelasId, string $nama): Siswa
    {
        $u = new User;
        $u->forceFill(['name' => $nama, 'email' => strtolower($nama).'@test.local', 'role' => 'siswa', 'password' => bcrypt('password')])->save();

        $s = new Siswa;
        $s->forceFill(['user_id' => $u->id, 'cabang_id' => $cabangId, 'kelas_id' => $kelasId, 'nisn' => 'N'.substr(md5($nama), 0, 10),
            'nama_lengkap' => $nama, 'jenis_kelamin' => 'L', 'tempat_lahir' => '-', 'tanggal_lahir' => '2010-01-01', 'alamat' => '-',
            'tanggal_masuk' => now(), 'status' => 'aktif'])->save();

        return $s;
    }

    private function makeTagihan(int $siswaId, int $taId, int $jumlah, string $status): Tagihan
    {
        $t = new Tagihan;
        $t->forceFill(['siswa_id' => $siswaId, 'tahun_ajaran_id' => $taId, 'jenis_tagihan' => 'spp_januari', 'jumlah' => $jumlah,
            'status' => $status, 'tanggal_jatuh_tempo' => now()->addMonth()])->save();

        return $t;
    }

    private function makePembayaran(int $siswaId, int $tagihanId, int $jumlah, string $status, string $kode): void
    {
        DB::connection('mysql')->table('pembayaran')->insert([
            'siswa_id' => $siswaId, 'tagihan_id' => $tagihanId, 'kode_pembayaran' => 'PAY'.$kode, 'jumlah_bayar' => $jumlah,
            'metode_pembayaran' => 'tunai', 'status_validasi' => $status, 'tanggal_bayar' => now(), 'created_at' => now(), 'updated_at' => now(),
        ]);
    }
}
