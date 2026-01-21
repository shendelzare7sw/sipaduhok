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
    public function index($kelasId, $mapelId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);

        $meetings = LmsMeeting::where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id)
            ->orderBy('waktu_mulai', 'desc')
            ->paginate(10);

        return view('guru.lms.meeting.index', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'meetings' => $meetings,
            'guru' => $tenagaPendidik,
        ]);
    }

    public function create($kelasId, $mapelId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);

        return view('guru.lms.meeting.create', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'guru' => $tenagaPendidik,
        ]);
    }

    public function store(Request $request, $kelasId, $mapelId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'platform' => 'required|in:zoom,google_meet,lainnya',
            'link_meeting' => 'required|string',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'nullable|date|after:waktu_mulai',
            'deskripsi' => 'nullable|string',
        ]);

        LmsMeeting::create([
            'kelas_id' => $kelasId,
            'mata_pelajaran_id' => $mapelId,
            'guru_id' => $tenagaPendidik->id,
            'judul' => $validated['judul'],
            'platform' => $validated['platform'],
            'link_meeting' => $validated['link_meeting'],
            'waktu_mulai' => $validated['waktu_mulai'],
            'waktu_selesai' => $validated['waktu_selesai'],
            'deskripsi' => $validated['deskripsi'],
        ]);

        return redirect()
            ->route('guru.lms.meeting.index', [$kelasId, $mapelId])
            ->with('success', 'Meeting berhasil dijadwalkan');
    }

    public function edit($kelasId, $mapelId, $id): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $meeting = LmsMeeting::where('id', $id)
            ->where('guru_id', $tenagaPendidik->id)
            ->firstOrFail();

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);

        return view('guru.lms.meeting.edit', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'meeting' => $meeting,
            'guru' => $tenagaPendidik,
        ]);
    }

    public function update(Request $request, $kelasId, $mapelId, $id): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $meeting = LmsMeeting::where('id', $id)
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

        $meeting->update($validated);

        return redirect()
            ->route('guru.lms.meeting.index', [$kelasId, $mapelId])
            ->with('success', 'Meeting berhasil diperbarui');
    }

    public function destroy($kelasId, $mapelId, $id): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $meeting = LmsMeeting::where('id', $id)
            ->where('guru_id', $tenagaPendidik->id)
            ->firstOrFail();

        $meeting->delete();

        return redirect()
            ->route('guru.lms.meeting.index', [$kelasId, $mapelId])
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
