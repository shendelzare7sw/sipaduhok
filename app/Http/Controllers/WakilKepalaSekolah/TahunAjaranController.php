<?php

namespace App\Http\Controllers\WakilKepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TahunAjaranController extends Controller
{
    public function index(Request $request)
    {
        $query = TahunAjaran::query();

        // Filter by status (is_active) only if filled
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $tahunAjarans = $query->orderBy('tanggal_mulai', 'desc')->paginate(10);

        return view('waka.tahun-ajaran.index', compact('tahunAjarans'));
    }

    public function toggleActive($id)
    {
        DB::beginTransaction();
        try {
            // Deactivate all
            TahunAjaran::where('is_active', true)->update(['is_active' => false]);

            // Activate selected
            $tahunAjaran = TahunAjaran::findOrFail($id);
            $tahunAjaran->is_active = true;
            $tahunAjaran->save();

            DB::commit();
            return redirect()->route('waka.tahun-ajaran.index')
                ->with('success', 'Tahun Ajaran ' . $tahunAjaran->nama_tahun_ajaran . ' berhasil diaktifkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengaktifkan tahun ajaran');
        }
    }

    public function create()
    {
        return view('waka.tahun-ajaran.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_tahun_ajaran' => 'required|string|max:255|unique:tahun_ajaran,nama_tahun_ajaran',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_active' => 'boolean'
        ], [
            'nama_tahun_ajaran.required' => 'Nama tahun ajaran harus diisi',
            'nama_tahun_ajaran.unique' => 'Nama tahun ajaran sudah ada',
            'tanggal_mulai.required' => 'Tanggal mulai harus diisi',
            'tanggal_mulai.date' => 'Tanggal mulai harus berupa tanggal yang valid',
            'tanggal_selesai.required' => 'Tanggal selesai harus diisi',
            'tanggal_selesai.date' => 'Tanggal selesai harus berupa tanggal yang valid',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai'
        ]);

        // If is_active is checked, deactivate all other tahun ajaran
        if ($request->has('is_active') && $request->is_active) {
            TahunAjaran::where('is_active', true)->update(['is_active' => false]);
            $validated['is_active'] = true;
        } else {
            $validated['is_active'] = false;
        }

        TahunAjaran::create($validated);

        return redirect()->route('waka.tahun-ajaran.index')
            ->with('success', 'Tahun Ajaran berhasil ditambahkan');
    }

    public function show(TahunAjaran $tahunAjaran)
    {
        $tahunAjaran->load('kelas.cabang');
        return view('waka.tahun-ajaran.show', compact('tahunAjaran'));
    }

    public function edit(TahunAjaran $tahunAjaran)
    {
        return view('waka.tahun-ajaran.edit', compact('tahunAjaran'));
    }

    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $validated = $request->validate([
            'nama_tahun_ajaran' => 'required|string|max:255|unique:tahun_ajaran,nama_tahun_ajaran,' . $tahunAjaran->id,
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_active' => 'boolean'
        ], [
            'nama_tahun_ajaran.required' => 'Nama tahun ajaran harus diisi',
            'nama_tahun_ajaran.unique' => 'Nama tahun ajaran sudah ada',
            'tanggal_mulai.required' => 'Tanggal mulai harus diisi',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai'
        ]);

        // If is_active is checked, deactivate all other tahun ajaran
        if ($request->has('is_active') && $request->is_active) {
            TahunAjaran::where('is_active', true)
                ->where('id', '!=', $tahunAjaran->id)
                ->update(['is_active' => false]);
            $validated['is_active'] = true;
        } else {
            $validated['is_active'] = false;
        }

        $tahunAjaran->update($validated);

        return redirect()->route('waka.tahun-ajaran.index')
            ->with('success', 'Tahun Ajaran berhasil diperbarui');
    }

    public function destroy(TahunAjaran $tahunAjaran)
    {
        try {
            // Check if there are any related records
            if ($tahunAjaran->kelas()->count() > 0) {
                return redirect()->back()->with('error', 'Tidak dapat menghapus tahun ajaran yang masih memiliki kelas terkait');
            }

            $tahunAjaran->delete();
            return redirect()->route('waka.tahun-ajaran.index')
                ->with('success', 'Tahun Ajaran berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus tahun ajaran');
        }
    }
}
