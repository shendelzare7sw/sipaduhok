@extends('layouts.lms-guru')

@section('title', 'Forum Diskusi')
@section('page-title', 'Forum Diskusi')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    <div class="card-custom text-center py-5">
        <i class="fas fa-comments text-muted" style="font-size: 80px; opacity: 0.2;"></i>
        <h4 class="mt-4 mb-2">Forum Diskusi</h4>
        <p class="text-muted">
            Fitur ini sedang dalam pengembangan (Phase 2)
        </p>
        <p class="text-muted small">
            Forum diskusi akan memungkinkan interaksi dua arah antara guru dan siswa<br>
            untuk membahas materi, tugas, dan ujian.
        </p>
    </div>
@endsection