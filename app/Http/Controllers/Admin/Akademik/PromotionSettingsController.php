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
            'setting' => $setting,
            'promotionReadiness' => $this->checkPromotionReadiness($tahunActive),
        ]);
    }

    /**
     * Sama seperti PromotionReportController::index() - dipakai supaya halaman
     * Pengaturan juga memperingatkan admin SEBELUM mereka set jadwal eksekusi
     * otomatis, bukan cuma di halaman Proses & Rekap. Kalau TA/kelas baru belum
     * siap, jadwal yang di-set di sini akan tetap tersimpan tapi eksekusinya nanti
     * gagal diam-diam (siswa NAIK_KELAS di riwayat tapi kelas_id tidak pernah
     * pindah, karena findNextClass() tidak menemukan kelas tujuan).
     */
    private function checkPromotionReadiness(TahunAjaran $activeYear): array
    {
        $nextTahunAjaran = TahunAjaran::where('is_active', false)
            ->where('tanggal_mulai', '>', $activeYear->tanggal_selesai)
            ->orderBy('tanggal_mulai', 'asc')
            ->first();

        $kelasBaruCount = $nextTahunAjaran
            ? \App\Models\Kelas::where('tahun_ajaran_id', $nextTahunAjaran->id)->count()
            : 0;

        return [
            'hasNextTA' => $nextTahunAjaran !== null,
            'nextTA' => $nextTahunAjaran,
            'kelasBaruCount' => $kelasBaruCount,
            'isReady' => $nextTahunAjaran !== null && $kelasBaruCount > 0,
        ];
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

        $tahunAjaran = TahunAjaran::findOrFail($validated['tahun_ajaran_id']);
        $isReady = $this->checkPromotionReadiness($tahunAjaran)['isReady'];

        if ($validated['tanggal_eksekusi'] && ! $isReady) {
            return back()
                ->withInput()
                ->with('error', 'Jadwal eksekusi otomatis tidak bisa diaktifkan: Tahun Ajaran baru dan/atau kelasnya belum dibuat. Buat dulu di menu Tahun Ajaran & Kelas, baru jadwal bisa disetel.');
        }

        // Field tanggal/waktu eksekusi di-disable di form kalau belum siap, jadi
        // browser tidak ikut mengirimnya - JANGAN anggap itu sebagai "kosongkan
        // jadwal", pertahankan jadwal yang sudah tersimpan (kalau ada) supaya tidak
        // ke-wipe cuma karena admin menyimpan pengaturan lain (mis. tanggal rapor).
        // Ambil dari PromotionSchedule.scheduled_at (dateTime, presisi jam), BUKAN
        // dari kolom pengaturan_naik_kelas.tanggal_eksekusi yang cuma date - jamnya
        // sudah hilang di situ, kalau dipakai malah menimpa jadwal jadi jam 00:00.
        if (! $isReady && ! $validated['tanggal_eksekusi']) {
            $existingSchedule = \App\Models\PromotionSchedule::where('tahun_ajaran_id', $validated['tahun_ajaran_id'])
                ->where('status', 'PENDING')
                ->first();
            if ($existingSchedule) {
                $validated['tanggal_eksekusi'] = $existingSchedule->scheduled_at->format('Y-m-d');
                $validated['waktu_eksekusi'] = $existingSchedule->scheduled_at->format('H:i');
            }
        }

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

        $routePrefix = $request->routeIs('waka.*') ? 'waka.kenaikan-kelas' : 'admin.akademik.kenaikan-kelas';

        return redirect()
            ->route($routePrefix . '.settings.index')
            ->with('success', 'Pengaturan Naik Kelas berhasil disimpan & Jadwal Otomatis diperbarui.');
    }
}
