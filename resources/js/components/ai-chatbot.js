/**
 * AI Chatbot General Assistant - Frontend Logic with Conversation History
 * SIPADUHOK - Claude/ChatGPT Style Interface
 */

const chatbotConfig = {
    userName: document.getElementById('aiChatbotWindow')?.dataset.userName || 'User',
    userRole: document.getElementById('aiChatbotWindow')?.dataset.userRole || 'guest',
};

// ==================== State Management ====================
const chatbotState = {
    isOpen: false,
    conversationHistory: [],
    isWaitingResponse: false,
    quickActionsLoaded: false,
    modelsLoaded: false,
    selectedModel: 'llama-3.3-70b-versatile',
    attachedFiles: [],
    availableModels: [],
    fabPosition: { right: 96 },
    isDragging: false,
    conversations: [],
    currentConversationId: null,
    isSidebarOpen: false,
};

const USER_ROLE = chatbotConfig.userRole;

// ==================== Initialize Chatbot ====================
const CHATBOT_DATA_VERSION = '3';

function initChatbot() {
    migrateLegacyChatbotData();
    loadConversationsFromStorage();
    restoreChatState();
    setupEventListeners();
    initDraggableFab();
    console.log('SIPADUHOK Chatbot initialized (LLM-only)');
}

function migrateLegacyChatbotData() {
    const currentVersion = localStorage.getItem('aiChatbotDataVersion');
    if (currentVersion === CHATBOT_DATA_VERSION) return;
    try {
        const raw = localStorage.getItem('aiChatbotConversations');
        if (raw) {
            const parsed = JSON.parse(raw);
            if (Array.isArray(parsed)) {
                const cleaned = parsed.map(conv => {
                    if (!conv || !Array.isArray(conv.messages)) return conv;
                    conv.messages = conv.messages.map(msg => {
                        if (!msg || typeof msg.content !== 'string') return msg;
                        if (msg.isHtml || /<\w+[\s>]/.test(msg.content)) {
                            const tmp = document.createElement('div');
                            tmp.innerHTML = msg.content;
                            return { role: msg.role, content: (tmp.textContent || '').trim(), structured: msg.structured || null };
                        }
                        return { role: msg.role, content: msg.content, structured: msg.structured || null };
                    });
                    return conv;
                });
                localStorage.setItem('aiChatbotConversations', JSON.stringify(cleaned));
            }
        }
    } catch (e) {
        console.warn('Chatbot legacy data migration failed:', e);
    }
    localStorage.setItem('aiChatbotDataVersion', CHATBOT_DATA_VERSION);
}

// ==================== Conversations Management ====================
function loadConversationsFromStorage() {
    const stored = localStorage.getItem('aiChatbotConversations');
    if (stored) {
        try {
            const parsed = JSON.parse(stored);
            if (Array.isArray(parsed)) {
                chatbotState.conversations = parsed.filter(conv =>
                    conv && typeof conv === 'object' && conv.id && conv.title !== undefined
                );
            } else {
                chatbotState.conversations = [];
                localStorage.removeItem('aiChatbotConversations');
            }
        } catch (e) {
            chatbotState.conversations = [];
            localStorage.removeItem('aiChatbotConversations');
        }
    }
}

function saveConversationsToStorage() {
    localStorage.setItem('aiChatbotConversations', JSON.stringify(chatbotState.conversations));
}

function createNewConversation() {
    if (chatbotState.currentConversationId) {
        saveCurrentConversation();
    }
    const newConv = {
        id: 'conv_' + Date.now(),
        title: 'New Chat',
        messages: [],
        created_at: new Date().toISOString(),
        updated_at: new Date().toISOString(),
    };
    chatbotState.conversations.unshift(newConv);
    chatbotState.currentConversationId = newConv.id;
    chatbotState.conversationHistory = [];
    saveConversationsToStorage();
    renderConversationsList();
    clearChatMessages();
    updateConversationTitle('New Chat');
    // Only close the sidebar. It may have been opened by the "+ New Chat" button.
    if (window.innerWidth <= 768 && chatbotState.isSidebarOpen) {
        toggleConversationsSidebar();
    }
}

function saveCurrentConversation() {
    if (!chatbotState.currentConversationId) return;
    const conv = chatbotState.conversations.find(c => c.id === chatbotState.currentConversationId);
    if (!conv) return;
    conv.messages = chatbotState.conversationHistory;
    conv.updated_at = new Date().toISOString();
    if (conv.title === 'New Chat' && conv.messages.length > 0) {
        const firstUserMsg = conv.messages.find(m => m.role === 'user');
        if (firstUserMsg) {
            conv.title = firstUserMsg.content.substring(0, 50) + (firstUserMsg.content.length > 50 ? '...' : '');
        }
    }
    saveConversationsToStorage();
    renderConversationsList();
}

function loadConversation(conversationId) {
    if (chatbotState.currentConversationId) {
        saveCurrentConversation();
    }
    const conv = chatbotState.conversations.find(c => c.id === conversationId);
    if (!conv) return;
    chatbotState.currentConversationId = conversationId;
    chatbotState.conversationHistory = [...conv.messages];
    clearChatMessages();
    conv.messages.forEach(msg => {
        addMessage(msg.role, msg.content, null, false, msg.isHtml || false, msg.structured || null);
    });
    updateConversationTitle(conv.title);
    renderConversationsList();
    scrollToBottom();
    // Only CLOSE the sidebar if it's open (user selected a conversation from sidebar)
    if (window.innerWidth <= 768 && chatbotState.isSidebarOpen) {
        toggleConversationsSidebar();
    }
}

