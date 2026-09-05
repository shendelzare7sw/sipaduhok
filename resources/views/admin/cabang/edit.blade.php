@extends('layouts.app')

@section('title', 'Edit Cabang')
@section('page-title', 'Edit Cabang')
@section('page-subtitle', 'Perbarui data ' . $cabang->nama_cabang)

@section('content')
    @include('admin.cabang.partials.form', ['isEdit' => true])
@endsection
