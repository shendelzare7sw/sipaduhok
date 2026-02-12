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
    public function kalenderIndex(Request $request)
    {
        $response = parent::kalenderIndex($request);
        return $this->wrapView($response, 'kalender.index');
    }

    public function kalenderCreate()
    {
        $response = parent::kalenderCreate();
        return $this->wrapView($response, 'kalender.form');
    }

    public function kalenderStore(Request $request)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'waktu_mulai' => 'nullable|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i|after:waktu_mulai',
            'keterangan' => 'nullable|string',
            'jenis_kegiatan' => 'required|string|max:100',
            'custom_jenis_kegiatan' => 'nullable|string|max:50',
            'lampiran_surat' => 'nullable|file|mimes:pdf|max:5120',
            'status' => 'required|in:draft,aktif,selesai',
        ]);
        
        // Process custom event type
        if ($request->jenis_kegiatan === 'lainnya' && $request->custom_jenis_kegiatan) {
            $validated['jenis_kegiatan'] = $request->custom_jenis_kegiatan;
        }

        $tahunAjaranAktif = \App\Models\TahunAjaran::where('is_active', true)->first();
        $validated['tahun_ajaran_id'] = $tahunAjaranAktif->id;

        if ($request->hasFile('lampiran_surat')) {
            $validated['lampiran_surat'] = $request->file('lampiran_surat')
                ->store('kalender/lampiran', 'public');
        }

        \App\Models\KalenderAkademik::create($validated);

        return redirect()->route('admin.akademik.kalender.index')
            ->with('success', 'Kalender akademik berhasil ditambahkan!');
    }

    public function kalenderShow($id)
    {
        $response = parent::kalenderShow($id);
        return $this->wrapView($response, 'kalender.show');
    }

    public function kalenderEdit($id)
    {
        $response = parent::kalenderEdit($id);
        return $this->wrapView($response, 'kalender.form');
    }

    public function kalenderUpdate(Request $request, $id)
    {
        $kalender = \App\Models\KalenderAkademik::findOrFail($id);

        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'waktu_mulai' => 'nullable|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i|after:waktu_mulai',
            'keterangan' => 'nullable|string',
            'jenis_kegiatan' => 'required|string|max:100',
            'custom_jenis_kegiatan' => 'nullable|string|max:50',
            'lampiran_surat' => 'nullable|file|mimes:pdf|max:5120',
            'status' => 'required|in:draft,aktif,selesai',
        ]);
        
        // Process custom event type
        if ($request->jenis_kegiatan === 'lainnya' && $request->custom_jenis_kegiatan) {
            $validated['jenis_kegiatan'] = $request->custom_jenis_kegiatan;
        }

        if ($request->hasFile('lampiran_surat')) {
            if ($kalender->lampiran_surat) {
                \Storage::disk('public')->delete($kalender->lampiran_surat);
            }

            $validated['lampiran_surat'] = $request->file('lampiran_surat')
                ->store('kalender/lampiran', 'public');
        }

        $kalender->update($validated);

        // Update pengumuman terkait
        if ($kalender->pengumuman()->exists()) {
            $kalender->pengumuman()->update([
                'judul' => 'Pengingat: ' . $validated['nama_kegiatan'],
                'isi_pengumuman' => "Kegiatan {$validated['nama_kegiatan']} akan dilaksanakan pada tanggal " .
                    \Carbon\Carbon::parse($validated['tanggal_mulai'])->format('d F Y') . ". " . ($validated['keterangan'] ?? ''),
            ]);
        }

        return redirect()->route('admin.akademik.kalender.index')
            ->with('success', 'Kalender akademik berhasil diperbarui!');
    }

    public function kalenderDestroy($id)
    {
        $kalender = \App\Models\KalenderAkademik::findOrFail($id);

        if ($kalender->lampiran_surat) {
            \Storage::disk('public')->delete($kalender->lampiran_surat);
        }

        $kalender->delete();

        return redirect()->route('admin.akademik.kalender.index')
            ->with('success', 'Kalender akademik berhasil dihapus!');
    }

    public function kalenderToggleVisibility($id)
    {
        try {
            \Log::info('Admin Toggle Visibility Request for ID: ' . $id);
            $kalender = \App\Models\KalenderAkademik::findOrFail($id);
            \Log::info('Current status: ' . $kalender->is_hidden_siswa);
            
            $kalender->is_hidden_siswa = !$kalender->is_hidden_siswa;
            $saved = $kalender->save();
            
            \Log::info('New status: ' . $kalender->is_hidden_siswa . ' | Saved: ' . ($saved ? 'Yes' : 'No'));

            return response()->json([
                'success' => true,
                'is_hidden' => $kalender->is_hidden_siswa,
                'message' => $kalender->is_hidden_siswa ? 'Kegiatan disembunyikan dari siswa' : 'Kegiatan ditampilkan ke siswa'
            ]);
        } catch (\Exception $e) {
            \Log::error('Admin Toggle Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
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
        $validated = $request->validate([
            'kalender_akademik_id' => 'nullable|exists:kalender_akademik,id',
            'judul' => 'required|string|max:255',
            'isi_pengumuman' => 'required|string',
            'tanggal_pengumuman' => 'required|date',
            'prioritas' => 'required|in:biasa,penting,mendesak',
            'lampiran_surat' => 'nullable|file|mimes:pdf|max:5120',
            'status' => 'required|in:draft,aktif,arsip',
        ]);

        $validated['dibuat_oleh'] = auth()->id();
        $validated['is_from_kalender'] = $request->filled('kalender_akademik_id');

        if ($request->hasFile('lampiran_surat')) {
            $validated['lampiran_surat'] = $request->file('lampiran_surat')
                ->store('pengumuman/lampiran', 'public');
        }

        \App\Models\Pengumuman::create($validated);

        return redirect()->route('admin.akademik.pengumuman.index')
            ->with('success', 'Pengumuman berhasil ditambahkan!');
    }

    public function pengumumanEdit($id)
    {
        $response = parent::pengumumanEdit($id);
        return $this->wrapView($response, 'pengumuman.form');
    }

    public function pengumumanUpdate(Request $request, $id)
    {
        $pengumuman = \App\Models\Pengumuman::findOrFail($id);

        $validated = $request->validate([
            'kalender_akademik_id' => 'nullable|exists:kalender_akademik,id',
            'judul' => 'required|string|max:255',
            'isi_pengumuman' => 'required|string',
            'tanggal_pengumuman' => 'required|date',
            'prioritas' => 'required|in:biasa,penting,mendesak',
            'lampiran_surat' => 'nullable|file|mimes:pdf|max:5120',
            'status' => 'required|in:draft,aktif,arsip',
        ]);

        if ($request->hasFile('lampiran_surat')) {
            if ($pengumuman->lampiran_surat) {
                \Storage::disk('public')->delete($pengumuman->lampiran_surat);
            }

            $validated['lampiran_surat'] = $request->file('lampiran_surat')
                ->store('pengumuman/lampiran', 'public');
        }

        $pengumuman->update($validated);

        return redirect()->route('admin.akademik.pengumuman.index')
            ->with('success', 'Pengumuman berhasil diperbarui!');
    }

    public function pengumumanDestroy($id)
    {
        $pengumuman = \App\Models\Pengumuman::findOrFail($id);

        if ($pengumuman->lampiran_surat) {
            \Storage::disk('public')->delete($pengumuman->lampiran_surat);
        }

        $pengumuman->delete();

        return redirect()->route('admin.akademik.pengumuman.index')
            ->with('success', 'Pengumuman berhasil dihapus!');
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
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi_singkat' => 'required|string|max:500',
            'gambar_thumbnail' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'url_berita' => 'required|url|max:500',
            'kategori' => 'required|in:kegiatan,prestasi,pengumuman,artikel,ujian',
            'tanggal_berita' => 'required|date',
            'is_featured' => 'boolean',
            'urutan_tampil' => 'required|integer|min:1|max:999',
            'status' => 'required|in:draft,aktif,arsip',
        ], [
            'judul.required' => 'Judul berita harus diisi',
            'deskripsi_singkat.required' => 'Deskripsi singkat harus diisi',
            'deskripsi_singkat.max' => 'Deskripsi singkat maksimal 500 karakter',
            'gambar_thumbnail.required' => 'Gambar thumbnail harus diunggah',
            'gambar_thumbnail.image' => 'File harus berupa gambar',
            'gambar_thumbnail.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'gambar_thumbnail.max' => 'Ukuran gambar maksimal 2MB',
            'url_berita.required' => 'URL berita harus diisi',
            'url_berita.url' => 'URL berita tidak valid',
            'kategori.required' => 'Kategori harus dipilih',
            'tanggal_berita.required' => 'Tanggal berita harus diisi',
            'urutan_tampil.required' => 'Urutan tampil harus diisi',
        ]);

        $validated['dibuat_oleh'] = auth()->id();
        $validated['is_featured'] = $request->has('is_featured');

        // Handle upload gambar
        if ($request->hasFile('gambar_thumbnail')) {
            try {
                $file = $request->file('gambar_thumbnail');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // Pastikan folder ada
                $destinationPath = public_path('img/berita');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                // Pindahkan file
                $file->move($destinationPath, $filename);
                $validated['gambar_thumbnail'] = $filename;
            } catch (\Exception $e) {
                return back()->withErrors(['gambar_thumbnail' => 'Gagal mengunggah gambar: ' . $e->getMessage()])
                    ->withInput();
            }
        }

        \App\Models\Berita::create($validated);

        return redirect()->route('admin.akademik.berita.index')
            ->with('success', 'Berita berhasil ditambahkan!');
    }

    public function beritaEdit($id)
    {
        $response = parent::beritaEdit($id);
        return $this->wrapView($response, 'berita.form');
    }

    public function beritaUpdate(Request $request, $id)
    {
        $berita = \App\Models\Berita::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi_singkat' => 'required|string|max:500',
            'gambar_thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'url_berita' => 'required|url|max:500',
            'kategori' => 'required|in:kegiatan,prestasi,pengumuman,artikel,ujian',
            'tanggal_berita' => 'required|date',
            'is_featured' => 'boolean',
            'urutan_tampil' => 'required|integer|min:1|max:999',
            'status' => 'required|in:draft,aktif,arsip',
        ], [
            'judul.required' => 'Judul berita harus diisi',
            'deskripsi_singkat.required' => 'Deskripsi singkat harus diisi',
            'deskripsi_singkat.max' => 'Deskripsi singkat maksimal 500 karakter',
            'gambar_thumbnail.image' => 'File harus berupa gambar',
            'gambar_thumbnail.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'gambar_thumbnail.max' => 'Ukuran gambar maksimal 2MB',
            'url_berita.required' => 'URL berita harus diisi',
            'url_berita.url' => 'URL berita tidak valid',
            'kategori.required' => 'Kategori harus dipilih',
            'tanggal_berita.required' => 'Tanggal berita harus diisi',
            'urutan_tampil.required' => 'Urutan tampil harus diisi',
        ]);

        $validated['is_featured'] = $request->has('is_featured');

        // Handle upload gambar baru
        if ($request->hasFile('gambar_thumbnail')) {
            try {
                // Hapus gambar lama
                if ($berita->gambar_thumbnail) {
                    $oldImagePath = public_path('img/berita/' . $berita->gambar_thumbnail);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $file = $request->file('gambar_thumbnail');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // Pastikan folder ada
                $destinationPath = public_path('img/berita');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                // Pindahkan file
                $file->move($destinationPath, $filename);
                $validated['gambar_thumbnail'] = $filename;
            } catch (\Exception $e) {
                return back()->withErrors(['gambar_thumbnail' => 'Gagal mengunggah gambar: ' . $e->getMessage()])
                    ->withInput();
            }
        }

        $berita->update($validated);

        return redirect()->route('admin.akademik.berita.index')
            ->with('success', 'Berita berhasil diperbarui!');
    }

    public function beritaDestroy($id)
    {
        $berita = \App\Models\Berita::findOrFail($id);

        // Hapus gambar
        if ($berita->gambar_thumbnail) {
            $imagePath = public_path('img/berita/' . $berita->gambar_thumbnail);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $berita->delete();

        return redirect()->route('admin.akademik.berita.index')
            ->with('success', 'Berita berhasil dihapus!');
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
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar_flyer' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link_url' => 'nullable|url',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'target_audience' => 'required|in:semua,siswa,guru,wali_kelas,orang_tua',
            'urutan_tampil' => 'required|integer|min:1',
            'status' => 'required|in:draft,aktif,nonaktif',
        ]);

        $validated['dibuat_oleh'] = auth()->id();

        if ($request->hasFile('gambar_flyer')) {
            $validated['gambar_flyer'] = $request->file('gambar_flyer')
                ->store('flyer/gambar', 'public');
        }

        \App\Models\Flyer::create($validated);

        return redirect()->route('admin.akademik.flyer.index')
            ->with('success', 'Flyer berhasil ditambahkan!');
    }

    public function flyerEdit($id)
    {
        $response = parent::flyerEdit($id);
        return $this->wrapView($response, 'flyer.form');
    }

    public function flyerUpdate(Request $request, $id)
    {
        $flyer = \App\Models\Flyer::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar_flyer' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link_url' => 'nullable|url',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'target_audience' => 'required|in:semua,siswa,guru,wali_kelas,orang_tua',
            'urutan_tampil' => 'required|integer|min:1',
            'status' => 'required|in:draft,aktif,nonaktif',
        ]);

        if ($request->hasFile('gambar_flyer')) {
            if ($flyer->gambar_flyer) {
                \Storage::disk('public')->delete($flyer->gambar_flyer);
            }

            $validated['gambar_flyer'] = $request->file('gambar_flyer')
                ->store('flyer/gambar', 'public');
        }

        $flyer->update($validated);

        return redirect()->route('admin.akademik.flyer.index')
            ->with('success', 'Flyer berhasil diperbarui!');
    }

    public function flyerDestroy($id)
    {
        $flyer = \App\Models\Flyer::findOrFail($id);

        if ($flyer->gambar_flyer) {
            \Storage::disk('public')->delete($flyer->gambar_flyer);
        }

        $flyer->delete();

        return redirect()->route('admin.akademik.flyer.index')
            ->with('success', 'Flyer berhasil dihapus!');
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
