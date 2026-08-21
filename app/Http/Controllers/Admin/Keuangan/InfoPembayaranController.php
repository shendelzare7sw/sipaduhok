<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\InfoPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class InfoPembayaranController extends Controller
{
    /**
     * Tampilkan halaman info pembayaran
     */
    public function index()
    {
        $infoPembayaran = InfoPembayaran::getInstance();

        return view('admin.keuangan.info-pembayaran.index', [
            'infoPembayaran' => $infoPembayaran,
            'paywuzWebhookUrl' => route('paywuz.webhook'),
        ]);
    }

    /**
     * Update info pembayaran
     */
    public function update(Request $request)
    {
        $type = $request->input('type');

        if ($type === 'rekening') {
            $request->validate([
                'nama_bank' => 'required|string|max:255',
                'rekening_bank' => 'required|string|max:255',
                'atas_nama' => 'required|string|max:255',
            ]);

            $data = [
                'nama_bank' => $request->nama_bank,
                'rekening_bank' => $request->rekening_bank,
                'atas_nama' => $request->atas_nama,
                'updated_by' => auth()->id(),
            ];

            $message = 'Informasi rekening bank berhasil diperbarui.';

        } elseif ($type === 'paywuz') {
            $validated = $request->validate([
                'paywuz_sandbox_api_key' => ['nullable', 'string', 'max:255', 'regex:/^pk_sand_[a-f0-9]{32}$/i'],
                'paywuz_production_api_key' => ['nullable', 'string', 'max:255', 'regex:/^pk_live_[a-f0-9]{32}$/i'],
                'paywuz_environment' => ['required', 'in:sandbox,production'],
                'paywuz_fee_by_merchant' => ['nullable', 'boolean'],
            ], [
                'paywuz_sandbox_api_key.regex' => 'API key Sandbox harus berformat pk_sand_ diikuti 32 karakter.',
                'paywuz_production_api_key.regex' => 'API key Production harus berformat pk_live_ diikuti 32 karakter.',
            ]);

            $info = InfoPembayaran::getInstance();
            $isProduction = $validated['paywuz_environment'] === 'production';
            $activeKey = $isProduction
                ? (filled($validated['paywuz_production_api_key'] ?? null) ? $validated['paywuz_production_api_key'] : $info->getPaywuzApiKey('production'))
                : (filled($validated['paywuz_sandbox_api_key'] ?? null) ? $validated['paywuz_sandbox_api_key'] : $info->getPaywuzApiKey('sandbox'));

            if (blank($activeKey)) {
                return back()->withErrors([
                    ($isProduction ? 'paywuz_production_api_key' : 'paywuz_sandbox_api_key') => 'API key untuk environment aktif wajib tersedia.',
                ])->withInput();
            }

            $data = [
                'paywuz_is_production' => $isProduction,
                'paywuz_enabled' => true,
                'paywuz_fee_by_merchant' => $request->boolean('paywuz_fee_by_merchant'),
                'updated_by' => auth()->id(),
            ];

            if (filled($validated['paywuz_sandbox_api_key'] ?? null)) {
                $data['paywuz_sandbox_api_key'] = Crypt::encryptString(trim($validated['paywuz_sandbox_api_key']));
            }

            if (filled($validated['paywuz_production_api_key'] ?? null)) {
                $data['paywuz_production_api_key'] = Crypt::encryptString(trim($validated['paywuz_production_api_key']));
            }

            $message = 'Konfigurasi pembayaran digital berhasil diperbarui.';

        } elseif ($type === 'tunai') {
            $request->validate([
                'tunai_lokasi' => 'nullable|string|max:255',
                'tunai_jam_operasional' => 'nullable|string|max:255',
                'tunai_deskripsi' => 'nullable|string|max:1000',
            ]);

            $data = [
                'tunai_lokasi' => $request->tunai_lokasi,
                'tunai_jam_operasional' => $request->tunai_jam_operasional,
                'tunai_deskripsi' => $request->tunai_deskripsi,
                'updated_by' => auth()->id(),
            ];

            $message = 'Informasi pembayaran tunai berhasil diperbarui.';

        } elseif ($type === 'paywuz_toggle') {
            $data = [
                'paywuz_enabled' => $request->has('paywuz_enabled'),
                'updated_by' => auth()->id(),
            ];

            $status = $request->has('paywuz_enabled') ? 'diaktifkan' : 'dinonaktifkan';
            $message = "Metode pembayaran digital berhasil {$status}.";

        } else {
            return redirect()->back()->with('error', 'Tipe update tidak valid.');
        }

        DB::beginTransaction();
        try {
            $infoPembayaran = InfoPembayaran::getInstance();
            $infoPembayaran->update($data);

            DB::commit();

            return redirect()->route('admin.keuangan.info-pembayaran.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            return redirect()->back()
                ->with('error', 'Konfigurasi pembayaran gagal disimpan. Silakan coba kembali.')
                ->withInput();
        }
    }
}
