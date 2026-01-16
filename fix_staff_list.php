<?php

use App\Models\LandingPageSection;

$section = LandingPageSection::where('section_key', 'staff_list')->first();

if ($section) {
    $content = $section->content;

    // If there's an 'items' key, use that as the new content (direct array)
    if (isset($content['items']) && is_array($content['items'])) {
        $section->content = $content['items'];
        $section->save();
        echo "Fixed: staff_list now has direct array structure\n";
    } else {
        echo "No fix needed\n";
    }
} else {
    echo "staff_list section not found\n";
}
