@php
    $content = $content ?? [];
    $features = isset($content['features']) ? explode('|', $content['features']) : ['Ijazah Resmi', 'Jadwal Fleksibel', 'Kelas Kecil', 'Bimbingan Intensif'];
@endphp

<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="relative">
                <img src="{{ asset($content['image'] ?? 'img/sd-main.jpg') }}" alt="SD"
                    class="rounded-2xl shadow-xl w-full h-[400px] object-cover"
                    onerror="this.src='https://via.placeholder.com/800x400/165fac/ffffff?text=Paket+A+(SD)'">

                <div
                    class="absolute -bottom-6 -right-6 bg-[#165fac] text-white p-6 rounded-2xl shadow-lg hidden md:block">
                    <p class="text-2xl font-bold">{{ $content['label'] ?? 'Paket A' }}</p>
                    <p class="text-sm">{{ $content['label_subtitle'] ?? 'Setara SD' }}</p>
                </div>
            </div>
            <!-- Rest of content -->
            <div>
                <span
                    class="inline-block bg-[#165fac]/20 text-[#165fac] px-4 py-2 rounded-full text-sm font-semibold mb-4">{{ $content['badge'] ?? 'Pendidikan Kesetaraan' }}</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">
                    {{ $content['title'] ?? 'Paket A (Setara SD)' }}</h2>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    {{ $content['description_1'] ?? 'Program Paket A adalah program pendidikan kesetaraan yang setara dengan Sekolah Dasar (SD). Program ini diperuntukkan bagi anak-anak yang tidak dapat mengikuti pendidikan formal karena berbagai alasan.' }}
                </p>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    {{ $content['description_2'] ?? 'Dengan kurikulum yang disesuaikan dan metode pembelajaran yang fleksibel, siswa dapat belajar sesuai dengan kecepatan masing-masing sambil tetap mencapai kompetensi yang diharapkan.' }}
                </p>
                <div class="grid grid-cols-2 gap-4">
                    @foreach($features as $feature)
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#287f3b]/10 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#287f3b]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <span class="text-gray-700 font-medium">{{ $feature }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mata Pelajaran -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span
                class="inline-block bg-[#165fac]/10 text-[#165fac] px-4 py-2 rounded-full text-sm font-semibold mb-4">Kurikulum</span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Mata Pelajaran</h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 p-4">
            <div
                class="bg-white rounded-3xl p-6 flex flex-col items-center justify-center shadow-sm hover:shadow-md transition-shadow cursor-pointer border border-gray-100">
                <div
                    class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mb-4 text-orange-600 text-2xl">
                    <i class="fas fa-book"></i>
                </div>
                <h3 class="font-bold text-gray-800 text-center">Bahasa Indonesia</h3>
            </div>
            <div
                class="bg-white rounded-3xl p-6 flex flex-col items-center justify-center shadow-sm hover:shadow-md transition-shadow cursor-pointer border border-gray-100">
                <div
                    class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-4 text-blue-600 text-2xl">
                    <i class="fas fa-calculator"></i>
                </div>
                <h3 class="font-bold text-gray-800 text-center">Matematika</h3>
            </div>
            <div
                class="bg-white rounded-3xl p-6 flex flex-col items-center justify-center shadow-sm hover:shadow-md transition-shadow cursor-pointer border border-gray-100">
                <div
                    class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mb-4 text-emerald-600 text-2xl">
                    <i class="fas fa-microscope"></i>
                </div>
                <h3 class="font-bold text-gray-800 text-center">IPA</h3>
            </div>
            <div
                class="bg-white rounded-3xl p-6 flex flex-col items-center justify-center shadow-sm hover:shadow-md transition-shadow cursor-pointer border border-gray-100">
                <div
                    class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center mb-4 text-amber-600 text-2xl">
                    <i class="fas fa-globe-asia"></i>
                </div>
                <h3 class="font-bold text-gray-800 text-center">IPS</h3>
            </div>
            <div
                class="bg-white rounded-3xl p-6 flex flex-col items-center justify-center shadow-sm hover:shadow-md transition-shadow cursor-pointer border border-gray-100">
                <div
                    class="w-16 h-16 bg-rose-100 rounded-2xl flex items-center justify-center mb-4 text-rose-600 text-2xl">
                    <i class="fas fa-mosque"></i>
                </div>
                <h3 class="font-bold text-gray-800 text-center">Agama</h3>
            </div>
            <div
                class="bg-white rounded-3xl p-6 flex flex-col items-center justify-center shadow-sm hover:shadow-md transition-shadow cursor-pointer border border-gray-100">
                <div
                    class="w-16 h-16 bg-sky-100 rounded-2xl flex items-center justify-center mb-4 text-sky-600 text-2xl">
                    <i class="fas fa-language"></i>
                </div>
                <h3 class="font-bold text-gray-800 text-center">B. Inggris</h3>
            </div>
        </div>
    </div>
</section>

<!-- Syarat -->
<section class="py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Syarat Pendaftaran</h2>
        </div>
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <ul class="space-y-4">
                <li class="flex items-start gap-4">
                    <div
                        class="w-8 h-8 bg-[#165fac] text-white rounded-full flex items-center justify-center font-bold flex-shrink-0">
                        1</div>
                    <div>
                        <h4 class="font-semibold text-gray-800">Usia Minimal 7 Tahun</h4>
                        <p class="text-gray-600 text-sm">Calon siswa berusia minimal 7 tahun pada saat mendaftar</p>
                    </div>
                </li>
                <li class="flex items-start gap-4">
                    <div
                        class="w-8 h-8 bg-[#165fac] text-white rounded-full flex items-center justify-center font-bold flex-shrink-0">
                        2</div>
                    <div>
                        <h4 class="font-semibold text-gray-800">Fotokopi Akta Kelahiran</h4>
                        <p class="text-gray-600 text-sm">Menyerahkan fotokopi akta kelahiran yang dilegalisir</p>
                    </div>
                </li>
                <li class="flex items-start gap-4">
                    <div
                        class="w-8 h-8 bg-[#165fac] text-white rounded-full flex items-center justify-center font-bold flex-shrink-0">
                        3</div>
                    <div>
                        <h4 class="font-semibold text-gray-800">Fotokopi Kartu Keluarga</h4>
                        <p class="text-gray-600 text-sm">Menyerahkan fotokopi KK yang masih berlaku</p>
                    </div>
                </li>
                <li class="flex items-start gap-4">
                    <div
                        class="w-8 h-8 bg-[#165fac] text-white rounded-full flex items-center justify-center font-bold flex-shrink-0">
                        4</div>
                    <div>
                        <h4 class="font-semibold text-gray-800">Pas Foto 3x4</h4>
                        <p class="text-gray-600 text-sm">Menyerahkan pas foto berwarna ukuran 3x4 sebanyak 4 lembar</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-16" style="background: linear-gradient(135deg, #165fac 0%, #287f3b 100%);">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold text-white mb-4">{{ $content['cta_title'] ?? 'Mulai Pendidikan Anda Sekarang' }}
        </h2>
        <p class="text-white/90 mb-8">
            {{ $content['cta_description'] ?? 'Dapatkan ijazah resmi setara SD dengan program Paket A kami.' }}</p>
        <a href="/ppdb"
            class="inline-block px-8 py-4 bg-white text-[#165fac] font-semibold rounded-full hover:bg-gray-100 transition"
            style="cursor: pointer;">
            Daftar Sekarang
        </a>
    </div>
</section>