<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CaptchaController extends Controller
{
    /**
     * Generate CAPTCHA image and store the code in session.
     */
    public function generate()
    {
        // Generate random 5-character alphanumeric string
        $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        $captchaString = '';
        for ($i = 0; $i < 5; $i++) {
            $captchaString .= $characters[rand(0, strlen($characters) - 1)];
        }

        // Store in session
        session(['captcha' => $captchaString]);

        // Target Output Size (Larger for UI)
        $targetWidth = 160;
        $targetHeight = 60;
        
        // Base Drawing Size (smaller so built-in font size 5 looks big when scaled up)
        $baseWidth = 120;
        $baseHeight = 45;

        // Create base image
        $image = imagecreatetruecolor($baseWidth, $baseHeight);

        // Colors
        $bgColor = imagecolorallocate($image, 243, 244, 246); // Tailwind gray-100
        $textColor = imagecolorallocate($image, 22, 95, 172); // SIPADU primary #165fac
        $lineColor = imagecolorallocate($image, 209, 213, 219); // Tailwind gray-300
        $dotColor = imagecolorallocate($image, 156, 163, 175); // Tailwind gray-400

        // Fill background
        imagefill($image, 0, 0, $bgColor);

        // Add some noise (lines)
        for ($i = 0; $i < 4; $i++) {
            imageline($image, 0, rand() % $baseHeight, $baseWidth, rand() % $baseHeight, $lineColor);
        }

        // Add some noise (dots)
        for ($i = 0; $i < 30; $i++) {
            imagesetpixel($image, rand() % $baseWidth, rand() % $baseHeight, $dotColor);
        }

        // Write characters
        // Font size 5 is the max built-in font size
        $charWidth = 12; // Approx width of font 5
        $startX = ($baseWidth - ($charWidth * 5)) / 2;
        
        for ($i = 0; $i < 5; $i++) {
            $x = $startX + ($i * $charWidth) + rand(-2, 2);
            $y = ($baseHeight / 2) - 8 + rand(-3, 3);
            
            imagestring($image, 5, $x, $y, $captchaString[$i], $textColor);
        }

        // Scale up the image for better readability
        $scaledImage = imagecreatetruecolor($targetWidth, $targetHeight);
        imagecopyresampled($scaledImage, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $baseWidth, $baseHeight);

        // Output image
        ob_start();
        imagepng($scaledImage);
        $imageData = ob_get_clean();
        
        imagedestroy($image);
        imagedestroy($scaledImage);

        return response($imageData)->header('Content-Type', 'image/png')->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    /**
     * Refresh CAPTCHA via AJAX/API.
     * Returns the URL with a timestamp to bust cache.
     */
    public function refresh()
    {
        return response()->json([
            'url' => route('captcha') . '?t=' . time()
        ]);
    }
}
