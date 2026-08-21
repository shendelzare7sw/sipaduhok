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
 * Fitur "Alumni Menunggak" di Kelola Tagihan bendahara:
 * mode ?tunggakan_alumni=1 hanya menampilkan siswa berstatus 'lulus' yang MASIH
 * punya tunggakan (lintas semua tahun ajaran), untuk penebusan ijazah.
 * Menjaga: alumni lunas & siswa aktif TIDAK muncul di mode ini; mode normal tetap
 * menampilkan siswa aktif.
 */
class BendaharaAlumniMenunggakFilterTest extends TestCase
{
    public function test_mode_alumni_hanya_tampilkan_alumni_yang_masih_menunggak(): void
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

            // Alumni menunggak: lulus + tagihan belum lunas.
            $alumniNunggak = $this->makeSiswa($cabang->id, null, 'lulus', "AlumniNunggak$suffix");
            $this->makeTagihan($alumniNunggak->id, $ta->id, 100000, 'belum_bayar');

            // Alumni lunas: lulus + tagihan sudah_bayar → TIDAK boleh muncul di mode alumni.
            $alumniLunas = $this->makeSiswa($cabang->id, null, 'lulus', "AlumniLunas$suffix");
            $this->makeTagihan($alumniLunas->id, $ta->id, 100000, 'sudah_bayar');

            // Siswa aktif menunggak → TIDAK boleh muncul di mode alumni, tapi muncul di mode normal.
            $siswaAktif = $this->makeSiswa($cabang->id, $kelas->id, 'aktif', "SiswaAktif$suffix");
            $this->makeTagihan($siswaAktif->id, $ta->id, 100000, 'belum_bayar');

            $this->actingAs($bendahara)->withoutMiddleware();

            // Pakai search=$suffix (ketiga nama mengandung suffix unik) agar hasil
            // deterministik meski DB test berisi banyak siswa nyata (hindari flaky pagination).

            // Mode alumni: hanya alumni menunggak; alumni lunas & siswa aktif tersaring keluar
            // oleh FILTER (bukan sekadar oleh search).
            $resAlumni = $this->get(route('bendahara.tagihan.index', ['tunggakan_alumni' => 1, 'search' => $suffix]));
            $resAlumni->assertOk();
            $resAlumni->assertSee("AlumniNunggak$suffix");
            $resAlumni->assertDontSee("AlumniLunas$suffix");
            $resAlumni->assertDontSee("SiswaAktif$suffix");

            // Mode normal (search sama): siswa aktif tetap muncul → membuktikan mode alumni
            // benar-benar menyaring, bukan search-nya.
            $resNormal = $this->get(route('bendahara.tagihan.index', ['search' => $suffix]));
            $resNormal->assertOk();
            $resNormal->assertSee("SiswaAktif$suffix");
            $resNormal->assertDontSee("AlumniNunggak$suffix");
            $resNormal->assertDontSee("AlumniLunas$suffix");
            $resNormal->assertViewHas('siswaList', function ($siswaList) use ($siswaAktif) {
                $row = $siswaList->getCollection()->firstWhere('id', $siswaAktif->id);

                return $row && (float) $row->total_tagihan === 100000.0;
            });

            // Surface Admin Keuangan (extends controller Bendahara + view sendiri) harus
            // punya mode & tombol yang sama.
            $resAdmin = $this->get(route('admin.keuangan.tagihan.index', ['tunggakan_alumni' => 1, 'search' => $suffix]));
            $resAdmin->assertOk();
            $resAdmin->assertSee("AlumniNunggak$suffix");
            $resAdmin->assertDontSee("SiswaAktif$suffix");
            $resAdmin->assertSee('Alumni Menunggak'); // tombol/banner ada di view admin
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    public function test_tagihan_massal_tampil_di_indeks_tahun_aktif_dan_tidak_diduplikasi(): void
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
            $cabang = Cabang::firstOrFail();
            $ta = TahunAjaran::where('is_active', true)->firstOrFail();
            $kelas = $this->makeKelas($cabang->id, $ta->id, $suffix);
            $siswa = $this->makeSiswa($cabang->id, $kelas->id, 'aktif', "Massal$suffix");
            $bendahara = $this->makeUser('bendahara', "bendahara.massal.$suffix@test.local");

            $payload = [
                'kelas_ids' => [$kelas->id],
                'tagihan' => ['uang_pendaftaran' => '125.000'],
                'tanggal_jatuh_tempo' => [
                    'uang_pendaftaran' => $ta->tanggal_selesai->toDateString(),
                ],
            ];

            $this->actingAs($bendahara)->withoutMiddleware();

            $created = $this->post(route('bendahara.tagihan.bulk-create'), $payload);
            $created->assertRedirect(route('bendahara.tagihan.index', ['tahun_ajaran_id' => $ta->id]));
            $created->assertSessionHas('success');

            $this->assertDatabaseHas('tagihan', [
                'siswa_id' => $siswa->id,
                'tahun_ajaran_id' => $ta->id,
                'jenis_tagihan' => 'uang_pendaftaran',
                'jumlah' => 125000,
                'tanggal_jatuh_tempo' => $ta->tanggal_selesai->toDateString(),
            ]);

            $index = $this->get(route('bendahara.tagihan.index', [
                'tahun_ajaran_id' => $ta->id,
                'search' => $suffix,
            ]));
            $index->assertOk();
            $index->assertViewHas('siswaList', function ($siswaList) use ($siswa) {
                $row = $siswaList->getCollection()->firstWhere('id', $siswa->id);

                return $row && (float) $row->total_tagihan === 125000.0;
            });

            $duplicate = $this->post(route('bendahara.tagihan.bulk-create'), $payload);
            $duplicate->assertSessionHas('warning');
            $this->assertSame(1, Tagihan::where([
                'siswa_id' => $siswa->id,
                'tahun_ajaran_id' => $ta->id,
                'jenis_tagihan' => 'uang_pendaftaran',
            ])->count());
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

    private function makeSiswa(int $cabangId, ?int $kelasId, string $status, string $suffix): Siswa
    {
        $u = $this->makeUser('siswa', "siswa.$suffix@test.local");

        $s = new Siswa;
        $s->user_id = $u->id;
        $s->cabang_id = $cabangId;
        $s->kelas_id = $kelasId;
        $s->nisn = 'N'.$suffix;
        $s->nama_lengkap = $suffix;
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
