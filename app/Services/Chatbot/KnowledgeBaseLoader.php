<?php

namespace App\Services\Chatbot;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class KnowledgeBaseLoader
{
    protected const ROLE_FILE_MAP = [
        'admin' => 'admin.md',
        'guru_pengajar' => 'guru.md',
        'siswa' => 'siswa.md',
        'orang_tua' => 'orang-tua.md',
        'wali_kelas' => 'wali-kelas.md',
        'bendahara' => 'bendahara.md',
        'sekretaris' => 'sekretaris.md',
        'wakil_kepala_sekolah' => 'wakil-kepala-sekolah.md',
        'ketua_pkbm' => 'ketua-pkbm.md',
    ];

    protected const ROLE_ROUTE_PREFIX = [
        'admin' => 'admin.',
        'guru_pengajar' => 'guru.',
        'siswa' => 'siswa.',
        'orang_tua' => 'orang-tua.',
        'wali_kelas' => ['wali.', 'wali-kelas.'],
        'bendahara' => 'bendahara.',
        'sekretaris' => 'sekretaris.',
        'wakil_kepala_sekolah' => ['waka.', 'wakil.'],
        'ketua_pkbm' => ['ketua.', 'ketua-pkbm.'],
    ];

    /**
     * Per-feature ownership map.
     * Key = feature topic (case-insensitive substring match against user query).
     * Value = ['owner' => role(s) yang ngerjakan, 'admin_view' => route admin untuk lihat/monitor (read-only), 'description' => penjelasan singkat]
     */
    protected const FEATURE_OWNERSHIP = [
        'input nilai|nilai siswa|kelola nilai|isi nilai' => [
            'owner' => ['wali_kelas', 'guru_pengajar'],
            'admin_view_route' => 'admin.guru-pengajar.index',
            'description' => 'Input dan kelola nilai siswa dilakukan oleh Guru Pengajar (per mata pelajaran) dan Wali Kelas (rekap rapor)',
        ],
        'presensi|absensi|kehadiran|catat absen' => [
            'owner' => ['wali_kelas'],
            'admin_view_route' => 'admin.wali-kelas.index',
            'description' => 'Input presensi harian siswa dilakukan oleh Wali Kelas masing-masing kelas',
        ],
        'ajukan izin|izin sakit|izin siswa' => [
            'owner' => ['orang_tua'],
            'admin_view_route' => 'admin.monitoring.pengguna',
            'description' => 'Pengajuan izin siswa dilakukan oleh Orang Tua melalui Dashboard Orang Tua, kemudian divalidasi Wali Kelas',
        ],
        'cetak rapor|generate rapor|isi rapor|kelola rapor' => [
            'owner' => ['wali_kelas'],
            'admin_view_route' => 'admin.wali-kelas.index',
            'description' => 'Generate, input catatan, dan kirim validasi rapor dilakukan oleh Wali Kelas; validasi akhir oleh Ketua PKBM',
        ],
        'validasi rapor|setujui rapor|approve rapor' => [
            'owner' => ['ketua_pkbm'],
            'admin_view_route' => 'admin.monitoring.pengguna',
            'description' => 'Validasi dan tanda tangan rapor dilakukan oleh Ketua PKBM',
        ],
        'validasi pembayaran|konfirmasi pembayaran|setujui pembayaran|tolak pembayaran' => [
            'owner' => ['bendahara', 'admin'],
            'admin_view_route' => 'admin.keuangan.pembayaran.index',
            'description' => 'Validasi pembayaran masuk dilakukan oleh Bendahara (atau Admin)',
        ],
        'buat tagihan|generate spp|tagihan custom' => [
            'owner' => ['bendahara', 'admin'],
            'admin_view_route' => 'admin.keuangan.tagihan.index',
            'description' => 'Pembuatan tagihan, generate SPP, bulk create dilakukan oleh Bendahara atau Admin',
        ],
        'buat pengumuman|kelola pengumuman' => [
            'owner' => ['sekretaris', 'admin'],
            'admin_view_route' => 'admin.akademik.pengumuman.index',
            'description' => 'Membuat dan publikasi pengumuman dilakukan oleh Sekretaris (Admin juga bisa)',
        ],
        'buat berita|kelola berita|tulis artikel' => [
            'owner' => ['sekretaris', 'admin'],
            'admin_view_route' => 'admin.akademik.berita.index',
            'description' => 'Pengelolaan berita/artikel website dilakukan oleh Sekretaris (Admin juga bisa)',
        ],
        'kalender akademik|jadwal libur|event sekolah' => [
            'owner' => ['sekretaris', 'admin'],
            'admin_view_route' => 'admin.akademik.kalender.index',
            'description' => 'Kalender akademik (event, libur) dikelola oleh Sekretaris (Admin juga bisa)',
        ],
        'flyer|poster|brosur' => [
            'owner' => ['sekretaris', 'admin'],
            'admin_view_route' => 'admin.akademik.flyer.index',
            'description' => 'Flyer dan poster digital dikelola oleh Sekretaris (Admin juga bisa)',
        ],
        'buat tugas|koreksi tugas|kelola tugas|nilai tugas' => [
            'owner' => ['guru_pengajar'],
            'admin_view_route' => 'admin.monitoring.guru-pengajar',
            'description' => 'Pembuatan, distribusi, dan koreksi tugas dilakukan oleh Guru Pengajar di LMS',
        ],
        'buat ujian|buat soal|generate soal|koreksi ujian' => [
            'owner' => ['guru_pengajar'],
            'admin_view_route' => 'admin.monitoring.guru-pengajar',
            'description' => 'Pembuatan ujian, soal, dan koreksi dilakukan oleh Guru Pengajar di LMS',
        ],
        'upload materi|buat materi|kelola materi' => [
            'owner' => ['guru_pengajar'],
            'admin_view_route' => 'admin.monitoring.guru-pengajar',
            'description' => 'Upload materi pelajaran dilakukan oleh Guru Pengajar di LMS',
        ],
        'kkm|kriteria ketuntasan' => [
            'owner' => ['wakil_kepala_sekolah', 'admin'],
            'admin_view_route' => 'admin.akademik.promotion.kkm',
            'description' => 'Pengaturan KKM (Kriteria Ketuntasan Minimal) per mapel dikelola oleh Wakasek atau Admin',
        ],
        'kenaikan kelas|promosi siswa|naik kelas' => [
            'owner' => ['wakil_kepala_sekolah', 'admin', 'ketua_pkbm'],
            'admin_view_route' => 'admin.akademik.promotion.report',
            'description' => 'Proses kenaikan kelas: Wakasek/Admin eksekusi, Ketua PKBM persetujuan akhir',
        ],
        'dispensasi|keringanan biaya' => [
            'owner' => ['ketua_pkbm', 'bendahara'],
            'admin_view_route' => 'admin.keuangan.promotion.validation',
            'description' => 'Validasi dispensasi pembayaran dikelola oleh Ketua PKBM atau Bendahara',
        ],
        'jadwal pelajaran|atur jadwal|buat jadwal|jadwal kelas' => [
            'owner' => ['wakil_kepala_sekolah', 'admin'],
            'admin_view_route' => 'admin.jadwal-pelajaran.index',
            'description' => 'Pengaturan jadwal pelajaran dilakukan oleh Wakasek atau Admin',
        ],
        'bayar spp|bayar tagihan|transfer pembayaran' => [
            'owner' => ['orang_tua'],
            'admin_view_route' => 'admin.keuangan.pembayaran.index',
            'description' => 'Pembayaran SPP dilakukan oleh Orang Tua via Dashboard Orang Tua (online Midtrans atau manual ke Bendahara)',
        ],
    ];

