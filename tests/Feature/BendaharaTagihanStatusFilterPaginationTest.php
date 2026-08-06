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
 * Regresi bug: filter ?status_tagihan= di Kelola Tagihan bendahara dulu diterapkan
 * SETELAH paginate(15) lewat Collection::filter(), sehingga halaman 1 hanya
 * menyisakan sebagian kecil baris dan siswa yang cocok di halaman lain tidak
 * pernah ikut ditarik, sementara ->total() tetap menghitung jumlah sebelum
 * filter. Fix memindahkan agregasi & filter ke SQL (selectSub + having) sebelum
 * paginate(). Test ini memastikan halaman 1 penuh dan ->total() akurat.
 */
class BendaharaTagihanStatusFilterPaginationTest extends TestCase
{
    public function test_filter_belum_lunas_tidak_terpotong_paginasi(): void
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

            $bendahara = $this->makeUser('bendahara', "bendahara.$suffix@test.local");

            // 17 siswa belum lunas (> 15 = ukuran satu halaman) supaya bug lama
            // (halaman 1 terpotong ke sisa hasil filter 15 baris) kelihatan.
            // Nama diberi prefix unik yang tidak saling bersinggungan sebagai substring
            // (Menunggak/SudahLunas/TanpaTagihan) supaya assertSee/assertDontSee akurat.
            $belumLunasNames = [];
            for ($i = 1; $i <= 17; $i++) {
                $nama = "Menunggak{$suffix}n{$i}";
                $belumLunasNames[] = $nama;
                $siswa = $this->makeSiswa($cabang->id, $kelas->id, 'aktif', $nama);
                $this->makeTagihan($siswa->id, $ta->id, 100000, 'belum_bayar');
            }

            // 2 siswa lunas & 1 siswa kosong (tanpa tagihan) — harus tersaring KELUAR.
            $lunas = $this->makeSiswa($cabang->id, $kelas->id, 'aktif', "SudahLunas{$suffix}");
            $tagihanLunas = $this->makeTagihan($lunas->id, $ta->id, 100000, 'sudah_bayar');
            DB::connection('mysql')->table('pembayaran')->insert([
                'siswa_id' => $lunas->id,
                'tagihan_id' => $tagihanLunas->id,
                'kode_pembayaran' => 'PAY'.$suffix,
                'jumlah_bayar' => 100000,
                'metode_pembayaran' => 'tunai',
                'status_validasi' => 'disetujui',
                'tanggal_bayar' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $kosong = $this->makeSiswa($cabang->id, $kelas->id, 'aktif', "TanpaTagihan{$suffix}");

            $this->actingAs($bendahara)->withoutMiddleware();

            $resPage1 = $this->get(route('bendahara.tagihan.index', [
                'status_tagihan' => 'belum_lunas',
                'search' => $suffix,
                'page' => 1,
            ]));
            $resPage1->assertOk();

            $paginator = $resPage1->viewData('siswaList');
            $this->assertNotNull($paginator);

            // ->total() harus mencerminkan 17 siswa "belum lunas" yang sebenarnya,
            // bukan jumlah keseluruhan 20 siswa dengan suffix ini.
            $this->assertSame(17, $paginator->total());

            // Halaman 1 (limit 15) harus PENUH 15 baris, bukan hasil sisa filter kecil.
            $this->assertCount(15, $paginator->items());

            // Semua nama di halaman 1 memang bagian dari daftar belum-lunas.
            foreach ($paginator->items() as $siswa) {
                $this->assertContains($siswa->nama_lengkap, $belumLunasNames);
            }

            $resPage1->assertDontSee("SudahLunas{$suffix}");
            $resPage1->assertDontSee("TanpaTagihan{$suffix}");

            // Halaman 2 berisi sisa 2 siswa belum-lunas yang tidak muat di halaman 1.
            $resPage2 = $this->get(route('bendahara.tagihan.index', [
                'status_tagihan' => 'belum_lunas',
                'search' => $suffix,
                'page' => 2,
            ]));
            $resPage2->assertOk();
            $paginator2 = $resPage2->viewData('siswaList');
            $this->assertCount(2, $paginator2->items());
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    private function makeUser(string $role, string $email): User
    {
        $u = new User;
        $u->name = 'User '.$email;
        $u->email = $email;
        $u->role = $role;
        $u->password = bcrypt('password');
        $u->save();

        return $u;
    }

    private function makeKelas(int $cabangId, int $taId, string $suffix): Kelas
    {
        $k = new Kelas;
        $k->cabang_id = $cabangId;
        $k->tahun_ajaran_id = $taId;
        $k->nama_kelas = 'Kelas '.$suffix;
        $k->jenjang = 'SMP';
        $k->kode_kelas = 'K'.$suffix;
        $k->kuota_siswa = 30;
        $k->save();

        return $k;
    }

    private function makeSiswa(int $cabangId, ?int $kelasId, string $status, string $nama): Siswa
    {
        $u = $this->makeUser('siswa', strtolower($nama).'@test.local');

        $s = new Siswa;
        $s->user_id = $u->id;
        $s->cabang_id = $cabangId;
        $s->kelas_id = $kelasId;
        $s->nisn = 'N'.substr(md5($nama), 0, 10);
        $s->nama_lengkap = $nama;
        $s->jenis_kelamin = 'L';
        $s->tempat_lahir = '-';
        $s->tanggal_lahir = '2010-01-01';
        $s->alamat = '-';
        $s->tanggal_masuk = now();
        $s->status = $status;
        $s->save();

        return $s;
    }

    private function makeTagihan(int $siswaId, int $taId, int $jumlah, string $status): Tagihan
    {
        $t = new Tagihan;
        $t->siswa_id = $siswaId;
        $t->tahun_ajaran_id = $taId;
        $t->jenis_tagihan = 'spp_januari';
        $t->jumlah = $jumlah;
        $t->status = $status;
        $t->tanggal_jatuh_tempo = now()->addMonth();
        $t->save();

        return $t;
    }
}
