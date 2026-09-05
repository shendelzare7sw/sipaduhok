<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminResponsiveUiTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_tahun_ajaran_list_uses_the_full_available_width(): void
    {
        $this->withoutMiddleware();

        $this->actingAs($this->admin())
            ->get(route('admin.tahun-ajaran.index'))
            ->assertOk()
            ->assertSee('min-w-0 w-full space-y-5', false)
            ->assertDontSee('mx-auto max-w-7xl', false);
    }

    public function test_siswa_create_uses_the_shared_tailwind_flow_without_bootstrap_dialogs(): void
    {
        $this->withoutMiddleware();

        $this->actingAs($this->admin())
            ->get(route('admin.users.create-siswa'))
            ->assertOk()
            ->assertSee('Buat akun, tempatkan ke kelas, lalu lengkapi biodata.')
            ->assertSee('data-conditional-select', false)
            ->assertDontSee('data-bs-toggle', false)
            ->assertDontSee('kelasModal', false);
    }

    public function test_siswa_edit_and_detail_render_with_real_relations(): void
    {
        $this->withoutMiddleware();
        $admin = $this->admin();
        $cabang = Cabang::create([
            'kode_cabang' => 'TST',
            'nama_cabang' => 'Cabang Pengujian',
            'alamat' => 'Alamat pengujian',
            'is_active' => true,
        ]);
        $tahunAjaran = TahunAjaran::create([
            'nama_tahun_ajaran' => '2026/2027',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2027-06-30',
            'is_active' => true,
        ]);
        $kelas = Kelas::create([
            'cabang_id' => $cabang->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'nama_kelas' => '12 SMP',
            'jenjang' => 'SMP',
            'kode_kelas' => 'TST-12',
            'kuota_siswa' => 30,
        ]);
        $studentUser = User::factory()->create([
            'name' => 'Siswa Pengujian',
            'username' => 'siswa.uji',
            'role' => 'siswa',
            'cabang_id' => $cabang->id,
            'is_active' => true,
        ]);
        $siswa = Siswa::create([
            'user_id' => $studentUser->id,
            'cabang_id' => $cabang->id,
            'kelas_id' => $kelas->id,
            'nisn' => '1234567890',
            'nis' => 'TST001',
            'nama_lengkap' => 'Siswa Pengujian',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Depok',
            'tanggal_lahir' => '2010-01-01',
            'alamat' => 'Alamat siswa',
            'agama' => 'Islam',
            'tanggal_masuk' => '2026-07-01',
            'status' => 'aktif',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.edit-siswa', $siswa->id))
            ->assertOk()
            ->assertSee('Edit data siswa')
            ->assertSee('12 SMP · SMP · Cabang Pengujian');

        $this->actingAs($admin)
            ->get(route('admin.users.show-siswa', $siswa->id))
            ->assertOk()
            ->assertSee('Profil siswa')
            ->assertSee('Siswa Pengujian');

        $this->actingAs($admin)
            ->get(route('admin.users.siswa'))
            ->assertOk()
            ->assertSee('name="kelas_id"', false)
            ->assertSee('12 SMP · SMP · Cabang Pengujian');
    }

    public function test_user_print_reports_share_the_tailwind_print_layout(): void
    {
        $this->withoutMiddleware();
        $admin = $this->admin();

        foreach ([
            'admin.users.siswa.print' => 'Laporan Data Siswa',
            'admin.users.tenaga-pendidik.print' => 'Laporan Tenaga Pendidik',
            'admin.users.wali-siswa.print' => 'Laporan Data Wali Siswa',
        ] as $routeName => $title) {
            $this->actingAs($admin)
                ->get(route($routeName))
                ->assertOk()
                ->assertSee($title)
                ->assertSee('data-print-page', false)
                ->assertDontSee('data-zoom-in', false);
        }
    }

    public function test_admin_payment_flow_uses_tailwind_alpine_without_page_assets(): void
    {
        $views = [
            'index.blade.php' => ['data-payment-index', 'Detail pembayaran'],
            'riwayat-siswa.blade.php' => ['data-payment-history', 'Cetak kwitansi'],
            'create.blade.php' => ['data-payment-create', 'submitPayment'],
            'show.blade.php' => ['data-payment-detail', 'x-ref="approveDialog"', 'x-ref="rejectDialog"', 'x-model="rejection"'],
            'cetak-kwitansi.blade.php' => ['@extends(\'layouts.print\')', 'data-payment-receipt', 'Kwitansi ini merupakan bukti pembayaran yang sah.'],
        ];

        foreach ($views as $file => $markers) {
            $contents = file_get_contents(resource_path('views/admin/keuangan/pembayaran/'.$file));

            $this->assertStringNotContainsString('@vite(', $contents, $file.' masih memuat aset halaman.');
            $this->assertStringNotContainsString('data-bs-', $contents, $file.' masih memuat interaksi Bootstrap.');
            $this->assertStringNotContainsString('<style', $contents, $file.' masih memuat CSS inline.');

            foreach ($markers as $marker) {
                $this->assertStringContainsString($marker, $contents, $file.' kehilangan pola CleanFlow penting.');
            }
        }
    }

    public function test_admin_tagihan_core_flow_uses_cleanflow_without_page_assets(): void
    {
        $views = [
            'index.blade.php' => ['data-tagihan-index', 'resetSelected()', 'x-teleport="body"'],
            'show.blade.php' => ['data-tagihan-detail', 'Rincian tagihan'],
            'import.blade.php' => ['data-tagihan-import', 'handleDrop(event)', 'submitImport(event)'],
            'edit.blade.php' => ['data-tagihan-edit', 'formatCurrency(event)', 'deleteItem(url, label)'],
        ];

        foreach ($views as $file => $markers) {
            $contents = file_get_contents(resource_path('views/admin/keuangan/tagihan/'.$file));

            $this->assertStringNotContainsString('@vite(', $contents, $file.' masih memuat aset halaman.');
            $this->assertStringNotContainsString('data-bs-', $contents, $file.' masih memuat interaksi Bootstrap.');
            $this->assertStringNotContainsString('<style', $contents, $file.' masih memuat CSS inline.');

            foreach ($markers as $marker) {
                $this->assertStringContainsString($marker, $contents, $file.' kehilangan pola CleanFlow penting.');
            }
        }
    }

    public function test_admin_finance_mass_actions_and_reports_use_cleanflow_without_page_assets(): void
    {
        $views = [
            'tagihan/duplicate.blade.php' => ['data-tagihan-duplicate', 'submitDuplicate(event)'],
            'tagihan/carryover.blade.php' => ['data-tagihan-carryover', 'previewCarryover()', 'confirmExecute()'],
            'tagihan/bulk-create.blade.php' => ['data-tagihan-bulk-create', 'toggleVisible()', 'submitForm(event)'],
            'tagihan/create-custom.blade.php' => ['data-tagihan-create-custom', 'visibleStudents', 'submitForm(event)'],
            'tagihan/generate-spp.blade.php' => ['data-tagihan-generate-spp', 'effectiveMonthCount', 'submitForm(event)'],
            'tagihan/cetak.blade.php' => ['@extends(\'layouts.print\')', 'Rincian Tagihan Siswa'],
            'tagihan/cetak-laporan.blade.php' => ['@extends(\'layouts.print\')', 'Laporan Rekap Tagihan Siswa'],
            'laporan/index.blade.php' => ['data-finance-report-index', 'Arus kas harian', 'table-fixed'],
            'laporan/rekap-tagihan.blade.php' => ['data-finance-class-recap', 'Pembayaran per kelas', 'table-fixed'],
            'laporan/belum-lunas.blade.php' => ['data-finance-unpaid-report', 'Rincian tunggakan siswa', 'x-cleanflow.table-action'],
            'laporan/cetak.blade.php' => ['@extends(\'layouts.print\')', 'Laporan Pembayaran Bulanan'],
            'laporan/cetak-rekap-tagihan.blade.php' => ['@extends(\'layouts.print\')', 'Rekap Tagihan per Kelas'],
            'laporan/cetak-belum-lunas.blade.php' => ['@extends(\'layouts.print\')', 'Laporan Siswa Belum Lunas'],
        ];

        foreach ($views as $file => $markers) {
            $contents = file_get_contents(resource_path('views/admin/keuangan/'.$file));

            $this->assertStringNotContainsString('@vite(', $contents, $file.' masih memuat aset halaman.');
            $this->assertStringNotContainsString('data-bs-', $contents, $file.' masih memuat interaksi Bootstrap.');
            $this->assertStringNotContainsString('<style', $contents, $file.' masih memuat CSS inline.');
            $this->assertStringNotContainsString('style=', $contents, $file.' masih memuat atribut style inline.');

            foreach ($markers as $marker) {
                $this->assertStringContainsString($marker, $contents, $file.' kehilangan pola CleanFlow penting.');
            }
        }

        $this->assertDirectoryDoesNotExist(resource_path('css/admin/keuangan'));
        $this->assertDirectoryDoesNotExist(resource_path('js/admin/keuangan'));
    }

    public function test_admin_general_reports_share_tailwind_views_without_legacy_assets(): void
    {
        $views = [
            'index.blade.php' => ['data-admin-report-center', '$routePrefix', '$supportsAcademic'],
            'print-siswa.blade.php' => ['@extends(\'layouts.print\')', 'Daftar Siswa'],
            'print-guru.blade.php' => ['@extends(\'layouts.print\')', 'Daftar Tenaga Pendidik'],
            'print-kelas.blade.php' => ['@extends(\'layouts.print\')', 'Daftar Kelas'],
            'print-wali-kelas.blade.php' => ['@extends(\'layouts.print\')', 'Daftar Wali Kelas'],
            'print-guru-pengajar.blade.php' => ['@extends(\'layouts.print\')', 'Daftar Guru Pengajar'],
            'print-rekap.blade.php' => ['@extends(\'layouts.print\')', 'Rekap Statistik Sekolah'],
            'print-rekap-akademik.blade.php' => ['@extends(\'layouts.print\')', 'Rekap Akademik per Tahun Ajaran'],
        ];

        foreach ($views as $file => $markers) {
            $contents = file_get_contents(resource_path('views/admin/laporan/'.$file));
            $this->assertStringNotContainsString('@vite(', $contents);
            $this->assertStringNotContainsString('<style', $contents);
            $this->assertStringNotContainsString('style=', $contents);
            $this->assertStringNotContainsString('data-bs-', $contents);
            foreach ($markers as $marker) {
                $this->assertStringContainsString($marker, $contents);
            }
        }

        $this->assertDirectoryDoesNotExist(resource_path('views/admin/cetak-laporan'));
        $this->assertDirectoryDoesNotExist(resource_path('css/admin/laporan'));
        $this->assertDirectoryDoesNotExist(resource_path('js/admin/laporan'));
        $this->assertDirectoryDoesNotExist(resource_path('css/admin/cetak-laporan'));
        $this->assertDirectoryDoesNotExist(resource_path('js/admin/cetak-laporan'));
        $this->assertDirectoryDoesNotExist(public_path('css/admin/laporan'));
        $this->assertDirectoryDoesNotExist(public_path('js/admin/laporan'));
    }

    public function test_landing_page_admin_uses_tailwind_and_inline_alpine_without_page_assets(): void
    {
        $views = [
            'index.blade.php' => ['data-landing-page-index', 'table-fixed', 'Edit konten'],
            'edit.blade.php' => ['data-landing-page-editor', 'landingPageEditor()', 'Alpine.data', 'Tambah item baru', 'goToSection(activeSection)', 'data-editor-section'],
            'partials/item-card.blade.php' => ['item-wrapper', 'data-image-preview', '@change="previewImage($event)"'],
        ];

        foreach ($views as $file => $markers) {
            $contents = file_get_contents(resource_path('views/admin/landing-pages/'.$file));
            $this->assertStringNotContainsString('@vite(', $contents, $file.' masih memuat aset halaman.');
            $this->assertStringNotContainsString('<style', $contents, $file.' masih memuat CSS inline.');
            $this->assertStringNotContainsString('style=', $contents, $file.' masih memuat atribut style inline.');
            $this->assertStringNotContainsString('data-bs-', $contents, $file.' masih memuat interaksi Bootstrap.');
            $this->assertStringNotContainsString('form-control', $contents, $file.' masih memakai class Bootstrap.');

            foreach ($markers as $marker) {
                $this->assertStringContainsString($marker, $contents, $file.' kehilangan pola CleanFlow penting.');
            }
        }

        $this->assertDirectoryDoesNotExist(resource_path('css/admin/landing-pages'));
        $this->assertDirectoryDoesNotExist(resource_path('js/admin/landing-pages'));
    }

    public function test_admin_dense_finance_and_shared_list_layouts_keep_responsive_spacing(): void
    {
        $validation = file_get_contents(resource_path('views/admin/keuangan/promotion/validation.blade.php'));
        $history = file_get_contents(resource_path('views/admin/keuangan/promotion/history.blade.php'));
        $report = file_get_contents(resource_path('views/admin/keuangan/laporan/index.blade.php'));
        $notes = file_get_contents(resource_path('views/catatan/partials/index-content.blade.php'));

        $this->assertStringContainsString('min-w-[64rem]', $validation);
        $this->assertStringContainsString('<col class="w-40">', $validation);
        $this->assertStringContainsString("\$loop->first ? 'col-span-2 sm:col-span-1'", $history);
        $this->assertStringContainsString('mt-4 grid grid-cols-3 gap-2', $report);
        $this->assertStringContainsString('min-w-0 w-full space-y-5', $notes);
        $this->assertStringNotContainsString('mx-auto max-w-7xl', $notes);
    }

    public function test_all_admin_html_print_views_use_tailwind_without_page_assets(): void
    {
        $printViews = [
            'akademik/promotion/print.blade.php',
            'guru-pengajar/print.blade.php',
            'jadwal-pelajaran/export-pdf.blade.php',
            'jadwal-pelajaran/print.blade.php',
            'kelas/print.blade.php',
            'keuangan/laporan/cetak-belum-lunas.blade.php',
            'keuangan/laporan/cetak-rekap-tagihan.blade.php',
            'keuangan/laporan/cetak.blade.php',
            'keuangan/pembayaran/cetak-kwitansi.blade.php',
            'keuangan/tagihan/cetak-laporan.blade.php',
            'keuangan/tagihan/cetak.blade.php',
            'laporan/print-guru-pengajar.blade.php',
            'laporan/print-guru.blade.php',
            'laporan/print-kelas.blade.php',
            'laporan/print-rekap-akademik.blade.php',
            'laporan/print-rekap.blade.php',
            'laporan/print-siswa.blade.php',
            'laporan/print-wali-kelas.blade.php',
            'manajemen-siswa/print-kartu.blade.php',
            'manajemen-siswa/print.blade.php',
            'mata-pelajaran/print.blade.php',
            'users/print/siswa.blade.php',
            'users/print/tenaga-pendidik.blade.php',
            'users/print/wali-siswa.blade.php',
            'wali-kelas/print.blade.php',
        ];

        foreach ($printViews as $file) {
            $contents = file_get_contents(resource_path('views/admin/'.$file));
            $this->assertStringContainsString("@extends('layouts.print')", $contents, $file.' tidak memakai layout cetak bersama.');
            $this->assertStringNotContainsString('@vite(', $contents, $file.' masih memuat bundle secara langsung.');
            $this->assertStringNotContainsString('<style', $contents, $file.' masih memuat CSS halaman.');
            $this->assertStringNotContainsString('style=', $contents, $file.' masih memuat style inline.');
            $this->assertStringNotContainsString('data-bs-', $contents, $file.' masih memuat Bootstrap.');
        }

    }
}
