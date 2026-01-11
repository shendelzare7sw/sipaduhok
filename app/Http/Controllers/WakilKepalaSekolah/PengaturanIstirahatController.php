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

        // Cek apakah urutan ini sudah ada untuk hari yang sama
        $existingIstirahat = PengaturanIstirahat::where('jenjang', $validated['jenjang'])
            ->where('urutan', $validated['urutan'])
            ->get();

        // Cek apakah ada bentrok hari aktif dengan istirahat yang sama urutan
        foreach ($existingIstirahat as $istirahat) {
            $hariSama = array_intersect($validated['hari_aktif'], $istirahat->hari_aktif ?? []);
            if (!empty($hariSama)) {
                $hariList = implode(', ', $hariSama);
                return back()->with('error', "Istirahat urutan {$validated['urutan']} sudah ada untuk hari: {$hariList}. Gunakan urutan yang berbeda atau edit yang sudah ada.")->withInput();
            }
        }

        // Cek bentrok waktu untuk jenjang yang sama (hanya untuk hari yang sama)
        $allIstirahat = PengaturanIstirahat::where('jenjang', $validated['jenjang'])->get();

        foreach ($allIstirahat as $istirahat) {
            // Cek apakah ada hari yang sama
            $hariSama = array_intersect($validated['hari_aktif'], $istirahat->hari_aktif ?? []);

            if (!empty($hariSama)) {
                // Cek apakah waktu bentrok
                if ($istirahat->jam_mulai < $validated['jam_selesai'] &&
                    $istirahat->jam_selesai > $validated['jam_mulai']) {
                    $hariList = implode(', ', $hariSama);
                    return back()->with('error', "Waktu istirahat bentrok dengan '{$istirahat->nama_istirahat}' pada hari: {$hariList}")->withInput();
                }
            }
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

        // Cek apakah urutan ini sudah ada untuk hari yang sama (exclude current record)
        $existingIstirahat = PengaturanIstirahat::where('jenjang', $validated['jenjang'])
            ->where('urutan', $validated['urutan'])
            ->where('id', '!=', $id)
            ->get();

        // Cek apakah ada bentrok hari aktif dengan istirahat yang sama urutan
        foreach ($existingIstirahat as $istirahat) {
            $hariSama = array_intersect($validated['hari_aktif'], $istirahat->hari_aktif ?? []);
            if (!empty($hariSama)) {
                $hariList = implode(', ', $hariSama);
                return back()->with('error', "Istirahat urutan {$validated['urutan']} sudah ada untuk hari: {$hariList}. Gunakan urutan yang berbeda atau edit yang sudah ada.")->withInput();
            }
        }

        // Cek bentrok waktu untuk jenjang yang sama (exclude current record, hanya untuk hari yang sama)
        $allIstirahat = PengaturanIstirahat::where('jenjang', $validated['jenjang'])
            ->where('id', '!=', $id)
            ->get();

        foreach ($allIstirahat as $istirahat) {
            // Cek apakah ada hari yang sama
            $hariSama = array_intersect($validated['hari_aktif'], $istirahat->hari_aktif ?? []);

            if (!empty($hariSama)) {
                // Cek apakah waktu bentrok
                if ($istirahat->jam_mulai < $validated['jam_selesai'] &&
                    $istirahat->jam_selesai > $validated['jam_mulai']) {
                    $hariList = implode(', ', $hariSama);
                    return back()->with('error', "Waktu istirahat bentrok dengan '{$istirahat->nama_istirahat}' pada hari: {$hariList}")->withInput();
                }
            }
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
