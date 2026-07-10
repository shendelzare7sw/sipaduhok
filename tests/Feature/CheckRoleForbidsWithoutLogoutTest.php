<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

/**
 * Regresi-guard F-06 (Opsi 1): gagal otorisasi peran → 403 tanpa mengakhiri sesi.
 * Dulu CheckRole me-logout paksa user (buang sesi) & membocorkan nama role di pesan.
 * Sekarang: user tetap login, hanya halaman itu ditolak (403), pesan generik.
 */
class CheckRoleForbidsWithoutLogoutTest extends TestCase
{
    public function test_salah_role_ditolak_403_dan_tetap_login(): void
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

            $siswa = new User();
            $siswa->name = 'Siswa F06';
            $siswa->email = "siswa.f06.$suffix@test.local";
            $siswa->role = 'siswa';
            $siswa->is_active = true;
            $siswa->password = bcrypt('password');
            $siswa->save();

            $this->actingAs($siswa);

            $middleware = new CheckRole();
            $request = Request::create('/dummy', 'GET');

            // Negatif (F-06): akses role yang bukan miliknya → HttpException 403.
            $threw = false;
            try {
                $middleware->handle($request, fn ($r) => response('ok'), 'admin');
            } catch (HttpException $e) {
                $threw = true;
                $this->assertSame(403, $e->getStatusCode());
                // Pesan generik: tidak menyebut nama role apa pun.
                $this->assertStringNotContainsString('siswa', strtolower($e->getMessage()));
                $this->assertStringNotContainsString('admin', strtolower($e->getMessage()));
            }
            $this->assertTrue($threw, 'CheckRole harus abort 403 untuk role yang salah');

            // KRUSIAL: user TIDAK di-logout — sesi tetap hidup.
            $this->assertTrue(auth()->check(), 'User tidak boleh ter-logout karena salah role');
            $this->assertSame($siswa->id, auth()->id());

            // Positif: role yang benar tetap lolos.
            $response = $middleware->handle($request, fn ($r) => response('ok'), 'siswa');
            $this->assertSame('ok', $response->getContent());
        } finally {
            DB::connection('mysql')->rollBack();
        }
    }
}
