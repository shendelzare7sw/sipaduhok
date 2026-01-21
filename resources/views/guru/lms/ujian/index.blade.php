@extends('layouts.lms-guru')

@section('title', $tipeUjian === 'kuis' ? 'Daftar Kuis' : 'Daftar Ujian')
@section('page-title', $tipeUjian === 'kuis' ? 'Kuis' : 'Ujian')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-file-alt me-2"></i>Daftar {{ $tipeUjian === 'kuis' ? 'Kuis' : 'Ujian' }}</h4>
        <a href="{{ $tipeUjian === 'kuis' ? route('guru.lms.kuis.create', [$kelas->id, $mapel->id]) : route('guru.lms.ujian.create', [$kelas->id, $mapel->id]) }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i>Buat {{ $tipeUjian === 'kuis' ? 'Kuis' : 'Ujian' }}
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
                        <th class="text-center" style="width: 120px;">Aksi</th>
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
                                        'kuis' => ['bg-success', 'Kuis'],
                                        'uts', 'pts_ganjil', 'pts_genap' => ['bg-warning', 'PTS'],
                                        'uas', 'pas_ganjil', 'pas_genap' => ['bg-danger', 'PAS'],
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
                                <span class="badge bg-secondary">{{ $ujian->durasi_menit }} menit</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('guru.lms.ujian.hasil', [$kelas->id, $mapel->id, $ujian->id]) }}"
                                        class="btn btn-success btn-sm" title="Lihat Hasil">
                                        <i class="fas fa-chart-bar"></i>
                                    </a>
                                    <a href="{{ route('guru.lms.ujian.soal.manage', [$kelas->id, $mapel->id, $ujian->id]) }}"
                                        class="btn btn-info btn-sm" title="Kelola Soal">
                                        <i class="fas fa-list-ol"></i>
                                    </a>
                                    <a href="{{ route('guru.lms.ujian.edit', [$kelas->id, $mapel->id, $ujian->id]) }}"
                                        class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('guru.lms.ujian.destroy', [$kelas->id, $mapel->id, $ujian->id]) }}"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus ujian ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
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
            <strong>{{ $tipeUjian === 'kuis' ? 'Tidak ada kuis' : 'Tidak ada ujian' }}</strong>
            <p class="mb-0 mt-2">Belum ada {{ $tipeUjian === 'kuis' ? 'kuis' : 'ujian' }} yang dibuat. <a
                    href="{{ $tipeUjian === 'kuis' ? route('guru.lms.kuis.create', [$kelas->id, $mapel->id]) : route('guru.lms.ujian.create', [$kelas->id, $mapel->id]) }}">Buat {{ $tipeUjian === 'kuis' ? 'kuis' : 'ujian' }} sekarang</a></p>
        </div>
    @endif
@endsection
