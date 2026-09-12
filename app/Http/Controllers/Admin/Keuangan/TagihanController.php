<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Exports\Templates\TagihanTemplate;
use App\Http\Controllers\Bendahara\TagihanController as BendaharaTagihanController;
use App\Imports\TagihanImport;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TagihanController extends BendaharaTagihanController
{
    protected function getRoutePrefix(): string
    {
        return 'admin.keuangan.tagihan';
    }

    public function importForm(): View
    {
        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();

        return view('admin.keuangan.tagihan.import', compact('tahunAjarans'));
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
        ]);

        try {
            $import = new TagihanImport($request->integer('tahun_ajaran_id'));
            Excel::import($import, $request->file('file'));

            $imported = $import->getImportedCount();
            $skipped = $import->getSkippedCount();
            $missingSiswa = $import->getMissingSiswa();
            $warnings = $import->getWarnings();

            $message = "Berhasil mengimport {$imported} tagihan.";
            if ($skipped > 0) {
                $message .= " {$skipped} data dilewati.";
            }

            $warningMessage = ! empty($warnings)
                ? implode(' | ', $warnings)
                : (! empty($missingSiswa) ? 'Siswa tidak ditemukan: '.implode(', ', $missingSiswa).'.' : '');

            $redirect = redirect()->route($this->getRoutePrefix().'.index')->with('success', $message);

            return $warningMessage !== ''
                ? $redirect->with('warning', $warningMessage)
                : $redirect;
        } catch (\Throwable $exception) {
            return back()->with('error', 'Gagal mengimport: '.$exception->getMessage());
        }
    }

    public function downloadTemplate(): BinaryFileResponse
    {
        return Excel::download(new TagihanTemplate, 'template_tagihan.xlsx');
    }

    /**
     * Fitur pemulihan khusus Admin selama masa percobaan sistem.
     */
    public function resetTagihan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'siswa_ids' => 'required|string',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
        ]);

        $siswaIds = json_decode($validated['siswa_ids'], true);
        if (empty($siswaIds) || ! is_array($siswaIds)) {
            return back()->with('error', 'Tidak ada siswa yang dipilih.');
        }

        try {
            [$deletedTagihan, $deletedPembayaran] = DB::transaction(function () use ($siswaIds, $validated) {
                $tagihanIds = Tagihan::whereIn('siswa_id', $siswaIds)
                    ->where('tahun_ajaran_id', $validated['tahun_ajaran_id'])
                    ->pluck('id');

                $deletedPembayaran = Pembayaran::whereIn('tagihan_id', $tagihanIds)->count();
                Pembayaran::whereIn('tagihan_id', $tagihanIds)->delete();

                $deletedTagihan = Tagihan::whereIn('siswa_id', $siswaIds)
                    ->where('tahun_ajaran_id', $validated['tahun_ajaran_id'])
                    ->delete();

                return [$deletedTagihan, $deletedPembayaran];
            });

            $siswaCount = count($siswaIds);

            return redirect()->route($this->getRoutePrefix().'.index', [
                'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
            ])->with('success', "Reset berhasil! {$deletedTagihan} tagihan dan {$deletedPembayaran} pembayaran dari {$siswaCount} siswa telah dihapus. Status kembali ke \"KOSONG\".");
        } catch (\Throwable $exception) {
            return back()->with('error', 'Gagal mereset tagihan: '.$exception->getMessage());
        }
    }
}
