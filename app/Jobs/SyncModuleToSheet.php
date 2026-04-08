<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\GoogleSheetsService;
use App\Models\GoogleSheetsSyncLog;
use Exception;

class SyncModuleToSheet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $module;
    protected $direction;
    protected $userId;

    public $tries = 3;
    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct($module, $direction = 'push', $userId = null)
    {
        $this->module = $module;
        $this->direction = $direction;
        $this->userId = $userId ?? auth()->id();
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $service = new GoogleSheetsService();
            $modules = config('google-sheets.modules');

            if (!isset($modules[$this->module])) {
                throw new Exception("Module '{$this->module}' not found in configuration.");
            }

            $moduleConfig = $modules[$this->module];
            $spreadsheetId = config('google-sheets.default_spreadsheet_id');
            $sheetName = $moduleConfig['sheet_name'];

            if ($this->direction === 'push') {
                $rowsSynced = $this->pushToGoogleSheets($service, $moduleConfig);
            } else {
                $rowsSynced = $this->pullFromGoogleSheets($service, $moduleConfig);
            }

            // Log success
            GoogleSheetsSyncLog::create([
                'module' => $this->module,
                'direction' => $this->direction,
                'spreadsheet_id' => $spreadsheetId,
                'sheet_name' => $sheetName,
                'rows_synced' => $rowsSynced,
                'status' => 'success',
                'error_message' => null,
                'synced_by' => $this->userId,
                'synced_at' => now(),
            ]);

            \Log::info("Sync {$this->direction} for module '{$this->module}' completed. Rows: {$rowsSynced}");

        } catch (Exception $e) {
            // Log failure
            GoogleSheetsSyncLog::create([
                'module' => $this->module,
                'direction' => $this->direction,
                'spreadsheet_id' => config('google-sheets.default_spreadsheet_id'),
                'sheet_name' => $modules[$this->module]['sheet_name'] ?? 'Unknown',
                'rows_synced' => 0,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'synced_by' => $this->userId,
                'synced_at' => now(),
            ]);

            \Log::error("Sync {$this->direction} for module '{$this->module}' failed: " . $e->getMessage());

            throw $e; // Re-throw to trigger queue retry
        }
    }

    /**
     * Push data to Google Sheets
     */
    private function pushToGoogleSheets($service, $moduleConfig)
    {
        $model = $moduleConfig['model'];
        $sheetName = $moduleConfig['sheet_name'];

        // Get all data from model
        if ($moduleConfig['is_derived'] ?? false) {
            // For derived data (siswa_belum_lunas, rekap_keuangan)
            $data = $this->getDerivedData($this->module);
        } else {
            $query = $model::query();

            // Apply filters if any
            if ($this->module === 'siswa' && config('google-sheets.per_cabang_enabled')) {
                $cabangId = auth()->user()->cabang_id ?? null;
                if ($cabangId) {
                    $query->where('cabang_id', $cabangId);
                }
            }

            $records = $query->get();
            $columns = $moduleConfig['columns'];
            $mappings = $moduleConfig['mappings'];

            // Format data as rows
            $data = [$columns]; // Header row

            foreach ($records as $record) {
                $row = [];
                foreach ($mappings as $sheetCol => $dbCol) {
                    $row[] = $record->{$dbCol} ?? '';
                }
                $data[] = $row;
            }
        }

        // Clear sheet and push data
        $service->clearSheet($sheetName);
        $service->appendRows($sheetName, $data);
        $service->formatHeader($sheetName, count($moduleConfig['columns']));

        return count($data) - 1; // -1 untuk header row
    }

    /**
     * Pull data from Google Sheets
     */
    private function pullFromGoogleSheets($service, $moduleConfig)
    {
        $sheetName = $moduleConfig['sheet_name'];
        $data = $service->getSheetData($sheetName);

        if (empty($data)) {
            return 0;
        }

        $headers = array_shift($data);
        $model = $moduleConfig['model'];
        $mappings = $moduleConfig['mappings'];
        $rowsProcessed = 0;

        foreach ($data as $row) {
            try {
                // Map sheet row to model attributes
                $attributes = [];
                foreach ($mappings as $sheetCol => $dbCol) {
                    $colIndex = array_search($sheetCol, $headers);
                    if ($colIndex !== false && isset($row[$colIndex])) {
                        $attributes[$dbCol] = $row[$colIndex];
                    }
                }

                // Find or create record
                if (isset($attributes['id'])) {
                    $model::updateOrCreate(['id' => $attributes['id']], $attributes);
                } else {
                    $model::create($attributes);
                }

                $rowsProcessed++;
            } catch (Exception $e) {
                \Log::warning("Error processing row in {$this->module}: " . $e->getMessage());
                continue;
            }
        }

        return $rowsProcessed;
    }

    /**
     * Get derived data (siswa_belum_lunas, rekap_keuangan)
     */
    private function getDerivedData($module)
    {
        $data = [];

        if ($module === 'siswa_belum_lunas') {
            // Get students with unpaid bills
            $students = \App\Models\Siswa::with('tagihan', 'pembayaran')
                ->get();

            $data[] = ['Siswa', 'Kelas', 'Total Tagihan', 'Sisa'];

            foreach ($students as $student) {
                $totalTagihan = $student->tagihan->sum('nominal');
                $totalPembayaran = $student->pembayaran->sum('jumlah');
                $sisa = $totalTagihan - $totalPembayaran;

                if ($sisa > 0) {
                    $data[] = [
                        $student->nama,
                        $student->kelas->nama ?? '',
                        $totalTagihan,
                        $sisa,
                    ];
                }
            }
        } elseif ($module === 'rekap_keuangan') {
            // Get financial summary by month
            $data[] = ['Bulan', 'Total', 'Tunai', 'Transfer', 'Midtrans'];

            $months = \App\Models\Pembayaran::selectRaw("DATE_FORMAT(tanggal_bayar, '%Y-%m') as bulan")
                ->distinct()
                ->orderByDesc('bulan')
                ->limit(12)
                ->pluck('bulan');

            foreach ($months as $bulan) {
                $pembayaran = \App\Models\Pembayaran::whereRaw("DATE_FORMAT(tanggal_bayar, '%Y-%m') = ?", [$bulan])
                    ->get();

                $total = $pembayaran->sum('jumlah');
                $tunai = $pembayaran->where('metode', 'tunai')->sum('jumlah');
                $transfer = $pembayaran->where('metode', 'transfer')->sum('jumlah');
                $midtrans = $pembayaran->where('metode', 'midtrans')->sum('jumlah');

                $data[] = [$bulan, $total, $tunai, $transfer, $midtrans];
            }
        }

        return $data;
    }
}
