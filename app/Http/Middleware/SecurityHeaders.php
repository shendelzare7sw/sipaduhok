<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Security headers to protect against common web attacks:
     * - Clickjacking (X-Frame-Options)
     * - MIME sniffing (X-Content-Type-Options)
     * - XSS (X-XSS-Protection)
     * - Referrer leakage (Referrer-Policy)
     * - Permission abuse (Permissions-Policy)
     * - HTTPS enforcement (Strict-Transport-Security)
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent clickjacking - only allow same origin to embed in iframes
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Enable browser XSS protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Control referrer information
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Restrict browser features/permissions
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // CSP minimal & aman (F-05): TIDAK membatasi script/style/img/font agar UI
        // (Sneat, Vite, Bootstrap, FontAwesome, inline script Blade) tetap berjalan.
        // Hanya menutup vektor yang jarang dipakai app normal:
        // - frame-ancestors 'self' : anti-clickjacking (pelengkap X-Frame-Options)
        // - object-src 'none'      : blokir <object>/<embed>/plugin lawas
        // - base-uri 'self'        : cegah pembajakan <base>
        // Catatan: policy penuh (script-src/default-src) perlu inventarisasi aset dulu.
        if (!$response->headers->has('Content-Security-Policy')) {
            $response->headers->set(
                'Content-Security-Policy',
                "frame-ancestors 'self'; object-src 'none'; base-uri 'self'"
            );
        }

        // HSTS - paksa HTTPS. Hanya dikirim pada koneksi HTTPS (spec: diabaikan di HTTP).
        // Tanpa includeSubDomains agar tak mengunci subdomain yang mungkin belum HTTPS.
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000');
        }

        return $response;
    }
}
