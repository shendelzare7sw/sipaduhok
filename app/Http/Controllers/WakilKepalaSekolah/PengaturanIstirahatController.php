<?php

namespace App\Http\Controllers\WakilKepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\PengaturanIstirahat;
use Illuminate\Http\Request;

class PengaturanIstirahatController extends Controller
{
    public function index()
    {
        $jenjangList = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];
        $pengaturanPerJenjang = [];

        foreach ($jenjangList as $jenjang) {
            $pengaturanPerJenjang[$jenjang] = PengaturanIstirahat::where('jenjang', $jenjang)
                ->orderBy('urutan')
                ->get();
        }

        return view('waka.pengaturan-istirahat.index', compact('jenjangList', 'pengaturanPerJenjang'));
    }

    public function create(Request $request)
    {
        $jenjangList = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $selectedJenjang = $request->query('jenjang');

        return view('waka.pengaturan-istirahat.create', compact('jenjangList', 'hariList', 'selectedJenjang'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenjang' => 'required|in:KB,TKA,TKB,SD,SMP,SMA',
            'urutan' => 'required|integer|min:1|max:2',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'hari_aktif' => 'required|array|min:1',
            'hari_aktif.*' => 'in:Senin,Selasa,Rabu,Kamis,Jumat',
            'nama_istirahat' => 'required|string|max:255',
        ]);

        // Cek apakah sudah ada 2 istirahat untuk jenjang ini
        $jumlahIstirahat = PengaturanIstirahat::where('jenjang', $validated['jenjang'])->count();
        if ($jumlahIstirahat >= 2) {
            return back()->with('error', 'Maksimal 2 waktu istirahat per jenjang');
        }

        // Cek bentrok waktu untuk jenjang yang sama
        $bentrok = PengaturanIstirahat::where('jenjang', $validated['jenjang'])
            ->where(function($query) use ($validated) {
                $query->where(function($q) use ($validated) {
                    $q->where('jam_mulai', '<', $validated['jam_selesai'])
                      ->where('jam_selesai', '>', $validated['jam_mulai']);
                });
            })
            ->exists();

        if ($bentrok) {
            return back()->with('error', 'Waktu istirahat bentrok dengan pengaturan yang sudah ada')->withInput();
        }

        PengaturanIstirahat::create($validated);

        return redirect()->route('waka.pengaturan-istirahat.index')
            ->with('success', 'Pengaturan istirahat berhasil ditambahkan');
    }

    public function edit($id)
    {
        $pengaturan = PengaturanIstirahat::findOrFail($id);
        $jenjangList = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        return view('waka.pengaturan-istirahat.edit', compact('pengaturan', 'jenjangList', 'hariList'));
    }

    public function update(Request $request, $id)
    {
        $pengaturan = PengaturanIstirahat::findOrFail($id);

        $validated = $request->validate([
            'jenjang' => 'required|in:KB,TKA,TKB,SD,SMP,SMA',
            'urutan' => 'required|integer|min:1|max:2',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'hari_aktif' => 'required|array|min:1',
            'hari_aktif.*' => 'in:Senin,Selasa,Rabu,Kamis,Jumat',
            'nama_istirahat' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        // Cek bentrok waktu (exclude current record)
        $bentrok = PengaturanIstirahat::where('jenjang', $validated['jenjang'])
            ->where('id', '!=', $id)
            ->where(function($query) use ($validated) {
                $query->where(function($q) use ($validated) {
                    $q->where('jam_mulai', '<', $validated['jam_selesai'])
                      ->where('jam_selesai', '>', $validated['jam_mulai']);
                });
            })
            ->exists();

        if ($bentrok) {
            return back()->with('error', 'Waktu istirahat bentrok dengan pengaturan yang sudah ada')->withInput();
        }

        $pengaturan->update($validated);

        return redirect()->route('waka.pengaturan-istirahat.index')
            ->with('success', 'Pengaturan istirahat berhasil diperbarui');
    }

    public function destroy($id)
    {
        $pengaturan = PengaturanIstirahat::findOrFail($id);
        $pengaturan->delete();

        return redirect()->route('waka.pengaturan-istirahat.index')
            ->with('success', 'Pengaturan istirahat berhasil dihapus');
    }

    public function toggleStatus($id)
    {
        $pengaturan = PengaturanIstirahat::findOrFail($id);
        $pengaturan->is_active = !$pengaturan->is_active;
        $pengaturan->save();

        return back()->with('success', 'Status berhasil diubah');
    }
}
