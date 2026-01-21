<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\LmsMeeting;

class SiswaLmsMeetingController extends Controller
{
    public function index($mapelId): View
    {
        $siswa = auth()->user()->siswa;

        // Ensure siswa belongs to the class
        $kelasId = $siswa->kelas_id;

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);

        $meetings = LmsMeeting::where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('is_active', true)
            ->orderBy('waktu_mulai', 'asc')
            ->paginate(10);

        return view('siswa.lms.meeting.index', [
            'kelas' => $kelas,
            'mataPelajaran' => $mataPelajaran,
            'meetings' => $meetings,
        ]);
    }
}
