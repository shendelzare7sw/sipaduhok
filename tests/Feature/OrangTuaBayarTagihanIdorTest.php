<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Regresi-guard F-18: wali siswa hanya boleh membayar tagihan milik ANAK-nya sendiri.
 * prosesBayar (pembayaran tunggal) dulu hanya validasi `exists:tagihan,id` tanpa cek
 * kepemilikan → bisa melampirkan tagihan siswa lain. (processBulkPay sudah aman.)
 */
class OrangTuaBayarTagihanIdorTest extends TestCase
{
    public function test_wali_siswa_tidak_bisa_bayar_tagihan_anak_orang_lain(): void
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
        Storage::fake('public');

        DB::connection('mysql')->beginTransaction();
        try {
            $suffix = substr(md5(uniqid('', true)), 0, 8);

            $cabang = Cabang::first();
            $ta = TahunAjaran::where('is_active', true)->first();
            $this->assertNotNull($cabang);
            $this->assertNotNull($ta, 'Butuh tahun ajaran aktif');

            $kelas = $this->makeKelas($cabang->id, $ta->id, $suffix);

            // Orang tua yang login + anaknya sendiri
            $parent = $this->makeUser('orang_tua', "ortu.$suffix@test.local");
            $anakSendiri = $this->makeSiswa($cabang->id, $kelas->id, 'S' . $suffix);
            $this->linkParent($parent->id, $anakSendiri->id);

            // Anak orang lain (TIDAK terhubung ke $parent)
            $anakOrangLain = $this->makeSiswa($cabang->id, $kelas->id, 'L' . $suffix);

            $tagihanSendiri = $this->makeTagihan($anakSendiri->id, $ta->id);
            $tagihanOrangLain = $this->makeTagihan($anakOrangLain->id, $ta->id);

            $this->actingAs($parent)->withoutMiddleware();

            // Negatif (F-18): bayar tagihan anak orang lain (lewat route anak sendiri) => ditolak.
            $this->post(route('wali-siswa.tagihan.bayar', $anakSendiri->id), [
                'tagihan_id' => $tagihanOrangLain->id,
                'jumlah_bayar' => 50000,
                'metode_pembayaran' => 'transfer',
                'bukti_bayar' => UploadedFile::fake()->image('bukti.jpg'),
            ])->assertRedirect();
            $this->assertDatabaseMissing('pembayaran', ['tagihan_id' => $tagihanOrangLain->id]);

            // Positif: bayar tagihan anak sendiri => berhasil dibuat (pending).
            $this->post(route('wali-siswa.tagihan.bayar', $anakSendiri->id), [
                'tagihan_id' => $tagihanSendiri->id,
                'jumlah_bayar' => 50000,
                'metode_pembayaran' => 'transfer',
                'bukti_bayar' => UploadedFile::fake()->image('bukti.jpg'),
            ])->assertRedirect();
            $this->assertDatabaseHas('pembayaran', [
                'tagihan_id' => $tagihanSendiri->id,
                'siswa_id' => $anakSendiri->id,
                'status_validasi' => 'pending',
            ]);
        } finally {
            DB::connection('mysql')->rollBack();
        }
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

    private function makeSiswa(int $cabangId, int $kelasId, string $suffix): Siswa
    {
        $u = $this->makeUser('siswa', "siswa.$suffix@test.local");

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

    private function linkParent(int $parentId, int $siswaId): void
    {
        DB::connection('mysql')->table('student_parents')->insert([
            'parent_id' => $parentId,
            'siswa_id' => $siswaId,
            'relationship' => 'ayah_kandung',
            'is_primary' => true,
            'is_financial_responsible' => true,
            'can_access_academic' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function makeTagihan(int $siswaId, int $taId): Tagihan
    {
        $t = new Tagihan();
        $t->siswa_id = $siswaId;
        $t->tahun_ajaran_id = $taId;
        $t->jenis_tagihan = 'spp';
        $t->keterangan = 'SPP Test';
        $t->jumlah = 50000;
        $t->tanggal_jatuh_tempo = now()->addDays(7);
        $t->status = 'belum_bayar';
        $t->save();
        return $t;
    }
}
