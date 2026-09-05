@extends('layouts.app')

@section('title', 'Presensi')
@section('page-title', 'Presensi Kehadiran')
@section('page-subtitle', 'Lihat rekap presensi kehadiran')


@section('styles')
    @vite(['resources/css/siswa/sia/presensi/index.css', 'resources/js/siswa/sia/presensi/index.js'])
@endsection

@section('content')
<div class="s-page">

<div class="s-stat-grid">
    <div class="s-stat-card">
        <div class="s-stat-icon s-stat-icon-success">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <div class="s-stat-value">{{ $rekap['hadir'] }}</div>
            <div class="s-stat-label">Hari Hadir</div>
        </div>
    </div>

    <div class="s-stat-card">
        <div class="s-stat-icon s-stat-icon-warning">
            <i class="fas fa-notes-medical"></i>
        </div>
        <div>
            <div class="s-stat-value">{{ $rekap['sakit'] }}</div>
            <div class="s-stat-label">Hari Sakit</div>
        </div>
    </div>

    <div class="s-stat-card">
        <div class="s-stat-icon s-stat-icon-info">
            <i class="fas fa-file-alt"></i>
        </div>
        <div>
            <div class="s-stat-value">{{ $rekap['izin'] }}</div>
            <div class="s-stat-label">Hari Izin</div>
        </div>
    </div>

    <div class="s-stat-card">
        <div class="s-stat-icon s-stat-icon-danger">
            <i class="fas fa-times-circle"></i>
        </div>
        <div>
            <div class="s-stat-value">{{ $rekap['alpha'] }}</div>
            <div class="s-stat-label">Hari Alpha</div>
        </div>
    </div>
</div>

<div class="s-card mb-4">
    <div class="s-card-header">
        <div>
            <h5 class="s-card-title">
                <i class="fas fa-calendar-check me-2 text-primary"></i>Riwayat Presensi Bulan Ini
            </h5>
            <p class="s-card-subtitle">
                Bulan {{ now()->translatedFormat('F Y') }}
            </p>
        </div>
        {{-- Note: Fitur Ajukan Izin dipindahkan ke akses Wali Siswa --}}
        {{-- Siswa fokus pada pembelajaran, pengajuan izin dilakukan oleh wali siswa sebagai bentuk pendampingan --}}
    </div>
</div>

@forelse($presensi as $minggu => $dataList)
<div class="s-card mb-4">
    <div class="s-card-header">
        <div class="attendance-week-title">
            <i class="fas fa-calendar-week me-2"></i>Minggu ke-{{ $minggu }}
        </div>
        <div class="attendance-note">{{ $dataList->count() }} catatan presensi</div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover s-card-table mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Tanggal</th>
                    <th>Hari</th>
                    <th class="text-center">Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataList as $item)
                <tr>
                    <td class="ps-4 fw-bold text-dark align-middle" data-label="Tanggal">{{ $item->tanggal->format('d F Y') }}</td>
                    <td class="align-middle text-muted" data-label="Hari">{{ ucfirst($item->tanggal->locale('id')->dayName) }}</td>
                    <td class="text-center align-middle" data-label="Status">
                        @php
                            $badgeClass = 'secondary';
                            $statusLabel = strtoupper($item->status);

                            if ($item->status_validasi == 'pending') {
                                $statusLabel = 'MENUNGGU VALIDASI';
                                $badgeClass = 'warning';
                            } elseif ($item->status_validasi == 'ditolak') {
                                $statusLabel = 'DITOLAK';
                                $badgeClass = 'danger';
                            } else {
                                $map = [
                                    'hadir' => 'success',
                                    'sakit' => 'warning',
                                    'izin'  => 'info',
                                    'alpha' => 'danger',
                                ];
                                $badgeClass = $map[$item->status] ?? 'secondary';
                            }
                        @endphp
                        <span class="s-chip s-chip-{{ $badgeClass === 'success' ? 'success' : ($badgeClass === 'warning' ? 'warning' : ($badgeClass === 'info' ? 'info' : ($badgeClass === 'danger' ? 'danger' : 'muted'))) }}">
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td class="align-middle small text-muted" data-label="Keterangan">
                        <div class="mb-1">{{ $item->keterangan ?? 'Tidak ada catatan' }}</div>
                        @if($item->bukti_file)
                            @php
                                $ext = strtolower(pathinfo($item->bukti_file, PATHINFO_EXTENSION));
                                $isPdf = $ext === 'pdf';
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                $previewUrl = preview_url($item->bukti_file);
                                $downloadUrl = asset('storage/' . $item->bukti_file);
                            @endphp

                            <button type="button" 
                                    class="btn btn-sm btn-outline-primary py-1 px-2 mt-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modal-bukti-{{ $item->id }}"
                                    @if($isPdf) data-pdf-preview-target="iframe-{{ $item->id }}" data-pdf-preview-url="{{ $previewUrl }}" @endif>
                                <i class="fas fa-eye me-1"></i>Lihat Bukti
                            </button>

                            <!-- Modal Preview -->
                            <div class="modal fade" id="modal-bukti-{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered {{ $isPdf ? 'modal-xl' : 'modal-lg' }}">
                                    <div class="modal-content {{ $isPdf ? 'modal-content-presensi-pdf' : '' }}">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="fas {{ $isPdf ? 'fa-file-pdf' : 'fa-image' }} me-2"></i>Preview Bukti
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body {{ $isPdf ? 'p-0 h-100' : 'text-center bg-light' }}">
                                            @if($isPdf)
                                                <iframe id="iframe-{{ $item->id }}" src="" class="presensi-preview-frame"></iframe>
                                            @elseif($isImage)
                                                <img src="{{ $downloadUrl }}" alt="Preview" class="img-fluid rounded shadow-sm presensi-preview-image">
                                            @else
                                                <div class="py-5 text-center">
                                                    <i class="fas fa-file-download fa-3x text-muted mb-3"></i>
                                                    <p>File tidak dapat dipreview.</p>
                                                    <a href="{{ $downloadUrl }}" download class="btn btn-primary">Download File</a>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <a href="{{ $downloadUrl }}" download class="btn btn-primary">
                                                <i class="fas fa-download me-1"></i>Download
                                            </a>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@empty
<div class="s-card">
    <div class="s-empty">
        <div class="s-empty-icon"><i class="fas fa-calendar-times"></i></div>
        <h5>Belum Ada Data Presensi Bulan Ini</h5>
        <p class="mb-0">Data presensi akan diperbarui otomatis oleh wali kelas setelah pembelajaran.</p>
    </div>
</div>
@endforelse

</div>
@endsection
