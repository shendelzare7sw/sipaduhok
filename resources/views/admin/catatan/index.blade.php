@extends('layouts.app')

@section('title', 'Daftar Catatan')
@section('page-title', 'Manajemen Catatan')
@section('page-subtitle', 'Kirim dan kelola catatan instruksi untuk pengguna')

@section('content')
    @include('catatan.partials.index-content', [
        'routePrefix' => request()->routeIs('ketua.*') ? 'ketua' : 'admin',
        'showDirection' => false,
        'toolbarDescription' => 'Kelola riwayat catatan, instruksi, dan teguran yang sudah dikirim.',
        'listTitle' => 'Riwayat Catatan Terkirim',
        'emptyTitle' => 'Belum ada catatan terkirim',
        'emptyDescription' => 'Catatan yang Anda buat akan tampil sebagai riwayat di halaman ini.',
    ])
@endsection
