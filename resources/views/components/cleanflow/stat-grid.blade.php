@props(['items', 'desktopColumns' => 4])

{{-- Teks singkat kartu (nilai, label, keterangan) selalu tampil utuh: boleh turun baris, tidak dipotong. --}}
<section {{ $attributes->class([
    'grid min-w-0 grid-cols-2 gap-3',
    'lg:grid-cols-6' => (int) $desktopColumns === 6,
    'lg:grid-cols-4' => (int) $desktopColumns !== 6,
]) }}>
    @foreach($items as $item)
        <article class="flex min-w-0 flex-col rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="flex flex-col-reverse items-start gap-2 sm:flex-row sm:justify-between sm:gap-2">
                <div class="min-w-0">
                    <p class="break-words text-xl font-extrabold leading-tight text-slate-900 sm:text-2xl">{{ $item['value'] }}</p>
                    <p class="mt-2 break-words text-[10px] font-bold uppercase leading-tight tracking-normal text-slate-500 sm:text-xs sm:tracking-wide">{{ $item['label'] }}</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $item['tone'] }} sm:h-10 sm:w-10">
                    <i class="fas {{ $item['icon'] }}" aria-hidden="true"></i>
                </span>
            </div>
            @if(!empty($item['meta']))
                <div class="mt-auto pt-3"><p class="break-words border-t border-slate-100 pt-3 text-[10px] leading-snug text-slate-500 sm:text-xs">{{ $item['meta'] }}</p></div>
            @endif
        </article>
    @endforeach
</section>
