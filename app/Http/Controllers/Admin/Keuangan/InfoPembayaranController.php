<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\InfoPembayaran;
use App\Services\MidtransService;
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
            'midtransWebhookUrl' => route('midtrans.webhook'),
            'midtransChannels' => collect(app(MidtransService::class)->paymentMethods())->pluck('name')->all(),
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

        } elseif ($type === 'midtrans') {
            $validated = $request->validate([
                'midtrans_sandbox_server_key' => ['nullable', 'string', 'max:255', 'regex:/^SB-Mid-server-[A-Za-z0-9_-]{10,}$/'],
                'midtrans_production_server_key' => ['nullable', 'string', 'max:255', 'regex:/^Mid-server-[A-Za-z0-9_-]{10,}$/'],
                'midtrans_environment' => ['required', 'in:sandbox,production'],
            ], [
                'midtrans_sandbox_server_key.regex' => 'Server key Sandbox harus diawali SB-Mid-server-.',
                'midtrans_production_server_key.regex' => 'Server key Production harus diawali Mid-server-.',
            ]);

            $info = InfoPembayaran::getInstance();
            $isProduction = $validated['midtrans_environment'] === 'production';
            $activeKey = $isProduction
                ? (filled($validated['midtrans_production_server_key'] ?? null) ? $validated['midtrans_production_server_key'] : $info->getMidtransServerKey('production'))
                : (filled($validated['midtrans_sandbox_server_key'] ?? null) ? $validated['midtrans_sandbox_server_key'] : $info->getMidtransServerKey('sandbox'));

            if (blank($activeKey)) {
                return back()->withErrors([
                    ($isProduction ? 'midtrans_production_server_key' : 'midtrans_sandbox_server_key') => 'Server key untuk environment aktif wajib tersedia.',
                ])->withInput();
            }

            $data = [
                'midtrans_is_production' => $isProduction,
                'midtrans_enabled' => true,
                'updated_by' => auth()->id(),
            ];

            if (filled($validated['midtrans_sandbox_server_key'] ?? null)) {
                $data['midtrans_sandbox_server_key'] = Crypt::encryptString(trim($validated['midtrans_sandbox_server_key']));
            }

            if (filled($validated['midtrans_production_server_key'] ?? null)) {
                $data['midtrans_production_server_key'] = Crypt::encryptString(trim($validated['midtrans_production_server_key']));
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

        } elseif ($type === 'midtrans_toggle') {
            $enabled = $request->has('midtrans_enabled');

            if ($enabled && ! InfoPembayaran::getInstance()->hasMidtrans()) {
                return redirect()->back()->with('error', 'Lengkapi server key Midtrans sebelum mengaktifkan kanal digital.');
            }

            $data = [
                'midtrans_enabled' => $enabled,
                'updated_by' => auth()->id(),
            ];

            $status = $enabled ? 'diaktifkan' : 'dinonaktifkan';
            $message = "Metode pembayaran digital berhasil {$status}.";

        } elseif ($type === 'direct_transfer_toggle') {
            $info = InfoPembayaran::getInstance();
            $enabled = $request->boolean('direct_transfer_enabled');

            if ($enabled && ! $info->hasRekeningBank()) {
                return redirect()->back()->with('error', 'Lengkapi rekening bank sebelum mengaktifkan Direct Transfer.');
            }

            $data = [
                'direct_transfer_enabled' => $enabled,
                'updated_by' => auth()->id(),
            ];

            $message = $enabled
                ? 'Direct Transfer berhasil diaktifkan dan akan tampil untuk wali siswa.'
                : 'Direct Transfer berhasil dinonaktifkan dan disembunyikan dari wali siswa.';

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
