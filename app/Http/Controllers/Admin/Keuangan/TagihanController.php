<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Http\Controllers\Bendahara\TagihanController as BendaharaTagihanController;
use Illuminate\Http\Request;

class TagihanController extends BendaharaTagihanController
{
    /**
     * Override getRoutePrefix for admin redirects
     */
    protected function getRoutePrefix()
    {
        return 'admin.keuangan.tagihan';
    }

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

    /**
     * Override cetak method to use admin/bendahara view (same view for both)
     */
    public function cetak($siswa)
    {
        // Use parent method which already returns the correct view
        return parent::cetak($siswa);
    }

    /**
     * Show import form.
     */
    public function importForm()
    {
        $tahunAjarans = \App\Models\TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        return view('admin.keuangan.tagihan.import', compact('tahunAjarans'));
    }

    /**
     * Process import from Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
        ]);

        try {
            $import = new \App\Imports\TagihanImport($request->tahun_ajaran_id);
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));

            $imported = $import->getImportedCount();
            $skipped = $import->getSkippedCount();
            $missingSiswa = $import->getMissingSiswa();

            $message = "Berhasil mengimport {$imported} tagihan.";
            if ($skipped > 0) {
                $message .= " {$skipped} data dilewati.";
            }

            // Build warning message
            $warningMessage = '';
            if (!empty($missingSiswa)) {
                $warningMessage .= "Siswa tidak ditemukan: " . implode(', ', $missingSiswa) . ". ";
            }

            if (!empty($warningMessage)) {
                return redirect()->route('admin.keuangan.tagihan.index')
                    ->with('success', $message)
                    ->with('warning', $warningMessage);
            }

            return redirect()->route('admin.keuangan.tagihan.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimport: ' . $e->getMessage());
        }
    }

    /**
     * Download import template.
     */
    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\Templates\TagihanTemplate(),
            'template_tagihan.xlsx'
        );
    }

    /**
     * Override createCustom method to use admin view
     */
    public function createCustom()
    {
        $response = parent::createCustom();

        if ($response instanceof \Illuminate\View\View) {
            return view('admin.keuangan.tagihan.create-custom', $response->getData());
        }

        return $response;
    }

    /**
     * Override generateSppForm method to use admin view
     */
    public function generateSppForm()
    {
        $response = parent::generateSppForm();

        if ($response instanceof \Illuminate\View\View) {
            return view('admin.keuangan.tagihan.generate-spp', $response->getData());
        }

        return $response;
    }

    /**
     * Override duplicateForm method to use admin view
     */
    public function duplicateForm()
    {
        $response = parent::duplicateForm();

        if ($response instanceof \Illuminate\View\View) {
            return view('admin.keuangan.tagihan.duplicate', $response->getData());
        }

        return $response;
    }
}

