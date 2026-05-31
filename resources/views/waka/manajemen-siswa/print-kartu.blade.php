<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Siswa - {{ $siswa->nama_lengkap }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            color-adjust: exact;
        }
        body { font-family: Arial, sans-serif; background: #f0f0f0; padding: 20px; text-align: center; }

        .preview-page {
            width: min(100%, 760px);
            margin: 0 auto;
            text-align: center;
        }

        .print-actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
            margin: 0 auto 22px;
            padding: 8px;
            position: sticky;
            top: 0;
            z-index: 10;
            background: rgba(240, 240, 240, 0.92);
            backdrop-filter: blur(6px);
        }

        .card-container { display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; }

        .student-card {
            width: 85.6mm; height: 53.98mm;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border-radius: 10px; overflow: hidden; position: relative;
            color: white; box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            text-align: left;
        }

        .student-card.back { background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%); }

        .card-header {
            background: rgba(255,255,255,0.12); padding: 7px 10px;
            display: flex; align-items: center; gap: 8px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .card-logo-img {
            width: 36px; height: 36px;
            object-fit: contain; flex-shrink: 0;
        }

        .card-title { flex: 1; }
        .card-title h3 { font-size: 9.5px; font-weight: 700; letter-spacing: 0.3px; line-height: 1.2; }
        .card-title p { font-size: 7px; opacity: 0.8; margin-top: 1px; }

        .card-body { padding: 9px 10px; display: flex; gap: 10px; }

        .photo-placeholder {
            width: 55px; height: 70px; background: rgba(255,255,255,0.2);
            border-radius: 4px; display: flex; align-items: center; justify-content: center;
            font-size: 7.5px; text-align: center; border: 1px dashed rgba(255,255,255,0.5);
            flex-shrink: 0; overflow: hidden;
        }
        .photo-placeholder img { width: 100%; height: 100%; object-fit: cover; }

        .card-info { flex: 1; font-size: 8px; min-width: 0; }
        .card-info .name {
            font-size: 10.5px; font-weight: 700; margin-bottom: 5px;
            line-height: 1.2; word-break: break-word;
        }

        /* 3-column table: label | : | value — strict alignment */
        .card-info table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }
        .card-info table td {
            padding: 1.5px 0;
            vertical-align: top;
            font-size: 7.5px;
            overflow: hidden;
        }
        .card-info table .col-label {
            width: 40px;
            color: rgba(255,255,255,0.7);
            white-space: nowrap;
        }
        .card-info table .col-colon {
            width: 10px;
            color: rgba(255,255,255,0.7);
            text-align: center;
        }
        .card-info table .col-value {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .card-footer {
            position: absolute; bottom: 0; left: 0; right: 0;
            background: rgba(0,0,0,0.25); padding: 4px 10px; font-size: 7px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .card-footer .footer-address {
            display: block;
            width: 100%;
            text-align: left;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Back card */
        .back .card-body {
            flex-direction: column;
            padding: 8px 10px 22px; /* bottom padding agar tidak overlap footer */
            text-align: center;
        }
        .back .info-section { margin-bottom: 5px; }
        .back .info-section h4 {
            font-size: 6.5px; margin-bottom: 2px;
            opacity: 0.7; text-transform: uppercase; letter-spacing: 0.3px;
        }
        .back .info-section p {
            font-size: 8px;
            word-break: break-word;
            line-height: 1.3;
        }

        .back .barcode {
            text-align: center;
            margin-top: auto;
            padding-top: 5px;
            border-top: 1px solid rgba(255,255,255,0.2);
        }
        .back .barcode-placeholder {
            background: white; color: #000; padding: 3px 10px;
            font-family: 'Courier New', monospace; font-size: 11px;
            font-weight: bold; letter-spacing: 2px;
            display: inline-block; border-radius: 3px;
        }

        .print-button {
            padding: 10px 20px; background: #3b82f6; color: white;
            border: none; border-radius: 8px; cursor: pointer;
            font-size: 13px; font-weight: bold;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            min-width: 140px;
            margin: 0 6px 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .back-button {
            padding: 10px 20px; background: #6b7280; color: white;
            border: none; border-radius: 8px; text-decoration: none; font-size: 13px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            min-width: 140px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 6px 18px;
        }
        @media (max-width: 575.98px) {
            body { padding: 12px; }
            .preview-page { width: 100%; }
            .print-actions {
                position: static;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                padding: 0;
                margin-bottom: 14px;
                background: transparent;
            }
            .print-button,
            .back-button {
                width: calc(50% - 6px);
                min-width: 0;
                padding: 10px 12px;
                margin: 0 3px 14px;
            }
            .upload-area {
                width: 100%;
                padding: 12px;
            }
        }

        .upload-area {
            text-align: center; margin-top: 16px; background: white;
            border-radius: 8px; padding: 12px 20px; display: inline-block;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .upload-label {
            cursor: pointer; background: #3b82f6; color: white;
            padding: 8px 16px; border-radius: 6px; font-size: 13px;
            display: inline-block;
        }
        .upload-label:hover { background: #2563eb; }
        .upload-hint { font-size: 11px; color: #888; margin-top: 6px; }

        @media print {
            @page { margin: 10mm; size: A4 portrait; }
            body {
                background: white;
                padding: 0;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            .no-print { display: none !important; }
            .preview-page {
                width: 100%;
                margin: 0;
                text-align: center;
            }
            .card-container { gap: 8mm; }
            .student-card {
                box-shadow: none;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
        }
    </style>
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

        // Resolve orang tua: prioritas primary, fallback ke first, lalu legacy fields
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

    <a href="{{ route('waka.manajemen-siswa.show', $siswa) }}" class="back-button no-print">← Kembali</a>
    <button onclick="window.print()" class="print-button no-print">
        <i class="fas fa-print"></i> Cetak Kartu
    </button>

    <div class="preview-page">
        <div class="card-container">
            {{-- Front Card --}}
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
                        <img id="kartuFoto"
                            src="{{ $siswa->foto ? asset('storage/' . $siswa->foto) : '' }}"
                            alt="Foto"
                            style="{{ $siswa->foto ? '' : 'display:none' }}">
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

            {{-- Back Card --}}
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
                        <h4>Nama Orang Tua/Wali</h4>
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

        {{-- Upload Foto Area --}}
        <div class="upload-area no-print">
            <label for="fotoInput" class="upload-label">
                <i class="fas fa-camera"></i> Pasang Foto dari Komputer
            </label>
            <input type="file" id="fotoInput" accept="image/*" style="display: none;">
            <p class="upload-hint">
                @if($siswa->foto)
                    Foto profil sudah ada. Klik untuk mengganti tampilan saat cetak.
                @else
                    Siswa belum punya foto profil. Pilih foto dari komputer untuk ditampilkan di kartu.
                @endif
            </p>
        </div>

        <p class="no-print" style="margin-top: 10px; color: #666; font-size: 12px;">
            Ukuran kartu: 85.6mm x 53.98mm (standar ID Card)
        </p>
    </div>

    <script>
        document.getElementById('fotoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(evt) {
                const img = document.getElementById('kartuFoto');
                const placeholder = document.getElementById('fotoPlaceholder');
                img.src = evt.target.result;
                img.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
        });
    </script>
</body>
</html>
