<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Nilai - {{ $siswa->nama_lengkap }}</title>
    @vite('resources/css/wali-nilai-print.css')
</head>
<body class="bg-[#f0f2f5] pt-[54px] font-[Arial,Helvetica,sans-serif] text-[9pt] text-black print:bg-white print:pt-0 print:text-[8.5pt]">
    @php
        $isKelasAkhir = str_contains(strtolower($kelas->nama_kelas), '9') ||
                        str_contains(strtolower($kelas->nama_kelas), '12') ||
                        str_contains(strtolower($kelas->nama_kelas), 'ix') ||
                        str_contains(strtolower($kelas->nama_kelas), 'xii');
        $semesterLabel = ucfirst($semester ?? 'genap');
    @endphp

    {{-- ── Controls bar ── --}}
    <div data-print-toolbar class="fixed inset-x-0 top-0 z-[9999] flex h-[46px] items-center justify-between gap-[10px] bg-[#2c3340] px-[14px] text-white shadow-[0_2px_8px_rgba(0,0,0,0.4)] print:hidden">
        <div class="flex items-center gap-[6px]">
            <button type="button" class="inline-flex touch-manipulation select-none items-center gap-[5px] rounded-[5px] border border-solid border-white/30 bg-white/[0.08] px-[14px] py-[7px] text-[14px] font-bold leading-none text-white active:bg-white/25" data-zoom-action="out" title="Perkecil">−</button>
            <span class="min-w-[46px] text-center text-[12px] font-bold text-[#d0d0d0]" id="zoomLabel">100%</span>
            <button type="button" class="inline-flex touch-manipulation select-none items-center gap-[5px] rounded-[5px] border border-solid border-white/30 bg-white/[0.08] px-[14px] py-[7px] text-[14px] font-bold leading-none text-white active:bg-white/25" data-zoom-action="in" title="Perbesar">+</button>
            <button type="button" class="inline-flex touch-manipulation select-none items-center gap-[5px] rounded-[5px] border border-solid border-white/30 bg-white/[0.08] px-[10px] py-[7px] text-[11px] font-bold leading-none text-white active:bg-white/25" data-zoom-action="fit" title="Sesuaikan layar">Fit</button>
        </div>
        <button type="button" class="inline-flex touch-manipulation select-none items-center gap-[5px] rounded-[5px] border border-solid border-[#dc3545] bg-[#dc3545] px-[14px] py-[7px] text-[14px] font-bold leading-none text-white active:bg-[#bb2d3b]" data-print-page>
            <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M6 9V3h12v6M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1-2 2h-2M6 15h12v6H6zM18 12h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg> Cetak / PDF
        </button>
    </div>

    {{-- ── Scroll wrapper ── --}}
    <div id="scaleWrapper" class="flex min-h-[calc(100vh-54px)] justify-center overflow-x-auto px-[10px] pb-[50px] pt-5 print:block print:min-h-0 print:overflow-visible print:p-0">
        <div id="pageContainer" class="w-[1058px] min-w-[1058px] min-h-[750px] shrink-0 bg-white px-[22px] py-[18px] shadow-[0_4px_20px_rgba(0,0,0,0.15)] print:!min-h-0 print:!min-w-0 print:!w-full print:!p-0 print:shadow-none">
            @include('partials.print-header', ['cabang' => $cabang ?? null])

            <div class="mb-2 text-center">
                <strong class="text-[11pt] uppercase">Rekap Nilai Siswa</strong>
            </div>

            <div class="mb-2 inline-block rounded-[4px] border border-solid border-[#90caf9] bg-[#e3f2fd] px-[10px] py-[2px] text-[8pt] font-bold text-[#1565c0]">
                <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="2"/><path d="M7 3v4m10-4v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg> Semester {{ $semesterLabel }}
            </div>

            <table class="mb-2 w-full text-[8.5pt] [&_td]:px-[6px] [&_td]:py-[2px]">
                <tr>
                    <td class="w-[110px] whitespace-nowrap font-bold">Nama Siswa</td><td>: <strong>{{ $siswa->nama_lengkap }}</strong></td>
                    <td class="w-[110px] whitespace-nowrap font-bold">Kelas</td><td>: {{ $kelas->nama_kelas }}</td>
                    <td class="w-[110px] whitespace-nowrap font-bold">NIS / NISN</td><td>: {{ $siswa->nis }} / {{ $siswa->nisn }}</td>
                    <td class="w-[110px] whitespace-nowrap font-bold">Tahun Ajaran</td><td>: {{ $kelas->tahunAjaran->nama_tahun_ajaran }}</td>
                </tr>
            </table>

            <table class="data w-full border-collapse text-[8.5pt] [&_th]:border [&_th]:border-solid [&_th]:border-[#444] [&_th]:bg-[#e9ecef] [&_th]:px-[3px] [&_th]:py-1 [&_th]:text-center [&_th]:font-bold [&_td]:border [&_td]:border-solid [&_td]:border-[#444] [&_td]:px-[3px] [&_td]:py-1">
                <thead>
                    <tr>
                        <th rowspan="2" class="w-[24px]">No</th>
                        <th rowspan="2" class="min-w-[110px] !text-left">Mata Pelajaran</th>
                        <th colspan="2">Tugas</th>
                        <th colspan="2">Latihan</th>
                        <th colspan="2">UH</th>
                        <th rowspan="2" class="w-[38px]">PTS</th>
                        <th rowspan="2" class="w-[38px]">PAS</th>
                        <th rowspan="2" class="w-[48px]">N. Akhir</th>
                        <th rowspan="2" class="w-[44px]">Predikat</th>
                        <th rowspan="2" class="w-[60px]">Status</th>
                    </tr>
                    <tr>
                        <th class="w-[30px]">Jml</th>
                        <th class="w-[36px]">Rata</th>
                        <th class="w-[30px]">Jml</th>
                        <th class="w-[36px]">Rata</th>
                        <th class="w-[30px]">Jml</th>
                        <th class="w-[36px]">Rata</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach($mataPelajaranList as $mapel)
                        @php
                            $nilai = $nilaiData[$mapel->id] ?? null;
                            $tugasCount = $latihanCount = $uhCount = 0;
                            if ($nilai) {
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($nilai->{'tugas_'.$i}   !== null) $tugasCount++;
                                    if ($nilai->{'latihan_'.$i} !== null) $latihanCount++;
                                    if ($nilai->{'uh_'.$i}      !== null) $uhCount++;
                                }
                            }
                            $isTuntas = $nilai && $nilai->nilai_akhir !== null && $nilai->nilai_akhir >= 70;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td>{{ $mapel->nama_mapel }}</td>
                            <td class="text-center">{{ $tugasCount }}/5</td>
                            <td class="bg-[#d4edda] text-center">{{ $nilai && $nilai->rata_tugas !== null ? number_format($nilai->rata_tugas, 1) : '-' }}</td>
                            <td class="text-center">{{ $latihanCount }}/5</td>
                            <td class="bg-[#d4edda] text-center">{{ $nilai && $nilai->rata_latihan !== null ? number_format($nilai->rata_latihan, 1) : '-' }}</td>
                            <td class="text-center">{{ $uhCount }}/5</td>
                            <td class="bg-[#d4edda] text-center">{{ $nilai && $nilai->rata_uh !== null ? number_format($nilai->rata_uh, 1) : '-' }}</td>
                            <td class="text-center">{{ $nilai && $nilai->pts !== null ? number_format($nilai->pts, 0) : '-' }}</td>
                            <td class="text-center">{{ $nilai && $nilai->pas !== null ? number_format($nilai->pas, 0) : '-' }}</td>
                            <td class="bg-[#c3e6cb] text-center font-bold">{{ $nilai && $nilai->nilai_akhir !== null ? number_format($nilai->nilai_akhir, 2) : '-' }}</td>
                            <td class="text-center">{{ $nilai ? $nilai->predikat() : '-' }}</td>
                            <td class="text-center">{{ $nilai && $nilai->nilai_akhir !== null ? ($isTuntas ? 'Tuntas' : 'Blm Tuntas') : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($isKelasAkhir)
            <h3 class="mb-[6px] mt-[14px] text-[9.5pt]">Penilaian Tingkat Akhir</h3>
            <table class="data w-full border-collapse text-[8.5pt] [&_th]:border [&_th]:border-solid [&_th]:border-[#444] [&_th]:bg-[#e9ecef] [&_th]:px-[3px] [&_th]:py-1 [&_th]:text-center [&_th]:font-bold [&_td]:border [&_td]:border-solid [&_td]:border-[#444] [&_td]:px-[3px] [&_td]:py-1">
                <thead>
                    <tr>
                        <th class="w-[24px]">No</th>
                        <th class="min-w-[130px] !text-left">Mata Pelajaran</th>
                        <th class="w-[52px]">TO 1</th>
                        <th class="w-[52px]">TO 2</th>
                        <th class="w-[52px]">TO 3</th>
                        <th class="w-[52px]">UPK</th>
                        <th class="w-[70px]">Ujian Praktek</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach($mataPelajaranList as $mapel)
                        @php $nilai = $nilaiData[$mapel->id] ?? null; @endphp
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td>{{ $mapel->nama_mapel }}</td>
                            <td class="text-center">{{ $nilai && $nilai->to_1 !== null ? number_format($nilai->to_1, 0) : '-' }}</td>
                            <td class="text-center">{{ $nilai && $nilai->to_2 !== null ? number_format($nilai->to_2, 0) : '-' }}</td>
                            <td class="text-center">{{ $nilai && $nilai->to_3 !== null ? number_format($nilai->to_3, 0) : '-' }}</td>
                            <td class="text-center">{{ $nilai && $nilai->upk !== null ? number_format($nilai->upk, 0) : '-' }}</td>
                            <td class="text-center">{{ $nilai && $nilai->ujian_praktek !== null ? number_format($nilai->ujian_praktek, 0) : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

            <div class="mt-[14px]">
                <p class="mb-2 text-[7.5pt] italic text-[#555]">Dicetak pada: {{ now()->locale('id')->isoFormat('dddd, D MMMM Y HH:mm') }}</p>
                <div class="flex justify-between">
                    <div class="w-[30%] text-center text-[8.5pt]">
                        <p>Mengetahui, Wali Kelas</p>
                        <div class="h-[38px] [border-bottom:1px_solid_#333]"></div>
                        <p>( _________________________ )</p>
                    </div>
                    <div class="w-[30%] text-center text-[8.5pt]">
                        <p>Wali Siswa / Wali Siswa</p>
                        <div class="h-[38px] [border-bottom:1px_solid_#333]"></div>
                        <p>( _________________________ )</p>
                    </div>
                </div>
            </div>
        </div>{{-- end .page-container --}}
    </div>{{-- end #scaleWrapper --}}
    <script src="{{ asset('js/wali-kelas/nilai/print.js') }}"></script>
</body>
</html>
