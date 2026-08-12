<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckLmsAccess;
use App\Http\Middleware\CheckSiswaMapelAccess;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\AppSetting;
use App\Models\Cabang;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class SITClosureAccessControlTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

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
    }

    protected function tearDown(): void
    {
        DB::connection('mysql')->rollBack();
        parent::tearDown();
    }

    public function test_akun_yang_dinonaktifkan_tidak_dapat_mempertahankan_session(): void
    {
        $user = $this->makeUser('siswa', false);

        $response = $this->actingAs($user)->get('/notifications');

        $response->assertOk();
        $response->assertViewIs('errors.account_inactive');
        $this->assertGuest();
    }

    public function test_siswa_tanpa_profil_atau_kelas_ditolak_middleware_mapel_dan_lms(): void
    {
        $user = $this->makeUser('siswa');
        $this->actingAs($user);

        $mapelRequest = $this->requestWithRouteParameter('mapelId', 999999);
        $this->assertMiddlewareThrows403(
            fn () => app(CheckSiswaMapelAccess::class)->handle($mapelRequest, fn () => response('leaked'))
        );

        $lmsRequest = Request::create('/siswa/lms', 'GET');
        $this->assertMiddlewareThrows403(
            fn () => app(CheckLmsAccess::class)->handle($lmsRequest, fn () => response('leaked'))
        );
    }

    public function test_lms_disabled_berdasarkan_jenjang_menghentikan_request_sebelum_konten(): void
    {
        $user = $this->makeUser('siswa');
        $cabang = Cabang::firstOrFail();
        $tahunAjaran = TahunAjaran::where('is_active', true)->firstOrFail();
        $suffix = substr(md5(uniqid('', true)), 0, 8);

        $kelas = Kelas::create([
            'cabang_id' => $cabang->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'nama_kelas' => 'SIT LMS '.$suffix,
            'jenjang' => 'SMP',
            'kode_kelas' => 'SL'.$suffix,
            'kuota_siswa' => 30,
        ]);
        $this->makeSiswa($user, $cabang, $kelas, $suffix);

        AppSetting::updateOrCreate(
            ['key' => 'lms_allowed_jenjang'],
            ['value' => json_encode(['SD'])]
        );

        $this->actingAs($user);
        $nextCalled = false;
        $response = app(CheckLmsAccess::class)->handle(
            Request::create('/siswa/lms', 'GET'),
            function () use (&$nextCalled) {
                $nextCalled = true;
                return response('leaked');
            }
        );

        $this->assertFalse($nextCalled);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringNotContainsString('leaked', $response->getContent());
    }

    public function test_direct_url_pengaturan_sensitif_ditolak_untuk_role_tanpa_kewenangan(): void
    {
        $user = $this->makeUser('siswa');

        foreach ([
            '/admin/recovery-tickets',
            '/admin/landing-pages',
            '/admin/keuangan/config',
            '/admin/akademik/kenaikan-kelas/kkm',
        ] as $url) {
            $this->actingAs($user)->get($url)->assertForbidden();
            $this->assertAuthenticatedAs($user);
        }
    }

    public function test_preview_file_menolak_path_traversal_dan_extension_di_luar_allowlist(): void
    {
        $user = $this->makeUser('siswa');
        $this->actingAs($user);

        $traversalToken = 'sit-traversal-'.bin2hex(random_bytes(6));
        Cache::put('docview_'.$traversalToken, [
            'path' => '../.env',
            'user_id' => $user->id,
        ], now()->addMinutes(5));

        $forbiddenTypeToken = 'sit-type-'.bin2hex(random_bytes(6));
        $forbiddenPath = 'sit-closure/payload.svg';
        Storage::disk('public')->put($forbiddenPath, '<svg onload="alert(1)"></svg>');
        Cache::put('docview_'.$forbiddenTypeToken, [
            'path' => $forbiddenPath,
            'user_id' => $user->id,
        ], now()->addMinutes(5));

        $expiredToken = 'sit-expired-'.bin2hex(random_bytes(6));
        Cache::put('docview_'.$expiredToken, [
            'path' => 'sit-closure/expired.pdf',
            'user_id' => $user->id,
        ], now()->subSecond());

        try {
            $this->get('/view-document/'.$traversalToken)->assertForbidden();
            $this->get('/view-document/'.$forbiddenTypeToken)->assertForbidden();
            $this->get('/view-document/'.$expiredToken)->assertNotFound();
        } finally {
            Cache::forget('docview_'.$traversalToken);
            Cache::forget('docview_'.$forbiddenTypeToken);
            Cache::forget('docview_'.$expiredToken);
            Storage::disk('public')->delete($forbiddenPath);
        }
    }

    public function test_rate_limit_login_menolak_attempt_keenam_dengan_decay_300_detik(): void
    {
        $request = LoginRequest::create('/login', 'POST', [
            'login' => 'sit-rate-limit@example.test',
            'password' => 'wrong-password',
        ], [], [], ['REMOTE_ADDR' => '198.51.100.25']);
        $key = $request->throttleKey();
        RateLimiter::clear($key);

        try {
            for ($attempt = 0; $attempt < 5; $attempt++) {
                RateLimiter::hit($key, 300);
            }

            $remaining = RateLimiter::availableIn($key);
            $this->assertGreaterThanOrEqual(295, $remaining);
            $this->assertLessThanOrEqual(300, $remaining);

            $this->expectException(ValidationException::class);
            $request->ensureIsNotRateLimited();
        } finally {
            RateLimiter::clear($key);
        }
    }

    private function makeUser(string $role, bool $active = true): User
    {
        $suffix = substr(md5(uniqid('', true)), 0, 10);

        return User::create([
            'name' => 'SIT Access '.$suffix,
            'email' => 'sit.access.'.$suffix.'@test.local',
            'username' => 'sitaccess'.$suffix,
            'password' => bcrypt('password'),
            'role' => $role,
            'is_active' => $active,
        ]);
    }

    private function makeSiswa(User $user, Cabang $cabang, Kelas $kelas, string $suffix): Siswa
    {
        return Siswa::create([
            'user_id' => $user->id,
            'cabang_id' => $cabang->id,
            'kelas_id' => $kelas->id,
            'nisn' => '91'.substr(preg_replace('/\D/', '', crc32($suffix)), 0, 8),
            'nis' => 'SIT'.$suffix,
            'nama_lengkap' => 'Siswa SIT '.$suffix,
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2010-01-01',
            'alamat' => 'Alamat SIT',
            'agama' => 'Islam',
            'tanggal_masuk' => now()->toDateString(),
            'status' => 'aktif',
        ]);
    }

    private function requestWithRouteParameter(string $name, mixed $value): Request
    {
        $request = Request::create('/dummy', 'GET');
        $route = new Route(['GET'], '/dummy/{'.$name.'}', fn () => null);
        $route->bind($request);
        $route->setParameter($name, $value);
        $request->setRouteResolver(fn () => $route);

        return $request;
    }

    private function assertMiddlewareThrows403(callable $callback): void
    {
        try {
            $callback();
            $this->fail('Middleware harus menolak request dengan HTTP 403.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
    }
}
