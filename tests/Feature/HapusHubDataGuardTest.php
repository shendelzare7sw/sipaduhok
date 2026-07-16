<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\GuruPengajarKelas;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * GUARD INTEGRITAS penghapusan data "hub" (siswa & tenaga pendidik).
 *
 * Latar: hampir semua FK ber-onDelete('cascade'). Menghapus siswa/guru yang masih
 * punya jejak akan MEMUSNAHKAN nilai, rapor, tagihan, & pembayaran secara berantai.
 * Guard menolak hard-delete bila masih ada data terkait dan menyarankan nonaktifkan.
 *
 * Menjalankan: php artisan test --filter=HapusHubDataGuardTest
 */
class HapusHubDataGuardTest extends TestCase
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

    public function test_siswa_dengan_tagihan_tidak_bisa_dihapus(): void
    {
        $this->useMysql();
        DB::connection('mysql')->beginTransaction();
        try {
            [$admin, $cabang, $ta, $suffix] = $this->baseData();

            $siswa = $this->makeSiswa($cabang->id, $suffix);
            Tagihan::create([
                'siswa_id' => $siswa->id,
                'tahun_ajaran_id' => $ta->id,
                'jenis_tagihan' => 'spp_juli',
                'jumlah' => 150000,
                'tanggal_jatuh_tempo' => now()->addDays(7)->toDateString(),
                'status' => 'belum_bayar',
            ]);

            $res = $this->actingAs($admin)->withoutMiddleware()
                ->delete(route('admin.users.delete-siswa', $siswa->id));

            $res->assertSessionHas('error');
            $this->assertNotNull(Siswa::find($siswa->id), 'Siswa ber-tagihan seharusnya TIDAK terhapus');
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    public function test_siswa_tanpa_jejak_bisa_dihapus(): void
    {
        $this->useMysql();
        DB::connection('mysql')->beginTransaction();
        try {
            [$admin, $cabang, $ta, $suffix] = $this->baseData();

            $siswa = $this->makeSiswa($cabang->id, $suffix);
            $siswaId = $siswa->id;

            $res = $this->actingAs($admin)->withoutMiddleware()
                ->delete(route('admin.users.delete-siswa', $siswaId));

            $res->assertSessionHas('success');
            $this->assertNull(Siswa::find($siswaId), 'Siswa tanpa jejak seharusnya terhapus');
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    public function test_guru_dengan_penugasan_tidak_bisa_dihapus(): void
    {
        $this->useMysql();
        DB::connection('mysql')->beginTransaction();
        try {
            [$admin, $cabang, $ta, $suffix] = $this->baseData();

            $guruUser = $this->makeUser('guru_pengajar', "guru.$suffix@test.local");
            $tenaga = new TenagaPendidik();
            $tenaga->user_id = $guruUser->id;
            $tenaga->nama_lengkap = 'Guru ' . $suffix;
            $tenaga->jenis_kelamin = 'L';
            $tenaga->save();

            $mapel = $this->makeMapel($suffix);
            $kelas = $this->makeKelas($cabang->id, $ta->id, $suffix);
            GuruPengajarKelas::create([
                'tenaga_pendidik_id' => $tenaga->id,
                'kelas_id' => $kelas->id,
                'mata_pelajaran_id' => $mapel->id,
            ]);

            // route menerima User ID (dari checkbox blade)
            $res = $this->actingAs($admin)->withoutMiddleware()
                ->delete(route('admin.users.delete-tenaga-pendidik', $guruUser->id));

            $res->assertSessionHas('error');
            $this->assertNotNull(TenagaPendidik::find($tenaga->id), 'Guru ber-penugasan seharusnya TIDAK terhapus');
            $this->assertNotNull(User::find($guruUser->id), 'Akun guru ber-penugasan seharusnya TIDAK terhapus');
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    public function test_bulk_siswa_hanya_hapus_yang_aman(): void
    {
        $this->useMysql();
        DB::connection('mysql')->beginTransaction();
        try {
            [$admin, $cabang, $ta, $suffix] = $this->baseData();

            // A: ada tagihan -> harus dilewati
            $siswaA = $this->makeSiswa($cabang->id, $suffix . 'A');
            Tagihan::create([
                'siswa_id' => $siswaA->id,
                'tahun_ajaran_id' => $ta->id,
                'jenis_tagihan' => 'spp_juli',
                'jumlah' => 150000,
                'tanggal_jatuh_tempo' => now()->addDays(7)->toDateString(),
                'status' => 'belum_bayar',
            ]);

            // B: bersih -> boleh dihapus
            $siswaB = $this->makeSiswa($cabang->id, $suffix . 'B');

            $res = $this->actingAs($admin)->withoutMiddleware()
                ->post(route('admin.users.bulk-delete-siswa'), ['ids' => [$siswaA->id, $siswaB->id]]);

            $res->assertSessionHas('warning');
            $this->assertNotNull(Siswa::find($siswaA->id), 'Siswa ber-tagihan seharusnya dilewati (tetap ada)');
            $this->assertNull(Siswa::find($siswaB->id), 'Siswa bersih seharusnya terhapus');
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    // ---------- helpers ----------

    /** @return array{0:User,1:Cabang,2:TahunAjaran,3:string} */
    private function baseData(): array
    {
        $cabang = Cabang::first();
        $ta = TahunAjaran::where('is_active', true)->first();
        $this->assertNotNull($cabang, 'Butuh data Cabang (seed).');
        $this->assertNotNull($ta, 'Butuh Tahun Ajaran aktif (seed).');
        $suffix = substr(md5(uniqid('', true)), 0, 8);
        $admin = $this->makeUser('admin', "admin.$suffix@test.local");
        return [$admin, $cabang, $ta, $suffix];
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

    private function makeSiswa(int $cabangId, string $suffix): Siswa
    {
        $user = $this->makeUser('siswa', "siswa.$suffix@test.local");
        $s = new Siswa();
        $s->user_id = $user->id;
        $s->cabang_id = $cabangId;
        $s->nisn = 'N' . substr(md5($suffix), 0, 9);
        $s->nama_lengkap = 'Siswa ' . $suffix;
        $s->jenis_kelamin = 'L';
        $s->tempat_lahir = 'Kota';
        $s->tanggal_lahir = '2010-01-01';
        $s->alamat = 'Alamat';
        $s->tanggal_masuk = now()->toDateString();
        $s->save();
        return $s;
    }

    private function makeMapel(string $suffix): MataPelajaran
    {
        $m = new MataPelajaran();
        $m->kode_mapel = 'K' . $suffix;
        $m->nama_mapel = 'Mapel ' . $suffix;
        $m->jenjang = 'SMA';
        $m->save();
        return $m;
    }

    private function makeKelas(int $cabangId, int $taId, string $suffix): Kelas
    {
        $k = new Kelas();
        $k->cabang_id = $cabangId;
        $k->tahun_ajaran_id = $taId;
        $k->nama_kelas = 'Kelas ' . $suffix;
        $k->jenjang = 'SMA';
        $k->kode_kelas = 'K' . $suffix;
        $k->kuota_siswa = 30;
        $k->save();
        return $k;
    }
}
