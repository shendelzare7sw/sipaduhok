@php
    $editing = isset($tenagaPendidik);
    $user = $editing ? $tenagaPendidik->user : null;
    $backUrl = $editing
        ? url()->previous(route('admin.users.tenaga-pendidik'))
        : route('admin.users.tenaga-pendidik');
    $inputClass = 'mt-1.5 block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-100';
    $labelClass = 'block text-sm font-bold text-slate-700';
    $errorClass = 'mt-1.5 block text-xs font-semibold text-red-600';
@endphp

<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <div class="flex min-w-0 items-center gap-3">
        <a href="{{ $backUrl }}" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 no-underline transition hover:border-brand-300 hover:text-brand-700" aria-label="Kembali">
            <i class="fas fa-arrow-left" aria-hidden="true"></i>
        </a>
        <div class="min-w-0">
            <p class="text-xs font-bold uppercase tracking-wider text-brand-600">Data pengguna</p>
            <h2 class="truncate text-xl font-extrabold text-slate-950 sm:text-2xl">{{ $editing ? 'Edit tenaga pendidik' : 'Tambah tenaga pendidik' }}</h2>
            <p class="mt-0.5 text-sm text-slate-500">{{ $editing ? 'Perbarui akun dan biodata tanpa mengubah alur kerja lainnya.' : 'Buat akun login, lalu lengkapi biodata dalam satu langkah.' }}</p>
        </div>
    </div>
</div>

@if ($errors->any())
    <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
        <div class="flex gap-3">
            <i class="fas fa-circle-exclamation mt-0.5 text-red-500" aria-hidden="true"></i>
            <div>
                <p class="font-extrabold">Data belum dapat disimpan.</p>
                <p class="mt-0.5 text-xs text-red-700">Periksa kembali kolom yang ditandai di bawah.</p>
            </div>
        </div>
    </div>
@endif

<form
    action="{{ $editing ? route('admin.users.update-tenaga-pendidik', $tenagaPendidik->user_id) : route('admin.users.store-tenaga-pendidik') }}"
    method="POST"
    class="space-y-4"
    data-role-cabang-form
    data-flexible-roles="ketua_pkbm,sekretaris,bendahara"
    data-default-cabang-id="1"
