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
                continue; // Should not happen if form is correct, or maybe section content not changed?
            }

            $sectionInput = $data[$section->id];

            // Handle Type Specific Logic
            if ($section->type === 'list') {
                $content = $section->content;

                // Check if input has 'items' key (Program-style: {items: [...], header: {...}})
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

                    $section->content = $content;
                } else {
                    // Direct array structure (Stats-style: [...])
                    // The form sends items under 'items' key, but we store as direct array
                    // Actually, let's check if content IS a direct array
                    $existingContent = $section->content;
                    $isDirectArray = is_array($existingContent) && !empty($existingContent) && isset($existingContent[0]);

                    if ($isDirectArray && isset($sectionInput['items'])) {
                        // Save as direct array (no 'items' wrapper)
                        $newItems = [];
                        foreach ($sectionInput['items'] as $index => $item) {
                            $newItems[] = $item;
                        }
                        $section->content = $newItems;
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
        // Simple and brutal: run the specific seeder (or all seeders if specific not possible easily without refactoring seeder)
        // Since LandingPageSeeder uses updateOrCreate based on slug/keys, it will restore the default values

        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'LandingPageSeeder']);

        return redirect()->back()->with('success', 'Konten halaman berhasil direset ke pengaturan awal.');
    }
}
