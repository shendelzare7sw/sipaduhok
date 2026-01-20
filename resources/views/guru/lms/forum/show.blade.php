@extends('layouts.lms-guru')

@section('title', 'Detail Diskusi')
@section('page-title', 'Detail Diskusi')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- Navigation --}}
            <a href="{{ route('guru.lms.forum.index', [$kelas->id, $mapel->id]) }}" class="btn btn-outline-secondary mb-4">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Diskusi
            </a>

            {{-- Main Thread --}}
            <div class="card-custom mb-4 border-primary border-top border-4">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-circle-md {{ $forum->isFromTeacher() ? 'bg-success' : 'bg-primary' }} text-white"
                                style="width: 48px; height: 48px; font-size: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                {{ substr($forum->user->name ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">
                                    {{ $forum->user->name ?? 'Unknown' }}
                                    @if($forum->isFromTeacher())
                                        <span class="badge bg-success ms-1">Guru</span>
                                    @endif
                                </h6>
                                <small class="text-muted">{{ $forum->created_at->format('d M Y, H:i') }}</small>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            @if($forum->is_pinned)
                                <span class="badge bg-warning text-dark"><i class="fas fa-thumbtack me-1"></i>Pinned</span>
                            @endif
                            @if($forum->is_closed)
                                <span class="badge bg-secondary"><i class="fas fa-lock me-1"></i>Closed</span>
                            @endif
                            <div class="dropdown">
                                <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <form
                                            action="{{ route('guru.lms.forum.pin', [$kelas->id, $mapel->id, $forum->id]) }}"
                                            method="POST">
                                            @csrf @method('PATCH')
                                            <button class="dropdown-item" type="submit">
                                                <i
                                                    class="fas fa-thumbtack me-2"></i>{{ $forum->is_pinned ? 'Unpin' : 'Pin' }}
                                                Diskusi
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form
                                            action="{{ route('guru.lms.forum.close', [$kelas->id, $mapel->id, $forum->id]) }}"
                                            method="POST">
                                            @csrf @method('PATCH')
                                            <button class="dropdown-item" type="submit">
                                                <i class="fas fa-lock me-2"></i>{{ $forum->is_closed ? 'Buka' : 'Tutup' }}
                                                Diskusi
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form
                                            action="{{ route('guru.lms.forum.destroy', [$kelas->id, $mapel->id, $forum->id]) }}"
                                            method="POST"
                                            onsubmit="return confirm('Hapus diskusi ini? Tindakan tidak dapat dibatalkan.')">
                                            @csrf @method('DELETE')
                                            <button class="dropdown-item text-danger" type="submit">
                                                <i class="fas fa-trash me-2"></i>Hapus Diskusi
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <h4 class="fw-bold mb-3">{{ $forum->judul }}</h4>
                    <div class="text-dark mb-4" style="font-size: 16px; line-height: 1.6; white-space: pre-wrap;">
                        {!! nl2br(e($forum->isi)) !!}
                    </div>

                    @if($forum->lampiran)
                        <div class="mb-4">
                            <a href="{{ Storage::url($forum->lampiran) }}" target="_blank" class="btn btn-sm btn-light border">
                                <i class="fas fa-paperclip me-1"></i> Lihat Lampiran
                            </a>
                        </div>
                    @endif

                    {{-- Reply Form for Teacher --}}
                    @if(!$forum->is_closed)
                        <div class="mt-4 p-3 bg-light rounded">
                            <h6 class="fw-bold mb-2"><i class="fas fa-reply me-2"></i>Balas sebagai Guru</h6>
                            <form action="{{ route('guru.lms.forum.reply', [$kelas->id, $mapel->id, $forum->id]) }}"
                                method="POST">
                                @csrf
                                <div class="mb-2">
                                    <textarea name="content" class="form-control" rows="3"
                                        placeholder="Tulis balasan untuk siswa..." required></textarea>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-1"></i> Kirim Balasan
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-secondary mb-0">
                            <i class="fas fa-lock me-2"></i>Diskusi ini telah ditutup. Komentar baru dinonaktifkan.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Replies List --}}
            <h5 class="mb-3 ps-2 border-start border-4 border-primary">Balasan ({{ $forum->replies->count() }})</h5>

            <div class="d-flex flex-column gap-3">
                @forelse($forum->replies->whereNull('parent_id') as $reply)
                    @include('guru.lms.forum._reply_item', ['reply' => $reply, 'level' => 0])
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="far fa-comment-dots mb-2" style="font-size: 32px;"></i>
                        <p class="mb-0">Belum ada balasan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection