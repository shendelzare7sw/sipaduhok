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
 * Regresi-guard F-19: endpoint "finish" Midtrans (snapFinish) TIDAK boleh mempercayai
 * `transaction_status` dari query (URL finish tak bertanda tangan).
 *  - Memalsukan `?transaction_status=settlement` TIDAK boleh menandai pembayaran lunas.
 *  - Order milik anak orang lain TIDAK boleh disentuh (403).
 * Jalur otoritatif adalah webhook bertanda tangan (MidtransWebhookController).
 */
class OrangTuaSnapFinishForgeryTest extends TestCase
{
    public function test_snap_finish_tidak_percaya_status_dari_query_dan_cek_kepemilikan(): void
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
            $this->assertNotNull($ta);

            $kelas = $this->makeKelas($cabang->id, $ta->id, $suffix);

            $parentA = $this->makeUser('orang_tua', "ortuA.$suffix@test.local");
            $anakA = $this->makeSiswa($cabang->id, $kelas->id, 'A' . $suffix);
            $this->linkParent($parentA->id, $anakA->id);

            $parentB = $this->makeUser('orang_tua', "ortuB.$suffix@test.local");
            $anakB = $this->makeSiswa($cabang->id, $kelas->id, 'B' . $suffix);
            $this->linkParent($parentB->id, $anakB->id);

            $tagihanA = $this->makeTagihan($anakA->id, $ta->id);
            $orderId = 'ORD-TEST-' . $suffix;
            $bayarA = $this->makePendingMidtrans($anakA->id, $tagihanA->id, $orderId, 'PAY-' . $suffix);

            // ===== Forgery: parent A memalsukan status settlement lewat query =====
            $this->actingAs($parentA)->withoutMiddleware();
            $this->get(route('wali-siswa.pembayaran.snap.finish', [
                'order_id' => $orderId,
                'status_code' => '200',
                'transaction_status' => 'settlement',
            ]))->assertRedirect();

            // Status HARUS tetap pending (query tidak dipercaya; API otoritatif tak konfirmasi).
            $this->assertSame('pending', $bayarA->fresh()->status_validasi,
                'snapFinish tidak boleh menandai lunas hanya dari query transaction_status');
            $this->assertSame('belum_bayar', $tagihanA->fresh()->status,
                'Tagihan tidak boleh berubah lunas dari forgery finish redirect');

            // ===== Kepemilikan: parent B mencoba menyentuh order milik anak A =====
            $this->actingAs($parentB)->withoutMiddleware();
            $this->get(route('wali-siswa.pembayaran.snap.finish', [
                'order_id' => $orderId,
                'status_code' => '200',
                'transaction_status' => 'settlement',
            ]))->assertForbidden();
            $this->assertSame('pending', $bayarA->fresh()->status_validasi);
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

    private function makePendingMidtrans(int $siswaId, int $tagihanId, string $orderId, string $kode): \App\Models\Pembayaran
    {
        $p = new \App\Models\Pembayaran();
        $p->tagihan_id = $tagihanId;
        $p->siswa_id = $siswaId;
        $p->kode_pembayaran = $kode;
        $p->jumlah_bayar = 50000;
        $p->tanggal_bayar = now();
        $p->metode_pembayaran = 'midtrans';
        $p->payment_gateway = 'midtrans';
        $p->order_id = $orderId;
        $p->status_validasi = 'pending';
        $p->save();
        return $p;
    }
}
