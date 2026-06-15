@php
    $quickLinkGroups = [
        [
            'id' => 'tab-akdmk',
            'label' => 'Akademik',
            'active' => true,
            'links' => [
                ['url' => route('admin.kelas.index'), 'icon' => 'fas fa-school text-primary', 'label' => 'Data Kelas'],
                ['url' => route('admin.jadwal-pelajaran.index'), 'icon' => 'fas fa-calendar-alt text-success', 'label' => 'Jadwal'],
                ['url' => route('admin.mata-pelajaran.index'), 'icon' => 'fas fa-book text-warning', 'label' => 'Mata Pelajaran'],
                ['url' => route('admin.akademik.kalender.index'), 'icon' => 'fas fa-calendar-check text-info', 'label' => 'Kalender'],
            ],
        ],
        [
            'id' => 'tab-kuang',
            'label' => 'Keuangan',
            'active' => false,
            'links' => [
                ['url' => route('admin.keuangan.tagihan.index'), 'icon' => 'fas fa-file-invoice-dollar text-warning', 'label' => 'Daftar Tagihan'],
                ['url' => route('admin.keuangan.pembayaran.index'), 'icon' => 'fas fa-money-bill-wave text-success', 'label' => 'Pembayaran'],
                ['url' => route('admin.keuangan.laporan.index'), 'icon' => 'fas fa-clipboard-list text-primary', 'label' => 'Lap. Keuangan'],
                ['url' => route('admin.keuangan.info-pembayaran.index'), 'icon' => 'fas fa-cogs text-secondary', 'label' => 'Config Payment'],
            ],
        ],
        [
            'id' => 'tab-sistem',
            'label' => 'Sistem',
            'active' => false,
            'links' => [
                ['url' => route('admin.users.siswa'), 'icon' => 'fas fa-user-graduate text-primary', 'label' => 'Data Siswa'],
                ['url' => route('admin.users.tenaga-pendidik'), 'icon' => 'fas fa-chalkboard-teacher text-success', 'label' => 'Tenaga Pendidik'],
                ['url' => route('admin.lms-settings.index'), 'icon' => 'fas fa-sliders-h text-secondary', 'label' => 'Pengaturan LMS'],
                ['url' => route('admin.ai-settings.index'), 'icon' => 'fas fa-robot text-info', 'label' => 'Setting AI'],
            ],
        ],
    ];
@endphp

<div class="dashboard-card">
    <div class="card-header-clean border-bottom-0 pb-2">
        <h5 class="card-title-clean">
            <i class="fas fa-th-large card-title-icon"></i> Akses Modul Utama
        </h5>
    </div>

    <div class="px-2 pb-2 border-bottom quick-link-tabs-wrap">
        <ul class="nav nav-pills nav-justified custom-nav-pills flex-column flex-sm-row admin-dashboard-tabs"
            role="tablist">
            @foreach($quickLinkGroups as $group)
                <li class="nav-item">
                    <button type="button"
                        class="nav-link py-2 px-1 {{ $group['active'] ? 'active' : '' }}"
                        role="tab"
                        data-bs-toggle="tab"
                        data-bs-target="#{{ $group['id'] }}"
                        aria-selected="{{ $group['active'] ? 'true' : 'false' }}">
                        {{ $group['label'] }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="tab-content p-0 bg-transparent border-0 shadow-none">
        @foreach($quickLinkGroups as $group)
            <div class="tab-pane fade {{ $group['active'] ? 'show active' : '' }}"
                id="{{ $group['id'] }}"
                role="tabpanel">
                <div class="quick-links-grid align-content-start pb-4">
                    @foreach($group['links'] as $link)
                        <a href="{{ $link['url'] }}" class="quick-link-item">
                            <i class="{{ $link['icon'] }}"></i>
                            <span class="quick-link-text">{{ $link['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
