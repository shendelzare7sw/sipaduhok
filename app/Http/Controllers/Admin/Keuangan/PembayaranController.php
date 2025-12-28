<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Http\Controllers\Bendahara\PembayaranController as BendaharaPembayaranController;
use Illuminate\Http\Request;

class PembayaranController extends BendaharaPembayaranController
{
    /**
     * Override index method to use admin view
     */
    public function index(Request $request)
    {
        $response = parent::index($request);

        if ($response instanceof \Illuminate\View\View) {
            return view('admin.keuangan.pembayaran.index', $response->getData());
        }

        return $response;
    }

    /**
     * Override show method to use admin view
     */
    public function show($pembayaran)
    {
        $response = parent::show($pembayaran);

        if ($response instanceof \Illuminate\View\View) {
            return view('admin.keuangan.pembayaran.show', $response->getData());
        }

        return $response;
    }

    /**
     * Override create method to use admin view
     */
    public function create($siswa)
    {
        $response = parent::create($siswa);

        if ($response instanceof \Illuminate\View\View) {
            return view('admin.keuangan.pembayaran.create', $response->getData());
        }

        return $response;
    }
}
