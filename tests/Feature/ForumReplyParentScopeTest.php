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
 * Regresi-guard F-07: balasan bertingkat (parent_id) harus berada di diskusi yang sama.
 * "parent_id" = balasan-induk pada thread, BUKAN orang tua. Membalas dengan parent_id
 * milik diskusi lain harus ditolak (404), bukan membuat balasan lintas-diskusi.
 */
class ForumReplyParentScopeTest extends TestCase
{
    public function test_balasan_tidak_bisa_pakai_parent_dari_diskusi_lain(): void
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
            $guruUser->name = 'Guru F07';
            $guruUser->email = "guru.f07.$suffix@test.local";
            $guruUser->role = 'guru_pengajar';
            $guruUser->password = bcrypt('password');
            $guruUser->save();

            $tenaga = new TenagaPendidik();
            $tenaga->user_id = $guruUser->id;
            $tenaga->nama_lengkap = 'Guru F07';
            $tenaga->jenis_kelamin = 'L';
            $tenaga->save();

            $kelas = new Kelas();
            $kelas->cabang_id = $cabang->id;
            $kelas->tahun_ajaran_id = $ta->id;
            $kelas->nama_kelas = 'Kelas F07';
            $kelas->jenjang = 'SMP';
            $kelas->kode_kelas = 'KF07' . $suffix;
            $kelas->kuota_siswa = 30;
            $kelas->save();

            $mapel = new MataPelajaran();
            $mapel->kode_mapel = 'M' . $suffix;
            $mapel->nama_mapel = 'Mapel ' . $suffix;
            $mapel->jenjang = 'SMP';
            $mapel->save();

            $gpk = new GuruPengajarKelas();
            $gpk->tenaga_pendidik_id = $tenaga->id;
            $gpk->kelas_id = $kelas->id;
            $gpk->mata_pelajaran_id = $mapel->id;
            $gpk->save();

            // Dua diskusi di kelas+mapel yang sama (dua-duanya diajar guru → authorizedForum lolos).
            $forumTarget = $this->makeForum($kelas->id, $mapel->id, $guruUser->id);
            $forumLain = $this->makeForum($kelas->id, $mapel->id, $guruUser->id);

            $replyDiTarget = $this->makeReply($forumTarget->id, $guruUser->id);
            $replyDiLain = $this->makeReply($forumLain->id, $guruUser->id);

            $this->actingAs($guruUser)->withoutMiddleware();

            $before = ForumReply::where('forum_diskusi_id', $forumTarget->id)->count();

            // Negatif (F-07): balas ke forumTarget dengan parent dari forumLain → 404.
            $this->post(route('guru.lms.forum.reply', [$kelas->id, $mapel->id, $forumTarget->id]), [
                'isi' => 'Balasan nakal',
                'parent_id' => $replyDiLain->id,
            ])->assertNotFound();
            $this->assertSame($before, ForumReply::where('forum_diskusi_id', $forumTarget->id)->count());

            // Positif: parent dari diskusi yang sama → berhasil.
            $this->post(route('guru.lms.forum.reply', [$kelas->id, $mapel->id, $forumTarget->id]), [
                'isi' => 'Balasan sah',
                'parent_id' => $replyDiTarget->id,
            ])->assertRedirect();
            $this->assertSame($before + 1, ForumReply::where('forum_diskusi_id', $forumTarget->id)->count());
        } finally {
            DB::connection('mysql')->rollBack();
        }
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
        $r->isi = 'Balasan induk';
        $r->save();
        return $r;
    }
}
