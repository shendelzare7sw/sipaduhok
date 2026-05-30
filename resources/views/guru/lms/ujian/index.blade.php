@extends('layouts.lms-guru')

@section('title', $tipeUjian === 'latihan' ? 'Daftar Latihan' : 'Daftar Ujian')
@section('page-title', $tipeUjian === 'latihan' ? 'Latihan' : 'Ujian')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-file-alt me-2"></i>Daftar {{ $tipeUjian === 'latihan' ? 'Latihan' : 'Ujian' }}</h4>
        <a href="{{ $tipeUjian === 'latihan' ? route('guru.lms.latihan.create', [$kelas->id, $mapel->id]) : route('guru.lms.ujian.create', [$kelas->id, $mapel->id]) }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i>Buat {{ $tipeUjian === 'latihan' ? 'Latihan' : 'Ujian' }}
        </a>
    </div>

    @if($ujianList->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th>Judul Ujian</th>
                        <th class="text-center">Tipe</th>
                        <th class="text-center">Tanggal Mulai</th>
                        <th class="text-center">Tanggal Selesai</th>
                        <th class="text-center">Durasi</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ujianList as $index => $ujian)
                        <tr>
                            <td class="text-center">{{ ($ujianList->currentPage() - 1) * $ujianList->perPage() + $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $ujian->judul_ujian }}</div>
                                @if($ujian->deskripsi)
                                    <small class="text-muted">{{ Str::limit($ujian->deskripsi, 80) }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $tipeBadge = match ($ujian->tipe_ujian) {
                                        'ulangan_harian' => ['bg-info', 'UH'],
                                        'latihan' => ['bg-success', 'Latihan'],
                                        'uts', 'pts_ganjil', 'pts_genap' => ['bg-warning', 'PTS'],
                                        'uas', 'pas_ganjil', 'pas_genap' => ['bg-danger', 'PAS'],
                                        'to_1' => ['bg-purple', 'TO 1'],
                                        'to_2' => ['bg-purple', 'TO 2'],
                                        'to_3' => ['bg-purple', 'TO 3'],
                                        'upk' => ['bg-dark', 'UPK'],
                                        'ujian_praktek' => ['bg-dark', 'Praktek'],
                                        default => ['bg-secondary', strtoupper(str_replace('_', ' ', $ujian->tipe_ujian))]
                                    };
                                @endphp
                                <span class="badge {{ $tipeBadge[0] }}">{{ $tipeBadge[1] }}</span>
                            </td>
                            <td class="text-center small">
                                {{ $ujian->tanggal_mulai->format('d/m/Y H:i') }}
                            </td>
                            <td class="text-center small">
                                {{ $ujian->tanggal_selesai->format('d/m/Y H:i') }}
                            </td>
                            <td class="text-center">
                                <div class="mt-2">
                                    <span class="badge bg-secondary">{{ $ujian->durasi_menit == 0 ? 'Tanpa Batas' : $ujian->durasi_menit . ' menit' }}</span>
                                    <span class="badge bg-info text-dark">{{ $ujian->soal_ujian_count }} Soal</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route($tipeUjian === 'latihan' ? 'guru.lms.latihan.hasil' : 'guru.lms.ujian.hasil', [$kelas->id, $mapel->id, $ujian->id]) }}"
                                        class="btn btn-success btn-sm" title="Lihat Hasil">
                                        <i class="fas fa-chart-bar"></i>
                                    </a>
                                    @if($tipeUjian !== 'latihan')
                                        <a href="{{ route('guru.lms.ujian.pengawasan', [$kelas->id, $mapel->id, $ujian->id]) }}"
                                            class="btn btn-primary btn-sm" title="Pengawasan Realtime">
                                            <i class="fas fa-desktop"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route($tipeUjian === 'latihan' ? 'guru.lms.latihan.soal.manage' : 'guru.lms.ujian.soal.manage', [$kelas->id, $mapel->id, $ujian->id]) }}"
                                        class="btn btn-info btn-sm" title="Kelola Soal">
                                        <i class="fas fa-list-ol"></i>
                                    </a>
                                    <a href="{{ route($tipeUjian === 'latihan' ? 'guru.lms.latihan.edit' : 'guru.lms.ujian.edit', [$kelas->id, $mapel->id, $ujian->id]) }}"
                                        class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm" title="Hapus"
                                        onclick="confirmDelete('{{ route($tipeUjian === 'latihan' ? 'guru.lms.latihan.destroy' : 'guru.lms.ujian.destroy', [$kelas->id, $mapel->id, $ujian->id]) }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($ujianList->hasPages())
            <nav class="d-flex justify-content-center mt-4">
                {{ $ujianList->links() }}
            </nav>
        @endif
    @else
        <div class="alert alert-info text-center" role="alert">
            <i class="fas fa-inbox me-2"></i>
            <strong>{{ $tipeUjian === 'latihan' ? 'Tidak ada latihan' : 'Tidak ada ujian' }}</strong>
            <p class="mb-0 mt-2">Belum ada {{ $tipeUjian === 'latihan' ? 'latihan' : 'ujian' }} yang dibuat. <a
                    href="{{ $tipeUjian === 'latihan' ? route('guru.lms.latihan.create', [$kelas->id, $mapel->id]) : route('guru.lms.ujian.create', [$kelas->id, $mapel->id]) }}">Buat {{ $tipeUjian === 'latihan' ? 'latihan' : 'ujian' }} sekarang</a></p>
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
                    Apakah Anda yakin ingin menghapus {{ $tipeUjian === 'latihan' ? 'latihan' : 'ujian' }} ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <div class="form-check mb-3 text-start">
                            <input class="form-check-input" type="checkbox" name="hapus_terkait" value="1" id="hapusTerkaitCheck">
                            <label class="form-check-label small text-danger" for="hapusTerkaitCheck">
                                Hapus juga {{ $tipeUjian === 'latihan' ? 'latihan' : 'ujian' }} ini dari kelas lain? (Jika ada duplikat)
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
