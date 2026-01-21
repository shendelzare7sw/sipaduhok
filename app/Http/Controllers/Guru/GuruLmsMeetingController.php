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
use App\Models\LmsMeeting;

class GuruLmsMeetingController extends Controller
{
    public function index($kelas, $mapel): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelas, $mapel);

        $kelasModel = Kelas::findOrFail($kelas);
        $mataPelajaran = MataPelajaran::findOrFail($mapel);

        $meetings = LmsMeeting::where('kelas_id', $kelas)
            ->where('mata_pelajaran_id', $mapel)
            ->where('guru_id', $tenagaPendidik->id)
            ->orderBy('waktu_mulai', 'desc')
            ->paginate(10);

        return view('guru.lms.meeting.index', [
            'kelas' => $kelasModel,
            'mapel' => $mataPelajaran,
            'meetings' => $meetings,
            'guru' => $tenagaPendidik,
        ]);
    }

    public function create($kelas, $mapel): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelas, $mapel);

        $kelasModel = Kelas::findOrFail($kelas);
        $mataPelajaran = MataPelajaran::findOrFail($mapel);

        return view('guru.lms.meeting.create', [
            'kelas' => $kelasModel,
            'mapel' => $mataPelajaran,
            'guru' => $tenagaPendidik,
        ]);
    }

    public function store(Request $request, $kelas, $mapel): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelas, $mapel);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'platform' => 'required|in:zoom,google_meet,lainnya',
            'link_meeting' => 'required|string',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'nullable|date|after:waktu_mulai',
            'deskripsi' => 'nullable|string',
        ]);

        LmsMeeting::create([
            'kelas_id' => $kelas,
            'mata_pelajaran_id' => $mapel,
            'guru_id' => $tenagaPendidik->id,
            'judul' => $validated['judul'],
            'platform' => $validated['platform'],
            'link_meeting' => $validated['link_meeting'],
            'waktu_mulai' => $validated['waktu_mulai'],
            'waktu_selesai' => $validated['waktu_selesai'],
            'deskripsi' => $validated['deskripsi'],
        ]);

        return redirect()
            ->route('guru.lms.meeting.index', [$kelas, $mapel])
            ->with('success', 'Meeting berhasil dijadwalkan');
    }

    public function edit($kelas, $mapel, $meeting): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelas, $mapel);

        $meetingModel = LmsMeeting::where('id', $meeting)
            ->where('guru_id', $tenagaPendidik->id)
            ->firstOrFail();

        $kelasModel = Kelas::findOrFail($kelas);
        $mataPelajaran = MataPelajaran::findOrFail($mapel);

        return view('guru.lms.meeting.edit', [
            'kelas' => $kelasModel,
            'mapel' => $mataPelajaran,
            'meeting' => $meetingModel,
            'guru' => $tenagaPendidik,
        ]);
    }

    public function update(Request $request, $kelas, $mapel, $meeting): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelas, $mapel);

        $meetingModel = LmsMeeting::where('id', $meeting)
            ->where('guru_id', $tenagaPendidik->id)
            ->firstOrFail();

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'platform' => 'required|in:zoom,google_meet,lainnya',
            'link_meeting' => 'required|string',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'nullable|date|after:waktu_mulai',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $meetingModel->update($validated);

        return redirect()
            ->route('guru.lms.meeting.index', [$kelas, $mapel])
            ->with('success', 'Meeting berhasil diperbarui');
    }

    public function destroy($kelas, $mapel, $meeting): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelas, $mapel);

        $meetingModel = LmsMeeting::where('id', $meeting)
            ->where('guru_id', $tenagaPendidik->id)
            ->firstOrFail();

        $meetingModel->delete();

        return redirect()
            ->route('guru.lms.meeting.index', [$kelas, $mapel])
            ->with('success', 'Meeting berhasil dihapus');
    }

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
