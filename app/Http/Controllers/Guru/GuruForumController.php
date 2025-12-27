<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\TenagaPendidik;
use App\Models\GuruPengajarKelas;
use App\Models\Kelas;
use App\Models\MataPelajaran;

class GuruForumController extends Controller
{
    /**
     * Tampilkan forum diskusi
     * 
     * Note: Full forum functionality requires additional tables:
     * - forum_threads (id, kelas_id, mapel_id, user_id, title, created_at)
     * - forum_posts (id, thread_id, user_id, content, created_at)
     */
    public function index($kelasId, $mapelId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);
        
        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);
        
        // TODO: Implement forum threads and posts
        // For now, return placeholder view
        
        return view('guru.lms.forum.index', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'guru' => $tenagaPendidik,
        ]);
    }
    
    /**
     * Verifikasi akses guru
     */
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