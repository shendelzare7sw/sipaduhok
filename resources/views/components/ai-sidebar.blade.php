{{--
    AI Question Bank Generator Sidebar Component

    Usage:
    @include('components.ai-sidebar', [
        'ujianId' => $ujian->id,
        'kelasId' => $kelas->id,
        'mapelId' => $mapel->id,
        'subjectName' => $mapel->nama_mapel,
        'generateUrl' => route('guru.lms.ujian.soal.ai-generate', [...]),
    ])

    ID dan name field dipakai resources/js/components/ai-question-generator.js — jangan diubah.
--}}

@php
    $aiProvider = \App\Models\AppSetting::where('key', 'ai_provider')->value('value') ?? 'groq';
    $currentModel = ai_model_aktif(\App\Models\AppSetting::where('key', 'ai_model')->value('value'), $aiProvider);
    $modelShortName = trim(preg_replace('/\s*\(.*\)$/', '', config("ai-models.available.{$aiProvider}", [])[$currentModel]['label'] ?? $currentModel));
    $field = 'mt-1 block w-full min-w-0 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100';
@endphp

<div id="aiQuestionSidebar"
     data-generate-url="{{ $generateUrl ?? url("/guru/lms/{$kelasId}/{$mapelId}/ujian/{$ujianId}/ai-generate-questions") }}"
     x-data="{ hints: { easy: 'Fakta dasar & hafalan', medium: 'Aplikasi konsep & perhitungan', hard: 'Analisis & problem solving' }, waktu: { 3: '10-15', 5: '15-20', 7: '20-25', 10: '25-30' }, tingkat: 'medium', jumlah: '5' }"
     class="fixed inset-y-0 right-0 z-[80] flex w-full translate-x-full flex-col overflow-hidden bg-white shadow-2xl transition-transform duration-300 md:w-[450px] md:min-w-[350px] md:max-w-[800px]">
    <div class="absolute inset-y-0 left-0 z-20 hidden w-2 cursor-ew-resize items-center justify-center hover:bg-indigo-50 md:flex" id="aiSidebarResizeHandle" title="Geser untuk mengubah ukuran panel">
        <div class="h-10 w-0.5 rounded-full bg-indigo-300"></div>
    </div>

    <div class="z-10 flex shrink-0 items-center justify-between gap-3 bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-4 text-white">
        <div class="min-w-0">
            <h2 class="flex flex-wrap items-center gap-2 text-base font-extrabold !text-white"><i class="fa-solid fa-robot" aria-hidden="true"></i>AI Question Generator<span class="rounded-full bg-white/20 px-2 py-0.5 text-[10px] font-bold">BETA</span></h2>
            <p class="mt-0.5 text-xs text-indigo-100">Generate soal otomatis dengan AI</p>
        </div>
        <button type="button" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border-0 bg-white/10 text-white hover:bg-white/20" data-close-ai-sidebar title="Tutup panel" aria-label="Tutup panel"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="min-h-0 flex-1 space-y-5 overflow-y-auto px-4 pb-[max(1rem,env(safe-area-inset-bottom))] pt-4 sm:px-6 sm:pt-6">
        <div class="flex flex-wrap items-center gap-2 text-xs" id="aiModelStatusContainer">
            <span class="text-slate-500"><i class="fa-solid fa-microchip mr-1" aria-hidden="true"></i>Model:</span>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-800 px-2.5 py-1 font-bold text-white" id="aiModelStatusBadge" title="{{ $currentModel }}"><span class="h-1.5 w-1.5 rounded-full bg-emerald-400" aria-hidden="true"></span>{{ $modelShortName }}</span>
            <span class="hidden rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-bold text-amber-800" id="aiModelSwitchInfo"></span>
        </div>

        <form id="aiGeneratorForm" class="space-y-4" @reset="$nextTick(() => { tingkat = 'medium'; jumlah = '5'; })">
            <label for="aiTopic" class="block text-xs font-bold text-slate-700"><i class="fa-solid fa-book-open mr-1 text-indigo-600" aria-hidden="true"></i>Topik/materi soal <span class="text-rose-600">*</span>
                <input type="text" id="aiTopic" name="topic" required maxlength="200" placeholder="Contoh: Persamaan Kuadrat, Siklus Air, Struktur Teks Berita" class="{{ $field }}">
                <span class="mt-1 block font-normal text-slate-500">Tuliskan topik spesifik untuk hasil terbaik.</span>
            </label>

            <label class="block text-xs font-bold text-slate-700"><i class="fa-solid fa-graduation-cap mr-1 text-emerald-600" aria-hidden="true"></i>Mata pelajaran
                <input type="text" value="{{ $subjectName }}" readonly class="{{ $field }} bg-slate-50 text-slate-500">
            </label>

            <label for="aiQuestionType" class="block text-xs font-bold text-slate-700"><i class="fa-solid fa-list-check mr-1 text-amber-500" aria-hidden="true"></i>Tipe soal <span class="text-rose-600">*</span>
                <select id="aiQuestionType" name="type" required class="{{ $field }}">
                    <option value="pilihan_ganda" selected>Pilihan Ganda (A-E)</option>
                    <option value="pilihan_ganda_kompleks">Pilihan Ganda Kompleks (multi-jawaban)</option>
                    <option value="benar_salah">Benar / Salah</option>
                    <option value="uraian">Uraian / Essay</option>
                    <option value="isian_singkat">Isian Singkat</option>
                </select>
            </label>

            <fieldset>
                <legend class="text-xs font-bold text-slate-700"><i class="fa-solid fa-signal mr-1 text-rose-500" aria-hidden="true"></i>Tingkat kesulitan <span class="text-rose-600">*</span></legend>
                <div class="mt-1 grid grid-cols-3 gap-2">
                    @foreach(['easy' => ['Mudah', 'fa-face-smile', 'has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 has-[:checked]:text-emerald-700'], 'medium' => ['Sedang', 'fa-face-meh', 'has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50 has-[:checked]:text-amber-700'], 'hard' => ['Sulit', 'fa-face-frown', 'has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50 has-[:checked]:text-rose-700']] as $value => [$text, $icon, $tone])
                        <label class="flex min-h-10 cursor-pointer items-center justify-center gap-1.5 rounded-xl border border-slate-300 bg-white px-2 text-xs font-bold text-slate-600 {{ $tone }}">
                            <input type="radio" name="difficulty" id="difficulty{{ ucfirst($value) }}" value="{{ $value }}" x-model="tingkat" @checked($value === 'medium') class="sr-only">
                            <i class="fa-solid {{ $icon }}" aria-hidden="true"></i>{{ $text }}
                        </label>
                    @endforeach
                </div>
                <p class="mt-1 text-xs text-slate-500" id="difficultyHint" x-text="hints[tingkat]">Aplikasi konsep & perhitungan</p>
            </fieldset>

            <label for="aiQuestionCount" class="block text-xs font-bold text-slate-700"><i class="fa-solid fa-hashtag mr-1 text-sky-600" aria-hidden="true"></i>Jumlah soal <span class="text-rose-600">*</span>
                <select id="aiQuestionCount" name="count" required x-model="jumlah" class="{{ $field }}">
                    <option value="3">3 soal</option>
                    <option value="5" selected>5 soal</option>
                    <option value="7">7 soal</option>
                    <option value="10">10 soal (maksimal)</option>
                </select>
                <span class="mt-1 block font-normal text-slate-500"><i class="fa-solid fa-clock mr-1" aria-hidden="true"></i>~<span id="estimatedTime" x-text="waktu[jumlah]">15-20</span> detik</span>
            </label>

            <details class="group rounded-xl border border-slate-200 bg-slate-50">
                <summary class="flex cursor-pointer list-none items-center gap-2 px-3 py-2.5 text-xs font-bold text-slate-600"><i class="fa-solid fa-gear" aria-hidden="true"></i>Opsi lanjutan<i class="fa-solid fa-chevron-down ml-auto text-[10px] transition group-open:rotate-180" aria-hidden="true"></i></summary>
                <div class="space-y-3 border-t border-slate-200 px-3 py-3">
                    <label class="flex cursor-pointer items-start gap-2.5 text-xs text-slate-600">
                        <input type="checkbox" id="generateNarasi" name="generate_narasi" class="mt-0.5 h-4 w-4 shrink-0 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span><strong class="block text-slate-900"><i class="fa-solid fa-book-open-reader mr-1 text-indigo-600" aria-hidden="true"></i>Generate dengan narasi/teks bacaan</strong>AI membuat teks bacaan/konteks untuk soal (cocok untuk pemahaman bacaan, analisis teks, dll).</span>
                    </label>
                    <label for="customInstructions" class="block text-xs font-bold text-slate-700">Instruksi tambahan (opsional)
                        <textarea id="customInstructions" name="custom_instructions" rows="2" maxlength="500" placeholder="Contoh: fokus pada soal cerita, gunakan konteks kehidupan sehari-hari" class="{{ $field }}"></textarea>
                        <span class="mt-1 block font-normal text-slate-500">Maksimal 500 karakter.</span>
                    </label>
                </div>
            </details>

            <button type="submit" id="generateBtn" class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 px-5 text-sm font-extrabold text-white shadow-md hover:from-indigo-700 hover:to-violet-700 disabled:cursor-wait disabled:opacity-80"><i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i>Generate soal dengan AI</button>

            <div class="flex items-start gap-2 rounded-xl border border-sky-200 bg-sky-50 p-3 text-xs text-sky-900">
                <i class="fa-solid fa-lightbulb mt-0.5" aria-hidden="true"></i>
                <div>
                    <strong>Tips:</strong>
                    <ul class="mt-1 list-disc space-y-0.5 pl-4">
                        <li>Gunakan topik yang <strong>spesifik</strong>.</li>
                        <li>AI menyesuaikan bahasa &amp; kompleksitas dengan jenjang kelas.</li>
                        <li>Tinjau &amp; edit soal sebelum ditambahkan.</li>
                        <li>Soal uraian dilengkapi <strong>rubrik penilaian</strong>.</li>
                    </ul>
                </div>
            </div>
        </form>

        <section id="generatedQuestionsSection" class="hidden border-t border-slate-200 pt-5">
            <h3 class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fa-solid fa-circle-check text-emerald-600" aria-hidden="true"></i>Soal yang di-generate (<span id="generatedCount">0</span>)</h3>
            <div id="generatedQuestionsList" class="mt-3 space-y-3"></div>
            <div class="sticky bottom-0 mt-4 flex justify-end gap-2 bg-white py-2">
                <button type="button" id="regenerateBtn" class="inline-flex min-h-10 items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-4 text-xs font-bold text-slate-700 hover:bg-slate-50"><i class="fa-solid fa-rotate-right" aria-hidden="true"></i>Regenerate</button>
                <button type="button" id="addSelectedBtn" class="inline-flex min-h-10 items-center gap-1.5 rounded-xl bg-emerald-600 px-4 text-xs font-bold text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"><i class="fa-solid fa-circle-plus" aria-hidden="true"></i>Tambahkan (<span id="selectedCount">0</span>)</button>
            </div>
        </section>
    </div>
</div>

<div id="aiSidebarBackdrop" class="invisible fixed inset-0 z-[70] bg-slate-950/55 opacity-0 backdrop-blur-sm transition" data-close-ai-sidebar></div>

<input type="hidden" id="aiGeneratorUjianId" value="{{ $ujianId }}">
<input type="hidden" id="aiGeneratorKelasId" value="{{ $kelasId }}">
<input type="hidden" id="aiGeneratorMapelId" value="{{ $mapelId }}">
