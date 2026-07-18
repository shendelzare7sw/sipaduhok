<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\User;
use App\Services\PromotionService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Uji kelayakan kenaikan kelas (PromotionService::checkEligibility) — inti fitur kenaikan.
 * Ditulis ulang ke pola proyek: MySQL nyata + data seed + rollback transaksi (bukan
 * sqlite/factory). Membuat TA non-aktif tersendiri lalu meneruskan id-nya eksplisit,
 * supaya tidak mengganggu TA aktif seed.
 *
 * Menjalankan: php artisan test --filter=PromotionSystemTest
 */
class PromotionSystemTest extends TestCase
{
    private function useMysql(): void
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
    }

    public function test_siswa_layak_naik_ketika_lunas_dan_nilai_tuntas(): void
    {
        $this->useMysql();
        DB::connection('mysql')->beginTransaction();
        try {
            $ctx = $this->scaffold();
            $siswa = $this->makeSiswa($ctx);

            // Keuangan LUNAS (tagihan sudah dibayar)
            $this->makeTagihan($siswa->id, $ctx['taId'], 'sudah_bayar');
            // Akademik tuntas: nilai_akhir 80 >= KKM 75 -> 100% >= threshold 50%
            $this->makeNilai($siswa, $ctx, 80);

            $result = (new PromotionService())->checkEligibility($siswa, $ctx['taId']);

            $this->assertTrue($result['eligible'], 'Siswa lunas + nilai tuntas harus LAYAK');
            $this->assertEquals('LUNAS', $result['financial']['status']);
            $this->assertTrue($result['academic']['is_tuntas']);
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    public function test_siswa_tidak_layak_ketika_masih_menunggak(): void
    {
        $this->useMysql();
        DB::connection('mysql')->beginTransaction();
        try {
            $ctx = $this->scaffold();
            $siswa = $this->makeSiswa($ctx);

            // Keuangan BELUM LUNAS (masih ada tagihan belum_bayar)
            $this->makeTagihan($siswa->id, $ctx['taId'], 'belum_bayar');
            // Akademik tetap tuntas -> yang menggagalkan murni keuangan
            $this->makeNilai($siswa, $ctx, 80);

            $result = (new PromotionService())->checkEligibility($siswa, $ctx['taId']);

            $this->assertFalse($result['eligible'], 'Siswa menunggak TIDAK boleh layak naik');
            $this->assertEquals('BELUM_LUNAS', $result['financial']['status']);
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    // ---------- helpers ----------

    /** Bangun TA non-aktif + setting + mapel + guru + kelas (jenjang SMP). */
    private function scaffold(): array
    {
        $cabang = Cabang::first();
        $this->assertNotNull($cabang, 'Butuh data Cabang (seed).');
        $suffix = substr(md5(uniqid('', true)), 0, 8);

        $ta = new TahunAjaran();
        $ta->nama_tahun_ajaran = 'TA-' . $suffix;
        $ta->tanggal_mulai = now()->toDateString();
        $ta->tanggal_selesai = now()->addYear()->toDateString();
        $ta->is_active = false; // jangan ganggu TA aktif seed
        $ta->save();

        DB::table('pengaturan_naik_kelas')->insert([
            'tahun_ajaran_id' => $ta->id,
            'tanggal_pengambilan_rapor' => now()->toDateString(),
            'persentase_minimal_tuntas' => 50,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $mapel = new MataPelajaran();
        $mapel->kode_mapel = 'K' . $suffix;
        $mapel->nama_mapel = 'Mapel ' . $suffix;
        $mapel->jenjang = 'SMP';
        $mapel->save();

        DB::table('pengaturan_kkm')->insert([
            'tahun_ajaran_id' => $ta->id,
            'jenjang' => 'SMP',
            'mata_pelajaran_id' => $mapel->id,
            'nilai_kkm' => 75,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $guruUser = $this->makeUser('guru_pengajar', "guru.$suffix@test.local");
        $guru = new TenagaPendidik();
        $guru->user_id = $guruUser->id;
        $guru->nama_lengkap = 'Guru ' . $suffix;
        $guru->jenis_kelamin = 'L';
        $guru->save();

        $kelas = new Kelas();
        $kelas->cabang_id = $cabang->id;
        $kelas->tahun_ajaran_id = $ta->id;
        $kelas->nama_kelas = 'Kelas ' . $suffix;
        $kelas->jenjang = 'SMP';
        $kelas->kode_kelas = 'K' . $suffix;
        $kelas->kuota_siswa = 30;
        $kelas->save();

        return ['taId' => $ta->id, 'cabang' => $cabang, 'mapel' => $mapel, 'guru' => $guru, 'kelas' => $kelas, 'suffix' => $suffix];
    }

    private function makeSiswa(array $ctx): Siswa
    {
        $user = $this->makeUser('siswa', "siswa.{$ctx['suffix']}@test.local");
        $s = new Siswa();
        $s->user_id = $user->id;
        $s->cabang_id = $ctx['cabang']->id;
        $s->kelas_id = $ctx['kelas']->id;
        $s->nisn = 'N' . substr(md5($ctx['suffix']), 0, 9);
        $s->nama_lengkap = 'Siswa ' . $ctx['suffix'];
        $s->jenis_kelamin = 'L';
        $s->tempat_lahir = 'Kota';
        $s->tanggal_lahir = '2011-01-01';
        $s->alamat = 'Alamat';
        $s->tanggal_masuk = now()->toDateString();
        $s->status = 'aktif';
        $s->save();
        return $s;
    }

    private function makeTagihan(int $siswaId, int $taId, string $status): void
    {
        Tagihan::create([
            'siswa_id' => $siswaId,
            'tahun_ajaran_id' => $taId,
            'jenis_tagihan' => 'spp_juli',
            'jumlah' => 150000,
            'tanggal_jatuh_tempo' => now()->addDays(7)->toDateString(),
            'status' => $status,
        ]);
    }

    private function makeNilai(Siswa $siswa, array $ctx, float $nilaiAkhir): void
    {
        Nilai::create([
            'siswa_id' => $siswa->id,
            'mata_pelajaran_id' => $ctx['mapel']->id,
            'kelas_id' => $ctx['kelas']->id,
            'tahun_ajaran_id' => $ctx['taId'],
            'guru_id' => $ctx['guru']->id,
            'nilai_akhir' => $nilaiAkhir,
        ]);
    }

    private function makeUser(string $role, string $email): User
    {
        $u = new User();
        $u->name = 'User ' . $email;
        $u->email = $email;
        $u->role = $role;
        $u->password = bcrypt('password');
        $u->save();
        return $u;
    }
}
