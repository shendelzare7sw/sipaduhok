<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Sekretaris\SekretarisController;
use Illuminate\Http\Request;

/**
 * Admin Akademik Controller - Wrapper for Sekretaris features
 * Admin can access all sekretaris (academic) features through admin namespace
 */
class AkademikController extends SekretarisController
{
    /**
     * Override view paths to use admin.akademik namespace
     */
    protected function viewPath($sekretarisView)
    {
        return str_replace('sekretaris.', 'admin.akademik.', $sekretarisView);
    }

    // Kalender Akademik
    public function kalenderIndex()
    {
        $response = parent::kalenderIndex();
        return $this->wrapView($response, 'kalender.index');
    }

    public function kalenderCreate()
    {
        $response = parent::kalenderCreate();
        return $this->wrapView($response, 'kalender.form');
    }

    public function kalenderStore(Request $request)
    {
        return parent::kalenderStore($request);
    }

    public function kalenderEdit($id)
    {
        $response = parent::kalenderEdit($id);
        return $this->wrapView($response, 'kalender.form');
    }

    public function kalenderUpdate(Request $request, $id)
    {
        return parent::kalenderUpdate($request, $id);
    }

    public function kalenderDestroy($id)
    {
        return parent::kalenderDestroy($id);
    }

    public function kalenderBulanan(Request $request)
    {
        return parent::kalenderBulanan($request);
    }

    public function kalenderCetak(Request $request)
    {
        return parent::kalenderCetak($request);
    }

    // Pengumuman
    public function pengumumanIndex()
    {
        $response = parent::pengumumanIndex();
        return $this->wrapView($response, 'pengumuman.index');
    }

    public function pengumumanCreate()
    {
        $response = parent::pengumumanCreate();
        return $this->wrapView($response, 'pengumuman.form');
    }

    public function pengumumanStore(Request $request)
    {
        return parent::pengumumanStore($request);
    }

    public function pengumumanEdit($id)
    {
        $response = parent::pengumumanEdit($id);
        return $this->wrapView($response, 'pengumuman.form');
    }

    public function pengumumanUpdate(Request $request, $id)
    {
        return parent::pengumumanUpdate($request, $id);
    }

    public function pengumumanDestroy($id)
    {
        return parent::pengumumanDestroy($id);
    }

    // Berita
    public function beritaIndex(Request $request)
    {
        $response = parent::beritaIndex($request);
        return $this->wrapView($response, 'berita.index');
    }

    public function beritaCreate()
    {
        $response = parent::beritaCreate();
        return $this->wrapView($response, 'berita.form');
    }

    public function beritaStore(Request $request)
    {
        return parent::beritaStore($request);
    }

    public function beritaEdit($id)
    {
        $response = parent::beritaEdit($id);
        return $this->wrapView($response, 'berita.form');
    }

    public function beritaUpdate(Request $request, $id)
    {
        return parent::beritaUpdate($request, $id);
    }

    public function beritaDestroy($id)
    {
        return parent::beritaDestroy($id);
    }

    public function beritaToggleFeatured($id)
    {
        return parent::beritaToggleFeatured($id);
    }

    // Flyer
    public function flyerIndex()
    {
        $response = parent::flyerIndex();
        return $this->wrapView($response, 'flyer.index');
    }

    public function flyerCreate()
    {
        $response = parent::flyerCreate();
        return $this->wrapView($response, 'flyer.form');
    }

    public function flyerStore(Request $request)
    {
        return parent::flyerStore($request);
    }

    public function flyerEdit($id)
    {
        $response = parent::flyerEdit($id);
        return $this->wrapView($response, 'flyer.form');
    }

    public function flyerUpdate(Request $request, $id)
    {
        return parent::flyerUpdate($request, $id);
    }

    public function flyerDestroy($id)
    {
        return parent::flyerDestroy($id);
    }

    /**
     * Helper to wrap view responses
     */
    private function wrapView($response, $viewSuffix)
    {
        if ($response instanceof \Illuminate\View\View) {
            return view($this->viewPath('sekretaris.' . $viewSuffix), $response->getData());
        }
        return $response;
    }
}
