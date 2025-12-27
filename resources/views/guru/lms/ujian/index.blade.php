@extends('layouts.lms-guru')

@section('title', 'Daftar Ujian')
@section('page-title', 'Ujian')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-file-alt me-2"></i>Daftar Ujian</h4>
        <a href="{{ route('guru.lms.ujian.create', [$kelas->id, $mapel->id]) }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i>Buat Ujian
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
                            @if($ujian->tipe_ujian === 'harian')
                                <span class="badge bg-info">Harian</span>
                            @elseif($ujian->tipe_ujian === 'uts')
                                <span class="badge bg-warning">UTS</span>
                            @elseif($ujian->tipe_ujian === 'uas')
                                <span class="badge bg-danger">UAS</span>
                            @endif
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
            <strong>Tidak ada ujian</strong>
            <p class="mb-0 mt-2">Belum ada ujian yang dibuat. <a href="{{ route('guru.lms.ujian.create', [$kelas->id, $mapel->id]) }}">Buat ujian sekarang</a></p>
        </div>
    @endif
@endsection
