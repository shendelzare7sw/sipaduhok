<!-- ==================== CTA SECTION ==================== -->
<section class="py-20" style="background: linear-gradient(135deg, #165fac 0%, #287f3b 100%);">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
            Siap Bergabung Bersama Kami?
        </h2>
        <p class="text-white/90 text-lg mb-8 max-w-2xl mx-auto">
            Daftarkan putra-putri Anda sekarang dan berikan mereka pendidikan terbaik untuk masa depan yang cerah.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('pendaftaran') }}"
                class="inline-flex items-center justify-center px-8 py-4 bg-white text-primary hover:bg-cream font-semibold rounded-full transition-all duration-300 hover:-translate-y-1 shadow-lg">
                Daftar Sekarang
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
            <a href="{{ url('/kontak') }}"
                class="inline-flex items-center justify-center px-8 py-4 bg-transparent border-2 border-white text-white hover:bg-white hover:text-primary font-semibold rounded-full transition-all duration-300">
                Hubungi Kami
            </a>
        </div>
    </div>
</section>