function deleteConversation(conversationId, event) {
    event.stopPropagation();
    showConfirmDialog('Hapus conversation ini?', () => {
        chatbotState.conversations = chatbotState.conversations.filter(c => c.id !== conversationId);
        if (chatbotState.currentConversationId === conversationId) {
            chatbotState.currentConversationId = null;
            chatbotState.conversationHistory = [];
            clearChatMessages();
            updateConversationTitle('New Chat');
        }
        saveConversationsToStorage();
        renderConversationsList();
        showToastChatbot('success', 'Conversation dihapus');
    });
}

function renderConversationsList() {
    const container = document.getElementById('conversationsList');
    if (!container) return;
    if (chatbotState.conversations.length === 0) {
        container.innerHTML = '<p class="text-center text-muted conversations-empty">Belum ada conversation</p>';
        return;
    }
    const groups = { today: [], yesterday: [], thisWeek: [], older: [] };
    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);
    const weekAgo = new Date(today);
    weekAgo.setDate(weekAgo.getDate() - 7);
    chatbotState.conversations.forEach(conv => {
        const convDate = new Date(conv.updated_at);
        const convDay = new Date(convDate.getFullYear(), convDate.getMonth(), convDate.getDate());
        if (convDay >= today) groups.today.push(conv);
        else if (convDay >= yesterday) groups.yesterday.push(conv);
        else if (convDay >= weekAgo) groups.thisWeek.push(conv);
        else groups.older.push(conv);
    });
    let html = '';
    if (groups.today.length > 0) {
        html += '<div class="conversation-date-group">Today</div>';
        groups.today.forEach(conv => { html += renderConversationItem(conv); });
    }
    if (groups.yesterday.length > 0) {
        html += '<div class="conversation-date-group">Yesterday</div>';
        groups.yesterday.forEach(conv => { html += renderConversationItem(conv); });
    }
    if (groups.thisWeek.length > 0) {
        html += '<div class="conversation-date-group">Last 7 Days</div>';
        groups.thisWeek.forEach(conv => { html += renderConversationItem(conv); });
    }
    if (groups.older.length > 0) {
        html += '<div class="conversation-date-group">Older</div>';
        groups.older.forEach(conv => { html += renderConversationItem(conv); });
    }
    container.innerHTML = html;
}

function renderConversationItem(conv) {
    if (!conv || !conv.id) return '';
    const isActive = conv.id === chatbotState.currentConversationId;
    const date = new Date(conv.updated_at || new Date());
    const timeStr = date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    const title = conv.title || 'New Chat';
    return `
        <div class="conversation-item ${isActive ? 'active' : ''}" data-conversation-id="${escapeHtml(conv.id)}">
            <div class="conversation-title">${escapeHtml(title)}</div>
            <div class="conversation-date">${timeStr}</div>
            <button class="conversation-delete" data-delete-conversation-id="${escapeHtml(conv.id)}" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
}

function updateConversationTitle(title) {
    const titleEl = document.getElementById('currentConversationTitle');
    if (titleEl) titleEl.textContent = title;
}

function clearChatMessages() {
    const container = document.getElementById('chatMessages');
    if (!container) return;
    const messages = container.querySelectorAll('.message-group');
    messages.forEach(msg => msg.remove());
    const welcomeHtml = `
        <div class="message-group ai-message">
            <div class="message-avatar"><i class="fas fa-headset ai-chatbot-icon-sm"></i></div>
            <div class="message-content">
                <div class="message-bubble">
                    <i class="far fa-hand-paper ai-chatbot-wave-icon"></i> Halo <strong>${escapeHtml(chatbotConfig.userName)}</strong>! Saya <strong>Asisten SIPADUHOK</strong>.<br>
                    Butuh Bantuan? Silahkan Tanyakan
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('afterbegin', welcomeHtml);
    const quickActionsContainer = document.getElementById('quickActionsContainer');
    if (quickActionsContainer && !chatbotState.quickActionsLoaded) {
        loadQuickActions();
    }
}

function toggleConversationsSidebar() {
    const sidebar = document.getElementById('conversationsSidebar');
    const chatWindow = document.getElementById('aiChatbotWindow');
    if (!sidebar || !chatWindow) return;
    sidebar.classList.toggle('active');
    chatbotState.isSidebarOpen = sidebar.classList.contains('active');
    if (chatbotState.isSidebarOpen) {
        chatWindow.classList.add('sidebar-open');
    } else {
        chatWindow.classList.remove('sidebar-open');
    }
    if (chatbotState.isSidebarOpen && chatbotState.conversations.length > 0) {
        renderConversationsList();
    }
}

// ==================== Load Available Models ====================
async function loadAvailableModels() {
    try {
        const response = await fetch('/ai-chatbot/models', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });
        const data = await response.json();
        if (response.ok && data.success && data.models && data.models.length > 0) {
            chatbotState.availableModels = data.models;
            populateModelSelector(data.models);
        } else {
            throw new Error(data.error || 'No models available');
        }
    } catch (error) {
        console.error('[AI Chatbot] Error loading models:', error);
        const defaultModels = [
            { id: 'qwen/qwen3-32b', name: 'Qwen 3 32B (High Rate Limit)', provider: 'groq', supports_vision: false, supports_pdf: false, default: false },
            { id: 'llama-3.3-70b-versatile', name: 'Llama 3.3 70B (Recommended)', provider: 'groq', supports_vision: false, supports_pdf: false, default: true },
            { id: 'openai/gpt-oss-120b', name: 'GPT OSS 120B', provider: 'groq', supports_vision: false, supports_pdf: false, default: false },
            { id: 'meta-llama/llama-4-scout-17b-16e-instruct', name: 'Llama 4 Scout (Vision)', provider: 'groq', supports_vision: true, supports_pdf: false, default: false },
            { id: 'gemini-2.5-flash', name: 'Gemini 2.5 Flash (PDF + Vision)', provider: 'gemini', supports_vision: true, supports_pdf: true, default: false }
        ];
        chatbotState.availableModels = defaultModels;
        populateModelSelector(defaultModels);
        showChatError('Menggunakan model default. Jika mengalami masalah, hubungi admin.');
    }
}

