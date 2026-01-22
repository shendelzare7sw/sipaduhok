@extends('layouts.sneat')

@section('title', 'Detail Kegiatan')

@section('page-title', 'Detail Kegiatan')
@section('page-subtitle', 'Kalender Akademik')

@section('sidebar-menu')
    @include('sekretaris.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div style="max-width: 1000px; margin: 0 auto; padding: 0 1rem;">
<div class="card shadow-lg border-0">
    <div class="card-header bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="m-0 fw-bold text-white">{{ $kalender->nama_kegiatan }}</h5>
            <div class="btn-group" role="group">
                <a href="{{ route('sekretaris.kalender.edit', $kalender->id) }}" class="btn btn-warning btn-sm shadow-sm">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
                <button type="button" class="btn btn-danger btn-sm shadow-sm" onclick="confirmDelete({{ $kalender->id }}, '{{ str_replace('"', '&quot;', $kalender->nama_kegiatan) }}')">
                    <i class="fas fa-trash me-1"></i> Hapus
                </button>
                <a href="{{ route('sekretaris.kalender.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="fw-bold text-muted small">NAMA KEGIATAN</label>
                    <h5 class="text-dark">{{ $kalender->nama_kegiatan }}</h5>
                </div>

                <div class="mb-3">
                    <label class="fw-bold text-muted small">JENIS KEGIATAN</label>
                    <div>
                        <span class="badge bg-info" style="font-size: 13px; padding: 8px 12px;">
                            {{ $kalender->jenis_label ?? $kalender->jenis_kegiatan }}
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="fw-bold text-muted small">STATUS</label>
                    <div>
                        @if($kalender->status == 'aktif')
                            <span class="badge bg-success" style="font-size: 13px; padding: 8px 12px;">AKTIF</span>
                        @elseif($kalender->status == 'draft')
                            <span class="badge bg-warning text-white" style="font-size: 13px; padding: 8px 12px;">DRAFT</span>
                        @else
                            <span class="badge bg-secondary" style="font-size: 13px; padding: 8px 12px;">SELESAI</span>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label class="fw-bold text-muted small">VISIBILITAS SISWA</label>
                    <div class="d-flex align-items-center">
                        <div class="form-check form-switch ps-0">
                            <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" 
                                id="visibilitySwitch" 
                                style="cursor: pointer; width: 3em; height: 1.5em;"
                                {{ !$kalender->is_hidden_siswa ? 'checked' : '' }}
                                onchange="toggleVisibility({{ $kalender->id }}, this)">
                            <label class="form-check-label d-flex align-items-center" for="visibilitySwitch">
                                <span id="visibilityLabel" class="badge {{ !$kalender->is_hidden_siswa ? 'bg-primary' : 'bg-secondary' }}" style="font-size: 13px; padding: 8px 12px;">
                                    <i class="fas {{ !$kalender->is_hidden_siswa ? 'fa-eye' : 'fa-eye-slash' }} me-2"></i>
                                    {{ !$kalender->is_hidden_siswa ? 'TAMPIL DI SISWA' : 'DISEMBUNYIKAN' }}
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="fw-bold text-muted small">WAKTU KEGIATAN</label>
                    <div class="text-dark">
                        <i class="fas fa-calendar me-2 text-primary"></i>
                        <strong>{{ $kalender->tanggal_mulai->translatedFormat('d F Y') }}</strong>
                        @if($kalender->tanggal_selesai && $kalender->tanggal_selesai != $kalender->tanggal_mulai)
                            <br>
                            <i class="fas fa-arrow-right me-2 text-primary"></i>
                            <strong>{{ $kalender->tanggal_selesai->translatedFormat('d F Y') }}</strong>
                        @endif
                    </div>
                </div>

                @if($kalender->waktu_mulai)
                <div class="mb-3">
                    <label class="fw-bold text-muted small">JAM KEGIATAN</label>
                    <div class="text-dark">
                        <i class="fas fa-clock me-2 text-primary"></i>
                        <strong>{{ $kalender->waktu_mulai }}</strong>
                        @if($kalender->waktu_selesai)
                            - <strong>{{ $kalender->waktu_selesai }}</strong>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <hr>

        @if($kalender->keterangan)
        <div class="mb-4">
            <label class="fw-bold text-muted small">KETERANGAN / DESKRIPSI</label>
            <div class="bg-light p-3 rounded border" style="line-height: 1.6; color: #333;">
                {!! nl2br(e($kalender->keterangan)) !!}
            </div>
        </div>
        @endif

        @if($kalender->lampiran_surat)
        <div class="mb-4">
            <label class="fw-bold text-muted small">LAMPIRAN</label>
            <div>
                <a href="{{ asset('storage/' . $kalender->lampiran_surat) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-file-pdf me-1"></i> Lihat Dokumen PDF
                </a>
            </div>
        </div>
        @endif

        <hr>

        <div class="row text-muted small">
            <div class="col-md-6">
                <div class="mb-2">
                    <i class="fas fa-user-circle me-2"></i>
                    <strong>Dibuat oleh:</strong> {{ $kalender->creator->name ?? 'Sistem' }}
                </div>
                <div class="mb-2">
                    <i class="fas fa-calendar me-2"></i>
                    <strong>Tanggal dibuat:</strong> {{ $kalender->created_at->translatedFormat('d F Y H:i') }}
                </div>
            </div>
            <div class="col-md-6">
                @if($kalender->updated_at != $kalender->created_at)
                <div class="mb-2">
                    <i class="fas fa-user-edit me-2"></i>
                    <strong>Diupdate oleh:</strong> {{ $kalender->updater->name ?? 'Sistem' }}
                </div>
                <div class="mb-2">
                    <i class="fas fa-clock me-2"></i>
                    <strong>Terakhir diupdate:</strong> {{ $kalender->updated_at->translatedFormat('d F Y H:i') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>

{{-- MODAL KONFIRMASI HAPUS --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                <h6 class="fw-bold mb-2">Apakah Anda yakin ingin menghapus kegiatan ini?</h6>
                <p class="text-muted mb-0" id="deleteKalenderName"></p>
                <small class="text-danger d-block mt-2">
                    <i class="fas fa-info-circle me-1"></i>Tindakan ini tidak dapat dibatalkan
                </small>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function confirmDelete(id, name) {
        document.getElementById('deleteKalenderName').textContent = name;
        document.getElementById('deleteForm').action = '{{ route('sekretaris.kalender.destroy', ':id') }}'.replace(':id', id);
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }

    function toggleVisibility(id, checkbox) {
        const isChecked = checkbox.checked;
        const labelBadge = document.getElementById('visibilityLabel');
        
        // Optimistic UI update
        if (isChecked) {
            labelBadge.className = 'badge bg-primary';
            labelBadge.innerHTML = '<i class="fas fa-eye me-2"></i> TAMPIL DI SISWA';
        } else {
            labelBadge.className = 'badge bg-secondary';
            labelBadge.innerHTML = '<i class="fas fa-eye-slash me-2"></i> DISEMBUNYIKAN';
        }

        const baseUrl = "{{ url('sekretaris/kalender') }}";
            
        fetch(`${baseUrl}/${id}/toggle-visibility`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 3000
                    });
                } else {
                    console.log(data.message);
                }
            } else {
                throw new Error(data.message || 'Gagal update status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Revert UI on error
            checkbox.checked = !isChecked;
            if (!isChecked) { 
                labelBadge.className = 'badge bg-primary';
                labelBadge.innerHTML = '<i class="fas fa-eye me-2"></i> TAMPIL DI SISWA';
            } else { 
                labelBadge.className = 'badge bg-secondary';
                labelBadge.innerHTML = '<i class="fas fa-eye-slash me-2"></i> DISEMBUNYIKAN';
            }
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal mengubah status visibilitas.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            } else {
                alert('Gagal ubah status');
            }
        });
    }
</script>
@endsection
