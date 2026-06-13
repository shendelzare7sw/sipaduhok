@php
    $ujian = $konten;
    $previewTitle = $ujian->judul_ujian ?? '-';
    $isLatihan = ($ujian->tipe_ujian ?? null) === 'latihan';
    $kontenLabel = $isLatihan ? 'Latihan' : 'Ujian';
    $tipeLabel = method_exists($ujian, 'getTipeLabelAttribute') ? $ujian->tipe_label : $ujian->tipe_ujian;
@endphp

@extends('guru.lms.arsip.preview-wrapper', [
    'previewTitle' => $previewTitle,
    'kontenLabel' => $kontenLabel,
])

@section('preview-content')
    <h2 class="preview-section-title">
        <i class="fas {{ $isLatihan ? 'fa-pencil-ruler' : 'fa-file-alt' }} me-2 {{ $isLatihan ? 'preview-title-icon-latihan' : 'preview-title-icon-ujian' }}"></i>{{ $ujian->judul_ujian }}
    </h2>

    <div class="preview-meta-row">
        <span class="badge preview-type-badge {{ $isLatihan ? 'preview-type-badge-latihan' : 'preview-type-badge-ujian' }}">
            {{ $tipeLabel }}
        </span>
        @if($ujian->mataPelajaran)
            <span><i class="fas fa-book"></i> {{ $ujian->mataPelajaran->nama_mapel }}</span>
        @endif
        @if($ujian->kelas)
            <span><i class="fas fa-school"></i> {{ $ujian->kelas->nama_kelas }}</span>
        @endif
        @if($ujian->kelas?->tahunAjaran)
            <span><i class="fas fa-calendar-alt"></i> TA {{ $ujian->kelas->tahunAjaran->nama_tahun_ajaran }}</span>
        @endif
        @if($ujian->tanggal_mulai)
            <span><i class="fas fa-play-circle"></i> {{ \Carbon\Carbon::parse($ujian->tanggal_mulai)->locale('id')->translatedFormat('d M Y, H:i') }}</span>
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

    @php
        $soalList = ($ujian->soalUjian ?? collect())->sortBy('urutan')->values();
        $totalBobot = $soalList->sum('bobot_nilai');
    @endphp
    <div class="preview-section">
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
            <div class="preview-section-label preview-label-tight">
                Daftar Soal ({{ $soalList->count() }} soal - Total bobot: {{ $totalBobot }})
            </div>
            @if($soalList->isNotEmpty())
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>Kunci jawaban ditampilkan dengan tanda hijau
                </small>
            @endif
        </div>

        @if($soalList->isEmpty())
            <div class="alert alert-warning preview-warning">
                <i class="fas fa-exclamation-triangle me-1"></i>Belum ada soal di {{ strtolower($kontenLabel) }} ini.
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
                                     class="soal-image-media">
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
                                            <span class="preview-option-text">{{ $pilihan[$letter] }}</span>
                                            @if($isBenar)
                                                <i class="fas fa-check-circle ms-auto preview-success-icon" title="Kunci jawaban"></i>
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
                                            <span class="preview-option-text">{{ $p['text'] ?? '' }}</span>
                                            <span class="badge preview-small-badge {{ ($p['benar'] ?? false) ? 'bg-success' : 'bg-secondary' }}">
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
                                <i class="fas fa-info-circle me-1"></i>Soal essay/uraian - koreksi manual oleh guru.
                                @if($soal->kunci_jawaban)
                                    <div class="preview-guidance">
                                        <strong>Pedoman jawaban:</strong> {{ \Illuminate\Support\Str::limit($soal->kunci_jawaban, 200) }}
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
