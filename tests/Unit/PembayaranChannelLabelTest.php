<?php

namespace Tests\Unit;

use App\Models\Pembayaran;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PembayaranChannelLabelTest extends TestCase
{
    #[DataProvider('channels')]
    public function test_label_kanal_paywuz_mengikuti_metode_final(string $code, string $label): void
    {
        $payment = new Pembayaran([
            'metode_pembayaran' => 'paywuz',
            'payment_gateway' => 'paywuz',
            'payment_type' => $code,
        ]);

        $this->assertSame($label, $payment->payment_channel_label);
    }

    public static function channels(): array
    {
        return [
            'qris' => ['QRIS', 'QRIS'],
            'va belum dipilih' => ['VA', 'Virtual Account (Pilih Bank)'],
            'bca va hasil resolve' => ['014', 'BCA Virtual Account'],
            'bni va hasil resolve' => ['009', 'BNI Virtual Account'],
            'bri va hasil resolve' => ['002', 'BRI Virtual Account'],
            'bsi va hasil resolve' => ['451', 'BSI Virtual Account'],
            'cimb va hasil resolve' => ['022', 'CIMB Niaga Virtual Account'],
            'danamon va hasil resolve' => ['011', 'Danamon Virtual Account'],
            'mandiri va hasil resolve' => ['008', 'Mandiri Virtual Account'],
            'maybank va hasil resolve' => ['016', 'Maybank Virtual Account'],
            'ocbc va hasil resolve' => ['028', 'OCBC Virtual Account'],
            'permata va hasil resolve' => ['013', 'Permata Virtual Account'],
            'alfamart' => ['ALFAMART', 'Alfamart'],
            'indomaret' => ['INDOMARET', 'Indomaret'],
        ];
    }
}
