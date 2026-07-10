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
 * Regresi-guard F-22: "Terapkan Template" capaian pada rapor hanya boleh untuk kelas
 * yang diampu wali. applyTemplate (per-rapor) & applyTemplateToAll (se-kelas) dulu
 * memuat rapor/kelas via id tanpa cek kepemilikan → wali bisa menulis deskripsi capaian
 * pada rapor kelas lain. (Terlewat saat F-11; ditemukan saat menelusuri alur template.)
 */
class WaliApplyTemplateIdorTest extends TestCase
{
    public function test_wali_tidak_bisa_terapkan_template_ke_rapor_kelas_lain(): void
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

            $waliUser = new User();
            $waliUser->name = 'Wali F22';
            $waliUser->email = "wali.f22.$suffix@test.local";
            $waliUser->role = 'wali_kelas';
            $waliUser->password = bcrypt('password');
            $waliUser->save();

            $tenaga = new TenagaPendidik();
            $tenaga->user_id = $waliUser->id;
            $tenaga->nama_lengkap = 'Wali F22';
            $tenaga->jenis_kelamin = 'L';
            $tenaga->save();

            $kelasOwn = $this->makeKelas($cabang->id, $ta->id, 'A' . $suffix);
            $kelasForeign = $this->makeKelas($cabang->id, $ta->id, 'B' . $suffix);

            $assign = new WaliKelasAssignment();
            $assign->tenaga_pendidik_id = $tenaga->id;
            $assign->kelas_id = $kelasOwn->id;
            $assign->assigned_at = now();
            $assign->save();

            $mapel = $this->makeMapel('M' . $suffix);
            $template = $this->makeTemplate($mapel->id, $waliUser->id, $suffix);

            $siswaOwn = $this->makeSiswa($cabang->id, $kelasOwn->id, 'O' . $suffix);
            $siswaForeign = $this->makeSiswa($cabang->id, $kelasForeign->id, 'F' . $suffix);

            $raporOwn = $this->makeRapor($siswaOwn->id, $kelasOwn->id, $ta->id);
            $raporForeign = $this->makeRapor($siswaForeign->id, $kelasForeign->id, $ta->id);

            $nilaiRecOwn = $this->makeNilai($siswaOwn->id, $mapel->id, $kelasOwn->id, $ta->id, $tenaga->id);
            $nilaiRecForeign = $this->makeNilai($siswaForeign->id, $mapel->id, $kelasForeign->id, $ta->id, $tenaga->id);

            $nilaiOwn = $this->makeRaporNilai($raporOwn->id, $mapel->id, $nilaiRecOwn->id);
            $nilaiForeign = $this->makeRaporNilai($raporForeign->id, $mapel->id, $nilaiRecForeign->id);

            $this->actingAs($waliUser)->withoutMiddleware();

            // ===== applyTemplate (per-rapor) =====
            // Positif: rapor kelas sendiri → deskripsi terisi template.
            $this->post(route('wali.rapor.apply-template'), [
                'rapor_id' => $raporOwn->id,
                'mata_pelajaran_id' => $mapel->id,
                'template_id' => $template->id,
            ])->assertRedirect();
            $this->assertSame('CAPAIAN TEMPLATE TEST', $nilaiOwn->fresh()->deskripsi);

            // Negatif (F-22): rapor kelas lain → 404 & deskripsi tetap kosong.
            $this->post(route('wali.rapor.apply-template'), [
                'rapor_id' => $raporForeign->id,
                'mata_pelajaran_id' => $mapel->id,
                'template_id' => $template->id,
            ])->assertNotFound();
            $this->assertNull($nilaiForeign->fresh()->deskripsi);

            // ===== applyTemplateToAll (se-kelas) =====
            // Negatif (F-22): kelas lain → 404 & deskripsi tetap kosong.
            $this->post(route('wali.rapor.apply-template-all'), [
                'kelas_id' => $kelasForeign->id,
                'semester' => 'ganjil',
                'jenis_rapor' => 'akhir_semester',
                'mata_pelajaran_id' => $mapel->id,
                'template_id' => $template->id,
            ])->assertNotFound();
            $this->assertNull($nilaiForeign->fresh()->deskripsi);
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

    private function makeMapel(string $suffix): MataPelajaran
    {
        $m = new MataPelajaran();
        $m->kode_mapel = 'K' . $suffix;
        $m->nama_mapel = 'Mapel ' . $suffix;
        $m->jenjang = 'SMP';
        $m->save();
        return $m;
    }

    private function makeTemplate(int $mapelId, int $createdBy, string $suffix): TemplateCapaianKompetensi
    {
        $t = new TemplateCapaianKompetensi();
        $t->mata_pelajaran_id = $mapelId;
        $t->nama_template = 'Template ' . $suffix;
        $t->template_text = 'CAPAIAN TEMPLATE TEST';
        $t->created_by = $createdBy;
        $t->save();
        return $t;
    }

    private function makeSiswa(int $cabangId, int $kelasId, string $suffix): Siswa
    {
        $u = new User();
        $u->name = 'Siswa ' . $suffix;
        $u->email = "siswa.$suffix@test.local";
        $u->role = 'siswa';
        $u->password = bcrypt('password');
        $u->save();

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
