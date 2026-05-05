@php
    $ujian = $konten;
    $previewTitle = $ujian->judul_ujian ?? '-';
    $isLatihan = ($ujian->tipe_ujian ?? null) === 'latihan';
    $kontenLabel = $isLatihan ? 'Latihan' : 'Ujian';
    $tipeLabel = method_exists($ujian, 'getTipeLabelAttribute') ? $ujian->tipe_label : $ujian->tipe_ujian;
@endphp

@extends('monitoring-lms.preview.wrapper', [
    'previewTitle' => $previewTitle,
    'kontenLabel' => $kontenLabel,
])

@section('preview-content')
    <h2 class="preview-section-title">
        <i class="fas {{ $isLatihan ? 'fa-pencil-ruler' : 'fa-file-alt' }} me-2"
           style="color: {{ $isLatihan ? '#7c3aed' : '#dc2626' }};"></i>{{ $ujian->judul_ujian }}
    </h2>

    <div class="preview-meta-row">
        <span class="badge" style="background: {{ $isLatihan ? 'rgba(124,58,237,0.1)' : 'rgba(220,38,38,0.1)' }}; color: {{ $isLatihan ? '#5b21b6' : '#b91c1c' }}; font-size: 0.7rem; padding: 4px 10px; border-radius: 999px; font-weight: 600;">
            {{ $tipeLabel }}
        </span>
        @if($ujian->guru)
            <span><i class="fas fa-user-tie"></i> {{ $ujian->guru->nama_lengkap }}</span>
        @endif
        @if($ujian->mataPelajaran)
            <span><i class="fas fa-book"></i> {{ $ujian->mataPelajaran->nama_mapel }}</span>
        @endif
        @if($ujian->kelas)
            <span><i class="fas fa-school"></i> {{ $ujian->kelas->nama_kelas }}</span>
        @endif
        @if($ujian->tanggal_mulai)
            <span><i class="fas fa-play-circle"></i> {{ $ujian->tanggal_mulai->locale('id')->translatedFormat('d M Y, H:i') }}</span>
        @endif
        @if($ujian->durasi_menit)
            <span><i class="fas fa-clock"></i> {{ $ujian->durasi_menit }} menit</span>
        @endif
    </div>

    @if($ujian->deskripsi)
        <div class="preview-section">
            <div class="preview-section-label">Deskripsi</div>
            <div class="preview-section-body">{!! nl2br(e($ujian->deskripsi)) !!}</div>
        </div>
    @endif

    {{-- Settings --}}
    <div class="preview-section">
        <div class="preview-section-label">Pengaturan {{ $kontenLabel }}</div>
        <div class="settings-grid">
            <div class="setting-item">
                <i class="fas fa-power-off"></i>
                <span>Status: <strong>{{ ($ujian->is_active ?? false) ? 'Aktif' : 'Nonaktif' }}</strong></span>
            </div>
            <div class="setting-item">
                <i class="fas fa-random"></i>
                <span>Acak Soal: <strong>{{ ($ujian->acak_soal ?? false) ? 'Ya' : 'Tidak' }}</strong></span>
            </div>
            <div class="setting-item">
                <i class="fas fa-eye"></i>
                <span>Tampilkan Nilai: <strong>{{ ($ujian->tampilkan_nilai ?? false) ? 'Ya' : 'Tidak' }}</strong></span>
            </div>
            <div class="setting-item">
                <i class="fas fa-redo"></i>
                <span>Bisa Diulang: <strong>{{ ($ujian->bisa_diulang ?? false) ? 'Ya' : 'Tidak' }}</strong></span>
            </div>
        </div>
    </div>

    {{-- Soal list --}}
    @php
        $soalList = ($ujian->soalUjian ?? collect())->sortBy('urutan')->values();
        $totalBobot = $soalList->sum('bobot_nilai');
    @endphp
    <div class="preview-section">
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
            <div class="preview-section-label" style="margin-bottom: 0;">
                Daftar Soal ({{ $soalList->count() }} soal · Total bobot: {{ $totalBobot }})
            </div>
            @if($soalList->isNotEmpty())
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>Kunci jawaban ditampilkan dengan tanda hijau
                </small>
            @endif
        </div>

        @if($soalList->isEmpty())
            <div class="alert alert-warning" style="border-radius: 10px; border: none; background: rgba(217, 119, 6, 0.08); color: #92400e;">
                <i class="fas fa-exclamation-triangle me-1"></i>Belum ada soal yang dibuat untuk {{ strtolower($kontenLabel) }} ini.
            </div>
        @else
            <div class="soal-list">
                @foreach($soalList as $soal)
                    <div class="soal-card">
                        <div class="soal-header">
                            <div class="soal-nomor">Soal #{{ $loop->iteration }}</div>
                            <div class="soal-meta">
                                <span class="soal-badge">{{ \App\Models\SoalUjian::getTipeSoalLabel($soal->tipe_soal) }}</span>
                                <span class="soal-bobot">{{ $soal->bobot_nilai ?? 0 }} poin</span>
                            </div>
                        </div>

                        @if($soal->narasi)
                            <div class="soal-narasi">{!! nl2br(e($soal->narasi)) !!}</div>
                        @endif

                        @if($soal->image_path)
                            <div class="soal-image">
                                <img src="{{ asset('storage/' . $soal->image_path) }}" alt="Gambar soal #{{ $loop->iteration }}"
                                     style="max-width: 100%; max-height: 400px; border-radius: 8px; border: 1px solid var(--border-color);">
                            </div>
                        @endif

                        <div class="soal-pertanyaan">{!! nl2br(e($soal->pertanyaan)) !!}</div>

                        @if(in_array($soal->tipe_soal, ['pilihan_ganda', 'pilihan_ganda_kompleks']))
                            @php
                                $pilihan = $soal->pilihan_jawaban ?? [];
                                $kunci = $soal->kunci_jawaban;
                                if (!is_array($kunci)) {
                                    $decoded = json_decode($kunci ?? '', true);
                                    $kunci = is_array($decoded) ? $decoded : [strtoupper(trim((string) $kunci))];
                                }
                                $kunciNorm = array_map(fn($k) => strtoupper(trim((string) $k)), $kunci);
                            @endphp
                            <div class="soal-pilihan-list">
                                @foreach(['A', 'B', 'C', 'D', 'E'] as $letter)
                                    @if(isset($pilihan[$letter]) && $pilihan[$letter] !== '')
                                        @php $isBenar = in_array($letter, $kunciNorm); @endphp
                                        <div class="soal-pilihan {{ $isBenar ? 'pilihan-benar' : '' }}">
                                            <span class="pilihan-letter">{{ $letter }}</span>
                                            <span style="flex: 1;">{{ $pilihan[$letter] }}</span>
                                            @if($isBenar)
                                                <i class="fas fa-check-circle ms-auto" style="color: #16a34a;" title="Kunci jawaban"></i>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @elseif($soal->tipe_soal === 'benar_salah')
                            @php $pernyataanList = $soal->pilihan_jawaban['pernyataan'] ?? []; @endphp
                            @if(!empty($pernyataanList))
                                <div class="soal-pilihan-list">
                                    @foreach($pernyataanList as $idx2 => $p)
                                        <div class="soal-pilihan">
                                            <span class="pilihan-letter">{{ $idx2 + 1 }}</span>
                                            <span style="flex: 1;">{{ $p['text'] ?? '' }}</span>
                                            <span class="badge {{ ($p['benar'] ?? false) ? 'bg-success' : 'bg-secondary' }}" style="font-size: 0.7rem;">
                                                Kunci: {{ ($p['benar'] ?? false) ? 'Benar' : 'Salah' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @elseif($soal->tipe_soal === 'isian_singkat')
                            <div class="soal-kunci">
                                <span class="kunci-label">Kunci Jawaban:</span>
                                @php
                                    $kj = $soal->pilihan_jawaban['jawaban_benar'] ?? null;
                                    if (!is_array($kj)) {
                                        $kj = $soal->kunci_jawaban ? [$soal->kunci_jawaban] : [];
                                    }
                                @endphp
                                <span>{{ implode(' / ', array_filter($kj)) ?: '-' }}</span>
                            </div>
                        @else
                            <div class="soal-kunci kunci-essay">
                                <i class="fas fa-info-circle me-1"></i>Soal essay/uraian — koreksi manual oleh guru.
                                @if($soal->kunci_jawaban)
                                    <div style="margin-top: 6px;">
                                        <strong>Pedoman jawaban:</strong> {{ Str::limit($soal->kunci_jawaban, 200) }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

@endsection
