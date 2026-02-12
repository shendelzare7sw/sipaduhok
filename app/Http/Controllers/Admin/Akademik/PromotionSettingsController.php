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
            'tanggal_eksekusi' => 'nullable|date',
            'waktu_eksekusi' => 'nullable|date_format:H:i',
            'persentase_minimal_tuntas' => 'required|integer|min:0|max:100',
        ]);

        DB::transaction(function () use ($validated, $request) {
            // Parse dates (browser sends yyyy-mm-dd format)
            $tanggalRapor = $validated['tanggal_pengambilan_rapor'];
            
            $tanggalEksekusi = null;
            if ($validated['tanggal_eksekusi']) {
                // Parse date and combine with time
                $date = \Carbon\Carbon::parse($validated['tanggal_eksekusi'], 'Asia/Jakarta');
                
                // If time is provided, set it; otherwise default to 02:00 AM
                if ($validated['waktu_eksekusi']) {
                    [$hour, $minute] = explode(':', $validated['waktu_eksekusi']);
                    $date->setTime((int)$hour, (int)$minute, 0);
                } else {
                    $date->setTime(2, 0, 0); // Default to 2 AM
                }
                $tanggalEksekusi = $date;
            }
            
            // 1. Save Settings
            DB::table('pengaturan_naik_kelas')->updateOrInsert(
                ['tahun_ajaran_id' => $validated['tahun_ajaran_id']],
                [
                    'tanggal_pengambilan_rapor' => $tanggalRapor,
                    'tanggal_eksekusi' => $tanggalEksekusi ? $tanggalEksekusi->format('Y-m-d H:i:s') : null,
                    'persentase_minimal_tuntas' => $validated['persentase_minimal_tuntas'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            // 2. Sync Schedule
            $schedule = \App\Models\PromotionSchedule::where('tahun_ajaran_id', $validated['tahun_ajaran_id'])
                ->where('status', 'PENDING')
                ->first();

            if ($tanggalEksekusi) {
                if ($schedule) {
                    $schedule->update([
                        'scheduled_at' => $tanggalEksekusi,
                        'created_by' => auth()->id(),
                        'updated_at' => now(),
                    ]);
                } else {
                    \App\Models\PromotionSchedule::create([
                        'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
                        'scheduled_at' => $tanggalEksekusi,
                        'status' => 'PENDING',
                        'created_by' => auth()->id(),
                        'notify_on_complete' => true,
                        'notification_email' => auth()->user()->email,
                    ]);
                }
            } else {
                // If date cleared, cancel pending schedule
                if ($schedule) {
                    $schedule->update(['status' => 'CANCELLED']);
                }
            }
        });

        $routePrefix = $request->routeIs('waka.*') ? 'waka.promotion' : 'admin.akademik.promotion';

        return redirect()
            ->route($routePrefix . '.settings.index')
            ->with('success', 'Pengaturan Naik Kelas berhasil disimpan & Jadwal Otomatis diperbarui.');
    }
}
