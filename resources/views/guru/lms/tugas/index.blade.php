@extends('layouts.lms-guru')

@section('title', 'Daftar Tugas')
@section('page-title', 'Tugas & Latihan')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-tasks me-2"></i>Daftar Tugas & Latihan</h4>
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
                                    onclick="confirmDelete('{{ route('guru.lms.tugas.destroy', [$kelas->id, $mapel->id, $tugas->id]) }}')">
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
            <i class="fas fa-inbox text-muted" style="font-size: 64px; opacity: 0.2;"></i>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <div class="form-check mb-3 text-start">
                            <input class="form-check-input" type="checkbox" name="hapus_terkait" value="1" id="hapusTerkaitCheck">
                            <label class="form-check-label small text-danger" for="hapusTerkaitCheck">
                                Hapus juga tugas ini dari kelas lain? (Jika ada duplikat)
                            </label>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function confirmDelete(url) {
            document.getElementById('deleteForm').action = url;
            document.getElementById('hapusTerkaitCheck').checked = false;
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
    </script>
    @endpush
@endsection
