<?php

namespace App\Http\Controllers\WaliKelas\Traits;

use App\Models\TenagaPendidik;
use App\Models\Kelas;
use App\Models\WaliKelasAssignment;

trait WaliKelasHelper
{
    /**
     * Get tenaga pendidik for current logged in user.
     */
    protected function getTenagaPendidik(): ?TenagaPendidik
    {
        return TenagaPendidik::where('user_id', auth()->id())->first();
    }

    /**
     * Get all kelas yang dipegang wali kelas ini.
     */
    protected function getKelasWali(TenagaPendidik $tenagaPendidik)
    {
        return Kelas::whereHas('waliKelasAssignments', function($q) use ($tenagaPendidik) {
            $q->where('tenaga_pendidik_id', $tenagaPendidik->id);
        })->with(['cabang', 'tahunAjaran'])->get();
    }

    /**
     * Get selected kelas from session or auto-select if only one.
     * Returns null if no kelas assigned or if selection is needed.
     */
    protected function getSelectedKelas(TenagaPendidik $tenagaPendidik): ?Kelas
    {
        $kelasList = $this->getKelasWali($tenagaPendidik);

        if ($kelasList->isEmpty()) {
            return null;
        }

        // If only one kelas, always use it
        if ($kelasList->count() === 1) {
            session(['wali_kelas_selected' => $kelasList->first()->id]);
            return $kelasList->first();
        }

        // Multiple kelas - check session
        $selectedId = session('wali_kelas_selected');
        
        if ($selectedId) {
            $selectedKelas = $kelasList->firstWhere('id', $selectedId);
            if ($selectedKelas) {
                return $selectedKelas;
            }
        }

        // No valid selection - return null to trigger redirect
        return null;
    }

    /**
     * Check if wali kelas needs to select a kelas.
     */
    protected function needsKelasSelection(TenagaPendidik $tenagaPendidik): bool
    {
        $kelasList = $this->getKelasWali($tenagaPendidik);
        
        if ($kelasList->count() <= 1) {
            return false;
        }

        $selectedId = session('wali_kelas_selected');
        if (!$selectedId) {
            return true;
        }

        // Check if selected kelas is still valid
        return !$kelasList->contains('id', $selectedId);
    }

    /**
     * Get redirect response to pilih kelas page.
     */
    protected function redirectToPilihKelas()
    {
        return redirect()->route('wali.pilih-kelas')
            ->with('info', 'Silakan pilih kelas yang ingin Anda kelola.');
    }
}