>
    @csrf
    @if($editing)
        @method('PUT')
        <input type="hidden" name="_return_url" value="{{ $backUrl }}">
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-start gap-3 border-b border-slate-200 px-4 py-4 sm:px-5">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><i class="fas fa-user-lock" aria-hidden="true"></i></span>
            <div>
                <h3 class="font-extrabold text-slate-950">1. Informasi akun</h3>
                <p class="mt-0.5 text-xs text-slate-500">Digunakan untuk login dan menentukan akses pengguna.</p>
            </div>
        </div>

        <div class="grid gap-4 p-4 sm:grid-cols-2 sm:p-5">
            <div>
                <label for="username" class="{{ $labelClass }}">Username <span class="text-red-500">*</span></label>
                <input id="username" type="text" name="username" value="{{ old('username', $user?->username) }}" class="{{ $inputClass }}" autocomplete="username" required>
                @error('username') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="email" class="{{ $labelClass }}">Email akun <span class="text-red-500">*</span></label>
                <input id="email" type="email" name="email" value="{{ old('email', $user?->email) }}" class="{{ $inputClass }}" autocomplete="email" required>
                @error('email') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="password" class="{{ $labelClass }}">Password @if(!$editing)<span class="text-red-500">*</span>@endif</label>
                <div class="relative mt-1.5">
                    <input id="password" type="password" name="password" class="block w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-3.5 pr-11 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-100" minlength="8" autocomplete="new-password" placeholder="{{ $editing ? 'Kosongkan jika tidak diubah' : 'Minimal 8 karakter' }}" @required(!$editing)>
                    <button type="button" data-password-toggle data-target="password" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-400 hover:text-brand-600" aria-label="Tampilkan atau sembunyikan password"><i class="fas fa-eye-slash" aria-hidden="true"></i></button>
                </div>
                @error('password') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="role" class="{{ $labelClass }}">Role / jabatan <span class="text-red-500">*</span></label>
                <select id="role" name="role" data-role-select class="{{ $inputClass }}" required>
                    <option value="">Pilih role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role }}" @selected(old('role', $user?->role) === $role)>{{ ucwords(str_replace('_', ' ', $role)) }}</option>
                    @endforeach
                </select>
                @error('role') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="cabangSelect" class="{{ $labelClass }}">Cabang penempatan <span class="text-red-500">*</span></label>
                <select id="cabangSelect" name="cabang_id" data-cabang-select class="{{ $inputClass }}" required>
                    <option value="">Pilih cabang</option>
                    @foreach($cabangList as $cabang)
                        <option value="{{ $cabang->id }}" @selected((string) old('cabang_id', $user?->cabang_id) === (string) $cabang->id)>{{ $cabang->nama_cabang }}</option>
                    @endforeach
                </select>
                <input type="hidden" data-cabang-hidden disabled>
                <p data-cabang-info class="mt-2 hidden rounded-lg bg-blue-50 px-3 py-2 text-xs leading-relaxed text-blue-700">
                    <i class="fas fa-circle-info mr-1" aria-hidden="true"></i>Role pusat dapat mengakses seluruh cabang dan otomatis ditempatkan di Gedung Utama.
                </p>
                @error('cabang_id') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>

            @if($editing)
                <div>
                    <label for="is_active" class="{{ $labelClass }}">Status akun <span class="text-red-500">*</span></label>
                    <select id="is_active" name="is_active" class="{{ $inputClass }}" required>
                        <option value="1" @selected((string) old('is_active', $user?->is_active ? '1' : '0') === '1')>Aktif</option>
                        <option value="0" @selected((string) old('is_active', $user?->is_active ? '1' : '0') === '0')>Nonaktif</option>
                    </select>
                    @error('is_active') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
                </div>
            @endif
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-start gap-3 border-b border-slate-200 px-4 py-4 sm:px-5">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"><i class="fas fa-id-card" aria-hidden="true"></i></span>
            <div>
                <h3 class="font-extrabold text-slate-950">2. Biodata pribadi</h3>
                <p class="mt-0.5 text-xs text-slate-500">Lengkapi identitas yang dipakai dalam administrasi sekolah.</p>
            </div>
        </div>

        <div class="grid gap-4 p-4 sm:grid-cols-2 sm:p-5">
            <div>
                <label for="nama_lengkap" class="{{ $labelClass }}">Nama lengkap dan gelar <span class="text-red-500">*</span></label>
                <input id="nama_lengkap" type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $editing ? $tenagaPendidik->nama_lengkap : null) }}" class="{{ $inputClass }}" required>
                @error('nama_lengkap') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="nip" class="{{ $labelClass }}">NIP <span class="font-normal text-slate-400">(opsional)</span></label>
                <input id="nip" type="text" name="nip" value="{{ old('nip', $editing ? $tenagaPendidik->nip : null) }}" class="{{ $inputClass }}" placeholder="Nomor Induk Pegawai">
                @error('nip') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="jenis_kelamin" class="{{ $labelClass }}">Jenis kelamin <span class="text-red-500">*</span></label>
                <select id="jenis_kelamin" name="jenis_kelamin" class="{{ $inputClass }}" required>
                    <option value="">Pilih jenis kelamin</option>
                    <option value="L" @selected(old('jenis_kelamin', $editing ? $tenagaPendidik->jenis_kelamin : null) === 'L')>Laki-laki</option>
                    <option value="P" @selected(old('jenis_kelamin', $editing ? $tenagaPendidik->jenis_kelamin : null) === 'P')>Perempuan</option>
                </select>
                @error('jenis_kelamin') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="telepon" class="{{ $labelClass }}">Nomor telepon / WhatsApp <span class="text-red-500">*</span></label>
                <input id="telepon" type="tel" name="telepon" value="{{ old('telepon', $editing ? $tenagaPendidik->telepon : null) }}" class="{{ $inputClass }}" inputmode="tel" required>
                @error('telepon') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>

            <div class="sm:col-span-2">
                <label for="personal_email" class="{{ $labelClass }}">Email pemulihan <span class="font-normal text-slate-400">(opsional)</span></label>
                <input id="personal_email" type="email" name="personal_email" value="{{ old('personal_email', $user?->personal_email) }}" class="{{ $inputClass }}" placeholder="contoh: nama@gmail.com">
                <p class="mt-1.5 text-xs text-slate-500">Gunakan email pribadi yang aktif untuk membantu pemulihan akun.</p>
                @error('personal_email') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="tempat_lahir" class="{{ $labelClass }}">Tempat lahir <span class="text-red-500">*</span></label>
                <input id="tempat_lahir" type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $editing ? $tenagaPendidik->tempat_lahir : null) }}" class="{{ $inputClass }}" placeholder="Kota atau negara kelahiran" required>
                @error('tempat_lahir') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="tanggal_lahir" class="{{ $labelClass }}">Tanggal lahir <span class="text-red-500">*</span></label>
                <input id="tanggal_lahir" type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $editing && $tenagaPendidik->tanggal_lahir ? \Carbon\Carbon::parse($tenagaPendidik->tanggal_lahir)->format('Y-m-d') : null) }}" class="{{ $inputClass }}" required>
                @error('tanggal_lahir') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>

            <div class="sm:col-span-2">
                <label for="pendidikan_terakhir" class="{{ $labelClass }}">Pendidikan terakhir <span class="text-red-500">*</span></label>
                <input id="pendidikan_terakhir" type="text" name="pendidikan_terakhir" value="{{ old('pendidikan_terakhir', $editing ? $tenagaPendidik->pendidikan_terakhir : null) }}" class="{{ $inputClass }}" placeholder="Contoh: S1 Pendidikan Matematika" required>
                @error('pendidikan_terakhir') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>

            <div class="sm:col-span-2">
                <label for="alamat" class="{{ $labelClass }}">Alamat lengkap <span class="text-red-500">*</span></label>
                <textarea id="alamat" name="alamat" rows="4" class="{{ $inputClass }}" placeholder="RT/RW, kelurahan, kecamatan, kota/kabupaten, provinsi" required>{{ old('alamat', $editing ? $tenagaPendidik->alamat : null) }}</textarea>
                @error('alamat') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>
        </div>
    </section>

    <div class="sticky bottom-3 z-10 flex items-center justify-end gap-2 rounded-2xl border border-slate-200 bg-white/95 p-3 shadow-lg backdrop-blur sm:static sm:border-0 sm:bg-transparent sm:p-0 sm:shadow-none">
        <a href="{{ $backUrl }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-bold text-slate-700 no-underline transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-sm font-bold text-white shadow-sm transition hover:bg-brand-700">
            <i class="fas fa-save" aria-hidden="true"></i>{{ $editing ? 'Simpan perubahan' : 'Simpan tenaga pendidik' }}
        </button>
    </div>
</form>
