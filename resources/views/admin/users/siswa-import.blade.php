@extends('layouts.sneat')

@section('title', 'Import Siswa')
@section('page-title', 'Import Siswa')
@section('page-subtitle', 'Import data siswa dari file Excel')
@section('sidebar-menu')@include('admin.partials.sneat-sidebar-menu')@endsection

@section('styles')
    @vite(['resources/css/admin/users/import.css'])
@endsection

@section('content')
    @include('admin.users.partials.import-form', [
        'variant' => 'siswa',
        'heading' => 'Petunjuk Import Siswa',
        'instructions' => [
            'Download template Excel dengan format yang benar',
            'Isi data siswa. Kolom <strong>nama_lengkap, nama_kelas, jenis_kelamin, tempat_lahir, tanggal_lahir, alamat, agama</strong> wajib diisi',
            'nama_kelas harus sesuai tahun ajaran aktif. Isi nama_cabang jika nama kelas sama di beberapa cabang',
            'agama wajib salah satu: Islam, Kristen, Katolik, Hindu, Buddha, atau Konghucu',
            'User account akan dibuat otomatis dengan password: <code>password</code>',
        ],
        'templateRoute' => route('admin.users.siswa-template'),
        'storeRoute' => route('admin.users.import-siswa.store'),
        'backRoute' => route('admin.users.siswa'),
    ])
@endsection

@section('scripts')
    @vite(['resources/js/admin/users/import.js'])
@endsection
