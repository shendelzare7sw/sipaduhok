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
use App\Models\Tugas;
use App\Models\TugasSiswa;
use App\Models\Siswa;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Storage;

class GuruTugasController extends Controller
{
    /**
     * Tampilkan daftar tugas
     */
    public function index($kelasId, $mapelId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);

        $tugasList = Tugas::where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id)
            ->withCount([
                'tugasSiswa as submitted_count' => function ($query) {
                    $query->where('status', '!=', 'belum_dikerjakan');
                }
            ])
            ->orderBy('tanggal_mulai', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('guru.lms.tugas.index', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'tugasList' => $tugasList,
            'guru' => $tenagaPendidik,
        ]);
    }

    /**
     * Form tambah tugas
     */
    /**
     * Form tambah tugas
     */
    public function create(Request $request, $kelasId, $mapelId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);

        $kelasLain = GuruPengajarKelas::where('tenaga_pendidik_id', $tenagaPendidik->id)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('kelas_id', '!=', $kelasId)
            ->with('kelas')
            ->get();

        return view('guru.lms.tugas.create', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'guru' => $tenagaPendidik,
            'kelasLain' => $kelasLain,
        ]);
    }

    /**
     * Simpan tugas baru
     */
    public function store(Request $request, $kelasId, $mapelId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $validated = $request->validate([
            'judul_tugas' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            // Batasi tipe lampiran (dokumen/gambar/arsip) & tolak tipe berbahaya
            // (html/svg/php/js) yang bisa memicu stored-XSS ke siswa.
            'file_tugas' => 'nullable|file|max:10240|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png,gif,zip', // 10MB
            'tanggal_mulai' => 'required|date',
            'tanggal_deadline' => 'required|date|after:tanggal_mulai',
            'tampilkan_nilai' => 'nullable|boolean',
            'bisa_diulang' => 'nullable|boolean',
            'batas_pengulangan' => 'nullable|integer|min:0',
        ]);

        $filePath = null;
        if ($request->hasFile('file_tugas')) {
            $filePath = $request->file('file_tugas')->store('tugas', 'public');
        }

        $mataPelajaran = MataPelajaran::findOrFail($mapelId);

        $tugasData = [
            'mata_pelajaran_id' => $mapelId,
            'guru_id' => $tenagaPendidik->id,
            'judul_tugas' => $validated['judul_tugas'],
            'deskripsi' => $validated['deskripsi'],
            'file_tugas' => $filePath,
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_deadline' => $validated['tanggal_deadline'],
            'tampilkan_nilai' => $request->has('tampilkan_nilai'),
            'bisa_diulang' => $request->has('bisa_diulang'),
            'batas_pengulangan' => $request->has('bisa_diulang') ? $validated['batas_pengulangan'] : null,
        ];

        // Buat untuk kelas utama
        $tugas = Tugas::create(array_merge($tugasData, ['kelas_id' => $kelasId]));
        $this->createTugasSiswaForKelas($tugas, $kelasId, $mataPelajaran);

        // Notify siswa in main class
        $notificationService = app(NotificationService::class);
        $notificationService->notifyTugasNew($tugas);

        // Duplikasi ke kelas tambahan
        $kelasTambahan = $request->input('kelas_tambahan', []);
        $jumlahDuplikasi = 0;
        foreach ($kelasTambahan as $kelasLainId) {
            if ($this->hasAccess($tenagaPendidik->id, $kelasLainId, $mapelId)) {
                $tugasDuplikat = Tugas::create(array_merge($tugasData, ['kelas_id' => $kelasLainId]));
                $this->createTugasSiswaForKelas($tugasDuplikat, $kelasLainId, $mataPelajaran);
                $notificationService->notifyTugasNew($tugasDuplikat);
                $jumlahDuplikasi++;
            }
        }

        $msg = 'Tugas berhasil ditambahkan';
        if ($jumlahDuplikasi > 0) {
            $msg .= " dan diduplikasi ke {$jumlahDuplikasi} kelas lain";
        }

        return redirect()
            ->route('guru.lms.tugas.index', [$kelasId, $mapelId])
            ->with('success', $msg);
    }

    /**
     * Form edit tugas
     */
    public function edit($kelasId, $mapelId, $id): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $tugas = Tugas::where('id', $id)
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id)
            ->firstOrFail();

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);

        $kelasLain = GuruPengajarKelas::where('tenaga_pendidik_id', $tenagaPendidik->id)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('kelas_id', '!=', $kelasId)
            ->with('kelas')
            ->get();

        return view('guru.lms.tugas.edit', [
            'tugas' => $tugas,
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'guru' => $tenagaPendidik,
            'kelasLain' => $kelasLain,
            'relatedClassIds' => Tugas::where('guru_id', $tenagaPendidik->id)
                ->where('mata_pelajaran_id', $mapelId)
                ->where('judul_tugas', $tugas->judul_tugas)
                ->where('id', '!=', $tugas->id)
                ->pluck('kelas_id')
                ->toArray(),
        ]);
    }

    /**
     * Update tugas
     */
    public function update(Request $request, $kelasId, $mapelId, $id): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $tugas = Tugas::where('id', $id)
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id)
            ->firstOrFail();

        // Capture original state for matching in other classes
        $originalFile = $tugas->file_tugas;
        $originalTitle = $tugas->judul_tugas;

        $validated = $request->validate([
            'judul_tugas' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'file_tugas' => 'nullable|file|max:10240|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png,gif,zip',
            'tanggal_mulai' => 'required|date',
            'tanggal_deadline' => 'required|date|after:tanggal_mulai',
            'tampilkan_nilai' => 'nullable|boolean',
            'bisa_diulang' => 'nullable|boolean',
            'batas_pengulangan' => 'nullable|integer|min:0',
        ]);

        $validated['tampilkan_nilai'] = $request->has('tampilkan_nilai');
        $validated['bisa_diulang'] = $request->has('bisa_diulang');
        $validated['batas_pengulangan'] = $request->has('bisa_diulang') ? $validated['batas_pengulangan'] : null;

        if ($request->hasFile('file_tugas')) {
            // SAFE FILE DELETE: Cek apakah file lama digunakan oleh tugas lain
            if ($tugas->file_tugas) {
                $isFileUsedElsewhere = Tugas::where('file_tugas', $tugas->file_tugas)
                    ->where('id', '!=', $tugas->id)
                    ->exists();

                if (!$isFileUsedElsewhere) {
                    Storage::disk('public')->delete($tugas->file_tugas);
                }
            }

            $validated['file_tugas'] = $request->file('file_tugas')->store('tugas', 'public');
        }

        $tugas->update($validated);

        // SYNC LOGIC (Update or Create to linked classes)
        $kelasTambahan = $request->input('kelas_tambahan', []);
        $jumlahDuplikasi = 0;
        $jumlahUpdate = 0;

        if (!empty($kelasTambahan)) {
            $mataPelajaran = MataPelajaran::findOrFail($mapelId);

            $syncData = [
                'mata_pelajaran_id' => $mapelId,
                'guru_id' => $tenagaPendidik->id,
                'judul_tugas' => $tugas->judul_tugas,
                'deskripsi' => $tugas->deskripsi,
                'file_tugas' => $tugas->file_tugas,
                'tanggal_mulai' => $tugas->tanggal_mulai,
                'tanggal_deadline' => $tugas->tanggal_deadline,
                'tampilkan_nilai' => $tugas->tampilkan_nilai,
            ];

            foreach ($kelasTambahan as $kelasLainId) {
                if ($this->hasAccess($tenagaPendidik->id, $kelasLainId, $mapelId)) {
                    $query = Tugas::where('kelas_id', $kelasLainId)
                        ->where('guru_id', $tenagaPendidik->id)
                        ->where('mata_pelajaran_id', $mapelId);

                    $existing = null;

                    if ($originalFile) {
                        $existing = (clone $query)->where('file_tugas', $originalFile)->first();
                    }

                    if (!$existing) {
                        $existing = (clone $query)->where('judul_tugas', $originalTitle)->first();
                    }

                    if ($existing) {
                        $existing->update($syncData);
                        $jumlahUpdate++;
                    } else {
                        $tugasBaru = Tugas::create(array_merge($syncData, ['kelas_id' => $kelasLainId]));
                        $this->createTugasSiswaForKelas($tugasBaru, $kelasLainId, $mataPelajaran);
                        $jumlahDuplikasi++;
                    }
                }
            }
        }

        $msg = 'Tugas berhasil diperbarui';
        if ($jumlahDuplikasi > 0 || $jumlahUpdate > 0) {
            $msg .= " (Disinkronisasi ke " . ($jumlahDuplikasi + $jumlahUpdate) . " kelas lain)";
        }

        return redirect()
            ->route('guru.lms.tugas.index', [$kelasId, $mapelId])
            ->with('success', $msg);
    }

    /**
     * Hapus tugas
     */
    public function destroy(Request $request, $kelasId, $mapelId, $id): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $tugas = Tugas::where('id', $id)
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id)
            ->firstOrFail();

        $filesToDelete = [];
        $idsToDelete = [$tugas->id];

        // BULK DELETE LOGIC
        if ($request->has('hapus_terkait')) {
            $relatedTugas = Tugas::where('guru_id', $tenagaPendidik->id)
                ->where('mata_pelajaran_id', $mapelId)
                ->where('judul_tugas', $tugas->judul_tugas)
                ->where('id', '!=', $tugas->id)
                ->get();

            foreach ($relatedTugas as $rel) {
                $idsToDelete[] = $rel->id;
            }
        }

        // Process Deletion
        $tugasToDelete = Tugas::whereIn('id', $idsToDelete)->get();

        foreach ($tugasToDelete as $t) {
            if ($t->file_tugas) {
                // SAFE DELETE: Cek apakah file digunakan oleh tugas yang TIDAK akan dihapus
                $usageCount = Tugas::where('file_tugas', $t->file_tugas)
                    ->whereNotIn('id', $idsToDelete)
                    ->count();

                if ($usageCount === 0) {
                    $filesToDelete[] = $t->file_tugas;
                }
            }
            $t->delete();
        }

        // Delete physical files (unique paths only)
        $filesToDelete = array_unique($filesToDelete);
        foreach ($filesToDelete as $file) {
            Storage::disk('public')->delete($file);
        }

        $msg = 'Tugas berhasil dihapus';
        if (count($idsToDelete) > 1) {
            $countLain = count($idsToDelete) - 1;
            $msg .= " (termasuk {$countLain} tugas terkait di kelas lain)";
        }

        return redirect()
            ->route('guru.lms.tugas.index', [$kelasId, $mapelId])
            ->with('success', $msg);
    }

    /**
     * Verifikasi akses guru
     */
    private function createTugasSiswaForKelas($tugas, $kelasId, $mataPelajaran)
    {
        $siswaList = Siswa::where('kelas_id', $kelasId)
            ->where('status', 'aktif')
            ->get()
            ->filter(fn($siswa) => $siswa->canAccessMapel($mataPelajaran));

        foreach ($siswaList as $siswa) {
            TugasSiswa::create([
                'tugas_id' => $tugas->id,
                'siswa_id' => $siswa->id,
                'status' => 'belum_dikerjakan',
            ]);
        }
    }

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
