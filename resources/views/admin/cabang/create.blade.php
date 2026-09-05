@extends('layouts.app')

@section('title', 'Tambah Cabang')
@section('page-title', 'Tambah Cabang')
@section('page-subtitle', 'Siapkan lokasi sekolah sebelum memasukkan pengguna dan kelas')

@section('content')
    @include('admin.cabang.partials.form', ['cabang' => null, 'isEdit' => false])
@endsection
