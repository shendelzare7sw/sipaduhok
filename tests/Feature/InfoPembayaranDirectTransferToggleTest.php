<?php

namespace Tests\Feature;

use App\Models\InfoPembayaran;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class InfoPembayaranDirectTransferToggleTest extends TestCase
{
    public function test_hanya_admin_dapat_menyembunyikan_dan_mengaktifkan_direct_transfer(): void
    {
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => '127.0.0.1',
            'database.connections.mysql.port' => '3306',
            'database.connections.mysql.database' => 'db_sipaduhok',
            'database.connections.mysql.username' => 'root',
            'database.connections.mysql.password' => '',
        ]);
        DB::purge('mysql');

        DB::connection('mysql')->beginTransaction();
        try {
            $suffix = substr(md5(uniqid('', true)), 0, 8);
            $info = InfoPembayaran::getInstance();
            $info->update([
                'nama_bank' => 'Bank Test',
                'rekening_bank' => '1234567890',
                'atas_nama' => 'Sekolah Test',
                'direct_transfer_enabled' => true,
            ]);

            $admin = $this->makeUser('admin', "admin.transfer.$suffix@test.local");
            $this->actingAs($admin)->withoutMiddleware()
                ->post(route('admin.keuangan.info-pembayaran.update'), [
                    'type' => 'direct_transfer_toggle',
                ])
                ->assertSessionHas('success');

            $this->assertFalse((bool) $info->fresh()->direct_transfer_enabled);
            $this->assertFalse($info->fresh()->isDirectTransferEnabled());

            $this->actingAs($admin)->withoutMiddleware()
                ->post(route('admin.keuangan.info-pembayaran.update'), [
                    'type' => 'direct_transfer_toggle',
                    'direct_transfer_enabled' => '1',
                ])
                ->assertSessionHas('success');

            $this->assertTrue((bool) $info->fresh()->direct_transfer_enabled);
            $this->assertTrue($info->fresh()->isDirectTransferEnabled());
            $this->assertFalse(\Illuminate\Support\Facades\Route::has('bendahara.info-pembayaran.update'));
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }

    private function makeUser(string $role, string $email): User
    {
        return User::create([
            'name' => 'User '.$email,
            'email' => $email,
            'role' => $role,
            'password' => bcrypt('password'),
        ]);
    }
}
