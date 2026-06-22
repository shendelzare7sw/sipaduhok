@php
    $statCards = [
        [
            'title'       => 'Total Guru',
            'value'       => $stats['totalTenagaPendidik'],
            'description' => 'Tenaga Pendidik',
            'icon'        => 'fas fa-chalkboard-teacher',
            'iconClass'   => 'stat-icon-green',
        ],
        [
            'title'       => 'Total Siswa',
            'value'       => $stats['totalSiswa'],
            'description' => 'Siswa Terdaftar',
            'icon'        => 'fas fa-user-graduate',
            'iconClass'   => 'stat-icon-blue',
        ],
        [
            'title'       => 'User Aktif',
            'value'       => $stats['totalUserAktif'],
            'description' => 'Akun dapat login',
            'icon'        => 'fas fa-user-check',
            'iconClass'   => 'stat-icon-purple',
        ],
        [
            'title'       => 'Non-Aktif',
            'value'       => $stats['totalUserNonAktif'],
            'description' => 'Perlu peninjauan',
            'icon'        => 'fas fa-user-times',
            'iconClass'   => 'stat-icon-red',
        ],
    ];
@endphp

<div class="stat-row">
    @foreach($statCards as $card)
        <div class="stat-widget">
            <div class="stat-icon {{ $card['iconClass'] }}">
                <i class="{{ $card['icon'] }}"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $card['value'] }}</div>
                <div class="stat-label">{{ $card['title'] }}</div>
                <div class="stat-desc">{{ $card['description'] }}</div>
            </div>
        </div>
    @endforeach
</div>
