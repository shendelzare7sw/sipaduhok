<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\TemplateCapaianKompetensi;
use App\Models\MataPelajaran;

class TemplateCapaianController extends Controller
{
    /**
     * Display list of templates with filtering.
     */
    public function index(Request $request): View
    {
        $query = TemplateCapaianKompetensi::with('mataPelajaran');

        // Filter by mata pelajaran
        if ($request->has('mata_pelajaran_id') && $request->mata_pelajaran_id != '') {
            $query->where('mata_pelajaran_id', $request->mata_pelajaran_id);
        }

        // Search by template name
        if ($request->has('search') && $request->search != '') {
            $query->where('nama_template', 'like', '%' . $request->search . '%');
        }

        $templates = $query->orderBy('mata_pelajaran_id')
                           ->orderBy('nama_template')
                           ->paginate(20);

        $mataPelajaranList = MataPelajaran::orderBy('nama_mapel')->get();

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
            'nama_template' => 'required|string|max:100',
            'template_text' => 'required|string',
        ]);

        TemplateCapaianKompetensi::create([
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'nama_template' => $request->nama_template,
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
            'nama_template' => 'required|string|max:100',
            'template_text' => 'required|string',
        ]);

        $template = TemplateCapaianKompetensi::findOrFail($id);

        $template->update([
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'nama_template' => $request->nama_template,
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
        $template->delete();

        return redirect()->route('wali.template-capaian.index')
            ->with('success', 'Template berhasil dihapus!');
    }
}
