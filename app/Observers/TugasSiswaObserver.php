<?php

namespace App\Observers;

use App\Models\TugasSiswa;
use App\Services\NilaiSyncService;

class TugasSiswaObserver
{
    public function __construct(private NilaiSyncService $sync) {}

    public function saved(TugasSiswa $tugasSiswa): void
    {
        if ($tugasSiswa->status === 'dinilai' && $tugasSiswa->nilai !== null) {
            $this->sync->syncFromTugasSiswa($tugasSiswa);
        }
    }
}
