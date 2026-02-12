<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAjaran;

class PromotionSettingsController extends Controller
{
    public function index(): View
    {
        $tahunActive = TahunAjaran::where('is_active', true)->firstOrFail();
        
        $setting = DB::table('pengaturan_naik_kelas')
            ->where('tahun_ajaran_id', $tahunActive->id)
            ->first();

        return view('admin.akademik.promotion.settings', [
            'tahun' => $tahunActive,
            'setting' => $setting
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'tanggal_pengambilan_rapor' => 'required|date',
            'tanggal_eksekusi' => 'nullable|date|after_or_equal:tanggal_pengambilan_rapor',
            'persentase_minimal_tuntas' => 'required|integer|min:0|max:100',
        ]);

        DB::table('pengaturan_naik_kelas')->updateOrInsert(
            ['tahun_ajaran_id' => $validated['tahun_ajaran_id']],
            [
                'tanggal_pengambilan_rapor' => $validated['tanggal_pengambilan_rapor'],
                'tanggal_eksekusi' => $validated['tanggal_eksekusi'],
                'persentase_minimal_tuntas' => $validated['persentase_minimal_tuntas'],
                'updated_at' => now(),
                'created_at' => now(), // only on insert
            ]
        );

        return redirect()
            ->route('admin.akademik.promotion.settings.index')
            ->with('success', 'Pengaturan Naik Kelas berhasil disimpan');
    }
}
