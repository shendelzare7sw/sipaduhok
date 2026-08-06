<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\Request;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        $kategori = $request->get('kategori', 'all');
        $search = $request->get('search');

        // Berita Utama (Featured)
        $beritaUtama = Berita::aktif()
            ->featured()
            ->ordered()
            ->first();

        // Berita Lainnya (exclude featured)
        $query = Berita::aktif()->ordered();
        
        if ($beritaUtama) {
            $query->where('id', '!=', $beritaUtama->id);
        }

        if ($kategori && $kategori !== 'all') {
            $query->kategori($kategori);
        }

        if ($search) {
            $query->search($search);
        }

        $beritaList = $query->get();

        return view('berita', compact('beritaUtama', 'beritaList', 'kategori', 'search'));
    }
}