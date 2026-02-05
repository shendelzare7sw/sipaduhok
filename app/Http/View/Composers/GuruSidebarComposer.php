<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\TenagaPendidik;
use App\Models\GuruPengajarKelas;

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
            // Get classes taught by this teacher, eager load relationships
            $sidebarKelas = GuruPengajarKelas::where('tenaga_pendidik_id', $guru->id)
                ->with(['kelas', 'mataPelajaran'])
                ->get()
                ->groupBy('kelas_id');
                
            $view->with('sidebarKelas', $sidebarKelas);
        }
    }
}
