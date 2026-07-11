<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Services\PromotionService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Keadilan finansial kenaikan kelas: status 'cicilan' (bayar sebagian) HARUS
 * dianggap BELUM_LUNAS, setara dengan 'belum_bayar' — konsisten dengan gate
 * ujian/rapor (cekSiswaLunas). Sebelumnya checkFinancial hanya melihat
 * ['belum_bayar','terlambat'] sehingga siswa cicilan salah dianggap LUNAS.
 * Juga menjaga tunggakan = SISA sebenarnya (jumlah - pembayaran disetujui).
 */
class PromotionCicilanFinancialTest extends TestCase
{
    public function test_cicilan_belum_lunas_dan_sisa_akurat_serta_lunas_penuh(): void
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
            $this->assertNotNull($cabang);
            $this->assertNotNull($ta, 'Butuh tahun ajaran aktif');

            $kelas = $this->makeKelas($cabang->id, $ta->id, $suffix);
            $service = new PromotionService();

            // ── Kasus 1: cicilan (tagihan 100rb, dibayar 40rb) → BELUM_LUNAS, sisa 60rb ──
            $siswaCicil = $this->makeSiswa($cabang->id, $kelas->id, 'C' . $suffix);
            $tagihan = $this->makeTagihan($siswaCicil->id, $ta->id, 100000, 'cicilan');
            $this->makePembayaran($tagihan->id, $siswaCicil->id, 40000, 'PAY-' . $suffix);

            $resCicil = $service->checkEligibility($siswaCicil, $ta->id);
            $this->assertSame('BELUM_LUNAS', $resCicil['financial']['status'], 'Cicilan harus dianggap belum lunas');
            $this->assertEquals(60000, $resCicil['financial']['unpaid_amount'], 'Tunggakan = sisa (100rb-40rb), bukan tagihan penuh');
            $this->assertFalse($resCicil['eligible'], 'Cicilan tidak boleh eligible');

            // ── Kasus 2: lunas penuh (status sudah_bayar) → LUNAS ──
            $siswaLunas = $this->makeSiswa($cabang->id, $kelas->id, 'L' . $suffix);
            $this->makeTagihan($siswaLunas->id, $ta->id, 100000, 'sudah_bayar');

            $resLunas = $service->checkEligibility($siswaLunas, $ta->id);
            $this->assertSame('LUNAS', $resLunas['financial']['status']);
            $this->assertEquals(0, $resLunas['financial']['unpaid_amount']);
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    private function makeUser(string $email): User
    {
        $u = new User();
        $u->name = 'User ' . $email;
        $u->email = $email;
        $u->role = 'siswa';
        $u->password = bcrypt('password');
        $u->save();
        return $u;
    }

    private function makeKelas(int $cabangId, int $taId, string $suffix): Kelas
    {
        $k = new Kelas();
        $k->cabang_id = $cabangId;
        $k->tahun_ajaran_id = $taId;
        $k->nama_kelas = 'Kelas ' . $suffix;
        $k->jenjang = 'SMP';
        $k->kode_kelas = 'K' . $suffix;
        $k->kuota_siswa = 30;
        $k->save();
        return $k;
    }

    private function makeSiswa(int $cabangId, int $kelasId, string $suffix): Siswa
    {
        $u = $this->makeUser("siswa.$suffix@test.local");

        $s = new Siswa();
        $s->user_id = $u->id;
        $s->cabang_id = $cabangId;
        $s->kelas_id = $kelasId;
        $s->nisn = 'N' . $suffix;
        $s->nama_lengkap = 'Siswa ' . $suffix;
        $s->jenis_kelamin = 'L';
        $s->tempat_lahir = '-';
        $s->tanggal_lahir = '2010-01-01';
        $s->alamat = '-';
        $s->tanggal_masuk = now();
        $s->status = 'aktif';
        $s->save();
        return $s;
    }

    private function makeTagihan(int $siswaId, int $taId, int $jumlah, string $status): Tagihan
    {
        $t = new Tagihan();
        $t->siswa_id = $siswaId;
        $t->tahun_ajaran_id = $taId;
        $t->jenis_tagihan = 'spp_januari';
        $t->jumlah = $jumlah;
        $t->status = $status;
        $t->tanggal_jatuh_tempo = now()->addMonth();
        $t->save();
        return $t;
    }

    private function makePembayaran(int $tagihanId, int $siswaId, int $jumlahBayar, string $kode): Pembayaran
    {
        $p = new Pembayaran();
        $p->tagihan_id = $tagihanId;
        $p->siswa_id = $siswaId;
        $p->kode_pembayaran = $kode;
        $p->jumlah_bayar = $jumlahBayar;
        $p->tanggal_bayar = now();
        $p->metode_pembayaran = 'tunai';
        $p->status_validasi = 'disetujui';
        $p->save();
        return $p;
    }
}
