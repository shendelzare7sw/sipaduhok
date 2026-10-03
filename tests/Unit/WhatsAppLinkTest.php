<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Tautan direct WhatsApp wajib memakai format internasional 62xxxx.
 * Dulu "08xxxx" dikirim apa adanya ke wa.me sehingga WhatsApp membaca kode negara keliru.
 */
class WhatsAppLinkTest extends TestCase
{
    public function test_nomor_lokal_dinormalisasi_ke_62(): void
    {
        $this->assertSame('6281234567890', wa_nomor('081234567890'));
        $this->assertSame('6281234567890', wa_nomor('0812-3456-7890'));
        $this->assertSame('6281234567890', wa_nomor('0812 3456 7890'));
        $this->assertSame('6281234567890', wa_nomor('+62 812-3456-7890'));
        $this->assertSame('6281234567890', wa_nomor('6281234567890'));
        $this->assertSame('6281234567890', wa_nomor('81234567890'));
    }

    public function test_nomor_kosong_atau_tidak_layak_menghasilkan_null(): void
    {
        $this->assertNull(wa_nomor(null));
        $this->assertNull(wa_nomor(''));
        $this->assertNull(wa_nomor('-'));
        $this->assertNull(wa_nomor('12345'));
        $this->assertNull(wa_link('bukan nomor'));
    }

    public function test_wa_link_membangun_url_dan_pesan(): void
    {
        $this->assertSame('https://wa.me/6281234567890', wa_link('081234567890'));
        $this->assertSame('https://wa.me/6281234567890?text=Halo%20Bu%20Guru', wa_link('081234567890', 'Halo Bu Guru'));
    }
}
