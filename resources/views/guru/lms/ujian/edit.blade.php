@extends('layouts.lms-guru')

@section('title', $tipeUjian === 'kuis' ? 'Edit Kuis' : 'Edit Ujian')
@section('page-title', $tipeUjian === 'kuis' ? 'Edit Kuis' : 'Edit Ujian')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    <div class="mb-3">
        <a href="{{ $tipeUjian === 'kuis' ? route('guru.lms.kuis.index', [$kelas->id, $mapel->id]) : route('guru.lms.ujian.index', [$kelas->id, $mapel->id]) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <div class="card-custom">
        <div class="card-header-custom">
            <i class="fas fa-edit me-2"></i>Edit {{ $tipeUjian === 'kuis' ? 'Kuis' : 'Ujian' }}
        </div>
        <div class="p-4">
            <form action="{{ $tipeUjian === 'kuis' ? route('guru.lms.kuis.update', [$kelas->id, $mapel->id, $ujian->id]) : route('guru.lms.ujian.update', [$kelas->id, $mapel->id, $ujian->id]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Judul {{ $tipeUjian === 'kuis' ? 'Kuis' : 'Ujian' }} <span class="text-danger">*</span></label>
                    <input type="text" name="judul_ujian" class="form-control"
                        value="{{ old('judul_ujian', $ujian->judul_ujian) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control"
                        rows="3">{{ old('deskripsi', $ujian->deskripsi) }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tipe <span class="text-danger">*</span></label>
                        <select name="tipe_ujian" class="form-control" @if($tipeUjian === 'kuis') disabled @endif required>
                            @if($tipeUjian === 'kuis')
                                <option value="kuis" selected>Kuis</option>
                            @else
                                <optgroup label="Tugas & Latihan">
                                    <option value="ulangan_harian" {{ $ujian->tipe_ujian == 'ulangan_harian' ? 'selected' : '' }}>
                                        Ulangan Harian</option>
                                </optgroup>
                                <optgroup label="Semester Ganjil">
                                    <option value="pts_ganjil" {{ $ujian->tipe_ujian == 'pts_ganjil' ? 'selected' : '' }}>PTS
                                        Ganjil</option>
                                    <option value="pas_ganjil" {{ $ujian->tipe_ujian == 'pas_ganjil' ? 'selected' : '' }}>PAS
                                        Ganjil</option>
                                </optgroup>
                                <optgroup label="Semester Genap">
                                    <option value="pts_genap" {{ $ujian->tipe_ujian == 'pts_genap' ? 'selected' : '' }}>PTS Genap
                                    </option>
                                    <option value="pas_genap" {{ $ujian->tipe_ujian == 'pas_genap' ? 'selected' : '' }}>PAS Genap
                                    </option>
                                </optgroup>
                            @endif
                        </select>
                        @if($tipeUjian === 'kuis')
                            <input type="hidden" name="tipe_ujian" value="kuis">
                        @endif
                        <small class="text-muted">PTS/PAS wajib memiliki 4 tipe soal</small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="tanggal_mulai" class="form-control"
                            value="{{ old('tanggal_mulai', $ujian->tanggal_mulai->format('Y-m-d\TH:i')) }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="tanggal_selesai" class="form-control"
                            value="{{ old('tanggal_selesai', $ujian->tanggal_selesai->format('Y-m-d\TH:i')) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Durasi {{ $tipeUjian === 'kuis' ? 'Kuis' : 'Ujian' }} (menit)</label>
                    <input type="number" name="durasi_menit" class="form-control"
                        value="{{ old('durasi_menit', $ujian->durasi_menit) }}" min="0">
                    <small class="text-muted">Ketik 0 untuk durasi tanpa batas</small>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Update {{ $tipeUjian === 'kuis' ? 'Kuis' : 'Ujian' }}
                    </button>
                    <a href="{{ $tipeUjian === 'kuis' ? route('guru.lms.kuis.index', [$kelas->id, $mapel->id]) : route('guru.lms.ujian.index', [$kelas->id, $mapel->id]) }}"
                        class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
