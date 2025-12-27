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
use App\Models\Ujian;
use App\Models\UjianSiswa;
use App\Models\SoalUjian;
use App\Models\Siswa;

class GuruUjianController extends Controller
{
    /**
     * Tampilkan daftar ujian
     */
    public function index($kelasId, $mapelId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);
        
        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);
        
        $ujianList = Ujian::where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id)
            ->orderBy('tanggal_mulai', 'desc')
            ->paginate(10);
        
        return view('guru.lms.ujian.index', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'ujianList' => $ujianList,
            'guru' => $tenagaPendidik,
        ]);
    }
    
    /**
     * Form tambah ujian
     */
    public function create($kelasId, $mapelId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);
        
        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);
        
        return view('guru.lms.ujian.create', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'guru' => $tenagaPendidik,
        ]);
    }
    
    /**
     * Simpan ujian baru
     */
    public function store(Request $request, $kelasId, $mapelId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);
        
        $validated = $request->validate([
            'judul_ujian' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe_ujian' => 'required|in:harian,uts,uas',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'durasi_menit' => 'required|integer|min:1',
        ]);
        
        $ujian = Ujian::create([
            'kelas_id' => $kelasId,
            'mata_pelajaran_id' => $mapelId,
            'guru_id' => $tenagaPendidik->id,
            'judul_ujian' => $validated['judul_ujian'],
            'deskripsi' => $validated['deskripsi'],
            'tipe_ujian' => $validated['tipe_ujian'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'durasi_menit' => $validated['durasi_menit'],
        ]);
        
        // Buat UjianSiswa untuk setiap siswa di kelas
        $siswaList = Siswa::where('kelas_id', $kelasId)
            ->where('status', 'aktif')
            ->get();
        
        foreach ($siswaList as $siswa) {
            UjianSiswa::create([
                'ujian_id' => $ujian->id,
                'siswa_id' => $siswa->id,
                'status' => 'belum_mulai',
            ]);
        }
        
        return redirect()
            ->route('guru.lms.ujian.index', [$kelasId, $mapelId])
            ->with('success', 'Ujian berhasil ditambahkan');
    }
    
    /**
     * Form edit ujian
     */
    public function edit($kelasId, $mapelId, $id): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);
        
        $ujian = Ujian::where('id', $id)
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id)
            ->firstOrFail();
        
        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);
        
        return view('guru.lms.ujian.edit', [
            'ujian' => $ujian,
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'guru' => $tenagaPendidik,
        ]);
    }
    
    /**
     * Update ujian
     */
    public function update(Request $request, $kelasId, $mapelId, $id): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);
        
        $ujian = Ujian::where('id', $id)
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id)
            ->firstOrFail();
        
        $validated = $request->validate([
            'judul_ujian' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe_ujian' => 'required|in:harian,uts,uas',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'durasi_menit' => 'required|integer|min:1',
        ]);
        
        $ujian->update($validated);
        
        return redirect()
            ->route('guru.lms.ujian.index', [$kelasId, $mapelId])
            ->with('success', 'Ujian berhasil diperbarui');
    }
    
    /**
     * Hapus ujian
     */
    public function destroy($kelasId, $mapelId, $id): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);
        
        $ujian = Ujian::where('id', $id)
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id)
            ->firstOrFail();
        
        $ujian->delete();
        
        return redirect()
            ->route('guru.lms.ujian.index', [$kelasId, $mapelId])
            ->with('success', 'Ujian berhasil dihapus');
    }
    
    /**
     * Lihat hasil ujian
     */
    public function hasil($kelasId, $mapelId, $id): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);
        
        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);
        $ujian = Ujian::findOrFail($id);
        
        $results = UjianSiswa::with('siswa')
            ->where('ujian_id', $id)
            ->get();
        
        // Statistik
        $stats = [
            'total' => $results->count(),
            'selesai' => $results->where('status', 'selesai')->count(),
            'sedang_mengerjakan' => $results->where('status', 'sedang_mengerjakan')->count(),
            'belum_mulai' => $results->where('status', 'belum_mulai')->count(),
            'rata_rata' => $results->where('status', 'selesai')->avg('nilai') ?? 0,
            'nilai_tertinggi' => $results->where('status', 'selesai')->max('nilai') ?? 0,
            'nilai_terendah' => $results->where('status', 'selesai')->min('nilai') ?? 0,
        ];
        
        return view('guru.lms.ujian.hasil', [
            'kelas' => $kelas,
            'mataPelajaran' => $mataPelajaran,
            'ujian' => $ujian,
            'results' => $results,
            'stats' => $stats,
            'guru' => $tenagaPendidik,
        ]);
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