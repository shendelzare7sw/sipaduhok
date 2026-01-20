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
use App\Models\Pertemuan;

class GuruPertemuanController extends Controller
{
    /**
     * Tampilkan kalender dan daftar pertemuan
     */
    public function index(Request $request, $kelasId, $mapelId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);

        // Filter Pekan
        $pekan = $request->get('pekan', 'all'); // Default tampilkan semua jika tidak ada filter

        $query = Pertemuan::where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->with(['materi', 'tugas', 'ujian', 'forumDiskusi'])
            ->orderBy('pekan', 'desc') // Group by Pekan desc
            ->orderBy('tanggal', 'desc');

        if ($pekan !== 'all') {
            $query->where('pekan', $pekan);
        }

        $pertemuanList = $query->get()->groupBy('pekan');

        // Events for calendar (optional)
        $allPertemuans = Pertemuan::where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)->get();

        $events = $allPertemuans->map(function ($p) {
            return [
                'title' => "Pekan $p->pekan: $p->judul",
                'start' => $p->tanggal->format('Y-m-d'),
                'url' => route('guru.lms.pertemuan.show', [$p->kelas_id, $p->mata_pelajaran_id, $p->id]),
            ];
        });

        // Get available weeks for filter
        $availableWeeks = Pertemuan::where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->distinct()
            ->pluck('pekan')
            ->sort()
            ->values();

        return view('guru.lms.pertemuan.index', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'guru' => $tenagaPendidik,
            'pertemuanList' => $pertemuanList, // Grouped by Pekan
            'events' => $events,
            'currentPekan' => $pekan,
            'availableWeeks' => $availableWeeks,
        ]);
    }

    /**
     * Tampilkan form untuk membuat pertemuan baru
     */
    public function create($kelasId, $mapelId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);

        return view('guru.lms.pertemuan.create', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'guru' => $tenagaPendidik,
        ]);
    }

    /**
     * Simpan pertemuan baru
     */
    public function store(Request $request, $kelasId, $mapelId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'pekan' => 'required|integer|min:1|max:20',
            'tanggal' => 'required|date',
            'deskripsi' => 'nullable|string',
            'zoom_link' => 'nullable|url',
        ]);

        Pertemuan::create([
            'kelas_id' => $kelasId,
            'mata_pelajaran_id' => $mapelId,
            'guru_id' => $tenagaPendidik->id,
            'judul' => $validated['judul'],
            'pekan' => $validated['pekan'],
            'tanggal' => $validated['tanggal'],
            'deskripsi' => $validated['deskripsi'],
            'zoom_link' => $validated['zoom_link'],
        ]);

        return back()->with('success', 'Pertemuan berhasil dibuat');
    }

    /**
     * Show pertemuan detail (opsional, untuk manage resource dalam pertemuan)
     */
    public function show($kelasId, $mapelId, $pertemuanId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);
        $pertemuan = Pertemuan::with(['materi', 'tugas', 'ujian', 'forumDiskusi'])
            ->findOrFail($pertemuanId);

        return view('guru.lms.pertemuan.show', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'guru' => $tenagaPendidik,
            'pertemuan' => $pertemuan,
        ]);
    }

    /**
     * Hapus pertemuan
     */
    public function destroy($kelasId, $mapelId, $pertemuanId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $pertemuan = Pertemuan::findOrFail($pertemuanId);
        $pertemuan->delete();

        return back()->with('success', 'Pertemuan berhasil dihapus');
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
