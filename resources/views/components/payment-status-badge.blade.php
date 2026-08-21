@props(['payment', 'class' => ''])

<span {{ $attributes->merge(['class' => 'badge '.$payment->payment_status_badge_class.' '.$class]) }}>
    <i class="{{ $payment->payment_status_icon }} me-1"></i>{{ $payment->payment_status_label }}
</span>
