<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InfoPembayaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class InfoPembayaranController extends Controller
{
    /**
     * Tampilkan halaman info pembayaran
     */
    public function index()
    {
        $infoPembayaran = InfoPembayaran::getInstance();
        
        return view('bendahara.info-pembayaran.index', [
            'infoPembayaran' => $infoPembayaran,
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
            $request->validate([
                'midtrans_merchant_id' => 'required|string|max:255',
                'midtrans_server_key' => 'required|string',
                'midtrans_client_key' => 'required|string',
                'midtrans_is_production' => 'nullable|boolean',
            ]);
            
            // Encrypt sensitive keys
            $data = [
                'midtrans_merchant_id' => $request->midtrans_merchant_id,
                'midtrans_server_key' => Crypt::encryptString($request->midtrans_server_key),
                'midtrans_client_key' => $request->midtrans_client_key, // Client key tidak perlu encrypt
                'midtrans_is_production' => $request->has('midtrans_is_production') ? true : false,
                'updated_by' => auth()->id(),
            ];
            
            $message = 'Konfigurasi Midtrans berhasil diperbarui.';
            
        } else {
            return redirect()->back()->with('error', 'Tipe update tidak valid.');
        }
        
        DB::beginTransaction();
        try {
            $infoPembayaran = InfoPembayaran::getInstance();
            $infoPembayaran->update($data);
            
            DB::commit();
            return redirect()->route('bendahara.info-pembayaran.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
}