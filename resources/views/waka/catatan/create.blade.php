@extends('layouts.sneat')

@section('title', 'Kirim Catatan Baru')
@section('page-title', 'Kirim Catatan Baru')
@section('page-subtitle', 'Kirim catatan atau teguran kepada tenaga pendidik dan siswa')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-edit text-primary me-2"></i>
                Form Kirim Catatan
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('waka.catatan.store') }}" method="POST">
                @csrf

                <!-- Judul -->
                <div class="mb-3">
                    <label for="judul" class="form-label">Judul Catatan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('judul') is-invalid @enderror"
                           id="judul" name="judul" value="{{ old('judul') }}" required>
                    @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Isi Catatan -->
                <div class="mb-3">
                    <label for="isi_catatan" class="form-label">Isi Catatan <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('isi_catatan') is-invalid @enderror"
                              id="isi_catatan" name="isi_catatan" rows="6" required>{{ old('isi_catatan') }}</textarea>
                    @error('isi_catatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Tipe Penerima -->
                <div class="mb-3">
                    <label for="tipe_penerima" class="form-label">Tipe Penerima <span class="text-danger">*</span></label>
                    <select class="form-select @error('tipe_penerima') is-invalid @enderror"
                            id="tipe_penerima" name="tipe_penerima" required>
                        <option value="">-- Pilih Tipe Penerima --</option>
                        <option value="semua" {{ old('tipe_penerima') == 'semua' ? 'selected' : '' }}>Semua (Wali Kelas, Guru, Siswa)</option>
                        <option value="role" {{ old('tipe_penerima') == 'role' ? 'selected' : '' }}>Berdasarkan Role</option>
                        <option value="individu" {{ old('tipe_penerima') == 'individu' ? 'selected' : '' }}>Individu Tertentu</option>
                    </select>
                    @error('tipe_penerima')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Role Penerima (conditional) -->
                <div class="mb-3" id="role_penerima_group" style="display: none;">
                    <label for="role_penerima" class="form-label">Pilih Role</label>
                    <select class="form-select @error('role_penerima') is-invalid @enderror"
                            id="role_penerima" name="role_penerima">
                        <option value="">-- Pilih Role --</option>
                        @foreach($roles as $key => $label)
                            <option value="{{ $key }}" {{ old('role_penerima') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_penerima')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Penerima Individu (conditional) -->
                <div class="mb-3" id="penerima_individu_group" style="display: none;">
                    <label for="penerima_id" class="form-label">Pilih Penerima</label>
                    <select class="form-select @error('penerima_id') is-invalid @enderror"
                            id="penerima_id" name="penerima_id">
                        <option value="">-- Pilih Penerima --</option>
                        <optgroup label="Tenaga Pendidik">
                            @foreach($tenagaPendidik as $guru)
                                <option value="{{ $guru->user_id }}" {{ old('penerima_id') == $guru->user_id ? 'selected' : '' }}>
                                    {{ $guru->nama_lengkap }} ({{ ucwords(str_replace('_', ' ', $guru->user->role)) }})
                                </option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Siswa">
                            @foreach($siswaList as $siswa)
                                <option value="{{ $siswa->user_id }}" {{ old('penerima_id') == $siswa->user_id ? 'selected' : '' }}>
                                    {{ $siswa->nama_lengkap }} - {{ $siswa->kelas->nama_kelas ?? '' }}
                                </option>
                            @endforeach
                        </optgroup>
                    </select>
                    @error('penerima_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Prioritas -->
                <div class="mb-3">
                    <label for="prioritas" class="form-label">Prioritas <span class="text-danger">*</span></label>
                    <select class="form-select @error('prioritas') is-invalid @enderror"
                            id="prioritas" name="prioritas" required>
                        <option value="">-- Pilih Prioritas --</option>
                        <option value="biasa" {{ old('prioritas') == 'biasa' ? 'selected' : '' }}>Biasa</option>
                        <option value="penting" {{ old('prioritas') == 'penting' ? 'selected' : '' }}>Penting</option>
                        <option value="mendesak" {{ old('prioritas') == 'mendesak' ? 'selected' : '' }}>Mendesak</option>
                    </select>
                    @error('prioritas')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i> Kirim Catatan
                    </button>
                    <a href="{{ route('waka.catatan.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipePenerimaSelect = document.getElementById('tipe_penerima');
    const rolePenerimaGroup = document.getElementById('role_penerima_group');
    const penerimaIndividuGroup = document.getElementById('penerima_individu_group');

    tipePenerimaSelect.addEventListener('change', function() {
        const value = this.value;

        // Hide all conditional fields
        rolePenerimaGroup.style.display = 'none';
        penerimaIndividuGroup.style.display = 'none';

        // Show relevant field
        if (value === 'role') {
            rolePenerimaGroup.style.display = 'block';
        } else if (value === 'individu') {
            penerimaIndividuGroup.style.display = 'block';
        }
    });

    // Trigger on page load if old value exists
    if (tipePenerimaSelect.value) {
        tipePenerimaSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection
