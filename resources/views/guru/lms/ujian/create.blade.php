@extends('layouts.lms-guru')

@section('title', $tipeUjian === 'latihan' ? 'Buat Latihan' : 'Buat Ujian')
@section('page-title', $tipeUjian === 'latihan' ? 'Buat Latihan Baru' : 'Buat Ujian Baru')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    <div class="mb-3">
        @if($tipeUjian === 'latihan')
            <a href="{{ route('guru.lms.latihan.index', [$kelas->id, $mapel->id]) }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Kembali
                </a>
            @else
                <a href="{{ route('guru.lms.ujian.index', [$kelas->id, $mapel->id]) }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Kembali
                </a>
            @endif
    </div>

    <div class="card-custom">
        <div class="card-header-custom">
            <i class="fas fa-plus-circle me-2"></i>Form Buat {{ $tipeUjian === 'latihan' ? 'Latihan' : 'Ujian' }}
        </div>
        <div class="p-4">
            <form action="{{ $tipeUjian === 'latihan' ? route('guru.lms.latihan.store', [$kelas->id, $mapel->id]) : route('guru.lms.ujian.store', [$kelas->id, $mapel->id]) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Judul {{ $tipeUjian === 'latihan' ? 'Latihan' : 'Ujian' }} <span class="text-danger">*</span></label>
                    <input type="text" name="judul_ujian" class="form-control @error('judul_ujian') is-invalid @enderror"
                        value="{{ old('judul_ujian') }}" required>
                    @error('judul_ujian')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tipe <span class="text-danger">*</span></label>
                        <select name="tipe_ujian" class="form-control @error('tipe_ujian') is-invalid @enderror" required @if($tipeUjian === 'latihan') disabled @endif>
                            @if($tipeUjian === 'latihan')
                                <option value="latihan" selected>Latihan</option>
                            @else
                                <option value="">-- Pilih Tipe --</option>
                                <optgroup label="Ulangan">
                                    <option value="ulangan_harian" {{ old('tipe_ujian') == 'ulangan_harian' ? 'selected' : '' }}>
                                        Ulangan Harian</option>
                                </optgroup>
                                <optgroup label="Semester Ganjil">
                                    <option value="pts_ganjil" {{ old('tipe_ujian') == 'pts_ganjil' ? 'selected' : '' }}>PTS
                                        Ganjil</option>
                                    <option value="pas_ganjil" {{ old('tipe_ujian') == 'pas_ganjil' ? 'selected' : '' }}>PAS
                                        Ganjil</option>
                                </optgroup>
                                <optgroup label="Semester Genap">
                                    <option value="pts_genap" {{ old('tipe_ujian') == 'pts_genap' ? 'selected' : '' }}>PTS Genap
                                    </option>
                                    <option value="pas_genap" {{ old('tipe_ujian') == 'pas_genap' ? 'selected' : '' }}>PAS Genap
                                    </option>
                                </optgroup>
                                @if($isTingkatAkhir)
                                <optgroup label="Ujian Kelulusan">
                                    <option value="to_1" {{ old('tipe_ujian') == 'to_1' ? 'selected' : '' }}>Try Out 1</option>
                                    <option value="to_2" {{ old('tipe_ujian') == 'to_2' ? 'selected' : '' }}>Try Out 2</option>
                                    <option value="to_3" {{ old('tipe_ujian') == 'to_3' ? 'selected' : '' }}>Try Out 3</option>
                                    <option value="upk" {{ old('tipe_ujian') == 'upk' ? 'selected' : '' }}>UPK</option>
                                    <option value="ujian_praktek" {{ old('tipe_ujian') == 'ujian_praktek' ? 'selected' : '' }}>Ujian Praktek</option>
                                </optgroup>
                                @endif
                            @endif
                        </select>
                        @if($tipeUjian === 'latihan')
                            <input type="hidden" name="tipe_ujian" value="latihan">
                        @endif
                        <small class="text-muted">PTS/PAS wajib memiliki 4 tipe soal: Pilihan Ganda, Benar/Salah, Isian,
                            Uraian</small>
                        @error('tipe_ujian')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="tanggal_mulai"
                            class="form-control @error('tanggal_mulai') is-invalid @enderror"
                            value="{{ old('tanggal_mulai') }}" required>
                        @error('tanggal_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="tanggal_selesai"
                            class="form-control @error('tanggal_selesai') is-invalid @enderror"
                            value="{{ old('tanggal_selesai') }}" required>
                        @error('tanggal_selesai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Durasi {{ $tipeUjian === 'latihan' ? 'Latihan' : 'Ujian' }} (menit) <span class="text-danger">*</span></label>
                    <input type="number" name="durasi_menit"
                        class="form-control @error('durasi_menit') is-invalid @enderror"
                        value="{{ old('durasi_menit', 90) }}" min="0" required>
                    <small class="text-muted">Isi 0 untuk waktu tidak terbatas.</small>
                    @error('durasi_menit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Catatan:</strong> Setelah {{ $tipeUjian === 'latihan' ? 'latihan' : 'ujian' }} dibuat, Anda dapat menambahkan soal di halaman edit.
                </div>

                @include('guru.partials.multi-kelas-selector')

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Buat {{ $tipeUjian === 'latihan' ? 'Latihan' : 'Ujian' }}
                    </button>
                    <a href="{{ $tipeUjian === 'latihan' ? route('guru.lms.latihan.index', [$kelas->id, $mapel->id]) : route('guru.lms.ujian.index', [$kelas->id, $mapel->id]) }}"
                        class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
