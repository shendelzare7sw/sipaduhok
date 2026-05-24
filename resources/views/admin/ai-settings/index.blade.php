@extends('layouts.sneat')

@section('title', 'Pengaturan AI Assistant')

@section('page-title', 'Pengaturan AI Assistant')
@section('page-subtitle', 'Konfigurasi integrasi kecerdasan buatan untuk fitur otomatisasi')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
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
                                <div class="form-check form-switch form-switch-lg mb-0" style="padding-left: 3rem;">
                                    <input class="form-check-input" type="checkbox" role="switch" name="context_restriction_enabled" id="context_restriction_enabled" style="width: 3rem; height: 1.5rem;" {{ $contextRestrictionEnabled ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold mb-1"><i class="fas fa-pen-fancy me-2 text-primary"></i>AI Question Generator</h6>
                                    <small class="text-muted">Izinkan Guru untuk menggunakan fitur AI Generator Soal Otomatis pada halaman Kelola Soal Ujian dan Latihan.</small>
                                </div>
                                <div class="form-check form-switch form-switch-lg mb-0" style="padding-left: 3rem;">
                                    <input class="form-check-input" type="checkbox" role="switch" name="ai_question_generator_enabled" id="ai_question_generator_enabled" style="width: 3rem; height: 1.5rem;" {{ $aiQuestionGeneratorEnabled ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-globe me-2 text-primary"></i>AI Provider (GLOBAL)
                            </label>
                            <select class="form-select form-select-lg" name="ai_provider" id="ai_provider" style="border: 2px solid #3b82f6;">
                                <option value="groq" {{ $provider == 'groq' ? 'selected' : '' }}>
                                    Groq Cloud (Llama / Qwen / Mixtral - FREE)
                                </option>
                                <option value="gemini" {{ $provider == 'gemini' ? 'selected' : '' }}>
                                    Google Gemini (2.5 Flash - FREE)
                                </option>
                            </select>
                            <div class="alert alert-info mt-2 mb-0" style="font-size: 13px;">
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
                        <div class="mb-3 provider-field" id="gemini_field" style="display: none;">
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
                            <div class="rounded bg-white text-info d-flex align-items-center justify-content-center" style="width: 100%; height: 100%;">
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
                        <li class="timeline-item pb-4 border-start border-2 ps-3" style="border-color: #e5e7eb;">
                            <span class="timeline-indicator-advanced text-primary fw-bold">1</span>
                            <div class="ms-2">
                                <div class="fw-bold text-dark">Analisis Konteks</div>
                                <p class="text-muted small mb-0">AI membaca Pertanyaan, Kunci Jawaban, dan Jawaban Siswa.</p>
                            </div>
                        </li>
                        <li class="timeline-item pb-4 border-start border-2 ps-3" style="border-color: #e5e7eb;">
                            <span class="timeline-indicator-advanced text-primary fw-bold">2</span>
                            <div class="ms-2">
                                <div class="fw-bold text-dark">Evaluasi Cerdas</div>
                                <p class="text-muted small mb-0">Model bahasa besar (LLM) mengevaluasi relevansi dan ketepatan jawaban.</p>
                            </div>
                        </li>
                        <li class="timeline-item border-start border-2 ps-3" style="border-color: transparent;">
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
                        @if($contextRestrictionEnabled)
                            <input type="hidden" name="context_restriction_enabled" value="on">
                        @endif
                        @if($aiQuestionGeneratorEnabled)
                            <input type="hidden" name="ai_question_generator_enabled" value="on">
                        @endif

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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const providerSelect = document.getElementById('ai_provider');
            const modelSelect = document.getElementById('ai_model');
            const groqField = document.getElementById('groq_field');
            const geminiField = document.getElementById('gemini_field');

            // Model mapping by provider (Only FREE & Verified working models)
            const modelsByProvider = {
                groq: [
                    'qwen/qwen3-32b',
                    'llama-3.3-70b-versatile',
                    'llama-3.1-8b-instant',
                    'openai/gpt-oss-120b',
                    'allam-2-7b',
                    'groq/compound'
                ],
                gemini: [
                    'gemini-2.5-flash'
                ]
            };

            // Toggle API Key Fields & Filter Models based on Provider
            function toggleProviderFields() {
                const provider = providerSelect.value;

                // Show/Hide API Key Fields
                if (provider === 'groq') {
                    groqField.style.display = 'block';
                    geminiField.style.display = 'none';
                } else if (provider === 'gemini') {
                    groqField.style.display = 'none';
                    geminiField.style.display = 'block';
                }

                // Filter Models
                filterModels(provider);
            }

            function filterModels(provider) {
                const options = modelSelect.querySelectorAll('option');
                const allowedModels = modelsByProvider[provider] || [];

                options.forEach(option => {
                    if (allowedModels.includes(option.value)) {
                        option.style.display = '';
                    } else {
                        option.style.display = 'none';
                    }
                });

                // Also hide/show optgroups
                const optgroups = modelSelect.querySelectorAll('optgroup');
                optgroups.forEach(optgroup => {
                    const visibleOptions = Array.from(optgroup.querySelectorAll('option')).filter(opt => opt.style.display !== 'none');
                    optgroup.style.display = visibleOptions.length > 0 ? '' : 'none';
                });

                // Auto-select first visible option if current selection is hidden
                const currentOption = modelSelect.querySelector(`option[value="${modelSelect.value}"]`);
                if (!currentOption || currentOption.style.display === 'none') {
                    const firstVisible = Array.from(options).find(opt => opt.style.display !== 'none');
                    if (firstVisible) {
                        modelSelect.value = firstVisible.value;
                    }
                }
            }

            // Initialize on page load
            toggleProviderFields();

            // Listen to provider change
            providerSelect.addEventListener('change', toggleProviderFields);

            // Toggle API Key Visibility - Groq
            const toggleGroqApiKey = document.getElementById('toggleGroqApiKey');
            const groqApiKeyInput = document.getElementById('groq_api_key');

            toggleGroqApiKey.addEventListener('click', function() {
                const type = groqApiKeyInput.getAttribute('type') === 'password' ? 'text' : 'password';
                groqApiKeyInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });

            // Toggle API Key Visibility - Gemini
            const toggleGeminiApiKey = document.getElementById('toggleGeminiApiKey');
            const geminiApiKeyInput = document.getElementById('gemini_api_key');

            toggleGeminiApiKey.addEventListener('click', function() {
                const type = geminiApiKeyInput.getAttribute('type') === 'password' ? 'text' : 'password';
                geminiApiKeyInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });

            // Test Connection Logic
            const testBtn = document.getElementById('testConnectionBtn');
            const alertEl = document.getElementById('connectionAlert');
            const alertMsg = document.getElementById('connectionMessage');
            const alertTitle = document.getElementById('connectionTitle');
            const alertIcon = document.getElementById('connectionIcon');

            // Helper function to hide alert with smooth fade
            function hideAlertSmooth(element) {
                element.classList.add('fading-out');
                setTimeout(() => {
                    element.classList.add('d-none');
                    element.classList.remove('fading-out', 'alert-success', 'alert-danger', 'alert-warning');
                    // Force reflow to reset animation state
                    void element.offsetWidth;
                }, 350);
            }

            // Helper function to show alert with smooth fade-in
            function showAlertSmooth(element) {
                // Remove d-none first
                element.classList.remove('d-none', 'fading-out');

                // Force reflow to ensure transition triggers
                void element.offsetWidth;

                // Add show class to trigger fade-in
                element.classList.add('showing');
            }

            testBtn.addEventListener('click', function() {
                // Reset Alert FIRST (clear previous state and force immediate hide)
                alertEl.classList.add('d-none');
                alertEl.classList.remove('alert-success', 'alert-danger', 'alert-info', 'alert-warning', 'fading-out', 'showing');

                // Force reflow
                void alertEl.offsetWidth;

                const provider = providerSelect.value;
                const apiKey = provider === 'groq' ? groqApiKeyInput.value : geminiApiKeyInput.value;

                if (!apiKey) {
                    alertEl.classList.add('alert-warning');
                    alertTitle.textContent = "⚠️ Peringatan!";
                    alertMsg.textContent = `API Key untuk ${provider === 'groq' ? 'Groq Cloud' : 'Google Gemini'} belum diisi.`;
                    alertIcon.className = "fas fa-exclamation-triangle me-2 fs-4";

                    // Show alert with smooth animation
                    showAlertSmooth(alertEl);

                    // Auto-hide warning after 5 seconds
                    setTimeout(() => {
                        hideAlertSmooth(alertEl);
                    }, 5000);

                    return;
                }

                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Testing...';
                this.disabled = true;

                const data = {
                    api_key: apiKey,
                    model: modelSelect.value,
                    provider: provider,
                };

                // Use pathname only (relative) to avoid HTTP/HTTPS mixed-content error in production
                const testUrl = new URL('{{ route("admin.ai-settings.test") }}').pathname;

                // AbortController: cancel fetch after 30 seconds to prevent infinite hang
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000);

                fetch(testUrl, {
                    method: 'POST',
                    signal: controller.signal,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    clearTimeout(timeoutId);
                    // Handle non-JSON responses (e.g. HTML 500/419 error pages)
                    const contentType = response.headers.get('Content-Type') || '';
                    if (!contentType.includes('application/json')) {
                        throw new Error('Server error (HTTP ' + response.status + '). Pastikan APP_URL di .env sudah benar dan jalankan php artisan config:clear.');
                    }
                    return response.json();
                })
                .then(data => {
                    // Set content
                    alertMsg.textContent = data.message;

                    if (data.success) {
                        alertEl.classList.add('alert-success');
                        alertTitle.textContent = "✅ Berhasil!";
                        alertIcon.className = "fas fa-check-circle me-2 fs-4";

                        // Show alert with smooth animation
                        showAlertSmooth(alertEl);

                        // Auto-hide success alert after 5 seconds with smooth fade
                        setTimeout(() => {
                            hideAlertSmooth(alertEl);
                        }, 5000);
                    } else {
                        alertEl.classList.add('alert-danger');
                        alertTitle.textContent = "❌ Gagal!";
                        alertIcon.className = "fas fa-times-circle me-2 fs-4";

                        // Show alert with smooth animation
                        showAlertSmooth(alertEl);

                        // Auto-hide error alert after 6 seconds with smooth fade
                        setTimeout(() => {
                            hideAlertSmooth(alertEl);
                        }, 6000);
                    }
                })
                .catch(error => {
                    clearTimeout(timeoutId);
                    alertEl.classList.add('alert-danger');
                    alertTitle.textContent = "⚠️ Error Sistem";
                    let errMsg = error.message || 'Terjadi kesalahan tidak diketahui.';
                    if (error.name === 'AbortError') {
                        errMsg = 'Request timeout (>30 detik). Server terlalu lama merespons.';
                    }
                    alertMsg.textContent = errMsg;
                    alertIcon.className = "fas fa-exclamation-triangle me-2 fs-4";

                    // Show alert with smooth animation
                    showAlertSmooth(alertEl);

                    // Auto-hide error after 6 seconds with smooth fade
                    setTimeout(() => {
                        hideAlertSmooth(alertEl);
                    }, 6000);
                })
                .finally(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                });
            });
        });
    </script>

    <style>
        .timeline-item:last-child {
            border-left-color: transparent !important;
        }

        /* Smooth alert animation */
        #connectionAlert {
            transition: opacity 0.4s ease-in-out, transform 0.4s ease-in-out;
        }

        #connectionAlert.d-none {
            display: none !important;
            opacity: 0;
            transform: translateY(-15px);
        }

        /* Initial state before showing */
        #connectionAlert:not(.showing):not(.d-none) {
            opacity: 0;
            transform: translateY(-15px);
        }

        /* Showing state */
        #connectionAlert.showing {
            display: block !important;
            opacity: 1;
            transform: translateY(0);
        }

        /* Alert fade-in animation (smoother) */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Alert fade-out animation */
        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-15px);
            }
        }

        #connectionAlert.fading-out {
            animation: fadeOut 0.4s ease-in-out forwards;
        }
    </style>
@endsection
