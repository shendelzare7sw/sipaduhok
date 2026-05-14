@extends('layouts.sneat')

@section('title', 'Riwayat Presensi Siswa')
@section('page-title', 'Riwayat Presensi')
@section('page-subtitle', 'Edit dan koreksi data presensi siswa')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
@include('shared.wali-kelas.styles')
<style>
    .badge-status { font-weight: 700; border-radius: 5px; text-transform: uppercase; font-size: 10px; }
    .table-history thead th { font-size: 11px; text-transform: uppercase; background: #f8f9fc; color: #4e73df; }
    .card-filter { border-top: 4px solid #4e73df; }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-start mb-3">
        <a href="{{ route('wali.presensi.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> Kembali ke Presensi
        </a>
    </div>

    {{-- FILTER --}}
    <div class="card card-filter shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('wali.presensi.riwayat') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">NAMA SISWA</label>
                    <select name="siswa_id" class="form-select select2">
                        <option value="">Semua Siswa</option>
                        @foreach($siswaList as $siswa)
                            <option value="{{ $siswa->id }}" {{ request('siswa_id') == $siswa->id ? 'selected' : '' }}>
                                {{ $siswa->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">DARI TANGGAL</label>
                    <input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">SAMPAI TANGGAL</label>
                    <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">STATUS</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bx bx-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('wali.presensi.riwayat') }}" class="btn btn-light border flex-grow-1">
                        <i class="bx bx-refresh me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- DATA TABLE --}}
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover table-history wk-card-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Tanggal</th>
                        <th>Nama Siswa</th>
                        <th class="text-center">Status</th>
                        <th>Keterangan</th>
                        <th class="text-center">Validasi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $item)
                    <tr>
                        <td class="ps-4 align-middle fw-bold">
                            {{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->translatedFormat('j F Y') }}
                        </td>
                        <td class="align-middle">
                            <div class="fw-bold">{{ $item->siswa->nama_lengkap ?? '-' }}</div>
                            <small class="text-muted">NIS: {{ $item->siswa->nis ?? '-' }}</small>
                        </td>
                        <td class="text-center align-middle">
                            @php
                                $statusMap = [
                                    'hadir' => ['bg' => 'bg-label-success', 'icon' => 'bx-check-circle'],
                                    'sakit' => ['bg' => 'bg-label-warning', 'icon' => 'bx-plus-medical'],
                                    'izin' => ['bg' => 'bg-label-info', 'icon' => 'bx-file'],
                                    'alpha' => ['bg' => 'bg-label-danger', 'icon' => 'bx-x-circle'],
                                ];
                                $st = $statusMap[$item->status] ?? ['bg' => 'bg-label-secondary', 'icon' => 'bx-question-mark'];
                            @endphp
                            <span class="badge {{ $st['bg'] }} badge-status">
                                <i class="bx {{ $st['icon'] }} me-1"></i>{{ $item->status }}
                            </span>
                        </td>
                        <td class="align-middle">
                            <div class="small text-muted mb-1">{{ $item->keterangan ?? '-' }}</div>
                            @if($item->bukti_file)
                                <x-file-preview 
                                    :path="$item->bukti_file" 
                                    label="Lihat Bukti"
                                    class="btn btn-xs btn-outline-primary"
                                    icon="bx bx-show"
                                />
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            @if($item->status_validasi == 'disetujui')
                                <span class="badge bg-label-success rounded-pill">DISETUJUI</span>
                            @elseif($item->status_validasi == 'ditolak')
                                <span class="badge bg-label-danger rounded-pill">DITOLAK</span>
                            @else
                                <span class="badge bg-label-warning rounded-pill">PENDING</span>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            <button class="btn btn-sm btn-icon btn-label-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}">
                                <i class="bx bx-edit"></i>
                            </button>
                        </td>
                    </tr>

                    {{-- MODAL EDIT --}}
                    <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content shadow-lg">
                                <div class="modal-header border-bottom">
                                    <h5 class="modal-title fw-bold">Edit Presensi</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('wali.presensi.riwayat.update', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <p class="mb-1">Siswa: <strong>{{ $item->siswa->nama_lengkap }}</strong></p>
                                            <p class="mb-0">Tanggal: <strong>{{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->translatedFormat('j F Y') }}</strong></p>
                                        </div>
                                        <hr>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Status Kehadiran</label>
                                            <select name="status" class="form-select" required>
                                                <option value="hadir" {{ $item->status == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                                <option value="sakit" {{ $item->status == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                                <option value="izin" {{ $item->status == 'izin' ? 'selected' : '' }}>Izin</option>
                                                <option value="alpha" {{ $item->status == 'alpha' ? 'selected' : '' }}>Alpha</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Validasi (Jika Sakit/Izin)</label>
                                            <select name="status_validasi" class="form-select" onchange="
                                                let statusSelect = this.closest('.modal-body').querySelector('select[name=status]');
                                                if (this.value === 'ditolak') {
                                                    statusSelect.value = 'alpha';
                                                } else if (this.value === 'disetujui' && statusSelect.value === 'alpha') {
                                                    statusSelect.value = 'izin';
                                                }
                                            ">
                                                <option value="" {{ is_null($item->status_validasi) ? 'selected' : '' }}>- Belum Tervalidasi -</option>
                                                <option value="pending" {{ $item->status_validasi == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="disetujui" {{ $item->status_validasi == 'disetujui' ? 'selected' : '' }}>Setujui (Sakit/Izin)</option>
                                                <option value="ditolak" {{ $item->status_validasi == 'ditolak' ? 'selected' : '' }}>Tolak (Jadi Alpha)</option>
                                            </select>
                                            <small class="text-muted mt-1 d-block">Pilih 'Setujui' agar status muncul sebagai Sakit/Izin di orang tua.</small>
                                        </div>
                                        @if($item->bukti_file)
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Bukti Foto/Surat</label>
                                            <div class="d-flex align-items-center">
                                                <a href="{{ asset('storage/' . $item->bukti_file) }}" target="_blank" class="btn btn-sm btn-label-info">
                                                    <i class="bx bx-file me-1"></i>Buka Lampiran
                                                </a>
                                            </div>
                                        </div>
                                        @endif
                                        <div class="mb-0">
                                            <label class="form-label fw-bold">Keterangan / Catatan</label>
                                            <textarea name="keterangan" class="form-control" rows="2">{{ $item->keterangan }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bx bx-calendar-x fs-2 d-block mb-2"></i>
                            Tidak ada data presensi ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($riwayat->hasPages())
        <div class="card-footer border-top bg-light">
            {{ $riwayat->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
