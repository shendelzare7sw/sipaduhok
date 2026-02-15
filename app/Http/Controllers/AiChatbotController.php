<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AiChatbotService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AiChatbotController extends Controller
{
    protected $chatbotService;

    public function __construct(AiChatbotService $chatbotService)
    {
        // Auth middleware already applied at route level (routes/web.php)
        // No need to apply again here
        $this->chatbotService = $chatbotService;
    }

    /**
     * Send message to AI chatbot
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        try {
            // Parse history JSON string to array (sent via FormData)
            $historyData = [];
            if ($request->has('history') && !empty($request->input('history'))) {
                $historyData = json_decode($request->input('history'), true);
                if (!is_array($historyData)) {
                    $historyData = [];
                }
            }

            // Validate request
            $validated = $request->validate([
                'message' => 'required|string|max:2000',
                'model' => 'required|string',
                'attachment' => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:4096', // 4MB
            ]);

            // Validate history separately (already parsed from JSON)
            if (!empty($historyData)) {
                if (count($historyData) > 20) {
                    $historyData = array_slice($historyData, -20);
                }

                foreach ($historyData as $msg) {
                    if (!isset($msg['role']) || !in_array($msg['role'], ['user', 'assistant'])) {
                        $historyData = [];
                        break;
                    }
                    if (!isset($msg['content']) || !is_string($msg['content'])) {
                        $historyData = [];
                        break;
                    }
                }
            }

            $userMessage = $validated['message'];
            $selectedModel = $validated['model'];
            $history = $historyData;
            $userRole = auth()->user()->role;

            // Handle file attachment
            $attachedFile = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = $file->store('temp/chatbot-attachments', 'local');

                $attachedFile = [
                    'path' => storage_path('app/' . $path),
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'name' => $file->getClientOriginalName(),
                ];

                // Validate file
                $fileValidation = $this->chatbotService->processFileAttachment($attachedFile);
                if (!$fileValidation['success']) {
                    // Delete temp file
                    Storage::disk('local')->delete($path);

                    return response()->json([
                        'success' => false,
                        'error' => $fileValidation['error'],
                    ], 400);
                }
            }

            // Send message to AI
            $result = $this->chatbotService->sendMessage(
                $userMessage,
                $history,
                $userRole,
                $selectedModel,
                $attachedFile
            );

            // Delete temp file after processing
            if ($attachedFile && isset($path)) {
                Storage::disk('local')->delete($path);
            }

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'response' => $result['response'],
                    'model' => $result['model'],
                    'provider' => $result['provider'] ?? 'unknown',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $result['error'],
                ], 500);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validasi gagal: ' . $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('AI Chatbot Controller Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan server: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available AI models for dropdown
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getModels()
    {
        try {
            $models = $this->chatbotService->getAvailableModels();

            return response()->json([
                'success' => true,
                'models' => $models,
            ]);

        } catch (\Exception $e) {
            Log::error('Get Models Error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'error' => 'Gagal memuat daftar model: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get role-specific quick action buttons
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQuickActions()
    {
        try {
            $userRole = auth()->user()->role;

            $quickActions = match ($userRole) {
                'guru' => [
                    'Cara input nilai?',
                    'Panduan LMS',
                    'Cara generate soal AI?',
                ],

                'admin' => [
                    'Cara kelola pengguna?',
                    'Panduan sistem',
                    'Kontak support',
                ],

                'bendahara' => [
                    'Cara catat pembayaran?',
                    'Laporan keuangan?',
                    'Kontak admin',
                ],

                'wali_kelas' => [
                    'Cara isi rapor?',
                    'Panduan presensi',
                    'Kontak admin',
                ],

                'wakil_kepala_sekolah' => [
                    'Monitoring akademik?',
                    'Laporan kinerja?',
                    'Panduan sistem',
                ],

                'ketua' => [
                    'Dashboard overview?',
                    'Laporan lengkap?',
                    'Kontak support',
                ],

                'sekretaris' => [
                    'Kelola dokumen?',
                    'Arsip data?',
                    'Kontak admin',
                ],

                'orang_tua' => [
                    'Cara cek nilai anak?',
                    'Cara bayar SPP?',
                    'Kontak sekolah',
                ],

                default => [
                    'Panduan sistem',
                    'Kontak admin',
                ],
            };

            return response()->json([
                'success' => true,
                'quick_actions' => $quickActions,
                'role' => $userRole,
            ]);

        } catch (\Exception $e) {
            Log::error('Get Quick Actions Error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'error' => 'Gagal memuat quick actions: ' . $e->getMessage(),
            ], 500);
        }
    }
}
