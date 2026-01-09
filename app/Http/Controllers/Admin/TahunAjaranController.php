<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TahunAjaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TahunAjaran::query();

        // Filter by status (is_active) only if filled
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $tahunAjarans = $query->orderBy('tanggal_mulai', 'desc')->paginate(10);

        return view('admin.tahun-ajaran.index', compact('tahunAjarans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.tahun-ajaran.create');
    }

    /**
     * Store a newly created resource in storage.
     */
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

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun Ajaran berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(TahunAjaran $tahunAjaran)
    {
        $tahunAjaran->load('kelas');
        return view('admin.tahun-ajaran.show', compact('tahunAjaran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TahunAjaran $tahunAjaran)
    {
        return view('admin.tahun-ajaran.edit', compact('tahunAjaran'));
    }

    /**
     * Update the specified resource in storage.
     */
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
            'tanggal_mulai.date' => 'Tanggal mulai harus berupa tanggal yang valid',
            'tanggal_selesai.required' => 'Tanggal selesai harus diisi',
            'tanggal_selesai.date' => 'Tanggal selesai harus berupa tanggal yang valid',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai'
        ]);

        // If is_active is checked, deactivate all other tahun ajaran
        if ($request->has('is_active') && $request->is_active) {
            TahunAjaran::where('id', '!=', $tahunAjaran->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
            $validated['is_active'] = true;
        } else {
            $validated['is_active'] = false;
        }

        $tahunAjaran->update($validated);

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun Ajaran berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TahunAjaran $tahunAjaran)
    {
        // Check if tahun ajaran is being used
        if ($tahunAjaran->kelas()->count() > 0) {
            return redirect()->route('admin.tahun-ajaran.index')
                ->with('error', 'Tahun Ajaran tidak dapat dihapus karena sedang digunakan di data kelas');
        }

        $tahunAjaran->delete();

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun Ajaran berhasil dihapus');
    }

    /**
     * Set tahun ajaran as active
     */
    public function activate(TahunAjaran $tahunAjaran)
    {
        DB::beginTransaction();
        try {
            // Deactivate all tahun ajaran
            TahunAjaran::where('is_active', true)->update(['is_active' => false]);
            
            // Activate selected tahun ajaran
            $tahunAjaran->update(['is_active' => true]);
            
            DB::commit();
            
            return redirect()->route('admin.tahun-ajaran.index')
                ->with('success', 'Tahun Ajaran berhasil diaktifkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.tahun-ajaran.index')
                ->with('error', 'Terjadi kesalahan saat mengaktifkan Tahun Ajaran');
        }
    }
}