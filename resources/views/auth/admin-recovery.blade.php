@extends('layouts.auth')

@section('title', 'Pemulihan Akses Khusus')

@section('content')
    <!-- Floating Background Shapes - Different colors for Warning Theme -->
    <div class="pointer-events-none fixed rounded-full opacity-10 bg-red-500 left-[10%] top-[10%] h-64 w-64 animate-[float-putar_25s_ease-in-out_0s_infinite]"></div>
    <div class="pointer-events-none fixed rounded-full opacity-10 bg-orange-500 right-[10%] top-[60%] h-96 w-96 animate-[float-putar_20s_ease-in-out_5s_infinite]"></div>
    <div class="pointer-events-none fixed rounded-full opacity-10 bg-yellow-500 bottom-[10%] left-[20%] h-80 w-80 animate-[float-putar_30s_ease-in-out_10s_infinite]"></div>

    <!-- Main Content -->
    <div class="flex min-h-screen items-center justify-center p-5 bg-[linear-gradient(-45deg,#111827,#1f2937,#374151,#111827)]">
        <div class="w-full max-w-2xl mx-auto">
            <!-- Main Container -->
            <div class="rounded-3xl shadow-2xl overflow-hidden animate-fade-up bg-white backdrop-blur-[20px] p-8 md:p-12 relative border border-t-4 border-orange-500">
                
                <!-- Back to Home Link -->
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-gray-500 font-semibold mb-6 hover:text-gray-800 transition text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali ke Login
                </a>

                <!-- Form Header -->
                <div class="mb-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-orange-100 text-orange-500 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Pemulihan Akses Khusus</h2>
                    <p class="text-gray-600">Fitur ini hanya untuk memulihkan akun Administrator dan Ketua PKBM.</p>
                </div>

                <!-- Recovery Form -->
                <form method="POST" action="{{ route('admin.recovery.reset') }}" class="space-y-5">
                    @csrf

                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-4 text-sm font-medium">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Identity -->
                    <div>
                        <label for="identifier" class="block text-sm font-semibold text-gray-700 mb-2">Username atau Email Admin</label>
                        <input type="text" id="identifier" name="identifier" value="{{ old('identifier') }}"
                            class="transition-all duration-300 focus:-translate-y-0.5 focus:shadow-[0_10px_30px_rgba(22,95,172,0.2)] [&::-ms-clear]:hidden [&::-ms-reveal]:hidden w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:outline-none"
                            placeholder="admin1 / admin@sipaduhok.test" required autofocus>
                    </div>

                    <!-- Security Question Dropdown -->
                    <div>
                        <label for="security_question" class="block text-sm font-semibold text-gray-700 mb-2">Pilih Pertanyaan Keamanan Anda</label>
                        <select id="security_question" name="security_question" class="transition-all duration-300 focus:-translate-y-0.5 focus:shadow-[0_10px_30px_rgba(22,95,172,0.2)] [&::-ms-clear]:hidden [&::-ms-reveal]:hidden w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:outline-none bg-white" required>
                            <option value="" disabled selected>Pilih pertanyaan yang Anda simpan...</option>
                            <option value="Apa nama SD Anda?">Apa nama SD Anda?</option>
                            <option value="Siapa nama teman masa kecil Anda?">Siapa nama teman masa kecil Anda?</option>
                            <option value="Di kota mana Anda bertemu pasangan Anda?">Di kota mana Anda bertemu pasangan Anda?</option>
                            <option value="Apa nama hewan peliharaan pertama Anda?">Apa nama hewan peliharaan pertama Anda?</option>
                            <option value="Apa judul film favorit Anda?">Apa judul film favorit Anda?</option>
                        </select>
                    </div>

                    <!-- Security Answer -->
                    <div>
                        <label for="security_answer" class="block text-sm font-semibold text-gray-700 mb-2">Jawaban (Case-insensitive)</label>
                        <input type="text" id="security_answer" name="security_answer"
                            class="transition-all duration-300 focus:-translate-y-0.5 focus:shadow-[0_10px_30px_rgba(22,95,172,0.2)] [&::-ms-clear]:hidden [&::-ms-reveal]:hidden w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:outline-none"
                            placeholder="Masukkan jawaban Anda" required>
                    </div>

                    <!-- PIN -->
                    <div>
                        <label for="security_pin" class="block text-sm font-semibold text-gray-700 mb-2">6-Digit PIN Keamanan</label>
                        <input type="password" id="security_pin" name="security_pin" maxlength="6" pattern="\d{6}"
                            class="transition-all duration-300 focus:-translate-y-0.5 focus:shadow-[0_10px_30px_rgba(22,95,172,0.2)] [&::-ms-clear]:hidden [&::-ms-reveal]:hidden w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:outline-none text-center text-lg tracking-[0.5em]"
                            placeholder="••••••" required>
                        <p class="text-xs text-gray-500 mt-1">Hanya angka (0-9).</p>
                    </div>

                    <hr class="my-6 border-gray-200">

                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password Baru</label>
                        <div class="relative" x-data="{ lihat: false }">
                            <input type="password" :type="lihat ? 'text' : 'password'" id="password" name="password" minlength="8"
                                class="transition-all duration-300 focus:-translate-y-0.5 focus:shadow-[0_10px_30px_rgba(22,95,172,0.2)] [&::-ms-clear]:hidden [&::-ms-reveal]:hidden w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:outline-none pr-12"
                                placeholder="Minimal 8 karakter" required>
                            <button type="button" x-on:click="lihat = !lihat" :aria-label="lihat ? 'Sembunyikan password' : 'Tampilkan password'" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition">
                                <svg x-show="!lihat" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="lihat" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password Baru</label>
                        <div class="relative" x-data="{ lihat: false }">
                            <input type="password" :type="lihat ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" minlength="8"
                                class="transition-all duration-300 focus:-translate-y-0.5 focus:shadow-[0_10px_30px_rgba(22,95,172,0.2)] [&::-ms-clear]:hidden [&::-ms-reveal]:hidden w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:outline-none pr-12"
                                placeholder="Ulangi password baru" required>
                            <button type="button" x-on:click="lihat = !lihat" :aria-label="lihat ? 'Sembunyikan password' : 'Tampilkan password'" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition">
                                <svg x-show="!lihat" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="lihat" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                        class="w-full bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold py-4 rounded-xl shadow-lg transition-all duration-300 mt-6">
                        Reset Password Admin
                    </button>
                    
                    <p class="text-xs text-center text-gray-500 mt-4">
                        Jika Anda lupa jawaban atau PIN, Anda <strong>wajib</strong> menghubungi Developer Utama (Sysadmin) untuk mereset akun via akses server langsung.
                    </p>
                    <div class="text-right mt-2">
                        <a href="https://wa.me/6282113100791" target="_blank" class="text-xs font-semibold text-orange-500 hover:text-orange-600 underline">
                            Kontak Developer
                        </a>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="text-center text-white/50 text-sm mt-6 mb-8">
                <p>&copy; 2026 PKBM House Of Knowledge. Access Restricted Area.</p>
            </div>
        </div>
    </div>
@endsection
