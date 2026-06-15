@php
    $statCards = [
        [
            'title' => 'Total Guru',
            'value' => $stats['totalTenagaPendidik'],
            'description' => 'Tenaga Pendidik',
            'icon' => 'fas fa-chalkboard-teacher',
            'gradient' => 'bg-gradient-green',
        ],
        [
            'title' => 'Total Siswa',
            'value' => $stats['totalSiswa'],
            'description' => 'Siswa Terdaftar',
            'icon' => 'fas fa-user-graduate',
            'gradient' => 'bg-gradient-blue',
        ],
        [
            'title' => 'User Aktif',
            'value' => $stats['totalUserAktif'],
            'description' => 'Akun dapat login',
            'icon' => 'fas fa-user-check',
            'gradient' => 'bg-gradient-purple',
        ],
        [
            'title' => 'Non-Aktif',
            'value' => $stats['totalUserNonAktif'],
            'description' => 'Perlu peninjauan',
            'icon' => 'fas fa-user-times',
            'gradient' => 'bg-gradient-red',
        ],
    ];
@endphp

<div class="row mb-4">
    @foreach($statCards as $card)
        <div class="col-md-3">
            <div class="stat-card {{ $card['gradient'] }}">
                <div class="stat-content">
                    <div class="stat-title">{{ $card['title'] }}</div>
                    <div class="stat-number">{{ $card['value'] }}</div>
                    <div class="stat-desc">{{ $card['description'] }}</div>
                </div>
                <div class="stat-icon-bg">
                    <i class="{{ $card['icon'] }}"></i>
                </div>
            </div>
        </div>
    @endforeach
</div>
