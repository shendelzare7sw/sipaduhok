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

        $fullPath = $disk->path($path);
        
        // Determine mime type manually for common types to ensure inline preview
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mimeType = $disk->mimeType($path);

        if ($extension === 'pdf') {
            $mimeType = 'application/pdf';
        } elseif (in_array($extension, ['jpg', 'jpeg'])) {
            $mimeType = 'image/jpeg';
        } elseif ($extension === 'png') {
            $mimeType = 'image/png';
        }

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }
}