    public function getOwnershipMap(): array
    {
        return self::FEATURE_OWNERSHIP;
    }

    /**
     * Render compact ownership table for system prompt.
     * Only includes entries where current role is NOT the primary owner (i.e., relevant for cross-role recommendation).
     */
    public function getOwnershipPromptForRole(string $role): string
    {
        $lines = [];
        foreach (self::FEATURE_OWNERSHIP as $keywords => $entry) {
            $owners = $entry['owner'];
            if (in_array($role, $owners, true)) continue;
            $kwLabel = explode('|', $keywords)[0];
            $ownerLabel = implode(' / ', array_map(fn($r) => $this->roleLabel($r), $owners));
            $lines[] = "- Topik '{$kwLabel}' → DIKELOLA OLEH: {$ownerLabel}. {$entry['description']}";
        }
        return implode("\n", $lines);
    }

    public function roleLabel(string $role): string
    {
        return match ($role) {
            'admin' => 'Admin',
            'guru_pengajar' => 'Guru Pengajar',
            'siswa' => 'Siswa',
            'orang_tua' => 'Orang Tua / Wali Murid',
            'wali_kelas' => 'Wali Kelas',
            'bendahara' => 'Bendahara',
            'sekretaris' => 'Sekretaris',
            'wakil_kepala_sekolah' => 'Wakil Kepala Sekolah (Wakasek)',
            'ketua_pkbm' => 'Ketua PKBM',
            default => ucfirst(str_replace('_', ' ', $role)),
        };
    }

