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
            ->orderBy('tanggal_upload', 'desc')
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

        return view('guru.lms.materi.create', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'guru' => $tenagaPendidik,
            'kategori' => $kategori,
        ]);
    }

    /**
     * Simpan materi baru
     */
    public function store(Request $request, $kelasId, $mapelId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $validated = $request->validate([
            'judul_materi' => 'required|string|max:255',
            'kategori' => 'required|in:materi,modul_ajar',
            'deskripsi' => 'nullable|string',
            'file_materi' => 'nullable|file|max:51200', // 50MB
            'tipe_file' => 'required|in:pdf,video,ppt,doc,link',
        ]);

        $filePath = null;
        if ($request->hasFile('file_materi')) {
            $filePath = $request->file('file_materi')->store('materi', 'public');
        }

        Materi::create([
            'kelas_id' => $kelasId,
            'mata_pelajaran_id' => $mapelId,
            'guru_id' => $tenagaPendidik->id,
            'judul_materi' => $validated['judul_materi'],
            'kategori' => $validated['kategori'],
            'deskripsi' => $validated['deskripsi'],
            'file_materi' => $filePath,
            'tipe_file' => $validated['tipe_file'],
            'tanggal_upload' => now(),
        ]);

        return redirect()
            ->route('guru.lms.materi.index', [$kelasId, $mapelId])
            ->with('success', 'Materi berhasil ditambahkan');
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

        return view('guru.lms.materi.edit', [
            'materi' => $materi,
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'guru' => $tenagaPendidik,
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

        $validated = $request->validate([
            'judul_materi' => 'required|string|max:255',
            'kategori' => 'required|in:materi,modul_ajar',
            'deskripsi' => 'nullable|string',
            'file_materi' => 'nullable|file|max:51200',
            'tipe_file' => 'required|in:pdf,video,ppt,doc,link',
        ]);

        if ($request->hasFile('file_materi')) {
            // Hapus file lama
            if ($materi->file_materi) {
                Storage::disk('public')->delete($materi->file_materi);
            }

            $validated['file_materi'] = $request->file('file_materi')->store('materi', 'public');
        }

        $materi->update($validated);

        return redirect()
            ->route('guru.lms.materi.index', [$kelasId, $mapelId])
            ->with('success', 'Materi berhasil diperbarui');
    }

    /**
     * Hapus materi
     */
    public function destroy($kelasId, $mapelId, $id): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $materi = Materi::where('id', $id)
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id)
            ->firstOrFail();

        // Hapus file
        if ($materi->file_materi) {
            Storage::disk('public')->delete($materi->file_materi);
        }

        $materi->delete();

        return redirect()
            ->route('guru.lms.materi.index', [$kelasId, $mapelId])
            ->with('success', 'Materi berhasil dihapus');
    }

    /**
     * Verifikasi akses guru
     */
    private function verifyAccess($guruId, $kelasId, $mapelId)
    {
        $access = GuruPengajarKelas::where('tenaga_pendidik_id', $guruId)
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->exists();

        if (!$access) {
            abort(403, 'Anda tidak memiliki akses ke mata pelajaran ini');
        }
    }
}
