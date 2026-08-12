@extends('layouts.sneat')

@section('title', 'Import Wali Siswa')
@section('page-title', 'Import Wali Siswa')
@section('page-subtitle', 'Import data wali siswa dari file Excel')
@section('sidebar-menu')@include('admin.partials.sneat-sidebar-menu')@endsection

@section('styles')
    @vite(['resources/css/admin/users/import.css'])
@endsection

@section('content')
    @include('admin.users.partials.import-form', [
        'variant' => 'wali-siswa',
        'heading' => 'Petunjuk Import Wali Siswa',
        'instructions' => [
            'Nama wali siswa WAJIB diisi',
            'Username & email opsional (auto-generate jika kosong)',
            'nis_anak: NIS siswa yang akan dihubungkan (pisah koma jika lebih dari satu)',
            'hubungan diisi jika nis_anak diisi, contoh: Ayah, Ibu, atau Wali',
            'Password default: <code>password</code>',
        ],
        'templateRoute' => route('admin.users.wali-siswa-template'),
        'storeRoute' => route('admin.users.import-wali-siswa.store'),
        'backRoute' => route('admin.users.wali-siswa'),
    ])
@endsection

@section('scripts')
    @vite(['resources/js/admin/users/import.js'])
@endsection
