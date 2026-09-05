@extends('layouts.app')

@section('title', 'Daftar Catatan')
@section('page-title', 'Manajemen Catatan')
@section('page-subtitle', 'Kelola catatan yang dikirim dan diterima')

@section('content')
    @include('catatan.partials.index-content', [
        'routePrefix' => 'waka',
        'showDirection' => true,
        'toolbarDescription' => 'Kelola catatan, instruksi, dan teguran yang Anda kirim atau terima.',
        'listTitle' => 'Daftar Catatan',
        'emptyTitle' => 'Belum ada catatan',
        'emptyDescription' => 'Catatan yang Anda kirim atau terima akan tampil di halaman ini.',
    ])
@endsection
