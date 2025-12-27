@extends('layouts.dashboard')

@section('title', 'Validasi Izin Ketidakhadiran')

@section('page-title', 'Validasi Izin')
@section('page-subtitle', 'Validasi pengajuan izin ketidakhadiran siswa')

@section('sidebar-menu')
    @include('wali-kelas.partials.sidebar')
@endsection

@section('content')
    <div class="content-card" style="margin-bottom: 24px;">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Validasi Izin Ketidakhadiran</h2>
                <p style="color: #666; margin: 0;">Periksa dan validasi pengajuan izin dari siswa</p>
            </div>
            <a href="{{ route('wali.presensi.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali ke Presensi
            </a>
        </div>
    </div>

    @if($pengajuanPending->count() > 0)
        <div class="content-card">
            <h3 style="margin-bottom: 16px;">
                <i class="fas fa-clock me-2" style="color: #f59e0b;"></i>
                Pengajuan Menunggu Validasi ({{ $pengajuanPending->count() }})
            </h3>

            @foreach($pengajuanPending as $presensi)
                <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 16px; background: #fffbeb;">
                    <div class="row">
                        <div class="col-md-8">
                            <div style="margin-bottom: 12px;">
                                <strong style="font-size: 16px; color: #165fac;">{{ $presensi->siswa->nama_lengkap }}</strong>
                                <span class="badge" style="background: #fbbf24; color: #92400e; margin-left: 8px;">
                                    {{ strtoupper($presensi->status) }}
                                </span>
                            </div>
                            <div style="color: #666; margin-bottom: 8px;">
                                <i class="fas fa-id-card me-2"></i>NIS: {{ $presensi->siswa->nis }}
                            </div>
                            <div style="color: #666; margin-bottom: 8px;">
                                <i class="fas fa-calendar me-2"></i>Tanggal: {{ \Carbon\Carbon::parse($presensi->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                            </div>
                            <div style="color: #666; margin-bottom: 12px;">
                                <i class="fas fa-comment me-2"></i>Keterangan: <strong>{{ $presensi->keterangan ?? '-' }}</strong>
                            </div>
                            
                            @if($presensi->bukti_surat)
                                <a href="{{ asset('storage/' . $presensi->bukti_surat) }}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-file-pdf me-1"></i>Lihat Surat Keterangan
                                </a>
                            @endif
                        </div>
                        <div class="col-md-4" style="text-align: right;">
                            <form action="{{ route('wali.presensi.proses-validasi-izin', $presensi->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <input type="hidden" name="action" value="setuju">
                                <button type="submit" class="btn btn-success" style="width: 100%; margin-bottom: 8px;">
                                    <i class="fas fa-check me-1"></i>Setujui
                                </button>
                            </form>
                            
                            <button type="button" class="btn btn-danger" style="width: 100%;" 
                                    onclick="tolakIzin({{ $presensi->id }}, '{{ $presensi->siswa->nama_lengkap }}')">
                                <i class="fas fa-times me-1"></i>Tolak (Alpha)
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="content-card">
            <div style="text-align: center; padding: 60px 20px; color: #999;">
                <i class="fas fa-check-circle" style="font-size: 64px; color: #10b981; margin-bottom: 16px;"></i>
                <h3>Tidak Ada Pengajuan Pending</h3>
                <p>Semua pengajuan izin sudah divalidasi</p>
            </div>
        </div>
    @endif

    {{-- Modal Tolak Izin --}}
    <div class="modal fade" id="tolakIzinModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="tolakIzinForm" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="tolak">
                    
                    <div class="modal-header" style="background: #fee2e2; border-bottom: 1px solid #fecaca;">
                        <h5 class="modal-title" style="color: #991b1b;">
                            <i class="fas fa-exclamation-triangle me-2"></i>Tolak Izin
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menolak pengajuan izin ini?</p>
                        <div style="padding: 16px; background: #f3f4f6; border-radius: 8px; margin: 16px 0;">
                            <strong>Siswa:</strong> <span id="tolak_nama_siswa"></span>
                        </div>
                        <div class="alert" style="background: #fee2e2; border-left: 4px solid #ef4444; color: #991b1b;">
                            <i class="fas fa-info-circle me-2"></i>
                            Status presensi akan otomatis berubah menjadi <strong>ALPHA</strong>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times me-1"></i>Ya, Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function tolakIzin(id, namaSiswa) {
            document.getElementById('tolakIzinForm').action = `/wali/presensi/${id}/proses-validasi-izin`;
            document.getElementById('tolak_nama_siswa').textContent = namaSiswa;
            
            var modal = new bootstrap.Modal(document.getElementById('tolakIzinModal'));
            modal.show();
        }
    </script>
@endsection