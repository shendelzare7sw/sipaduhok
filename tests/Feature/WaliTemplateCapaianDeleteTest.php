<?php

namespace Tests\Feature;

use App\Models\MataPelajaran;
use App\Models\TemplateCapaianKompetensi;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Regresi-guard F-17 (final: pustaka PRIVATE per wali). Template hanya boleh
 * dikelola (edit & hapus) oleh pembuatnya; wali lain tak boleh mengubah/menghapus.
 */
class WaliTemplateCapaianDeleteTest extends TestCase
{
    public function test_template_hanya_bisa_dikelola_pembuatnya(): void
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
            $template->template_text = 'ISI ASLI';
            $template->created_by = $waliPembuat->id;
            $template->save();

            // ===== Wali LAIN: edit & hapus DITOLAK (pustaka private) =====
            $this->actingAs($waliLain)->withoutMiddleware();

            // Hapus oleh wali lain → ditolak, template tetap ada.
            $this->delete(route('wali.template-capaian.destroy', $template->id))->assertRedirect();
            $this->assertDatabaseHas('template_capaian_kompetensi', ['id' => $template->id]);

            // Edit oleh wali lain → ditolak, isi TIDAK berubah.
            $this->put(route('wali.template-capaian.update', $template->id), [
                'mata_pelajaran_id' => $mapel->id,
                'template_text' => 'ISI DIEDIT ORANG LAIN',
            ])->assertRedirect();
            $this->assertSame('ISI ASLI', $template->fresh()->template_text);

            // ===== Pembuat: edit & hapus BOLEH =====
            $this->actingAs($waliPembuat)->withoutMiddleware();

            $this->put(route('wali.template-capaian.update', $template->id), [
                'mata_pelajaran_id' => $mapel->id,
                'template_text' => 'ISI DIPERBARUI PEMBUAT',
            ])->assertRedirect();
            $this->assertSame('ISI DIPERBARUI PEMBUAT', $template->fresh()->template_text);

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
