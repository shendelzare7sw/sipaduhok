<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Kelas;
use App\Models\Notification;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Menjaga perbaikan notifikasi:
 * (A) BUG BESAR: notifikasi orang tua dulu SELALU ter-skip ($parent->user_id null,
 *     seharusnya $parent->id) → kini orang tua benar-benar menerima notifikasi.
 * (B) notifyHasilKenaikanKelas: NAIK/LULUS → siswa & ortu; TIDAK_NAIK → tak dikirim.
 * (C) notifyPembayaranBaru: bendahara & admin.
 * (D) notifyTagihanMassal: orang tua.
 */
class NotificationParentAndKenaikanTest extends TestCase
{
    public function test_notifikasi_ortu_kenaikan_pembayaran_dan_tagihan_massal(): void
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
            $siswa = $this->makeSiswa($cabang->id, $kelas->id, 'S' . $suffix);

            // Orang tua login + tautkan ke siswa via pivot student_parents.
            $parent = $this->makeUser('orang_tua', "ortu.$suffix@test.local");
            $siswa->parents()->attach($parent->id, [
                'relationship' => 'ayah',
                'is_primary' => true,
                'is_financial_responsible' => true,
                'can_access_academic' => true,
            ]);

            $svc = new NotificationService();

            // ── (A) BUG FIX: tagihan baru → ORANG TUA menerima notif (dulu 0) ──
            $tagihan = $this->makeTagihan($siswa->id, $ta->id, 100000, 'belum_bayar');
            $svc->notifyTagihanBaru($tagihan);
            $this->assertSame(1, Notification::where('user_id', $parent->id)->count(),
                'Orang tua HARUS menerima notifikasi tagihan (bug $parent->user_id → $parent->id)');

            // ── (B) Hasil kenaikan kelas NAIK → siswa & ortu ──
            $svc->notifyHasilKenaikanKelas($siswa, 'NAIK_KELAS', 'Kelas 8A');
            $this->assertSame(1, Notification::where('user_id', $siswa->user_id)
                ->where('tipe', Notification::TIPE_KENAIKAN)->count());
            $this->assertSame(1, Notification::where('user_id', $parent->id)
                ->where('tipe', Notification::TIPE_KENAIKAN)->count());

            // ── (C) TIDAK_NAIK_KELAS → tidak menambah notifikasi (sengaja senyap) ──
            $ortuBefore = Notification::where('user_id', $parent->id)->count();
            $svc->notifyHasilKenaikanKelas($siswa, 'TIDAK_NAIK_KELAS', 'Kelas 7A');
            $this->assertSame($ortuBefore, Notification::where('user_id', $parent->id)->count(),
                'Hasil TIDAK_NAIK_KELAS tidak boleh mengirim notifikasi');

            // ── (D) Pembayaran baru → bendahara & admin ──
            $bendahara = $this->makeUser('bendahara', "bend.$suffix@test.local");
            $admin = $this->makeUser('admin', "adm.$suffix@test.local");
            $pmb = $this->makePembayaran($tagihan->id, $siswa->id, 50000, 'PAY-' . $suffix);
            $svc->notifyPembayaranBaru($pmb);
            $this->assertSame(1, Notification::where('user_id', $bendahara->id)
                ->where('tipe', Notification::TIPE_PEMBAYARAN)->count());
            $this->assertSame(1, Notification::where('user_id', $admin->id)
                ->where('tipe', Notification::TIPE_PEMBAYARAN)->count());

            // ── (E) Tagihan massal → orang tua ──
            $ortuBefore2 = Notification::where('user_id', $parent->id)->count();
            $svc->notifyTagihanMassal([$siswa->id], 'Tagihan SPP baru telah ditambahkan.');
            $this->assertSame($ortuBefore2 + 1, Notification::where('user_id', $parent->id)->count());
        } finally {
            DB::connection('mysql')->rollBack();
        }
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
        $p->metode_pembayaran = 'transfer';
        $p->status_validasi = 'pending';
        $p->save();
        return $p;
    }
}
