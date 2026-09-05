@extends('layouts.app')

@section('title', 'Riwayat Presensi - ' . $siswa->nama_lengkap)
@section('page-title', 'Riwayat Presensi')


@section('styles')
    @vite(['resources/css/wali-siswa/presensi/riwayat-presensi.css'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y attendance-history-page">
    <div class="history-heading d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <div class="history-title">Riwayat Presensi</div>
            <p class="text-muted mb-0">
                <i class="fas fa-user-graduate me-1"></i>{{ $siswa->nama_lengkap }}
                <span class="mx-2">|</span>
                <i class="fas fa-school me-1"></i>{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}
            </p>
        </div>
        <div class="history-actions">
            <a href="{{ route('wali-siswa.presensi.ajukan-izin', $siswa->id) }}" class="btn btn-warning text-white fw-bold">
                <i class="fas fa-file-medical me-1"></i>Ajukan Izin / Sakit
            </a>
            <a href="{{ route('wali-siswa.presensi.anak', $siswa->id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card summary-hadir">
            <div class="summary-label">Hadir</div>
            <div class="summary-number">{{ $rekap['hadir'] }}</div>
        </div>
        <div class="summary-card summary-sakit">
            <div class="summary-label">Sakit</div>
            <div class="summary-number">{{ $rekap['sakit'] }}</div>
        </div>
        <div class="summary-card summary-izin">
            <div class="summary-label">Izin</div>
            <div class="summary-number">{{ $rekap['izin'] }}</div>
        </div>
        <div class="summary-card summary-alpha">
            <div class="summary-label">Alpha</div>
            <div class="summary-number">{{ $rekap['alpha'] }}</div>
        </div>
    </div>

    <div class="history-filter mb-4">
        <form action="{{ route('wali-siswa.presensi.riwayat-presensi', $siswa->id) }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold small">Dari Tanggal</label>
                <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small">Sampai Tanggal</label>
                <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="hadir" {{ request('status') === 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="sakit" {{ request('status') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="izin" {{ request('status') === 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="alpha" {{ request('status') === 'alpha' ? 'selected' : '' }}>Alpha</option>
                </select>
            </div>
            <div class="col-md-3">
                <div class="filter-actions d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-search me-1"></i>Filter
                    </button>
                    <a href="{{ route('wali-siswa.presensi.riwayat-presensi', $siswa->id) }}" class="btn btn-light border flex-fill">
                        <i class="fas fa-sync-alt me-1"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="history-table-card">
        <div class="table-responsive">
            <table class="table table-hover table-history mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Tanggal</th>
                        <th>Hari</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Validasi</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $item)
                        @php
                            $statusMap = [
                                'hadir' => ['class' => 'success', 'label' => 'Hadir'],
                                'sakit' => ['class' => 'warning', 'label' => 'Sakit'],
                                'izin' => ['class' => 'info', 'label' => 'Izin'],
                                'alpha' => ['class' => 'danger', 'label' => 'Alpha'],
                            ];
                            $status = $statusMap[$item->status] ?? ['class' => 'secondary', 'label' => ucfirst($item->status)];
                            $buktiPath = $item->bukti_file;
                            $buktiUrl = $buktiPath ? asset('storage/' . $buktiPath) : null;
                            $buktiExtension = $buktiPath ? strtolower(pathinfo($buktiPath, PATHINFO_EXTENSION)) : null;
                            $isBuktiImage = in_array($buktiExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            $isBuktiPdf = $buktiExtension === 'pdf';
                        @endphp
                        <tr>
                            <td data-label="Tanggal" class="ps-4 fw-bold">
                                {{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->translatedFormat('j F Y') }}
                            </td>
                            <td data-label="Hari" class="text-muted">
                                {{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->translatedFormat('l') }}
                            </td>
                            <td data-label="Status" class="text-center">
                                <span class="badge bg-{{ $status['class'] }} rounded-pill px-3 py-2">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td data-label="Validasi" class="text-center">
                                @if($item->status_validasi === 'disetujui')
                                    <span class="badge bg-success rounded-pill">Disetujui</span>
                                @elseif($item->status_validasi === 'ditolak')
                                    <span class="badge bg-danger rounded-pill">Ditolak</span>
                                @elseif($item->status_validasi === 'pending')
                                    <span class="badge bg-warning rounded-pill">Menunggu</span>
                                @else
                                    <span class="badge bg-label-secondary rounded-pill">-</span>
                                @endif
                            </td>
                            <td data-label="Keterangan">
                                <div class="small text-muted">{{ $item->keterangan ?: 'Tidak ada catatan' }}</div>
                                @if($buktiPath)
                                    <button type="button"
                                            class="btn btn-outline-primary btn-sm mt-2"
                                            data-bs-toggle="modal"
                                            data-bs-target="#buktiPresensiModal{{ $item->id }}">
                                        <i class="fas fa-paperclip me-1"></i>Lihat Bukti
                                    </button>
                                @endif
                            </td>
                        </tr>

                        @if($buktiPath)
                            <div class="modal fade" id="buktiPresensiModal{{ $item->id }}" tabindex="-1" aria-labelledby="buktiPresensiModalLabel{{ $item->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="buktiPresensiModalLabel{{ $item->id }}">
                                                <i class="fas fa-paperclip me-2"></i>Lampiran Bukti
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body p-0">
                                            @if($isBuktiImage)
                                                <div class="text-center p-3">
                                                    <img src="{{ $buktiUrl }}"
                                                         alt="Lampiran bukti presensi {{ $siswa->nama_lengkap }}"
                                                         class="img-fluid rounded attendance-proof-image">
                                                </div>
                                            @elseif($isBuktiPdf)
                                                <iframe src="{{ $buktiUrl }}"
                                                        title="Lampiran bukti presensi {{ $siswa->nama_lengkap }}"
                                                        class="attendance-proof-frame"></iframe>
                                            @else
                                                <div class="text-center p-5">
                                                    <i class="fas fa-file fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted mb-0">Format lampiran tidak dapat dipreview.</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-calendar-times fa-2x d-block mb-2"></i>
                                Tidak ada data presensi ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($riwayat->hasPages())
            <div class="card-footer bg-light border-top">
                {{ $riwayat->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
