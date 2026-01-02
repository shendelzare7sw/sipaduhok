@extends('layouts.sneat')

@section('title', 'Tambah Siswa')
@section('page-title', 'Tambah Siswa Baru')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
<style>
    .card { background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px; padding: 24px; }
    .form-title { font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0; }
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; margin-bottom: 6px; font-weight: 500; font-size: 14px; color: #475569; }
    .form-control { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; box-sizing: border-box; }
    .row { display: flex; gap: 20px; flex-wrap: wrap; }
    .col { flex: 1; min-width: 250px; }
    .btn-primary { background: #2563eb; color: white; padding: 10px 24px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block;}
    .btn-secondary { background: white; border: 1px solid #cbd5e1; color: #475569; padding: 10px 24px; border-radius: 6px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block;}
</style>

<form action="{{ route('admin.users.store-siswa') }}" method="POST">
    @csrf
    
    {{-- CARD 1: AKUN & AKADEMIK --}}
    <div class="card">
        <h5 class="form-title">1. Akun & Data Akademik</h5>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Username <span style="color:red">*</span></label>
                    <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Password <span style="color:red">*</span></label>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Email (Opsional)</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
        </div>
        
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">NISN <span style="color:red">*</span></label>
                    <input type="text" name="nisn" class="form-control" value="{{ old('nisn') }}" required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">NIS (Nomor Induk Sekolah) <span style="color:red">*</span></label>
                    <input type="text" name="nis" class="form-control" value="{{ old('nis') }}" required>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Cabang <span style="color:red">*</span></label>
                    <select name="cabang_id" class="form-control" required>
                        <option value="">-- Pilih Cabang --</option>
                        @foreach($cabangList as $cabang)
                            <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Kelas <span style="color:red">*</span></label>
                    <select name="kelas_id" class="form-control" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }} ({{ $kelas->jenjang }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Tanggal Masuk <span style="color:red">*</span></label>
            <input type="date" name="tanggal_masuk" class="form-control" value="{{ old('tanggal_masuk') }}" required>
        </div>
    </div>

    {{-- CARD 2: BIODATA --}}
    <div class="card">
        <h5 class="form-title">2. Biodata Siswa</h5>
        <div class="form-group">
            <label class="form-label">Nama Lengkap <span style="color:red">*</span></label>
            <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap') }}" required>
        </div>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Jenis Kelamin <span style="color:red">*</span></label>
                    <select name="jenis_kelamin" class="form-control" required>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Tempat Lahir <span style="color:red">*</span></label>
                    <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}" required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Tanggal Lahir <span style="color:red">*</span></label>
                    <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}" required>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Alamat Lengkap <span style="color:red">*</span></label>
            <textarea name="alamat" class="form-control" rows="3" required>{{ old('alamat') }}</textarea>
        </div>
    </div>

    {{-- CARD 3: ORANG TUA --}}
    <div class="card">
        <h5 class="form-title">3. Data Orang Tua / Wali</h5>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Nama Ayah</label>
                    <input type="text" name="nama_ayah" class="form-control" value="{{ old('nama_ayah') }}">
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Nama Ibu</label>
                    <input type="text" name="nama_ibu" class="form-control" value="{{ old('nama_ibu') }}">
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">No. Telepon Orang Tua (WA Aktif)</label>
            <input type="text" name="telepon_orangtua" class="form-control" value="{{ old('telepon_orangtua') }}">
        </div>
    </div>

    {{-- CARD 4: AKUN ORANG TUA --}}
    <div class="card">
        <h5 class="form-title">4. Akun Orang Tua (Login Sistem)</h5>
        <p style="color: #64748b; font-size: 13px; margin-bottom: 16px;">
            <i class="fas fa-info-circle"></i>
            Pilih orang tua yang sudah ada atau buat akun baru untuk memberikan akses login ke sistem.
        </p>

        <div class="form-group">
            <label class="form-label">Opsi Akun Orang Tua</label>
            <select name="parent_option" id="parentOption" class="form-control" onchange="toggleParentForm()">
                <option value="">-- Pilih Opsi --</option>
                <option value="existing">Pilih Orang Tua yang Sudah Ada</option>
                <option value="new">Buat Akun Orang Tua Baru</option>
                <option value="none">Tidak Perlu Akun (Bisa Ditambahkan Nanti)</option>
            </select>
        </div>

        {{-- Existing Parent Selection --}}
        <div id="existingParentForm" style="display: none;">
            <div class="form-group">
                <label class="form-label">Pilih Orang Tua <span style="color:red">*</span></label>
                <select name="parent_id" class="form-control">
                    <option value="">-- Pilih Orang Tua --</option>
                    @foreach($orangTuaList as $ortu)
                        <option value="{{ $ortu->id }}">
                            {{ $ortu->name }} ({{ $ortu->username }})
                            @if($ortu->studentParents->count() > 0)
                                - Anak: {{ $ortu->studentParents->pluck('siswa.nama_lengkap')->join(', ') }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Hubungan <span style="color:red">*</span></label>
                <select name="existing_relationship" class="form-control">
                    <option value="ayah_kandung">Ayah Kandung</option>
                    <option value="ibu_kandung">Ibu Kandung</option>
                    <option value="wali">Wali</option>
                    <option value="ayah_tiri">Ayah Tiri</option>
                    <option value="ibu_tiri">Ibu Tiri</option>
                </select>
            </div>
        </div>

        {{-- New Parent Form --}}
        <div id="newParentForm" style="display: none;">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap Orang Tua <span style="color:red">*</span></label>
                        <input type="text" name="parent_name" class="form-control" value="{{ old('parent_name') }}">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Username Orang Tua <span style="color:red">*</span></label>
                        <input type="text" name="parent_username" class="form-control" value="{{ old('parent_username') }}">
                        <small style="color: #64748b;">Untuk login ke sistem</small>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Email Orang Tua <span style="color:red">*</span></label>
                        <input type="email" name="parent_email" class="form-control" value="{{ old('parent_email') }}">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Password <span style="color:red">*</span></label>
                        <input type="password" name="parent_password" class="form-control">
                        <small style="color: #64748b;">Minimal 8 karakter</small>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Hubungan dengan Siswa <span style="color:red">*</span></label>
                        <select name="new_relationship" class="form-control">
                            <option value="ayah_kandung">Ayah Kandung</option>
                            <option value="ibu_kandung">Ibu Kandung</option>
                            <option value="wali">Wali</option>
                            <option value="ayah_tiri">Ayah Tiri</option>
                            <option value="ibu_tiri">Ibu Tiri</option>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">No. Telepon/WA <span style="color:red">*</span></label>
                        <input type="text" name="parent_phone" class="form-control" value="{{ old('parent_phone') }}">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="is_primary" value="1" checked>
                    <span style="font-weight: 500; font-size: 14px;">Jadikan sebagai kontak utama</span>
                </label>
            </div>
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="can_access_academic" value="1" checked>
                    <span style="font-weight: 500; font-size: 14px;">Dapat mengakses data akademik siswa</span>
                </label>
            </div>
        </div>
    </div>

    <script>
    function toggleParentForm() {
        const option = document.getElementById('parentOption').value;
        const existingForm = document.getElementById('existingParentForm');
        const newForm = document.getElementById('newParentForm');

        existingForm.style.display = 'none';
        newForm.style.display = 'none';

        if (option === 'existing') {
            existingForm.style.display = 'block';
        } else if (option === 'new') {
            newForm.style.display = 'block';
        }
    }
    </script>

    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 40px;">
        <a href="{{ route('admin.users.siswa') }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary">Simpan Data Siswa</button>
    </div>
</form>
@endsection