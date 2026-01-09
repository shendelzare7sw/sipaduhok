<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Admin',
                'level' => 1, // Highest level - full access
                'description' => 'Full access to all system features and configurations',
                'permissions' => ['*'], // all permissions
            ],
            [
                'name' => 'ketua_pkbm',
                'display_name' => 'Ketua PKBM',
                'level' => 2,
                'description' => 'Head of PKBM with access to monitoring and reports',
                'permissions' => ['view_all_reports', 'view_dashboard', 'view_analytics'],
            ],
            [
                'name' => 'wakil_kepala_sekolah',
                'display_name' => 'Wakil Kepala Sekolah',
                'level' => 2,
                'description' => 'Deputy head of school with academic management access',
                'permissions' => ['manage_academic_year', 'manage_classes', 'manage_students', 'assign_wali_kelas', 'manage_schedules', 'manage_subjects', 'monitor_teachers', 'monitor_students', 'send_warnings'],
            ],
            [
                'name' => 'sekretaris',
                'display_name' => 'Sekretaris',
                'level' => 3,
                'description' => 'Manage academic calendar, announcements, and news',
                'permissions' => ['manage_calendar', 'manage_announcements', 'manage_news', 'view_reports'],
            ],
            [
                'name' => 'bendahara',
                'display_name' => 'Bendahara',
                'level' => 2,
                'description' => 'Manage financial transactions, payments, and invoices',
                'permissions' => ['manage_payments', 'manage_invoices', 'view_financial_reports', 'verify_payments'],
            ],
            [
                'name' => 'wali_kelas',
                'display_name' => 'Wali Kelas',
                'level' => 4,
                'description' => 'Manage students in assigned class',
                'permissions' => ['manage_class_students', 'input_scores', 'input_attendance', 'view_class_reports'],
            ],
            [
                'name' => 'guru_pengajar',
                'display_name' => 'Guru Pengajar',
                'level' => 4,
                'description' => 'Teach subjects and manage student scores',
                'permissions' => ['input_scores', 'input_attendance', 'manage_assignments', 'view_students'],
            ],
            [
                'name' => 'orang_tua',
                'display_name' => 'Orang Tua/Wali',
                'level' => 5,
                'description' => 'View student information, payments, and academic progress',
                'permissions' => ['view_own_students', 'view_payments', 'make_payments', 'view_scores', 'view_attendance'],
            ],
            [
                'name' => 'siswa',
                'display_name' => 'Siswa',
                'level' => 6,
                'description' => 'View own academic information and assignments',
                'permissions' => ['view_own_scores', 'view_own_attendance', 'submit_assignments', 'view_schedule'],
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }

        $this->command->info('Roles seeded successfully!');
    }
}