// ==================== Populate Model Selector ====================
function populateModelSelector(models) {
    const selector = document.getElementById('modelSelector');
    if (!selector) return;
    
    // Prioritize backend default, fallback to Llama
    const backendDefault = models.find(m => m.default === true);
    const fallbackDefault = models.find(m => m.id === 'llama-3.3-70b-versatile');
    const forcedDefaultId = backendDefault ? backendDefault.id : (fallbackDefault ? fallbackDefault.id : models[0]?.id);
    
    selector.innerHTML = models.map(m => {
        let icons = '';
        if (m.supports_vision) icons += ' [Vision]';
        if (m.supports_pdf) icons += ' [PDF]';
        const isDefault = m.id === forcedDefaultId;
        return `<option value="${m.id}" ${isDefault ? 'selected' : ''}>${m.name} ${icons}</option>`;
    }).join('');
    chatbotState.selectedModel = forcedDefaultId;
    const savedModel = localStorage.getItem('selectedChatModel');
    if (savedModel && models.some(m => m.id === savedModel)) {
        const savedModelInfo = models.find(m => m.id === savedModel);
        if (savedModelInfo && savedModelInfo.provider === 'groq') {
            chatbotState.selectedModel = savedModel;
            selector.value = savedModel;
        } else {
            localStorage.removeItem('selectedChatModel');
        }
    }
}

// ==================== Open/Close Chat Window ====================
async function openChatWindow() {
    const chatWindow = document.getElementById('aiChatbotWindow');
    const fab = document.getElementById('aiChatbotFab');
    if (!chatWindow || !fab) return;
    chatWindow.classList.add('active');
    fab.classList.add('hidden');
    chatbotState.isOpen = true;
    if (!chatbotState.modelsLoaded) {
        await loadAvailableModels();
        chatbotState.modelsLoaded = true;
    }
    if (!chatbotState.quickActionsLoaded) {
        loadQuickActions();
    }
    if (!chatbotState.currentConversationId) {
        if (chatbotState.conversations.length === 0) {
            // No conversations at all, create a fresh one.
            createNewConversation();
        } else {
            // Resume the most recent conversation silently (no sidebar toggle)
            const mostRecent = chatbotState.conversations[0];
            loadConversation(mostRecent.id);
        }
    }
    localStorage.setItem('aiChatbotOpen', 'true');
    scrollToBottom();
}

function closeChatWindow() {
    const chatWindow = document.getElementById('aiChatbotWindow');
    const fab = document.getElementById('aiChatbotFab');
    if (!chatWindow || !fab) return;
    if (chatbotState.currentConversationId) {
        saveCurrentConversation();
    }
    chatWindow.classList.remove('active');
    fab.classList.remove('hidden');
    fab.classList.remove('fab-hidden-mobile');
    chatbotState.isOpen = false;
    localStorage.setItem('aiChatbotOpen', 'false');
}

