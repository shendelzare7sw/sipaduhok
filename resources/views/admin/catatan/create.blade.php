@extends('layouts.sneat')

@section('title', 'Kirim Catatan Baru')
@section('page-title', 'Kirim Catatan Baru')
@section('page-subtitle', 'Kirim catatan atau pesan kepada pengguna')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite('resources/css/admin/catatan/create.css')
@endsection

@section('content')
@php
    $routePrefix = 'admin';
    $recipientMode = 'multi';
    $selectedType = old('tipe_penerima');
@endphp

<div class="catatan-page">
    <div class="catatan-alert">
        <i class="fas fa-info-circle mt-1"></i>
        <div>Catatan akan tersimpan sebagai riwayat pengirim dan dikirim sebagai notifikasi kepada penerima yang dipilih.</div>
    </div>

    <form action="{{ route($routePrefix . '.catatan.store') }}" method="POST" data-catatan-create-form>
        @csrf

        <div class="catatan-form-card">
            <div class="catatan-form-header">
                <h5><i class="fas fa-edit text-primary me-2"></i>Form Kirim Catatan</h5>
                <a href="{{ route($routePrefix . '.catatan.index') }}" class="catatan-btn secondary">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
            </div>

            <div class="catatan-form-body">
                <div class="catatan-field">
                    <label for="judul">Judul Catatan <span class="catatan-required">*</span></label>
                    <input
                        type="text"
                        id="judul"
                        name="judul"
                        class="catatan-input @error('judul') is-invalid @enderror"
                        value="{{ old('judul') }}"
                        placeholder="Contoh: Evaluasi progres pembelajaran minggu ini"
                        required
                    >
                    @error('judul')
                        <span class="catatan-invalid">{{ $message }}</span>
                    @enderror
                    <div class="catatan-help">Gunakan judul singkat agar mudah dikenali penerima.</div>
                </div>

                <div class="catatan-field">
                    <label for="isi_catatan">Isi Catatan <span class="catatan-required">*</span></label>
                    <textarea
                        id="isi_catatan"
                        name="isi_catatan"
                        class="catatan-textarea @error('isi_catatan') is-invalid @enderror"
                        placeholder="Tulis catatan, instruksi, atau teguran dengan jelas..."
                        required
                    >{{ old('isi_catatan') }}</textarea>
                    @error('isi_catatan')
                        <span class="catatan-invalid">{{ $message }}</span>
                    @enderror
                </div>

                <div class="catatan-field">
                    <label>Kirim Kepada <span class="catatan-required">*</span></label>
                    <div class="catatan-recipient-grid">
                        <label class="catatan-option">
                            <input
                                type="radio"
                                name="tipe_penerima"
                                value="semua"
                                {{ $selectedType === 'semua' ? 'checked' : '' }}
                                required
                                data-catatan-recipient-option
                            >
                            <div>
                                <strong>Semua Pengguna</strong>
                                <span>{{ $recipientMode === 'single' ? 'Wali kelas, guru, dan siswa di cabang Anda.' : 'Semua role penerima yang tersedia.' }}</span>
                            </div>
                        </label>

                        <label class="catatan-option">
                            <input
                                type="radio"
                                name="tipe_penerima"
                                value="role"
                                {{ $selectedType === 'role' ? 'checked' : '' }}
                                data-catatan-recipient-option
                            >
                            <div>
                                <strong>Per Role</strong>
                                <span>Kirim ke satu kelompok role tertentu.</span>
                            </div>
                        </label>

                        <label class="catatan-option">
                            <input
                                type="radio"
                                name="tipe_penerima"
                                value="individu"
                                {{ $selectedType === 'individu' ? 'checked' : '' }}
                                data-catatan-recipient-option
                            >
                            <div>
                                <strong>Individu</strong>
                                <span>{{ $recipientMode === 'multi' ? 'Pilih satu atau lebih penerima.' : 'Pilih satu penerima spesifik.' }}</span>
                            </div>
                        </label>
                    </div>
                    @error('tipe_penerima')
                        <span class="catatan-invalid">{{ $message }}</span>
                    @enderror

                    <div class="catatan-conditional catatan-hidden" id="roleField">
                        <label for="role_penerima">Pilih Role <span class="catatan-required">*</span></label>
                        <select
                            id="role_penerima"
                            name="role_penerima"
                            class="catatan-select @error('role_penerima') is-invalid @enderror"
                        >
                            <option value="">Pilih role penerima</option>
                            @foreach($roles as $key => $label)
                                <option value="{{ $key }}" {{ old('role_penerima') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_penerima')
                            <span class="catatan-invalid">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="catatan-conditional catatan-hidden" id="individuField">
                        <label>{{ $recipientMode === 'multi' ? 'Pilih Penerima' : 'Penerima Individu' }} <span class="catatan-required">*</span></label>

                        @if($recipientMode === 'multi')
                            <div class="catatan-filter-panel">
                                <div class="catatan-filter-row">
                                    <div>
                                        <label for="filterRole">Role</label>
                                        <select id="filterRole" class="catatan-select">
                                            <option value="">Semua Role</option>
                                            @foreach($roles as $roleKey => $roleLabel)
                                                <option value="{{ $roleKey }}">{{ $roleLabel }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="filterCabang">Cabang</label>
                                        <select id="filterCabang" class="catatan-select">
                                            <option value="">Semua Cabang</option>
                                            @foreach(($cabangList ?? collect()) as $cabang)
                                                <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="filterSearch">Cari</label>
                                        <input type="text" id="filterSearch" class="catatan-input" placeholder="Cari nama penerima...">
                                    </div>
                                </div>

                                <div class="catatan-filter-actions">
                                    <button type="button" class="catatan-btn secondary" id="selectAllBtn">
                                        <i class="fas fa-check-square"></i> Pilih Semua
                                    </button>
                                    <button type="button" class="catatan-btn danger" id="clearAllBtn">
                                        <i class="fas fa-square"></i> Hapus Semua
                                    </button>
                                    <span class="catatan-count-chip" id="selectedCount">0 dipilih</span>
                                </div>

                                <div class="catatan-user-list" id="userChecklist"></div>
                            </div>

                            @error('penerima_ids')
                                <span class="catatan-invalid">{{ $message }}</span>
                            @enderror
                            @error('penerima_ids.*')
                                <span class="catatan-invalid">{{ $message }}</span>
                            @enderror
                            <div class="catatan-help">Filter hanya menyaring tampilan. Penerima yang sudah dicentang tetap tersimpan.</div>
                        @else
                            <select
                                id="penerima_id"
                                name="penerima_id"
                                class="catatan-select @error('penerima_id') is-invalid @enderror"
                            >
                                <option value="">Pilih penerima</option>
                                <optgroup label="Tenaga Pendidik">
                                    @foreach(($tenagaPendidik ?? collect()) as $guru)
                                        <option value="{{ $guru->user_id }}" {{ (string) old('penerima_id') === (string) $guru->user_id ? 'selected' : '' }}>
                                            {{ $guru->nama_lengkap }} ({{ ucwords(str_replace('_', ' ', $guru->user->role ?? '')) }})
                                        </option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Siswa">
                                    @foreach(($siswaList ?? collect()) as $siswa)
                                        <option value="{{ $siswa->user_id }}" {{ (string) old('penerima_id') === (string) $siswa->user_id ? 'selected' : '' }}>
                                            {{ $siswa->nama_lengkap }}{{ $siswa->kelas ? ' - ' . $siswa->kelas->nama_kelas : '' }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            </select>
                            @error('penerima_id')
                                <span class="catatan-invalid">{{ $message }}</span>
                            @enderror
                            <div class="catatan-help">Daftar penerima dibatasi sesuai cabang yang Anda kelola.</div>
                        @endif
                    </div>
                </div>

                <div class="catatan-field">
                    <label>Prioritas <span class="catatan-required">*</span></label>
                    <div class="catatan-priority-grid">
                        <label class="catatan-option">
                            <input type="radio" name="prioritas" value="biasa" {{ old('prioritas', 'biasa') === 'biasa' ? 'checked' : '' }} required>
                            <div>
                                <strong>Biasa</strong>
                                <span>Informasi rutin atau arahan umum.</span>
                            </div>
                        </label>
                        <label class="catatan-option">
                            <input type="radio" name="prioritas" value="penting" {{ old('prioritas') === 'penting' ? 'checked' : '' }}>
                            <div>
                                <strong>Penting</strong>
                                <span>Perlu diperhatikan dalam waktu dekat.</span>
                            </div>
                        </label>
                        <label class="catatan-option">
                            <input type="radio" name="prioritas" value="mendesak" {{ old('prioritas') === 'mendesak' ? 'checked' : '' }}>
                            <div>
                                <strong>Mendesak</strong>
                                <span>Butuh tindak lanjut secepatnya.</span>
                            </div>
                        </label>
                    </div>
                    @error('prioritas')
                        <span class="catatan-invalid">{{ $message }}</span>
                    @enderror
                </div>

                <div class="catatan-form-actions">
                    <button type="submit" class="catatan-btn primary">
                        <i class="fas fa-paper-plane"></i>
                        Kirim Catatan
                    </button>
                    <a href="{{ route($routePrefix . '.catatan.index') }}" class="catatan-btn secondary">
                        <i class="fas fa-times"></i>
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
    @php
        $catatanCreateConfig = [
            'recipientMode' => 'multi',
            'users' => $usersForIndividu ?? [],
            'selectedUserIds' => collect(old('penerima_ids', []))->map(fn ($id) => (string) $id)->values(),
        ];
    @endphp

    <template id="catatanCreateConfig" data-config="{{ e(json_encode($catatanCreateConfig)) }}"></template>
    @vite('resources/js/admin/catatan/create.js')
@endsection
