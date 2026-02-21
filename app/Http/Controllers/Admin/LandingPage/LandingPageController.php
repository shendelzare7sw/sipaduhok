<?php

namespace App\Http\Controllers\Admin\LandingPage;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;
use App\Models\LandingPageSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LandingPageController extends Controller
{
    public function index()
    {
        $pages = LandingPage::orderBy('order')->get();
        return view('admin.landing-pages.index', compact('pages'));
    }

    public function edit(LandingPage $landingPage)
    {
        $landingPage->load('sections');
        return view('admin.landing-pages.edit', compact('landingPage'));
    }

    public function update(Request $request, LandingPage $landingPage)
    {
        $data = $request->input('sections', []);

        foreach ($landingPage->sections as $section) {
            if (!isset($data[$section->id])) {
                continue; 
            }

            \Illuminate\Support\Facades\Log::info("Updating Section ID: {$section->id}", ['input' => $data[$section->id]]);

            $sectionInput = $data[$section->id];

            // Handle Type Specific Logic
            if ($section->type === 'list') {
                $content = $section->content;

                // Check structure of existing content
                // If it has 'items' key, it's Program-style.
                // If it's a direct array (index 0 exists), it's Stats-style.
                $isDirectArray = false;
                if (is_array($content) && !empty($content) && isset($content[0])) {
                     $isDirectArray = true;
                } elseif (isset($content['items']) && is_array($content['items'])) {
                     $isDirectArray = false;
                } else {
                    // Fallback/Empty: Check if input looks like direct array items without 'items' wrapper in input?
                    // Actually, the form ALWAYS sends `items` array for list types (see edit.blade.php).
                    // So we must rely on what the section is SUPPOSED to be.
                    // Let's assume complex sections (Programs) have 'items' key structure.
                    // Simple lists (Stats) tend to be direct arrays.
                    // If content is empty/null, default to 'items' structure UNLESS it is specifically 'stats' key? 
                    // But we don't have section key here easily available unless we load it. 
                    // Fortunately $section->section_key is available.
                    if ($section->section_key === 'stats') {
                        $isDirectArray = true;
                    } elseif (
                        Str::contains($section->section_key, 'ruang_') || 
                        Str::contains($section->section_key, 'area_') || 
                        Str::contains($section->section_key, 'perpustakaan') || 
                        Str::contains($section->section_key, 'gallery') ||
                        Str::contains($section->section_key, 'biaya_')
                    ) {
                        $isDirectArray = false;
                    }
                }

                if ($isDirectArray) {
                     // STATS Style: Direct Array
                     // The form input still comes as ['items' => [...]] because of how the form is built.
                     // We need to extract that and save as direct array.
                     
                     if (isset($sectionInput['items']) && is_array($sectionInput['items'])) {
                        $newItems = [];
                        foreach ($sectionInput['items'] as $index => $item) {
                            $processedItem = $item;

                            // Handle image upload for list item
                            $sectionIdStr = (string) $section->id;
                            $allFiles = $request->allFiles();
                            if (isset($allFiles['sections'][$sectionIdStr]['items'][$index])) {
                                foreach ($allFiles['sections'][$sectionIdStr]['items'][$index] as $fileKey => $file) {
                                    if ($file instanceof \Illuminate\Http\UploadedFile) {
                                        $path = $file->store('landing-pages', 'public');
                                        $processedItem[$fileKey] = 'storage/' . $path;
                                    }
                                }
                            }
                            
                            $newItems[] = $processedItem;
                        }
                        $section->content = $newItems;
                     }
                } else {
                    // PROGRAM Style: {items: [...], header: {...}}
                    if (isset($sectionInput['items']) && is_array($sectionInput['items'])) {
                        $newItems = [];
                        foreach ($sectionInput['items'] as $index => $item) {
                            $processedItem = $item;

                            // Handle image upload for list item
                            $sectionIdStr = (string) $section->id;
                            $allFiles = $request->allFiles();
                            if (isset($allFiles['sections'][$sectionIdStr]['items'][$index])) {
                                foreach ($allFiles['sections'][$sectionIdStr]['items'][$index] as $fileKey => $file) {
                                    if ($file instanceof \Illuminate\Http\UploadedFile) {
                                        $path = $file->store('landing-pages', 'public');
                                        $processedItem[$fileKey] = 'storage/' . $path;
                                    }
                                }
                            }

                            $newItems[] = $processedItem;
                        }

                        $content['items'] = $newItems;

                        // Handle Header fields if any
                        if (isset($sectionInput['header'])) {
                            $content['header'] = array_merge($content['header'] ?? [], $sectionInput['header']);
                        }

                        // Handle Header File Uploads (Icon/Image in Header)
                        $sectionIdStr = (string) $section->id;
                        $allFiles = $request->allFiles();
                        if (isset($allFiles['sections'][$sectionIdStr]['header'])) {
                            foreach ($allFiles['sections'][$sectionIdStr]['header'] as $key => $file) {
                                if ($file instanceof \Illuminate\Http\UploadedFile) {
                                    $path = $file->store('landing-pages', 'public');
                                    // Ensure header array exists
                                    if (!isset($content['header'])) {
                                        $content['header'] = [];
                                    }
                                    $content['header'][$key] = 'storage/' . $path;
                                }
                            }
                        }

                        $section->content = $content;
                    }
                }

            } else {
                // Text / Rich Text / Image
                $content = $section->content;

                // 1. Update text fields first
                foreach ($sectionInput as $key => $value) {
                    // We update content with ALL input values first.
                    // This includes the hidden inputs that preserve old image paths.
                    // If a specific key corresponds to a file upload, it will be overwritten in the next loop.
                    $content[$key] = $value;
                }

                // 2. Handle ANY File Uploads
                // We iterate through all keys in the input that *might* be files
                $allFiles = $request->allFiles();
                // Cast section ID to string because form array keys come as strings
                $sectionIdKey = (string) $section->id;
                $currentSectionFiles = $allFiles['sections'][$sectionIdKey] ?? [];

                foreach ($currentSectionFiles as $fileKey => $file) {
                    // $file is already the UploadedFile object because we got it from allFiles structure
                    if ($file instanceof \Illuminate\Http\UploadedFile) {
                        $path = $file->store('landing-pages', 'public');
                        $content[$fileKey] = 'storage/' . $path;
                    }
                }

                $section->content = $content;
            }

            $section->save();
        }

        return redirect()->route('admin.landing-pages.index')->with('success', 'Halaman berhasil diperbarui.');
    }

    public function reset(LandingPage $landingPage)
    {
        // Re-run the seeders to restore default values.
        // --force is REQUIRED for production environment, otherwise Laravel silently blocks the command.
        // Since LandingPageSeeder uses updateOrCreate based on slug/keys, it will restore the default values.

        try {
            \Illuminate\Support\Facades\Artisan::call('db:seed', [
                '--class' => 'LandingPageSeeder',
                '--force' => true,
            ]);
            \Illuminate\Support\Facades\Artisan::call('db:seed', [
                '--class' => 'FasilitasLibrarySeeder',
                '--force' => true,
            ]);

            return redirect()->back()->with('success', 'Konten halaman berhasil direset ke pengaturan awal.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Landing page reset failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mereset konten halaman: ' . $e->getMessage());
        }
    }
}
