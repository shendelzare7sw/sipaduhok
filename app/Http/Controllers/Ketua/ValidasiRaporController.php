<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Rapor;
use App\Models\TahunAjaran;
use App\Models\PengajuanRaporKetua;

class ValidasiRaporController extends Controller
{
    /**
     * Display list of students awaiting Ketua PKBM validation.
     * Only show students where Bendahara AND Wali Kelas already validated.
     */
    public function index(Request $request): View
    {
        $activeYear = TahunAjaran::where('is_active', true)->first();

        // Base query: Students that Wali Kelas has submitted for Ketua review
        $query = Siswa::with(['kelas.cabang', 'kelas.tahunAjaran'])
            ->where('validasi_rapor_wali', true)
            ->where('status', 'aktif');

        // Filter by active year
        if ($activeYear) {
            $query->whereHas('kelas', function($q) use ($activeYear) {
                $q->where('tahun_ajaran_id', $activeYear->id);
            });
        }

        // Filter by kelas
        if ($request->has('kelas_id') && $request->kelas_id != '') {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Filter by Ketua validation status
        if ($request->has('status_ketua') && $request->status_ketua != '') {
            if ($request->status_ketua == 'pending') {
                $query->where('validasi_rapor_ketua', false);
            } elseif ($request->status_ketua == 'validated') {
                $query->where('validasi_rapor_ketua', true);
            }
        }

        // Search by name or NIS
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('nis', 'like', '%' . $request->search . '%');
            });
        }

        $siswaList = $query->orderBy('kelas_id')
                           ->orderBy('nama_lengkap')
                           ->paginate(50);

        // Stats for cards
        $stats = [
            'pendingTotal' => Siswa::where('validasi_rapor_wali', true)
                                   ->where('validasi_rapor_ketua', false)
                                   ->where('status', 'aktif')
                                   ->count(),
            'validatedToday' => Siswa::where('validasi_rapor_ketua', true)
                                     ->whereDate('tanggal_validasi_rapor_ketua', today())
                                     ->count(),
        ];

        // Get kelas list for filter dropdown
        $kelasList = Kelas::with('cabang')
            ->when($activeYear, function($q) use ($activeYear) {
                $q->where('tahun_ajaran_id', $activeYear->id);
            })
            ->orderBy('nama_kelas')
            ->get();

        return view('ketua.validasi-rapor.index', [
            'siswaList' => $siswaList,
            'stats' => $stats,
            'kelasList' => $kelasList,
            'activeYear' => $activeYear,
        ]);
    }

    /**
     * Validate rapor access for a student (Ketua PKBM approval).
     */
    public function validasiRapor(int $siswaId): RedirectResponse
    {
        $siswa = Siswa::findOrFail($siswaId);

        // Ensure prerequisite: Wali Kelas must have submitted first
        if (!$siswa->validasi_rapor_wali) {
            return redirect()->back()->with('error', 'Wali Kelas belum mengirim rapor ini untuk divalidasi.');
        }

        $siswa->validasi_rapor_ketua = true;
        $siswa->tanggal_validasi_rapor_ketua = now();
        $siswa->validasi_rapor_ketua_oleh = auth()->id();
        $siswa->save();

        // Notify Wali Kelas & Bendahara
        app(\App\Services\NotificationService::class)->notifyKetuaApproveRapor($siswa);

        return redirect()->back()->with('success', "Akses rapor untuk {$siswa->nama_lengkap} berhasil divalidasi.");
    }

    /**
     * Cancel Ketua PKBM validation.
     */
    public function batalkanRapor(int $siswaId): RedirectResponse
    {
        $siswa = Siswa::findOrFail($siswaId);

        $siswa->validasi_rapor_ketua = false;
        $siswa->tanggal_validasi_rapor_ketua = null;
        $siswa->validasi_rapor_ketua_oleh = null;
        // CASCADE: reset bendahara validation
        $siswa->validasi_rapor_bendahara = false;
        $siswa->tanggal_validasi_rapor_bendahara = null;
        $siswa->validasi_rapor_oleh = null;
        $siswa->save();

        // Notify Wali Kelas
        app(\App\Services\NotificationService::class)->notifyKetuaBatalkanRapor($siswa);

        return redirect()->back()->with('success', "Validasi rapor untuk {$siswa->nama_lengkap} berhasil dibatalkan.");
    }

    /**
     * Bulk validate selected students.
     */
    public function bulkValidasi(Request $request): RedirectResponse
    {
        $request->validate([
            'siswa_ids' => 'required|array',
            'siswa_ids.*' => 'exists:siswa,id',
        ]);

        $siswaList = Siswa::whereIn('id', $request->siswa_ids)
            ->where('validasi_rapor_wali', true)
            ->get();

        $validated = 0;
        $notifService = app(\App\Services\NotificationService::class);
        foreach ($siswaList as $siswa) {
            $siswa->validasi_rapor_ketua = true;
            $siswa->tanggal_validasi_rapor_ketua = now();
            $siswa->validasi_rapor_ketua_oleh = auth()->id();
            $siswa->save();
            $notifService->notifyKetuaApproveRapor($siswa);
            $validated++;
        }

        return redirect()->back()->with('success', "Berhasil memvalidasi {$validated} siswa.");
    }

    /**
     * Preview rapor siswa (Ketua can preview before approving).
     */
    public function previewRapor(int $siswaId): View|RedirectResponse
    {
        $siswa = Siswa::findOrFail($siswaId);
        $activeYear = TahunAjaran::where('is_active', true)->first();

        $rapor = Rapor::with([
            'siswa',
            'kelas.waliKelas',
            'tahunAjaran',
            'raporNilai.mataPelajaran',
            'raporNilai.nilai',
            'kegiatanEkstra',
        ])
        ->where('siswa_id', $siswa->id)
        ->when($activeYear, fn($q) => $q->where('tahun_ajaran_id', $activeYear->id))
        ->latest()
        ->first();

        if (!$rapor) {
            return redirect()->back()->with('error', 'Rapor belum digenerate untuk siswa ini.');
        }

        if ($rapor->jenis_rapor === 'tengah_semester') {
            return view('wali-kelas.rapor.preview-pts', compact('rapor'));
        }
        return view('wali-kelas.rapor.preview-pas', compact('rapor'));
    }

    /**
     * Validate all pending students (where Bendahara + Wali already approved).
     */
    public function validasiSemuaRapor(): RedirectResponse
    {
        $siswaList = Siswa::where('validasi_rapor_wali', true)
            ->where('validasi_rapor_ketua', false)
            ->where('status', 'aktif')
            ->get();

        if ($siswaList->isEmpty()) {
            return redirect()->back()->with('info', 'Tidak ada siswa yang perlu divalidasi.');
        }

        $notifService = app(\App\Services\NotificationService::class);
        foreach ($siswaList as $siswa) {
            $siswa->validasi_rapor_ketua = true;
            $siswa->tanggal_validasi_rapor_ketua = now();
            $siswa->validasi_rapor_ketua_oleh = auth()->id();
            $siswa->save();
            $notifService->notifyKetuaApproveRapor($siswa);
        }

        return redirect()->back()->with('success', "Berhasil memvalidasi {$siswaList->count()} siswa.");
    }

    /**
     * Minta revisi rapor - kirim catatan ke wali kelas.
     */
    public function mintaRevisi(Request $request, int $siswaId): RedirectResponse
    {
        $request->validate([
            'catatan_revisi' => 'required|string|max:1000',
        ]);

        $siswa = Siswa::findOrFail($siswaId);
        $activeYear = TahunAjaran::where('is_active', true)->first();

        $rapor = Rapor::where('siswa_id', $siswa->id)
            ->when($activeYear, fn($q) => $q->where('tahun_ajaran_id', $activeYear->id))
            ->latest()
            ->first();

        if (!$rapor) {
            return redirect()->back()->with('error', 'Rapor tidak ditemukan.');
        }

        $rapor->update([
            'catatan_revisi_ketua' => $request->catatan_revisi,
            'status_review_ketua' => 'perlu_revisi',
        ]);

        // Reset validasi wali agar wali kelas harus kirim ulang setelah revisi
        $siswa->update([
            'validasi_rapor_wali' => false,
            'tanggal_validasi_rapor_wali' => null,
            'validasi_rapor_ketua' => false,
            'tanggal_validasi_rapor_ketua' => null,
            'validasi_rapor_ketua_oleh' => null,
        ]);

        // Notify Wali Kelas about revision request
        app(\App\Services\NotificationService::class)->notifyKetuaMintaRevisi($siswa, $request->catatan_revisi);

        return redirect()->back()->with('success', "Catatan revisi berhasil dikirim ke wali kelas untuk rapor {$siswa->nama_lengkap}.");
    }

    /**
     * Tampilkan daftar dispensasi dari bendahara.
     */
    public function dispensasiIndex(Request $request): View
    {
        $query = PengajuanRaporKetua::with(['siswa.kelas', 'pengaju'])
            ->orderByRaw("FIELD(status, 'menunggu', 'disetujui', 'ditolak')")
            ->latest('tanggal_pengajuan');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        $dispensasiList = $query->paginate(25);

        $stats = [
            'menunggu' => PengajuanRaporKetua::where('status', 'menunggu')->count(),
            'disetujui' => PengajuanRaporKetua::where('status', 'disetujui')->count(),
            'ditolak' => PengajuanRaporKetua::where('status', 'ditolak')->count(),
        ];

        return view('ketua.dispensasi.index', compact('dispensasiList', 'stats'));
    }

    /**
     * Approve dispensasi (satu atau beberapa).
     */
    public function approveDispensasi(Request $request): RedirectResponse
    {
        $request->validate([
            'dispensasi_ids' => 'required|array',
            'dispensasi_ids.*' => 'exists:pengajuan_rapor_ketua,id',
            'catatan_ketua' => 'nullable|string|max:500',
        ]);

        $approved = 0;
        $pengajuanList = PengajuanRaporKetua::whereIn('id', $request->dispensasi_ids)
            ->where('status', 'menunggu')
            ->with('siswa')
            ->get();

        foreach ($pengajuanList as $pengajuan) {
            $pengajuan->update([
                'status' => 'disetujui',
                'diputuskan_oleh' => auth()->id(),
                'catatan_ketua' => $request->catatan_ketua,
                'tanggal_keputusan' => now(),
            ]);

            // Auto-set validasi pada siswa
            $siswa = $pengajuan->siswa;
            if ($siswa) {
                if ($pengajuan->tipe === 'rapor') {
                    $siswa->update([
                        'validasi_rapor_bendahara' => true,
                        'tanggal_validasi_rapor_bendahara' => now(),
                        'validasi_rapor_oleh' => auth()->id(),
                    ]);
                } elseif ($pengajuan->tipe === 'ujian') {
                    $siswa->update([
                        'validasi_ujian_bendahara' => true,
                        'tanggal_validasi_ujian_bendahara' => now(),
                        'validasi_ujian_oleh' => auth()->id(),
                    ]);
                }
            }
            $approved++;
        }

        // Notify pengaju (bendahara/admin)
        app(\App\Services\NotificationService::class)->notifyKeputusanDispensasi($pengajuanList, 'disetujui', $request->catatan_ketua);

        return redirect()->back()->with('success', "Berhasil menyetujui {$approved} dispensasi.");
    }

    /**
     * Reject dispensasi.
     */
    public function rejectDispensasi(Request $request): RedirectResponse
    {
        $request->validate([
            'dispensasi_ids' => 'required|array',
            'dispensasi_ids.*' => 'exists:pengajuan_rapor_ketua,id',
            'catatan_ketua' => 'nullable|string|max:500',
        ]);

        // Get the list before updating for notification
        $pengajuanList = PengajuanRaporKetua::whereIn('id', $request->dispensasi_ids)
            ->where('status', 'menunggu')
            ->with('pengaju')
            ->get();

        PengajuanRaporKetua::whereIn('id', $request->dispensasi_ids)
            ->where('status', 'menunggu')
            ->update([
                'status' => 'ditolak',
                'diputuskan_oleh' => auth()->id(),
                'catatan_ketua' => $request->catatan_ketua,
                'tanggal_keputusan' => now(),
            ]);
        $rejected = count($request->dispensasi_ids);

        // Notify pengaju (bendahara/admin)
        app(\App\Services\NotificationService::class)->notifyKeputusanDispensasi($pengajuanList, 'ditolak', $request->catatan_ketua);

        return redirect()->back()->with('success', "Berhasil menolak {$rejected} dispensasi.");
    }
}
