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
                'tugasSiswa as submitted_count' => function($query) {
                    $query->where('status', '!=', 'belum_dikerjakan');
                }
            ])
            ->orderBy('tanggal_mulai', 'desc')
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
    public function create($kelasId, $mapelId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);
        
        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);
        
        return view('guru.lms.tugas.create', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'guru' => $tenagaPendidik,
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
            'file_tugas' => 'nullable|file|max:10240', // 10MB
            'tanggal_mulai' => 'required|date',
            'tanggal_deadline' => 'required|date|after:tanggal_mulai',
        ]);
        
        $filePath = null;
        if ($request->hasFile('file_tugas')) {
            $filePath = $request->file('file_tugas')->store('tugas', 'public');
        }
        
        $tugas = Tugas::create([
            'kelas_id' => $kelasId,
            'mata_pelajaran_id' => $mapelId,
            'guru_id' => $tenagaPendidik->id,
            'judul_tugas' => $validated['judul_tugas'],
            'deskripsi' => $validated['deskripsi'],
            'file_tugas' => $filePath,
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_deadline' => $validated['tanggal_deadline'],
        ]);
        
        // Buat TugasSiswa untuk setiap siswa di kelas
        $siswaList = Siswa::where('kelas_id', $kelasId)
            ->where('status', 'aktif')
            ->get();
        
        foreach ($siswaList as $siswa) {
            TugasSiswa::create([
                'tugas_id' => $tugas->id,
                'siswa_id' => $siswa->id,
                'status' => 'belum_dikerjakan',
            ]);
        }
        
        return redirect()
            ->route('guru.lms.tugas.index', [$kelasId, $mapelId])
            ->with('success', 'Tugas berhasil ditambahkan');
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
        
        return view('guru.lms.tugas.edit', [
            'tugas' => $tugas,
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'guru' => $tenagaPendidik,
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
        
        $validated = $request->validate([
            'judul_tugas' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'file_tugas' => 'nullable|file|max:10240',
            'tanggal_mulai' => 'required|date',
            'tanggal_deadline' => 'required|date|after:tanggal_mulai',
        ]);
        
        if ($request->hasFile('file_tugas')) {
            if ($tugas->file_tugas) {
                Storage::disk('public')->delete($tugas->file_tugas);
            }
            
            $validated['file_tugas'] = $request->file('file_tugas')->store('tugas', 'public');
        }
        
        $tugas->update($validated);
        
        return redirect()
            ->route('guru.lms.tugas.index', [$kelasId, $mapelId])
            ->with('success', 'Tugas berhasil diperbarui');
    }
    
    /**
     * Hapus tugas
     */
    public function destroy($kelasId, $mapelId, $id): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);
        
        $tugas = Tugas::where('id', $id)
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id)
            ->firstOrFail();
        
        if ($tugas->file_tugas) {
            Storage::disk('public')->delete($tugas->file_tugas);
        }
        
        $tugas->delete();
        
        return redirect()
            ->route('guru.lms.tugas.index', [$kelasId, $mapelId])
            ->with('success', 'Tugas berhasil dihapus');
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