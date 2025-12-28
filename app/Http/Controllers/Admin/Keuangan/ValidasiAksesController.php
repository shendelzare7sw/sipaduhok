<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Http\Controllers\Bendahara\ValidasiAksesController as BendaharaValidasiAksesController;
use Illuminate\Http\Request;

class ValidasiAksesController extends BendaharaValidasiAksesController
{
    /**
     * Override index method to use admin view
     */
    public function index(Request $request)
    {
        $response = parent::index($request);

        if ($response instanceof \Illuminate\View\View) {
            return view('admin.keuangan.validasi-akses.index', $response->getData());
        }

        return $response;
    }
}
