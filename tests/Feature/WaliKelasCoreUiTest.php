<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Kelas;
use App\Models\GuruPengajarKelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Rapor;
use App\Models\RaporKegiatanEkstra;
use App\Models\RaporNilai;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\User;
use App\Models\WaliKelasAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class WaliKelasCoreUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unassigned_wali_sees_safe_empty_states(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas', 'is_active' => true]);
        $this->actingAs($user);

        $dashboard = $this->get(route('wali.dashboard'));
        $dashboard->assertOk()->assertSee('Kelas belum tersedia');
        $this->assertCleanflow($dashboard->getContent());

        $schedule = $this->get(route('wali.jadwal.index'));
        $schedule->assertOk()->assertSee('Data tenaga pendidik tidak ditemukan.');
        $this->assertCleanflow($schedule->getContent());

        $this->get(route('wali.jadwal.print'))->assertRedirect(route('wali.jadwal-pelajaran'));
    }

    public function test_dashboard_class_selection_schedule_and_print_use_tailwind_shell(): void
    {
        $cabang = Cabang::create([
            'kode_cabang' => 'WLI',
            'nama_cabang' => 'Cabang Wali',
            'alamat' => 'Alamat pengujian',
            'is_active' => true,
        ]);
        $tahun = TahunAjaran::create([
            'nama_tahun_ajaran' => '2026/2027',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2027-06-30',
            'is_active' => true,
        ]);
        $user = User::factory()->create([
            'role' => 'wali_kelas',
            'cabang_id' => $cabang->id,
            'is_active' => true,
        ]);
        $tenaga = TenagaPendidik::create([
            'user_id' => $user->id,
            'nama_lengkap' => 'Wali Uji',
            'jenis_kelamin' => 'L',
        ]);

        $kelas = [];
        foreach (['7A', '7B'] as $nama) {
            $kelas[] = Kelas::create([
                'cabang_id' => $cabang->id,
                'tahun_ajaran_id' => $tahun->id,
                'nama_kelas' => $nama,
                'jenjang' => 'SMP',
                'kode_kelas' => 'WLI-'.$nama,
                'kuota_siswa' => 30,
            ]);
            WaliKelasAssignment::create([
                'tenaga_pendidik_id' => $tenaga->id,
                'kelas_id' => end($kelas)->id,
                'assigned_at' => now(),
            ]);
        }

        $studentUser = User::factory()->create(['role' => 'siswa', 'cabang_id' => $cabang->id]);
        $student = new Siswa;
        $student->user_id = $studentUser->id;
        $student->cabang_id = $cabang->id;
        $student->kelas_id = $kelas[0]->id;
        $student->nisn = '9900000001';
        $student->nama_lengkap = 'Siswa Audit';
        $student->jenis_kelamin = 'L';
        $student->tempat_lahir = '-';
        $student->tanggal_lahir = '2010-01-01';
        $student->alamat = '-';
        $student->tanggal_masuk = now();
        $student->status = 'aktif';
        $student->save();

        $this->actingAs($user);

        $this->get(route('wali.dashboard'))->assertRedirect(route('wali.pilih-kelas'));

        $selection = $this->get(route('wali.pilih-kelas'));
        $selection->assertOk()->assertSee('Pilih kelas ini')->assertSee('7A')->assertSee('7B');
        $this->assertCleanflow($selection->getContent());

        $this->withSession(['wali_kelas_selected' => $kelas[0]->id]);
        foreach (['wali.dashboard', 'wali.jadwal.index', 'wali.jadwal-pelajaran'] as $routeName) {
            $response = $this->get(route($routeName));
            $response->assertOk()->assertSee('7A');
            $this->assertCleanflow($response->getContent());
        }

        $attendance = $this->get(route('wali.presensi.index'));
        $attendance->assertOk()->assertSee('Siswa Audit')->assertSee('Simpan presensi');
        $this->assertCleanflow($attendance->getContent());
        $this->assertSame(1, substr_count($attendance->getContent(), 'name="presensi[0][status]"'), 'Status siswa tidak boleh diduplikasi antara mobile dan desktop.');
        $this->assertStringNotContainsString('resources/css/wali-kelas/presensi/index.css', $attendance->getContent());

        $attendancePrint = $this->get(route('wali.presensi.print-rekap', ['bulan' => 9, 'tahun' => 2026]));
        $attendancePrint->assertOk()->assertSee('Pratinjau dokumen')->assertSee('Siswa Audit')->assertSee('Wali Uji');
        $attendancePrint->assertDontSee('id="admin-sidebar"', false);
        $this->assertCleanflow($attendancePrint->getContent());

        $today = now()->toDateString();
        $this->post(route('wali.presensi.input-harian'), [
            'kelas_id' => $kelas[0]->id,
            'tanggal' => $today,
            'presensi' => [['siswa_id' => $student->id, 'status' => 'hadir', 'keterangan' => 'Audit']],
        ])->assertRedirect();

        $dailyList = $this->get(route('wali.presensi.rekap-harian'));
        $dailyList->assertOk()->assertSee('Kehadiran');
        $this->assertCleanflow($dailyList->getContent());

        $dailyDetail = $this->get(route('wali.presensi.show-harian', ['tanggal' => $today]));
        $dailyDetail->assertOk()->assertSee('Siswa Audit')->assertSee('Audit')->assertSee('SIPADUHOK · Presensi Harian');
        $this->assertCleanflow($dailyDetail->getContent());
        $this->assertMatchesRegularExpression('/<aside id="admin-sidebar" class="[^"]*print:!hidden/', $dailyDetail->getContent());
        $this->assertMatchesRegularExpression('/<header class="sticky top-0 [^"]*print:!hidden/', $dailyDetail->getContent());
        $this->assertStringContainsString('<div data-ai-chatbot class="print:!hidden">', $dailyDetail->getContent());
        $this->assertStringContainsString('class="hidden border-b border-slate-300 pb-3 print:!block"', $dailyDetail->getContent());
        $this->assertStringContainsString('print:!inline-flex', $dailyDetail->getContent());

        Presensi::where('siswa_id', $student->id)->whereDate('tanggal', $today)->update([
            'status' => 'izin',
            'status_validasi' => 'pending',
            'keterangan' => 'Diajukan oleh wali siswa',
        ]);

        $validation = $this->get(route('wali.presensi.validasi-izin'));
        $validation->assertOk()->assertSee('Siswa Audit')->assertSee('Ya, tolak');
        $this->assertCleanflow($validation->getContent());
        $this->assertSame(1, substr_count($validation->getContent(), 'x-ref="rejectDialog"'));

        $history = $this->get(route('wali.presensi.riwayat'));
        $history->assertOk()->assertSee('Siswa Audit')->assertSee('Edit presensi');
        $this->assertCleanflow($history->getContent());
        $this->assertSame(1, substr_count($history->getContent(), 'x-ref="editDialog"'));
        $this->assertStringContainsString('name="_method" value="PUT"', $history->getContent());
        $this->assertStringNotContainsString("@method('PUT')", $history->getContent());
        $this->assertStringNotContainsString('name="status_validasi"', $history->getContent());

        $presensi = Presensi::where('siswa_id', $student->id)->whereDate('tanggal', $today)->firstOrFail();
        $originalInputBy = $presensi->diinput_oleh;
        $this->put(route('wali.presensi.riwayat.update', $presensi->id), [
            'status' => 'izin',
            'keterangan' => 'Catatan diperjelas',
        ])->assertRedirect();
        $this->assertDatabaseHas('presensi', [
            'id' => $presensi->id,
            'status' => 'izin',
            'status_validasi' => 'pending',
            'keterangan' => 'Catatan diperjelas',
            'diinput_oleh' => $originalInputBy,
        ]);
        $this->putJson(route('wali.presensi.riwayat.update', $presensi->id), [
            'status' => 'alpha',
            'keterangan' => 'Tidak boleh berubah sebelum validasi',
        ])->assertUnprocessable()->assertJsonValidationErrors('status');
        $this->putJson(route('wali.presensi.riwayat.update', $presensi->id), [
            'status' => 'izin',
            'status_validasi' => 'disetujui',
        ])->assertUnprocessable()->assertJsonValidationErrors('status_validasi');
        $this->assertDatabaseHas('presensi', ['id' => $presensi->id, 'status_validasi' => 'pending']);

        $this->post(route('wali.presensi.proses-validasi-izin', $presensi->id), ['status' => 'setuju'])->assertRedirect();
        $this->assertDatabaseHas('presensi', ['id' => $presensi->id, 'status' => 'izin', 'status_validasi' => 'disetujui']);
        $this->postJson(route('wali.presensi.proses-validasi-izin', $presensi->id), ['status' => 'tolak'])
            ->assertUnprocessable()->assertJsonValidationErrors('status');

        $mapel = MataPelajaran::create(['kode_mapel' => 'WLI-MTK', 'nama_mapel' => 'Matematika Wali', 'jenjang' => 'SMP']);
        GuruPengajarKelas::create(['tenaga_pendidik_id' => $tenaga->id, 'kelas_id' => $kelas[0]->id, 'mata_pelajaran_id' => $mapel->id]);
        $nilaiIndex = $this->get(route('wali.nilai.index', ['semester' => 'ganjil', 'mata_pelajaran_id' => $mapel->id]));
        $nilaiIndex->assertOk()->assertSee('Matematika Wali')->assertSee('Siswa Audit');
        $this->assertCleanflow($nilaiIndex->getContent());
        $detailNilai = $this->get(route('wali.nilai.show', ['siswa' => $student->id, 'semester' => 'ganjil']));
        $detailNilai->assertOk()->assertSee('Matematika Wali')->assertSee('Lanjut ke rapor');
        $this->assertCleanflow($detailNilai->getContent());
        $editNilai = $this->get(route('wali.nilai.edit', ['siswa' => $student->id, 'semester' => 'ganjil']));
        $editNilai->assertOk()->assertSee('Matematika Wali')->assertSee('Bandingkan nilai guru');
        $this->assertCleanflow($editNilai->getContent());
        $this->assertSame(1, substr_count($editNilai->getContent(), 'name="nilai['.$mapel->id.'][tugas_1]"'));
        $this->assertStringContainsString('name="_method" value="PUT"', $editNilai->getContent());

        $this->putJson(route('wali.nilai.update', $student->id), [
            'semester' => 'ganjil',
            'nilai' => [$mapel->id => ['mata_pelajaran_id' => $mapel->id + 999, 'pts' => 80]],
        ])->assertUnprocessable()->assertJsonValidationErrors("nilai.{$mapel->id}.mata_pelajaran_id");
        $foreignMapel = MataPelajaran::create(['kode_mapel' => 'WLI-FOREIGN', 'nama_mapel' => 'Mapel luar kelas', 'jenjang' => 'SMP']);
        $this->putJson(route('wali.nilai.update', $student->id), [
            'semester' => 'ganjil',
            'nilai' => [$foreignMapel->id => ['mata_pelajaran_id' => $foreignMapel->id, 'pts' => 80]],
        ])->assertUnprocessable()->assertJsonValidationErrors("nilai.{$foreignMapel->id}.mata_pelajaran_id");
        $this->put(route('wali.nilai.update', $student->id), [
            'semester' => 'ganjil',
            'nilai' => [$mapel->id => ['mata_pelajaran_id' => $mapel->id, 'tugas_1' => 0, 'pts' => 0]],
        ])->assertRedirect(route('wali.nilai.show', ['siswa' => $student->id, 'semester' => 'ganjil']));
        $nilai = Nilai::where('siswa_id', $student->id)->where('mata_pelajaran_id', $mapel->id)->firstOrFail();
        $this->assertSame(0.0, (float) $nilai->fresh()->nilai_akhir);
        $this->assertSame(0.0, (float) $nilai->fresh()->rata_tugas);
        $this->put(route('wali.nilai.update', $student->id), [
            'semester' => 'ganjil',
            'nilai' => [$mapel->id => ['mata_pelajaran_id' => $mapel->id, 'tugas_1' => '', 'pts' => '']],
        ])->assertRedirect(route('wali.nilai.show', ['siswa' => $student->id, 'semester' => 'ganjil']));
        $this->assertNull($nilai->fresh()->rata_tugas);
        $this->assertNull($nilai->fresh()->nilai_akhir);

        foreach ([
            'wali.kenaikan-kelas.prediction',
            'wali.rapor-pending',
            'wali.validasi-akses.index',
            'wali.rapor.request-download.index',
            'wali.rapor.index',
            'wali.template-capaian.index',
            'wali.arsip.index',
        ] as $routeName) {
            $page = $this->get(route($routeName));
            $page->assertOk();
            $this->assertCleanflow($page->getContent());
        }
        foreach (['wali.arsip.show', 'wali.arsip.rapor', 'wali.arsip.presensi', 'wali.arsip.nilai'] as $routeName) {
            $page = $this->get(route($routeName, $kelas[0]->id));
            $page->assertOk()->assertSee('7A');
            $this->assertCleanflow($page->getContent());
        }
        $filteredRapor = $this->get(route('wali.rapor.index', [
            'semester' => 'ganjil', 'jenis_rapor' => 'akhir_semester', 'siswa_id' => $student->id,
        ]));
        $filteredRapor->assertOk()->assertSee('Siswa Audit')->assertSee('Generate')->assertSee('Upload PDF');
        $this->assertCleanflow($filteredRapor->getContent());
        $this->assertStringContainsString('col-start-2 row-span-3 row-start-1', $filteredRapor->getContent());
        foreach ([
            route('wali.nilai.print', ['semester' => 'ganjil']),
            route('wali.nilai.print', ['semester' => 'ganjil', 'mata_pelajaran_id' => $mapel->id]),
            route('wali.nilai.print-siswa', ['siswa' => $student->id, 'semester' => 'ganjil']),
            route('wali.nilai.print-siswa', ['siswa' => $student->id, 'semester' => 'genap']),
        ] as $printUrl) {
            $this->get($printUrl)->assertOk()->assertDontSee('id="admin-sidebar"', false)->assertDontSee('bootstrap-icons');
        }
        $studentPrint = $this->get(route('wali.nilai.print-siswa', ['siswa' => $student->id, 'semester' => 'ganjil']));
        $studentPrint->assertSee('Semester Ganjil')->assertSee('data-print-toolbar')
            // Build: wali-nilai-print-<hash>.css; dev server Vite: resources/css/wali-nilai-print.css.
            ->assertSee('wali-nilai-print');
        $studentPrint->assertDontSee('css/wali-kelas/nilai/print.css');
        $this->get(route('wali.nilai.print-siswa', ['siswa' => $student->id, 'semester' => 'genap']))
            ->assertSee('Semester Genap')->assertSee('data-print-toolbar');

        $rapor = Rapor::create([
            'siswa_id' => $student->id,
            'kelas_id' => $kelas[0]->id,
            'tahun_ajaran_id' => $tahun->id,
            'semester' => 'ganjil',
            'jenis_rapor' => 'akhir_semester',
            'status' => 'draft',
        ]);
        $otherRapor = Rapor::create([
            'siswa_id' => $student->id,
            'kelas_id' => $kelas[0]->id,
            'tahun_ajaran_id' => $tahun->id,
            'semester' => 'genap',
            'jenis_rapor' => 'tengah_semester',
            'status' => 'draft',
        ]);
        $raporNilai = RaporNilai::create(['rapor_id' => $rapor->id, 'mata_pelajaran_id' => $mapel->id, 'nilai_id' => $nilai->id, 'nilai_angka' => 0, 'nilai_huruf' => 'E']);
        $otherRaporNilai = RaporNilai::create(['rapor_id' => $otherRapor->id, 'mata_pelajaran_id' => $mapel->id, 'nilai_id' => $nilai->id, 'nilai_angka' => 0, 'nilai_huruf' => 'E']);
        $raporList = $this->get(route('wali.rapor.index', ['semester' => 'ganjil', 'jenis_rapor' => 'akhir_semester', 'siswa_id' => $student->id]));
        $raporList->assertOk()->assertSee('Aksi')->assertSee('Kirim ke Ketua')->assertSee('Pratinjau');
        $this->assertStringContainsString('absolute right-0 top-full z-30', $raporList->getContent());
        $this->assertStringContainsString('group-open:rotate-180', $raporList->getContent());
        $this->assertStringContainsString('d="m4 6 4 4 4-4"', $raporList->getContent());
        $this->assertStringNotContainsString('⌄</span>', $raporList->getContent());
        $this->assertCleanflow($raporList->getContent());
        foreach ([$rapor, $otherRapor] as $reportToPrint) {
            $this->get(route('wali.rapor.preview', $reportToPrint->id))
                ->assertOk()
                ->assertSee('with-watermark')
                ->assertSee('rapor-document') // build: rapor-document-<hash>.css; dev: rapor-document.css
                ->assertDontSee('bootstrap-icons')
                ->assertDontSee('id="admin-sidebar"', false);
            $this->get(route('wali.rapor.print', $reportToPrint->id))
                ->assertOk()
                ->assertSee('rapor-document') // build: rapor-document-<hash>.css; dev: rapor-document.css
                ->assertDontSee('id="admin-sidebar"', false);
        }
        $editRapor = $this->get(route('wali.rapor.edit', $rapor->id));
        $editRapor->assertOk()->assertSee('Matematika Wali')->assertSee('Simpan perubahan');
        $this->assertCleanflow($editRapor->getContent());
        $this->assertSame(1, substr_count($editRapor->getContent(), 'name="deskripsi['.$raporNilai->id.']"'));
        $this->assertStringContainsString('sm:col-span-2 lg:col-span-1', $editRapor->getContent());
        $this->assertStringContainsString('flex min-h-10 items-center gap-2 rounded-lg border border-slate-200 p-2 text-xs text-slate-700', $editRapor->getContent());
        $this->putJson(route('wali.rapor.update', $rapor->id), [
            'jumlah_sakit' => 0, 'jumlah_izin' => 0, 'jumlah_alpha' => 0,
            'deskripsi' => [$otherRaporNilai->id => 'Tidak boleh berubah'],
        ])->assertUnprocessable()->assertJsonValidationErrors('deskripsi.'.$otherRaporNilai->id);
        $this->assertNull($otherRaporNilai->fresh()->deskripsi);
        RaporKegiatanEkstra::create(['rapor_id' => $rapor->id, 'kegiatan_nama' => 'Musik']);
        $this->put(route('wali.rapor.update', $rapor->id), [
            'jumlah_sakit' => 0, 'jumlah_izin' => 0, 'jumlah_alpha' => 0,
            'deskripsi' => [$raporNilai->id => 'Capaian uji'],
            'kegiatan_ekstra_present' => 1,
        ])->assertRedirect();
        $this->assertSame('Capaian uji', $raporNilai->fresh()->deskripsi);
        $this->assertDatabaseMissing('rapor_kegiatan_ekstra', ['rapor_id' => $rapor->id]);

        $print = $this->get(route('wali.jadwal.print'));
        $print->assertOk()->assertSee('Pratinjau dokumen')->assertSee('Jadwal Pelajaran')->assertSee('Wali Uji');
        $print->assertDontSee('id="admin-sidebar"', false);
        $this->assertCleanflow($print->getContent());

        foreach ([
            resource_path('views/wali-kelas/jadwal-pelajaran.blade.php'),
            resource_path('css/wali-kelas/dashboard.css'),
            resource_path('js/wali-kelas/dashboard.js'),
            resource_path('css/wali-kelas/pilih-kelas/index.css'),
            resource_path('js/wali-kelas/pilih-kelas/index.js'),
            resource_path('css/wali-kelas/jadwal/index.css'),
            resource_path('js/wali-kelas/jadwal/index.js'),
            resource_path('css/wali-kelas/jadwal-pelajaran.css'),
            resource_path('js/wali-kelas/jadwal-pelajaran.js'),
            public_path('css/wali-kelas/jadwal/print.css'),
            public_path('js/wali-kelas/jadwal/print.js'),
            resource_path('css/wali-kelas/presensi/index.css'),
            resource_path('css/wali-kelas/presensi/rekap-harian.css'),
            resource_path('css/wali-kelas/presensi/riwayat.css'),
            resource_path('css/wali-kelas/presensi/show-harian.css'),
            resource_path('css/wali-kelas/presensi/validasi-izin.css'),
            resource_path('js/wali-kelas/presensi/index.js'),
            resource_path('js/wali-kelas/presensi/rekap-harian.js'),
            resource_path('js/wali-kelas/presensi/riwayat.js'),
            resource_path('js/wali-kelas/presensi/show-harian.js'),
            resource_path('js/wali-kelas/presensi/validasi-izin.js'),
            public_path('css/wali-kelas/presensi/print-rekap.css'),
            public_path('js/wali-kelas/presensi/print-rekap.js'),
            public_path('css/wali-kelas/nilai/print.css'),
            resource_path('views/wali-kelas/rapor/print.blade.php'),
            public_path('css/wali-kelas/rapor/print.css'),
            public_path('js/wali-kelas/rapor/print.js'),
        ] as $retiredAsset) {
            $this->assertFalse(File::exists($retiredAsset), $retiredAsset.' masih ada.');
        }
    }

    private function assertCleanflow(string $html): void
    {
        foreach (['data-bs-toggle', 'class="card ', 'class="btn ', 'class="row ', 'class="container-fluid', '/css/wali-kelas/', '/js/wali-kelas/'] as $legacyMarker) {
            $this->assertStringNotContainsString($legacyMarker, $html);
        }
    }
}