// ==================== Load Quick Actions ====================
function loadQuickActions() {
    const container = document.getElementById('quickActionsContainer');
    if (!container) return;
    // Role-specific quick actions with FA icons
    const roleActions = {
        admin: [
            {i:'fa-coins',t:'Kelola tagihan'},{i:'fa-credit-card',t:'Validasi pembayaran'},{i:'fa-calendar-week',t:'Jadwal pelajaran'},
            {i:'fa-user-graduate',t:'Kelola siswa'},{i:'fa-chalkboard-teacher',t:'Data guru'},{i:'fa-school',t:'Data kelas'},
            {i:'fa-file-alt',t:'Cetak rapor'},{i:'fa-unlock-alt',t:'Validasi akses'},{i:'fa-chart-bar',t:'Laporan keuangan'},
            {i:'fa-calendar-alt',t:'Tahun ajaran'},{i:'fa-bullhorn',t:'Pengumuman'},{i:'fa-key',t:'Lupa password'},
        ],
        bendahara: [
            {i:'fa-home',t:'Dashboard bendahara'},{i:'fa-file-invoice-dollar',t:'Kelola tagihan'},{i:'fa-money-bill-wave',t:'Kelola pembayaran'},
            {i:'fa-cog',t:'Config pembayaran'},{i:'fa-check-circle',t:'Validasi ujian'},{i:'fa-hand-holding-usd',t:'Validasi dispensasi'},
            {i:'fa-chart-bar',t:'Laporan pembayaran'},{i:'fa-file-alt',t:'Rekap tagihan'},{i:'fa-exclamation-triangle',t:'Siswa belum lunas'},
            {i:'fa-key',t:'Lupa password'},
        ],
        ketua_pkbm: [
            {i:'fa-home',t:'Dashboard ketua'},{i:'fa-check-double',t:'Approval dispensasi'},{i:'fa-certificate',t:'Validasi rapor'},
            {i:'fa-users',t:'Monitoring pengguna'},{i:'fa-file-pdf',t:'Laporan'},{i:'fa-sticky-note',t:'Kirim catatan'},
            {i:'fa-key',t:'Lupa password'},
        ],
        sekretaris: [
            {i:'fa-home',t:'Dashboard sekretaris'},{i:'fa-calendar-alt',t:'Kalender akademik'},{i:'fa-bullhorn',t:'Pengumuman'},
            {i:'fa-image',t:'Flyer iklan'},{i:'fa-newspaper',t:'Kelola berita'},{i:'fa-file-excel',t:'Import excel'},
            {i:'fa-key',t:'Lupa password'},
        ],
        wakil_kepala_sekolah: [
            {i:'fa-home',t:'Dashboard waka'},{i:'fa-school',t:'Manajemen akademik'},{i:'fa-chalkboard-teacher',t:'Plotting guru'},
            {i:'fa-users',t:'Manajemen siswa'},{i:'fa-calendar-week',t:'Jadwal pelajaran'},{i:'fa-chart-line',t:'Pengaturan kkm'},
            {i:'fa-file-alt',t:'Rekap kenaikan'},{i:'fa-sticky-note',t:'Kirim catatan'},{i:'fa-key',t:'Lupa password'},
        ],
        wali_kelas: [
            {i:'fa-home',t:'Dashboard wali'},{i:'fa-exchange-alt',t:'Ganti kelas'},{i:'fa-clipboard-check',t:'Input presensi'},
            {i:'fa-check-circle',t:'Validasi izin'},{i:'fa-history',t:'Riwayat presensi'},{i:'fa-chart-line',t:'Nilai kelas'},
            {i:'fa-file-alt',t:'Kelola rapor'},{i:'fa-download',t:'Request rapor'},{i:'fa-chart-bar',t:'Prediksi naik kelas'},
            {i:'fa-unlock-alt',t:'Validasi akses'},{i:'fa-school',t:'Jadwal kelas'},{i:'fa-key',t:'Lupa password'},
        ],
        guru_pengajar: [
            {i:'fa-home',t:'Dashboard guru'},{i:'fa-calendar-alt',t:'Jadwal mengajar'},{i:'fa-list',t:'Semua kelas'},
            {i:'fa-book',t:'Kelola materi'},{i:'fa-tasks',t:'Kelola tugas'},{i:'fa-pencil-ruler',t:'Buat latihan'},
            {i:'fa-file-alt',t:'Buat ujian'},{i:'fa-check-circle',t:'Koreksi tugas'},{i:'fa-chart-line',t:'Nilai guru'},
            {i:'fa-comments',t:'Forum guru'},{i:'fa-video',t:'Meeting guru'},{i:'fa-key',t:'Lupa password'},
        ],
        siswa: [
            {i:'fa-home',t:'Dashboard SIA'},{i:'fa-graduation-cap',t:'Masuk LMS'},{i:'fa-calendar-check',t:'Presensi saya'},
            {i:'fa-chart-line',t:'Data penilaian'},{i:'fa-edit',t:'Tugas saya'},{i:'fa-clipboard-list',t:'Ujian saya'},
            {i:'fa-book',t:'Materi pelajaran'},{i:'fa-calendar-week',t:'Jadwal saya'},{i:'fa-comments',t:'Forum diskusi'},
            {i:'fa-file-alt',t:'Rapor saya'},{i:'fa-credit-card',t:'Pembayaran saya'},{i:'fa-key',t:'Lupa password'},
        ],
        orang_tua: [
            {i:'fa-home',t:'Dashboard orang tua'},{i:'fa-calendar-check',t:'Presensi anak'},{i:'fa-edit',t:'Ajukan izin anak'},
            {i:'fa-history',t:'Riwayat izin'},{i:'fa-credit-card',t:'Tagihan anak'},{i:'fa-money-bill-wave',t:'Bayar spp'},
            {i:'fa-receipt',t:'Cetak invoice'},{i:'fa-file-alt',t:'Rapor anak'},{i:'fa-chart-bar',t:'Nilai anak'},
            {i:'fa-graduation-cap',t:'LMS anak'},{i:'fa-key',t:'Lupa password'},{i:'fa-bell',t:'Notifikasi'},
        ],
    };
    const actions = roleActions[USER_ROLE] || [
        {i:'fa-book-open',t:'Akses LMS'},{i:'fa-file-alt',t:'Lihat rapor'},{i:'fa-key',t:'Lupa password'},{i:'fa-bell',t:'Notifikasi'},
    ];
    container.innerHTML = actions.map(a =>
        `<button class="quick-action-btn" data-quick-action="${escapeHtml(a.t)}"><i class="fas ${escapeHtml(a.i)} quick-action-icon"></i>${escapeHtml(a.t)}</button>`
    ).join('');
    chatbotState.quickActionsLoaded = true;
}

function renderQuickActions(actions) {
    const container = document.getElementById('quickActionsContainer');
    if (!container || !actions || actions.length === 0) return;
    container.innerHTML = actions.map(action =>
        `<button class="quick-action-btn" data-quick-action="${escapeHtml(action)}">${escapeHtml(action)}</button>`
    ).join('');
}

function sendQuickAction(message) {
    const input = document.getElementById('chatInput');
    if (input) { input.value = message; sendMessage(); }
}

// ==================== Handle File Attachments ====================
function handleFileAttachment(event) {
    const files = Array.from(event.target.files);
    if (files.length === 0) return;
    if (chatbotState.attachedFiles.length + files.length > 5) {
        showToastChatbot('warning', 'Maksimal 5 file');
        event.target.value = '';
        return;
    }
    files.forEach(file => {
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'application/pdf'];
        if (!validTypes.includes(file.type)) {
            showToastChatbot('error', `${file.name}: Format tidak didukung`);
            return;
        }
        if (file.size > 4 * 1024 * 1024) {
            showToastChatbot('error', `${file.name}: File terlalu besar (max 4MB)`);
            return;
        }
        const fileObj = { file, preview: file.name, dataUrl: null };
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => { fileObj.dataUrl = e.target.result; renderAttachmentsPreview(); };
            reader.readAsDataURL(file);
        }
        chatbotState.attachedFiles.push(fileObj);
    });
    const hasPdf = chatbotState.attachedFiles.some(f => f.file.type === 'application/pdf');
    const hasImage = chatbotState.attachedFiles.some(f => f.file.type.startsWith('image/'));
    if (hasPdf || hasImage) {
        const selectedModel = chatbotState.availableModels.find(m => m.id === chatbotState.selectedModel);
        if (hasPdf) {
            const geminiModel = chatbotState.availableModels.find(m => m.id === 'gemini-2.5-flash');
            if (geminiModel && chatbotState.selectedModel !== 'gemini-2.5-flash') {
                chatbotState.selectedModel = geminiModel.id;
                const selector = document.getElementById('modelSelector');
                if (selector) selector.value = geminiModel.id;
                showToastChatbot('info', 'PDF hanya support Gemini 2.5 Flash. Model beralih otomatis.');
            } else if (!geminiModel) {
                showToastChatbot('error', 'PDF memerlukan Gemini 2.5 Flash. Hubungi admin untuk konfigurasi API key.');
            }
        } else if (hasImage && selectedModel && !selectedModel.supports_vision) {
            const visionModel = chatbotState.availableModels.find(m => m.supports_vision);
            if (visionModel) {
                chatbotState.selectedModel = visionModel.id;
                const selector = document.getElementById('modelSelector');
                if (selector) selector.value = visionModel.id;
                showToastChatbot('info', `Berhasil beralih ke ${visionModel.name}`);
            } else {
                showToastChatbot('error', 'Tidak ada model vision tersedia');
            }
        }
    }
    renderAttachmentsPreview();
    event.target.value = '';
}

