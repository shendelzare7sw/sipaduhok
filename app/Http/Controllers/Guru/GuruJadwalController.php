<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\TenagaPendidik;
use App\Models\JadwalPelajaran;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class GuruJadwalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $guru = TenagaPendidik::where('user_id', auth()->id())->first();

        if (!$guru) {
            return redirect()->route('guru.dashboard')->with('error', 'Data guru tidak ditemukan.');
        }

        $tahunAjarans = TahunAjaran::orderByDesc('tanggal_mulai')->get();

        // Default ke TA aktif agar tidak tercampur dengan jadwal TA lama;
        // 0 (dipilih eksplisit lewat dropdown "Semua TA") berarti tanpa filter.
        $taFilterId = $request->has('tahun_ajaran_id')
            ? $request->integer('tahun_ajaran_id')
            : (int) ($tahunAjarans->firstWhere('is_active', true)->id ?? 0);

        $jadwal = JadwalPelajaran::with(['kelas', 'mataPelajaran', 'tahunAjaran'])
            ->where('guru_id', $guru->id)
            ->when($taFilterId, fn ($q) => $q->where('tahun_ajaran_id', $taFilterId))
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari');

        return view('guru.jadwal.index', compact('guru', 'jadwal', 'tahunAjarans', 'taFilterId'));
    }
}
