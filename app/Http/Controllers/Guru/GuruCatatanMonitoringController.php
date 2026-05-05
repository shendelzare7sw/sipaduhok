<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\CatatanMonitoring;
use App\Models\TenagaPendidik;
use Illuminate\Http\Request;

class GuruCatatanMonitoringController extends Controller
{
    public function index(Request $request)
    {
        $tp = TenagaPendidik::where('user_id', auth()->id())->first();
        if (!$tp) {
            abort(404, 'Data guru tidak ditemukan.');
        }

        $catatan = CatatanMonitoring::with(['pengirim', 'kelas', 'mataPelajaran'])
            ->forGuru($tp->id)
            ->latest()
            ->paginate(15);

        return view('guru.lms.catatan-monitoring.index', compact('catatan'));
    }

    public function show($id)
    {
        $tp = TenagaPendidik::where('user_id', auth()->id())->first();
        if (!$tp) {
            abort(404, 'Data guru tidak ditemukan.');
        }

        $catatan = CatatanMonitoring::with(['pengirim', 'kelas', 'mataPelajaran'])
            ->forGuru($tp->id)
            ->findOrFail($id);

        $catatan->markAsRead();

        $konten = $catatan->konten();

        return view('guru.lms.catatan-monitoring.show', compact('catatan', 'konten'));
    }
}
