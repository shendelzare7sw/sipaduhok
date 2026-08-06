<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Rapor;
use App\Models\RaporNilai;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\TemplateCapaianKompetensi;
use App\Models\TenagaPendidik;
use App\Models\User;
use App\Models\WaliKelasAssignment;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Fitur "Terapkan Template ke Kelas" (applyTemplateBatch): mengisi deskripsi capaian
 * seluruh siswa di kelas yang dikelola, per mapel, dari template milik wali sendiri.
 * Menjaga: (a) hanya isi yang kosong kecuali "timpa", (b) template harus milik wali
 * (created_by), (c) ter-scope ke kelas yang dikelola.
 */
class WaliApplyTemplateBatchTest extends TestCase
{
    public function test_terapkan_template_massal_per_mapel(): void
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

            // Wali yang login (diassign HANYA ke 1 kelas → auto-selected).
            $waliUser = $this->makeUser('wali_kelas', "wali.batch.$suffix@test.local");
            $tenaga = new TenagaPendidik();
            $tenaga->user_id = $waliUser->id;
            $tenaga->nama_lengkap = 'Wali Batch';
            $tenaga->jenis_kelamin = 'L';
            $tenaga->save();

            $otherUser = $this->makeUser('wali_kelas', "other.batch.$suffix@test.local");

            $kelas = $this->makeKelas($cabang->id, $ta->id, $suffix);

            $assign = new WaliKelasAssignment();
            $assign->tenaga_pendidik_id = $tenaga->id;
            $assign->kelas_id = $kelas->id;
            $assign->assigned_at = now();
            $assign->save();

            $mapel = $this->makeMapel('M' . $suffix);

            // Dua siswa + rapor + rapor_nilai (deskripsi kosong).
            $s1 = $this->makeSiswa($cabang->id, $kelas->id, 'A' . $suffix);
            $s2 = $this->makeSiswa($cabang->id, $kelas->id, 'B' . $suffix);
            $r1 = $this->makeRapor($s1->id, $kelas->id, $ta->id);
            $r2 = $this->makeRapor($s2->id, $kelas->id, $ta->id);
            $n1 = $this->makeNilai($s1->id, $mapel->id, $kelas->id, $ta->id, $tenaga->id);
            $n2 = $this->makeNilai($s2->id, $mapel->id, $kelas->id, $ta->id, $tenaga->id);
            $rn1 = $this->makeRaporNilai($r1->id, $mapel->id, $n1->id);
            $rn2 = $this->makeRaporNilai($r2->id, $mapel->id, $n2->id);

            $ownTpl = $this->makeTemplate($mapel->id, $waliUser->id, 'CAPAIAN SENDIRI', 'A' . $suffix);
            $foreignTpl = $this->makeTemplate($mapel->id, $otherUser->id, 'CAPAIAN ORANG LAIN', 'B' . $suffix);

            $this->actingAs($waliUser)->withoutMiddleware();

            $payloadBase = ['semester' => 'ganjil', 'jenis_rapor' => 'akhir_semester'];

            // 1) Negatif (created_by): pakai template milik wali lain → dilewati, deskripsi tetap kosong.
            $this->post(route('wali.rapor.apply-template-batch'), $payloadBase + [
                'templates' => [$mapel->id => $foreignTpl->id],
            ])->assertRedirect();
            $this->assertNull($rn1->fresh()->deskripsi);
            $this->assertNull($rn2->fresh()->deskripsi);

            // 2) Positif: template sendiri → semua siswa terisi.
            $this->post(route('wali.rapor.apply-template-batch'), $payloadBase + [
                'templates' => [$mapel->id => $ownTpl->id],
            ])->assertRedirect();
            $this->assertSame('CAPAIAN SENDIRI', $rn1->fresh()->deskripsi);
            $this->assertSame('CAPAIAN SENDIRI', $rn2->fresh()->deskripsi);

            // 3) Tanpa "timpa": deskripsi yang sudah terisi TIDAK berubah.
            $ownTpl2 = $this->makeTemplate($mapel->id, $waliUser->id, 'CAPAIAN BARU', 'C' . $suffix);
            $this->post(route('wali.rapor.apply-template-batch'), $payloadBase + [
                'templates' => [$mapel->id => $ownTpl2->id],
            ])->assertRedirect();
            $this->assertSame('CAPAIAN SENDIRI', $rn1->fresh()->deskripsi, 'Tanpa timpa, yang sudah terisi tak berubah');

            // 4) Dengan "timpa": deskripsi terisi diganti.
            $this->post(route('wali.rapor.apply-template-batch'), $payloadBase + [
                'templates' => [$mapel->id => $ownTpl2->id],
                'overwrite' => 1,
            ])->assertRedirect();
            $this->assertSame('CAPAIAN BARU', $rn1->fresh()->deskripsi);
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

    private function makeTemplate(int $mapelId, int $createdBy, string $text, string $suffix): TemplateCapaianKompetensi
    {
        $t = new TemplateCapaianKompetensi();
        $t->mata_pelajaran_id = $mapelId;
        $t->template_text = $text;
        $t->created_by = $createdBy;
        $t->save();
        return $t;
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

    private function makeRapor(int $siswaId, int $kelasId, int $taId): Rapor
    {
        $r = new Rapor();
        $r->siswa_id = $siswaId;
        $r->kelas_id = $kelasId;
        $r->tahun_ajaran_id = $taId;
        $r->semester = 'ganjil';
        $r->jenis_rapor = 'akhir_semester';
        $r->status = 'draft';
        $r->save();
        return $r;
    }

    private function makeNilai(int $siswaId, int $mapelId, int $kelasId, int $taId, int $guruId): \App\Models\Nilai
    {
        $n = new \App\Models\Nilai();
        $n->siswa_id = $siswaId;
        $n->mata_pelajaran_id = $mapelId;
        $n->kelas_id = $kelasId;
        $n->tahun_ajaran_id = $taId;
        $n->semester = 'ganjil';
        $n->guru_id = $guruId;
        $n->save();
        return $n;
    }

    private function makeRaporNilai(int $raporId, int $mapelId, int $nilaiId): RaporNilai
    {
        $rn = new RaporNilai();
        $rn->rapor_id = $raporId;
        $rn->mata_pelajaran_id = $mapelId;
        $rn->nilai_id = $nilaiId;
        $rn->nilai_angka = 80;
        $rn->nilai_huruf = 'B';
        $rn->deskripsi = null;
        $rn->save();
        return $rn;
    }
}
