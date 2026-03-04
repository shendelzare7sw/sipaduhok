<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peningkatan Keamanan Akun - PKBM House Of Knowledge</title>
    
    <!-- CDN Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind.config.js') }}"></script>

    @vite(['resources/css/pages/login.css'])
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
</head>
<body>
    <div class="animated-bg" style="background: linear-gradient(-45deg, #165fac, #287f3b, #d45930);">
        <div class="login-container max-w-2xl mx-auto">
            <div class="glass-effect rounded-3xl shadow-2xl overflow-hidden slide-in bg-white p-8 md:p-12 relative border-t-4 border-[#165fac]">
                
                <div class="mb-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 text-[#165fac] mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Peningkatan Keamanan</h2>
                    <p class="text-gray-600">Sebagai Admin/Ketua PKBM, Anda diwajibkan mengatur Pertanyaan Keamanan & PIN untuk memulihkan akun Anda di masa mendatang saat darurat.</p>
                </div>

                <form method="POST" action="{{ route('admin.security.setup.store') }}" class="space-y-5">
                    @csrf

                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-4 text-sm font-medium">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Security Question -->
                    <div>
                        <label for="security_question" class="block text-sm font-semibold text-gray-700 mb-2">Pilih Pertanyaan Keamanan</label>
                        <select id="security_question" name="security_question" class="input-field w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#165fac] focus:outline-none bg-white" required>
                            <option value="" disabled selected>Pilih pertanyaan...</option>
                            <option value="Apa nama SD Anda?">Apa nama SD Anda?</option>
                            <option value="Siapa nama teman masa kecil Anda?">Siapa nama teman masa kecil Anda?</option>
                            <option value="Di kota mana Anda bertemu pasangan Anda?">Di kota mana Anda bertemu pasangan Anda?</option>
                            <option value="Apa nama hewan peliharaan pertama Anda?">Apa nama hewan peliharaan pertama Anda?</option>
                            <option value="Apa judul film favorit Anda?">Apa judul film favorit Anda?</option>
                        </select>
                    </div>

                    <!-- Answer -->
                    <div>
                        <label for="security_answer" class="block text-sm font-semibold text-gray-700 mb-2">Jawaban (Disimpan Terenkripsi)</label>
                        <input type="text" id="security_answer" name="security_answer"
                            class="input-field w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#165fac] focus:outline-none"
                            placeholder="Jawaban Anda" required maxlength="255">
                        <p class="text-xs text-gray-500 mt-1">Harap catat baik-baik. Jawaban ini bersifat <em>case-insensitive</em> saat pemulihan nantinya.</p>
                    </div>

                    <!-- PIN -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="security_pin" class="block text-sm font-semibold text-gray-700 mb-2">Buat 6-Digit PIN</label>
                            <input type="password" id="security_pin" name="security_pin" maxlength="6" pattern="\d{6}"
                                class="input-field w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#165fac] focus:outline-none text-center tracking-[0.5em]"
                                placeholder="••••••" required>
                        </div>
                        <div>
                            <label for="security_pin_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi PIN</label>
                            <input type="password" id="security_pin_confirmation" name="security_pin_confirmation" maxlength="6" pattern="\d{6}"
                                class="input-field w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#165fac] focus:outline-none text-center tracking-[0.5em]"
                                placeholder="••••••" required>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-xl mt-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>PENTING:</strong> Simpan jawaban dan PIN ini dengan aman. Jika Anda lupa kredensial login, data ini adalah satu-satunya cara Anda untuk memulihkan akun secara mandiri tanpa harus intervensi langsung dari Server Database.
                                </p>
                            </div>
                        </div>
                    </div>

                    <button type="submit" 
                        class="w-full bg-[#165fac] hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg transition-all duration-300 mt-6">
                        Simpan Pengaturan Keamanan
                    </button>
                    
                </form>

                <form method="POST" action="{{ route('logout') }}" class="mt-4 text-center">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 hover:text-red-500 underline transition">
                        Batal dan Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
