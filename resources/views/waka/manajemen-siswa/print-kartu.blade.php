<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Siswa - {{ $siswa->nama_lengkap }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f0f0f0; padding: 20px; }
        
        .card-container { display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; }
        
        .student-card {
            width: 85.6mm; height: 53.98mm;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border-radius: 10px; overflow: hidden; position: relative;
            color: white; box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        
        .student-card.back { background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%); }
        
        .card-header {
            background: rgba(255,255,255,0.1); padding: 8px 12px;
            display: flex; align-items: center; gap: 8px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        
        .card-logo {
            width: 30px; height: 30px; background: white; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: bold; color: #1d4ed8; font-size: 12px;
        }
        
        .card-title { flex: 1; }
        .card-title h3 { font-size: 10px; font-weight: 700; letter-spacing: 0.5px; }
        .card-title p { font-size: 7px; opacity: 0.8; }
        
        .card-body { padding: 10px 12px; display: flex; gap: 10px; }
        
        .photo-placeholder {
            width: 55px; height: 70px; background: rgba(255,255,255,0.2);
            border-radius: 5px; display: flex; align-items: center; justify-content: center;
            font-size: 8px; text-align: center; border: 1px dashed rgba(255,255,255,0.5);
            flex-shrink: 0; overflow: hidden;
        }
        
        .photo-placeholder img { width: 100%; height: 100%; object-fit: cover; }
        
        .card-info { flex: 1; font-size: 8px; }
        .card-info .name { font-size: 11px; font-weight: 700; margin-bottom: 6px; line-height: 1.2; }
        .card-info table { width: 100%; }
        .card-info table td { padding: 1px 0; vertical-align: top; }
        .card-info table td:first-child { width: 40px; color: rgba(255,255,255,0.7); }
        
        .card-footer {
            position: absolute; bottom: 0; left: 0; right: 0;
            background: rgba(0,0,0,0.2); padding: 5px 12px; font-size: 7px;
            display: flex; justify-content: space-between;
        }
        
        .back .card-body { flex-direction: column; padding: 12px; }
        .back .info-section { margin-bottom: 6px; }
        .back .info-section h4 { font-size: 7px; margin-bottom: 2px; opacity: 0.7; text-transform: uppercase; }
        .back .info-section p { font-size: 8px; }
        
        .back .barcode { text-align: center; margin-top: auto; padding-top: 6px; border-top: 1px solid rgba(255,255,255,0.2); }
        .back .barcode-placeholder {
            background: white; color: #000; padding: 4px 12px;
            font-family: 'Courier New', monospace; font-size: 12px;
            font-weight: bold; letter-spacing: 2px; display: inline-block; border-radius: 3px;
        }
        
        .print-button { position: fixed; top: 20px; right: 20px; padding: 12px 24px; background: #3b82f6; color: white; border: none; border-radius: 8px; cursor: pointer; }
        .back-button { position: fixed; top: 20px; right: 130px; padding: 12px 24px; background: #6b7280; color: white; border: none; border-radius: 8px; text-decoration: none; }
        .print-info { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
        
        @media print {
            body { background: white; padding: 0; }
            .no-print { display: none !important; }
            .card-container { gap: 10mm; }
            .student-card { box-shadow: none; }
        }
    </style>
</head>
<body>
    <a href="{{ route('waka.manajemen-siswa.show', $siswa) }}" class="back-button no-print">← Kembali</a>
    <button onclick="window.print()" class="print-button no-print"><i class="fas fa-print"></i> Cetak Kartu</button>

    <div class="card-container">
        {{-- Front Card --}}
        <div class="student-card front">
            <div class="card-header">
                <div class="card-logo">HOK</div>
                <div class="card-title">
                    <h3>PKBM HOUSE OF KNOWLEDGE</h3>
                    <p>Kartu Tanda Siswa</p>
                </div>
            </div>
            <div class="card-body">
                <div class="photo-placeholder">
                    @if($siswa->foto)
                        <img src="{{ asset('storage/' . $siswa->foto) }}" alt="Foto">
                    @else
                        Pas Foto<br>3x4
                    @endif
                </div>
                <div class="card-info">
                    <div class="name">{{ strtoupper($siswa->nama_lengkap) }}</div>
                    <table>
                        <tr><td>NISN</td><td>: {{ $siswa->nisn }}</td></tr>
                        @if($siswa->nis)
                        <tr><td>NIS</td><td>: {{ $siswa->nis }}</td></tr>
                        @endif
                        <tr><td>Kelas</td><td>: {{ $siswa->kelas->nama_kelas ?? '-' }}</td></tr>
                        <tr><td>TTL</td><td>: {{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir->format('d/m/Y') }}</td></tr>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <span>{{ $siswa->cabang->nama_cabang ?? 'PKBM HOK' }}</span>
                <span>Berlaku: {{ $siswa->kelas->tahunAjaran->nama_tahun_ajaran ?? date('Y') }}</span>
            </div>
        </div>

        {{-- Back Card --}}
        <div class="student-card back">
            <div class="card-header">
                <div class="card-logo">HOK</div>
                <div class="card-title">
                    <h3>PKBM HOUSE OF KNOWLEDGE</h3>
                    <p>Pusat Kegiatan Belajar Masyarakat</p>
                </div>
            </div>
            <div class="card-body">
                <div class="info-section">
                    <h4>Alamat Siswa</h4>
                    <p>{{ Str::limit($siswa->alamat, 70) }}</p>
                </div>
                <div class="info-section">
                    <h4>Nama Orang Tua/Wali</h4>
                    <p>{{ $siswa->nama_ayah ?? '-' }} / {{ $siswa->nama_ibu ?? '-' }}</p>
                </div>
                <div class="info-section">
                    <h4>Kontak Darurat</h4>
                    <p>{{ $siswa->telepon_orangtua ?? '-' }}</p>
                </div>
                <div class="barcode">
                    <div class="barcode-placeholder">{{ $siswa->nisn }}</div>
                </div>
            </div>
            <div class="card-footer">
                <span>Jl. Ruko Reni Jaya, Pamulang</span>
                <span>info@hok.sch.id</span>
            </div>
        </div>
    </div>

    <p class="print-info no-print">Ukuran kartu: 85.6mm x 53.98mm (standar ID Card)</p>
</body>
</html>
