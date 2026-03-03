@extends('layouts.lms-guru')

@section('title', 'Kelas Virtual')
@section('page-title', 'Kelas Virtual (Meeting)')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold">Kelas Virtual (Meeting)</h5>
                <small class="text-muted">{{ $mapel->nama_mapel }} - Kelas {{ $kelas->nama_kelas }}</small>
            </div>
            <a href="{{ route('guru.lms.meeting.create', [$kelas->id, $mapel->id]) }}"
                class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-2"></i>Jadwalkan Meeting
            </a>
        </div>
        <div class="card-body">
            @forelse($meetings as $meeting)
                <div class="card mb-3 border {{ $meeting->is_active ? 'border-primary' : 'border-light bg-light' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title fw-bold mb-1">
                                    {{ $meeting->judul }}
                                    @if($meeting->is_active)
                                        <span class="badge bg-success ms-2" style="font-size: 0.7em;">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary ms-2"
                                            style="font-size: 0.7em;">Selesai/Non-aktif</span>
                                    @endif
                                </h5>
                                <div class="mb-2">
                                    <span class="badge bg-info text-dark me-2">
                                        <i class="fas fa-video me-1"></i>
                                        {{ ucfirst(str_replace('_', ' ', $meeting->platform)) }}
                                    </span>
                                    <small class="text-muted">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        {{ $meeting->waktu_mulai->translatedFormat('l, d F Y') }}
                                        <i class="far fa-clock ms-3 me-1"></i>
                                        {{ $meeting->waktu_mulai->format('H:i') }} -
                                        {{ $meeting->waktu_selesai ? $meeting->waktu_selesai->format('H:i') : 'Selesai' }}
                                    </small>
                                </div>
                                @if($meeting->deskripsi)
                                    <p class="card-text text-muted small mb-3">{{ $meeting->deskripsi }}</p>
                                @endif

                                <div class="d-flex gap-2">
                                    <a href="{{ $meeting->link_meeting }}" target="_blank"
                                        class="btn btn-primary btn-sm">
                                        <i class="fas fa-external-link-alt me-1"></i> Mulai Meeting
                                    </a>
                                    <button class="btn btn-outline-secondary btn-sm"
                                        onclick="copyLink('{{ $meeting->link_meeting }}')">
                                        <i class="far fa-copy me-1"></i> Copy Link
                                    </button>
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('guru.lms.meeting.edit', [$kelas->id, $mapel->id, $meeting->id]) }}">
                                            <i class="fas fa-edit me-2 text-warning"></i> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <button class="dropdown-item text-danger" type="button"
                                            onclick="confirmDelete('{{ route('guru.lms.meeting.destroy', [$kelas->id, $mapel->id, $meeting->id]) }}')">
                                            <i class="fas fa-trash-alt me-2"></i> Hapus
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/online-meeting-4450216-3726715.png"
                        alt="Empty" style="width: 150px; opacity: 0.5;">
                    <p class="text-muted mt-3">Belum ada jadwal meeting/kelas virtual.</p>
                    <a href="{{ route('guru.lms.meeting.create', [$kelas->id, $mapel->id]) }}"
                        class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Buat Jadwal Baru
                    </a>
                </div>
            @endforelse

            <div class="mt-3">
                {{ $meetings->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function copyLink(link) {
            navigator.clipboard.writeText(link).then(() => {
                // You might want to use a toast or sweetalert here normally
                alert('Link meeting berhasil disalin!');
            });
        }

        function confirmDelete(url) {
            document.getElementById('deleteForm').action = url;
            document.getElementById('hapusTerkaitCheck').checked = false;
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
    </script>
    @endpush

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus meeting ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <div class="form-check mb-3 text-start">
                            <input class="form-check-input" type="checkbox" name="hapus_terkait" value="1" id="hapusTerkaitCheck">
                            <label class="form-check-label small text-danger" for="hapusTerkaitCheck">
                                Hapus juga meeting ini dari kelas lain? (Jika ada duplikat)
                            </label>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection