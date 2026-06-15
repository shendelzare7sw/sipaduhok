<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Tenaga Pendidik</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/admin/users/print.css'])
</head>
<body>
    <div class="print-controls no-print">
        <a href="{{ route('admin.users.tenaga-pendidik') }}" class="btn btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
        <div class="zoom-controls">
            <button class="btn-zoom" data-zoom-out title="Perkecil">-</button>
            <span class="zoom-level" id="zoomLevel">100%</span>
            <button class="btn-zoom" data-zoom-in title="Perbesar">+</button>
            <button class="btn-zoom btn-fit" data-zoom-reset title="Reset">Fit</button>
        </div>
        <button type="button" class="btn btn-print" data-print-page><i class="fas fa-print"></i> Cetak</button>
    </div>

    <div class="print-wrapper">
        <div class="container">
            @include('partials.print-header', ['cabang' => $cabang ?? null])

            <div class="title">
                <h3>LAPORAN TENAGA PENDIDIK</h3>
                @if($request->has('role') && $request->role)
                    <p>Role: {{ $roles[$request->role] ?? $request->role }}</p>
                @endif
                <p>Total Data: {{ count($tenagaPendidik) }} Tenaga Pendidik</p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="20%">Nama Lengkap</th>
                        <th width="15%">NIP</th>
                        <th width="15%">Role / Jabatan</th>
                        <th width="20%">Email</th>
                        <th width="15%">Telepon</th>
                        <th width="10%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tenagaPendidik as $index => $tp)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $tp->name }}</td>
                            <td>{{ $tp->tenagaPendidik->nip ?? '-' }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $tp->role)) }}</td>
                            <td>{{ $tp->email }}</td>
                            <td>{{ $tp->tenagaPendidik->telepon ?? $tp->phone ?? '-' }}</td>
                            <td class="text-center">{{ $tp->is_active ? 'Aktif' : 'Non-Aktif' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="footer">
                <div>
                    <p>Dicetak pada: {{ now()->format('d F Y, H:i') }}</p>
                    <p>Oleh: {{ auth()->user()->name }}</p>
                </div>
                <div class="signature-block">
                    <p>Tangerang Selatan, {{ now()->format('d F Y') }}</p>
                    <p>Mengetahui,</p>
                    <div class="signature-line"></div>
                    <p>Kepala PKBM House of Knowledge</p>
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/js/admin/users/print.js'])
</body>
</html>
