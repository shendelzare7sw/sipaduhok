<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Ketua\KetuaController;
use App\Models\Catatan;
use Illuminate\Http\Request;

/**
 * Admin Monitoring Controller - Wrapper for Ketua features
 * Admin can access all ketua (monitoring & reporting) features
 */
class MonitoringController extends KetuaController
{
    /**
     * Override view paths to use admin namespace
     */
    protected function viewPath($ketuaView)
    {
        return str_replace('ketua.', 'admin.', $ketuaView);
    }

    // Monitoring
    public function monitoringPengguna(Request $request)
    {
        $response = parent::monitoringPengguna($request);

        return $this->wrapView($response, 'monitoring.pengguna');
    }

    public function monitoringWaliKelas(Request $request)
    {
        $response = parent::monitoringWaliKelas($request);

        return $this->wrapView($response, 'monitoring.wali-kelas');
    }

    public function monitoringGuruPengajar(Request $request)
    {
        $response = parent::monitoringGuruPengajar($request);

        return $this->wrapView($response, 'monitoring.guru-pengajar');
    }

    public function monitoringSiswa(Request $request)
    {
        $response = parent::monitoringSiswa($request);

        return $this->wrapView($response, 'monitoring.siswa');
    }

    // Laporan
    public function index()
    {
        $response = parent::index();
        if ($response instanceof \Illuminate\View\View) {
            return view('admin.laporan.index', array_merge($response->getData(), [
                'routePrefix' => 'admin.laporan',
                'supportsAcademic' => false,
            ]));
        }

        return $response;
    }

    public function siswa(Request $request)
    {
        return $this->wrapReportView(parent::siswa($request), 'print-siswa');
    }

    public function tenagaPendidik(Request $request)
    {
        return $this->wrapReportView(parent::tenagaPendidik($request), 'print-guru');
    }

    public function kelas(Request $request)
    {
        return $this->wrapReportView(parent::kelas($request), 'print-kelas');
    }

    public function waliKelas(Request $request)
    {
        return $this->wrapReportView(parent::waliKelas($request), 'print-wali-kelas');
    }

    public function guruPengajar(Request $request)
    {
        return $this->wrapReportView(parent::guruPengajar($request), 'print-guru-pengajar');
    }

    public function rekap(Request $request)
    {
        return $this->wrapReportView(parent::rekap($request), 'print-rekap');
    }

    // Catatan
    public function catatanIndex()
    {
        $response = parent::catatanIndex();

        return $this->wrapView($response, 'catatan.index');
    }

    public function catatanCreate()
    {
        $response = parent::catatanCreate();

        return $this->wrapView($response, 'catatan.create');
    }

    public function catatanStore(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi_catatan' => 'required|string',
            'tipe_penerima' => 'required|in:semua,role,individu',
            'role_penerima' => 'required_if:tipe_penerima,role',
            'penerima_ids' => 'required_if:tipe_penerima,individu|array|min:1',
            'penerima_ids.*' => 'exists:users,id',
            'prioritas' => 'required|in:biasa,penting,mendesak',
        ]);

        $pengirimId = auth()->id();
        $tanggalKirim = now();
        $notificationService = app(\App\Services\NotificationService::class);

        if ($validated['tipe_penerima'] === 'individu') {
            foreach ($validated['penerima_ids'] as $penerimaId) {
                $catatan = Catatan::create([
                    'pengirim_id' => $pengirimId,
                    'judul' => $validated['judul'],
                    'isi_catatan' => $validated['isi_catatan'],
                    'tipe_penerima' => 'individu',
                    'penerima_id' => $penerimaId,
                    'prioritas' => $validated['prioritas'],
                    'tanggal_kirim' => $tanggalKirim,
                ]);
                $catatan->load('pengirim');
                $notificationService->notifyCatatan($catatan);
            }
        } else {
            $catatan = Catatan::create([
                'pengirim_id' => $pengirimId,
                'judul' => $validated['judul'],
                'isi_catatan' => $validated['isi_catatan'],
                'tipe_penerima' => $validated['tipe_penerima'],
                'role_penerima' => $validated['role_penerima'] ?? null,
                'prioritas' => $validated['prioritas'],
                'tanggal_kirim' => $tanggalKirim,
            ]);
            $catatan->load('pengirim');
            $notificationService->notifyCatatan($catatan);
        }

        return redirect_to_previous('admin.catatan.index')->with('success', 'Catatan berhasil dikirim!');
    }

    public function catatanShow($id)
    {
        $response = parent::catatanShow($id);

        return $this->wrapView($response, 'catatan.show');
    }

    public function catatanDestroy($id)
    {
        $catatan = Catatan::where('pengirim_id', auth()->id())->findOrFail($id);
        $catatan->delete();

        return back()->with('success', 'Catatan berhasil dihapus dari riwayat.');
    }

    /**
     * Helper to wrap view responses
     */
    private function wrapView($response, $viewSuffix)
    {
        if ($response instanceof \Illuminate\View\View) {
            return view($this->viewPath('ketua.'.$viewSuffix), $response->getData());
        }

        return $response;
    }

    private function wrapReportView($response, string $view)
    {
        if ($response instanceof \Illuminate\View\View) {
            return view('admin.laporan.'.$view, array_merge($response->getData(), [
                'backRoute' => 'admin.laporan.index',
            ]));
        }

        return $response;
    }

    /**
     * Override LMS view context for admin namespace.
     */
    protected function lmsViewContext(): array
    {
        return [
            'rolePartial' => 'admin.partials.cleanflow-sidebar',
            'baseRoute' => 'admin.monitoring.lms',
            'cabangScope' => null,
        ];
    }
}