function renderAttachmentsPreview() {
    const container = document.getElementById('attachmentsPreview');
    if (!container) return;
    if (chatbotState.attachedFiles.length === 0) {
        container.classList.add('d-none');
        return;
    }
    container.classList.remove('d-none');
    const html = chatbotState.attachedFiles.map((fileObj, index) => {
        const isImage = fileObj.file.type.startsWith('image/');
        if (isImage && fileObj.dataUrl) {
            return `
                <div class="attachment-preview-item">
                    <img src="${fileObj.dataUrl}" alt="${escapeHtml(fileObj.file.name)}" data-lightbox-src="${fileObj.dataUrl}">
                    <button class="attachment-remove-btn" data-remove-attachment-index="${index}"><i class="fas fa-times"></i></button>
                </div>`;
        } else {
            return `
                <div class="attachment-preview-item file-preview">
                    <i class="fas fa-file-pdf"></i>
                    <button class="attachment-remove-btn" data-remove-attachment-index="${index}"><i class="fas fa-times"></i></button>
                </div>`;
        }
    }).join('');
    container.innerHTML = html;
}

function removeAttachmentByIndex(index) {
    chatbotState.attachedFiles.splice(index, 1);
    renderAttachmentsPreview();
}

function openLightbox(dataUrl) {
    const lightbox = document.getElementById('imageLightbox');
    const lightboxImg = document.getElementById('lightboxImage');
    if (lightbox && lightboxImg) { lightboxImg.src = dataUrl; lightbox.classList.remove('d-none'); }
}

function closeLightbox() {
    const lightbox = document.getElementById('imageLightbox');
    if (lightbox) lightbox.classList.add('d-none');
}

// ==================== Send Message ====================
async function sendMessage(messageText = null) {
    const input = document.getElementById('chatInput');
    if (!input) return;
    const message = messageText || input.value.trim();
    if (!message && chatbotState.attachedFiles.length === 0) {
        showToastChatbot('warning', 'Ketik pesan atau lampirkan file');
        return;
    }
    if (chatbotState.isWaitingResponse) return;
    if (!chatbotState.currentConversationId) {
        createNewConversation();
    }
    addMessage('user', message, chatbotState.attachedFiles);
    input.value = '';
    const currentFiles = [...chatbotState.attachedFiles];
    chatbotState.attachedFiles = [];
    renderAttachmentsPreview();
    updateCharCount(0);

    // Always send to LLM API. The rule-based engine has been removed.
    showTypingIndicator();
    chatbotState.isWaitingResponse = true;
    const response = await sendMessageToApi(message, currentFiles);
    chatbotState.isWaitingResponse = false;
    hideTypingIndicator();
    if (response.success) {
        const text = response.structured?.text ?? response.response;
        addMessage('assistant', text, null, true, false, response.structured ?? null);
    } else {
        const errorMsg = response.error || 'Terjadi kesalahan tidak diketahui.';
        let errStructured;
        if (errorMsg.includes('tidak support PDF') || errorMsg.includes('belum dikonfigurasi')) {
            errStructured = {
                text: 'Fitur Tidak Tersedia\n' + errorMsg,
                callout: null,
                button: null,
                related: null,
            };
        } else if (errorMsg.includes('503') || errorMsg.includes('UNAVAILABLE') || errorMsg.includes('high demand') || errorMsg.includes('overloaded')) {
            errStructured = {
                text: 'Layanan sedang tidak tersedia\nServer sedang mengalami gangguan. Silakan coba lagi dalam beberapa saat.',
                callout: 'Server sedang kelebihan beban. Coba lagi dalam 1-2 menit atau ganti model AI di pengaturan.',
                button: null,
                related: null,
            };
        } else {
            errStructured = {
                text: 'Layanan sedang tidak tersedia\nServer sedang mengalami gangguan. Silakan coba lagi dalam beberapa saat.',
                callout: null,
                button: null,
                related: null,
            };
        }
        addMessage('assistant', errStructured.text, null, true, false, errStructured);
    }
    saveCurrentConversation();
    scrollToBottom();
}

