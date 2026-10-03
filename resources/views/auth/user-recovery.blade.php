@extends('layouts.auth')

@section('title', 'Pemulihan Akun')

@section('content')
    <!-- Floating Background Shapes -->
    <div class="pointer-events-none fixed rounded-full opacity-10 bg-blue-500 left-[10%] top-[10%] h-64 w-64 animate-[float-putar_25s_ease-in-out_0s_infinite]"></div>
    <div class="pointer-events-none fixed rounded-full opacity-10 bg-green-500 right-[10%] top-[60%] h-96 w-96 animate-[float-putar_20s_ease-in-out_5s_infinite]"></div>
    <div class="pointer-events-none fixed rounded-full opacity-10 bg-teal-500 bottom-[10%] left-[20%] h-80 w-80 animate-[float-putar_30s_ease-in-out_10s_infinite]"></div>

    <!-- Main Content -->
    <div class="flex min-h-screen items-center justify-center p-5 bg-[linear-gradient(-45deg,#f3f4f6,#e5e7eb,#d1d5db,#f3f4f6)]">
        <div class="w-full max-w-2xl mx-auto">
            <!-- Main Container -->
            <div class="rounded-3xl shadow-2xl overflow-hidden animate-fade-up bg-white backdrop-blur-[20px] p-8 md:p-12 relative border border-t-4 border-[#165fac]">
                
                <!-- Back to Home Link -->
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-gray-500 font-semibold mb-6 hover:text-gray-800 transition text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali ke Login
                </a>

                <!-- Form Header -->
                <div class="mb-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 text-[#165fac] mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Pemulihan Akun</h2>
                    <p class="text-gray-600">Sistem akan membantu memulihkan akses Anda otomatis via Email.</p>
                </div>

                <!-- Recovery Form -->
                <form method="POST" action="{{ route('user.recovery.store') }}" class="space-y-5" x-data="pemulihanAkun">
                    @csrf
                    @php
                        // Kelas border dihitung di sini agar hanya SATU warna yang aktif
                        // (hindari bentrok border-gray-200 vs border-red-500 di class attribute).
                        $borderTipe  = $errors->has('tipe_recovery') ? 'border-red-500' : 'border-gray-200';
                        $borderIdent = $errors->has('identifier') ? 'border-red-500' : 'border-gray-200';
                    @endphp

                    <!-- Error Messages -->
                    @if(session('error'))
                        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-4 text-sm font-medium flex items-start gap-2">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif
                    
                    @if(session('info'))
                        <div class="bg-blue-50 border border-blue-200 text-blue-600 px-4 py-3 rounded-xl mb-4 text-sm font-medium flex items-start gap-2">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ session('info') }}</span>
                        </div>
                    @endif

                    <!-- Problem Type -->
                    <div>
                        <label for="tipe_recovery" class="block text-sm font-semibold text-gray-700 mb-2">Apa kendala yang Anda alami?</label>
                        <div class="relative">
                        <select id="tipe_recovery" name="tipe_recovery" x-model="tipe" class="appearance-none cursor-pointer !pr-14 transition-all duration-300 focus:-translate-y-0.5 focus:shadow-[0_10px_30px_rgba(22,95,172,0.2)] [&::-ms-clear]:hidden [&::-ms-reveal]:hidden w-full px-4 py-3 border-2 {{ $borderTipe }} rounded-xl focus:border-[#165fac] focus:outline-none bg-white" required>
                            <option value="lupa_password" selected>Saya lupa Password</option>
                            <option value="lupa_username">Saya lupa Username/Email</option>
                            <option value="lupa_keduanya">Saya lupa Keduanya (Username & Password)</option>
                        </select>
                        <svg class="pointer-events-none absolute right-5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                        @error('tipe_recovery')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Identity -->
                    <div>
                        <label for="identifier" class="block text-sm font-semibold text-gray-700 mb-2">Masukkan Identitas Anda</label>
                        <input type="text" id="identifier" name="identifier" value="{{ old('identifier') }}"
                            class="transition-all duration-300 focus:-translate-y-0.5 focus:shadow-[0_10px_30px_rgba(22,95,172,0.2)] [&::-ms-clear]:hidden [&::-ms-reveal]:hidden w-full px-4 py-3 border-2 {{ $borderIdent }} rounded-xl focus:border-[#165fac] focus:outline-none"
                            placeholder="Username / Email / NISN / NIP" :placeholder="placeholder" required autofocus>
                        <p class="text-xs text-gray-500 mt-1" x-text="bantuan">Masukkan Username, Email, NISN (Siswa), atau NIP (Guru/Pegawai) Anda.</p>
                        @error('identifier')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                        class="w-full bg-gradient-to-r from-[#165fac] to-[#287f3b] hover:from-[#0d3a6b] hover:to-[#1e5c2b] text-white font-bold py-4 rounded-xl shadow-lg transition-all duration-300 mt-6 flex justify-center items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Kirim Instruksi via Email
                    </button>
                    
                    <p class="text-xs text-center text-gray-500 mt-4">
                        Layanan ini terenkripsi dan dilindungi secara otomatis.
                    </p>
                </form>
            </div>

            <!-- Footer -->
            <div class="text-center text-gray-500 text-sm mt-6 mb-8">
                <p>&copy; 2026 PKBM House Of Knowledge.</p>
            </div>
        </div>
    </div>
@endsection
