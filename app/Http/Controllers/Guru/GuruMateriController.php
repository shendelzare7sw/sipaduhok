<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\TenagaPendidik;
use App\Models\GuruPengajarKelas;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Materi;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Storage;

class GuruMateriController extends Controller
{
    /**
     * Tampilkan daftar materi
     */
    public function index($kelasId, $mapelId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);

        $materiList = Materi::where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id)
            ->when(request('tanggal'), function ($q) {
                return $q->whereDate('tanggal_upload', request('tanggal'));
            })
            ->when(request('search'), function ($q) {
                return $q->where('judul_materi', 'like', '%' . request('search') . '%');
            })
            ->orderBy('tanggal_upload', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('guru.lms.materi.index', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'materiList' => $materiList,
            'guru' => $tenagaPendidik,
        ]);
    }

    /**
     * Form tambah materi
     */
    /**
     * Form tambah materi
     */
    public function create(Request $request, $kelasId, $mapelId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);
        $kategori = $request->get('kategori', 'materi');

        // Kelas lain yang guru ini ajar mapel yang sama
        $kelasLain = GuruPengajarKelas::where('tenaga_pendidik_id', $tenagaPendidik->id)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('kelas_id', '!=', $kelasId)
            ->with('kelas')
            ->get();

        return view('guru.lms.materi.create', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'guru' => $tenagaPendidik,
            'kategori' => $kategori,
            'kelasLain' => $kelasLain,
        ]);
    }

    /**
     * Simpan materi baru
     */
    public function store(Request $request, $kelasId, $mapelId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        // Conditional validation based on tipe_file
        $tipeFile = $request->input('tipe_file');

        if ($tipeFile === 'link') {
            $validated = $request->validate([
                'judul_materi' => 'required|string|max:255',
                'kategori' => 'required|in:materi,modul_ajar',
                'deskripsi' => 'nullable|string',
                'url_materi' => 'required|url',
                'tipe_file' => 'required|in:pdf,video,ppt,doc,link',
            ]);
        } else {
            $validated = $request->validate([
                'judul_materi' => 'required|string|max:255',
                'kategori' => 'required|in:materi,modul_ajar',
                'deskripsi' => 'nullable|string',
                'file_materi' => 'required|file|max:51200', // 50MB
                'tipe_file' => 'required|in:pdf,video,ppt,doc,link',
            ]);
        }

        $filePath = null;
        $urlMateri = null;

        if ($tipeFile === 'link') {
            $urlMateri = $validated['url_materi'];
        } else {
            if ($request->hasFile('file_materi')) {
                $filePath = $request->file('file_materi')->store('materi', 'public');
            }
        }

        $materiData = [
            'mata_pelajaran_id' => $mapelId,
            'guru_id' => $tenagaPendidik->id,
            'judul_materi' => $validated['judul_materi'],
            'kategori' => $validated['kategori'],
            'deskripsi' => $validated['deskripsi'],
            'file_materi' => $filePath,
            'url_materi' => $urlMateri,
            'tipe_file' => $validated['tipe_file'],
            'tanggal_upload' => now(),
        ];

        // Buat untuk kelas utama
        $materi = Materi::create(array_merge($materiData, ['kelas_id' => $kelasId]));

        // Notify siswa in main class
        $notificationService = app(NotificationService::class);
        $notificationService->notifyMateriNew($materi);

        // Duplikasi ke kelas tambahan
        $kelasTambahan = $request->input('kelas_tambahan', []);
        $jumlahDuplikasi = 0;
        foreach ($kelasTambahan as $kelasLainId) {
            if ($this->hasAccess($tenagaPendidik->id, $kelasLainId, $mapelId)) {
                $materiDuplikat = Materi::create(array_merge($materiData, ['kelas_id' => $kelasLainId]));
                $notificationService->notifyMateriNew($materiDuplikat);
                $jumlahDuplikasi++;
            }
        }

        $msg = 'Materi berhasil ditambahkan';
        if ($jumlahDuplikasi > 0) {
            $msg .= " dan diduplikasi ke {$jumlahDuplikasi} kelas lain";
        }

        return redirect()
            ->route('guru.lms.materi.index', [$kelasId, $mapelId])
            ->with('success', $msg);
    }

    /**
     * Form edit materi
     */
    public function edit($kelasId, $mapelId, $id): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $materi = Materi::where('id', $id)
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id)
            ->firstOrFail();

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);

        // Kelas lain yang guru ini ajar mapel yang sama
        $kelasLain = GuruPengajarKelas::where('tenaga_pendidik_id', $tenagaPendidik->id)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('kelas_id', '!=', $kelasId)
            ->with('kelas')
            ->get();

        return view('guru.lms.materi.edit', [
            'materi' => $materi,
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'guru' => $tenagaPendidik,
            'kelasLain' => $kelasLain,
            'relatedClassIds' => Materi::where('guru_id', $tenagaPendidik->id)
                ->where('mata_pelajaran_id', $mapelId)
                ->where('judul_materi', $materi->judul_materi)
                ->where('id', '!=', $materi->id)
                ->pluck('kelas_id')
                ->toArray(),
        ]);
    }

    /**
     * Update materi
     */
    public function update(Request $request, $kelasId, $mapelId, $id): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $materi = Materi::where('id', $id)
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id)
            ->firstOrFail();

        // Capture original state for matching in other classes
        $originalFile = $materi->file_materi;
        $originalTitle = $materi->judul_materi;
        $tipeFile = $request->input('tipe_file');

        // Conditional validation based on tipe_file
        if ($tipeFile === 'link') {
            $validated = $request->validate([
                'judul_materi' => 'required|string|max:255',
                'kategori' => 'required|in:materi,modul_ajar',
                'deskripsi' => 'nullable|string',
                'url_materi' => 'required|url',
                'tipe_file' => 'required|in:pdf,video,ppt,doc,link',
                'tanggal_upload' => 'required|date',
            ]);
        } else {
            $validated = $request->validate([
                'judul_materi' => 'required|string|max:255',
                'kategori' => 'required|in:materi,modul_ajar',
                'deskripsi' => 'nullable|string',
                'file_materi' => 'nullable|file|max:51200',
                'tipe_file' => 'required|in:pdf,video,ppt,doc,link',
                'tanggal_upload' => 'required|date',
            ]);
        }

        if ($tipeFile === 'link') {
            // Link type: store URL, clear file
            $validated['file_materi'] = null;
            // SAFE FILE DELETE LOGIC for previous file if exists
            if ($materi->file_materi) {
                $isFileUsedElsewhere = Materi::where('file_materi', $materi->file_materi)
                    ->where('id', '!=', $materi->id)
                    ->exists();

                if (!$isFileUsedElsewhere) {
                    Storage::disk('public')->delete($materi->file_materi);
                }
            }
        } else {
            // File type: handle file upload if provided
            if ($request->hasFile('file_materi')) {
                // SAFE FILE DELETE LOGIC
                // Cek apakah file lama digunakan oleh materi lain?
                if ($materi->file_materi) {
                    $isFileUsedElsewhere = Materi::where('file_materi', $materi->file_materi)
                        ->where('id', '!=', $materi->id)
                        ->exists();

                    if (!$isFileUsedElsewhere) {
                        Storage::disk('public')->delete($materi->file_materi);
                    }
                }

                $validated['file_materi'] = $request->file('file_materi')->store('materi', 'public');
            } else {
                // Keep existing file if no new file is uploaded
                $validated['file_materi'] = $materi->file_materi;
            }
            // Clear URL for file types
            $validated['url_materi'] = null;
        }

        $materi->update($validated);

        // DUPLICATE / SYNC LOGIC (Add/Update to linked classes)
        $kelasTambahan = $request->input('kelas_tambahan', []);
        $jumlahDuplikasi = 0;
        $jumlahUpdate = 0;

        if (!empty($kelasTambahan)) {
            // Data untuk duplikasi/sync
            $syncData = [
                'mata_pelajaran_id' => $mapelId,
                'guru_id' => $tenagaPendidik->id,
                'judul_materi' => $materi->judul_materi, // New Title
                'kategori' => $materi->kategori,
                'deskripsi' => $materi->deskripsi,
                'file_materi' => $materi->file_materi, // New/Current File Path
                'url_materi' => $materi->url_materi, // New/Current URL
                'tipe_file' => $materi->tipe_file,
                'tanggal_upload' => $materi->tanggal_upload,
            ];

            foreach ($kelasTambahan as $kelasLainId) {
                if ($this->hasAccess($tenagaPendidik->id, $kelasLainId, $mapelId)) {
                    // Try to find existing material in target class to update
                    // Match priorities: 1. By Original File Path (strong link), 2. By Original Title
                    $query = Materi::where('kelas_id', $kelasLainId)
                        ->where('guru_id', $tenagaPendidik->id)
                        ->where('mata_pelajaran_id', $mapelId);

                    $existing = null;

                    if ($originalFile) {
                        // First try finding by file path
                        $existing = (clone $query)->where('file_materi', $originalFile)->first();
                    }

                    if (!$existing) {
                        // If not found by file (or no file), try by Title
                        $existing = (clone $query)->where('judul_materi', $originalTitle)->first();
                    }

                    if ($existing) {
                        // Update existing match
                        $existing->update($syncData);
                        $jumlahUpdate++;
                    } else {
                        // Create new if no match found
                        Materi::create(array_merge($syncData, ['kelas_id' => $kelasLainId]));
                        $jumlahDuplikasi++;
                    }
                }
            }
        }

        $msg = 'Materi berhasil diperbarui';
        if ($jumlahDuplikasi > 0 || $jumlahUpdate > 0) {
            $msg .= " (Disinkronisasi ke " . ($jumlahDuplikasi + $jumlahUpdate) . " kelas lain)";
        }

        return redirect()
            ->route('guru.lms.materi.index', [$kelasId, $mapelId])
            ->with('success', $msg);
    }

    /**
     * Hapus materi
     */
    public function destroy(Request $request, $kelasId, $mapelId, $id): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $materi = Materi::where('id', $id)
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id)
            ->firstOrFail();

        $filesToDelete = []; // Collect files to delete safely
        $idsToDelete = [$materi->id];

        // BULK DELETE LOGIC
        if ($request->has('hapus_terkait')) {
            // Cari materi lain dengan Judul, Tipe, dan Guru yang sama di mapel ini (beda kelas)
            $relatedMateris = Materi::where('guru_id', $tenagaPendidik->id)
                ->where('mata_pelajaran_id', $mapelId)
                ->where('judul_materi', $materi->judul_materi)
                ->where('tipe_file', $materi->tipe_file)
                ->where('id', '!=', $materi->id) // Exclude current
                ->get();

            foreach($relatedMateris as $rel) {
                $idsToDelete[] = $rel->id;
            }
        }

        // Process Deletion
        $materisToDelete = Materi::whereIn('id', $idsToDelete)->get();

        foreach ($materisToDelete as $m) {
            if ($m->file_materi) {
                // SAFE DELETE: Cek apakah file digunakan oleh materi yang TIDAK akan dihapus
                $usageCount = Materi::where('file_materi', $m->file_materi)
                    ->whereNotIn('id', $idsToDelete) // Check usage outside of the deletionlist
                    ->count();

                if ($usageCount === 0) {
                    $filesToDelete[] = $m->file_materi;
                }
            }
            $m->delete();
        }

        // Delete physical files (unique paths only)
        $filesToDelete = array_unique($filesToDelete);
        foreach ($filesToDelete as $file) {
            Storage::disk('public')->delete($file);
        }

        $msg = 'Materi berhasil dihapus';
        if (count($idsToDelete) > 1) {
            $countLain = count($idsToDelete) - 1;
            $msg .= " (termasuk {$countLain} materi terkait di kelas lain)";
        }

        return redirect()
            ->route('guru.lms.materi.index', [$kelasId, $mapelId])
            ->with('success', $msg);
    }

    /**
     * Verifikasi akses guru
     */
    private function verifyAccess($guruId, $kelasId, $mapelId)
    {
        if (!$this->hasAccess($guruId, $kelasId, $mapelId)) {
            abort(403, 'Anda tidak memiliki akses ke mata pelajaran ini');
        }
    }

    private function hasAccess($guruId, $kelasId, $mapelId): bool
    {
        return GuruPengajarKelas::where('tenaga_pendidik_id', $guruId)
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->exists();
    }
}
