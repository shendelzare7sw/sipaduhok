@props(['roleLabel' => null, 'backUrl' => null, 'backLabel' => 'Kembali ke Dashboard'])

<details class="group relative">
    <summary class="flex h-11 cursor-pointer list-none items-center gap-2 rounded-xl border border-slate-200 py-1.5 pl-1.5 pr-2 transition hover:border-brand-500">
        <span class="flex h-8 w-8 items-center justify-center overflow-hidden rounded-lg bg-brand-600 text-sm font-bold text-white">
            @if(auth()->user()->foto_profil)
                <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}" alt="" class="h-full w-full object-cover">
            @else
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            @endif
        </span>
        <span class="hidden max-w-32 truncate text-xs font-semibold text-slate-700 md:block">{{ auth()->user()->name }}</span>
        <i class="fa-solid fa-chevron-down hidden text-[10px] text-slate-400 transition group-open:rotate-180 md:block"></i>
    </summary>
    <div class="absolute right-0 mt-2 w-60 overflow-hidden rounded-2xl border border-slate-200 bg-white p-2 shadow-soft">
        <div class="border-b border-slate-100 px-3 py-2.5">
            <p class="truncate text-sm font-bold text-slate-900">{{ auth()->user()->name }}</p>
            <p class="truncate text-xs text-slate-500">{{ $roleLabel ?: ucwords(str_replace('_', ' ', auth()->user()->role ?? 'Pengguna')) }}</p>
        </div>
        <a href="{{ route('profile.index') }}" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"><i class="fa-regular fa-user w-4 text-center"></i> Profil Saya</a>
        <a href="{{ route('account.settings') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"><i class="fa-solid fa-gear w-4 text-center"></i> Pengaturan Akun</a>
        @if($backUrl)
            <a href="{{ $backUrl }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"><i class="fa-solid fa-arrow-left w-4 text-center"></i> {{ $backLabel }}</a>
        @endif
        <form action="{{ route('logout') }}" method="POST" data-confirm="logout" class="mt-1 border-t border-slate-100 pt-1">
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50"><i class="fa-solid fa-arrow-right-from-bracket w-4 text-center"></i> Keluar</button>
        </form>
    </div>
</details>
