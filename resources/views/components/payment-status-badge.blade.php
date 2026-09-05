@props(['payment', 'class' => ''])

@php
    $tone = match ($payment->payment_status_label) {
        'Lunas', 'Disetujui' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'Menunggu Pembayaran' => 'bg-blue-50 text-blue-700 ring-blue-200',
        'Menunggu Validasi' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'Kedaluwarsa' => 'bg-slate-100 text-slate-600 ring-slate-200',
        default => 'bg-red-50 text-red-700 ring-red-200',
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex max-w-full items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-bold ring-1 '.$tone.' '.$class]) }}>
    <i class="{{ $payment->payment_status_icon }} shrink-0" aria-hidden="true"></i>
    <span class="truncate">{{ $payment->payment_status_label }}</span>
</span>
