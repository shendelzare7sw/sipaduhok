<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    private function getPageContent($slug)
    {
        $page = LandingPage::with('sections')->where('slug', $slug)->first();

        if (!$page) {
            return null; // Handle 404 gracefully in view or abort
        }

        // Transform content for easier access in view
        // We want to be able to do $page->content->hero['title'] or similar.
        // Or better, a helper object/array.

        // Let's attach a helper method to the page model or just parse it here.
        // For simplicity, we pass the $page object which has the 'getSection' helper we defined.

        return $page;
    }

    public function home()
    {
        $page = $this->getPageContent('home');

        // Fetch latest news for carousel
        $beritaList = \App\Models\Berita::aktif()
            ->latest('tanggal_berita') // Prioritize latest by date
            ->take(6)
            ->get();

        return view('home', compact('page', 'beritaList'));
    }

    public function tentangSekolah()
    {
        $page = $this->getPageContent('tentang-sekolah');
        return view('tentang-sekolah', compact('page'));
    }

    public function profilGuru()
    {
        $page = $this->getPageContent('profil-guru');
        return view('profil-guru', compact('page'));
    }

    public function visiMisi()
    {
        $page = $this->getPageContent('visi-misi');
        return view('visi-misi', compact('page'));
    }

    public function strukturOrganisasi()
    {
        $page = $this->getPageContent('struktur-organisasi');
        return view('struktur-organisasi', compact('page'));
    }

    public function programPaudTk()
    {
        $page = $this->getPageContent('program-paud-tk');
        return view('program-paud-tk', compact('page'));
    }

    public function programSdSma()
    {
        $page = $this->getPageContent('program-sd-sma');
        return view('program-sd-sma', compact('page'));
    }

    public function programInklusi()
    {
        $page = $this->getPageContent('program-inklusi');
        return view('program-inklusi', compact('page'));
    }

    public function programTerapi()
    {
        $page = $this->getPageContent('program-terapi');
        return view('program-terapi', compact('page'));
    }

    public function fasilitas()
    {
        $page = $this->getPageContent('fasilitas');
        return view('fasilitas', compact('page'));
    }

    public function ppdb()
    {
        $page = $this->getPageContent('ppdb');
        return view('ppdb', compact('page'));
    }

    public function galeri()
    {
        $page = $this->getPageContent('galeri');
        return view('galeri', compact('page'));
    }

    public function kontak()
    {
        $page = $this->getPageContent('kontak');
        return view('kontak', compact('page'));
    }

    // Add other methods as we implement them
}