// ==================== Send Message to API ====================
async function sendMessageToApi(message, attachedFiles) {
    const formData = new FormData();
    formData.append('message', message);
    formData.append('model', chatbotState.selectedModel);
    let historyForRequest = chatbotState.conversationHistory;
    const lastMessage = historyForRequest[historyForRequest.length - 1];
    if (lastMessage && lastMessage.role === 'user' && lastMessage.content === message) {
        historyForRequest = historyForRequest.slice(0, -1);
    }

    const cleanHistory = historyForRequest
        .slice(-10)
        .map(msg => ({ role: msg.role, content: msg.content }));
    formData.append('history', JSON.stringify(cleanHistory));
    if (attachedFiles && attachedFiles.length > 0) {
        attachedFiles.forEach((fileObj, index) => {
            formData.append(`attachment_${index}`, fileObj.file);
        });
        formData.append('attachment_count', attachedFiles.length);
    }
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        const response = await fetch('/ai-chatbot/send-message', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData,
        });
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error || 'Request failed');
        }
        const data = await response.json();
        if (data.success) {
            if (chatbotState.conversationHistory.length > 20) {
                chatbotState.conversationHistory = chatbotState.conversationHistory.slice(-20);
            }
        } else if (data.switch_to_gemini) {
            const geminiModel = chatbotState.availableModels.find(m => m.id === 'gemini-2.5-flash');
            if (geminiModel) {
                chatbotState.selectedModel = geminiModel.id;
                const selector = document.getElementById('modelSelector');
                if (selector) selector.value = geminiModel.id;
                showToastChatbot('info', 'Beralih ke Gemini untuk membaca PDF. Mengirim ulang...');
                return await sendMessageToApi(message, attachedFiles);
            }
        }
        return data;
    } catch (error) {
        console.error('API Error:', error);
        return { success: false, error: error.message || 'Koneksi gagal. Coba lagi.' };
    }
}

// ==================== Render Structured Response Template ====================
function renderStructuredResponse(structured) {
    if (!structured || !structured.text) return '';
    let html = '';
    const textHtml = escapeHtml(structured.text).replace(/\n/g, '<br>');
    html += `<div class="chatbot-response-text">${textHtml}</div>`;
    if (structured.callout) {
        html += `<div class="chatbot-callout"><i class="fas fa-info-circle"></i><span>${escapeHtml(structured.callout)}</span></div>`;
    }
    if (structured.button && structured.button.url && structured.button.label) {
        const safeUrl = encodeURI(structured.button.url);
        html += `<a href="${safeUrl}" class="chatbot-cta-btn"><span>${escapeHtml(structured.button.label)}</span><i class="fas fa-arrow-right"></i></a>`;
    }
    if (Array.isArray(structured.related) && structured.related.length > 0) {
        html += '<div class="chatbot-related-chips"><span class="chatbot-related-label"><i class="fas fa-link"></i> Terkait:</span>';
        for (const chip of structured.related) {
            if (!chip || !chip.url || !chip.label) continue;
            const safeUrl = encodeURI(chip.url);
            html += `<a href="${safeUrl}" class="chatbot-chip">${escapeHtml(chip.label)}</a>`;
        }
        html += '</div>';
    }
    return html;
}

// ==================== Add Message to UI ====================
function addMessage(role, content, attachments = null, saveToHistory = true, isHtml = false, structured = null) {
    const messagesContainer = document.getElementById('chatMessages');
    if (!messagesContainer) return;
    const isUser = role === 'user';
    const now = new Date();
    const time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    let attachmentsHtml = '';
    if (attachments && attachments.length > 0) {
        const items = attachments.map(fileObj => {
            const isImage = fileObj.file.type.startsWith('image/');
            const icon = isImage ? 'fa-image' : 'fa-file-pdf';
            return `<div class="message-attachment-item"><i class="fas ${icon}"></i> ${fileObj.file.name}</div>`;
        }).join('');
        attachmentsHtml = `<div class="message-attachments">${items}</div>`;
    }
    let bubbleContent;
    if (structured) {
        bubbleContent = renderStructuredResponse(structured);
    } else if (isHtml) {
        bubbleContent = content;
    } else {
        bubbleContent = escapeHtml(content).replace(/\n/g, '<br>');
    }
    const avatarIcon = !isUser ? '<div class="message-avatar"><i class="fas fa-headset ai-chatbot-icon-sm"></i></div>' : '';
    const messageHtml = `
        <div class="message-group ${isUser ? 'user-message' : 'ai-message'}">
            ${avatarIcon}
            <div class="message-content">
                ${attachmentsHtml}
                <div class="message-bubble">${bubbleContent}</div>
                <div class="message-meta">
                    <span class="message-time">${time}</span>
                    ${!isUser ? '<button class="btn-copy" data-copy-message title="Copy"><i class="fas fa-copy"></i></button>' : ''}
                </div>
            </div>
            ${isUser ? '<div class="message-avatar"><i class="fas fa-user"></i></div>' : ''}
        </div>
    `;
    messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
    if (saveToHistory) {
        chatbotState.conversationHistory.push({ role, content, isHtml, structured });
    }
    scrollToBottom();
}

// ==================== Show/Hide Typing Indicator ====================
let typingTimerInterval = null;
let typingStartTime = null;

function showTypingIndicator() {
    const indicator = document.getElementById('typingIndicator');
    const timerDisplay = document.getElementById('typingTimer');
    const chatBody = document.getElementById('chatMessages');
    
    if (indicator) {
        // Move to the very bottom of the chat container
        if (chatBody) {
            chatBody.appendChild(indicator);
        }
        
        indicator.classList.remove('d-none');
        
        if (timerDisplay) {
            timerDisplay.textContent = '0.00 s';
            typingStartTime = Date.now();
            
            if (typingTimerInterval) clearInterval(typingTimerInterval);
            
            typingTimerInterval = setInterval(() => {
                const elapsed = (Date.now() - typingStartTime) / 1000;
                timerDisplay.textContent = elapsed.toFixed(2) + ' s';
            }, 50); // Update frequently for smooth visual
        }
        
        scrollToBottom();
    }
}

function hideTypingIndicator() {
    const indicator = document.getElementById('typingIndicator');
    if (indicator) indicator.classList.add('d-none');
    
    if (typingTimerInterval) {
        clearInterval(typingTimerInterval);
        typingTimerInterval = null;
    }
}

