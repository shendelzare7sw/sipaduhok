<?php

namespace Tests\Feature;

use App\Models\MataPelajaran;
use App\Models\TemplateCapaianKompetensi;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Regresi-guard F-17 (Opsi A): Template Capaian = pustaka bersama antar-wali.
 * Tambah/edit tetap terbuka, tetapi HAPUS hanya boleh oleh pembuatnya
 * (cegah wali menghapus permanen template rekannya).
 */
class WaliTemplateCapaianDeleteTest extends TestCase
{
    public function test_hapus_template_hanya_oleh_pembuat_tapi_edit_tetap_bersama(): void
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

            $waliPembuat = $this->makeWali("pembuat.$suffix@test.local", 'Pembuat');
            $waliLain = $this->makeWali("lain.$suffix@test.local", 'Lain');

            $mapel = new MataPelajaran();
            $mapel->kode_mapel = 'K' . $suffix;
            $mapel->nama_mapel = 'Mapel ' . $suffix;
            $mapel->jenjang = 'SMP';
            $mapel->save();

            $template = new TemplateCapaianKompetensi();
            $template->mata_pelajaran_id = $mapel->id;
            $template->nama_template = 'Template ' . $suffix;
            $template->template_text = 'ISI ASLI';
            $template->created_by = $waliPembuat->id;
            $template->save();

            // ===== HAPUS =====
            // Negatif (F-17): wali lain TIDAK boleh menghapus → template tetap ada.
            $this->actingAs($waliLain)->withoutMiddleware();
            $this->delete(route('wali.template-capaian.destroy', $template->id))->assertRedirect();
            $this->assertDatabaseHas('template_capaian_kompetensi', ['id' => $template->id]);

            // ===== EDIT (tetap bersama) =====
            // Wali lain MASIH boleh mengedit (Opsi A: edit terbuka, hanya hapus dikunci).
            $this->put(route('wali.template-capaian.update', $template->id), [
                'mata_pelajaran_id' => $mapel->id,
                'nama_template' => 'Template Diedit',
                'template_text' => 'ISI DIEDIT BERSAMA',
            ])->assertRedirect();
            $this->assertSame('ISI DIEDIT BERSAMA', $template->fresh()->template_text);

            // ===== HAPUS oleh pembuat =====
            // Positif: pembuat boleh menghapus → template terhapus.
            $this->actingAs($waliPembuat)->withoutMiddleware();
            $this->delete(route('wali.template-capaian.destroy', $template->id))->assertRedirect();
            $this->assertDatabaseMissing('template_capaian_kompetensi', ['id' => $template->id]);
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    private function makeWali(string $email, string $name): User
    {
        $u = new User();
        $u->name = $name;
        $u->email = $email;
        $u->role = 'wali_kelas';
        $u->password = bcrypt('password');
        $u->save();
        return $u;
    }
}
