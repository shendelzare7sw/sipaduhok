@extends('layouts.sneat')

@section('title', 'Catatan Monitoring')
@section('page-title', 'Catatan Monitoring')
@section('page-subtitle', 'Masukan dari Kepala Sekolah / Wakil Kepala Sekolah / Admin')

@section('sidebar-menu')
    @include('guru.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/guru/lms/catatan-monitoring/index.css'])
@endsection

@section('content')
<div class="cm-wrapper guru-lms-monitoring-index-page">
    @if($catatan->isEmpty())
        <div class="empty-state">
            <i class="fas fa-inbox empty-icon"></i>
            <p class="text-muted mb-0">Belum ada catatan yang masuk.</p>
        </div>
    @else
        <div class="cm-list">
            @foreach($catatan as $c)
                <a href="{{ route('guru.lms.catatan-monitoring.show', $c->id) }}"
                   class="cm-item {{ is_null($c->dibaca_pada) ? 'cm-unread' : '' }}">
                    <div class="cm-item-top">
                        <div class="cm-pengirim">
                            <i class="fas fa-user-shield"></i> {{ $c->pengirim->name ?? 'Pimpinan' }}
                            <span class="cm-role-badge">{{ str_replace('_', ' ', $c->pengirim_role) }}</span>
                        </div>
                        @if(is_null($c->dibaca_pada))
                            <span class="cm-status-pill cm-status-baru">Baru</span>
                        @endif
                    </div>
                    <div class="cm-konten-info">
                        <span class="cm-konten-badge">{{ $c->kontenLabel() }}</span>
                        <strong>{{ $c->kontenJudul() }}</strong>
                        @if($c->mataPelajaran)
                            <span class="cm-mapel">&bull; {{ $c->mataPelajaran->nama_mapel }}</span>
                        @endif
                        @if($c->kelas)
                            <span class="cm-kelas">&bull; {{ $c->kelas->nama_kelas }}</span>
                        @endif
                    </div>
                    <p class="cm-preview">{{ Str::limit($c->isi_catatan, 180) }}</p>
                    <div class="cm-footer">
                        <span><i class="fas fa-clock me-1"></i>{{ \Carbon\Carbon::parse($c->created_at)->translatedFormat('d F Y, H:i') }}</span>
                        <span class="cm-arrow">Lihat detail <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="cm-pagination">
            {{ $catatan->links() }}
        </div>
    @endif
</div>
@endsection
