<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AppSetting;

class LmsSettingController extends Controller
{
    public function index()
    {
        $setting = AppSetting::where('key', 'lms_allowed_jenjang')->first();
        $allowedJenjang = $setting ? json_decode($setting->value, true) : [];
        $allJenjang = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];

        return view('admin.lms-settings.index', compact('allowedJenjang', 'allJenjang'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'jenjang' => 'array',
            'jenjang.*' => 'string|in:KB,TKA,TKB,SD,SMP,SMA'
        ]);

        $jenjangs = $request->input('jenjang', []);

        AppSetting::updateOrCreate(
            ['key' => 'lms_allowed_jenjang'],
            ['value' => json_encode($jenjangs)]
        );

        return redirect()->back()->with('success', 'Pengaturan LMS berhasil disimpan.');
    }
}
