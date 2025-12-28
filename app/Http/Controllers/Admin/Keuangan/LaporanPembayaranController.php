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
}
