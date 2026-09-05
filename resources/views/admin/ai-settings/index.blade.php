@extends('layouts.app')

@section('title', 'Pengaturan AI Assistant')
@section('page-title', 'Pengaturan AI Assistant')
@section('page-subtitle', 'Satu konfigurasi untuk chatbot, generator soal, dan bantuan penilaian')

@section('content')
@php
    $providers = [
        'groq' => ['label' => 'Groq Cloud', 'note' => 'Cepat untuk chatbot dan otomatisasi teks.', 'icon' => 'fa-bolt', 'tone' => 'bg-violet-50 text-violet-700'],
        'gemini' => ['label' => 'Google Gemini', 'note' => 'Multimodal untuk teks, gambar, dan PDF.', 'icon' => 'fa-gem', 'tone' => 'bg-blue-50 text-blue-700'],
    ];
    $roles = [
        'ketua_pkbm' => ['Ketua PKBM', 'Ringkasan dan keputusan operasional'],
        'wakil_kepala_sekolah' => ['Wakil Kepala Sekolah', 'Jadwal dan koordinasi akademik'],
        'sekretaris' => ['Sekretaris', 'Administrasi dan publikasi'],
        'bendahara' => ['Bendahara', 'Keuangan dan pembayaran'],
        'wali_kelas' => ['Wali Kelas', 'Presensi dan rapor kelas'],
        'guru_pengajar' => ['Guru Pengajar', 'LMS, nilai, dan soal'],
        'siswa' => ['Siswa', 'Panduan penggunaan sistem'],
        'orang_tua' => ['Orang Tua', 'Informasi siswa dan tagihan'],
    ];
    $models = config('ai-models.available');
@endphp