// ==================== Copy Message ====================
function copyMessage(button) {
    const messageBubble = button.closest('.message-content').querySelector('.message-bubble');
    if (!messageBubble) return;
    
    // Use innerText to preserve newlines and formatting
    const textToCopy = messageBubble.innerText;
    
    // Modern approach (Requires HTTPS or localhost)
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(textToCopy).then(() => {
            showToastChatbot('success', 'Pesan disalin');
        }).catch(() => {
            showToastChatbot('error', 'Gagal menyalin pesan');
        });
    } else {
        // Fallback for insecure contexts (e.g., HTTP on Laragon)
        const textArea = document.createElement("textarea");
        textArea.value = textToCopy;
        
        // Make the textarea invisible
        textArea.style.position = "absolute";
        textArea.style.left = "-999999px";
        
        document.body.prepend(textArea);
        textArea.select();
        
        try {
            document.execCommand('copy');
            showToastChatbot('success', 'Pesan disalin');
        } catch (error) {
            console.error(error);
            showToastChatbot('error', 'Gagal menyalin pesan');
        } finally {
            textArea.remove();
        }
    }
}

// ==================== Auto-Resize Textarea ====================
function autoResizeTextarea(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
}

function updateCharCount(length) {
    const charCountEl = document.getElementById('charCount');
    if (charCountEl) charCountEl.textContent = length;
}

// ==================== Scroll to Bottom ====================
function scrollToBottom() {
    const chatBody = document.getElementById('chatMessages');
    if (chatBody) {
        setTimeout(() => { chatBody.scrollTop = chatBody.scrollHeight; }, 100);
    }
}

// ==================== Restore Chat State ====================
function restoreChatState() {
    // NOTE: Chatbot always starts CLOSED on page navigation.
    // Only restore model preference, not open/close state.
    const savedModel = localStorage.getItem('selectedChatModel');
    if (savedModel) chatbotState.selectedModel = savedModel;
}

// ==================== Setup Event Listeners ====================
function setupEventListeners() {
    const modelSelector = document.getElementById('modelSelector');
    if (modelSelector) {
        modelSelector.addEventListener('change', (e) => {
            chatbotState.selectedModel = e.target.value;
            const selectedModelInfo = chatbotState.availableModels.find(m => m.id === e.target.value);
            if (selectedModelInfo && selectedModelInfo.provider === 'groq') {
                localStorage.setItem('selectedChatModel', e.target.value);
            } else if (selectedModelInfo && selectedModelInfo.provider === 'gemini') {
                // Don't save Gemini as default preference
            }
        });
    }
    const fileInput = document.getElementById('fileAttachment');
    if (fileInput) fileInput.addEventListener('change', handleFileAttachment);
    const chatInput = document.getElementById('chatInput');
    if (chatInput) {
        chatInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
        });
        chatInput.addEventListener('input', (e) => {
            autoResizeTextarea(e.target);
            updateCharCount(e.target.value.length);
        });
    }
    const sendButton = document.getElementById('sendButton');
    if (sendButton) sendButton.addEventListener('click', () => sendMessage());

    document.addEventListener('click', (event) => {
        const actionButton = event.target.closest('[data-chatbot-action]');
        if (!actionButton) return;

        const action = actionButton.dataset.chatbotAction;
        if (action === 'toggle-sidebar') {
            toggleConversationsSidebar();
        } else if (action === 'new-conversation') {
            createNewConversation();
        } else if (action === 'clear-conversations') {
            clearAllConversations();
        } else if (action === 'close') {
            closeChatWindow();
        } else if (action === 'close-lightbox') {
            closeLightbox();
        } else if (action === 'confirm') {
            confirmAction();
        } else if (action === 'cancel-confirm') {
            cancelConfirm();
        }
    });

    const conversationsList = document.getElementById('conversationsList');
    if (conversationsList) {
        conversationsList.addEventListener('click', (event) => {
            const deleteButton = event.target.closest('[data-delete-conversation-id]');
            if (deleteButton) {
                deleteConversation(deleteButton.dataset.deleteConversationId, event);
                return;
            }

            const conversationItem = event.target.closest('[data-conversation-id]');
            if (conversationItem) {
                loadConversation(conversationItem.dataset.conversationId);
            }
        });
    }

    const quickActionsContainer = document.getElementById('quickActionsContainer');
    if (quickActionsContainer) {
        quickActionsContainer.addEventListener('click', (event) => {
            const button = event.target.closest('[data-quick-action]');
            if (button) sendQuickAction(button.dataset.quickAction);
        });
    }

    const attachmentsPreview = document.getElementById('attachmentsPreview');
    if (attachmentsPreview) {
        attachmentsPreview.addEventListener('click', (event) => {
            const removeButton = event.target.closest('[data-remove-attachment-index]');
            if (removeButton) {
                removeAttachmentByIndex(Number(removeButton.dataset.removeAttachmentIndex));
                return;
            }

            const preview = event.target.closest('[data-lightbox-src]');
            if (preview) openLightbox(preview.dataset.lightboxSrc);
        });
    }

    const chatMessages = document.getElementById('chatMessages');
    if (chatMessages) {
        chatMessages.addEventListener('click', (event) => {
            const copyButton = event.target.closest('[data-copy-message]');
            if (copyButton) copyMessage(copyButton);
        });
    }
}

