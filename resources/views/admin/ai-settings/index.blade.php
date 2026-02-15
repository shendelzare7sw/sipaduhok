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

                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-globe me-2 text-primary"></i>AI Provider (GLOBAL)
                            </label>
                            <select class="form-select form-select-lg" name="ai_provider" id="ai_provider" style="border: 2px solid #3b82f6;">
                                <option value="groq" {{ $provider == 'groq' ? 'selected' : '' }}>
                                    ⚡ Groq Cloud (Llama / Qwen / Mixtral - FREE)
                                </option>
                                <option value="gemini" {{ $provider == 'gemini' ? 'selected' : '' }}>
                                    🤖 Google Gemini (2.5 Flash - FREE)
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
                                <optgroup label="Llama 3 (Groq)">
                                    <option value="llama-3.3-70b-versatile" {{ $model == 'llama-3.3-70b-versatile' ? 'selected' : '' }}>Llama 3.3 70B (Recommended)</option>
                                    <option value="llama-3.1-8b-instant" {{ $model == 'llama-3.1-8b-instant' ? 'selected' : '' }}>Llama 3.1 8B (Fastest)</option>
                                    <option value="llama-3.1-70b-versatile" {{ $model == 'llama-3.1-70b-versatile' ? 'selected' : '' }}>Llama 3.1 70B</option>
                                </optgroup>
                                <optgroup label="Qwen 2.5 (Groq - Recommended)">
                                    <option value="qwen-2.5-32b-instruct" {{ $model == 'qwen-2.5-32b-instruct' ? 'selected' : '' }}>Qwen 2.5 32B Instruct</option>
                                    <option value="qwen-2.5-coder-32b-instruct" {{ $model == 'qwen-2.5-coder-32b-instruct' ? 'selected' : '' }}>Qwen 2.5 Coder 32B</option>
                                </optgroup>
                                <optgroup label="Gemma (Groq)">
                                    <option value="gemma2-9b-it" {{ $model == 'gemma2-9b-it' ? 'selected' : '' }}>Gemma 2 9B</option>
                                </optgroup>
                                <optgroup label="Mixtral (Groq)">
                                    <option value="mixtral-8x7b-32768" {{ $model == 'mixtral-8x7b-32768' ? 'selected' : '' }}>Mixtral 8x7B</option>
                                </optgroup>
                                <optgroup label="Google Gemini (FREE Tier)">
                                    <option value="gemini-2.5-flash" {{ $model == 'gemini-2.5-flash' ? 'selected' : '' }}>Gemini 2.5 Flash (Only FREE model)</option>
                                </optgroup>
                            </select>
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

            // Model mapping by provider
            const modelsByProvider = {
                groq: [
                    'llama-3.3-70b-versatile',
                    'llama-3.1-8b-instant',
                    'llama-3.1-70b-versatile',
                    'qwen-2.5-32b-instruct',
                    'qwen-2.5-coder-32b-instruct',
                    'gemma2-9b-it',
                    'mixtral-8x7b-32768'
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

            testBtn.addEventListener('click', function() {
                // Reset Alert FIRST (clear previous state)
                alertEl.classList.add('d-none');
                alertEl.classList.remove('alert-success', 'alert-danger', 'alert-info', 'alert-warning');

                const provider = providerSelect.value;
                const apiKey = provider === 'groq' ? groqApiKeyInput.value : geminiApiKeyInput.value;

                if (!apiKey) {
                    alertEl.classList.remove('d-none');
                    alertEl.classList.add('alert-warning');
                    alertTitle.textContent = "Peringatan!";
                    alertMsg.textContent = `API Key untuk ${provider === 'groq' ? 'Groq Cloud' : 'Google Gemini'} belum diisi.`;
                    alertIcon.className = "fas fa-exclamation-triangle me-2 fs-4";
                    return;
                }

                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Testing...';
                this.disabled = true;

                const data = {
                    api_key: apiKey,
                    model: modelSelect.value,
                    provider: provider,
                    _token: '{{ csrf_token() }}'
                };

                fetch('{{ route("admin.ai-settings.test") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    alertEl.classList.remove('d-none');
                    alertMsg.textContent = data.message;

                    if (data.success) {
                        alertEl.classList.add('alert-success');
                        alertTitle.textContent = "Berhasil!";
                        alertIcon.className = "fas fa-check-circle me-2 fs-4";

                        // Auto-hide success alert after 10 seconds
                        setTimeout(() => {
                            alertEl.classList.add('d-none');
                        }, 10000);
                    } else {
                        alertEl.classList.add('alert-danger');
                        alertTitle.textContent = "Gagal!";
                        alertIcon.className = "fas fa-times-circle me-2 fs-4";

                        // Error tetap tampil, tidak auto-hide
                    }
                })
                .catch(error => {
                    alertEl.classList.remove('d-none');
                    alertEl.classList.add('alert-danger');
                    alertTitle.textContent = "Error Sistem";
                    alertMsg.textContent = 'Terjadi kesalahan: ' + error.message;
                    alertIcon.className = "fas fa-exclamation-triangle me-2 fs-4";
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
    </style>
@endsection
