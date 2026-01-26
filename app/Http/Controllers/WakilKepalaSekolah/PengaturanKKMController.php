<?php

namespace App\Http\Controllers\WakilKepalaSekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAjaran;
use App\Models\MataPelajaran;

class PengaturanKKMController extends Controller
{
    public function index(Request $request): View
    {
        $tahunActive = TahunAjaran::where('is_active', true)->firstOrFail();
        
        // Filter jenjang if needed, default SMP
        $jenjang = $request->get('jenjang', 'SMP');
        
        $mapelList = MataPelajaran::where('jenjang', $jenjang)->get();
        
        // Get existing KKM
        $existingKKM = DB::table('pengaturan_kkm')
            ->where('tahun_ajaran_id', $tahunActive->id)
            ->where('jenjang', $jenjang)
            ->pluck('nilai_kkm', 'mata_pelajaran_id');

        return view('waka.akademik.promotion.kkm', [
            'tahun' => $tahunActive,
            'mapelList' => $mapelList,
            'existingKKM' => $existingKKM,
            'jenjang' => $jenjang
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'jenjang' => 'required|in:PAUD,SD,SMP,SMA,SMK',
            'kkm' => 'required|array',
            'kkm.*' => 'required|integer|min:0|max:100',
        ]);

        foreach ($validated['kkm'] as $mapelId => $nilai) {
            DB::table('pengaturan_kkm')->updateOrInsert(
                [
                    'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
                    'jenjang' => $validated['jenjang'],
                    'mata_pelajaran_id' => $mapelId,
                ],
                [
                    'nilai_kkm' => $nilai,
                    'updated_at' => now(),
                    'created_at' => now(), // only on insert
                ]
            );
        }

        return redirect()
            ->route('waka.promotion.kkm.index', ['jenjang' => $validated['jenjang']])
            ->with('success', 'Pengaturan KKM berhasil disimpan');
    }
}
