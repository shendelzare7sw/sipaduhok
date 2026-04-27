<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

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
     * Preview file inline (legacy route, kept for backward compatibility)
     */
    public function preview(Request $request)
    {
        $path = $request->query('path');

        if (!$path && $request->query('b64path')) {
            $path = base64_decode($request->query('b64path'));
        }

        if (!$path) {
            abort(404, 'No file path provided');
        }

        return $this->serveFileInline($path);
    }

    /**
     * Preview file using cache-based integer ID.
     * URL looks like /view-document/54321 — identical structure to validasi-izin's /preview-bukti/1
     * IDM cannot detect this as a file download because:
     *   - No file extension in URL
     *   - No query parameters with file paths
     *   - URL looks like a normal page
     */
    public function previewHash($id)
    {
        $path = Cache::get('docview_' . $id);

        if (!$path) {
            abort(404, 'Preview link expired or invalid.');
        }

        return $this->serveFileInline($path);
    }

    /**
     * Serve a file inline — EXACTLY matching PresensiController::previewBukti()
     * which uses: return response()->file($path);
     *
     * This is the pattern that works in validasi-izin without IDM interference.
     */
    private function serveFileInline(string $path)
    {
        // Sanitize: reject null bytes
        if (str_contains($path, "\0")) {
            abort(403, 'Invalid path');
        }

        // Prevent directory traversal
        if (str_contains($path, '..') || str_contains($path, '\\')) {
            abort(403, 'Invalid path');
        }

        // Normalize slashes
        $path = ltrim(str_replace('\\', '/', $path), '/');

        // Reject hidden files
        if (preg_match('/(?:^|\/)\./', $path)) {
            abort(403, 'Invalid path');
        }

        // Check file extension against allowlist
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            abort(403, 'File type not allowed');
        }

        $fullPath = storage_path('app/public/' . $path);

        if (!file_exists($fullPath)) {
            abort(404, 'File not found');
        }

        // EXACTLY like validasi-izin: response()->file($path) with NO custom headers
        // Laravel will automatically set Content-Type based on the file's MIME type
        // and Content-Disposition: inline
        return response()->file($fullPath);
    }
}
