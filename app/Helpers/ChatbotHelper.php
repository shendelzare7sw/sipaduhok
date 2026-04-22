<?php

use App\Models\AppSetting;
use Illuminate\Support\Facades\Cache;

if (!function_exists('canAccessChatbot')) {
    /**
     * Check if a user role can access the AI Chatbot
     *
     * @param string $role User role (admin, guru, siswa, etc.)
     * @return bool
     */
    function canAccessChatbot($role)
    {
        // Admin always has access (no toggle)
        if ($role === 'admin') {
            return true;
        }

        // Get from cache or fetch and cache
        $enabledRoles = Cache::remember('chatbot_enabled_roles', 3600, function () {
            $enabledRolesSetting = AppSetting::where('key', 'chatbot_enabled_roles')->first();
            
            if (!$enabledRolesSetting) {
                return [
                    'ketua_pkbm' => true,
                    'wakil_kepala_sekolah' => true,
                    'sekretaris' => true,
                    'bendahara' => true,
                    'wali_kelas' => true,
                    'guru_pengajar' => true,
                    'siswa' => false,
                    'orang_tua' => false,
                ];
            }
            
            return json_decode($enabledRolesSetting->value, true);
        });

        // Return role status (default false if not found)
        return $enabledRoles[$role] ?? false;
    }
}

if (!function_exists('isLlmModeEnabled')) {
    /**
     * Check if LLM Mode (Generative AI) is enabled globally
     *
     * @return bool
     */
    function isLlmModeEnabled()
    {
        return Cache::remember('llm_mode_enabled', 3600, function () {
            $setting = AppSetting::where('key', 'llm_mode_enabled')->first();
            return $setting ? filter_var($setting->value, FILTER_VALIDATE_BOOLEAN) : true;
        });
    }
}
