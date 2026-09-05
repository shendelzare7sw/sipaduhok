@props([
    'href' => null,
    'type' => 'button',
    'tone' => 'neutral',
    'icon',
    'label',
])

@php
    $toneClasses = match ($tone) {
        'view' => 'bg-blue-50 text-blue-700 ring-blue-100 hover:bg-blue-100 focus-visible:ring-blue-500',
        'edit' => 'bg-amber-50 text-amber-700 ring-amber-100 hover:bg-amber-100 focus-visible:ring-amber-500',
        'delete' => 'bg-red-50 text-red-700 ring-red-100 hover:bg-red-100 focus-visible:ring-red-500',
        'success' => 'bg-emerald-50 text-emerald-700 ring-emerald-100 hover:bg-emerald-100 focus-visible:ring-emerald-500',
        'visibility' => 'bg-violet-50 text-violet-700 ring-violet-100 hover:bg-violet-100 focus-visible:ring-violet-500',
        'featured' => 'bg-amber-50 text-amber-700 ring-amber-100 hover:bg-amber-100 focus-visible:ring-amber-500',
        'info' => 'bg-cyan-50 text-cyan-700 ring-cyan-100 hover:bg-cyan-100 focus-visible:ring-cyan-500',
        default => 'bg-slate-100 text-slate-700 ring-slate-200 hover:bg-slate-200 focus-visible:ring-slate-500',
    };

    $classes = "inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-xs no-underline ring-1 ring-inset transition hover:-translate-y-0.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:translate-y-0 disabled:cursor-not-allowed disabled:opacity-50 {$toneClasses}";
@endphp

@if($href)
    <a href="{{ $href }}" aria-label="{{ $label }}" title="{{ $label }}" {{ $attributes->class($classes) }}>
        <i class="{{ $icon }}" aria-hidden="true"></i>
    </a>
@else
    <button type="{{ $type }}" aria-label="{{ $label }}" title="{{ $label }}" {{ $attributes->class($classes) }}>
        <i class="{{ $icon }}" aria-hidden="true"></i>
    </button>
@endif
