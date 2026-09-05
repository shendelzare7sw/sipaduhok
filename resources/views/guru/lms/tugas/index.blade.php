@extends('layouts.lms-guru')

@section('title', 'Daftar Tugas')
@section('page-title', 'Tugas')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
<div class="guru-lms-tugas-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-tasks me-2"></i>Daftar Tugas</h4>
        <a href="{{ route('guru.lms.tugas.create', [$kelas->id, $mapel->id]) }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i>Buat Tugas Baru
        </a>
    </div>

    @if($tugasList->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Judul Tugas</th>
                        <th class="text-center">Mulai</th>
                        <th class="text-center">Deadline</th>
                        <th class="text-center">Dikumpulkan</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tugasList as $tugas)
                    <tr>
                        <td>
                            <strong>{{ $tugas->judul_tugas }}</strong>
                            <br>
                            <small class="text-muted">{{ Str::limit($tugas->deskripsi, 60) }}</small>
                        </td>
                        <td class="text-center">{{ $tugas->tanggal_mulai->format('d M Y') }}</td>
                        <td class="text-center">
                            {{ $tugas->tanggal_deadline->format('d M Y') }}
                            @if($tugas->isOverdue())
                                <br><span class="badge bg-danger">Lewat</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info">
                                {{ $tugas->submitted_count }}/{{ $tugas->tugas_siswa_count }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($tugas->isOverdue())
                                <span class="badge bg-secondary">Selesai</span>
                            @else
                                <span class="badge bg-success">Aktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('guru.lms.tugas.koreksi', [$kelas->id, $mapel->id, $tugas->id]) }}" 
                                   class="btn btn-success" title="Koreksi">
                                    <i class="fas fa-check-circle"></i>
                                </a>
                                <a href="{{ route('guru.lms.tugas.edit', [$kelas->id, $mapel->id, $tugas->id]) }}" 
                                   class="btn btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-sm" title="Hapus"
                                    data-delete-url="{{ route('guru.lms.tugas.destroy', [$kelas->id, $mapel->id, $tugas->id]) }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $tugasList->links() }}
        </div>
    @else
        <div class="card-custom text-center py-5">
            <i class="fas fa-inbox text-muted text-[64px] opacity-20"></i>
            <p class="text-muted mt-3">Belum ada tugas. Klik "Buat Tugas Baru" untuk memulai.</p>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus tugas ini?
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="POST" class="w-100 m-0">
                        @csrf
                        @method('DELETE')
                        <div class="form-check mb-3 text-start">
                            <input class="form-check-input" type="checkbox" name="hapus_terkait" value="1" id="hapusTerkaitCheck">
                            <label class="form-check-label small text-danger" for="hapusTerkaitCheck">
                                Hapus juga tugas ini dari kelas lain? (Jika ada duplikat)
                            </label>
                        </div>
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    @vite(['resources/js/guru/lms/tugas/index.js'])
@endpush
