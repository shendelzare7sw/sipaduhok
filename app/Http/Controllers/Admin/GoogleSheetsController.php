<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\GoogleSheetsService;
use App\Models\GoogleSheetsSyncLog;
use Exception;

class GoogleSheetsController extends Controller
{
    protected $googleSheetsService;

    public function __construct()
    {
        $this->googleSheetsService = new GoogleSheetsService();
    }

    /**
     * Display Google Sheets dashboard
     */
    public function index()
    {
        try {
            // Get last sync logs for each module
            $modules = config('google-sheets.modules');
            $lastSyncs = [];

            foreach (array_keys($modules) as $module) {
                $lastSyncs[$module] = [
                    'push' => GoogleSheetsSyncLog::where('module', $module)
                        ->where('direction', 'push')
                        ->latest('synced_at')
                        ->first(),
                    'pull' => GoogleSheetsSyncLog::where('module', $module)
                        ->where('direction', 'pull')
                        ->latest('synced_at')
                        ->first(),
                ];
            }

            // Get recent sync history
            $syncHistory = GoogleSheetsSyncLog::latest('synced_at')
                ->limit(20)
                ->get();

            $isEnabled = config('google-sheets.enabled');
            $spreadsheetId = config('google-sheets.default_spreadsheet_id');
            $credentialsPath = config('google-sheets.credentials_path');
            $credentialsExists = file_exists($credentialsPath);

            return view('admin.google-sheets.index', compact(
                'isEnabled',
                'spreadsheetId',
                'credentialsExists',
                'modules',
                'lastSyncs',
                'syncHistory'
            ));
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Show setup wizard
     */
    public function setup()
    {
        try {
            $sheetNames = [];
            $isEnabled = config('google-sheets.enabled');
            $credentialsPath = config('google-sheets.credentials_path');
            $credentialsExists = file_exists($credentialsPath);
            $currentSpreadsheetId = config('google-sheets.default_spreadsheet_id');

            if ($credentialsExists && $isEnabled) {
                try {
                    $sheetNames = $this->googleSheetsService->getSheetNames();
                } catch (Exception $e) {
                    \Log::warning('Failed to get sheet names: ' . $e->getMessage());
                }
            }

            return view('admin.google-sheets.setup', compact(
                'isEnabled',
                'credentialsExists',
                'sheetNames',
                'currentSpreadsheetId'
            ));
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Save credential and test connection
     */
    public function saveCredential(Request $request)
    {
        try {
            $request->validate([
                'spreadsheet_id' => 'required|string',
                'json_file' => 'required|file|mimes:json',
            ]);

            $credentialsPath = storage_path('app/credentials/google-service-account.json');
            
            // Ensure directory exists
            if (!is_dir(dirname($credentialsPath))) {
                mkdir(dirname($credentialsPath), 0755, true);
            }

            // Save JSON file
            if ($request->hasFile('json_file')) {
                $file = $request->file('json_file');
                $file->move(dirname($credentialsPath), basename($credentialsPath));
            }

            // Update .env
            $this->updateEnv('GOOGLE_SHEETS_DEFAULT_SPREADSHEET_ID', $request->spreadsheet_id);
            $this->updateEnv('GOOGLE_SHEETS_ENABLED', 'true');

            // Clear config cache to apply new settings
            if (function_exists('artisan')) {
                \Artisan::call('config:clear');
            }

            return back()->with('success', 'Credentials saved successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Test connection to Google Sheets
     */
    public function testConnection(Request $request)
    {
        try {
            // If JSON file is provided, validate it first
            if ($request->hasFile('json_file')) {
                try {
                    $jsonContent = file_get_contents($request->file('json_file')->getRealPath());
                    $data = json_decode($jsonContent, true);
                    
                    if (!$data || !isset($data['type']) || $data['type'] !== 'service_account') {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid Google Service Account JSON file.',
                        ], 400);
                    }
                } catch (Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to parse JSON: ' . $e->getMessage(),
                    ], 400);
                }
            }

            // Test with current/provided spreadsheet ID
            $spreadsheetId = $request->input('spreadsheet_id', config('google-sheets.default_spreadsheet_id'));
            
            if (!$spreadsheetId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Spreadsheet ID is required.',
                ], 400);
            }

            try {
                // Test the connection
                $connected = $this->googleSheetsService->testConnection();

                if ($connected) {
                    try {
                        $metadata = $this->googleSheetsService->getSpreadsheetMetadata();
                        return response()->json([
                            'success' => true,
                            'message' => 'Connection successful! Access to spreadsheet verified.',
                            'spreadsheet_name' => $metadata->getProperties()->getTitle(),
                            'sheet_count' => count($metadata->getSheets()),
                        ]);
                    } catch (Exception $e) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Connection test failed: ' . $e->getMessage(),
                            'error' => $e->getMessage(),
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Connection failed. Please check your credentials and Spreadsheet ID.',
                    ], 400);
                }
            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Connection test error: ' . $e->getMessage(),
                    'error' => $e->getMessage(),
                ], 400);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Push module data to Google Sheets
     */
    public function push($module)
    {
        try {
            $modules = config('google-sheets.modules');

            if (!isset($modules[$module])) {
                return response()->json([
                    'success' => false,
                    'message' => "Module '{$module}' not found.",
                ], 404);
            }

            // Dispatch sync job
            \App\Jobs\SyncModuleToSheet::dispatch($module, 'push')
                ->onQueue('default');

            // Log the action
            GoogleSheetsSyncLog::create([
                'module' => $module,
                'direction' => 'push',
                'spreadsheet_id' => config('google-sheets.default_spreadsheet_id'),
                'sheet_name' => $modules[$module]['sheet_name'],
                'rows_synced' => 0,
                'status' => 'success',
                'synced_by' => auth()->id(),
                'synced_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Sync job queued for module '{$module}'",
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Pull module data from Google Sheets (with preview)
     */
    public function pullPreview($module)
    {
        try {
            $modules = config('google-sheets.modules');

            if (!isset($modules[$module])) {
                return response()->json([
                    'success' => false,
                    'message' => "Module '{$module}' not found.",
                ], 404);
            }

            $sheetName = $modules[$module]['sheet_name'];
            $data = $this->googleSheetsService->getSheetData($sheetName);

            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => "No data found in sheet '{$sheetName}'",
                ], 400);
            }

            // Extract headers and rows
            $headers = array_shift($data);
            $rows = array_slice($data, 0, 50); // Preview first 50 rows

            return response()->json([
                'success' => true,
                'module' => $module,
                'sheet_name' => $sheetName,
                'headers' => $headers,
                'rows' => $rows,
                'total_rows' => count($data),
                'preview_rows' => count($rows),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Confirm pull data to database
     */
    public function pull($module)
    {
        try {
            $modules = config('google-sheets.modules');

            if (!isset($modules[$module])) {
                return response()->json([
                    'success' => false,
                    'message' => "Module '{$module}' not found.",
                ], 404);
            }

            // Dispatch sync job
            \App\Jobs\SyncModuleToSheet::dispatch($module, 'pull')
                ->onQueue('default');

            return response()->json([
                'success' => true,
                'message' => "Pull job queued for module '{$module}'",
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get sync status
     */
    public function status($module = null)
    {
        try {
            if ($module) {
                $logs = GoogleSheetsSyncLog::where('module', $module)
                    ->latest('synced_at')
                    ->limit(10)
                    ->get();
            } else {
                $logs = GoogleSheetsSyncLog::latest('synced_at')
                    ->limit(50)
                    ->get();
            }

            return response()->json([
                'success' => true,
                'data' => $logs->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'module' => $log->module,
                        'direction' => $log->getDirectionLabel(),
                        'sheet_name' => $log->sheet_name,
                        'rows_synced' => $log->rows_synced,
                        'status' => $log->getStatusLabel(),
                        'error' => $log->error_message,
                        'synced_by' => $log->user->name ?? 'System',
                        'synced_at' => $log->synced_at->format('Y-m-d H:i:s'),
                    ];
                }),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Disconnect/disable Google Sheets
     */
    public function disconnect()
    {
        try {
            $this->updateEnv('GOOGLE_SHEETS_ENABLED', 'false');

            return response()->json([
                'success' => true,
                'message' => 'Google Sheets integration disabled.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update .env file
     */
    private function updateEnv($key, $value)
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        if (preg_match("/^{$key}=/m", $envContent)) {
            $envContent = preg_replace(
                "/^{$key}=.*/m",
                "{$key}={$value}",
                $envContent
            );
        } else {
            $envContent .= "\n{$key}={$value}";
        }

        file_put_contents($envFile, $envContent);

        // Refresh config cache
        \Artisan::call('config:cache');
    }
}
