<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\TenagaPendidik;
use App\Models\Kelas;
use App\Models\JadwalPelajaran;
use App\Models\MataPelajaran;

class JadwalPelajaranController extends Controller
{
    /**
     * Display jadwal pelajaran
     */
    public function index(): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->first();
        $kelas = Kelas::where('wali_kelas_id', $tenagaPendidik->id)->first();

        if (!$kelas) {
            return view('wali-kelas.jadwal.index')->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
        }

        // Get jadwal pelajaran per hari
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jadwalPerHari = [];

        foreach ($hariList as $hari) {
            $jadwalPerHari[$hari] = JadwalPelajaran::where('kelas_id', $kelas->id)
                ->where('hari', $hari)
                ->with(['mataPelajaran', 'guru'])
                ->orderBy('jam_mulai')
                ->get();
        }

        // Get mata pelajaran yang tersedia untuk kelas ini
        $mataPelajaranList = MataPelajaran::where('jenjang', $kelas->jenjang)
            ->where('is_active', true)
            ->get();

        // Get guru pengajar yang tersedia
        $guruList = TenagaPendidik::whereHas('user', function($q) {
            $q->where('role', 'guru_pengajar')
              ->where('is_active', true);
        })->get();

        return view('wali-kelas.jadwal.index', [
            'kelas' => $kelas,
            'jadwalPerHari' => $jadwalPerHari,
            'hariList' => $hariList,
            'mataPelajaranList' => $mataPelajaranList,
            'guruList' => $guruList,
        ]);
    }

    /**
     * Store jadwal pelajaran baru
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'guru_id' => 'required|exists:tenaga_pendidik,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        // Cek apakah ada jadwal yang bentrok
        $bentrok = JadwalPelajaran::where('kelas_id', $request->kelas_id)
            ->where('hari', $request->hari)
            ->where(function($q) use ($request) {
                $q->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                  ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                  ->orWhere(function($q2) use ($request) {
                      $q2->where('jam_mulai', '<=', $request->jam_mulai)
                         ->where('jam_selesai', '>=', $request->jam_selesai);
                  });
            })
            ->exists();

        if ($bentrok) {
            return back()->with('error', 'Jadwal bentrok dengan jadwal yang sudah ada!');
        }

        JadwalPelajaran::create($request->all());

        return redirect()->route('wali.jadwal.index')
            ->with('success', 'Jadwal pelajaran berhasil ditambahkan!');
    }

    /**
     * Update jadwal pelajaran
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'guru_id' => 'required|exists:tenaga_pendidik,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        $jadwal = JadwalPelajaran::findOrFail($id);

        // Cek apakah ada jadwal yang bentrok (kecuali jadwal ini sendiri)
        $bentrok = JadwalPelajaran::where('kelas_id', $jadwal->kelas_id)
            ->where('hari', $request->hari)
            ->where('id', '!=', $id)
            ->where(function($q) use ($request) {
                $q->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                  ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                  ->orWhere(function($q2) use ($request) {
                      $q2->where('jam_mulai', '<=', $request->jam_mulai)
                         ->where('jam_selesai', '>=', $request->jam_selesai);
                  });
            })
            ->exists();

        if ($bentrok) {
            return back()->with('error', 'Jadwal bentrok dengan jadwal yang sudah ada!');
        }

        $jadwal->update($request->all());

        return redirect()->route('wali.jadwal.index')
            ->with('success', 'Jadwal pelajaran berhasil diperbarui!');
    }

    /**
     * Delete jadwal pelajaran
     */
    public function destroy($id): RedirectResponse
    {
        $jadwal = JadwalPelajaran::findOrFail($id);
        $jadwal->delete();

        return redirect()->route('wali.jadwal.index')
            ->with('success', 'Jadwal pelajaran berhasil dihapus!');
    }

    /**
     * Print jadwal pelajaran
     */
    public function print()
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->first();
        $kelas = Kelas::where('wali_kelas_id', $tenagaPendidik->id)->first();

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jadwalPerHari = [];

        foreach ($hariList as $hari) {
            $jadwalPerHari[$hari] = JadwalPelajaran::where('kelas_id', $kelas->id)
                ->where('hari', $hari)
                ->with(['mataPelajaran', 'guru'])
                ->orderBy('jam_mulai')
                ->get();
        }

        return view('wali-kelas.jadwal.print', [
            'kelas' => $kelas,
            'jadwalPerHari' => $jadwalPerHari,
            'hariList' => $hariList,
        ]);
    }
}