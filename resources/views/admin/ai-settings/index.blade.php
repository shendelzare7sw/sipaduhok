@extends('layouts.sneat')

@section('title', 'Pengaturan AI Assistant')

@section('page-title', 'Pengaturan AI Assistant')
@section('page-subtitle', 'Konfigurasi integrasi kecerdasan buatan untuk fitur otomatisasi')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/admin/ai-settings/index.css'])
@endsection

@section('content')
    <div class="admin-ai-settings-page" data-test-url="{{ route('admin.ai-settings.test') }}" data-csrf-token="{{ csrf_token() }}">
    <div class="row g-4">
        <!-- Settings Column -->
        <div class="col-12 col-md-8 col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-bottom bg-transparent py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-robot me-2 text-primary"></i>
                        Konfigurasi AI Provider
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.ai-settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Hidden fields to preserve Chatbot Access Control settings --}}
                        @foreach(['ketua_pkbm','wakil_kepala_sekolah','sekretaris','bendahara','wali_kelas','guru_pengajar','siswa','orang_tua'] as $role)
                            @if($chatbotEnabledRoles[$role] ?? false)
                                <input type="hidden" name="chatbot_{{ $role }}" value="on">
                            @endif
                        @endforeach

                        <div class="mb-4 pb-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="fw-bold mb-1"><i class="fas fa-shield-alt me-2 text-primary"></i>Pembatasan Konteks Chatbot</h6>
                                    <small class="text-muted">Jika diaktifkan, Chatbot <strong>HANYA</strong> menjawab pertanyaan seputar menu &amp; fitur SIPADUHOK. Pertanyaan di luar konteks (cuaca, politik, hiburan, dll) akan ditolak sopan. Disarankan tetap aktif untuk fokus penggunaan.</small>
                                </div>
                                <div class="form-check form-switch form-switch-lg mb-0 ai-switch-wrap">
                                    <input class="form-check-input ai-switch-input" type="checkbox" role="switch" name="context_restriction_enabled" id="context_restriction_enabled" {{ $contextRestrictionEnabled ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold mb-1"><i class="fas fa-pen-fancy me-2 text-primary"></i>AI Question Generator</h6>
                                    <small class="text-muted">Izinkan Guru untuk menggunakan fitur AI Generator Soal Otomatis pada halaman Kelola Soal Ujian dan Latihan.</small>
                                </div>
                                <div class="form-check form-switch form-switch-lg mb-0 ai-switch-wrap">
                                    <input class="form-check-input ai-switch-input" type="checkbox" role="switch" name="ai_question_generator_enabled" id="ai_question_generator_enabled" {{ $aiQuestionGeneratorEnabled ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-globe me-2 text-primary"></i>AI Provider (GLOBAL)
                            </label>
                            <select class="form-select form-select-lg ai-provider-select" name="ai_provider" id="ai_provider">
                                <option value="groq" {{ $provider == 'groq' ? 'selected' : '' }}>
                                    Groq Cloud (Llama / Qwen / Mixtral - FREE)
                                </option>
                                <option value="gemini" {{ $provider == 'gemini' ? 'selected' : '' }}>
                                    Google Gemini (2.5 Flash - FREE)
                                </option>
                            </select>
                            <div class="alert alert-info mt-2 mb-0 ai-provider-note">
                                <i class="fas fa-info-circle me-1"></i>
                                <strong>Provider ini berlaku untuk SEMUA user</strong> (Admin, Guru, Siswa).
                                Groq Cloud dan Google Gemini menawarkan Tier Gratis yang generous.
                            </div>
                        </div>

                        <!-- Groq API Key -->
                        <div class="mb-3 provider-field" id="groq_field">
                            <label class="form-label fw-bold">Groq Cloud API Key</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input type="password" class="form-control" name="groq_api_key" id="groq_api_key"
                                    value="{{ $groqApiKey }}" placeholder="gsk_...">
                                <button class="btn btn-outline-secondary" type="button" id="toggleGroqApiKey">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                Dapatkan API Key gratis di <a href="https://console.groq.com/keys" target="_blank">Groq Console</a>.
                            </div>
                        </div>

                        <!-- Gemini API Key -->
                        <div class="mb-3 provider-field is-hidden" id="gemini_field">
                            <label class="form-label fw-bold">Google Gemini API Key</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input type="password" class="form-control" name="gemini_api_key" id="gemini_api_key"
                                    value="{{ $geminiApiKey }}" placeholder="AIza...">
                                <button class="btn btn-outline-secondary" type="button" id="toggleGeminiApiKey">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                Dapatkan API Key gratis di <a href="https://aistudio.google.com/app/apikey" target="_blank">Google AI Studio</a>.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Model Text (Chat)</label>
                            <select class="form-select" name="ai_model" id="ai_model">
                                <optgroup label="Groq Cloud (FREE)">
                                    <option value="qwen/qwen3-32b" {{ $model == 'qwen/qwen3-32b' ? 'selected' : '' }}>Qwen 3 32B (60 RPM - Balanced)</option>
                                    <option value="llama-3.3-70b-versatile" {{ $model == 'llama-3.3-70b-versatile' ? 'selected' : '' }}>Llama 3.3 70B (Recommended - Best)</option>
                                    <option value="llama-3.1-8b-instant" {{ $model == 'llama-3.1-8b-instant' ? 'selected' : '' }}>Llama 3.1 8B (Fastest - Light)</option>
                                    <option value="openai/gpt-oss-120b" {{ $model == 'openai/gpt-oss-120b' ? 'selected' : '' }}>GPT OSS 120B (Heavy Model)</option>
                                    <option value="allam-2-7b" {{ $model == 'allam-2-7b' ? 'selected' : '' }}>Allam 2 7B</option>
                                    <option value="groq/compound" {{ $model == 'groq/compound' ? 'selected' : '' }}>Groq Compound</option>
                                </optgroup>
                                <optgroup label="Google Gemini (FREE)">
                                    <option value="gemini-2.5-flash" {{ $model == 'gemini-2.5-flash' ? 'selected' : '' }}>Gemini 2.5 Flash (Vision + PDF Support)</option>
                                </optgroup>
                            </select>
                            <div class="form-text">Fitur Auto-Fallback aktif: Jika model Groq melebihi batas Rate Limit, sistem akan otomatis beralih meminjam model lain.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Model Vision (Multimodal)</label>
                            <select class="form-select" name="ai_vision_model" id="ai_vision_model">
                                <optgroup label="Llama 4 (Groq)">
                                    <option value="meta-llama/llama-4-scout-17b-16e-instruct" {{ $visionModel == 'meta-llama/llama-4-scout-17b-16e-instruct' ? 'selected' : '' }}>Llama 4 Scout (Image Only)</option>
                                </optgroup>
                                <optgroup label="Google Gemini">
                                    <option value="gemini-2.5-flash" {{ $visionModel == 'gemini-2.5-flash' ? 'selected' : '' }}>Gemini 2.5 Flash (Image + PDF - Only FREE)</option>
                                </optgroup>
                            </select>
                            <div class="form-text">Model ini digunakan khusus untuk menganalisis gambar dan PDF pada tugas. Hanya Gemini 2.5 Flash yang gratis.</div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <button type="button" class="btn btn-outline-success" id="testConnectionBtn">
                                <i class="fas fa-plug me-2"></i> Test Koneksi
                            </button>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                <i class="fas fa-save me-2"></i> Simpan Pengaturan
                            </button>
                        </div>

                        <!-- Connection Status Alert (Inline) -->
                        <div id="connectionAlert" class="alert mt-3 d-none fade show" role="alert">
                            <div class="d-flex align-items-center">
                                <i id="connectionIcon" class="fas fa-info-circle me-2 fs-4"></i>
                                <div>
                                    <strong id="connectionTitle" class="d-block">Status Koneksi</strong>
                                    <span id="connectionMessage">Checking...</span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Column -->
        <div class="col-12 col-md-4 col-lg-5">
            <div class="card bg-label-info border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                         <div class="avatar me-2">
                            <div class="rounded bg-white text-info d-flex align-items-center justify-content-center ai-brain-icon">
                                <i class="fas fa-brain"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="card-title fw-bold text-dark mb-1">AI Grading Assistant</h5>
                            <p class="card-text text-muted mb-0">Fitur ini membantu guru memberikan penilaian awal dan feedback otomatis untuk soal uraian.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                 <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="fas fa-lightbulb text-warning me-2"></i>Cara Kerja</h6>
                    <ul class="timeline ms-2">
                        <li class="timeline-item pb-4 border-start border-2 ps-3 timeline-border">
                            <span class="timeline-indicator-advanced text-primary fw-bold">1</span>
                            <div class="ms-2">
                                <div class="fw-bold text-dark">Analisis Konteks</div>
                                <p class="text-muted small mb-0">AI membaca Pertanyaan, Kunci Jawaban, dan Jawaban Siswa.</p>
                            </div>
                        </li>
                        <li class="timeline-item pb-4 border-start border-2 ps-3 timeline-border">
                            <span class="timeline-indicator-advanced text-primary fw-bold">2</span>
                            <div class="ms-2">
                                <div class="fw-bold text-dark">Evaluasi Cerdas</div>
                                <p class="text-muted small mb-0">Model bahasa besar (LLM) mengevaluasi relevansi dan ketepatan jawaban.</p>
                            </div>
                        </li>
                        <li class="timeline-item border-start border-2 ps-3 timeline-border-transparent">
                             <span class="timeline-indicator-advanced text-success fw-bold">3</span>
                            <div class="ms-2">
                                <div class="fw-bold text-dark">Rekomendasi</div>
                                <p class="text-muted small mb-0">Sistem memberikan saran skor (0-100) dan feedback konstruktif untuk guru.</p>
                            </div>
                        </li>
                    </ul>
                 </div>
            </div>
        </div>
    </div>

    {{-- Chatbot Access Control Section --}}
    <div class="row g-4 mt-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom bg-transparent py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-user-shield me-2 text-success"></i>
                        Kontrol Akses Chatbot AI
                    </h5>
                    <p class="text-muted small mb-0 mt-2">Atur role mana saja yang dapat mengakses fitur AI Chatbot Assistant. Role <strong>Admin</strong> selalu memiliki akses.</p>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.ai-settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Hidden fields to preserve other settings --}}
                        <input type="hidden" name="groq_api_key" value="{{ $groqApiKey }}">
                        <input type="hidden" name="gemini_api_key" value="{{ $geminiApiKey }}">
                        <input type="hidden" name="ai_model" value="{{ $model }}">
                        <input type="hidden" name="ai_vision_model" value="{{ $visionModel }}">
                        <input type="hidden" name="ai_provider" value="{{ $provider }}">
                        <input type="hidden" name="context_restriction_enabled" id="context_restriction_enabled_hidden" value="{{ $contextRestrictionEnabled ? '1' : '0' }}">
                        <input type="hidden" name="ai_question_generator_enabled" id="ai_question_generator_enabled_hidden" value="{{ $aiQuestionGeneratorEnabled ? '1' : '0' }}">

                        <div class="row g-3">
                            {{-- Staff Roles (Left Column) --}}
                            <div class="col-12 col-md-6">
                                <h6 class="text-primary fw-bold mb-3">
                                    <i class="fas fa-users me-2"></i>Role Staff
                                </h6>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="chatbot_ketua_pkbm" id="chatbot_ketua_pkbm"
                                        {{ ($chatbotEnabledRoles['ketua_pkbm'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="chatbot_ketua_pkbm">
                                        <strong>Ketua PKBM</strong>
                                        <span class="text-muted d-block small">Akses penuh untuk kepala pusat kegiatan</span>
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="chatbot_wakil_kepala_sekolah" id="chatbot_wakil_kepala_sekolah"
                                        {{ ($chatbotEnabledRoles['wakil_kepala_sekolah'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="chatbot_wakil_kepala_sekolah">
                                        <strong>Wakil Kepala Sekolah</strong>
                                        <span class="text-muted d-block small">Bantuan untuk tugas wakil kepala sekolah</span>
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="chatbot_sekretaris" id="chatbot_sekretaris"
                                        {{ ($chatbotEnabledRoles['sekretaris'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="chatbot_sekretaris">
                                        <strong>Sekretaris</strong>
                                        <span class="text-muted d-block small">Asisten untuk tugas administrasi</span>
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="chatbot_bendahara" id="chatbot_bendahara"
                                        {{ ($chatbotEnabledRoles['bendahara'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="chatbot_bendahara">
                                        <strong>Bendahara</strong>
                                        <span class="text-muted d-block small">Bantuan untuk keuangan dan pembayaran</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Teaching Staff & Users (Right Column) --}}
                            <div class="col-12 col-md-6">
                                <h6 class="text-primary fw-bold mb-3">
                                    <i class="fas fa-chalkboard-teacher me-2"></i>Role Pengajar & Pengguna
                                </h6>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="chatbot_wali_kelas" id="chatbot_wali_kelas"
                                        {{ ($chatbotEnabledRoles['wali_kelas'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="chatbot_wali_kelas">
                                        <strong>Wali Kelas</strong>
                                        <span class="text-muted d-block small">Bantuan untuk presensi dan rapor</span>
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="chatbot_guru_pengajar" id="chatbot_guru_pengajar"
                                        {{ ($chatbotEnabledRoles['guru_pengajar'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="chatbot_guru_pengajar">
                                        <strong>Guru Pengajar</strong>
                                        <span class="text-muted d-block small">Asisten untuk LMS, nilai, dan soal ujian</span>
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="chatbot_siswa" id="chatbot_siswa"
                                        {{ ($chatbotEnabledRoles['siswa'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="chatbot_siswa">
                                        <strong>Siswa</strong>
                                        <span class="text-muted d-block small">Bantuan untuk pertanyaan seputar sistem</span>
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="chatbot_orang_tua" id="chatbot_orang_tua"
                                        {{ ($chatbotEnabledRoles['orang_tua'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="chatbot_orang_tua">
                                        <strong>Orang Tua</strong>
                                        <span class="text-muted d-block small">Bantuan untuk cek nilai dan pembayaran</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <div class="text-muted small">
                                <i class="fas fa-info-circle me-1"></i>
                                Perubahan akan berlaku setelah user login kembali
                            </div>
                            <button type="submit" class="btn btn-success px-4 shadow-sm">
                                <i class="fas fa-save me-2"></i> Simpan Pengaturan Akses
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    </div>
@endsection

@section('scripts')
    @vite(['resources/js/admin/ai-settings/index.js'])
@endsection
