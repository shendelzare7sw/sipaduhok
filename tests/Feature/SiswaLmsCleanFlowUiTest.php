<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\Cabang;
use App\Models\ForumDiskusi;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\LmsMeeting;
use App\Models\MataPelajaran;
use App\Models\Materi;
use App\Models\Pengumuman;
use App\Models\Siswa;
use App\Models\SoalUjian;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\Tugas;
use App\Models\Ujian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Kontrak CleanFlow LMS Siswa: seluruh halaman LMS, layar mulai/hasil/pembahasan,
 * serta mode fokus Ujian dan lembar kerja Latihan memakai Tailwind + Alpine tanpa
 * Bootstrap maupun aset CSS/JS halaman.
 */
class SiswaLmsCleanFlowUiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Siswa $siswa;

    private Kelas $kelas;

    private MataPelajaran $mapel;

    private TenagaPendidik $guru;

    protected function setUp(): void
    {
        parent::setUp();

        AppSetting::updateOrCreate(['key' => 'lms_allowed_jenjang'], ['value' => json_encode(['SMP', 'SMA'])]);

        $cabang = Cabang::create(['kode_cabang' => 'TST', 'nama_cabang' => 'Cabang Pengujian', 'alamat' => 'Alamat', 'is_active' => true]);
        $tahunAjaran = TahunAjaran::create(['nama_tahun_ajaran' => '2026/2027', 'tanggal_mulai' => '2026-07-01', 'tanggal_selesai' => '2027-06-30', 'is_active' => true]);
        $this->kelas = Kelas::create(['cabang_id' => $cabang->id, 'tahun_ajaran_id' => $tahunAjaran->id, 'nama_kelas' => '8A', 'jenjang' => 'SMP', 'kode_kelas' => 'TST-SMP-8A-2026', 'kuota_siswa' => 30]);

        $this->user = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
        $this->siswa = Siswa::create([
            'user_id' => $this->user->id, 'cabang_id' => $cabang->id, 'kelas_id' => $this->kelas->id,
            'nisn' => '1234567890', 'nis' => '2026001', 'nama_lengkap' => 'Siswa Pengujian', 'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Kota', 'tanggal_lahir' => '2012-01-01', 'alamat' => 'Alamat', 'tanggal_masuk' => '2026-07-01', 'status' => 'aktif',
        ]);

        $guruUser = User::factory()->create(['role' => 'guru_pengajar', 'name' => 'Guru Pengujian']);
        $this->guru = TenagaPendidik::create(['user_id' => $guruUser->id, 'nip' => 'GP-001', 'nama_lengkap' => 'Guru Pengujian', 'jenis_kelamin' => 'L', 'email' => 'guru@test.local']);
        $this->mapel = MataPelajaran::create(['kode_mapel' => 'SMP-IPA', 'nama_mapel' => 'IPA Terpadu', 'jenjang' => 'SMP']);

        $jadwal = JadwalPelajaran::create([
            'guru_id' => $this->guru->id, 'kelas_id' => $this->kelas->id, 'mata_pelajaran_id' => $this->mapel->id,
            'hari' => 'Senin', 'jam_mulai' => '08:00', 'jam_selesai' => '09:00', 'status' => 'aktif', 'tahun_ajaran_id' => $tahunAjaran->id,
        ]);
        $jadwal->kelas()->syncWithoutDetaching([$this->kelas->id]);
    }

    private function buatUjian(string $tipe, string $judul): Ujian
    {
        $ujian = Ujian::create([
            'kelas_id' => $this->kelas->id, 'mata_pelajaran_id' => $this->mapel->id, 'guru_id' => $this->guru->id,
            'judul_ujian' => $judul, 'deskripsi' => 'Deskripsi ' . $judul, 'tipe_ujian' => $tipe, 'urutan' => 1,
            'tanggal_mulai' => now()->subHour(), 'tanggal_selesai' => now()->addDay(), 'durasi_menit' => $tipe === 'latihan' ? 0 : 60,
            'is_active' => true, 'tampilkan_nilai' => true, 'bisa_diulang' => true, 'batas_pengulangan' => 2, 'tampilkan_riwayat' => true,
        ]);

        $soal = [
            ['tipe_soal' => 'pilihan_ganda', 'pertanyaan' => 'Planet terdekat dari matahari?', 'narasi' => 'Bacaan tata surya.',
                'pilihan_jawaban' => ['A' => 'Merkurius', 'B' => 'Venus', 'C' => 'Bumi', 'D' => 'Mars'], 'jawaban_benar' => 'A'],
            ['tipe_soal' => 'pilihan_ganda_kompleks', 'pertanyaan' => 'Planet berbatu?',
                'pilihan_jawaban' => ['A' => 'Merkurius', 'B' => 'Jupiter', 'C' => 'Mars', 'jawaban_benar' => ['A', 'C']]],
            ['tipe_soal' => 'benar_salah', 'pertanyaan' => 'Tentukan benar/salah.',
                'pilihan_jawaban' => ['pernyataan' => [['text' => 'Matahari adalah bintang', 'benar' => true], ['text' => 'Bulan adalah planet', 'benar' => false]]]],
            ['tipe_soal' => 'isian_singkat', 'pertanyaan' => 'Satelit alami bumi adalah ...', 'pilihan_jawaban' => ['jawaban_benar' => ['bulan']]],
            ['tipe_soal' => 'uraian', 'pertanyaan' => 'Jelaskan proses evaporasi.', 'jawaban_benar' => 'Penguapan air.'],
        ];
        foreach ($soal as $i => $data) {
            SoalUjian::create(array_merge(['ujian_id' => $ujian->id, 'urutan' => $i + 1, 'bobot_nilai' => 10], $data));
        }

        return $ujian->fresh('soalUjian');
    }

    private function assertTanpaLegacy($response): void
    {
        $response->assertOk()
            ->assertDontSee('data-bs-', false)
            ->assertDontSee('btn btn-', false)
            ->assertDontSee('form-control', false)
            ->assertDontSee('class="row', false)
            ->assertDontSee('resources/css/siswa', false)
            ->assertDontSee('resources/js/siswa', false)
            ->assertDontSee('resources/css/layouts/lms', false)
            ->assertDontSee('bootstrap.min.css', false);
    }

    public function test_lms_pages_render_on_tailwind_without_legacy_markup(): void
    {
        $tugas = Tugas::create([
            'kelas_id' => $this->kelas->id, 'mata_pelajaran_id' => $this->mapel->id, 'guru_id' => $this->guru->id, 'jenis_tugas' => 'tugas',
            'urutan' => 1, 'judul_tugas' => 'Laporan Pengamatan', 'deskripsi' => 'Amati cuaca.', 'tampilkan_nilai' => true,
            'bisa_diulang' => true, 'batas_pengulangan' => 2, 'tanggal_mulai' => now()->subDay(), 'tanggal_deadline' => now()->addDays(3),
        ]);
        $materi = Materi::create([
            'kelas_id' => $this->kelas->id, 'mata_pelajaran_id' => $this->mapel->id, 'guru_id' => $this->guru->id,
            'judul_materi' => 'Tata Surya', 'deskripsi' => 'Materi tata surya.', 'tipe_file' => 'link', 'file_materi' => 'https://example.test/materi', 'tanggal_upload' => now(),
        ]);
        $forum = ForumDiskusi::create([
            'mata_pelajaran_id' => $this->mapel->id, 'kelas_id' => $this->kelas->id, 'user_id' => $this->guru->user_id,
            'topik' => 'umum', 'judul' => 'Diskusi Hujan', 'isi' => 'Mengapa hujan turun?', 'is_pinned' => true, 'is_closed' => false,
        ]);
        LmsMeeting::create([
            'kelas_id' => $this->kelas->id, 'mata_pelajaran_id' => $this->mapel->id, 'guru_id' => $this->guru->id, 'judul' => 'Kelas Virtual IPA',
            'platform' => 'zoom', 'link_meeting' => 'https://zoom.test/j/1', 'waktu_mulai' => now()->addHour(), 'waktu_selesai' => now()->addHours(2), 'is_active' => true,
        ]);
        $this->buatUjian('ulangan_harian', 'Ulangan Tata Surya');

        $this->actingAs($this->user);
        $m = $this->mapel->id;

        $halaman = [
            route('siswa.lms.dashboard') => 'Halo, Siswa Pengujian!',
            route('siswa.lms.pengumuman.index') => 'pengumuman ditemukan',
            route('siswa.lms.kalender') => 'Keterangan',
            route('siswa.lms.kalender', ['mode' => 'minggu']) => 'Sehari',
            route('siswa.lms.kalender', ['mode' => 'tahun']) => 'Januari',
            route('siswa.lms.kalender.detail', ['tanggal' => now()->toDateString()]) => 'Kegiatan hari ini',
            route('siswa.lms.jadwal') => 'Cetak jadwal',
            route('siswa.lms.guru') => 'Belum ada guru pengajar',
            route('siswa.lms.tugas.index') => 'Laporan Pengamatan',
            route('siswa.lms.mapel.show', $m) => 'Ulangan Tata Surya',
            route('siswa.lms.mapel.materi', [$m, $materi->id]) => 'Buka link',
            route('siswa.lms.mapel.tugas.show', [$m, $tugas->id]) => 'Kirim jawaban',
            route('siswa.lms.mapel.forum.index', $m) => 'Diskusi Hujan',
            route('siswa.lms.mapel.forum.show', [$m, $forum->id]) => 'cocok($el)',
            route('siswa.lms.mapel.meeting.index', $m) => 'Kelas Virtual IPA',
        ];

        foreach ($halaman as $url => $teks) {
            $response = $this->get($url);
            $this->assertTanpaLegacy($response);
            $response->assertSee($teks, false);
        }

        // Cetak jadwal memakai layouts.print tanpa bridge Bootstrap.
        $this->assertTanpaLegacy($this->get(route('siswa.lms.jadwal.print')));

        // Rute tugas per mapel dulu menunjuk method yang tidak ada (HTTP 500).
        $this->get(route('siswa.lms.mapel.tugas.index', $m))->assertRedirect(route('siswa.lms.tugas.index', ['mapel' => $m]));
    }

    public function test_daftar_guru_whatsapp_link_uses_indonesian_country_code(): void
    {
        $this->guru->update(['telepon' => '0812-1111-2222']);
        \App\Models\GuruPengajarKelas::create(['tenaga_pendidik_id' => $this->guru->id, 'kelas_id' => $this->kelas->id, 'mata_pelajaran_id' => $this->mapel->id]);

        $this->actingAs($this->user)->get(route('siswa.lms.guru'))
            ->assertOk()
            ->assertSee('https://wa.me/6281211112222', false)
            ->assertDontSee('wa.me/0812', false);
    }

    public function test_forum_media_previews_inline_with_lightbox_and_youtube(): void
    {
        $forum = ForumDiskusi::create([
            'mata_pelajaran_id' => $this->mapel->id, 'kelas_id' => $this->kelas->id, 'user_id' => $this->guru->user_id, 'topik' => 'umum',
            'judul' => 'Media Forum', 'isi' => "Tonton: https://youtu.be/dQw4w9WgXcQ
Skrip <script>alert(1)</script>",
            'lampiran' => ['forum-attachments/a.png', 'forum-attachments/b.jpg', 'forum-attachments/c.mov'], 'is_pinned' => false, 'is_closed' => false,
        ]);

        $this->actingAs($this->user)->get(route('siswa.lms.mapel.forum.show', [$this->mapel->id, $forum->id]))
            ->assertOk()
            // Gambar dibuka di lightbox halaman yang sama, bukan tab baru.
            ->assertSee('data-lightbox="topik-' . $forum->id . '"', false)
            ->assertSee('$store.lightbox.buka', false)
            ->assertSee('aria-label="Pratinjau gambar"', false)
            ->assertDontSee('target="_blank" rel="noopener noreferrer" class="block w-fit', false)
            // Video (termasuk .mov) diputar langsung.
            ->assertSee('type="video/quicktime"', false)
            // Tautan YouTube menjadi pratinjau yang bisa diputar di tempat.
            ->assertSee('i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg', false)
            ->assertSee('youtube-nocookie.com/embed/dQw4w9WgXcQ', false)
            ->assertSee('href="https://youtu.be/dQw4w9WgXcQ"', false)
            // Isi tetap di-escape.
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_konten_terhapus_dialihkan_ke_induk_dan_notifikasinya_dibersihkan(): void
    {
        $m = $this->mapel->id;
        $forum = ForumDiskusi::create([
            'mata_pelajaran_id' => $m, 'kelas_id' => $this->kelas->id, 'user_id' => $this->guru->user_id,
            'topik' => 'umum', 'judul' => 'Topik sementara', 'isi' => 'Akan dihapus.', 'is_pinned' => false, 'is_closed' => false,
        ]);
        $url = route('siswa.lms.mapel.forum.show', [$m, $forum->id]);
        $notif = \App\Models\Notification::create([
            'user_id' => $this->user->id, 'tipe' => \App\Models\Notification::TIPE_SISTEM,
            'judul' => 'Diskusi baru', 'pesan' => 'Topik sementara', 'link' => $url,
        ]);
        $notifLain = \App\Models\Notification::create([
            'user_id' => $this->user->id, 'tipe' => \App\Models\Notification::TIPE_SISTEM,
            'judul' => 'Lain', 'pesan' => 'Tetap', 'link' => route('siswa.lms.mapel.forum.show', [$m, $forum->id + 1000]),
        ]);
        $forum->delete(); // guru menghapus topik setelah notifikasi terkirim

        // Notifikasi yang menunjuk topik itu langsung hilang; notifikasi lain tetap ada.
        $this->assertDatabaseMissing('notifications', ['id' => $notif->id]);
        $this->assertDatabaseHas('notifications', ['id' => $notifLain->id]);

        $this->actingAs($this->user);

        // Notifikasi yang sudah hilang / tautan lama → dialihkan dengan pesan, bukan halaman 404.
        $this->get(route('notifications.show', $notif->id))
            ->assertRedirect(route('notifications.index'))
            ->assertSessionHas('warning', \App\Support\NotFoundRedirector::PESAN);
        $this->get($url)
            ->assertRedirect(route('siswa.lms.mapel.forum.index', $m))
            ->assertSessionHas('warning', \App\Support\NotFoundRedirector::PESAN);

        // Data milik kelas lain juga dialihkan tanpa membocorkan isi.
        $kelasLain = Kelas::create(['cabang_id' => $this->kelas->cabang_id, 'tahun_ajaran_id' => $this->kelas->tahun_ajaran_id, 'nama_kelas' => '8B', 'jenjang' => 'SMP', 'kode_kelas' => 'TST-SMP-8B-2026', 'kuota_siswa' => 30]);
        $tugasLain = Tugas::create([
            'kelas_id' => $kelasLain->id, 'mata_pelajaran_id' => $m, 'guru_id' => $this->guru->id, 'jenis_tugas' => 'tugas',
            'urutan' => 1, 'judul_tugas' => 'Rahasia kelas lain', 'deskripsi' => 'x', 'tampilkan_nilai' => true, 'bisa_diulang' => false,
            'tanggal_mulai' => now(), 'tanggal_deadline' => now()->addDay(),
        ]);
        $this->get(route('siswa.lms.mapel.tugas.show', [$m, $tugasLain->id]))
            ->assertRedirect()->assertSessionHas('warning')->assertDontSee('Rahasia kelas lain');

        // URL yang memang tidak ada tetap 404, dan berkas tetap 404 standar.
        $this->get('/siswa/lms/halaman-tidak-ada')->assertNotFound();
    }

    public function test_lampiran_tugas_gambar_dan_video_tampil_langsung_dokumen_tetap_pratinjau(): void
    {
        $buat = fn (string $judul, string $file) => Tugas::create([
            'kelas_id' => $this->kelas->id, 'mata_pelajaran_id' => $this->mapel->id, 'guru_id' => $this->guru->id, 'jenis_tugas' => 'tugas',
            'urutan' => 1, 'judul_tugas' => $judul, 'deskripsi' => 'Lihat lampiran.', 'file_tugas' => $file, 'tampilkan_nilai' => true,
            'bisa_diulang' => true, 'tanggal_mulai' => now()->subDay(), 'tanggal_deadline' => now()->addDays(2),
        ]);
        $m = $this->mapel->id;
        $this->actingAs($this->user);

        // Gambar: tampil di halaman + perbesar lewat lightbox (bukan tombol "Lihat Gambar" / tab baru).
        $this->get(route('siswa.lms.mapel.tugas.show', [$m, $buat('Gambar logo', 'tugas/logo.png')->id]))
            ->assertOk()
            ->assertSee('storage/tugas/logo.png', false)
            ->assertSee('data-lightbox=', false)
            ->assertSee('$store.lightbox.buka', false)
            ->assertSee('aria-label="Pratinjau gambar"', false);

        // Video: diputar langsung.
        $this->get(route('siswa.lms.mapel.tugas.show', [$m, $buat('Video', 'tugas/klip.mp4')->id]))
            ->assertOk()->assertSee('<video', false)->assertSee('type="video/mp4"', false);

        // PDF: tetap tombol pratinjau (dialog), tidak ditampilkan langsung.
        $this->get(route('siswa.lms.mapel.tugas.show', [$m, $buat('Dokumen', 'tugas/soal.pdf')->id]))
            ->assertOk()->assertSee('Lihat Tugas')->assertSee('data-dialog-open', false)->assertDontSee('<video', false);
    }

    public function test_pengumuman_filters_run_on_the_server(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Pengumuman::create(['judul' => 'Libur Nasional', 'isi_pengumuman' => 'Sekolah libur.', 'tanggal_pengumuman' => now()->toDateString(), 'prioritas' => 'penting', 'status' => 'aktif', 'dibuat_oleh' => $admin->id]);
        Pengumuman::create(['judul' => 'Lomba Kebersihan', 'isi_pengumuman' => 'Ayo ikut.', 'tanggal_pengumuman' => now()->subDays(10), 'prioritas' => 'biasa', 'status' => 'aktif', 'dibuat_oleh' => $admin->id]);

        $this->actingAs($this->user);

        // Periksa daftar hasil controller (judul juga bisa muncul di lonceng notifikasi).
        $judul = fn (array $query) => $this->get(route('siswa.lms.pengumuman.index', $query))
            ->assertOk()->viewData('pengumumanList')->pluck('judul')->all();

        $this->assertSame(['Libur Nasional'], $judul(['prioritas' => 'penting']));
        $this->assertSame(['Lomba Kebersihan'], $judul(['q' => 'lomba']));
        $this->assertSame(['Libur Nasional'], $judul(['from' => now()->subDays(3)->toDateString()]));
        $this->assertSame(['Lomba Kebersihan', 'Libur Nasional'], $judul(['sort' => 'terlama']));
        // Tanggal & urutan tidak valid diabaikan, bukan error.
        $this->assertCount(2, $judul(['from' => 'bukan-tanggal', 'sort' => 'acak']));

        // Label prioritas di dashboard memakai nilai enum yang benar (dulu 'tinggi' yang tidak pernah ada).
        $this->get(route('siswa.lms.dashboard'))->assertOk()->assertSee('Penting');
    }

    public function test_ujian_focus_mode_flow_renders_on_the_bootstrap_free_exam_shell(): void
    {
        $ujian = $this->buatUjian('ulangan_harian', 'Ulangan Tata Surya');
        $m = $this->mapel->id;
        $this->actingAs($this->user);

        $mulai = $this->get(route('siswa.lms.mapel.ujian.show', [$m, $ujian->id]));
        $this->assertTanpaLegacy($mulai);
        $mulai->assertSee('Mulai Ujian Sekarang')->assertSee('examStart(false)', false)->assertSee('startExam($event)', false);

        $this->post(route('siswa.lms.mapel.ujian.mulai', [$m, $ujian->id]))->assertRedirect();

        $kerja = $this->get(route('siswa.lms.mapel.ujian.show', [$m, $ujian->id]));
        $this->assertTanpaLegacy($kerja);
        $kerja->assertSee('x-data="ujianWork"', false)
            ->assertSee('data-monitoring-url=', false)
            ->assertSee('SELESAIKAN UJIAN')
            ->assertSee('RAGU-RAGU')
            ->assertSee('data-kompleks', false)
            ->assertSee('data-benar-salah-answer', false)
            ->assertSee('x-ref="zoomDialog"', false)
            ->assertDontSee('cleanflow-nav', false); // mode fokus tanpa sidebar LMS

        $soal = $ujian->soalUjian->keyBy('tipe_soal');
        $this->post(route('siswa.lms.mapel.ujian.submit', [$m, $ujian->id]), ['jawaban' => [
            $soal['pilihan_ganda']->id => 'A',
            $soal['pilihan_ganda_kompleks']->id => json_encode(['A', 'C']),
            $soal['benar_salah']->id => json_encode([true, false]),
            $soal['isian_singkat']->id => 'bulan',
            $soal['uraian']->id => 'Air menguap karena panas.',
        ]])->assertRedirect();

        $hasil = $this->get(route('siswa.lms.mapel.ujian.show', [$m, $ujian->id]));
        $this->assertTanpaLegacy($hasil);
        $hasil->assertSee('Ujian Selesai!')->assertSee('Lihat Pembahasan')->assertSee('confirmRetake()', false);

        $review = $this->get(route('siswa.lms.mapel.ujian.review', [$m, $ujian->id]));
        $this->assertTanpaLegacy($review);
        $review->assertSee('Pembahasan Ujian')->assertSee('Merkurius')->assertSee('Matahari adalah bintang')->assertSee('Kunci / Referensi');
    }

    public function test_latihan_worksheet_flow_renders_without_anti_cheat_monitoring(): void
    {
        $latihan = $this->buatUjian('latihan', 'Latihan Tata Surya');
        $m = $this->mapel->id;
        $this->actingAs($this->user);

        $mulai = $this->get(route('siswa.lms.mapel.latihan.show', [$m, $latihan->id]));
        $this->assertTanpaLegacy($mulai);
        $mulai->assertSee('Mulai Latihan Sekarang')->assertSee('examStart(true)', false);

        $this->post(route('siswa.lms.mapel.latihan.mulai', [$m, $latihan->id]));

        $kerja = $this->get(route('siswa.lms.mapel.latihan.show', [$m, $latihan->id]));
        $this->assertTanpaLegacy($kerja);
        $kerja->assertSee('x-data="latihanWork"', false)
            ->assertSee('KIRIM JAWABAN')
            ->assertSee('Planet terdekat dari matahari?')
            ->assertSee('Jelaskan proses evaporasi.')
            ->assertDontSee('data-monitoring-url', false);
    }

    public function test_siswa_lms_sources_stay_free_of_page_assets_and_inline_styles(): void
    {
        $paths = collect(File::allFiles(resource_path('views/siswa/lms')))->map->getPathname()
            ->merge([
                resource_path('views/siswa/partials/sidebar-lms.blade.php'),
                resource_path('views/lms/forum/reply-item.blade.php'),
                resource_path('views/layouts/lms.blade.php'),
                resource_path('views/layouts/lms-ujian.blade.php'),
                resource_path('views/layouts/lms-latihan.blade.php'),
            ]);

        foreach ($paths as $path) {
            $source = File::get($path);
            $this->assertStringNotContainsString('<style', $source, $path);
            $this->assertStringNotContainsString('style="', $source, $path);
            $this->assertStringNotContainsString('@vite', $source, $path);
            $this->assertStringNotContainsString("@push('styles')", $source, $path);
            $this->assertStringNotContainsString("@push('scripts')", $source, $path);
            $this->assertStringNotContainsString('data-bs-', $source, $path);
            $this->assertStringNotContainsString('class="row', $source, $path);
            $this->assertStringNotContainsString('btn btn-', $source, $path);
        }

        // Shell LMS Siswa & mode ujian memuat inti CleanFlow tanpa bridge Bootstrap.
        $this->assertStringContainsString("'bootstrapFree' => true", File::get(resource_path('views/layouts/lms.blade.php')));
        $examShell = File::get(resource_path('views/layouts/partials/lms-exam-shell.blade.php'));
        $this->assertStringContainsString('resources/css/cleanflow.css', $examShell);
        $this->assertStringNotContainsString('admin.css', $examShell);
        $this->assertStringNotContainsString('bootstrap', File::get(resource_path('css/cleanflow.css')));
        $this->assertStringNotContainsString('legacy-content', File::get(resource_path('js/cleanflow.js')));
    }
}
