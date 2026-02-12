<?php

namespace App\Exports;

use App\Models\Rapor;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RaporExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $rapor;

    public function __construct(Rapor $rapor)
    {
        $this->rapor = $rapor;
    }

    public function view(): View
    {
        // Choose view based on jenis_rapor
        $viewName = $this->rapor->jenis_rapor === 'tengah_semester'
            ? 'exports.rapor-pts'
            : 'exports.rapor-pas';

        return view($viewName, [
            'rapor' => $this->rapor,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Bold first row (header)
            1 => ['font' => ['bold' => true]],
        ];
    }
}
