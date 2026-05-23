<?php

use App\Models\LandingPage;
use App\Models\LandingPageSection;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        $ppdb = LandingPage::where('slug', 'ppdb')->first();
        if (!$ppdb) {
            return;
        }

        LandingPageSection::where('landing_page_id', $ppdb->id)
            ->whereIn('section_key', ['investasi', 'biaya_paud', 'biaya_sd', 'biaya_smp', 'biaya_sma'])
            ->update(['is_visible' => false]);
    }

    public function down(): void
    {
        $ppdb = LandingPage::where('slug', 'ppdb')->first();
        if (!$ppdb) {
            return;
        }

        LandingPageSection::where('landing_page_id', $ppdb->id)
            ->whereIn('section_key', ['investasi', 'biaya_paud', 'biaya_sd', 'biaya_smp', 'biaya_sma'])
            ->update(['is_visible' => true]);
    }
};
