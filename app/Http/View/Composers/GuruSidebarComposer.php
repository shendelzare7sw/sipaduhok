<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\TenagaPendidik;
use App\Models\GuruPengajarKelas;
use App\Models\TahunAjaran;

class GuruSidebarComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view)
    {
        // Ensure user is authenticated
        if (!auth()->check() || !auth()->user()->isGuruPengajar()) {
            return;
        }

        $guru = TenagaPendidik::where('user_id', auth()->id())->first();

        if ($guru) {
            // Scope sidebar ke TA aktif. Untuk akses kelas/mapel TA lama,
            // guru pakai menu "Arsip LMS".
            $taAktifId = TahunAjaran::where('is_active', true)->value('id');

            $sidebarKelas = GuruPengajarKelas::where('tenaga_pendidik_id', $guru->id)
                ->when($taAktifId, fn($q) => $q->whereHas('kelas', fn($k) => $k->where('tahun_ajaran_id', $taAktifId)))
                ->with(['kelas', 'mataPelajaran'])
                ->get()
                ->groupBy('kelas_id');

            $view->with('sidebarKelas', $sidebarKelas);
        }
    }
}
