<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    /**
     * Preview file inline (instead of download)
     */
    public function preview(Request $request)
    {
        $path = $request->query('path');

        if (!$path) {
            abort(404);
        }

        // Prevent directory traversal
        if (str_contains($path, '..')) {
            abort(403);
        }

        // Check if file exists in public disk or default disk
        // Assuming 'public' disk is used for storage/ assets
        $disk = Storage::disk('public');

        if (!$disk->exists($path)) {
            abort(404);
        }

        $fullPath = storage_path('app/public/' . $path);

        if (!file_exists($fullPath)) {
            abort(404);
        }

        return response()->file($fullPath);
    }
}