    public function getForRole(string $role): string
    {
        $cacheKey = "chatbot_kb_{$role}";

        return Cache::remember($cacheKey, 3600, function () use ($role) {
            $file = self::ROLE_FILE_MAP[$role] ?? 'landing-pages.md';
            $path = base_path("docs/flow/{$file}");

            if (!file_exists($path)) {
                return '';
            }

            $content = file_get_contents($path);
            return $this->trimToEssentials($content);
        });
    }

    public function getLandingKnowledge(): string
    {
        return Cache::remember('chatbot_kb_landing', 3600, function () {
            $path = base_path('docs/flow/landing-pages.md');
            if (!file_exists($path)) {
                return '';
            }
            return $this->trimToEssentials(file_get_contents($path));
        });
    }

    /**
     * Compact list of public landing-page URLs (no auth needed) — relevant for ALL roles.
     * Returned format suitable for direct injection to system prompt.
     */
    public function getLandingPagesPrompt(): string
    {
        return Cache::remember('chatbot_landing_pages_compact', 3600, function () {
            $pages = [
                '/' => 'Beranda / Home — landing utama sekolah',
                '/tentang-sekolah' => 'Tentang Sekolah — profil singkat PKBM',
                '/visi-misi' => 'Visi & Misi sekolah',
                '/struktur-organisasi' => 'Struktur Organisasi sekolah (pengurus, jabatan)',
                '/profil-guru' => 'Profil Guru — daftar pengajar (foto, NIP, mapel)',
                '/program-paud-tk' => 'Program PAUD / TK',
                '/program-sd-sma' => 'Program Pendidikan Kesetaraan (Paket A/B/C ≈ SD/SMP/SMA)',
                '/program-inklusi' => 'Program Pendidikan Inklusi (anak berkebutuhan khusus)',
                '/program-terapi' => 'Program Terapi / Konseling',
                '/fasilitas' => 'Fasilitas sekolah (ruang kelas, lab, dll)',
                '/ppdb' => 'PPDB — Penerimaan Peserta Didik Baru (alur pendaftaran, syarat, biaya)',
                '/galeri' => 'Galeri foto kegiatan sekolah',
                '/kontak' => 'Kontak sekolah (alamat, telepon, email, peta)',
                '/berita' => 'Berita & Artikel sekolah',
            ];
            $lines = [];
            foreach ($pages as $path => $desc) {
                $lines[] = "{$path} — {$desc}";
            }
            return implode("\n", $lines);
        });
    }

    /**
     * Whitelist of public landing page URLs (used by parseStructuredResponse to allow direct URL buttons).
     */
    public function getLandingPageUrls(): array
    {
        return [
            '/', '/tentang-sekolah', '/visi-misi', '/struktur-organisasi', '/profil-guru',
            '/program-paud-tk', '/program-sd-sma', '/program-inklusi', '/program-terapi',
            '/fasilitas', '/ppdb', '/galeri', '/kontak', '/berita',
        ];
    }

    public function isLandingPageUrl(string $path): bool
    {
        return in_array(rtrim($path, '/') ?: '/', $this->getLandingPageUrls(), true);
    }

    public function getRouteMapForRole(string $role): array
    {
        $cacheKey = "chatbot_routes_{$role}";

        return Cache::remember($cacheKey, 3600, function () use ($role) {
            $prefixes = (array) (self::ROLE_ROUTE_PREFIX[$role] ?? []);
            $sharedPrefixes = ['', 'login', 'logout', 'profile', 'account', 'notifications', 'home'];

            $map = [];
            foreach (Route::getRoutes()->getRoutesByName() as $name => $route) {
                if (!$name) continue;
                if ($this->isExcludedRoute($name)) continue;

                $matchesRolePrefix = false;
                foreach ($prefixes as $prefix) {
                    if (str_starts_with($name, $prefix)) {
                        $matchesRolePrefix = true;
                        break;
                    }
                }

                $matchesSharedPrefix = false;
                foreach ($sharedPrefixes as $shared) {
                    if ($shared === '' && !str_contains($name, '.')) {
                        $matchesSharedPrefix = true;
                        break;
                    }
                    if ($shared !== '' && (str_starts_with($name, $shared . '.') || $name === $shared)) {
                        $matchesSharedPrefix = true;
                        break;
                    }
                }

                if (!$matchesRolePrefix && !$matchesSharedPrefix) continue;

                if ($route->methods()[0] !== 'GET') continue;
                if (preg_match('/\{[^}]+\}/', $route->uri())) continue;

                $map[$name] = '/' . ltrim($route->uri(), '/');
            }

            return $map;
        });
    }

