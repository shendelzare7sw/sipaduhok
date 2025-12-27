@extends('layouts.lms-guru')

@section('title', 'Daftar Materi')
@section('page-title', 'Materi Pembelajaran')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-book me-2"></i>Daftar Materi</h4>
        <a href="{{ route('guru.lms.materi.create', [$kelas->id, $mapel->id]) }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i>Tambah Materi
        </a>
    </div>

    @if($materiList->count() > 0)
        <div class="row g-3">
            @foreach($materiList as $materi)
            <div class="col-md-6 col-lg-4">
                <div class="card-custom h-100">
                    <div class="p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-info">{{ strtoupper($materi->tipe_file) }}</span>
                            <small class="text-muted">{{ $materi->tanggal_upload->format('d M Y') }}</small>
                        </div>
                        <h6 class="fw-bold">{{ $materi->judul_materi }}</h6>
                        <p class="text-muted small">{{ Str::limit($materi->deskripsi, 100) }}</p>
                        <div class="d-flex gap-2 mt-3">
                            <a href="{{ route('guru.lms.materi.edit', [$kelas->id, $mapel->id, $materi->id]) }}" 
                               class="btn btn-sm btn-warning flex-fill">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('guru.lms.materi.destroy', [$kelas->id, $mapel->id, $materi->id]) }}" 
                                  method="POST" class="flex-fill" onsubmit="return confirm('Yakin hapus materi ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger w-100">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $materiList->links() }}
        </div>
    @else
        <div class="card-custom text-center py-5">
            <i class="fas fa-inbox text-muted" style="font-size: 64px; opacity: 0.2;"></i>
            <p class="text-muted mt-3">Belum ada materi. Klik tombol "Tambah Materi" untuk memulai.</p>
        </div>
    @endif
@endsection