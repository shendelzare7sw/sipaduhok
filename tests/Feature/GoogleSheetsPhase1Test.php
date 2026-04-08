<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\GoogleSheetsService;
use App\Models\GoogleSheetsSyncLog;

class GoogleSheetsPhase1Test extends TestCase
{
    public function test_google_sheets_service_initialization()
    {
        $gs = new GoogleSheetsService();
        $this->assertIsString($gs->getSpreadsheetId());
        $this->assertTrue(true); // Service initialized without errors
    }

    public function test_google_sheets_config()
    {
        $modules = config('google-sheets.modules');
        $this->assertIsArray($modules);
        $this->assertCount(10, $modules);
        
        $this->assertArrayHasKey('siswa', $modules);
        $this->assertArrayHasKey('guru', $modules);
        $this->assertArrayHasKey('kelas', $modules);
        $this->assertArrayHasKey('jadwal_pelajaran', $modules);
        $this->assertArrayHasKey('presensi', $modules);
        $this->assertArrayHasKey('nilai', $modules);
        $this->assertArrayHasKey('tagihan', $modules);
        $this->assertArrayHasKey('pembayaran', $modules);
        $this->assertArrayHasKey('siswa_belum_lunas', $modules);
        $this->assertArrayHasKey('rekap_keuangan', $modules);
    }

    public function test_google_sheets_sync_log_model()
    {
        $this->assertTrue(class_exists(GoogleSheetsSyncLog::class));
        $count = GoogleSheetsSyncLog::count();
        $this->assertIsInt($count);
        $this->assertEquals(0, $count);
    }

    public function test_google_sheets_database_table()
    {
        $columns = \DB::getSchemaBuilder()->getColumnListing('google_sheets_sync_logs');
        $expectedColumns = [
            'id', 'module', 'direction', 'spreadsheet_id', 'sheet_name',
            'rows_synced', 'status', 'error_message', 'synced_by', 'synced_at',
            'created_at', 'updated_at'
        ];
        
        foreach ($expectedColumns as $column) {
            $this->assertContains($column, $columns);
        }
    }

    public function test_google_sheets_service_methods()
    {
        $gs = new GoogleSheetsService();
        
        $methods = [
            'testConnection',
            'getSpreadsheetMetadata',
            'getSheetNames',
            'getSheetData',
            'getRange',
            'updateRange',
            'clearSheet',
            'appendRows',
            'formatHeader',
            'shareSpreadsheet',
            'getSpreadsheetUrl',
            'setSpreadsheetId',
            'getSpreadsheetId',
        ];

        foreach ($methods as $method) {
            $this->assertTrue(method_exists($gs, $method), "Method {$method} not found");
        }
    }
}
