<?php

namespace Tests\Feature;

use App\Models\InfoPembayaran;
use App\Services\PaywuzService;
use ReflectionClass;
use Tests\TestCase;

class PaywuzDisabledServerSideTest extends TestCase
{
    public function test_api_key_lengkap_tetapi_kanal_disabled_ditolak_server_side(): void
    {
        $infoPembayaran = new class extends InfoPembayaran
        {
            public function isPaywuzEnabled(): bool
            {
                return false;
            }
        };

        $reflection = new ReflectionClass(PaywuzService::class);
        /** @var PaywuzService $service */
        $service = $reflection->newInstanceWithoutConstructor();
        $property = $reflection->getProperty('infoPembayaran');
        $property->setValue($service, $infoPembayaran);

        $this->assertFalse(
            $service->isConfigured(),
            'Kanal yang dinonaktifkan tidak boleh dianggap siap oleh guard server-side.',
        );
    }
}
