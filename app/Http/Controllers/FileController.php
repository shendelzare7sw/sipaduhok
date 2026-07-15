<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class FileController extends Controller
{
    /**
     * Allowed file extensions for preview.
     * Restricts access to safe file types only.
     */
    private const ALLOWED_EXTENSIONS = [
        // Images (SVG SENGAJA DIKELUARKAN: disajikan inline dapat mengeksekusi
        // JavaScript embedded -> stored XSS. Semua jalur upload juga menolak svg.)
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'ico',
        // Documents
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
        // Media
        'mp4', 'mp3', 'wav', 'ogg', 'webm',
    ];

    /**
     * Preview file lewat token cache yang diikat ke pemilik.
     * URL: /view-document/{token} — token acak 48 char (via helper preview_url()).
     *
     * Keamanan:
     *  - Token acak & panjang => tidak bisa dienumerasi.
     *  - Entri cache menyimpan user_id pembuat => hanya user itu yang boleh membuka,
     *    mencegah pengguna lain memanen file yang sedang dipratinjau orang lain.
     */
    public function previewHash($id)
    {
        $entry = Cache::get('docview_' . $id);

        // Format lama (string) tidak lagi didukung; wajib array ber-user_id.
        if (!is_array($entry) || !isset($entry['path'])) {
            abort(404, 'Preview link expired or invalid.');
        }

        if (($entry['user_id'] ?? null) !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke file ini.');
        }

        return $this->serveFileInline($entry['path']);
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
