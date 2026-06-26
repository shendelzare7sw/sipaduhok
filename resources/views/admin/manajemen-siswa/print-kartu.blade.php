<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Siswa - {{ $siswa->nama_lengkap }}</title>
    <link rel="stylesheet" href="{{ asset('css/admin/manajemen-siswa/print-kartu.css') }}?v={{ filemtime(public_path('css/admin/manajemen-siswa/print-kartu.css')) }}">
    <script src="{{ asset('js/admin/manajemen-siswa/print-kartu.js') }}?v={{ filemtime(public_path('js/admin/manajemen-siswa/print-kartu.js')) }}" defer></script>
</head>
<body>
    @php
        $cabang = $siswa->cabang ?? null;
        $namaSekolahKartu = $cabang
            ? strtoupper(preg_replace('/\s*\(?\s*Gedung\s+\w+\s*\)?$/i', '', $cabang->nama_cabang))
            : 'PKBM HOUSE OF KNOWLEDGE';
        $alamatKartu = $cabang
            ? ($cabang->alamat ?? 'Jl. Ruko Reni Jaya, Pamulang')
            : 'Jl. Ruko Reni Jaya, Pamulang';

        $primaryParent = $siswa->studentParents->firstWhere('is_primary', true)
            ?? $siswa->studentParents->first();
        $namaOrtu = null;
        $kontakOrtu = null;
        if ($primaryParent && $primaryParent->parent) {
            $namaOrtu = $primaryParent->parent->name;
            $kontakOrtu = $primaryParent->parent->phone ?? $siswa->telepon_orangtua;
        } elseif ($siswa->nama_ayah || $siswa->nama_ibu) {
            $namaOrtu = implode(' / ', array_filter([$siswa->nama_ayah, $siswa->nama_ibu]));
            $kontakOrtu = $siswa->telepon_orangtua;
        }
    @endphp

    <div class="print-actions no-print">
        <a href="{{ route('admin.manajemen-siswa.show', $siswa) }}" class="back-button">&larr; Kembali</a>
        <button type="button" class="print-button" data-print-button>
            <i class="fas fa-print"></i> Cetak Kartu
        </button>
    </div>

    <div class="preview-page">
        <div class="card-container">
            <div class="student-card front">
                <div class="card-header">
                    <img src="{{ asset('img/logo/hok-watermark.png') }}" alt="Logo HOK" class="card-logo-img">
                    <div class="card-title">
                        <h3>{{ $namaSekolahKartu }}</h3>
                        <p>Kartu Tanda Siswa</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="photo-placeholder" id="photoContainer">
                        <img
                            id="kartuFoto"
                            src="{{ $siswa->foto ? asset('storage/' . $siswa->foto) : '' }}"
                            alt="Foto"
                            class="{{ $siswa->foto ? '' : 'is-hidden' }}"
                        >
                        @if(!$siswa->foto)
                            <span id="fotoPlaceholder">Pas Foto<br>3x4</span>
                        @endif
                    </div>
                    <div class="card-info">
                        <div class="name">{{ strtoupper($siswa->nama_lengkap) }}</div>
                        <table>
                            <tr>
                                <td class="col-label">NISN</td>
                                <td class="col-colon">:</td>
                                <td class="col-value">{{ $siswa->nisn }}</td>
                            </tr>
                            @if($siswa->nis)
                                <tr>
                                    <td class="col-label">NIS</td>
                                    <td class="col-colon">:</td>
                                    <td class="col-value">{{ $siswa->nis }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td class="col-label">Kelas</td>
                                <td class="col-colon">:</td>
                                <td class="col-value">{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="col-label">Tmp. Lahir</td>
                                <td class="col-colon">:</td>
                                <td class="col-value">{{ $siswa->tempat_lahir }}</td>
                            </tr>
                            <tr>
                                <td class="col-label">Tgl. Lahir</td>
                                <td class="col-colon">:</td>
                                <td class="col-value">{{ $siswa->tanggal_lahir->format('d/m/Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <span>{{ $siswa->cabang->nama_cabang ?? 'PKBM HOK' }}</span>
                </div>
            </div>

            <div class="student-card back">
                <div class="card-header">
                    <img src="{{ asset('img/logo/hok-watermark.png') }}" alt="Logo HOK" class="card-logo-img">
                    <div class="card-title">
                        <h3>{{ $namaSekolahKartu }}</h3>
                        <p>Pusat Kegiatan Belajar Masyarakat</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="info-section">
                        <h4>Alamat Siswa</h4>
                        <p>{{ Str::limit($siswa->alamat, 65) }}</p>
                    </div>
                    <div class="info-section">
                        <h4>Nama Wali Siswa/Wali</h4>
                        <p>{{ $namaOrtu ?? '-' }}</p>
                    </div>
                    <div class="info-section">
                        <h4>Kontak Darurat</h4>
                        <p>{{ $kontakOrtu ?? '-' }}</p>
                    </div>
                    <div class="barcode">
                        <div class="barcode-placeholder">{{ $siswa->nisn }}</div>
                    </div>
                </div>
                <div class="card-footer">
                    <span class="footer-address" title="{{ $alamatKartu }}">{{ Str::limit($alamatKartu, 78) }}</span>
                </div>
            </div>
        </div>

        <div class="upload-area no-print">
            <label for="fotoInput" class="upload-label">
                <i class="fas fa-camera"></i> Pasang Foto dari Komputer
            </label>
            <input type="file" id="fotoInput" class="foto-input" accept="image/*">
            <p class="upload-hint">
                @if($siswa->foto)
                    Foto profil sudah ada. Klik untuk mengganti tampilan saat cetak.
                @else
                    Siswa belum punya foto profil. Pilih foto dari komputer untuk ditampilkan di kartu.
                @endif
            </p>
        </div>

        <p class="no-print card-size-note">Ukuran kartu: 85.6mm x 53.98mm (standar ID Card)</p>
    </div>
</body>
</html>
