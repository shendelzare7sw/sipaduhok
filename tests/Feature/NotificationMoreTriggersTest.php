<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Kelas;
use App\Models\LmsMeeting;
use App\Models\MataPelajaran;
use App\Models\Notification;
use App\Models\Pengumuman;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Menjaga pemicu notifikasi tambahan (#4-#7):
 * - notifyKelasVirtualBaru  → siswa di kelas
 * - notifyAbsensiAlpha      → orang tua
 * - notifyGuruPengajarAssignments → guru (per penugasan baru)
 * - notifyPengumumanBaru    → user per-role, dgn field yg benar (isi_pengumuman)
 */
class NotificationMoreTriggersTest extends TestCase
{
    public function test_pemicu_notifikasi_tambahan(): void
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
            $mapel = $this->makeMapel($suffix);
            $siswa = $this->makeSiswa($cabang->id, $kelas->id, 'S' . $suffix);
            $parent = $this->makeUser('orang_tua', "ortu.$suffix@test.local");
            $siswa->parents()->attach($parent->id, ['relationship' => 'ayah', 'is_primary' => true]);

            $svc = new NotificationService();

            // Guru (dipakai untuk meeting.guru_id & penugasan #5)
            $guruUser = $this->makeUser('guru_pengajar', "guru.$suffix@test.local");
            $tenaga = new TenagaPendidik();
            $tenaga->user_id = $guruUser->id;
            $tenaga->nama_lengkap = 'Guru ' . $suffix;
            $tenaga->jenis_kelamin = 'L';
            $tenaga->save();

            // ── #6 Kelas virtual → siswa di kelas ──
            $meeting = LmsMeeting::create([
                'kelas_id' => $kelas->id,
                'mata_pelajaran_id' => $mapel->id,
                'guru_id' => $tenaga->id,
                'judul' => 'Meeting ' . $suffix,
                'platform' => 'zoom',
                'link_meeting' => 'https://zoom.us/j/' . $suffix,
                'waktu_mulai' => now()->addDay(),
            ]);
            $svc->notifyKelasVirtualBaru($meeting);
            $this->assertSame(1, Notification::where('user_id', $siswa->user_id)
                ->where('tipe', Notification::TIPE_MATERI)->count());

            // ── #7 Absensi alpha → orang tua ──
            $presensi = Presensi::create([
                'siswa_id' => $siswa->id,
                'kelas_id' => $kelas->id,
                'tanggal' => now()->toDateString(),
                'status' => 'alpha',
                'diinput_oleh' => $parent->id,
            ]);
            $svc->notifyAbsensiAlpha($presensi);
            $this->assertSame(1, Notification::where('user_id', $parent->id)
                ->where('tipe', Notification::TIPE_IZIN)->count());

            // ── #5 Penugasan guru → guru (guru & tenaga sudah dibuat di atas) ──
            $svc->notifyGuruPengajarAssignments([
                ['tenaga_pendidik_id' => $tenaga->id, 'kelas_id' => $kelas->id, 'mata_pelajaran_id' => $mapel->id],
                ['tenaga_pendidik_id' => $tenaga->id, 'kelas_id' => $kelas->id, 'mata_pelajaran_id' => $mapel->id],
            ]);
            // Diringkas per guru → tepat 1 notifikasi walau 2 penugasan.
            $this->assertSame(1, Notification::where('user_id', $guruUser->id)
                ->where('tipe', Notification::TIPE_KELAS)->count());

            // ── #4 Pengumuman → field isi_pengumuman benar (pesan tidak kosong) ──
            $pengumuman = Pengumuman::create([
                'dibuat_oleh' => $parent->id,
                'judul' => 'Judul ' . $suffix,
                'isi_pengumuman' => 'ISI-PENGUMUMAN-' . $suffix,
                'tanggal_pengumuman' => now()->toDateString(),
                'prioritas' => 'biasa',
                'status' => 'aktif',
            ]);
            $svc->notifyPengumumanBaru($pengumuman);
            $notifSiswa = Notification::where('user_id', $siswa->user_id)
                ->where('tipe', Notification::TIPE_PENGUMUMAN)->first();
            $this->assertNotNull($notifSiswa, 'Siswa harus menerima notifikasi pengumuman');
            $this->assertStringContainsString('ISI-PENGUMUMAN-' . $suffix, $notifSiswa->pesan,
                'Pesan harus dari isi_pengumuman (bukan field $isi yang tak ada)');
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

    private function makeMapel(string $suffix): MataPelajaran
    {
        $m = new MataPelajaran();
        $m->kode_mapel = 'K' . $suffix;
        $m->nama_mapel = 'Mapel ' . $suffix;
        $m->jenjang = 'SMP';
        $m->save();
        return $m;
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
}
