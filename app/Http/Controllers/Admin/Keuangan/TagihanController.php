<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Http\Controllers\Bendahara\TagihanController as BendaharaTagihanController;
use Illuminate\Http\Request;

class TagihanController extends BendaharaTagihanController
{
    /**
     * Override index method to use admin view
     */
    public function index(Request $request)
    {
        // Call parent method to get data
        $response = parent::index($request);

        // If response is a view, change the view path
        if ($response instanceof \Illuminate\View\View) {
            return view('admin.keuangan.tagihan.index', $response->getData());
        }

        return $response;
    }

    /**
     * Override show method to use admin view
     */
    public function show($siswa)
    {
        $response = parent::show($siswa);

        if ($response instanceof \Illuminate\View\View) {
            return view('admin.keuangan.tagihan.show', $response->getData());
        }

        return $response;
    }

    /**
     * Override edit method to use admin view
     */
    public function edit($siswa)
    {
        $response = parent::edit($siswa);

        if ($response instanceof \Illuminate\View\View) {
            return view('admin.keuangan.tagihan.edit', $response->getData());
        }

        return $response;
    }

    /**
     * Override bulkCreate method to use admin view
     */
    public function bulkCreate(Request $request)
    {
        $response = parent::bulkCreate($request);

        if ($response instanceof \Illuminate\View\View) {
            return view('admin.keuangan.tagihan.bulk-create', $response->getData());
        }

        return $response;
    }
}
