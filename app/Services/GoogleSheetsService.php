<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Drive;
use Exception;

class GoogleSheetsService
{
    private $client;
    private $sheetsService;
    private $driveService;
    private $spreadsheetId;
    private $credentialsPath;

    public function __construct()
    {
        $this->credentialsPath = env('GOOGLE_SERVICE_ACCOUNT_JSON_PATH', storage_path('app/credentials/google-service-account.json'));
        $this->spreadsheetId = env('GOOGLE_SHEETS_DEFAULT_SPREADSHEET_ID', '');
        
        if (function_exists('config')) {
            $this->credentialsPath = config('google-sheets.credentials_path', $this->credentialsPath);
            $this->spreadsheetId = config('google-sheets.default_spreadsheet_id', $this->spreadsheetId);
        }
        
        $this->initializeClient();
    }

    /**
     * Initialize Google Client
     */
    private function initializeClient()
    {
        try {
            if (!file_exists($this->credentialsPath)) {
                \Log::warning("Google Service Account JSON not found at {$this->credentialsPath}");
                return; // Allow service to work without credentials (not yet setup)
            }

            $this->client = new Client();
            $this->client->setAuthConfig($this->credentialsPath);
            $this->client->addScope([
                Sheets::SPREADSHEETS,
                Drive::DRIVE,
            ]);
            $this->client->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));

            $this->sheetsService = new Sheets($this->client);
            $this->driveService = new Drive($this->client);

