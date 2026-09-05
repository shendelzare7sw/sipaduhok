@extends('layouts.app')

@section('title', 'Tambah Tahun Ajaran')
@section('page-title', 'Tambah Tahun Ajaran')
@section('page-subtitle', 'Langkah pertama sebelum membuat kelas, pengguna, dan jadwal')

@section('content')
    @include('admin.tahun-ajaran.partials.form', ['tahunAjaran' => null, 'isEdit' => false])
@endsection
