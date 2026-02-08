@extends('layouts.lms-guru')

@section('title', 'Edit Meeting')
@section('page-title', 'Kelas Virtual (Meeting)')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    <div class="row justify-content-center">
        <!-- Content -->
        <div class="col-md-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('guru.lms.meeting.index', [$kelas->id, $mapel->id]) }}"
                            class="btn btn-sm btn-outline-secondary me-3">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h5 class="mb-0 fw-bold">Edit Jadwal Meeting</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('guru.lms.meeting.update', [$kelas->id, $mapel->id, $meeting->id]) }}"
                        method="POST">
                        @csrf @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Judul Pertemuan <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror"
                                value="{{ old('judul', $meeting->judul) }}" required>
                            @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Platform <span class="text-danger">*</span></label>
                                <select name="platform" class="form-select @error('platform') is-invalid @enderror"
                                    required>
                                    <option value="google_meet" {{ old('platform', $meeting->platform) == 'google_meet' ? 'selected' : '' }}>Google Meet</option>
                                    <option value="zoom" {{ old('platform', $meeting->platform) == 'zoom' ? 'selected' : '' }}>Zoom Meeting</option>
                                    <option value="lainnya" {{ old('platform', $meeting->platform) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('platform') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Link Meeting <span class="text-danger">*</span></label>
                                <input type="url" name="link_meeting"
                                    class="form-control @error('link_meeting') is-invalid @enderror"
                                    value="{{ old('link_meeting', $meeting->link_meeting) }}" required>
                                @error('link_meeting') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="waktu_mulai"
                                    class="form-control @error('waktu_mulai') is-invalid @enderror"
                                    value="{{ old('waktu_mulai', $meeting->waktu_mulai->format('Y-m-d\TH:i')) }}" required>
                                @error('waktu_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Waktu Selesai (Estimasi)</label>
                                <input type="datetime-local" name="waktu_selesai"
                                    class="form-control @error('waktu_selesai') is-invalid @enderror"
                                    value="{{ old('waktu_selesai', $meeting->waktu_selesai ? $meeting->waktu_selesai->format('Y-m-d\TH:i') : '') }}">
                                @error('waktu_selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi / Catatan Tambahan</label>
                            <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror"
                                rows="4">{{ old('deskripsi', $meeting->deskripsi) }}</textarea>
                            @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4 form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1" {{ old('is_active', $meeting->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">Status Aktif (Tampilkan ke Siswa)</label>
                        </div>

                        @include('guru.partials.multi-kelas-selector')

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('guru.lms.meeting.index', [$kelas->id, $mapel->id]) }}"
                                class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection