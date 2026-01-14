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

    /**
     * Override riwayatSiswa method to use admin view
     */
    public function riwayatSiswa($siswaId)
    {
        $response = parent::riwayatSiswa($siswaId);

        if ($response instanceof \Illuminate\View\View) {
            return view('admin.keuangan.pembayaran.riwayat-siswa', $response->getData());
        }

        return $response;
    }

    /**
     * Override store to redirect to admin route
     */
    public function store(Request $request, $siswaId)
    {
        // Call parent store but catch redirect to override destination
        try {
            parent::store($request, $siswaId);
            return redirect()->route('admin.keuangan.pembayaran.riwayat-siswa', $siswaId)
                ->with('success', 'Pembayaran berhasil dicatat.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Override validasi to redirect to admin route
     */
    public function validasi(Request $request, $id)
    {
        $response = parent::validasi($request, $id);

        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            $message = $request->status_validasi === 'disetujui'
                ? 'Pembayaran berhasil divalidasi.'
                : 'Pembayaran ditolak.';
            return redirect()->route('admin.keuangan.pembayaran.index')
                ->with('success', $message);
        }

        return $response;
    }

    /**
     * Override validasiLangsung to redirect to admin route
     */
    public function validasiLangsung(Request $request, $siswaId)
    {
        // Call parent but redirect to admin route
        try {
            parent::validasiLangsung($request, $siswaId);
            return redirect()->route('admin.keuangan.pembayaran.riwayat-siswa', $siswaId)
                ->with('success', 'Pembayaran tunai berhasil dicatat dan divalidasi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
