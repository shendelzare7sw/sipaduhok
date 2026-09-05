@php
    $editing = isset($orangTua);
    $inputClass = 'mt-1.5 block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-100';
    $labelClass = 'block text-sm font-bold text-slate-700';
    $errorClass = 'mt-1.5 block text-xs font-semibold text-red-600';
@endphp

<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="flex items-start gap-3 border-b border-slate-200 px-4 py-4 sm:px-5">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><i class="fas fa-user-lock" aria-hidden="true"></i></span>
        <div>
            <h3 class="font-extrabold text-slate-950">1. Akun dan identitas wali</h3>
            <p class="mt-0.5 text-xs leading-relaxed text-slate-500">Data login dan kontak yang dapat digunakan sekolah untuk menghubungi wali.</p>
        </div>
    </div>

    <div class="grid gap-4 p-4 sm:grid-cols-2 sm:p-5">
        <div>
            <label for="name" class="{{ $labelClass }}">Nama lengkap <span class="text-red-500">*</span></label>
            <input id="name" type="text" name="name" value="{{ old('name', $orangTua->name ?? '') }}" class="{{ $inputClass }}" autocomplete="name" required>
            @error('name') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="username" class="{{ $labelClass }}">Username <span class="text-red-500">*</span></label>
            <input id="username" type="text" name="username" value="{{ old('username', $orangTua->username ?? '') }}" class="{{ $inputClass }}" autocomplete="username" required>
            <p class="mt-1.5 text-xs text-slate-500">Digunakan wali untuk masuk ke aplikasi.</p>
            @error('username') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="email" class="{{ $labelClass }}">Email akun <span class="font-normal text-slate-400">(opsional)</span></label>
            <input id="email" type="email" name="email" value="{{ old('email', $orangTua->email ?? '') }}" class="{{ $inputClass }}" autocomplete="email" placeholder="contoh@email.com">
            @error('email') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="phone" class="{{ $labelClass }}">Nomor telepon / WhatsApp <span class="font-normal text-slate-400">(opsional)</span></label>
            <input id="phone" type="tel" name="phone" value="{{ old('phone', $orangTua->phone ?? '') }}" class="{{ $inputClass }}" inputmode="tel" autocomplete="tel" placeholder="08xxxxxxxxxx">
            @error('phone') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
        </div>

        <div class="sm:col-span-2">
            <label for="personal_email" class="{{ $labelClass }}">Email pemulihan <span class="font-normal text-slate-400">(opsional)</span></label>
            <input id="personal_email" type="email" name="personal_email" value="{{ old('personal_email', $orangTua->personal_email ?? '') }}" class="{{ $inputClass }}" placeholder="Email pribadi yang aktif">
            <p class="mt-1.5 text-xs text-slate-500">Disarankan berbeda dari email akun untuk membantu pemulihan akses.</p>
            @error('personal_email') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
        </div>

        <div class="{{ $editing ? 'sm:col-span-2' : '' }}">
            <label for="password" class="{{ $labelClass }}">Password @if(!$editing)<span class="text-red-500">*</span>@else<span class="font-normal text-slate-400">(opsional)</span>@endif</label>
            <div class="relative mt-1.5">
                <input id="password" type="password" name="password" class="block w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-3.5 pr-11 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-100" minlength="{{ $editing ? 8 : 6 }}" autocomplete="new-password" placeholder="{{ $editing ? 'Kosongkan jika tidak diubah' : 'Minimal 6 karakter' }}" @required(!$editing)>
                <button type="button" data-password-toggle data-target="password" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-400 hover:text-brand-600" aria-label="Tampilkan atau sembunyikan password"><i class="fas fa-eye-slash" aria-hidden="true"></i></button>
            </div>
            @error('password') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
        </div>

        @if(!$editing)
            <div>
                <label for="password_confirmation" class="{{ $labelClass }}">Konfirmasi password <span class="text-red-500">*</span></label>
                <div class="relative mt-1.5">
                    <input id="password_confirmation" type="password" name="password_confirmation" class="block w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-3.5 pr-11 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-100" minlength="6" autocomplete="new-password" required>
                    <button type="button" data-password-toggle data-target="password_confirmation" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-400 hover:text-brand-600" aria-label="Tampilkan atau sembunyikan konfirmasi password"><i class="fas fa-eye-slash" aria-hidden="true"></i></button>
                </div>
                @error('password_confirmation') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>

            <div class="sm:col-span-2">
                <label for="address" class="{{ $labelClass }}">Alamat <span class="font-normal text-slate-400">(opsional)</span></label>
                <textarea id="address" name="address" rows="3" class="{{ $inputClass }}" placeholder="Alamat domisili wali siswa">{{ old('address') }}</textarea>
                @error('address') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>
        @else
            <div class="sm:col-span-2">
                <label for="is_active" class="{{ $labelClass }}">Status akun <span class="text-red-500">*</span></label>
                <select id="is_active" name="is_active" class="{{ $inputClass }}" required>
                    <option value="1" @selected((string) old('is_active', $orangTua->is_active ? '1' : '0') === '1')>Aktif — dapat login</option>
                    <option value="0" @selected((string) old('is_active', $orangTua->is_active ? '1' : '0') === '0')>Nonaktif — tidak dapat login</option>
                </select>
                @error('is_active') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>
        @endif
    </div>
</section>
