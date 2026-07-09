<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\ForumDiskusi;
use App\Models\ForumReply;
use App\Models\GuruPengajarKelas;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Regresi-guard F-16: forum diskusi guru hanya boleh dibaca/dikelola pada kelas+mapel
 * yang diajar guru. Membuka/pin/menghapus balasan pada forum mapel lain harus ditolak,
 * meski guru menaruh kelas+mapel miliknya sendiri di URL (verifyAccess lolos).
 */
class GuruForumIdorTest extends TestCase
{
    public function test_guru_tidak_bisa_kelola_forum_mapel_lain(): void
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
            $ta = TahunAjaran::first();
            $this->assertNotNull($cabang);
            $this->assertNotNull($ta);

            $guruUser = new User();
            $guruUser->name = 'Guru F16';
            $guruUser->email = "guru.f16.$suffix@test.local";
            $guruUser->role = 'guru_pengajar';
            $guruUser->password = bcrypt('password');
            $guruUser->save();

            $tenaga = new TenagaPendidik();
            $tenaga->user_id = $guruUser->id;
            $tenaga->nama_lengkap = 'Guru F16';
            $tenaga->jenis_kelamin = 'L';
            $tenaga->save();

            $kelas = new Kelas();
            $kelas->cabang_id = $cabang->id;
            $kelas->tahun_ajaran_id = $ta->id;
            $kelas->nama_kelas = 'Kelas F16';
            $kelas->jenjang = 'SMP';
            $kelas->kode_kelas = 'KF16' . $suffix;
            $kelas->kuota_siswa = 30;
            $kelas->save();

            $mapelX = $this->makeMapel('X' . $suffix); // diajar guru
            $mapelY = $this->makeMapel('Y' . $suffix); // TIDAK diajar guru

            $gpk = new GuruPengajarKelas();
            $gpk->tenaga_pendidik_id = $tenaga->id;
            $gpk->kelas_id = $kelas->id;
            $gpk->mata_pelajaran_id = $mapelX->id;
            $gpk->save();

            $forumOwn = $this->makeForum($kelas->id, $mapelX->id, $guruUser->id);
            $forumForeign = $this->makeForum($kelas->id, $mapelY->id, $guruUser->id);

            $replyOwn = $this->makeReply($forumOwn->id, $guruUser->id);
            $replyForeign = $this->makeReply($forumForeign->id, $guruUser->id);

            $this->actingAs($guruUser)->withoutMiddleware();

            // ===== show (baca) =====
            // Negatif (F-16): forum mapel lain tak boleh dibaca walau URL pakai mapel sendiri.
            $this->get(route('guru.lms.forum.show', [$kelas->id, $mapelX->id, $forumForeign->id]))
                ->assertNotFound();

            // ===== togglePin (tulis) =====
            // Positif: pin forum mapel sendiri => berhasil, state berubah.
            $this->patch(route('guru.lms.forum.togglePin', [$kelas->id, $mapelX->id, $forumOwn->id]))
                ->assertRedirect();
            $this->assertTrue((bool) $forumOwn->fresh()->is_pinned);

            // Negatif (F-16): pin forum mapel lain => ditolak, state tidak berubah.
            $this->patch(route('guru.lms.forum.togglePin', [$kelas->id, $mapelX->id, $forumForeign->id]))
                ->assertNotFound();
            $this->assertFalse((bool) $forumForeign->fresh()->is_pinned);

            // ===== destroyReply (hapus) =====
            // Positif: hapus balasan pada forum mapel sendiri => berhasil.
            $this->delete(route('guru.lms.forum.reply.destroy', [$kelas->id, $mapelX->id, $forumOwn->id, $replyOwn->id]))
                ->assertRedirect();
            $this->assertDatabaseMissing('forum_replies', ['id' => $replyOwn->id]);

            // Negatif (F-16): hapus balasan pada forum mapel lain => ditolak, balasan tetap ada.
            $this->delete(route('guru.lms.forum.reply.destroy', [$kelas->id, $mapelX->id, $forumForeign->id, $replyForeign->id]))
                ->assertNotFound();
            $this->assertDatabaseHas('forum_replies', ['id' => $replyForeign->id]);
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    private function makeMapel(string $suffix): MataPelajaran
    {
        $m = new MataPelajaran();
        $m->kode_mapel = 'M' . $suffix;
        $m->nama_mapel = 'Mapel ' . $suffix;
        $m->jenjang = 'SMP';
        $m->save();
        return $m;
    }

    private function makeForum(int $kelasId, int $mapelId, int $userId): ForumDiskusi
    {
        $f = new ForumDiskusi();
        $f->kelas_id = $kelasId;
        $f->mata_pelajaran_id = $mapelId;
        $f->user_id = $userId;
        $f->topik = ForumDiskusi::TOPIK_UMUM;
        $f->judul = 'Diskusi Test';
        $f->isi = 'Isi diskusi';
        $f->is_pinned = false;
        $f->is_closed = false;
        $f->save();
        return $f;
    }

    private function makeReply(int $forumId, int $userId): ForumReply
    {
        $r = new ForumReply();
        $r->forum_diskusi_id = $forumId;
        $r->user_id = $userId;
        $r->isi = 'Balasan test';
        $r->save();
        return $r;
    }
}