    public function isRouteAllowedForRole(string $routeName, string $role): bool
    {
        $map = $this->getRouteMapForRole($role);
        return array_key_exists($routeName, $map);
    }

    public function resolveRouteUrl(string $routeName): ?string
    {
        try {
            return route($routeName, [], false);
        } catch (\Throwable) {
            return null;
        }
    }

    protected function trimToEssentials(string $markdown): string
    {
        $lines = preg_split('/\r\n|\r|\n/', $markdown);
        $skipPatterns = '/^#{1,3}\s+(Glossary|Layout|Sidebar|Catatan Akhir|Footer|Ringkasan Singkat|Penjelasan Menu Non-Trivial|Menu Lintas-Role|Catatan Logika|Hal yang TIDAK|Verifikasi)/i';

        // Pass 1: keep Peta Menu (always) + Ringkasan Peran (compact intro). These give the LLM full coverage of all menus.
        $intro = [];
        $skipSection = false;
        $inDetailSection = false;
        $currentSubsection = null;
        $subsectionBuffer = [];
        $subsections = [];

        foreach ($lines as $line) {
            if (preg_match('/^##\s+Detail Sub-Halaman per Menu/i', $line)) {
                $inDetailSection = true;
                continue;
            }
            if ($inDetailSection) {
                if (preg_match('/^###\s+(.+)/', $line, $m)) {
                    if ($currentSubsection !== null) {
                        $subsections[$currentSubsection] = $this->condenseSubsection($subsectionBuffer);
                    }
                    $currentSubsection = trim($m[1]);
                    $subsectionBuffer = [];
                    continue;
                }
                if (preg_match('/^##\s+/', $line)) {
                    // end of Detail Sub-Halaman section, save last
                    if ($currentSubsection !== null) {
                        $subsections[$currentSubsection] = $this->condenseSubsection($subsectionBuffer);
                        $currentSubsection = null;
                    }
                    $inDetailSection = false;
                    continue;
                }
                if ($currentSubsection !== null) {
                    $subsectionBuffer[] = $line;
                }
                continue;
            }
            // Not in detail section
            if (preg_match($skipPatterns, $line)) {
                $skipSection = true;
                continue;
            }
            if ($skipSection && preg_match('/^#{1,3}\s+/', $line)) {
                $skipSection = false;
            }
            if ($skipSection) continue;
            $intro[] = $line;
        }
        if ($currentSubsection !== null) {
            $subsections[$currentSubsection] = $this->condenseSubsection($subsectionBuffer);
        }

        $introText = trim(preg_replace('/\n{3,}/', "\n\n", implode("\n", $intro)));

        $detail = "## Detail Sub-Halaman per Menu (ringkas)\n\n";
        foreach ($subsections as $name => $condensed) {
            $detail .= "### {$name}\n{$condensed}\n\n";
        }

        $result = $introText . "\n\n" . $detail;
        $result = preg_replace('/`/', '', $result);
        $result = trim($result);

        $maxChars = 12000;
        if (strlen($result) > $maxChars) {
            $result = substr($result, 0, $maxChars) . "\n\n[... knowledge base dipotong ...]";
        }

        return $result;
    }

    /**
     * Condense subsection body: keep first description paragraph + button table rows (compact).
     */
    protected function condenseSubsection(array $bodyLines): string
    {
        $out = [];
        $tableStarted = false;
        $tableRowCount = 0;
        $foundFirstText = false;

        foreach ($bodyLines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') continue;
            if (preg_match('/^\*\*Tampilan index\*\*:/i', $trimmed)) continue;
            if (preg_match('/^\*\*Catatan\*\*:\s*Mirror/i', $trimmed)) continue;
            if (preg_match('/^---+$/', $trimmed)) continue;

            if (preg_match('/^\|/', $trimmed)) {
                if (preg_match('/^\|---+/', $trimmed)) continue;
                if ($tableRowCount >= 8) continue;
                $tableStarted = true;
                $tableRowCount++;
                $out[] = $trimmed;
                continue;
            }
            if ($tableStarted) continue;

            if (!$foundFirstText) {
                if (strlen($trimmed) > 240) $trimmed = substr($trimmed, 0, 240) . '...';
                $out[] = $trimmed;
                $foundFirstText = true;
            }
        }
        return implode("\n", $out);
    }

    protected function isExcludedRoute(string $name): bool
    {
        $excluded = ['_debugbar', 'ignition', 'livewire', 'sanctum', 'telescope', 'horizon', 'passport', 'l5-swagger'];
        foreach ($excluded as $prefix) {
            if (str_starts_with($name, $prefix)) return true;
        }
        return false;
    }
}
