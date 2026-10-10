<?php

namespace Tests\Unit;

use App\Models\Pembayaran;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PembayaranChannelLabelTest extends TestCase
{
    #[DataProvider('channels')]
    public function test_label_kanal_midtrans_mengikuti_metode_final(string $code, string $label): void
    {
        $payment = new Pembayaran([
            'metode_pembayaran' => 'midtrans',
            'payment_gateway' => 'midtrans',
            'payment_type' => $code,
        ]);

        $this->assertSame($label, $payment->payment_channel_label);
    }

    public static function channels(): array
    {
        return [
            'qris' => ['QRIS', 'QRIS'],
            'gopay' => ['GOPAY', 'GoPay'],
            'shopeepay' => ['SHOPEEPAY', 'ShopeePay'],
            'bca va midtrans' => ['BCAVA', 'BCA Virtual Account'],
            'mandiri bill midtrans' => ['MANDIRIVA', 'Mandiri Virtual Account'],
            'permata va midtrans' => ['PERMATAVA', 'Permata Virtual Account'],
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

    #[DataProvider('paymentStatuses')]
    public function test_label_status_midtrans_mengikuti_status_gateway(
        string $validationStatus,
        string $gatewayStatus,
        string $label,
    ): void {
        $payment = new Pembayaran([
            'metode_pembayaran' => 'midtrans',
            'payment_gateway' => 'midtrans',
            'status_validasi' => $validationStatus,
            'gateway_status' => $gatewayStatus,
        ]);

        $this->assertSame($label, $payment->payment_status_label);
    }

    public static function paymentStatuses(): array
    {
        return [
            'pending' => ['pending', 'pending', 'Menunggu Pembayaran'],
            'kartu ditinjau' => ['pending', 'capture', 'Menunggu Verifikasi'],
            'settlement' => ['disetujui', 'settlement', 'Lunas'],
            'capture' => ['disetujui', 'capture', 'Lunas'],
            'cancel' => ['ditolak', 'cancel', 'Dibatalkan'],
            'expire' => ['ditolak', 'expire', 'Kedaluwarsa'],
            'deny' => ['ditolak', 'deny', 'Gagal'],
            'failure' => ['ditolak', 'failure', 'Gagal'],
        ];
    }

    public function test_riwayat_paywuz_lama_tetap_terbaca_sebagai_pembayaran_digital(): void
    {
        $payment = new Pembayaran([
            'metode_pembayaran' => 'paywuz',
            'payment_gateway' => 'paywuz',
            'payment_type' => 'QRIS',
            'status_validasi' => 'disetujui',
            'gateway_status' => 'success',
        ]);

        $this->assertTrue($payment->is_digital);
        $this->assertSame('QRIS', $payment->payment_channel_label);
        $this->assertSame('Lunas', $payment->payment_status_label);
        $this->assertSame(['midtrans', 'paywuz'], Pembayaran::metodeFilterValues('midtrans'));
        $this->assertSame(['tunai'], Pembayaran::metodeFilterValues('tunai'));
    }
}
