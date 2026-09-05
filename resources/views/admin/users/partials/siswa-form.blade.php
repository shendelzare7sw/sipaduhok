@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $editing = isset($siswa);
    $user = $editing ? $siswa->user : null;
    $backUrl = $editing ? url()->previous(route('admin.users.siswa')) : route('admin.users.siswa');
    $inputClass = 'mt-1.5 block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-100';
    $labelClass = 'block text-sm font-bold text-slate-700';
    $errorClass = 'mt-1.5 block text-xs font-semibold text-red-600';
    $relationships = [
        'ayah_kandung' => 'Ayah Kandung',
        'ibu_kandung' => 'Ibu Kandung',
        'wali' => 'Wali',
        'ayah_tiri' => 'Ayah Tiri',
        'ibu_tiri' => 'Ibu Tiri',
        'lainnya' => 'Lainnya',
    ];
    $linkedParentIds = $editing ? $siswa->studentParents->pluck('parent_id')->map(fn ($id) => (string) $id)->all() : [];
    $selectedParentMode = old($editing ? 'add_parent_option' : 'parent_option', $editing ? '' : 'none');
@endphp

<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <div class="flex min-w-0 items-center gap-3">
        <a href="{{ $backUrl }}" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 no-underline transition hover:border-brand-300 hover:text-brand-700" aria-label="Kembali">
            <i class="fas fa-arrow-left" aria-hidden="true"></i>
        </a>
        <div class="min-w-0">
            <p class="text-xs font-bold uppercase tracking-wider text-brand-600">Data siswa</p>
            <h2 class="truncate text-xl font-extrabold text-slate-950 sm:text-2xl">{{ $editing ? 'Edit data siswa' : 'Tambah siswa baru' }}</h2>
            <p class="mt-0.5 text-sm text-slate-500">{{ $editing ? 'Perbarui data inti dan relasi wali dalam satu alur.' : 'Buat akun, tempatkan ke kelas, lalu lengkapi biodata.' }}</p>
        </div>
    </div>
    @if($editing)
        <span class="max-w-full truncate rounded-full bg-brand-50 px-3 py-1.5 text-xs font-bold text-brand-700">{{ $siswa->nama_lengkap }}</span>
    @endif
</div>

@if (isset($errors) && $errors->any())
    <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
        <div class="flex gap-3">
            <i class="fas fa-circle-exclamation mt-0.5 text-red-500" aria-hidden="true"></i>
            <div class="min-w-0">
                <p class="font-extrabold">Data belum dapat disimpan.</p>
                <ul class="mt-1 list-disc space-y-0.5 pl-4 text-xs leading-5 text-red-700">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

