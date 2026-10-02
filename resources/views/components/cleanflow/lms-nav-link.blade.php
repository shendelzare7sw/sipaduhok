{{-- Item navigasi sidebar LMS (Guru & Siswa). Tema terang beraksen indigo, berbeda dari sidebar SIA. --}}
@props([
    'href' => null,
    'icon',
    'active' => false,
    'badge' => null,
    'muted' => false,
])

@php
    $classes = 'group flex min-h-11 w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold no-underline transition '
        .($active
            ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25'
            : ($muted ? 'cursor-default text-slate-400' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700'));
    $iconClasses = 'flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-sm transition '
        .($active ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-white group-hover:text-indigo-600');
@endphp

@if($href && ! $muted)
    <a href="{{ $href }}" @if($active) aria-current="page" @endif {{ $attributes->class($classes) }}>
        <span class="{{ $iconClasses }}"><i class="fa-solid {{ $icon }}" aria-hidden="true"></i></span>
        <span class="min-w-0 flex-1 truncate">{{ $slot }}</span>
        @if($badge)
            <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-extrabold {{ $active ? 'bg-white text-indigo-700' : 'bg-rose-500 text-white' }}">{{ $badge }}</span>
        @endif
    </a>
@else
    <span {{ $attributes->class($classes) }}>
        <span class="{{ $iconClasses }}"><i class="fa-solid {{ $icon }}" aria-hidden="true"></i></span>
        <span class="min-w-0 flex-1 truncate">{{ $slot }}</span>
    </span>
@endif
