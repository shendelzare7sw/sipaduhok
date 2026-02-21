<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    /**
     * Allowed file extensions for preview.
     * Restricts access to safe file types only.
     */
    private const ALLOWED_EXTENSIONS = [
        // Images
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico',
        // Documents
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
        // Media
        'mp4', 'mp3', 'wav', 'ogg', 'webm',
    ];

    /**
     * Preview file inline (instead of download)
     */
    public function preview(Request $request)
    {
        $path = $request->query('path');

        if (!$path) {
            abort(404);
        }

        // Sanitize: reject null bytes
        if (str_contains($path, "\0")) {
            abort(403, 'Invalid path');
        }

        // Prevent directory traversal
        if (str_contains($path, '..') || str_contains($path, '\\')) {
            abort(403, 'Invalid path');
        }

        // Normalize slashes and reject absolute paths
        $path = ltrim(str_replace('\\', '/', $path), '/');

        // Reject paths starting with dots (hidden files) or containing suspicious segments
        if (preg_match('/(?:^|\/)\./', $path)) {
            abort(403, 'Invalid path');
        }

        // Check file extension against allowlist
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            abort(403, 'File type not allowed');
        }

        // Check if file exists in public disk
        $disk = Storage::disk('public');

        if (!$disk->exists($path)) {
            abort(404);
        }

        $fullPath = storage_path('app/public/' . $path);

        if (!file_exists($fullPath)) {
            abort(404);
        }

        // Return file with inline disposition (prevent download prompt)
        return response()->file($fullPath, [
            'Content-Disposition' => 'inline',
        ]);
    }
}