<form action="{{ $editing ? route('admin.users.update-siswa', $siswa->id) : route('admin.users.store-siswa') }}" method="POST" class="space-y-4">
    @csrf
    @if($editing)
        @method('PUT')
        <input type="hidden" name="_return_url" value="{{ $backUrl }}">
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-start gap-3 border-b border-slate-200 px-4 py-4 sm:px-5">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600"><i class="fas fa-user-lock" aria-hidden="true"></i></span>
            <div><h3 class="font-extrabold text-slate-950">1. Akun login</h3><p class="mt-0.5 text-xs text-slate-500">Kredensial yang digunakan siswa untuk masuk ke aplikasi.</p></div>
        </div>
        <div class="grid gap-4 p-4 sm:grid-cols-2 sm:p-5">
            <div>
                <label for="username" class="{{ $labelClass }}">Username <span class="text-red-500">*</span></label>
                <input id="username" type="text" name="username" value="{{ old('username', $user?->username) }}" class="{{ $inputClass }}" autocomplete="username" maxlength="50" required>
                @error('username') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="password" class="{{ $labelClass }}">Password @unless($editing)<span class="text-red-500">*</span>@endunless</label>
                <div class="relative mt-1.5">
                    <input id="password" type="password" name="password" class="block w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-3.5 pr-11 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100" minlength="8" autocomplete="new-password" placeholder="{{ $editing ? 'Kosongkan jika tidak diubah' : 'Minimal 8 karakter' }}" @required(!$editing)>
                    <button type="button" data-password-toggle data-target="password" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-400 hover:text-brand-600" aria-label="Tampilkan atau sembunyikan password"><i class="fas fa-eye-slash" aria-hidden="true"></i></button>
                </div>
                @error('password') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="email" class="{{ $labelClass }}">Email akun <span class="font-normal text-slate-400">(opsional)</span></label>
                <input id="email" type="email" name="email" value="{{ old('email', $user?->email) }}" class="{{ $inputClass }}" autocomplete="email" placeholder="Jika kosong, sistem membuat email internal">
                @error('email') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="personal_email" class="{{ $labelClass }}">Email pemulihan <span class="font-normal text-slate-400">(opsional)</span></label>
                <input id="personal_email" type="email" name="personal_email" value="{{ old('personal_email', $user?->personal_email) }}" class="{{ $inputClass }}" placeholder="Email pribadi yang masih aktif">
                @error('personal_email') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>
            @if($editing)
                <div class="sm:col-span-2">
                    <label for="is_active" class="{{ $labelClass }}">Status akun <span class="text-red-500">*</span></label>
                    <select id="is_active" name="is_active" class="{{ $inputClass }}" required>
                        <option value="1" @selected((string) old('is_active', $user?->is_active ? '1' : '0') === '1')>Aktif — dapat login</option>
                        <option value="0" @selected((string) old('is_active', $user?->is_active ? '1' : '0') === '0')>Nonaktif — tidak dapat login</option>
                    </select>
                    @error('is_active') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
                </div>
            @endif
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-start gap-3 border-b border-slate-200 px-4 py-4 sm:px-5">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600"><i class="fas fa-school" aria-hidden="true"></i></span>
            <div><h3 class="font-extrabold text-slate-950">2. Identitas dan penempatan</h3><p class="mt-0.5 text-xs text-slate-500">Kelas menentukan cabang siswa secara otomatis.</p></div>
        </div>
        <div class="grid gap-4 p-4 sm:grid-cols-2 sm:p-5 lg:grid-cols-3">
            <div class="sm:col-span-2 lg:col-span-1">
                <label for="nama_lengkap" class="{{ $labelClass }}">Nama lengkap <span class="text-red-500">*</span></label>
                <input id="nama_lengkap" type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $editing ? $siswa->nama_lengkap : null) }}" class="{{ $inputClass }}" required>
                @error('nama_lengkap') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="nisn" class="{{ $labelClass }}">NISN <span class="text-red-500">*</span></label>
                <input id="nisn" type="text" name="nisn" value="{{ old('nisn', $editing ? $siswa->nisn : null) }}" class="{{ $inputClass }}" maxlength="20" inputmode="numeric" required>
                @error('nisn') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="nis" class="{{ $labelClass }}">NIS <span class="text-red-500">*</span></label>
                <input id="nis" type="text" name="nis" value="{{ old('nis', $editing ? $siswa->nis : null) }}" class="{{ $inputClass }}" maxlength="20" required>
                @error('nis') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>
            <div class="sm:col-span-2">
                <label for="kelas_id" class="{{ $labelClass }}">Kelas aktif <span class="text-red-500">*</span></label>
                <select id="kelas_id" name="kelas_id" class="{{ $inputClass }}" required>
                    <option value="">Pilih kelas, jenjang, dan cabang</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" @selected((string) old('kelas_id', $editing ? $siswa->kelas_id : '') === (string) $kelas->id)>{{ $kelas->nama_kelas }} · {{ $kelas->jenjang }} · {{ $kelas->cabang->nama_cabang ?? 'Tanpa cabang' }}</option>
                    @endforeach
                </select>
                @error('kelas_id') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="tanggal_masuk" class="{{ $labelClass }}">Tanggal masuk <span class="text-red-500">*</span></label>
                <input id="tanggal_masuk" type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', $editing && $siswa->tanggal_masuk ? \Carbon\Carbon::parse($siswa->tanggal_masuk)->format('Y-m-d') : null) }}" class="{{ $inputClass }}" required>
                @error('tanggal_masuk') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>
            @if($editing)
                <div class="sm:col-span-2 lg:col-span-1">
                    <label for="status" class="{{ $labelClass }}">Status akademik <span class="text-red-500">*</span></label>
                    <select id="status" name="status" class="{{ $inputClass }}" required>
                        @foreach(['aktif' => 'Aktif', 'lulus' => 'Lulus', 'pindah' => 'Pindah', 'keluar' => 'Keluar'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $siswa->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
                </div>
            @endif
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-start gap-3 border-b border-slate-200 px-4 py-4 sm:px-5">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"><i class="fas fa-id-card" aria-hidden="true"></i></span>
            <div><h3 class="font-extrabold text-slate-950">3. Biodata pribadi</h3><p class="mt-0.5 text-xs text-slate-500">Data identitas untuk administrasi dan dokumen akademik.</p></div>
        </div>
        <div class="grid gap-4 p-4 sm:grid-cols-2 sm:p-5 lg:grid-cols-3">
            <div>
                <label for="jenis_kelamin" class="{{ $labelClass }}">Jenis kelamin <span class="text-red-500">*</span></label>
                <select id="jenis_kelamin" name="jenis_kelamin" class="{{ $inputClass }}" required>
                    <option value="">Pilih jenis kelamin</option>
                    <option value="L" @selected(old('jenis_kelamin', $editing ? $siswa->jenis_kelamin : null) === 'L')>Laki-laki</option>
                    <option value="P" @selected(old('jenis_kelamin', $editing ? $siswa->jenis_kelamin : null) === 'P')>Perempuan</option>
                </select>
                @error('jenis_kelamin') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="tempat_lahir" class="{{ $labelClass }}">Tempat lahir <span class="text-red-500">*</span></label>
                <input id="tempat_lahir" type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $editing ? $siswa->tempat_lahir : null) }}" class="{{ $inputClass }}" placeholder="Kota atau negara kelahiran" required>
                @error('tempat_lahir') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="tanggal_lahir" class="{{ $labelClass }}">Tanggal lahir <span class="text-red-500">*</span></label>
                <input id="tanggal_lahir" type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $editing && $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('Y-m-d') : null) }}" class="{{ $inputClass }}" required>
                @error('tanggal_lahir') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="agama" class="{{ $labelClass }}">Agama <span class="text-red-500">*</span></label>
                <select id="agama" name="agama" class="{{ $inputClass }}" required>
                    <option value="">Pilih agama</option>
                    @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                        <option value="{{ $agama }}" @selected(old('agama', $editing ? $siswa->agama : null) === $agama)>{{ $agama }}</option>
                    @endforeach
                </select>
                @error('agama') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <label for="alamat" class="{{ $labelClass }}">Alamat lengkap <span class="text-red-500">*</span></label>
                <textarea id="alamat" name="alamat" rows="3" class="{{ $inputClass }}" placeholder="RT/RW, kelurahan, kecamatan, kota/kabupaten" required>{{ old('alamat', $editing ? $siswa->alamat : null) }}</textarea>
                @error('alamat') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-start gap-3 border-b border-slate-200 px-4 py-4 sm:px-5">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600"><i class="fas fa-users" aria-hidden="true"></i></span>
            <div><h3 class="font-extrabold text-slate-950">4. Kontak keluarga</h3><p class="mt-0.5 text-xs text-slate-500">Biodata kontak; bagian ini tidak otomatis membuat akun wali.</p></div>
        </div>
        <div class="grid gap-4 p-4 sm:grid-cols-2 sm:p-5 lg:grid-cols-3">
            <div><label for="nama_ayah" class="{{ $labelClass }}">Nama ayah <span class="font-normal text-slate-400">(opsional)</span></label><input id="nama_ayah" type="text" name="nama_ayah" value="{{ old('nama_ayah', $editing ? $siswa->nama_ayah : null) }}" class="{{ $inputClass }}">@error('nama_ayah') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror</div>
            <div><label for="nama_ibu" class="{{ $labelClass }}">Nama ibu <span class="font-normal text-slate-400">(opsional)</span></label><input id="nama_ibu" type="text" name="nama_ibu" value="{{ old('nama_ibu', $editing ? $siswa->nama_ibu : null) }}" class="{{ $inputClass }}">@error('nama_ibu') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror</div>
            <div><label for="telepon_orangtua" class="{{ $labelClass }}">Nomor WhatsApp keluarga <span class="font-normal text-slate-400">(opsional)</span></label><input id="telepon_orangtua" type="tel" name="telepon_orangtua" value="{{ old('telepon_orangtua', $editing ? $siswa->telepon_orangtua : null) }}" class="{{ $inputClass }}" inputmode="tel">@error('telepon_orangtua') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror</div>
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" data-conditional-root>
        <div class="flex items-start gap-3 border-b border-slate-200 px-4 py-4 sm:px-5">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-50 text-cyan-600"><i class="fas fa-user-shield" aria-hidden="true"></i></span>
            <div><h3 class="font-extrabold text-slate-950">5. Akses wali siswa</h3><p class="mt-0.5 text-xs text-slate-500">{{ $editing ? 'Tinjau wali terhubung atau tambahkan satu wali baru.' : 'Hubungkan akun lama, buat akun baru, atau lewati dahulu.' }}</p></div>
        </div>

        @if($editing)
            <div class="border-b border-slate-100 p-4 sm:p-5">
                <p class="mb-3 text-sm font-extrabold text-slate-800">Wali yang sudah terhubung</p>
                @forelse($siswa->studentParents as $studentParent)
                    <label class="mb-2 flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-3 last:mb-0 has-[:checked]:border-red-300 has-[:checked]:bg-red-50">
                        <input type="checkbox" name="remove_parents[]" value="{{ $studentParent->id }}" class="mt-1 h-4 w-4 shrink-0 rounded border-slate-300 text-red-600 focus:ring-red-500">
                        <span class="min-w-0 flex-1"><span class="block truncate text-sm font-bold text-slate-800">{{ $studentParent->parent->name }}</span><span class="mt-0.5 block text-xs text-slate-500">{{ ucwords(str_replace('_', ' ', $studentParent->relationship)) }} · {{ $studentParent->parent->username }}</span></span>
                        <span class="shrink-0 text-[10px] font-bold text-red-600">Hapus saat disimpan</span>
                    </label>
                @empty
                    <div class="rounded-xl bg-slate-50 p-4 text-center text-xs text-slate-500">Belum ada akun wali yang terhubung.</div>
                @endforelse
            </div>
        @endif

        <div class="p-4 sm:p-5">
            <label for="parent_mode" class="{{ $labelClass }}">{{ $editing ? 'Tambahkan wali' : 'Pilihan akses wali' }}</label>
            <select id="parent_mode" name="{{ $editing ? 'add_parent_option' : 'parent_option' }}" data-conditional-select class="{{ $inputClass }}">
                <option value="" @selected($selectedParentMode === '')>{{ $editing ? 'Tidak menambah wali' : 'Pilih tindakan' }}</option>
                <option value="existing" @selected($selectedParentMode === 'existing')>Hubungkan akun wali yang sudah ada</option>
                <option value="new" @selected($selectedParentMode === 'new')>Buat akun wali baru</option>
                @unless($editing)<option value="none" @selected($selectedParentMode === 'none')>Lewati — dapat ditambahkan nanti</option>@endunless
            </select>

            <div data-conditional-panel="existing" class="mt-4 hidden rounded-2xl border border-slate-200 bg-slate-50 p-4">
                @php
                    $existingParentIdName = $editing ? 'add_existing_parent_id' : 'parent_id';
                    $existingRelationshipName = $editing ? 'add_existing_relationship' : 'existing_relationship';
                    $existingOtherName = $editing ? 'add_existing_relationship_lainnya' : 'existing_relationship_lainnya';
                    $existingOtherId = $editing ? 'add_existing_relationship_other' : 'existing_relationship_other';
                @endphp
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="existing_parent_id" class="{{ $labelClass }}">Akun wali <span class="text-red-500">*</span></label>
                        <select id="existing_parent_id" name="{{ $existingParentIdName }}" class="{{ $inputClass }}" data-required-when-active>
                            <option value="">Pilih berdasarkan nama dan username</option>
                            @foreach($orangTuaList as $orangTua)
                                @if(!$editing || !in_array((string) $orangTua->id, $linkedParentIds, true))
                                    <option value="{{ $orangTua->id }}" @selected((string) old($existingParentIdName) === (string) $orangTua->id)>{{ $orangTua->name }} · {{ $orangTua->username }}{{ $orangTua->studentParents->isNotEmpty() ? ' · sudah memiliki anak terdaftar' : '' }}</option>
                                @endif
                            @endforeach
                        </select>
                        @error($existingParentIdName) <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="existing_relationship" class="{{ $labelClass }}">Hubungan keluarga <span class="text-red-500">*</span></label>
                        <select id="existing_relationship" name="{{ $existingRelationshipName }}" class="{{ $inputClass }}" data-required-when-active data-relationship-select data-other-target="{{ $existingOtherId }}">
                            @foreach($relationships as $value => $label)<option value="{{ $value }}" @selected(old($existingRelationshipName, 'ayah_kandung') === $value)>{{ $label }}</option>@endforeach
                        </select>
                        @error($existingRelationshipName) <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
                    </div>
                    <div id="{{ $existingOtherId }}" class="hidden">
                        <label for="{{ $existingOtherId }}_input" class="{{ $labelClass }}">Sebutkan hubungan <span class="text-red-500">*</span></label>
                        <input id="{{ $existingOtherId }}_input" type="text" name="{{ $existingOtherName }}" value="{{ old($existingOtherName) }}" class="{{ $inputClass }}" maxlength="100" placeholder="Contoh: Kakek, nenek, paman">
                        @error($existingOtherName) <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div data-conditional-panel="new" class="mt-4 hidden rounded-2xl border border-slate-200 bg-slate-50 p-4">
                @php
                    $newPrefix = $editing ? 'add_new_parent_' : 'parent_';
                    $newRelationshipName = $editing ? 'add_new_relationship' : 'new_relationship';
                    $newOtherName = $editing ? 'add_new_relationship_lainnya' : 'new_relationship_lainnya';
                    $newOtherId = $editing ? 'add_new_relationship_other' : 'new_relationship_other';
                @endphp
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><label for="new_parent_name" class="{{ $labelClass }}">Nama lengkap <span class="text-red-500">*</span></label><input id="new_parent_name" type="text" name="{{ $newPrefix }}name" value="{{ old($newPrefix . 'name') }}" class="{{ $inputClass }}" data-required-when-active>@error($newPrefix . 'name') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror</div>
                    <div><label for="new_parent_username" class="{{ $labelClass }}">Username <span class="text-red-500">*</span></label><input id="new_parent_username" type="text" name="{{ $newPrefix }}username" value="{{ old($newPrefix . 'username') }}" class="{{ $inputClass }}" data-required-when-active maxlength="50">@error($newPrefix . 'username') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror</div>
                    <div><label for="new_parent_email" class="{{ $labelClass }}">Email <span class="text-red-500">*</span></label><input id="new_parent_email" type="email" name="{{ $newPrefix }}email" value="{{ old($newPrefix . 'email') }}" class="{{ $inputClass }}" data-required-when-active>@error($newPrefix . 'email') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror</div>
                    <div>
                        <label for="new_parent_password" class="{{ $labelClass }}">Password awal <span class="text-red-500">*</span></label>
                        <div class="relative mt-1.5"><input id="new_parent_password" type="password" name="{{ $newPrefix }}password" class="block w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-3.5 pr-11 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100" data-required-when-active minlength="8" autocomplete="new-password"><button type="button" data-password-toggle data-target="new_parent_password" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-400 hover:text-brand-600" aria-label="Tampilkan atau sembunyikan password"><i class="fas fa-eye-slash" aria-hidden="true"></i></button></div>
                        @error($newPrefix . 'password') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
                    </div>
                    <div><label for="new_parent_phone" class="{{ $labelClass }}">Nomor WhatsApp <span class="font-normal text-slate-400">(opsional)</span></label><input id="new_parent_phone" type="tel" name="{{ $newPrefix }}phone" value="{{ old($newPrefix . 'phone') }}" class="{{ $inputClass }}" inputmode="tel">@error($newPrefix . 'phone') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror</div>
                    <div>
                        <label for="new_relationship" class="{{ $labelClass }}">Hubungan keluarga <span class="text-red-500">*</span></label>
                        <select id="new_relationship" name="{{ $newRelationshipName }}" class="{{ $inputClass }}" data-required-when-active data-relationship-select data-other-target="{{ $newOtherId }}">
                            @foreach($relationships as $value => $label)<option value="{{ $value }}" @selected(old($newRelationshipName, 'ayah_kandung') === $value)>{{ $label }}</option>@endforeach
                        </select>
                        @error($newRelationshipName) <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
                    </div>
                    <div id="{{ $newOtherId }}" class="hidden sm:col-span-2"><label for="{{ $newOtherId }}_input" class="{{ $labelClass }}">Sebutkan hubungan <span class="text-red-500">*</span></label><input id="{{ $newOtherId }}_input" type="text" name="{{ $newOtherName }}" value="{{ old($newOtherName) }}" class="{{ $inputClass }}" maxlength="100">@error($newOtherName) <span class="{{ $errorClass }}">{{ $message }}</span> @enderror</div>
                    @unless($editing)
                        <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-white p-3"><input type="checkbox" name="is_primary" value="1" @checked(old('is_primary', true)) class="mt-0.5 h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500"><span><span class="block text-sm font-bold text-slate-800">Kontak utama</span><span class="text-xs text-slate-500">Menjadi kontak pertama untuk siswa ini.</span></span></label>
                        <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-white p-3"><input type="checkbox" name="can_access_academic" value="1" @checked(old('can_access_academic', true)) class="mt-0.5 h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500"><span><span class="block text-sm font-bold text-slate-800">Akses akademik</span><span class="text-xs text-slate-500">Wali dapat melihat data akademik siswa.</span></span></label>
                    @endunless
                </div>
            </div>
        </div>
    </section>

    <div class="sticky bottom-3 z-10 flex items-center justify-end gap-2 rounded-2xl border border-slate-200 bg-white/95 p-3 shadow-lg backdrop-blur sm:static sm:border-0 sm:bg-transparent sm:p-0 sm:shadow-none">
        <a href="{{ $backUrl }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-bold text-slate-700 no-underline hover:bg-slate-50">Batal</a>
        <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-sm font-bold text-white shadow-sm transition hover:bg-brand-700"><i class="fas fa-save" aria-hidden="true"></i>{{ $editing ? 'Simpan perubahan' : 'Simpan siswa' }}</button>
    </div>
</form>
