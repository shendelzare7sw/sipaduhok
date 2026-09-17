@extends('layouts.app')

@section('title', 'Daftar Catatan')
@section('page-title', 'Manajemen Catatan')
@section('page-subtitle', 'Kirim dan kelola catatan instruksi untuk pengguna')

@section('content')
    @php $isWaka = request()->routeIs('waka.*'); @endphp
    @include('catatan.partials.index-content', [
        'routePrefix' => $isWaka ? 'waka' : (request()->routeIs('ketua.*') ? 'ketua' : 'admin'),
        'showDirection' => $isWaka,
        'toolbarDescription' => $isWaka ? 'Kelola catatan akademik yang Anda kirim atau terima di cabang.' : 'Kelola riwayat catatan, instruksi, dan teguran yang sudah dikirim.',
        'listTitle' => $isWaka ? 'Riwayat Catatan' : 'Riwayat Catatan Terkirim',
        'emptyTitle' => $isWaka ? 'Belum ada catatan' : 'Belum ada catatan terkirim',
        'emptyDescription' => $isWaka ? 'Catatan yang Anda kirim atau terima akan tampil di halaman ini.' : 'Catatan yang Anda buat akan tampil sebagai riwayat di halaman ini.',
    ])
@endsection
