@extends('layouts.sneat')

@section('title', 'Import Tenaga Pendidik')
@section('page-title', 'Import Tenaga Pendidik')
@section('page-subtitle', 'Import data tenaga pendidik dari file Excel')
@section('sidebar-menu')@include('admin.partials.sneat-sidebar-menu')@endsection

@section('styles')
    @vite(['resources/css/admin/users/import.css'])
@endsection

@section('content')
    @include('admin.users.partials.import-form', [
        'variant' => 'tenaga-pendidik',
        'heading' => 'Petunjuk Import Tenaga Pendidik',
        'instructions' => [
            'Download template Excel berisi referensi role dan cabang',
            'Isi data. Kolom <strong>nama_lengkap</strong> wajib diisi',
            'Role harus sesuai: guru_pengajar, wali_kelas, dll',
            'User account akan dibuat otomatis dengan password: <code>password</code>',
        ],
        'templateRoute' => route('admin.users.tenaga-pendidik-template'),
        'storeRoute' => route('admin.users.import-tenaga-pendidik.store'),
        'backRoute' => route('admin.users.tenaga-pendidik'),
    ])
@endsection

@section('scripts')
    @vite(['resources/js/admin/users/import.js'])
@endsection
