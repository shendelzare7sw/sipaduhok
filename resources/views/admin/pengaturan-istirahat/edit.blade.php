@extends('layouts.app')

@section('title', 'Edit Waktu Istirahat')
@section('page-title', 'Edit Waktu Istirahat')
@section('page-subtitle', 'Perbarui waktu, hari, dan status jeda')

@section('content')
    @include('admin.pengaturan-istirahat.partials.form')
@endsection
