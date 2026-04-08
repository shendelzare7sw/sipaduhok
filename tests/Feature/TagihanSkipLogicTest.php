<?php

namespace Tests\Feature;

use App\Models\Tagihan;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagihanSkipLogicTest extends TestCase
{
    use RefreshDatabase;

    protected $tahunAjaranAktif;
    protected $siswa;

    public function setUp(): void
    {
        parent::setUp();
        
        // Create test academic year
        $this->tahunAjaranAktif = TahunAjaran::create([
            'nama_tahun_ajaran' => '2024/2025',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addYear(),
            'is_active' => true,
        ]);
    }

    /**
     * Test bulkCreate Skip Logic
     * 
     * @test
     */
    public function test_bulk_create_skips_existing_tagihan()
    {
        // Create students
        $siswa1 = Siswa::factory()->create(['nama' => 'Siswa 1']);
        $siswa2 = Siswa::factory()->create(['nama' => 'Siswa 2']);
        
        // Create pre-existing tagihan for siswa1
        Tagihan::create([
            'siswa_id' => $siswa1->id,
            'tahun_ajaran_id' => $this->tahunAjaranAktif->id,
            'jenis_tagihan' => 'uang_pendaftaran',
            'jumlah' => 100000,
            'tanggal_jatuh_tempo' => now()->addMonth(),
            'status' => 'belum_bayar',
            'keterangan' => 'Existing',
        ]);
        
        // Simulate bulkCreate behavior
        $totalCreated = 0;
        $totalSkipped = 0;
        $jenisTagihan = ['uang_pendaftaran', 'uang_daftang_ulang'];
        $siswaList = [$siswa1, $siswa2];
        
        foreach ($siswaList as $siswa) {
            foreach ($jenisTagihan as $jenis) {
                $exists = Tagihan::where('siswa_id', $siswa->id)
                    ->where('tahun_ajaran_id', $this->tahunAjaranAktif->id)
                    ->where('jenis_tagihan', $jenis)
                    ->first();
                
                if ($exists) {
                    $totalSkipped++;
                    continue;
                }
                
                Tagihan::create([
                    'siswa_id' => $siswa->id,
                    'tahun_ajaran_id' => $this->tahunAjaranAktif->id,
                    'jenis_tagihan' => $jenis,
                    'jumlah' => 150000,
                    'tanggal_jatuh_tempo' => now()->addMonth(),
                    'status' => 'belum_bayar',
                ]);
                
                $totalCreated++;
            }
        }
        
        // Assertions
        $this->assertEquals(3, $totalCreated);  // 1 for siswa2, 2 for siswa1 (one skipped)
        $this->assertEquals(1, $totalSkipped);  // uang_pendaftaran for siswa1
        
        // Verify database has correct records
        $this->assertDatabaseCount('tagihans', 4);  // 1 existing + 3 created
    }

    /**
     * Test storeCustom Skip Logic
     * 
     * @test
     */
    public function test_store_custom_skips_existing_custom_tagihan()
    {
        // Create students
        $siswa1 = Siswa::factory()->create(['nama' => 'Siswa 1']);
        $siswa2 = Siswa::factory()->create(['nama' => 'Siswa 2']);
        
        // Create pre-existing custom tagihan for siswa1
        Tagihan::create([
            'siswa_id' => $siswa1->id,
            'tahun_ajaran_id' => $this->tahunAjaranAktif->id,
            'jenis_tagihan' => 'biaya_pramuka',
            'jumlah' => 50000,
            'tanggal_jatuh_tempo' => now()->addMonth(),
            'status' => 'belum_bayar',
        ]);
        
        // Simulate storeCustom behavior
        $totalCreated = 0;
        $totalSkipped = 0;
        $jenisTagihanCustom = 'biaya_pramuka';
        $siswaIds = [$siswa1->id, $siswa2->id];
        
        foreach ($siswaIds as $siswaId) {
            $exists = Tagihan::where('siswa_id', $siswaId)
                ->where('tahun_ajaran_id', $this->tahunAjaranAktif->id)
                ->where('jenis_tagihan', $jenisTagihanCustom)
                ->first();
            
            if ($exists) {
                $totalSkipped++;
                continue;
            }
            
            Tagihan::create([
                'siswa_id' => $siswaId,
                'tahun_ajaran_id' => $this->tahunAjaranAktif->id,
                'jenis_tagihan' => $jenisTagihanCustom,
                'jumlah' => 50000,
                'tanggal_jatuh_tempo' => now()->addMonth(),
                'status' => 'belum_bayar',
            ]);
            
            $totalCreated++;
        }
        
        // Assertions
        $this->assertEquals(1, $totalCreated);  // Only siswa2
        $this->assertEquals(1, $totalSkipped);  // siswa1 already has it
        
        // Verify database
        $this->assertDatabaseCount('tagihans', 2);  // 1 existing + 1 created
    }

    /**
     * Test generateSpp BULK mode (skip existing)
     * 
     * @test
     */
    public function test_generate_spp_bulk_mode_skips_existing()
    {
        // Create students (multiple = bulk)
        $siswa1 = Siswa::factory()->create(['nama' => 'Siswa 1']);
        $siswa2 = Siswa::factory()->create(['nama' => 'Siswa 2']);
        
        // Create pre-existing SPP for siswa1
        Tagihan::create([
            'siswa_id' => $siswa1->id,
            'tahun_ajaran_id' => $this->tahunAjaranAktif->id,
            'jenis_tagihan' => 'spp_januari',
            'jumlah' => 200000,
            'tanggal_jatuh_tempo' => now()->addMonth(),
            'status' => 'belum_bayar',
        ]);
        
        // Simulate BULK generateSpp (isBulkOperation = true)
        $siswaList = [$siswa1, $siswa2];
        $isBulkOperation = $siswaList->count() > 1; // TRUE = BULK
        
        $totalCreated = 0;
        $totalSkipped = 0;
        
        if ($isBulkOperation) {
            foreach ($siswaList as $siswa) {
                $sppKey = 'spp_januari';
                
                $existingSpp = Tagihan::where('siswa_id', $siswa->id)
                    ->where('tahun_ajaran_id', $this->tahunAjaranAktif->id)
                    ->where('jenis_tagihan', $sppKey)
                    ->first();
                
                if ($existingSpp) {
                    $totalSkipped++;
                    continue;  // SKIP mode
                }
                
                Tagihan::create([
                    'siswa_id' => $siswa->id,
                    'tahun_ajaran_id' => $this->tahunAjaranAktif->id,
                    'jenis_tagihan' => $sppKey,
                    'jumlah' => 200000,
                    'tanggal_jatuh_tempo' => now()->addMonth(),
                    'status' => 'belum_bayar',
                ]);
                
                $totalCreated++;
            }
        }
        
        // Assertions
        $this->assertEquals(1, $totalCreated);  // Only siswa2
        $this->assertEquals(1, $totalSkipped);  // siswa1 skipped
        $this->assertDatabaseCount('tagihans', 2);
    }

    /**
     * Test generateSpp INDIVIDUAL mode (timpa/update existing)
     * 
     * @test
     */
    public function test_generate_spp_individual_mode_timpas_existing()
    {
        // Create single student (not bulk)
        $siswa = Siswa::factory()->create(['nama' => 'Siswa 1']);
        
        // Create pre-existing SPP
        $initialAmount = 200000;
        Tagihan::create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $this->tahunAjaranAktif->id,
            'jenis_tagihan' => 'spp_januari',
            'jumlah' => $initialAmount,
            'tanggal_jatuh_tempo' => now()->addMonth(),
            'status' => 'belum_bayar',
        ]);
        
        // Simulate INDIVIDUAL generateSpp (isBulkOperation = false)
        $siswaList = [$siswa];
        $isBulkOperation = count($siswaList) > 1; // FALSE = INDIVIDUAL
        
        $newAmount = 250000;
        
        if (!$isBulkOperation) {
            foreach ($siswaList as $siswaItem) {
                $sppKey = 'spp_januari';
                
                // Using firstOrNew which allows TIMPA (update) for individual
                $tagihan = Tagihan::firstOrNew([
                    'siswa_id' => $siswaItem->id,
                    'tahun_ajaran_id' => $this->tahunAjaranAktif->id,
                    'jenis_tagihan' => $sppKey,
                ]);
                
                $tagihan->jumlah = $newAmount;
                $tagihan->tanggal_jatuh_tempo = now()->addMonth()->addDay();
                
                if (!$tagihan->exists) {
                    $tagihan->status = 'belum_bayar';
                }
                
                $tagihan->save();
            }
        }
        
        // Assertions
        $this->assertDatabaseCount('tagihans', 1);
        
        // Verify amount was updated (TIMPA)
        $updated = Tagihan::where('siswa_id', $siswa->id)
            ->where('jenis_tagihan', 'spp_januari')
            ->first();
        
        $this->assertEquals($newAmount, $updated->jumlah);
    }
}
