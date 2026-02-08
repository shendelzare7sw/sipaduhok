@extends('layouts.lms-guru')

@section('title', 'Kelola Soal Ujian')
@section('page-title', 'Kelola Soal: ' . $ujian->judul_ujian)
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('guru.lms.ujian.index', [$kelas->id, $mapel->id]) }}" class="btn btn-outline-secondary mb-2">
                <i class="fas fa-arrow-left me-1"></i>Kembali ke Daftar Ujian
            </a>
            <h4 class="mb-0">
                <i class="fas fa-list-ol me-2"></i>Daftar Soal
                <span class="badge bg-primary ms-2">{{ $soalList->count() }} Soal</span>
            </h4>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('guru.lms.ujian.soal.create', [$kelas->id, $mapel->id, $ujian->id]) }}"
                class="btn btn-success">
                <i class="fas fa-plus-circle me-1"></i>Tambah Soal Baru
            </a>
            <!-- Optional: Button for importing questions -->
        </div>
    </div>

    @if($soalList->count() > 0)
        <div class="card-custom">
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="15%">Tipe Soal</th>
                                <th width="45%">Pertanyaan</th>
                                <th width="10%" class="text-center">Bobot</th>
                                <th width="25%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($soalList as $index => $soal)
                                            <tr>
                                                <td class="text-center fw-bold">{{ $soal->urutan }}</td>
                                                <td>
                                                    <span class="badge {{ match ($soal->tipe_soal) {
                                    'pilihan_ganda' => 'bg-primary',
                                    'pilihan_ganda_kompleks' => 'bg-info text-dark',
                                    'benar_salah' => 'bg-warning text-dark',
                                    'isian_singkat' => 'bg-success',
                                    'uraian' => 'bg-secondary',
                                    default => 'bg-light text-dark'
                                } }}">
                                                        {{ ucwords(str_replace('_', ' ', $soal->tipe_soal)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="text-truncate" style="max-width: 400px;">
                                                        {{ strip_tags($soal->pertanyaan) }}
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ $soal->bobot }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <a href="{{ route('guru.lms.ujian.soal.edit', [$kelas->id, $mapel->id, $ujian->id, $soal->id]) }}"
                                                            class="btn btn-sm btn-warning" title="Edit Soal">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="{{ route('guru.lms.ujian.soal.edit', [$kelas->id, $mapel->id, $ujian->id, $soal->id]) }}"
                                                            class="btn btn-sm btn-warning" title="Edit Soal">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-danger" title="Hapus Soal"
                                                            onclick="confirmDelete('{{ route('guru.lms.ujian.soal.destroy', [$kelas->id, $mapel->id, $ujian->id, $soal->id]) }}')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card-custom text-center py-5">
            <i class="fas fa-clipboard-list text-muted" style="font-size: 64px; opacity: 0.2;"></i>
            <h5 class="mt-3 text-muted">Belum ada soal ujian</h5>
            <p class="text-muted mb-4">Mulai tambahkan soal untuk ujian ini</p>
            <a href="{{ route('guru.lms.ujian.soal.create', [$kelas->id, $mapel->id, $ujian->id]) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Buat Soal Pertama
            </a>
        </div>
    @endif
    @endif

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus soal ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function confirmDelete(url) {
            document.getElementById('deleteForm').action = url;
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
    </script>
    @endpush
@endsection