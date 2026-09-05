@extends('layouts.app')

@section('title', 'Import Tenaga Pendidik')
@section('page-title', 'Import Tenaga Pendidik')
@section('page-subtitle', 'Import data tenaga pendidik dari file Excel')

@section('content')
    @include('admin.users.partials.import-form', [
        'variant' => 'tenaga-pendidik',
        'heading' => 'Petunjuk Import Tenaga Pendidik',
        'instructions' => [
            'Download template Excel berisi referensi role dan cabang',
            'Isi data. Kolom <strong>nama_lengkap, email, jenis_kelamin, tempat_lahir, tanggal_lahir, alamat, telepon, pendidikan_terakhir, role, nama_cabang</strong> wajib diisi',
            'Role dan nama_cabang harus sesuai daftar di template',
            'User account akan dibuat otomatis dengan password: <code>password</code>',
        ],
        'templateRoute' => route('admin.users.tenaga-pendidik-template'),
        'storeRoute' => route('admin.users.import-tenaga-pendidik.store'),
        'backRoute' => route('admin.users.tenaga-pendidik'),
    ])
@endsection
