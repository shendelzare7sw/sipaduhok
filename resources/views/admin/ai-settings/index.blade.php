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

                        <div class="mb-3">
                            <label class="form-label fw-bold">Provider AI</label>
                            <select class="form-select" name="ai_provider" id="ai_provider">
                                <option value="groq" {{ $provider == 'groq' ? 'selected' : '' }}>Groq Cloud (Llama 3 / Mixtral)</option>
                                <option value="gemini" {{ $provider == 'gemini' ? 'selected' : '' }}>Google Gemini (Flash / Pro)</option>
                                <option value="openai" {{ $provider == 'openai' ? 'selected' : '' }} disabled>OpenAI (GPT-3.5 / GPT-4) - Coming Soon</option>
                            </select>
                            <div class="form-text">Groq Cloud dan Google Gemini menawarkan Tier Gratis yang sangat generous.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">API Key</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input type="password" class="form-control" name="ai_api_key" id="ai_api_key" 
                                    value="{{ $apiKey }}" placeholder="gsk_... atau AIza..." required>
                                <button class="btn btn-outline-secondary" type="button" id="toggleApiKey">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                Dapatkan API Key gratis di <a href="https://console.groq.com/keys" target="_blank">Groq Console</a> atau <a href="https://aistudio.google.com/app/apikey" target="_blank">Google AI Studio</a>.
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
                                <optgroup label="Gemma (Groq)">
                                    <option value="gemma2-9b-it" {{ $model == 'gemma2-9b-it' ? 'selected' : '' }}>Gemma 2 9B</option>
                                </optgroup>
                                <optgroup label="Mixtral (Groq)">
                                    <option value="mixtral-8x7b-32768" {{ $model == 'mixtral-8x7b-32768' ? 'selected' : '' }}>Mixtral 8x7B</option>
                                </optgroup>
                                <optgroup label="Google Gemini">
                                    <option value="gemini-1.5-flash" {{ $model == 'gemini-1.5-flash' ? 'selected' : '' }}>Gemini 1.5 Flash (Fast & Free)</option>
                                    <option value="gemini-1.5-pro" {{ $model == 'gemini-1.5-pro' ? 'selected' : '' }}>Gemini 1.5 Pro (Smarter)</option>
                                </optgroup>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Model Vision (Multimodal)</label>
                            <select class="form-select" name="ai_vision_model" id="ai_vision_model">
                                <optgroup label="Llama 4 (Groq)">
                                    <option value="meta-llama/llama-4-scout-17b-16e-instruct" {{ $visionModel == 'meta-llama/llama-4-scout-17b-16e-instruct' ? 'selected' : '' }}>Llama 4 Scout (Multimodal)</option>
                                </optgroup>
                            </select>
                            <div class="form-text">Model ini digunakan khusus untuk menganalisis gambar pada tugas.</div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <button type="button" class="btn btn-outline-success" id="testConnectionBtn">
                                <i class="fas fa-plug me-2"></i> Test Koneksi
                            </button>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                <i class="fas fa-save me-2"></i> Simpan Pengaturan
                            </button>
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

    <!-- Toast for Test Result -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="testToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fas fa-robot rounded me-2" id="toastIcon"></i>
                <strong class="me-auto">System</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastMessage">
                Testing connection...
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle API Key Visibility
            const toggleApiKey = document.getElementById('toggleApiKey');
            const apiKeyInput = document.getElementById('ai_api_key');
            
            toggleApiKey.addEventListener('click', function() {
                const type = apiKeyInput.getAttribute('type') === 'password' ? 'text' : 'password';
                apiKeyInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });

            // Test Connection Logic
            const testBtn = document.getElementById('testConnectionBtn');
            const toastEl = document.getElementById('testToast');
            const toast = new bootstrap.Toast(toastEl);
            const toastMsg = document.getElementById('toastMessage');
            const toastIcon = document.getElementById('toastIcon');

            testBtn.addEventListener('click', function() {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Testing...';
                this.disabled = true;

                const data = {
                    api_key: document.getElementById('ai_api_key').value,
                    model: document.getElementById('ai_model').value,
                    provider: document.getElementById('ai_provider').value,
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
                    toastMsg.textContent = data.message;
                    if (data.success) {
                        toastIcon.classList.remove('text-danger');
                        toastIcon.classList.add('text-success');
                        toastEl.classList.add('bg-success', 'text-white', 'bg-opacity-10');
                    } else {
                        toastIcon.classList.remove('text-success');
                        toastIcon.classList.add('text-danger');
                        toastEl.classList.remove('bg-success', 'text-white', 'bg-opacity-10');
                    }
                    toast.show();
                })
                .catch(error => {
                    toastMsg.textContent = 'Error: ' + error.message;
                    toastIcon.classList.add('text-danger');
                    toast.show();
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
