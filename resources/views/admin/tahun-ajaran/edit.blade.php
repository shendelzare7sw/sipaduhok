@extends('layouts.app')

@section('title', 'Edit Tahun Ajaran')
@section('page-title', 'Edit Tahun Ajaran')
@section('page-subtitle', 'Perbarui periode ' . $tahunAjaran->nama_tahun_ajaran)

@section('content')
    @include('admin.tahun-ajaran.partials.form', ['isEdit' => true])
@endsection
