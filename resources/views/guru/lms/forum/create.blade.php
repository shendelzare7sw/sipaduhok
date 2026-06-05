@extends('layouts.lms-guru')

@section('title', 'Buat Diskusi Baru')
@section('page-title', 'Buat Diskusi Baru')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    <div class="mb-3">
        @if(!empty($pertemuanId))
            <a href="{{ route('guru.lms.meeting.index', [$kelas->id, $mapel->id]) }}"
                class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Kembali ke Meeting
            </a>
        @else
            <a href="{{ route('guru.lms.forum.index', [$kelas->id, $mapel->id]) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        @endif
    </div>

    <div class="card-custom">
        <div class="card-header-custom">
            <i class="fas fa-plus-circle me-2"></i>Form Diskusi Baru
        </div>
        <div class="p-4">
            <form action="{{ route('guru.lms.forum.store', [$kelas->id, $mapel->id]) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Topik Diskusi <span class="text-danger">*</span></label>
                    <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror"
                        value="{{ old('judul') }}" placeholder="Contoh: Diskusi Materi Aljabar" required>
                    @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Isi Diskusi / Pertanyaan Pemicu <span class="text-danger">*</span></label>
                    <textarea name="isi" class="form-control @error('isi') is-invalid @enderror" rows="6"
                        placeholder="Tuliskan materi diskusi atau pertanyaan pemantik disini..."
                        required>{{ old('isi') }}</textarea>
                    @error('isi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_pinned" value="1" id="is_pinned" {{ old('is_pinned') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_pinned">
                            Sematkan Diskusi (Pin)
                        </label>
                        <div class="form-text">Diskusi yang disematkan akan muncul paling atas.</div>
                    </div>
                </div>

                @include('guru.partials.multi-kelas-selector')

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i>Mulai Diskusi
                    </button>
                    <a href="{{ !empty($pertemuanId) ? route('guru.lms.meeting.index', [$kelas->id, $mapel->id]) : route('guru.lms.forum.index', [$kelas->id, $mapel->id]) }}"
                        class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
