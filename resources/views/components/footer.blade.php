@php
    use App\Models\LandingPage;
    
    $footerPage = LandingPage::where('slug', 'footer')->with('sections')->first();
    
    $aboutSection = $footerPage?->getSection('about');
    $about = $aboutSection?->content ?? [];
    
    $quickLinksSection = $footerPage?->getSection('quick_links');
    $quickLinksContent = $quickLinksSection?->content ?? [];
    $quickLinks = $quickLinksContent['items'] ?? [];
    $quickLinksHeader = $quickLinksContent['header'] ?? [];
    
    $programLinksSection = $footerPage?->getSection('program_links');
    $programLinksContent = $programLinksSection?->content ?? [];
    $programLinks = $programLinksContent['items'] ?? [];
    $programLinksHeader = $programLinksContent['header'] ?? [];
    
    $contactSection = $footerPage?->getSection('contact_info');
    $contact = $contactSection?->content ?? [];
    
    $bottomSection = $footerPage?->getSection('bottom');
    $bottom = $bottomSection?->content ?? [];

    $about = array_merge($about, [
        'logo' => 'img/logo/logo.png',
        'name' => 'Sipadu Homescholing',
        'description' => 'Layanan homeschooling dengan akses LMS dan pembayaran tagihan orang tua dalam satu portal.',
    ]);
    $quickLinksHeader = ['title' => 'Navigasi'];
    $quickLinks = [
        ['label' => 'Beranda', 'url' => '/'],
        ['label' => 'Homeschooling', 'url' => '/program-homeschooling'],
        ['label' => 'Pendaftaran', 'url' => '/pendaftaran'],
        ['label' => 'Kontak', 'url' => '/kontak'],
    ];
    $programLinksHeader = ['title' => 'Portal'];
    $programLinks = [
        ['label' => 'Masuk LMS', 'url' => '/login'],
        ['label' => 'Pembayaran Orang Tua', 'url' => '/login'],
    ];
    $contact = array_merge($contact, [
        'title' => 'Hubungi Kami',
        'address' => 'Komplek Ruko Reni Jaya Baru Jl.Ketapang III Blok AF 5 No 22-23 Pamulang Barat, Tangerang Selatan',
    ]);
@endphp

<footer class="bg-gradient-to-br from-gray-900 to-gray-800 text-white pt-16 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            {{-- About Section --}}
            <div>
                <div class="mb-6">
                    <img src="{{ asset($about['logo'] ?? 'img/logo.png') }}" alt="Logo Sipadu Homescholing" class="h-16 mb-4">
                    <h3 class="text-xl font-bold mb-2">{{ $about['name'] ?? 'House Of Knowledge' }}</h3>
                </div>
                <p class="text-gray-300 text-sm leading-relaxed mb-4">
                    {{ $about['description'] ?? 'Tempat mencetak penerus bangsa yang berkualitas dan berprestasi di segala bidang yang dapat bersaing di dunia internasional.' }}
                </p>
            </div>

            {{-- Quick Links --}}
            <div>
                <h3 class="text-lg font-bold mb-6 text-blue-400">{{ $quickLinksHeader['title'] ?? 'Link Cepat' }}</h3>
                <ul class="space-y-3">
                    @foreach($quickLinks as $link)
                        <li><a href="{{ url($link['url'] ?? '#') }}" class="text-gray-300 hover:text-blue-400 transition duration-300 flex items-center"><span class="mr-2">›</span> {{ $link['label'] ?? '' }}</a></li>
                    @endforeach
                </ul>
            </div>


            {{-- Program --}}
            <div>
                <h3 class="text-lg font-bold mb-6 text-green-400">{{ $programLinksHeader['title'] ?? 'Program Pendidikan' }}</h3>
                <ul class="space-y-3">
                    @foreach($programLinks as $link)
                        <li><a href="{{ url($link['url'] ?? '#') }}" class="text-gray-300 hover:text-green-400 transition duration-300 flex items-center"><span class="mr-2">›</span> {{ $link['label'] ?? '' }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Contact Info --}}
            <div>
                <h3 class="text-lg font-bold mb-6 text-orange-400">{{ $contact['title'] ?? 'Hubungi Kami' }}</h3>
                <ul class="space-y-4">
                    @if(!empty($contact['address']))
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-400 mr-3 flex-shrink-0 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="text-gray-300 text-sm">
                            {{ $contact['address'] ?? '' }}
                        </span>
                    </li>
                    @endif
                    <li class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span class="text-gray-300 text-sm">{{ $contact['phone'] ?? '+62 858-1125-8534' }}</span>
                    </li>
                    <li class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span class="text-gray-300 text-sm">{{ $contact['email'] ?? 'hokhomeschool@gmail.com' }}</span>
                    </li>
                    <li class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-gray-300 text-sm">{{ $contact['hours'] ?? 'Senin - Jumat: 08:00 - 14:00' }}</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Bottom Footer --}}
        <div class="border-t border-gray-700 pt-8 mt-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm text-center md:text-left mb-4 md:mb-0">
                    &copy; {{ date('Y') }} Sipadu Homescholing. All rights reserved.
                </p>
                <div class="flex space-x-6">
                    <a href="{{ (!empty($bottom['privacy_url']) && $bottom['privacy_url'] !== '#') ? $bottom['privacy_url'] : route('kebijakan-privasi') }}" class="text-gray-400 hover:text-blue-400 text-sm transition duration-300">{{ $bottom['privacy_label'] ?? 'Kebijakan Privasi' }}</a>
                    <a href="{{ (!empty($bottom['terms_url']) && $bottom['terms_url'] !== '#') ? $bottom['terms_url'] : route('syarat-ketentuan') }}" class="text-gray-400 hover:text-blue-400 text-sm transition duration-300">{{ $bottom['terms_label'] ?? 'Syarat & Ketentuan' }}</a>
                    <a href="{{ (!empty($bottom['sitemap_url']) && $bottom['sitemap_url'] !== '#') ? $bottom['sitemap_url'] : route('sitemap') }}" class="text-gray-400 hover:text-blue-400 text-sm transition duration-300">{{ $bottom['sitemap_label'] ?? 'Sitemap' }}</a>
                </div>
            </div>
        </div>
    </div>
</footer>
