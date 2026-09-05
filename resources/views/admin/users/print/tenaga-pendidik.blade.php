@extends('layouts.print')

@section('title', 'Laporan Tenaga Pendidik')
@section('back-url', route('admin.users.tenaga-pendidik'))
@section('report-title', 'Laporan Tenaga Pendidik')

@section('report-meta')
    @if($request->filled('role'))<p class="mt-1 text-xs text-slate-600 print:text-[8pt]">Jabatan: {{ $roles[$request->role] ?? ucwords(str_replace('_', ' ', $request->role)) }}</p>@endif
    <p class="mt-1 text-xs font-semibold print:text-[8pt]">Total: {{ count($tenagaPendidik) }} tenaga pendidik</p>
@endsection

@section('report-content')
<table class="w-full table-fixed border-collapse text-[10px] print:text-[7.5pt]">
    <colgroup><col class="w-[5%]"><col class="w-[22%]"><col class="w-[13%]"><col class="w-[16%]"><col class="w-[21%]"><col class="w-[14%]"><col class="w-[9%]"></colgroup>
    <thead class="bg-slate-100"><tr>@foreach(['No', 'Nama lengkap', 'NIP', 'Jabatan', 'Email', 'Telepon', 'Status'] as $heading)<th class="border border-slate-900 px-2 py-2 text-center font-extrabold">{{ $heading }}</th>@endforeach</tr></thead>
    <tbody>
        @forelse($tenagaPendidik as $index => $item)
            <tr class="break-inside-avoid">
                <td class="border border-slate-900 px-2 py-1.5 text-center tabular-nums">{{ $index + 1 }}</td>
                <td class="border border-slate-900 px-2 py-1.5 font-semibold">{{ $item->name }}</td>
                <td class="border border-slate-900 px-2 py-1.5">{{ $item->tenagaPendidik->nip ?? '-' }}</td>
                <td class="border border-slate-900 px-2 py-1.5">{{ ucwords(str_replace('_', ' ', $item->role)) }}</td>
                <td class="break-all border border-slate-900 px-2 py-1.5">{{ $item->email }}</td>
                <td class="border border-slate-900 px-2 py-1.5">{{ $item->tenagaPendidik->telepon ?? $item->phone ?? '-' }}</td>
                <td class="border border-slate-900 px-2 py-1.5 text-center">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</td>
            </tr>
        @empty
            <tr><td colspan="7" class="border border-slate-900 p-6 text-center text-slate-500">Tidak ada data tenaga pendidik.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
