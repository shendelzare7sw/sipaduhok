<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Password Baru - PKBM House Of Knowledge</title>
    
    <!-- CDN Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind.config.js') }}"></script>

    @vite(['resources/css/pages/login.css', 'resources/js/pages/auth.js'])
    
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
                
                <!-- Form Header -->
                <div class="mb-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 text-[#165fac] mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Buat Password Baru</h2>
                    <p class="text-gray-600">Masukkan password baru untuk mengamankan kembali akun Anda.</p>
                </div>

                <form method="POST" action="{{ route('password.reset.ticket.submit') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <!-- Error Messages -->
                    @if(session('error'))
                        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-4 text-sm font-medium flex items-start gap-2">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- Password Baru -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password Baru</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" minlength="8" required autocomplete="new-password"
                                class="input-field w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#165fac] focus:outline-none pr-12 @error('password') border-red-500 @enderror"
                                placeholder="Minimal 8 karakter">
                            <button type="button" onclick="togglePasswordVisibility('password')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition">
                                <svg class="w-5 h-5" id="eye-icon-password" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" minlength="8" required autocomplete="new-password"
                                class="input-field w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#165fac] focus:outline-none pr-12"
                                placeholder="Ulangi password baru">
                            <button type="button" onclick="togglePasswordVisibility('password_confirmation')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition">
                                <svg class="w-5 h-5" id="eye-icon-password_confirmation" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                        class="w-full bg-gradient-to-r from-[#165fac] to-[#287f3b] hover:from-[#0d3a6b] hover:to-[#1e5c2b] text-white font-bold py-4 rounded-xl shadow-lg transition-all duration-300 mt-6 flex justify-center items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        Simpan Password Baru
                    </button>
                    
                    <p class="text-xs text-center text-gray-500 mt-4">
                        <svg class="w-4 h-4 inline-block text-green-500 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        Tautan ini bersifat unik dan hanya berlaku satu kali pemakaian.
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
