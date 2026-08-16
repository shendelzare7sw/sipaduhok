<?php

namespace Tests\Unit;

use App\Models\TahunAjaran;
use Carbon\Carbon;
use Tests\TestCase;

class TahunAjaranTagihanDateTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_default_jatuh_tempo_mengikuti_tanggal_sekarang_dalam_tahun_ajaran(): void
    {
        Carbon::setTestNow('2026-08-16');

        $this->assertSame('2026-09-16', $this->tahunAjaran()->getDefaultTagihanDueDate()->toDateString());
    }

    public function test_default_jatuh_tempo_tidak_melewati_akhir_tahun_ajaran(): void
    {
        Carbon::setTestNow('2027-06-15');

        $this->assertSame('2027-06-20', $this->tahunAjaran()->getDefaultTagihanDueDate()->toDateString());
    }

    public function test_tanggal_dari_tahun_lama_dipetakan_ke_tahun_ajaran_target(): void
    {
        Carbon::setTestNow('2026-08-16');

        $this->assertSame(
            '2026-09-16',
            $this->tahunAjaran()->normalizeTagihanDueDate('2025-09-16')->toDateString()
        );
    }

    public function test_bulan_spp_dibangun_dari_rentang_tahun_ajaran(): void
    {
        $months = $this->tahunAjaran()->getTagihanMonths();

        $this->assertCount(12, $months);
        $this->assertSame('2026-07', $months[0]['value']);
        $this->assertSame('2027-06', $months[11]['value']);
    }

    public function test_jatuh_tempo_spp_dibatasi_oleh_awal_dan_akhir_tahun_ajaran(): void
    {
        $tahunAjaran = $this->tahunAjaran();

        $this->assertSame('2026-07-14', $tahunAjaran->getTagihanDueDateForMonth('2026-07', 10)->toDateString());
        $this->assertSame('2027-02-28', $tahunAjaran->getTagihanDueDateForMonth('2027-02', 31)->toDateString());
        $this->assertSame('2027-06-20', $tahunAjaran->getTagihanDueDateForMonth('2027-06', 30)->toDateString());
    }

    private function tahunAjaran(): TahunAjaran
    {
        return new TahunAjaran([
            'nama_tahun_ajaran' => '2026/2027',
            'tanggal_mulai' => '2026-07-14',
            'tanggal_selesai' => '2027-06-20',
        ]);
    }
}
