<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\ForumDiskusi;
use App\Models\ForumReply;
use App\Models\Siswa;
use App\Models\SoalUjian;
use App\Models\Ujian;
use App\Models\UjianSiswa;
use App\Models\TugasSiswa;
use App\Models\GuruPengajarKelas;
use App\Models\Kelas;
use App\Models\LmsMeeting;
use App\Models\Materi;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class GuruLmsCleanFlowUiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected TenagaPendidik $guru;

    protected Kelas $kelas;

    protected Kelas $kelasLain;

    protected MataPelajaran $mapel;

    protected User $waliUser;

    protected function setUp(): void
    {
        parent::setUp();

        $cabang = Cabang::create(['kode_cabang' => 'TST', 'nama_cabang' => 'Cabang Pengujian', 'alamat' => 'Alamat', 'is_active' => true]);
        $tahun = TahunAjaran::create(['nama_tahun_ajaran' => '2026/2027', 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2027-06-30', 'is_active' => true]);
        $this->kelas = Kelas::create(['cabang_id' => $cabang->id, 'tahun_ajaran_id' => $tahun->id, 'nama_kelas' => '7A', 'jenjang' => 'SMP', 'kode_kelas' => 'TST-7A', 'kuota_siswa' => 30]);
        $this->kelasLain = Kelas::create(['cabang_id' => $cabang->id, 'tahun_ajaran_id' => $tahun->id, 'nama_kelas' => '7B', 'jenjang' => 'SMP', 'kode_kelas' => 'TST-7B', 'kuota_siswa' => 30]);
        $this->mapel = MataPelajaran::create(['kode_mapel' => 'SMP-001', 'nama_mapel' => 'Matematika', 'jenjang' => 'SMP']);

        $this->user = User::factory()->create(['role' => 'guru_pengajar', 'is_active' => true]);
        $this->guru = TenagaPendidik::create(['user_id' => $this->user->id, 'nip' => 'GP-001', 'nama_lengkap' => 'Guru Pengujian', 'jenis_kelamin' => 'L', 'email' => $this->user->email]);
        foreach ([$this->kelas, $this->kelasLain] as $kelas) {
            GuruPengajarKelas::create(['tenaga_pendidik_id' => $this->guru->id, 'kelas_id' => $kelas->id, 'mata_pelajaran_id' => $this->mapel->id]);
        }

        $this->waliUser = User::factory()->create(['role' => 'wali_kelas', 'is_active' => true]);
        $wali = TenagaPendidik::create(['user_id' => $this->waliUser->id, 'nama_lengkap' => 'Wali Pengujian', 'jenis_kelamin' => 'P']);
        \App\Models\WaliKelasAssignment::create(['tenaga_pendidik_id' => $wali->id, 'kelas_id' => $this->kelas->id, 'assigned_at' => now()]);
    }

    protected function args(...$extra): array
    {
        return [$this->kelas->id, $this->mapel->id, ...$extra];
    }

    protected function assertCleanLmsPage($response, bool $editorModule = false): void
    {
        $response->assertOk()
            ->assertSee('HOK Teaching')
            ->assertSee('bg-indigo-600', false)
            ->assertDontSee('data-bs-', false)
            ->assertDontSee('form-control', false)
            ->assertDontSee('form-select', false)
            ->assertDontSee('btn btn-', false)
            ->assertDontSee('class="card', false)
            ->assertDontSee('class="row', false)
            ->assertDontSee('modal fade', false)
            ->assertDontSee('resources/css/guru', false)
            ->assertDontSee('nav-section-title', false);

        if (! $editorModule) {
            $response->assertDontSee('resources/js/guru', false);
        }
    }

    public function test_lms_shell_uses_its_own_light_theme_and_sidebar(): void
    {
        $html = $this->actingAs($this->user)->get(route('guru.lms.dashboard', $this->args()))->getContent();

        $this->assertStringContainsString('Anda mengajar', $html);
        $this->assertStringContainsString('border-r border-slate-200 bg-white', $html);
        $this->assertStringNotContainsString('from-[#245f91]', $html);
        $this->assertStringContainsString('aria-current="page"', $html);
        $this->assertStringContainsString(route('guru.kelas.mapel', $this->kelas->id), $html);
    }

    public function test_dashboard_materi_tugas_and_meeting_pages_render_clean(): void
    {
        $materi = Materi::create(['kelas_id' => $this->kelas->id, 'mata_pelajaran_id' => $this->mapel->id, 'guru_id' => $this->guru->id, 'judul_materi' => 'Materi Aljabar', 'kategori' => 'materi', 'deskripsi' => 'Pengantar', 'url_materi' => 'https://example.test', 'tipe_file' => 'link', 'tanggal_upload' => now()->toDateString()]);
        $tugas = Tugas::create(['kelas_id' => $this->kelas->id, 'mata_pelajaran_id' => $this->mapel->id, 'guru_id' => $this->guru->id, 'jenis_tugas' => 'tugas', 'judul_tugas' => 'Tugas Bab 1', 'deskripsi' => 'Kerjakan', 'tanggal_mulai' => now()->toDateString(), 'tanggal_deadline' => now()->addWeek()->toDateString(), 'bisa_diulang' => true, 'batas_pengulangan' => null]);
        $meeting = LmsMeeting::create(['kelas_id' => $this->kelas->id, 'mata_pelajaran_id' => $this->mapel->id, 'guru_id' => $this->guru->id, 'judul' => 'Sesi Daring', 'platform' => 'google_meet', 'link_meeting' => 'https://meet.example.test/abc', 'waktu_mulai' => now()->addDay(), 'is_active' => true]);

        $this->actingAs($this->user);

        foreach ([
            route('guru.lms.dashboard', $this->args()),
            route('guru.lms.materi.index', $this->args()),
            route('guru.lms.materi.create', $this->args()),
            route('guru.lms.materi.edit', $this->args($materi->id)),
            route('guru.lms.tugas.index', $this->args()),
            route('guru.lms.tugas.create', $this->args()),
            route('guru.lms.tugas.edit', $this->args($tugas->id)),
            route('guru.lms.meeting.index', $this->args()),
            route('guru.lms.meeting.create', $this->args()),
            route('guru.lms.meeting.edit', $this->args($meeting->id)),
        ] as $url) {
            $this->assertCleanLmsPage($this->get($url));
        }

        $this->get(route('guru.lms.materi.index', $this->args()))
            ->assertSee('Materi Aljabar')->assertSee('name="hapus_terkait"', false)->assertSee('data-confirmed="true"', false);
        $this->get(route('guru.lms.materi.edit', $this->args($materi->id)))
            ->assertViewIs('guru.lms.materi.form')->assertSee('name="_method" value="PUT"', false)
            ->assertSee('name="kelas_tambahan[]"', false)->assertSee('7B');
        $this->get(route('guru.lms.tugas.edit', $this->args($tugas->id)))
            ->assertViewIs('guru.lms.tugas.form')->assertSee('name="_method" value="PUT"', false)->assertSee('name="batas_pengulangan"', false);
        $this->get(route('guru.lms.tugas.index', $this->args()))
            ->assertSee('Tugas Bab 1')->assertSee(route('guru.lms.tugas.koreksi', $this->args($tugas->id)), false);
        $this->get(route('guru.lms.meeting.index', $this->args()))
            ->assertSee('Sesi Daring')->assertSee('data-link="https://meet.example.test/abc"', false);
        $this->get(route('guru.lms.meeting.edit', $this->args($meeting->id)))
            ->assertViewIs('guru.lms.meeting.form')->assertSee('name="is_active"', false);
    }

    public function test_forum_and_koreksi_pages_render_clean(): void
    {
        $forum = ForumDiskusi::create(['mata_pelajaran_id' => $this->mapel->id, 'kelas_id' => $this->kelas->id, 'user_id' => $this->user->id, 'topik' => 'umum', 'judul' => 'Diskusi Aljabar', 'isi' => 'Apa itu variabel?', 'is_pinned' => true, 'is_closed' => false]);
        $reply = ForumReply::create(['forum_diskusi_id' => $forum->id, 'user_id' => $this->user->id, 'isi' => 'Balasan guru']);
        ForumReply::create(['forum_diskusi_id' => $forum->id, 'user_id' => $this->user->id, 'parent_id' => $reply->id, 'isi' => 'Balasan bersarang']);

        $siswaUser = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
        $siswa = Siswa::create(['user_id' => $siswaUser->id, 'cabang_id' => $this->kelas->cabang_id, 'kelas_id' => $this->kelas->id, 'nisn' => '99887766', 'nama_lengkap' => 'Siswa Koreksi', 'jenis_kelamin' => 'P', 'tempat_lahir' => 'Kota', 'tanggal_lahir' => '2012-01-01', 'alamat' => 'Alamat', 'tanggal_masuk' => '2026-07-01', 'status' => 'aktif']);
        $tugas = Tugas::create(['kelas_id' => $this->kelas->id, 'mata_pelajaran_id' => $this->mapel->id, 'guru_id' => $this->guru->id, 'jenis_tugas' => 'tugas', 'judul_tugas' => 'Tugas Koreksi', 'deskripsi' => 'Kerjakan', 'tanggal_mulai' => now()->toDateString(), 'tanggal_deadline' => now()->addWeek()->toDateString()]);
        $submission = TugasSiswa::create(['tugas_id' => $tugas->id, 'siswa_id' => $siswa->id, 'jawaban_text' => 'Jawaban saya', 'tanggal_submit' => now(), 'status' => 'dikerjakan']);

        $this->actingAs($this->user);

        foreach ([
            route('guru.lms.forum.index', $this->args()),
            route('guru.lms.forum.create', $this->args()),
            route('guru.lms.forum.show', $this->args($forum->id)),
            route('guru.lms.tugas.koreksi', $this->args($tugas->id)),
            route('guru.lms.tugas.koreksi.show', $this->args($tugas->id, $submission->id)),
        ] as $url) {
            $this->assertCleanLmsPage($this->get($url));
        }

        $this->get(route('guru.lms.forum.index', $this->args()))
            ->assertSee('Diskusi Aljabar')->assertSee('Disematkan')
            ->assertSee(route('guru.lms.forum.togglePin', $this->args($forum->id)), false)
            ->assertSee('name="sync_kelas"', false)->assertSee('name="hapus_terkait"', false);
        $this->get(route('guru.lms.forum.show', $this->args($forum->id)))
            ->assertSee('Balasan bersarang')->assertSee('name="parent_id" value="'.$reply->id.'"', false)
            ->assertSee(route('guru.lms.forum.reply.update', $this->args($forum->id, $reply->id)), false)
            ->assertSee('name="_method" value="PUT"', false)->assertSee('sm:ml-8', false);
        $this->get(route('guru.lms.tugas.koreksi', $this->args($tugas->id)))
            ->assertSee('Siswa Koreksi')->assertSee('Perlu koreksi');
        $this->get(route('guru.lms.tugas.koreksi.show', $this->args($tugas->id, $submission->id)))
            ->assertSee('Analisis AI')->assertSee('Jawaban saya')
            ->assertSee(route('guru.lms.tugas.koreksi.ai-suggest', $this->args($tugas->id, $submission->id)), false);
    }

    public function test_ujian_latihan_soal_koreksi_pengawasan_and_nilai_pages_render_clean(): void
    {
        $siswaUser = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
        $siswa = Siswa::create(['user_id' => $siswaUser->id, 'cabang_id' => $this->kelas->cabang_id, 'kelas_id' => $this->kelas->id, 'nisn' => '11223344', 'nama_lengkap' => 'Siswa Ujian', 'jenis_kelamin' => 'L', 'tempat_lahir' => 'Kota', 'tanggal_lahir' => '2012-01-01', 'alamat' => 'Alamat', 'tanggal_masuk' => '2026-07-01', 'status' => 'aktif']);

        $base = ['kelas_id' => $this->kelas->id, 'mata_pelajaran_id' => $this->mapel->id, 'guru_id' => $this->guru->id, 'deskripsi' => 'desc', 'tanggal_mulai' => now(), 'tanggal_selesai' => now()->addDays(7), 'durasi_menit' => 60, 'is_active' => false, 'tampilkan_nilai' => true, 'bisa_diulang' => false, 'batas_pengulangan' => 1, 'tampilkan_riwayat' => false];
        $ujian = Ujian::create($base + ['judul_ujian' => 'UH Aljabar', 'tipe_ujian' => Ujian::TIPE_ULANGAN_HARIAN]);
        $latihan = Ujian::create($base + ['judul_ujian' => 'Latihan Aljabar', 'tipe_ujian' => Ujian::TIPE_LATIHAN]);

        $pg = SoalUjian::create(['ujian_id' => $ujian->id, 'urutan' => 1, 'pertanyaan' => 'Berapa 2+2?', 'tipe_soal' => 'pilihan_ganda', 'jumlah_pilihan' => 4, 'pilihan_jawaban' => ['A' => '3', 'B' => '4', 'C' => '5', 'D' => '6'], 'kunci_jawaban' => 'B', 'bobot_nilai' => 10]);
        SoalUjian::create(['ujian_id' => $ujian->id, 'urutan' => 2, 'pertanyaan' => 'Jelaskan variabel.', 'tipe_soal' => 'uraian', 'bobot_nilai' => 20]);
        $hasil = UjianSiswa::create(['ujian_id' => $ujian->id, 'siswa_id' => $siswa->id, 'status' => 'selesai', 'nilai' => 50, 'waktu_mulai' => now()->subHour(), 'waktu_selesai' => now()]);

        $this->actingAs($this->user);

        foreach ([
            route('guru.lms.ujian.index', $this->args()),
            route('guru.lms.latihan.index', $this->args()),
            route('guru.lms.ujian.create', $this->args()),
            route('guru.lms.latihan.create', $this->args()),
            route('guru.lms.ujian.edit', $this->args($ujian->id)),
            route('guru.lms.latihan.edit', $this->args($latihan->id)),
            route('guru.lms.ujian.soal.index', $this->args($ujian->id)),
            route('guru.lms.ujian.soal.create', $this->args($ujian->id)),
            route('guru.lms.ujian.soal.edit', $this->args($ujian->id, $pg->id)),
            route('guru.lms.ujian.hasil', $this->args($ujian->id)),
            route('guru.lms.latihan.hasil', $this->args($latihan->id)),
            route('guru.lms.ujian.koreksi.show', $this->args($ujian->id, $hasil->id)),
            route('guru.lms.ujian.pengawasan', $this->args($ujian->id)),
            route('guru.lms.ujian.soal.manage', $this->args($ujian->id)),
            route('guru.lms.latihan.soal.manage', $this->args($latihan->id)),
            route('guru.lms.nilai.index', $this->args()),
        ] as $url) {
            $this->assertCleanLmsPage($this->get($url), str_contains($url, 'manage-soal'));
        }

        $this->get(route('guru.lms.ujian.index', $this->args()))
            ->assertSee('UH Aljabar')->assertSee(route('guru.lms.ujian.pengawasan', $this->args($ujian->id)), false)
            ->assertSee(route('guru.lms.ujian.soal.manage', $this->args($ujian->id)), false);
        $this->get(route('guru.lms.latihan.index', $this->args()))
            ->assertSee('Latihan Aljabar')->assertDontSee('/pengawasan', false);
        $this->get(route('guru.lms.latihan.edit', $this->args($latihan->id)))
            ->assertViewIs('guru.lms.ujian.form')->assertSee('name="tipe_ujian" value="latihan"', false)->assertSee('name="_method" value="PUT"', false);
        $this->get(route('guru.lms.ujian.koreksi.show', $this->args($ujian->id, $hasil->id)))
            ->assertSee('Analisis AI Assistant')->assertSee('name="nilai['.$pg->id.']"', false)->assertSee('/ai-suggest', false);
        $this->get(route('guru.lms.ujian.pengawasan', $this->args($ujian->id)))
            ->assertSee(route('guru.lms.ujian.pengawasan.data', $this->args($ujian->id)), false)->assertSee('x-ref="detailDialog"', false);

        $this->get(route('guru.lms.latihan.soal.manage', $this->args($latihan->id)))
            ->assertSee('id="soalAccordion"', false)->assertSee('id="soalTemplate"', false)
            ->assertSee('id="aiQuestionSidebar"', false)
            ->assertSee('data-generate-url="'.route('guru.lms.latihan.soal.ai-generate', $this->args($latihan->id)).'"', false)
            ->assertSee(route('guru.lms.latihan.soal.storeAll', $this->args($latihan->id)), false)
            ->assertDontSee('ai-question-generator.js', false);
        $this->get(route('guru.lms.ujian.soal.manage', $this->args($ujian->id)))
            ->assertSee('Berapa 2+2?');

        $this->get(route('guru.lms.nilai.index', $this->args()))
            ->assertSee('id="importNilaiDialog"', false);

        $nilai = \App\Models\Nilai::where('siswa_id', $siswa->id)->where('mata_pelajaran_id', $this->mapel->id)->first();
        $this->assertNotNull($nilai, 'Halaman nilai harus menyiapkan baris nilai siswa.');
        $nilai->update(['tugas_1' => 75, 'pts' => 9.8]);
        $html = $this->get(route('guru.lms.nilai.index', $this->args()))->assertOk()->getContent();
        // Satu input bernama per nilai: kartu mobile dan baris desktop berbagi markup yang sama.
        $this->assertSame(1, substr_count($html, 'name="nilai['.$nilai->id.'][tugas_1]"'));
        $this->assertSame(1, substr_count($html, 'name="nilai['.$nilai->id.'][pas]"'));
        $this->assertStringContainsString('value="75"', $html);
        $this->assertStringContainsString('value="9.8"', $html);
        $this->assertStringContainsString('max-lg:hidden', $html);
        $this->assertStringContainsString('Buka semua', $html);
        // Rata-rata & nilai akhir dihitung langsung dari isian (rumus sama dengan Nilai::hitungNilaiAkhir).
        $this->assertStringContainsString('data-grup="tugas"', $html);
        $this->assertStringContainsString('data-field="pts"', $html);
        $this->assertStringContainsString("x-text=\"tampil(rata('uh'))\"", $html);
        $this->assertStringContainsString('x-text="tampil(akhir(), 2)"', $html);
    }

    public function test_nilai_edited_by_wali_shows_guru_snapshot_and_save_keeps_wali_value(): void
    {
        $siswaUser = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
        $siswa = Siswa::create(['user_id' => $siswaUser->id, 'cabang_id' => $this->kelas->cabang_id, 'kelas_id' => $this->kelas->id, 'nisn' => '55667788', 'nama_lengkap' => 'Siswa Snapshot', 'jenis_kelamin' => 'P', 'tempat_lahir' => 'Kota', 'tanggal_lahir' => '2012-01-01', 'alamat' => 'Alamat', 'tanggal_masuk' => '2026-07-01', 'status' => 'aktif']);

        $this->actingAs($this->user);
        $semester = \App\Models\Nilai::getCurrentSemester();
        $this->get(route('guru.lms.nilai.index', [...$this->args(), 'semester' => $semester]))->assertOk();

        $nilai = \App\Models\Nilai::where('siswa_id', $siswa->id)->where('mata_pelajaran_id', $this->mapel->id)->firstOrFail();
        $nilai->update(['tugas_1' => 80, 'tugas_1_guru' => 75, 'pts' => 70, 'pts_guru' => 70, 'wali_terakhir_edit_at' => now()->subHour(), 'guru_terakhir_simpan_at' => now()->subDay()]);

        $html = $this->get(route('guru.lms.nilai.index', [...$this->args(), 'semester' => $semester]))->getContent();
        $this->assertMatchesRegularExpression('/name="nilai\['.$nilai->id.'\]\[tugas_1\]"[^>]*value="75"/', $html);
        $this->assertStringContainsString('Diedit wali', $html);
        $this->assertStringContainsString('Nilai aktif (wali): 80', $html);

        $this->post(route('guru.lms.nilai.updateBatch', $this->args()), [
            'semester' => $semester,
            'nilai' => [$nilai->id => ['id' => $nilai->id, 'tugas_1' => '78', 'pts' => '70']],
        ])->assertRedirect(route('guru.lms.nilai.index', [...$this->args(), 'semester' => $semester]));

        $nilai->refresh();
        $this->assertEquals(78.0, (float) $nilai->tugas_1_guru, 'Simpanan guru masuk ke snapshot.');
        $this->assertEquals(80.0, (float) $nilai->tugas_1, 'Nilai aktif wali tidak tertimpa.');
        $this->assertEquals(70.0, (float) $nilai->pts_guru, 'Kolom yang tidak diubah tidak tertimpa nilai wali.');

        $this->get(route('guru.lms.nilai.index', [...$this->args(), 'semester' => $semester]))
            ->assertSee('value="78"', false);

        // Wali kelas menerima notifikasi dan melihat penanda pada detail nilai siswa.
        $notif = \App\Models\Notification::where('user_id', $this->waliUser->id)->latest('id')->first();
        $this->assertNotNull($notif, 'Wali kelas harus menerima notifikasi perubahan nilai dari guru.');
        $this->assertStringContainsString('Matematika', $notif->judul);
        $this->assertStringContainsString('Siswa Snapshot', $notif->pesan);
        $this->assertStringContainsString('/wali/nilai/'.$siswa->id, $notif->link);
        $this->assertSame(0, \App\Models\Notification::where('user_id', $this->user->id)->where('tipe', 'nilai')->count(), 'Guru tidak menerima notifikasinya sendiri.');

        $this->actingAs($this->waliUser)->withSession(['wali_kelas_selected' => $this->kelas->id]);
        $this->get(route('wali.nilai.show', ['siswa' => $siswa->id, 'semester' => $semester]))
            ->assertOk()
            ->assertSee('Guru mapel mengubah nilai yang sudah Anda revisi')
            ->assertSee('Update guru')
            ->assertSee(route('wali.nilai.edit', ['siswa' => $siswa->id, 'semester' => $semester]), false);
    }

    public function test_guru_save_before_wali_revision_updates_active_value_without_notifying_wali(): void
    {
        $siswaUser = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
        $siswa = Siswa::create(['user_id' => $siswaUser->id, 'cabang_id' => $this->kelas->cabang_id, 'kelas_id' => $this->kelas->id, 'nisn' => '99001122', 'nama_lengkap' => 'Siswa Awal', 'jenis_kelamin' => 'L', 'tempat_lahir' => 'Kota', 'tanggal_lahir' => '2012-01-01', 'alamat' => 'Alamat', 'tanggal_masuk' => '2026-07-01', 'status' => 'aktif']);

        $this->actingAs($this->user);
        $semester = \App\Models\Nilai::getCurrentSemester();
        $this->get(route('guru.lms.nilai.index', [...$this->args(), 'semester' => $semester]))->assertOk();
        $nilai = \App\Models\Nilai::where('siswa_id', $siswa->id)->where('mata_pelajaran_id', $this->mapel->id)->firstOrFail();

        $this->post(route('guru.lms.nilai.updateBatch', $this->args()), [
            'semester' => $semester,
            'nilai' => [$nilai->id => ['id' => $nilai->id, 'tugas_1' => '88']],
        ])->assertRedirect();

        $nilai->refresh();
        $this->assertEquals(88.0, (float) $nilai->tugas_1, 'Sebelum revisi wali, nilai guru langsung menjadi nilai aktif (dipakai siswa & rapor).');
        $this->assertEquals(88.0, (float) $nilai->tugas_1_guru);
        $this->assertSame(0, \App\Models\Notification::where('user_id', $this->waliUser->id)->count());
    }

    public function test_tingkat_akhir_fields_follow_the_same_guru_snapshot_flow(): void
    {
        $kelas9 = Kelas::create(['cabang_id' => $this->kelas->cabang_id, 'tahun_ajaran_id' => $this->kelas->tahun_ajaran_id, 'nama_kelas' => '9A', 'jenjang' => 'SMP', 'kode_kelas' => 'TST-9A', 'kuota_siswa' => 30]);
        $this->assertTrue($kelas9->isTingkatAkhir());
        GuruPengajarKelas::create(['tenaga_pendidik_id' => $this->guru->id, 'kelas_id' => $kelas9->id, 'mata_pelajaran_id' => $this->mapel->id]);
        $siswaUser = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
        $siswa = Siswa::create(['user_id' => $siswaUser->id, 'cabang_id' => $kelas9->cabang_id, 'kelas_id' => $kelas9->id, 'nisn' => '44556677', 'nama_lengkap' => 'Siswa Akhir', 'jenis_kelamin' => 'L', 'tempat_lahir' => 'Kota', 'tanggal_lahir' => '2010-01-01', 'alamat' => 'Alamat', 'tanggal_masuk' => '2026-07-01', 'status' => 'aktif']);

        $args = [$kelas9->id, $this->mapel->id];
        $semester = \App\Models\Nilai::getCurrentSemester();
        $this->actingAs($this->user);
        $this->get(route('guru.lms.nilai.index', [...$args, 'semester' => $semester]))->assertOk()->assertSee('Penilaian tingkat akhir');

        $nilai = \App\Models\Nilai::where('siswa_id', $siswa->id)->where('mata_pelajaran_id', $this->mapel->id)->firstOrFail();
        $nilai->update(['to_1' => 75, 'to_1_guru' => 75, 'upk' => 75, 'upk_guru' => 75, 'wali_terakhir_edit_at' => now()->subHour(), 'guru_terakhir_simpan_at' => now()->subDay()]);

        $this->post(route('guru.lms.nilai.updateBatch', $args), [
            'semester' => $semester,
            'nilai' => [$nilai->id => ['id' => $nilai->id, 'to_1' => '82', 'to_2' => '', 'to_3' => '', 'upk' => '75', 'ujian_praktek' => '']],
        ])->assertRedirect();

        $nilai->refresh();
        $this->assertEquals(82.0, (float) $nilai->to_1_guru, 'Simpanan TO guru masuk ke versi guru.');
        $this->assertEquals(75.0, (float) $nilai->to_1, 'Nilai TO aktif milik wali tidak tertimpa.');

        $html = $this->get(route('guru.lms.nilai.index', [...$args, 'semester' => $semester]))->getContent();
        $this->assertMatchesRegularExpression('/name="nilai\['.$nilai->id.'\]\[to_1\]"[^>]*value="82"/', $html, 'Input TO menampilkan versi guru setelah disimpan.');
        $this->assertStringContainsString('Nilai aktif (wali): 75', $html);
    }

    public function test_forum_create_accepts_image_and_document_attachments(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $this->actingAs($this->user);

        $this->get(route('guru.lms.forum.create', $this->args()))
            ->assertOk()
            ->assertSee('enctype="multipart/form-data"', false)
            ->assertSee('name="lampiran[]"', false)
            ->assertSee('multiple', false);

        $this->post(route('guru.lms.forum.store', $this->args()), [
            'judul' => 'Diskusi Berlampiran',
            'isi' => 'Lihat gambar dan dokumen ini.',
            'lampiran' => [
                \Illuminate\Http\UploadedFile::fake()->image('grafik.png'),
                \Illuminate\Http\UploadedFile::fake()->create('ringkasan.pdf', 120, 'application/pdf'),
            ],
        ])->assertRedirect(route('guru.lms.forum.index', $this->args()));

        $forum = ForumDiskusi::where('judul', 'Diskusi Berlampiran')->firstOrFail();
        $this->assertCount(2, $forum->lampiran);
        foreach ($forum->lampiran as $path) {
            \Illuminate\Support\Facades\Storage::disk('public')->assertExists($path);
        }

        // PDF/dokumen tampil sebagai kartu file (tanpa iframe yang memicu unduhan otomatis); gambar tetap dipratinjau.
        $html = $this->get(route('guru.lms.forum.show', $this->args($forum->id)))->assertOk()->getContent();
        $this->assertStringNotContainsString('<iframe', $html);
        $this->assertStringContainsString('Dokumen PDF', $html);
        $this->assertStringContainsString('alt="Lampiran gambar"', $html);
        $this->assertStringContainsString('title="Buka di tab baru"', $html);

        // Tombol Buka PDF memakai /view-document/{token} (tanpa ekstensi .pdf) dan benar-benar menyajikan PDF inline.
        $this->assertMatchesRegularExpression('#href="[^"]*/view-document/[A-Za-z0-9]+"#', $html);
        $this->assertStringNotContainsString('fetch($el.href', $html);
        preg_match('#href="([^"]*/view-document/[A-Za-z0-9]+)"#', $html, $cocok);
        $pdfPath = collect($forum->lampiran)->first(fn ($p) => str_ends_with($p, '.pdf'));
        $nyata = storage_path('app/public/'.$pdfPath);
        \Illuminate\Support\Facades\File::ensureDirectoryExists(dirname($nyata));
        file_put_contents($nyata, "%PDF-1.4
%dummy
");
        try {
            $this->get(parse_url($cocok[1], PHP_URL_PATH))->assertOk()->assertHeader('content-type', 'application/pdf');
        } finally {
            @unlink($nyata);
        }
    }

    public function test_migrated_lms_views_have_no_page_assets_or_inline_styles(): void
    {
        $paths = collect(File::allFiles(resource_path('views/guru')))->map->getPathname()
            ->push(resource_path('views/components/ai-sidebar.blade.php'))
            ->push(resource_path('views/layouts/partials/cleanflow-lms-shell.blade.php'));

        foreach ($paths as $path) {
            $source = File::get($path);

            $this->assertStringNotContainsString('<style', $source, $path);
            $this->assertStringNotContainsString('style="', $source, $path);
            if (! str_ends_with($path, 'manage_soal.blade.php') && ! str_ends_with($path, 'cleanflow-lms-shell.blade.php')) {
                $this->assertStringNotContainsString('@vite', $source, $path);
            }
            $this->assertStringNotContainsString('data-bs-', $source, $path);
            $this->assertStringNotContainsString('btn btn-', $source, $path);
            $this->assertStringNotContainsString('form-control', $source, $path);
            $this->assertStringNotContainsString('class="row', $source, $path);
        }

        // Satu-satunya aset JS halaman Guru yang tersisa adalah modul editor soal (dipakai AI Question Generator).
        $this->assertSame(['manage-soal.js'], collect(File::allFiles(resource_path('js/guru')))->map->getFilename()->values()->all());
        $this->assertDirectoryDoesNotExist(resource_path('css/guru'));
        $this->assertFileDoesNotExist(resource_path('css/layouts/lms-guru.css'));
        $this->assertFileDoesNotExist(public_path('js/ai-question-generator.js'));
    }
}
