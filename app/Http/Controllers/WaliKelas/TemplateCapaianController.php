<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WaliKelas\Traits\WaliKelasHelper;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\TemplateCapaianKompetensi;
use App\Models\MataPelajaran;

class TemplateCapaianController extends Controller
{
    use WaliKelasHelper;

    /**
     * Display list of templates with filtering.
     */
    public function index(Request $request): View
    {
        // Pustaka private per wali: hanya tampilkan template milik wali yang login.
        $query = TemplateCapaianKompetensi::with('mataPelajaran')
            ->where('created_by', auth()->id());

        // Filter by mata pelajaran
        if ($request->has('mata_pelajaran_id') && $request->mata_pelajaran_id != '') {
            $query->where('mata_pelajaran_id', $request->mata_pelajaran_id);
        }

        // Search by isi deskripsi (template_text)
        if ($request->has('search') && $request->search != '') {
            $query->where('template_text', 'like', '%' . $request->search . '%');
        }

        $templates = $query->orderBy('mata_pelajaran_id')
                           ->orderByDesc('id')
                           ->paginate(20);

        // Batasi daftar mapel ke jenjang kelas yang diampu wali (mis. SMA) agar dropdown
        // tidak menampilkan mapel lintas jenjang yang tampak "duplikat" (mis. Bahasa
        // Indonesia yang ada di tiap jenjang KB/TKA/TKB/SD/SMP/SMA).
        $mapelQuery = MataPelajaran::orderBy('nama_mapel');
        $tenagaPendidik = $this->getTenagaPendidik();
        if ($tenagaPendidik) {
            $jenjang = $this->getKelasWali($tenagaPendidik)
                ->pluck('jenjang')->filter()->unique()->values();
            if ($jenjang->isNotEmpty()) {
                $mapelQuery->whereIn('jenjang', $jenjang);
            }
        }
        $mataPelajaranList = $mapelQuery->get();

        return view('wali-kelas.template-capaian.index', [
            'templates' => $templates,
            'mataPelajaranList' => $mataPelajaranList,
        ]);
    }

    /**
     * Store new template.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'template_text' => 'required|string',
        ]);

        TemplateCapaianKompetensi::create([
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'template_text' => $request->template_text,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('wali.template-capaian.index')
            ->with('success', 'Template berhasil ditambahkan!');
    }

    /**
     * Update existing template.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'template_text' => 'required|string',
        ]);

        $template = TemplateCapaianKompetensi::findOrFail($id);

        // Pustaka private per wali: hanya pemilik yang boleh mengubah.
        if ($template->created_by != auth()->id()) {
            return redirect()->route('wali.template-capaian.index')
                ->with('error', 'Anda hanya dapat mengubah template yang Anda buat sendiri.');
        }

        $template->update([
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'template_text' => $request->template_text,
        ]);

        return redirect()->route('wali.template-capaian.index')
            ->with('success', 'Template berhasil diperbarui!');
    }

    /**
     * Delete template.
     */
    public function destroy(int $id): RedirectResponse
    {
        $template = TemplateCapaianKompetensi::findOrFail($id);

        // Pustaka private per wali: hanya pemilik yang boleh menghapus.
        if ($template->created_by != auth()->id()) {
            return redirect()->route('wali.template-capaian.index')
                ->with('error', 'Anda hanya dapat menghapus template yang Anda buat sendiri.');
        }

        $template->delete();

        return redirect()->route('wali.template-capaian.index')
            ->with('success', 'Template berhasil dihapus!');
    }
}
