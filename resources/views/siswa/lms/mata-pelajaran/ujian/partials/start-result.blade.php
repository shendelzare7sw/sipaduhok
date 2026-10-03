{{--
    Layar mulai & hasil Latihan/Ujian (shell LMS). Dipakai show.blade.php dan show_latihan.blade.php.
    Ekuivalen-piksel dengan tampilan lama (nilai warna/ukuran diambil dari computed style baseline).
    Param: $ujian, $ujianSiswa, $soalList, $mataPelajaran, $isLatihan, $routePrefix
--}}
@php
    $btn = 'inline-flex items-center justify-center rounded-[6px] border px-6 py-[7px] text-[15px] leading-[22.95px] no-underline transition';
    $btnLg = 'inline-flex items-center justify-center rounded-[8px] border px-6 py-3 text-base leading-[24.48px]';
    $btnOff = $btnLg.' cursor-default border-[#8592a3] bg-[#8592a3] text-white opacity-65';
@endphp
<div x-data="examStart(@js($isLatihan))" class="px-[13px] text-[15px] leading-[1.5] text-slate-800">
    <div class="mx-auto w-full px-[13px] min-[992px]:w-2/3">
        @if($ujianSiswa && in_array($ujianSiswa->status, ['selesai', 'dinilai']))
            {{-- HASIL --}}
            <div class="rounded-[8px] bg-white p-[30px] text-center shadow-[0_2px_4px_rgba(0,0,0,0.1)]">
                <i class="fas fa-check-circle mb-4 text-[60px] leading-[60px] text-[#71dd37]"></i>
                <h3 class="mb-2 text-[15px] font-normal leading-[16.5px] text-[#71dd37]">{{ $isLatihan ? 'Latihan Selesai!' : 'Ujian Selesai!' }}</h3>
                <p class="mb-6 text-[#a1acb8]">{{ $isLatihan ? 'Jawaban Anda telah tersimpan.' : 'Semua jawaban Anda telah tersimpan.' }}</p>

                <div class="mb-4 rounded-[12px] border border-[#d9dee3] bg-[#fcfcfd] p-[15px] text-[#495057]">
                    <p class="mb-1 text-[12.75px] leading-[19.125px] text-[#a1acb8]">Diselesaikan pada:</p>
                    <strong>{{ $ujianSiswa->waktu_selesai->format('d F Y, H:i') }} WIB</strong>
                </div>

                @if($ujian->tampilkan_nilai)
                    @if($ujianSiswa->nilai !== null)
                        <div class="my-6">
                            <h1 class="text-[40px] font-bold leading-[44px] text-[#696cff]">{{ number_format($ujianSiswa->nilai_terbaik ?? $ujianSiswa->nilai, 1) }}/100</h1>
                            <span class="text-[#a1acb8]">Nilai Terbaik Anda</span>
                            @if(($ujianSiswa->pengulangan_ke ?? 1) > 1)
                                <div class="mt-2 text-[12.75px] text-[#a1acb8]">Nilai Percobaan Terakhir: {{ number_format($ujianSiswa->nilai, 1) }}</div>
                            @endif
                        </div>
                    @else
                        <div class="my-6">
                            <i class="fas fa-hourglass-half mb-2 text-[45px] leading-[45px] text-[#ffab00]"></i>
                            <h5 class="text-[18px] font-medium leading-[1.1] text-[#8592a3]">Menunggu Penilaian Guru</h5>
                        </div>
                    @endif
                @else
                    <div class="my-6">
                        <h5 class="text-[18px] font-medium leading-[1.1] text-[#8592a3]">Terima Kasih Sudah Menyelesaikan {{ ucwords(str_replace('_', ' ', $ujian->tipe_ujian)) }}</h5>
                    </div>
                @endif

                <div class="mt-6 flex flex-wrap items-center justify-center gap-2">
                    @if($ujian->bisa_diulang && $ujian->isOngoing())
                        @php
                            $sisaPengulangan = $ujian->batas_pengulangan ? max(0, $ujian->batas_pengulangan - (($ujianSiswa->pengulangan_ke ?? 1) - 1)) : null;
                        @endphp
                        @if($sisaPengulangan === null || $sisaPengulangan > 0)
                            <form x-ref="retakeForm" id="form-retake" action="{{ route($routePrefix . 'retake', [$mataPelajaran->id, $ujian->id]) }}" method="POST" class="m-0">
                                @csrf
                                <button type="button" data-confirm-retake x-on:click="confirmRetake()" class="{{ $btn }} border-[#ffab00] bg-[#ffab00] text-white shadow-[0_2px_4px_rgba(255,171,0,0.4)] hover:border-[#e69a00] hover:bg-[#e69a00]">
                                    <i class="fas fa-redo-alt mr-2"></i> Kerjakan Ulang @if($sisaPengulangan !== null) (Sisa: {{ $sisaPengulangan }}) @endif
                                </button>
                            </form>
                        @else
                            <button type="button" disabled class="{{ $btn }} cursor-default border-[#8592a3] bg-[#8592a3] text-white opacity-65">
                                <i class="fas fa-ban mr-2"></i> Pengulangan Habis
                            </button>
                        @endif
                    @endif
                    @if($ujian->tampilkan_riwayat)
                        <a href="{{ route($routePrefix . 'review', [$mataPelajaran->id, $ujian->id]) }}" class="{{ $btn }} border-[#696cff] bg-transparent text-[#696cff] hover:bg-[#696cff] hover:text-white">
                            <i class="fas fa-search mr-2"></i> Lihat Pembahasan
                        </a>
                    @endif
                    <a href="{{ route('siswa.lms.mapel.show', $mataPelajaran->id) }}" class="{{ $btn }} border-[#696cff] bg-[#696cff] text-white shadow-[0_2px_4px_rgba(105,108,255,0.4)] hover:border-[#5f61e6] hover:bg-[#5f61e6] hover:text-white">
                        <i class="fas fa-arrow-left mr-2"></i> {{ $isLatihan ? 'Kembali' : 'Kembali ke Mata Pelajaran' }}
                    </a>
                </div>
            </div>
        @else
            {{-- MULAI --}}
            <div class="rounded-[8px] bg-white p-[30px] shadow-[0_2px_4px_rgba(0,0,0,0.1)]">
                <div class="mb-6 text-center">
                    <h3 class="m-0 text-[15px] font-bold leading-[16.5px] text-[#696cff]">{{ $ujian->judul_ujian }}</h3>
                    <span class="inline-block rounded px-[0.593em] py-[0.52em] text-[0.8125em] font-medium uppercase leading-[0.75] text-white bg-[#8592a3]">{{ strtoupper(str_replace('_', ' ', $ujian->tipe_ujian)) }}</span>
                </div>

                <div class="mb-6 grid gap-4 md:grid-cols-4">
                    @php
                        $infoBoxes = [
                            ['fas fa-clock text-[#ffab00]', $ujian->durasi_menit == 0 ? 'Tanpa Batas' : $ujian->durasi_menit . ' Menit', 'Durasi'],
                            ['fas fa-list-ol text-[#03c3ec]', $soalList->count() . ' Soal', 'Jumlah Soal'],
                            ['fas fa-calendar-alt text-[#71dd37]', $ujian->tanggal_mulai->format('d M'), 'Tanggal'],
                            ['fas fa-redo-alt text-[#696cff]',
                                $ujian->bisa_diulang
                                    ? ($ujian->batas_pengulangan ? max(0, $ujian->batas_pengulangan - ($ujianSiswa->pengulangan_ke ?? 0)) . ' Kali' : 'Tak Terbatas')
                                    : '1 Kali',
                                $ujian->bisa_diulang ? 'Sisa Pengulangan' : 'Batas Ujian'],
                        ];
                    @endphp
                    @foreach($infoBoxes as [$icon, $value, $label])
                        <div class="rounded-[6px] border border-[#dee2e6] bg-[#f8f9fa] p-5 text-center">
                            <i class="{{ $icon }} mb-[10px] text-[32px] leading-8"></i>
                            <h5 class="mb-[5px] text-[20px] font-semibold leading-[22px] text-[#566a7f]">{{ $value }}</h5>
                            <small class="text-[12px] leading-[18px] text-[#a1acb8]">{{ $label }}</small>
                        </div>
                    @endforeach
                </div>

                <div class="mb-4 rounded-[12px] border border-[#b3edf9] bg-[#d7f5fc] p-[15px] text-[#03c3ec]">
                    <strong><i class="fas fa-info-circle mr-2"></i>Petunjuk:</strong>
                    <ul class="mb-0 mt-2 list-none p-0">
                        <li>Berdoalah sebelum mengerjakan</li>
                        <li>Waktu berjalan otomatis saat tombol "Mulai" diklik</li>
                        @unless($isLatihan)
                            <li>Tidak dapat mengulang ujian yang sudah disubmit</li>
                        @endunless
                        <li>Pastikan koneksi internet stabil</li>
                    </ul>
                </div>

                <div class="mt-6 text-center">
                    @if(!$ujian->is_active)
                        <button type="button" class="{{ $btnOff }}" disabled><i class="fas fa-lock mr-2"></i> Belum Dirilis</button>
                    @elseif($ujian->isOngoing())
                        <form action="{{ route($routePrefix . 'mulai', [$mataPelajaran->id, $ujian->id]) }}" method="POST" x-on:submit="startExam($event)">
                            @csrf
                            <button type="submit" x-bind:disabled="starting" class="{{ $btnLg }} border-[#696cff] bg-[#696cff] text-white shadow-[0_2px_4px_rgba(105,108,255,0.4)] hover:border-[#5f61e6] hover:bg-[#5f61e6] disabled:opacity-65">
                                <span x-show="!starting"><i class="fas fa-play mr-2"></i> {{ $isLatihan ? 'Mulai Latihan Sekarang' : 'Mulai Ujian Sekarang' }}</span>
                                <span x-show="starting" x-cloak><i class="fas fa-spinner fa-spin mr-2"></i> Mempersiapkan Ujian...</span>
                            </button>
                        </form>
                    @elseif($ujian->tanggal_mulai->isFuture())
                        <button type="button" class="{{ $btnOff }}" disabled><i class="fas fa-hourglass-start mr-2"></i> Belum Dimulai</button>
                    @else
                        <button type="button" class="{{ $btnOff }}" disabled><i class="fas fa-history mr-2"></i> {{ $isLatihan ? 'Sudah Berakhir' : 'Ujian Sudah Berakhir' }}</button>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
