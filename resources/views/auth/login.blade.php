<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PKBM House Of Knowledge</title>
    
    <!-- CDN Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind.config.js') }}"></script>

    @vite(['resources/css/pages/login.css'])
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
</head>
<body>

    <!-- Floating Background Shapes -->
    <div class="floating-shape bg-yellow-300 rounded-full"></div>
    <div class="floating-shape bg-orange-400 rounded-full"></div>
    <div class="floating-shape bg-yellow-400 rounded-full"></div>

    <!-- Main Content -->
    <div class="animated-bg">
        <div class="login-container">
            <!-- Main Container -->
            <div class="glass-effect rounded-3xl shadow-2xl overflow-hidden slide-in">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                    
                    <!-- Left Side - Logo Section -->
                    <div class="hidden lg:flex flex-col justify-center items-center p-12 text-white relative overflow-hidden bg-gradient-to-br from-[#165fac] to-[#0e4b8a] min-h-[600px]">
                        
                        <div class="absolute top-10 right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
                        <div class="absolute bottom-10 left-10 w-60 h-60 bg-yellow-300/10 rounded-full blur-3xl"></div>
                        
                        <div class="relative z-10 text-center">
                            <div class="logo-container mb-8">
                                <div class="w-32 h-32 mx-auto bg-white rounded-3xl shadow-2xl flex items-center justify-center transform hover:rotate-12 transition-transform duration-300">
                                    <svg class="w-20 h-20 text-[#165fac]" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                    </svg>
                                </div>
                            </div>
                            <h1 class="text-4xl font-bold mb-4">Selamat Datang!</h1>
                            <p class="text-xl text-white/90">PKBM House Of Knowledge</p>
                        </div>
                    </div>

                    <!-- Right Side - Login Form -->
                    <div class="p-8 md:p-12 bg-white min-h-[600px] flex flex-col justify-center">

                        <!-- Back to Home Link -->
                        <a href="/" class="inline-flex items-center gap-2 text-[#165fac] font-semibold mb-8 hover:text-[#287f3b] transition text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Kembali ke Beranda
                        </a>

                        <!-- Mobile Logo -->
                        <div class="lg:hidden text-center mb-8">
                            <div class="w-16 h-16 mx-auto bg-gradient-to-br from-[#165fac] to-[#287f3b] rounded-2xl shadow-xl flex items-center justify-center mb-3">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-gray-800">PKBM House Of Knowledge</h2>
                        </div>
                        
                        <!-- Form Header -->
                        <div class="mb-8">
                            <h2 class="text-3xl font-bold text-gray-800 mb-2">Login Dashboard</h2>
                            <p class="text-gray-600">Login Akun untuk melanjutkan</p>
                        </div>

                        <!-- Login Form -->
                        <form method="POST" action="{{ route('login') }}" class="space-y-6">
                            @csrf

                            <!-- Error Messages -->
                            @if ($errors->any())
                                <div class="error-message">
                                    <strong>Login gagal!</strong> {{ $errors->first('login') }}
                                </div>
                            @endif

                            @if (session('status'))
                                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <!-- Username or Email Input -->
                            <div>
                                <label for="login" class="block text-sm font-semibold text-gray-700 mb-2">Username atau Email</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                        </svg>
                                    </div>
                                    <input type="text" id="login" name="login" value="{{ old('login') }}"
                                        class="input-field w-full pl-12 pr-4 py-3.5 border-2 border-gray-200 rounded-xl focus:border-[#165fac] focus:outline-none @error('login') input-error @enderror"
                                        placeholder="nama@email.com atau username" required autofocus>
                                </div>
                            </div>

                            <!-- Password Input -->
                            <div>
                                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <input type="password" id="password" name="password"
                                        class="input-field w-full pl-12 pr-12 py-3.5 border-2 border-gray-200 rounded-xl focus:border-[#165fac] focus:outline-none @error('password') input-error @enderror"
                                        placeholder="••••••••" required>
                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                        <button type="button" onclick="togglePassword()" class="password-toggle text-gray-400">
                                            <svg id="eyeOpen" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                            </svg>
                                            <svg id="eyeClosed" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"/>
                                                <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Remember Me Checkbox -->
                            <div class="flex items-center">
                                <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded border-gray-300 text-[#165fac] focus:ring-[#165fac]">
                                <label for="remember" class="ml-2 text-sm text-gray-700">Ingat saya</label>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" 
                                class="btn-primary w-full bg-gradient-to-r from-[#165fac] via-[#287f3b] to-[#d45930] text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 flex items-center justify-center gap-2">
                                <span>Masuk ke Dashboard</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-white/80 text-sm mt-6">
                <p>&copy; 2026 PKBM House Of Knowledge. All rights reserved.</p>
            </div>
        </div>
    </div>
    @vite(['resources/js/pages/login.js'])
</body>
</html>