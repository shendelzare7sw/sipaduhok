<?php

use App\Models\AppSetting;

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

        // Get chatbot enabled roles from settings
        $enabledRolesSetting = AppSetting::where('key', 'chatbot_enabled_roles')->first();

        // If setting doesn't exist, use default values
        if (!$enabledRolesSetting) {
            // Default: Enable for all staff roles, disable for siswa and orang_tua
            $defaultEnabledRoles = [
                'ketua_pkbm' => true,
                'wakil_kepala_sekolah' => true,
                'sekretaris' => true,
                'bendahara' => true,
                'wali_kelas' => true,
                'guru_pengajar' => true,
                'siswa' => false,
                'orang_tua' => false,
            ];

            return $defaultEnabledRoles[$role] ?? false;
        }

        // Parse JSON value
        $enabledRoles = json_decode($enabledRolesSetting->value, true);

        // Return role status (default false if not found)
        return $enabledRoles[$role] ?? false;
    }
}
