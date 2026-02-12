<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Rapor;
use App\Models\Nilai;
use Barryvdh\DomPDF\Facade\Pdf;

class SiaRaporController extends Controller
{
    /**
     * Halaman utama rapor
     */
    public function index()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with('kelas.tahunAjaran')->first();

        if (!$siswa) {
            return redirect()->route('siswa.sia.dashboard')
                ->with('error', 'Data siswa tidak ditemukan');
        }

        // BLOCK ACCESS FOR STUDENT ROLE
        // User request: "orang tua yang nantinya akan diberi akses melihat rapor bukan siswa"
        // We redirect them back with a message.
        if (!Auth::user()->hasRole('orang_tua')) { 
             // Double check if this controller is shared. The route middleware is 'role:siswa'.
             // If parents use this, checking role is good. 
             // But wait, parents use OrangTuaController.
             // So this controller is ONLY for students.
             return redirect()->route('siswa.sia.dashboard')
                ->with('error', 'Akses Rapor hanya diperuntukkan bagi Orang Tua/Wali.');
        }

        // Ambil rapor yang sudah diterbitkan
        $raporList = Rapor::where('siswa_id', $siswa->id)
            ->where('status', 'diterbitkan')
            ->with('tahunAjaran')
            ->orderBy('tahun_ajaran_id', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        // Cek validasi akses rapor (3-level: Bendahara, Wali Kelas, Ketua PKBM)
        $aksesRapor = [
            'bendahara' => $siswa->validasi_rapor_bendahara ?? false,
            'wali' => $siswa->validasi_rapor_wali ?? false,
            'ketua' => $siswa->validasi_rapor_ketua ?? false,
        ];

        $bolehLihat = $siswa->hasFullRaporAccess();

        return view('siswa.sia.rapor.index', compact('siswa', 'raporList', 'aksesRapor', 'bolehLihat'));
    }

    /**
     * Rapor Tengah Semester
     */
    public function tengahSemester($raporId)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return redirect()->route('siswa.sia.dashboard')
                ->with('error', 'Data siswa tidak ditemukan');
        }

        // Cek validasi akses (3-level validation required)
        if (!$siswa->hasFullRaporAccess()) {
            return redirect()->route('siswa.sia.rapor.index')
                ->with('error', 'Belum Memiliki Akses Rapor. Rapor harus divalidasi oleh Bendahara, Wali Kelas, dan Ketua PKBM.');
        }

        $rapor = Rapor::where('id', $raporId)
            ->where('siswa_id', $siswa->id)
            ->where('status', 'diterbitkan')
            ->with(['raporNilai.mataPelajaran', 'kelas', 'tahunAjaran'])
            ->firstOrFail();

        // Hitung rata-rata
        $rataRata = $rapor->raporNilai->avg('nilai_angka');

        return view('siswa.sia.rapor.tengah-semester', compact('siswa', 'rapor', 'rataRata'));
    }

    /**
     * Rapor Akhir Semester
     */
    public function akhirSemester($raporId)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return redirect()->route('siswa.sia.dashboard')
                ->with('error', 'Data siswa tidak ditemukan');
        }

        // Cek validasi akses (3-level validation required)
        if (!$siswa->hasFullRaporAccess()) {
            return redirect()->route('siswa.sia.rapor.index')
                ->with('error', 'Belum Memiliki Akses Rapor. Rapor harus divalidasi oleh Bendahara, Wali Kelas, dan Ketua PKBM.');
        }

        $rapor = Rapor::where('id', $raporId)
            ->where('siswa_id', $siswa->id)
            ->where('status', 'diterbitkan')
            ->with(['raporNilai.mataPelajaran', 'kelas', 'tahunAjaran'])
            ->firstOrFail();

        // Hitung rata-rata
        $rataRata = $rapor->raporNilai->avg('nilai_angka');

        return view('siswa.sia.rapor.akhir-semester', compact('siswa', 'rapor', 'rataRata'));
    }

    /**
     * Download rapor PDF
     */
    public function download($raporId)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return redirect()->route('siswa.sia.dashboard')
                ->with('error', 'Data siswa tidak ditemukan');
        }

        // Cek validasi akses (3-level validation required)
        if (!$siswa->hasFullRaporAccess()) {
            return redirect()->route('siswa.sia.rapor.index')
                ->with('error', 'Belum Memiliki Akses Rapor. Rapor harus divalidasi oleh Bendahara, Wali Kelas, dan Ketua PKBM.');
        }

        $rapor = Rapor::where('id', $raporId)
            ->where('siswa_id', $siswa->id)
            ->where('status', 'diterbitkan')
            ->with(['raporNilai.mataPelajaran', 'kelas', 'tahunAjaran'])
            ->firstOrFail();

        // Check if download is allowed for this rapor
        if (!$rapor->allow_download) {
            return redirect()->route('siswa.sia.rapor.index')
                ->with('error', 'Download rapor belum diizinkan oleh Wali Kelas.');
        }

        $rataRata = $rapor->raporNilai->avg('nilai_angka');

        $pdf = Pdf::loadView('siswa.sia.rapor.pdf-akhir', compact('siswa', 'rapor', 'rataRata'));

        $filename = 'Rapor_' . $siswa->nama_lengkap . '_' . $rapor->semester . '_' . $rapor->tahunAjaran->nama_tahun_ajaran . '.pdf';

        return $pdf->download($filename);
    }
}