            \Log::info('Google Client initialized successfully');
        } catch (Exception $e) {
            \Log::error('Failed to initialize Google Client: ' . $e->getMessage());
            // Don't throw - allow service to be partially initialized
        }
    }

    /**
     * Test connection to Google Sheets API
     */
    public function testConnection(): bool
    {
        try {
            $spreadsheet = $this->sheetsService->spreadsheets->get($this->spreadsheetId);
            Log::channel(config('google-sheets.log_channel'))
                ->info('Google Sheets connection test successful');
            return true;
        } catch (Exception $e) {
            Log::channel(config('google-sheets.log_channel'))
                ->error('Google Sheets connection test failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get spreadsheet metadata
     */
    public function getSpreadsheetMetadata()
    {
        try {
            return $this->sheetsService->spreadsheets->get($this->spreadsheetId);
        } catch (Exception $e) {
            Log::channel(config('google-sheets.log_channel'))
                ->error('Failed to get spreadsheet metadata: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get all sheet names from spreadsheet
     */
    public function getSheetNames(): array
    {
        try {
            $spreadsheet = $this->getSpreadsheetMetadata();
            $sheetNames = [];

            foreach ($spreadsheet->getSheets() as $sheet) {
                $sheetNames[] = $sheet->getProperties()->getTitle();
            }

            return $sheetNames;
        } catch (Exception $e) {
            Log::channel(config('google-sheets.log_channel'))
                ->error('Failed to get sheet names: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get sheet data by name
     */
    public function getSheetData(string $sheetName): array
    {
        try {
            $range = "{$sheetName}!A1:ZZ";
            $response = $this->sheetsService->spreadsheets_values->get($this->spreadsheetId, $range);
            $values = $response->getValues();

            return $values ?? [];
        } catch (Exception $e) {
            Log::channel(config('google-sheets.log_channel'))
                ->error("Failed to get sheet data from '{$sheetName}': " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get specific range from sheet
     */
    public function getRange(string $range): array
    {
        try {
            $response = $this->sheetsService->spreadsheets_values->get($this->spreadsheetId, $range);
            return $response->getValues() ?? [];
        } catch (Exception $e) {
            Log::channel(config('google-sheets.log_channel'))
                ->error("Failed to get range '{$range}': " . $e->getMessage());
            return [];
        }
    }

    /**
     * Update sheet data
     */
    public function updateRange(string $range, array $values): bool
    {
        try {
            $body = new Sheets\ValueRange();
            $body->setValues($values);

            $this->sheetsService->spreadsheets_values->update(
                $this->spreadsheetId,
                $range,
                $body,
                ['valueInputOption' => 'RAW']
            );

            Log::channel(config('google-sheets.log_channel'))
                ->info("Successfully updated range: {$range}");
            return true;
        } catch (Exception $e) {
            Log::channel(config('google-sheets.log_channel'))
                ->error("Failed to update range '{$range}': " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear sheet content
     */
    public function clearSheet(string $sheetName): bool
    {
        try {
            $range = "{$sheetName}!A1:ZZ1000";
            $this->sheetsService->spreadsheets_values->clear(
                $this->spreadsheetId,
                $range,
                new Sheets\ClearValuesRequest()
            );

            Log::channel(config('google-sheets.log_channel'))
                ->info("Successfully cleared sheet: {$sheetName}");
            return true;
        } catch (Exception $e) {
            Log::channel(config('google-sheets.log_channel'))
                ->error("Failed to clear sheet '{$sheetName}': " . $e->getMessage());
            return false;
        }
    }

    /**
     * Append rows to sheet
     */
    public function appendRows(string $sheetName, array $rows): bool
    {
        try {
            $body = new Sheets\ValueRange();
            $body->setValues($rows);

            $this->sheetsService->spreadsheets_values->append(
                $this->spreadsheetId,
                $sheetName,
                $body,
                ['valueInputOption' => 'RAW']
            );

            Log::channel(config('google-sheets.log_channel'))
                ->info("Successfully appended " . count($rows) . " rows to {$sheetName}");
            return true;
        } catch (Exception $e) {
            Log::channel(config('google-sheets.log_channel'))
                ->error("Failed to append rows to '{$sheetName}': " . $e->getMessage());
            return false;
        }
    }

    /**
     * Format header row with styling
     */
    public function formatHeader(string $sheetName, int $rowCount): bool
    {
        try {
            $requests = [];
            $spreadsheet = $this->getSpreadsheetMetadata();
            $sheetId = null;

            foreach ($spreadsheet->getSheets() as $sheet) {
                if ($sheet->getProperties()->getTitle() === $sheetName) {
                    $sheetId = $sheet->getProperties()->getSheetId();
                    break;
                }
            }

            if ($sheetId === null) {
                return false;
            }

            $headerBgColor = config('google-sheets.formatting.header_bg_color');
            $headerTextColor = config('google-sheets.formatting.header_text_color');

            // Convert hex to RGB for Google Sheets API
            $bgRgb = $this->hexToRgb($headerBgColor);
            $textRgb = $this->hexToRgb($headerTextColor);

            $requests[] = [
                'repeatCell' => [
                    'range' => [
                        'sheetId' => $sheetId,
                        'startRowIndex' => 0,
                        'endRowIndex' => 1,
                        'startColumnIndex' => 0,
                        'endColumnIndex' => $rowCount,
                    ],
                    'cell' => [
                        'userEnteredFormat' => [
                            'backgroundColor' => [
                                'red' => $bgRgb['r'] / 255,
                                'green' => $bgRgb['g'] / 255,
                                'blue' => $bgRgb['b'] / 255,
                            ],
                            'textFormat' => [
                                'foregroundColor' => [
                                    'red' => $textRgb['r'] / 255,
                                    'green' => $textRgb['g'] / 255,
                                    'blue' => $textRgb['b'] / 255,
                                ],
                                'bold' => true,
                            ],
                            'horizontalAlignment' => 'CENTER',
                        ],
                    ],
                    'fields' => 'userEnteredFormat(backgroundColor,textFormat,horizontalAlignment)',
                ],
            ];

            // Freeze header row if enabled
            if (config('google-sheets.formatting.freeze_header_row')) {
                $requests[] = [
                    'updateSheetProperties' => [
                        'properties' => [
                            'sheetId' => $sheetId,
                            'gridProperties' => [
                                'frozenRowCount' => 1,
                            ],
                        ],
                        'fields' => 'gridProperties.frozenRowCount',
                    ],
                ];
            }

            $batchUpdateRequest = new Sheets\BatchUpdateSpreadsheetRequest();
            $batchUpdateRequest->setRequests($requests);

            $this->sheetsService->spreadsheets->batchUpdate(
                $this->spreadsheetId,
                $batchUpdateRequest
            );

            return true;
        } catch (Exception $e) {
            Log::channel(config('google-sheets.log_channel'))
                ->error("Failed to format header for '{$sheetName}': " . $e->getMessage());
            return false;
        }
    }

    /**
     * Convert hex color to RGB
     */
    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * Share spreadsheet with email
     */
    public function shareSpreadsheet(string $email): bool
    {
        try {
            $permission = new Drive\Permission();
            $permission->setType('user');
            $permission->setRole('reader');
            $permission->setEmailAddress($email);

            $this->driveService->permissions->create(
                $this->spreadsheetId,
                $permission
            );

            Log::channel(config('google-sheets.log_channel'))
                ->info("Successfully shared spreadsheet with {$email}");
            return true;
        } catch (Exception $e) {
            Log::channel(config('google-sheets.log_channel'))
                ->error("Failed to share spreadsheet: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get spreadsheet URL
     */
    public function getSpreadsheetUrl(): string
    {
        return "https://docs.google.com/spreadsheets/d/{$this->spreadsheetId}";
    }

    /**
     * Set spreadsheet ID
     */
    public function setSpreadsheetId(string $id): void
    {
        $this->spreadsheetId = $id;
    }

    /**
     * Get current spreadsheet ID
     */
    public function getSpreadsheetId(): string
    {
        return $this->spreadsheetId;
    }
}
