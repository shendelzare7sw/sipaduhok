<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\TenagaPendidik;
use App\Models\JadwalPelajaran;
use Illuminate\Http\Request;

class GuruJadwalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $guru = TenagaPendidik::where('user_id', auth()->id())->first();

        if (!$guru) {
            return redirect()->route('guru.dashboard')->with('error', 'Data guru tidak ditemukan.');
        }

        // Get all schedule for this teacher
        $jadwal = JadwalPelajaran::with(['kelas', 'mataPelajaran', 'tahunAjaran'])
            ->where('guru_id', $guru->id)
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari');

        return view('guru.jadwal.index', compact('guru', 'jadwal'));
    }
}
