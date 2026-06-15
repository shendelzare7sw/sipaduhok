@extends('layouts.sneat')

@section('title', 'Import Orang Tua')
@section('page-title', 'Import Orang Tua')
@section('page-subtitle', 'Import data orang tua dari file Excel')
@section('sidebar-menu')@include('admin.partials.sneat-sidebar-menu')@endsection

@section('styles')
    @vite(['resources/css/admin/users/import.css'])
@endsection

@section('content')
    @include('admin.users.partials.import-form', [
        'variant' => 'orang-tua',
        'heading' => 'Petunjuk Import Orang Tua',
        'instructions' => [
            'Nama orang tua WAJIB diisi',
            'Username & email opsional (auto-generate jika kosong)',
            'nis_anak: NIS siswa yang akan dihubungkan (pisah koma jika lebih dari satu)',
            'Password default: <code>password</code>',
        ],
        'templateRoute' => route('admin.users.orang-tua-template'),
        'storeRoute' => route('admin.users.import-orang-tua.store'),
        'backRoute' => route('admin.users.orang-tua'),
    ])
@endsection

@section('scripts')
    @vite(['resources/js/admin/users/import.js'])
@endsection
