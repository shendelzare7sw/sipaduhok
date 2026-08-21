@props(['payment'])

@php
    $group = $payment->payment_channel_group;
    $badgeClass = match ($group) {
        'qris' => 'bg-primary',
        'va' => 'bg-info',
        'retail' => 'bg-warning text-dark',
        default => $payment->metode_pembayaran === 'tunai'
            ? 'bg-primary'
            : ($payment->metode_pembayaran === 'transfer' ? 'bg-success' : 'bg-secondary'),
    };
    $icon = match ($group) {
        'qris' => 'fas fa-qrcode',
        'va' => 'fas fa-university',
        'retail' => 'fas fa-store',
        default => $payment->metode_pembayaran === 'tunai'
            ? 'fas fa-money-bill-wave'
            : ($payment->metode_pembayaran === 'transfer' ? 'fas fa-university' : 'fas fa-credit-card'),
    };
@endphp

<span {{ $attributes->class(['badge', $badgeClass]) }}>
    <i class="{{ $icon }} me-1"></i>{{ $payment->payment_channel_label }}
</span>
