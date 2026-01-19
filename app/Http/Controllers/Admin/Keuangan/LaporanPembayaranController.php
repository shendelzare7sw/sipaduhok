<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Http\Controllers\Bendahara\LaporanPembayaranController as BendaharaLaporanController;
use Illuminate\Http\Request;

class LaporanPembayaranController extends BendaharaLaporanController
{
    /**
     * Override index method to use admin view
     */
    public function index(Request $request)
    {
        $response = parent::index($request);

        if ($response instanceof \Illuminate\View\View) {
            return view('admin.keuangan.laporan.index', $response->getData());
        }

        return $response;
    }
    /**
     * Override rekapTagihan method to use admin view
     */
    public function rekapTagihan(Request $request)
    {
        $response = parent::rekapTagihan($request);

        if ($response instanceof \Illuminate\View\View) {
            return view('admin.keuangan.laporan.rekap-tagihan', $response->getData());
        }

        return $response;
    }

    /**
     * Override belumLunas method to use admin view
     */
    public function belumLunas(Request $request)
    {
        $response = parent::belumLunas($request);

        if ($response instanceof \Illuminate\View\View) {
            return view('admin.keuangan.laporan.belum-lunas', $response->getData());
        }

        return $response;
    }
}
