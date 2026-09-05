@extends('layouts.app')

@section('title', 'Pengaturan Akun')
@section('page-title', 'Pengaturan Akun')
@section('page-subtitle', 'Kelola profil, password, dan keamanan pemulihan')

@section('content')
@php
    $inputClass = 'min-h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 disabled:bg-slate-100 disabled:text-slate-500';
    $labelClass = 'mb-1.5 block text-xs font-bold text-slate-700';
    $helpClass = 'mt-1.5 block text-[11px] leading-5 text-slate-500';
    $errorClass = 'mt-1.5 block text-[11px] font-semibold text-red-600';
    $toggleClass = 'flex w-11 shrink-0 items-center justify-center border-l border-slate-200 bg-slate-50 text-slate-500 hover:bg-slate-100 hover:text-slate-700';
@endphp

<div class="grid min-w-0 grid-cols-1 gap-5 lg:grid-cols-2 xl:grid-cols-3">
    <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="flex items-center gap-3 border-b border-slate-200 bg-slate-50 px-4 py-4 sm:px-5">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600"><i class="fas fa-user-circle" aria-hidden="true"></i></span>
            <div class="min-w-0">
                <h2 class="text-sm font-extrabold text-slate-900 sm:text-base">Informasi Profil</h2>
                <p class="mt-0.5 text-[11px] text-slate-500">Identitas utama akun Anda.</p>
            </div>
        </header>
        <form action="{{ route('account.update-settings') }}" method="POST" class="space-y-4 p-4 sm:p-5">
            @csrf
            @method('PUT')

            <div>
                <label for="account-name" class="{{ $labelClass }}">Nama Lengkap</label>
                <input id="account-name" type="text" name="name" class="{{ $inputClass }} {{ $errors->has('name') ? 'border-red-400' : '' }}" value="{{ old('name', $user->name) }}">
                @error('name') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="account-username" class="{{ $labelClass }}">Username</label>
                <input id="account-username" type="text" name="username" class="{{ $inputClass }} {{ $errors->has('username') ? 'border-red-400' : '' }}" value="{{ old('username', $user->username) }}">
                @error('username') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="account-email" class="{{ $labelClass }}">Email</label>
                @if(auth()->user()->isAdmin())
                    <input id="account-email" type="email" name="email" class="{{ $inputClass }} {{ $errors->has('email') ? 'border-red-400' : '' }}" value="{{ old('email', $user->email) }}">
                    @error('email') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
                @else
                    @php
                        [$localPart, $domainPart] = array_pad(explode('@', $user->email, 2), 2, '');
                    @endphp
                    <div class="flex overflow-hidden rounded-xl border {{ $errors->has('email_local') ? 'border-red-400' : 'border-slate-200' }} focus-within:border-brand-500 focus-within:ring-2 focus-within:ring-brand-500/20">
                        <input id="account-email" type="text" name="email_local" class="min-h-11 min-w-0 flex-1 border-0 px-3 text-sm outline-none focus:ring-0" value="{{ old('email_local', $localPart) }}">
                        <span class="flex shrink-0 items-center border-l border-slate-200 bg-slate-50 px-3 text-xs font-semibold text-slate-500">@ {{ $domainPart }}</span>
                    </div>
                    <span class="{{ $helpClass }}">Domain email tidak dapat diubah.</span>
                    @error('email_local') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
                @endif
            </div>

            <div>
                <label for="personal-email" class="{{ $labelClass }}">Email Pemulihan <span class="font-normal text-slate-400">(penting)</span></label>
                <input id="personal-email" type="email" name="personal_email" class="{{ $inputClass }} {{ $errors->has('personal_email') ? 'border-red-400' : '' }}" value="{{ old('personal_email', $user->personal_email) }}">
                @error('personal_email') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="account-role" class="{{ $labelClass }}">Role</label>
                <input id="account-role" type="text" class="{{ $inputClass }}" value="{{ $user->roleRelation ? $user->roleRelation->display_name : ucwords(str_replace('_', ' ', $user->role)) }}" disabled>
                <span class="{{ $helpClass }}">Hubungi administrator jika role tidak sesuai.</span>
            </div>

            <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-sm font-bold text-white shadow-sm hover:bg-brand-700">
                <i class="fas fa-save" aria-hidden="true"></i> Simpan Perubahan
            </button>
        </form>
    </section>

    <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="flex items-center gap-3 border-b border-slate-200 bg-slate-50 px-4 py-4 sm:px-5">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600"><i class="fas fa-lock" aria-hidden="true"></i></span>
            <div class="min-w-0">
                <h2 class="text-sm font-extrabold text-slate-900 sm:text-base">Keamanan Password</h2>
                <p class="mt-0.5 text-[11px] text-slate-500">Perbarui password secara berkala.</p>
            </div>
        </header>
        <form action="{{ route('account.change-password') }}" method="POST" class="space-y-4 p-4 sm:p-5">
            @csrf
            @method('PUT')

            @foreach([
                ['current_password', 'Password Lama'],
                ['new_password', 'Password Baru'],
                ['new_password_confirmation', 'Konfirmasi Password Baru'],
            ] as [$field, $fieldLabel])
                <div>
                    <label for="{{ $field }}" class="{{ $labelClass }}">{{ $fieldLabel }}</label>
                    <div class="flex overflow-hidden rounded-xl border {{ $errors->has($field) ? 'border-red-400' : 'border-slate-200' }} focus-within:border-brand-500 focus-within:ring-2 focus-within:ring-brand-500/20">
                        <input type="password" id="{{ $field }}" name="{{ $field }}" class="min-h-11 min-w-0 flex-1 border-0 px-3 text-sm outline-none placeholder:text-slate-400 focus:ring-0" placeholder="••••••••••••">
                        <button class="{{ $toggleClass }}" type="button" data-password-toggle data-target="{{ $field }}" aria-label="Tampilkan {{ strtolower($fieldLabel) }}"><i class="fas fa-eye-slash" aria-hidden="true"></i></button>
                    </div>
                    @if($field === 'new_password')<span class="{{ $helpClass }}">Gunakan minimal 8 karakter.</span>@endif
                    @error($field) <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
                </div>
            @endforeach

            <div class="flex items-start gap-2 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs leading-5 text-amber-800">
                <i class="fas fa-info-circle mt-0.5 shrink-0" aria-hidden="true"></i>
                Sesi akan berakhir otomatis setelah password diperbarui.
            </div>

            <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-amber-500 px-4 text-sm font-bold text-white shadow-sm hover:bg-amber-600">
                <i class="fas fa-key" aria-hidden="true"></i> Update Password Sekarang
            </button>
        </form>
    </section>

    @if(auth()->user()->isAdmin() || auth()->user()->isKetuaPKBM())
        <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2 xl:col-span-1">
            <header class="flex items-center gap-3 border-b border-slate-200 bg-slate-50 px-4 py-4 sm:px-5">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600"><i class="fas fa-shield-alt" aria-hidden="true"></i></span>
                <div class="min-w-0">
                    <h2 class="text-sm font-extrabold text-slate-900 sm:text-base">Keamanan Pemulihan</h2>
                    <p class="mt-0.5 text-[11px] text-slate-500">Pertanyaan keamanan dan PIN darurat.</p>
                </div>
            </header>
            <form action="{{ route('account.update-security') }}" method="POST" class="space-y-4 p-4 sm:p-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="security-question" class="{{ $labelClass }}">Pertanyaan Keamanan Baru</label>
                    <select id="security-question" name="security_question" class="{{ $inputClass }} {{ $errors->has('security_question') ? 'border-red-400' : '' }}" required>
                        <option value="" disabled selected>Pilih pertanyaan...</option>
                        @foreach(['Apa nama SD Anda?', 'Siapa nama teman masa kecil Anda?', 'Di kota mana Anda bertemu pasangan Anda?', 'Apa nama hewan peliharaan pertama Anda?', 'Apa judul film favorit Anda?'] as $question)
                            <option value="{{ $question }}" {{ old('security_question', $user->security_question) === $question ? 'selected' : '' }}>{{ $question }}</option>
                        @endforeach
                    </select>
                    @error('security_question') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="security-answer" class="{{ $labelClass }}">Jawaban</label>
                    <input id="security-answer" type="text" name="security_answer" class="{{ $inputClass }} {{ $errors->has('security_answer') ? 'border-red-400' : '' }}" placeholder="Jawaban baru Anda" required>
                    <span class="{{ $helpClass }}">Huruf besar dan kecil dianggap sama.</span>
                    @error('security_answer') <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach([
                        ['security_pin', 'PIN Keamanan Baru'],
                        ['security_pin_confirmation', 'Konfirmasi PIN'],
                    ] as [$field, $fieldLabel])
                        <div class="min-w-0">
                            <label for="{{ $field }}" class="{{ $labelClass }}">{{ $fieldLabel }}</label>
                            <div class="flex overflow-hidden rounded-xl border {{ $errors->has($field) ? 'border-red-400' : 'border-slate-200' }} focus-within:border-brand-500 focus-within:ring-2 focus-within:ring-brand-500/20">
                                <input type="password" id="{{ $field }}" name="{{ $field }}" class="min-h-11 min-w-0 flex-1 border-0 px-3 text-sm outline-none focus:ring-0" minlength="6" maxlength="6" pattern="\d{6}" inputmode="numeric" placeholder="••••••" required>
                                <button class="{{ $toggleClass }}" type="button" data-password-toggle data-target="{{ $field }}" aria-label="Tampilkan {{ strtolower($fieldLabel) }}"><i class="fas fa-eye-slash" aria-hidden="true"></i></button>
                            </div>
                            @if($field === 'security_pin')<span class="{{ $helpClass }}">Enam digit angka.</span>@endif
                            @error($field) <span class="{{ $errorClass }}">{{ $message }}</span> @enderror
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-slate-200 pt-4">
                    <label for="current_password_security" class="mb-1.5 block text-xs font-bold text-red-700">Password Saat Ini</label>
                    <div class="flex overflow-hidden rounded-xl border border-red-300 focus-within:ring-2 focus-within:ring-red-500/20">
                        <input type="password" id="current_password_security" name="current_password" class="min-h-11 min-w-0 flex-1 border-0 px-3 text-sm outline-none focus:ring-0" placeholder="Otorisasi perubahan keamanan" required>
                        <button class="{{ $toggleClass }} text-red-600" type="button" data-password-toggle data-target="current_password_security" aria-label="Tampilkan password saat ini"><i class="fas fa-eye-slash" aria-hidden="true"></i></button>
                    </div>
                    <span class="{{ $errorClass }}"><i class="fas fa-exclamation-triangle mr-1" aria-hidden="true"></i>Wajib untuk mengubah pengaturan keamanan.</span>
                    @if($errors->has('current_password_security'))<span class="{{ $errorClass }}">{{ $errors->first('current_password_security') }}</span>@endif
                </div>

                <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-sm font-bold text-white shadow-sm hover:bg-brand-700">
                    <i class="fas fa-user-shield" aria-hidden="true"></i> Perbarui Keamanan
                </button>
            </form>
        </section>
    @endif
</div>
@endsection
