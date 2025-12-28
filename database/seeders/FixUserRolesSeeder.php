<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\TenagaPendidik;

class FixUserRolesSeeder extends Seeder
{
    /**
     * Fix all users to have proper role_id
     */
    public function run(): void
    {
        // Get all roles
        $roles = Role::all()->keyBy('name');

        echo "=== Fixing User Roles ===\n\n";

        // Fix Siswa users
        $siswaRole = $roles->get('siswa');
        if ($siswaRole) {
            $siswaUsers = Siswa::with('user')->get();
            foreach ($siswaUsers as $siswa) {
                if ($siswa->user) {
                    $siswa->user->update(['role_id' => $siswaRole->id]);
                    echo "✓ Fixed siswa: {$siswa->user->name} (role_id: {$siswaRole->id})\n";
                }
            }
        }

        // Fix Tenaga Pendidik users based on jabatan
        $tenagaPendidikUsers = TenagaPendidik::with('user')->get();
        foreach ($tenagaPendidikUsers as $tp) {
            if (!$tp->user) continue;

            $roleName = match($tp->jabatan) {
                'ketua_pkbm' => 'ketua_pkbm',
                'sekretaris' => 'sekretaris',
                'bendahara' => 'bendahara',
                'wali_kelas' => 'wali_kelas',
                'guru_pengajar' => 'guru_pengajar',
                default => 'guru_pengajar'
            };

            $role = $roles->get($roleName);
            if ($role) {
                $tp->user->update(['role_id' => $role->id]);
                echo "✓ Fixed tenaga pendidik: {$tp->user->name} as {$roleName} (role_id: {$role->id})\n";
            }
        }

        // Fix admin user (create if not exists)
        $adminRole = $roles->get('admin');
        if ($adminRole) {
            $adminUser = User::where('email', 'admin@sipaduhok.test')->first();
            if ($adminUser) {
                $adminUser->update(['role_id' => $adminRole->id]);
                echo "✓ Fixed admin: {$adminUser->name} (role_id: {$adminRole->id})\n";
            } else {
                echo "⚠ Admin user not found. Please create manually.\n";
            }
        }

        echo "\n=== Done! ===\n";
    }
}
