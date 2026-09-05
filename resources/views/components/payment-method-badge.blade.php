@props(['payment'])

@php
    $group = $payment->payment_channel_group;
    $badgeClass = match ($group) {
        'qris' => 'bg-blue-50 text-blue-700 ring-blue-200',
        'va' => 'bg-cyan-50 text-cyan-700 ring-cyan-200',
        'retail' => 'bg-amber-50 text-amber-700 ring-amber-200',
        default => $payment->metode_pembayaran === 'tunai'
            ? 'bg-blue-50 text-blue-700 ring-blue-200'
            : ($payment->metode_pembayaran === 'transfer'
                ? 'bg-emerald-50 text-emerald-700 ring-emerald-200'
                : 'bg-slate-100 text-slate-600 ring-slate-200'),
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

<span {{ $attributes->class(['inline-flex max-w-full items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-bold ring-1', $badgeClass]) }}>
    <i class="{{ $icon }} shrink-0" aria-hidden="true"></i>
    <span class="truncate">{{ $payment->payment_channel_label }}</span>
</span>
