<?php

namespace Tests\Feature;

use App\Models\InfoPembayaran;
use App\Services\MidtransService;
use ReflectionClass;
use Tests\TestCase;

/**
 * Regression guard for REQ-KEU-008 / RULE-057 / SIT-RSK-010.
 *
 * This test deliberately avoids a gateway call and database write. It verifies
 * that the service-level gate rejects a complete configuration whose channel
 * has explicitly been disabled by the payment administrator.
 */
class MidtransDisabledServerSideTest extends TestCase
{
    public function test_konfigurasi_lengkap_tetapi_disabled_ditolak_server_side(): void
    {
        $infoPembayaran = new class extends InfoPembayaran
        {
            public function hasMidtrans()
            {
                return true;
            }

            public function isMidtransEnabled()
            {
                return false;
            }
        };

        $reflection = new ReflectionClass(MidtransService::class);
        /** @var MidtransService $service */
        $service = $reflection->newInstanceWithoutConstructor();

        $property = $reflection->getProperty('infoPembayaran');
        $property->setAccessible(true);
        $property->setValue($service, $infoPembayaran);

        $this->assertFalse(
            $service->isConfigured(),
            'Kanal Midtrans disabled tidak boleh dianggap siap oleh guard server-side.'
        );
    }
}