<div
    x-data="{
        provider: @js($provider),
        showGroqKey: false,
        showGeminiKey: false,
        testing: false,
        async testConnection() {
            const apiKey = this.provider === 'groq' ? this.$refs.groqKey.value : this.$refs.geminiKey.value;
            const model = this.provider === 'groq' ? this.$refs.groqModel.value : this.$refs.geminiModel.value;
            if (!apiKey) { await Swal.fire({ icon: 'warning', title: 'API key belum diisi', text: 'Isi API key provider yang dipilih terlebih dahulu.', confirmButtonColor: '#285dcc' }); return; }
            this.testing = true;
            try {
                const response = await fetch(@js(route('admin.ai-settings.test')), { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': @js(csrf_token()) }, body: JSON.stringify({ provider: this.provider, api_key: apiKey, model }) });
                const result = await response.json();
                await Swal.fire({ icon: result.success ? 'success' : 'error', title: result.success ? 'Koneksi berhasil' : 'Koneksi gagal', text: result.message, confirmButtonColor: '#285dcc' });
            } catch (error) {
                await Swal.fire({ icon: 'error', title: 'Koneksi gagal', text: 'Server tidak dapat menguji provider saat ini.', confirmButtonColor: '#285dcc' });
            } finally { this.testing = false; }
        }
    }"
    class="min-w-0 w-full space-y-4"
>
    <header><p class="text-xs font-bold uppercase tracking-wide text-brand-600">Integrasi sistem</p><h2 class="text-xl font-extrabold text-slate-950 sm:text-2xl">Konfigurasi AI terpadu</h2><p class="mt-1 text-sm text-slate-500">Pilih satu provider global, lalu tentukan fitur dan pengguna yang diizinkan.</p></header>

    @if($errors->any())
        <section class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"><strong class="block">Pengaturan belum dapat disimpan.</strong><ul class="mt-2 list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></section>
    @endif

    <form action="{{ route('admin.ai-settings.update') }}" method="POST" class="space-y-4">
        @csrf @method('PUT')

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 p-4 sm:p-5"><h3 class="font-extrabold text-slate-950"><i class="fas fa-cloud mr-2 text-brand-600"></i>1. Provider dan model</h3><p class="mt-1 text-xs text-slate-500">Provider ini dipakai oleh seluruh fitur AI dan seluruh role.</p></div>
            <div class="space-y-5 p-4 sm:p-5">
                <fieldset><legend class="mb-2 text-xs font-bold uppercase tracking-wide text-slate-500">Provider aktif</legend><div class="grid gap-3 sm:grid-cols-2">@foreach($providers as $key => $info)<label class="flex cursor-pointer items-start gap-3 rounded-2xl border p-4 transition" :class="provider === '{{ $key }}' ? 'border-brand-500 bg-brand-50 ring-2 ring-brand-100' : 'border-slate-200 hover:bg-slate-50'"><input type="radio" name="ai_provider" value="{{ $key }}" x-model="provider" class="mt-1 h-4 w-4 border-slate-300 text-brand-600"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $info['tone'] }}"><i class="fas {{ $info['icon'] }}"></i></span><span><strong class="block text-sm text-slate-950">{{ $info['label'] }}</strong><span class="mt-1 block text-xs leading-5 text-slate-500">{{ $info['note'] }}</span></span></label>@endforeach</div></fieldset>

                <div x-show="provider === 'groq'" x-cloak class="grid gap-4 lg:grid-cols-2">
                    <label class="lg:col-span-2"><span class="mb-1.5 block text-xs font-bold text-slate-700">Groq API key</span><span class="flex"><input x-ref="groqKey" name="groq_api_key" :type="showGroqKey ? 'text' : 'password'" value="{{ $groqApiKey }}" autocomplete="off" placeholder="gsk_..." class="h-11 min-w-0 flex-1 rounded-l-xl border border-slate-300 px-3 text-sm outline-none focus:border-brand-500"><button type="button" @click="showGroqKey = !showGroqKey" class="h-11 w-11 rounded-r-xl border border-l-0 border-slate-300 text-slate-500" :aria-label="showGroqKey ? 'Sembunyikan API key' : 'Tampilkan API key'"><i class="fas" :class="showGroqKey ? 'fa-eye-slash' : 'fa-eye'"></i></button></span><span class="mt-1 block text-xs text-slate-500">Buat key di <a href="https://console.groq.com/keys" target="_blank" rel="noopener" class="font-bold text-brand-700">Groq Console</a>.</span></label>
                    <label><span class="mb-1.5 block text-xs font-bold text-slate-700">Model chat</span><select x-ref="groqModel" name="ai_model" :disabled="provider !== 'groq'" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm">@foreach($models['groq'] as $id => $info)<option value="{{ $id }}" @selected($model === $id)>{{ $info['label'] }}</option>@endforeach</select></label>
                    <label><span class="mb-1.5 block text-xs font-bold text-slate-700">Model vision</span><select name="ai_vision_model" :disabled="provider !== 'groq'" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm">@foreach($models['groq'] as $id => $info)@if($info['vision'] ?? false)<option value="{{ $id }}" @selected($visionModel === $id)>{{ $info['label'] }}</option>@endif @endforeach</select></label>
                </div>

                <div x-show="provider === 'gemini'" x-cloak class="grid gap-4 lg:grid-cols-2">
                    <label class="lg:col-span-2"><span class="mb-1.5 block text-xs font-bold text-slate-700">Gemini API key</span><span class="flex"><input x-ref="geminiKey" name="gemini_api_key" :type="showGeminiKey ? 'text' : 'password'" value="{{ $geminiApiKey }}" autocomplete="off" placeholder="AIza..." class="h-11 min-w-0 flex-1 rounded-l-xl border border-slate-300 px-3 text-sm outline-none focus:border-brand-500"><button type="button" @click="showGeminiKey = !showGeminiKey" class="h-11 w-11 rounded-r-xl border border-l-0 border-slate-300 text-slate-500" :aria-label="showGeminiKey ? 'Sembunyikan API key' : 'Tampilkan API key'"><i class="fas" :class="showGeminiKey ? 'fa-eye-slash' : 'fa-eye'"></i></button></span><span class="mt-1 block text-xs text-slate-500">Buat key di <a href="https://aistudio.google.com/app/apikey" target="_blank" rel="noopener" class="font-bold text-brand-700">Google AI Studio</a>.</span></label>
                    <label><span class="mb-1.5 block text-xs font-bold text-slate-700">Model chat</span><select x-ref="geminiModel" name="ai_model" :disabled="provider !== 'gemini'" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm">@foreach($models['gemini'] as $id => $info)<option value="{{ $id }}" @selected($model === $id)>{{ $info['label'] }}</option>@endforeach</select></label>
                    <label><span class="mb-1.5 block text-xs font-bold text-slate-700">Model vision</span><select name="ai_vision_model" :disabled="provider !== 'gemini'" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm">@foreach($models['gemini'] as $id => $info)@if($info['vision'] ?? false)<option value="{{ $id }}" @selected($visionModel === $id)>{{ $info['label'] }}</option>@endif @endforeach</select></label>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-slate-50 p-3"><p class="text-xs leading-5 text-slate-500"><i class="fas fa-shield-alt mr-1 text-emerald-600"></i>API key disimpan di pengaturan aplikasi dan tidak dikirim saat tes selain ke endpoint provider.</p><button type="button" @click="testConnection()" :disabled="testing" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-emerald-300 bg-white px-4 text-xs font-bold text-emerald-700 disabled:opacity-50"><i class="fas" :class="testing ? 'fa-spinner fa-spin' : 'fa-plug'"></i><span x-text="testing ? 'Menguji...' : 'Tes koneksi'">Tes koneksi</span></button></div>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 p-4 sm:p-5"><h3 class="font-extrabold text-slate-950"><i class="fas fa-wand-magic-sparkles mr-2 text-brand-600"></i>2. Fitur AI</h3><p class="mt-1 text-xs text-slate-500">Batasi fungsi AI sesuai kebutuhan operasional sekolah.</p></div>
            <div class="grid sm:grid-cols-2">
                <label class="flex cursor-pointer items-start gap-3 border-b border-slate-100 p-4 sm:border-b-0 sm:border-r sm:p-5"><span class="min-w-0 flex-1"><strong class="block text-sm text-slate-950">Pembatasan konteks chatbot</strong><span class="mt-1 block text-xs leading-5 text-slate-500">Chatbot hanya menjawab pertanyaan tentang menu dan fitur SIPADUHOK.</span></span><span class="relative inline-flex h-6 w-11 shrink-0 items-center"><input type="checkbox" name="context_restriction_enabled" class="peer sr-only" @checked($contextRestrictionEnabled)><span class="absolute inset-0 rounded-full bg-slate-300 transition peer-checked:bg-brand-600"></span><span class="absolute left-1 h-4 w-4 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span></span></label>
                <label class="flex cursor-pointer items-start gap-3 p-4 sm:p-5"><span class="min-w-0 flex-1"><strong class="block text-sm text-slate-950">Generator soal AI</strong><span class="mt-1 block text-xs leading-5 text-slate-500">Guru dapat membuat rancangan soal ujian dan latihan secara otomatis.</span></span><span class="relative inline-flex h-6 w-11 shrink-0 items-center"><input type="checkbox" name="ai_question_generator_enabled" class="peer sr-only" @checked($aiQuestionGeneratorEnabled)><span class="absolute inset-0 rounded-full bg-slate-300 transition peer-checked:bg-brand-600"></span><span class="absolute left-1 h-4 w-4 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span></span></label>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 p-4 sm:p-5"><h3 class="font-extrabold text-slate-950"><i class="fas fa-user-shield mr-2 text-emerald-600"></i>3. Pengguna chatbot</h3><p class="mt-1 text-xs text-slate-500">Admin selalu memiliki akses. Aktifkan role lain sesuai kesiapan penggunaan.</p></div>
            <div class="grid sm:grid-cols-2 xl:grid-cols-4">@foreach($roles as $key => [$label, $description])<label class="flex cursor-pointer items-start gap-3 border-b border-slate-100 p-4 sm:border-r sm:p-5 xl:[&:nth-child(4n)]:border-r-0"><span class="min-w-0 flex-1"><strong class="block text-sm text-slate-950">{{ $label }}</strong><span class="mt-1 block text-xs leading-5 text-slate-500">{{ $description }}</span></span><span class="relative inline-flex h-6 w-11 shrink-0 items-center"><input type="checkbox" name="chatbot_{{ $key }}" class="peer sr-only" @checked($chatbotEnabledRoles[$key] ?? false)><span class="absolute inset-0 rounded-full bg-slate-300 transition peer-checked:bg-brand-600"></span><span class="absolute left-1 h-4 w-4 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span></span></label>@endforeach</div>
        </section>

        <div class="sticky bottom-3 z-10 flex justify-end rounded-2xl border border-slate-200 bg-white/95 p-3 shadow-lg backdrop-blur"><button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-sm font-bold text-white hover:bg-brand-700"><i class="fas fa-save"></i>Simpan seluruh pengaturan</button></div>
    </form>
</div>
@endsection
