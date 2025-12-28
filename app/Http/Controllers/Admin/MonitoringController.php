<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Ketua\KetuaController;
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
    public function monitoringPengguna()
    {
        $response = parent::monitoringPengguna();
        return $this->wrapView($response, 'monitoring.pengguna');
    }

    public function monitoringWaliKelas()
    {
        $response = parent::monitoringWaliKelas();
        return $this->wrapView($response, 'monitoring.wali-kelas');
    }

    public function monitoringGuruPengajar()
    {
        $response = parent::monitoringGuruPengajar();
        return $this->wrapView($response, 'monitoring.guru-pengajar');
    }

    public function monitoringSiswa()
    {
        $response = parent::monitoringSiswa();
        return $this->wrapView($response, 'monitoring.siswa');
    }

    // Laporan
    public function index()
    {
        $response = parent::index();
        return $this->wrapView($response, 'laporan.index');
    }

    public function siswa(Request $request)
    {
        return parent::siswa($request);
    }

    public function tenagaPendidik(Request $request)
    {
        return parent::tenagaPendidik($request);
    }

    public function kelas(Request $request)
    {
        return parent::kelas($request);
    }

    public function waliKelas(Request $request)
    {
        return parent::waliKelas($request);
    }

    public function guruPengajar(Request $request)
    {
        return parent::guruPengajar($request);
    }

    public function rekap(Request $request)
    {
        return parent::rekap($request);
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
        return parent::catatanStore($request);
    }

    public function catatanShow($id)
    {
        $response = parent::catatanShow($id);
        return $this->wrapView($response, 'catatan.show');
    }

    /**
     * Helper to wrap view responses
     */
    private function wrapView($response, $viewSuffix)
    {
        if ($response instanceof \Illuminate\View\View) {
            return view($this->viewPath('ketua.' . $viewSuffix), $response->getData());
        }
        return $response;
    }
}
