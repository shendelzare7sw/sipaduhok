<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemulihan Akun - PKBM House Of Knowledge</title>
    
    <!-- CDN Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind.config.js') }}"></script>

    @vite(['resources/css/pages/login.css', 'resources/js/pages/auth.js'])

    <style>
        /* Fix dropdown arrow spacing */
        select.input-field {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1.25rem center !important;
            background-size: 1rem;
            padding-right: 3.5rem !important;
            cursor: pointer;
        }
    </style>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
</head>
<body>

    <!-- Floating Background Shapes -->
    <div class="floating-shape bg-blue-500 rounded-full" style="opacity: 0.1"></div>
    <div class="floating-shape bg-green-500 rounded-full" style="opacity: 0.1"></div>
    <div class="floating-shape bg-teal-500 rounded-full" style="opacity: 0.1"></div>

    <!-- Main Content -->
    <div class="animated-bg" style="background: linear-gradient(-45deg, #f3f4f6, #e5e7eb, #d1d5db, #f3f4f6);">
        <div class="login-container max-w-2xl mx-auto">
            <!-- Main Container -->
            <div class="glass-effect rounded-3xl shadow-2xl overflow-hidden slide-in bg-white p-8 md:p-12 relative border-t-4 border-[#165fac]">
                
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
                <form method="POST" action="{{ route('user.recovery.store') }}" class="space-y-5">
                    @csrf

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
                        <select id="tipe_recovery" name="tipe_recovery" class="input-field w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#165fac] focus:outline-none bg-white @error('tipe_recovery') border-red-500 @enderror" required onchange="updatePlaceholder()">
                            <option value="lupa_password" selected>Saya lupa Password</option>
                            <option value="lupa_username">Saya lupa Username/Email</option>
                            <option value="lupa_keduanya">Saya lupa Keduanya (Username & Password)</option>
                        </select>
                        @error('tipe_recovery')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Identity -->
                    <div>
                        <label for="identifier" class="block text-sm font-semibold text-gray-700 mb-2">Masukkan Identitas Anda</label>
                        <input type="text" id="identifier" name="identifier" value="{{ old('identifier') }}"
                            class="input-field w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#165fac] focus:outline-none @error('identifier') border-red-500 @enderror"
                            placeholder="Username / NISN / NIP" required autofocus>
                        <p class="text-xs text-gray-500 mt-1" id="identifierHelp">Masukkan Username, NISN (Siswa), atau NIP (Guru/Pegawai) Anda.</p>
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
</body>
</html>
