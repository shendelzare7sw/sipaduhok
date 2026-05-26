<?php

namespace App\Observers;

use App\Models\UjianSiswa;
use App\Services\NilaiSyncService;

class UjianSiswaObserver
{
    public function __construct(private NilaiSyncService $sync) {}

    public function saved(UjianSiswa $ujianSiswa): void
    {
        if ($ujianSiswa->status === 'selesai' && $ujianSiswa->nilai !== null) {
            $this->sync->syncFromUjianSiswa($ujianSiswa);
        }
    }
}
