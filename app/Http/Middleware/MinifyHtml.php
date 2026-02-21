<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MinifyHtml
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya proses HTML response
        $contentType = $response->headers->get('Content-Type', '');
        if (!str_contains($contentType, 'text/html')) {
            return $response;
        }

        $content = $response->getContent();
        if ($content === false || strlen($content) === 0) {
            return $response;
        }

        $response->setContent($this->minify($content));

        return $response;
    }

    protected function minify(string $html): string
    {
        $blocks = [];

        // Simpan <pre> dan <textarea> persis apa adanya (whitespace sensitif)
        $html = preg_replace_callback(
            '/<(pre|textarea)(\s[^>]*)?>.*?<\/\1>/si',
            function ($matches) use (&$blocks) {
                $key = '##PRESERVE_BLOCK_' . count($blocks) . '##';
                $blocks[$key] = $matches[0];
                return $key;
            },
            $html
        );

        // Minify CSS di dalam <style>
        $html = preg_replace_callback(
            '/<style(\s[^>]*)?>.*?<\/style>/si',
            function ($matches) {
                $tag    = $matches[0];
                $open   = strpos($tag, '>');
                $close  = strrpos($tag, '</style>');
                $attrs  = substr($tag, 0, $open + 1);
                $css    = substr($tag, $open + 1, $close - $open - 1);
                return $attrs . $this->minifyCss($css) . '</style>';
            },
            $html
        );

        // Minify JS di dalam <script> (konservatif: hanya hapus komentar block)
        $html = preg_replace_callback(
            '/<script(\s[^>]*)?>.*?<\/script>/si',
            function ($matches) {
                $tag   = $matches[0];
                $open  = strpos($tag, '>');
                $close = strrpos($tag, '</script>');
                $attrs = substr($tag, 0, $open + 1);
                $js    = substr($tag, $open + 1, $close - $open - 1);
                return $attrs . $this->minifyJs($js) . '</script>';
            },
            $html
        );

        // Hapus komentar HTML (kecuali IE conditionals)
        $html = preg_replace('/<!--(?!\s*\[if\s).*?-->/s', '', $html);

        // Hapus whitespace antar tag
        $html = preg_replace('/>\s+</', '><', $html);

        // Collapse multiple spasi jadi satu
        $html = preg_replace('/[^\S\r\n]{2,}/', ' ', $html);

        // Hapus spasi di awal/akhir setiap baris
        $html = preg_replace('/^\s+|\s+$/m', '', $html);

        // Hapus baris kosong berurutan
        $html = preg_replace('/\n{2,}/', "\n", $html);

        // Kembalikan blok <pre> dan <textarea> yang dipreserve
        foreach ($blocks as $key => $block) {
            $html = str_replace($key, $block, $html);
        }

        return trim($html);
    }

    protected function minifyCss(string $css): string
    {
        // Hapus komentar CSS /* ... */
        $css = preg_replace('/\/\*.*?\*\//s', '', $css);

        // Hapus whitespace di awal/akhir setiap baris
        $css = preg_replace('/^\s+|\s+$/m', '', $css);

        // Hapus spasi di sekitar : ; { } , >
        $css = preg_replace('/\s*([:;{},>])\s*/', '$1', $css);

        // Hapus titik koma sebelum }
        $css = str_replace(';}', '}', $css);

        // Collapse multiple whitespace/newline
        $css = preg_replace('/\s{2,}/', ' ', $css);

        // Hapus baris kosong
        $css = preg_replace('/\n+/', '', $css);

        return trim($css);
    }

    protected function minifyJs(string $js): string
    {
        // Hapus komentar block /* ... */ (konservatif, aman untuk kode umum)
        $js = preg_replace('/\/\*.*?\*\//s', '', $js);

        // Hapus spasi di awal/akhir setiap baris
        $js = preg_replace('/^\s+|\s+$/m', '', $js);

        // Hapus baris kosong berurutan
        $js = preg_replace('/\n{2,}/', "\n", $js);

        return trim($js);
    }
}
