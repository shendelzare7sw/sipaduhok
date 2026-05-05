@extends('layouts.sneat')

@section('title', 'Catatan Monitoring')
@section('page-title', 'Catatan Monitoring')
@section('page-subtitle', 'Masukan dari Kepala Sekolah / Wakil Kepala Sekolah / Admin')

@section('sidebar-menu')
    @include('guru.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    .cm-wrapper { 
        padding: 0; 
        max-width: 900px; 
        margin: 0 auto; 
    }

    .cm-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-bottom: 24px;
    }

    .cm-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px 24px;
        text-decoration: none;
        color: inherit;
        display: block;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .cm-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.06);
        border-color: var(--bs-primary, #4361ee);
        text-decoration: none;
        color: inherit;
    }

    .cm-unread {
        border-left: 4px solid var(--bs-primary, #4361ee);
        background: linear-gradient(to right, rgba(67,97,238,0.03) 0%, #ffffff 5%);
    }

    .cm-item-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .cm-pengirim {
        font-size: 0.9rem;
        color: var(--bs-primary, #4361ee);
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .cm-role-badge {
        background: rgba(67, 97, 238, 0.1);
        color: var(--bs-primary, #4361ee);
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .cm-status-pill {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 999px;
    }

    .cm-status-baru {
        background: #fef3c7;
        color: #d97706;
    }

    .cm-konten-info {
        font-size: 0.85rem;
        color: #1e293b;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .cm-konten-badge {
        background: #fee2e2;
        color: #b91c1c;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .cm-mapel, .cm-kelas {
        color: #64748b;
        font-size: 0.8rem;
    }

    .cm-preview {
        font-size: 0.9rem;
        color: #475569;
        line-height: 1.6;
        margin: 0 0 16px 0;
    }

    .cm-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.8rem;
        color: #64748b;
        padding-top: 12px;
        border-top: 1px solid #f1f5f9;
    }

    .cm-arrow {
        color: var(--bs-primary, #4361ee);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .empty-state {
        background: #ffffff;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        padding: 60px 20px;
        text-align: center;
    }

    .empty-icon {
        font-size: 3.5rem;
        color: #cbd5e1;
        margin-bottom: 16px;
        display: block;
    }

    .cm-pagination {
        display: flex;
        justify-content: center;
        margin-top: 24px;
    }

    @media (max-width: 768px) {
        .cm-item { padding: 16px; }
        .cm-item-top { flex-direction: column; align-items: flex-start; }
    }
</style>
@endsection

@section('content')
<div class="cm-wrapper">
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