// ==================== Initialize Draggable FAB ====================
function initDraggableFab() {
    const fab = document.getElementById('aiChatbotFab');
    if (!fab) return;

    chatbotState.fabPosition = { right: 96 };

    let isDraggingFab = false;
    let hasMoved = false;
    let startX = 0;
    let startY = 0;
    let startRight = 0;

    function isMobileView() {
        return window.innerWidth <= 768;
    }

    // Click: fallback for desktop mouse clicks.
    fab.addEventListener('click', function() {
        if (hasMoved) { hasMoved = false; return; }
        openChatWindow();
    });

    // Mouse drag, desktop only.
    fab.addEventListener('mousedown', function(e) {
        if (isMobileView()) return;
        isDraggingFab = true;
        hasMoved = false;
        startX = e.clientX;
        startRight = chatbotState.fabPosition.right;
        fab.style.cursor = 'grabbing';
        fab.style.transition = 'none';
        e.preventDefault();
    });

    document.addEventListener('mousemove', function(e) {
        if (!isDraggingFab) return;
        const deltaX = startX - e.clientX;
        const newRight = startRight + deltaX;
        const boundedRight = Math.max(24, Math.min(newRight, window.innerWidth - fab.offsetWidth - 24));
        chatbotState.fabPosition.right = boundedRight;
        fab.style.right = `${boundedRight}px`;
        if (Math.abs(deltaX) > 5) hasMoved = true;
    });

    document.addEventListener('mouseup', function() {
        if (!isDraggingFab) return;
        isDraggingFab = false;
        fab.style.cursor = '';
        fab.style.transition = '';
    });

    // Touch: always record start position.
    fab.addEventListener('touchstart', function(e) {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
        if (isMobileView()) {
            // Mobile: touchend on FAB handles tap explicitly.
            return;
        }
        // Desktop touch: set up drag.
        isDraggingFab = true;
        hasMoved = false;
        startRight = chatbotState.fabPosition.right;
        fab.style.transition = 'none';
        e.preventDefault();
    }, { passive: false });

    // Mobile tap: touchend on FAB.
    fab.addEventListener('touchend', function(e) {
        if (!isMobileView()) return;
        const touch = e.changedTouches[0];
        const deltaX = Math.abs(touch.clientX - startX);
        const deltaY = Math.abs(touch.clientY - startY);
        if (deltaX < 15 && deltaY < 15) {
            e.preventDefault();
            openChatWindow();
        }
    }, { passive: false });

    // Desktop touch drag.
    document.addEventListener('touchmove', function(e) {
        if (!isDraggingFab || isMobileView()) return;
        const deltaX = startX - e.touches[0].clientX;
        const newRight = startRight + deltaX;
        const boundedRight = Math.max(24, Math.min(newRight, window.innerWidth - fab.offsetWidth - 24));
        chatbotState.fabPosition.right = boundedRight;
        fab.style.right = `${boundedRight}px`;
        if (Math.abs(deltaX) > 5) hasMoved = true;
        e.preventDefault();
    }, { passive: false });

    document.addEventListener('touchend', function() {
        if (!isDraggingFab || isMobileView()) return;
        isDraggingFab = false;
        fab.style.cursor = '';
        fab.style.transition = '';
        if (!hasMoved) openChatWindow();
        hasMoved = false;
    });
}

// ==================== Utility ====================
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showToastChatbot(type, message) {
    if (typeof window.showToast === 'function') { window.showToast(type, message); return; }
    console.log(`[${type}] ${message}`);
}

function showChatError(message) {
    const messagesContainer = document.getElementById('chatMessages');
    if (!messagesContainer) { console.error('Chat error:', message); return; }
    const errorHtml = `
        <div class="message-group ai-message">
            <div class="message-avatar chatbot-warning-avatar">
                <i class="fas fa-exclamation-triangle ai-chatbot-icon-sm"></i>
            </div>
            <div class="message-content">
                <div class="message-bubble chatbot-warning-bubble">
                    ${escapeHtml(message)}
                </div>
            </div>
        </div>
    `;
    messagesContainer.insertAdjacentHTML('beforeend', errorHtml);
    scrollToBottom();
}

function clearAllConversations() {
    showConfirmDialog('Hapus SEMUA conversation? Tindakan ini tidak dapat dibatalkan.', () => {
        chatbotState.conversations = [];
        chatbotState.currentConversationId = null;
        chatbotState.conversationHistory = [];
        saveConversationsToStorage();
        renderConversationsList();
        clearChatMessages();
        updateConversationTitle('New Chat');
        showToastChatbot('success', 'Semua conversation dihapus');
        createNewConversation();
    });
}

// ==================== Custom Confirmation Dialog ====================
let confirmCallback = null;

function showConfirmDialog(message, onConfirm) {
    const dialog = document.getElementById('confirmDialog');
    const messageEl = document.getElementById('confirmDialogMessage');
    if (!dialog || !messageEl) return;
    messageEl.textContent = message;
    confirmCallback = onConfirm;
    dialog.classList.remove('d-none');
}

function confirmAction() {
    const dialog = document.getElementById('confirmDialog');
    if (dialog) dialog.classList.add('d-none');
    if (confirmCallback) { confirmCallback(); confirmCallback = null; }
}

function cancelConfirm() {
    const dialog = document.getElementById('confirmDialog');
    if (dialog) dialog.classList.add('d-none');
    confirmCallback = null;
}

// ==================== Make Functions Global ====================
window.initChatbot = initChatbot;
window.openChatWindow = openChatWindow;
window.closeChatWindow = closeChatWindow;
window.sendMessage = sendMessage;
window.sendQuickAction = sendQuickAction;
window.copyMessage = copyMessage;
window.toggleConversationsSidebar = toggleConversationsSidebar;
window.createNewConversation = createNewConversation;
window.loadConversation = loadConversation;
window.deleteConversation = deleteConversation;
window.clearAllConversations = clearAllConversations;
window.removeAttachmentByIndex = removeAttachmentByIndex;
window.openLightbox = openLightbox;
window.closeLightbox = closeLightbox;
window.showConfirmDialog = showConfirmDialog;
window.confirmAction = confirmAction;
window.cancelConfirm = cancelConfirm;

// ==================== Auto-initialize ====================
document.addEventListener('DOMContentLoaded', function() {
    initChatbot();
});
