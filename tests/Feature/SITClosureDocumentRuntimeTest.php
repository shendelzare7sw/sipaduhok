<?php

namespace Tests\Feature;

use App\Exports\Templates\SiswaTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class SITClosureDocumentRuntimeTest extends TestCase
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
    }

    public function test_laravel_excel_membentuk_xlsx_yang_dapat_dibaca_kembali(): void
    {
        $binary = Excel::raw(new SiswaTemplate, ExcelFormat::XLSX);
        $this->assertStringStartsWith('PK', $binary);

        $path = tempnam(sys_get_temp_dir(), 'sit-xlsx-');
        file_put_contents($path, $binary);

        try {
            $spreadsheet = IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $this->assertSame('nis', $sheet->getCell('A1')->getValue());
            $this->assertSame('agama', $sheet->getCell('Q1')->getValue());
            $this->assertSame('PETUNJUK:', $sheet->getCell('A5')->getValue());
        } finally {
            @unlink($path);
        }
    }

    public function test_dompdf_membentuk_dokumen_pdf_valid_dari_view_runtime(): void
    {
        $binary = Pdf::loadHTML('<html><body><h1>SIPADUHOK</h1><p>SIT closure PDF runtime</p></body></html>')
            ->setPaper('a4')
            ->output();

        $this->assertStringStartsWith('%PDF-', $binary);
        $this->assertGreaterThan(500, strlen($binary));
    }

    public function test_pdftotext_runtime_mengekstrak_pdf_digital(): void
    {
        $marker = 'SIPADUHOK SIT CLOSURE PDF TEXT';
        $binary = Pdf::loadHTML('<html><body><p>'.$marker.'</p></body></html>')->output();
        $path = tempnam(sys_get_temp_dir(), 'sit-pdf-');
        file_put_contents($path, $binary);

        try {
            $text = \Spatie\PdfToText\Pdf::getText($path, 'C:\\laragon\\bin\\git\\mingw64\\bin\\pdftotext.exe');
            $this->assertStringContainsString($marker, preg_replace('/\s+/', ' ', $text));
        } finally {
            @unlink($path);
        }
    }
}
