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
                        <th class="text-center">Submitted</th>
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
                                <form action="{{ route('guru.lms.tugas.destroy', [$kelas->id, $mapel->id, $tugas->id]) }}" 
                                      method="POST" style="display: inline;"
                                      onsubmit="return confirm('Yakin hapus tugas ini?')">
                                    @csrf @method('DELETE')
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

        <div class="mt-3">
            {{ $tugasList->links() }}
        </div>
    @else
        <div class="card-custom text-center py-5">
            <i class="fas fa-inbox text-muted" style="font-size: 64px; opacity: 0.2;"></i>
            <p class="text-muted mt-3">Belum ada tugas. Klik "Buat Tugas Baru" untuk memulai.</p>
        </div>
    @endif
@endsection