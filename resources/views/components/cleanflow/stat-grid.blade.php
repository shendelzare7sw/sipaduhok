@props(['items', 'desktopColumns' => 4])

<section {{ $attributes->class([
    'grid min-w-0 grid-cols-2 gap-3',
    'lg:grid-cols-6' => (int) $desktopColumns === 6,
    'lg:grid-cols-4' => (int) $desktopColumns !== 6,
]) }}>
    @foreach($items as $item)
        <article class="min-w-0 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="truncate text-xl font-extrabold leading-none text-slate-900 sm:text-2xl" title="{{ $item['value'] }}">{{ $item['value'] }}</p>
                    <p class="mt-2 min-h-8 text-[9px] font-bold uppercase leading-4 tracking-wide text-slate-500 sm:min-h-0 sm:truncate sm:text-xs" title="{{ $item['label'] }}">{{ $item['label'] }}</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $item['tone'] }} sm:h-10 sm:w-10">
                    <i class="fas {{ $item['icon'] }}" aria-hidden="true"></i>
                </span>
            </div>
            @if(!empty($item['meta']))
                <p class="mt-3 truncate border-t border-slate-100 pt-3 text-[10px] text-slate-500 sm:text-xs" title="{{ $item['meta'] }}">{{ $item['meta'] }}</p>
            @endif
        </article>
    @endforeach
</section>
