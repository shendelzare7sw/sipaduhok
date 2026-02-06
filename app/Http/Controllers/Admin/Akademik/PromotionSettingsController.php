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

        DB::transaction(function () use ($validated) {
            // 1. Save Settings
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

            // 2. Sync Schedule
            $schedule = \App\Models\PromotionSchedule::where('tahun_ajaran_id', $validated['tahun_ajaran_id'])
                ->where('status', 'PENDING')
                ->first();

            if ($validated['tanggal_eksekusi']) {
                $executionTime = \Carbon\Carbon::parse($validated['tanggal_eksekusi'])->setTime(0, 0, 0); // Default to midnight or keep user time if input was datetime
                // Note: The input type in view is 'date', so let's default to a reasonable time, e.g., 01:00 AM the next day or same day?
                // Usually automatic jobs run at night. Let's set it to 02:00 AM on that date.
                $executionTime = $executionTime->addHours(2); 

                if ($schedule) {
                    $schedule->update([
                        'scheduled_at' => $executionTime,
                        'created_by' => auth()->id(), // Update creator to current user
                        'updated_at' => now(),
                    ]);
                } else {
                    \App\Models\PromotionSchedule::create([
                        'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
                        'scheduled_at' => $executionTime,
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

        return redirect()
            ->route('admin.akademik.promotion.settings.index')
            ->with('success', 'Pengaturan Naik Kelas berhasil disimpan & Jadwal Otomatis diperbarui.');
    }
}
