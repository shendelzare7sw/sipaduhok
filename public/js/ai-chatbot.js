/**
 * AI Chatbot General Assistant - Frontend Logic with Conversation History
 * SIPADUHOK - Claude/ChatGPT Style Interface
 */

// ==================== State Management ====================
const chatbotState = {
    isOpen: false,
    conversationHistory: [], // [{role, content}, ...]
    isWaitingResponse: false,
    quickActionsLoaded: false,
    modelsLoaded: false,
    selectedModel: 'llama-3.3-70b-versatile',
    attachedFiles: [], // Multiple files: [{file: File, preview: string, dataUrl: string}, ...]
    availableModels: [],
    fabPosition: { right: 96 },
    isDragging: false,

    // Conversation Management
    conversations: [], // [{id, title, messages, created_at, updated_at}, ...]
    currentConversationId: null,
    isSidebarOpen: false,
};

// ==================== Initialize Chatbot ====================
function initChatbot() {
    loadConversationsFromStorage();
    restoreChatState();
    setupEventListeners();
    initDraggableFab();
    console.log('AI Chatbot initialized');
}

// ==================== Conversations Management ====================

// Load conversations from localStorage
function loadConversationsFromStorage() {
    const stored = localStorage.getItem('aiChatbotConversations');
    if (stored) {
        try {
            const parsed = JSON.parse(stored);
            // Validate that it's an array
            if (Array.isArray(parsed)) {
                // Filter out invalid conversations
                chatbotState.conversations = parsed.filter(conv =>
                    conv &&
                    typeof conv === 'object' &&
                    conv.id &&
                    conv.title !== undefined
                );
            } else {
                console.warn('Invalid conversations data, resetting...');
                chatbotState.conversations = [];
                localStorage.removeItem('aiChatbotConversations');
            }
        } catch (e) {
            console.error('Error loading conversations:', e);
            chatbotState.conversations = [];
            localStorage.removeItem('aiChatbotConversations');
        }
    }
}

// Save conversations to localStorage
function saveConversationsToStorage() {
    localStorage.setItem('aiChatbotConversations', JSON.stringify(chatbotState.conversations));
}

// Create new conversation
function createNewConversation() {
    // Save current conversation first
    if (chatbotState.currentConversationId) {
        saveCurrentConversation();
    }

    // Create new conversation
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

    // Close sidebar on mobile
    if (window.innerWidth <= 768) {
        toggleConversationsSidebar();
    }
}

// Save current conversation
function saveCurrentConversation() {
    if (!chatbotState.currentConversationId) return;

    const conv = chatbotState.conversations.find(c => c.id === chatbotState.currentConversationId);
    if (!conv) return;

    conv.messages = chatbotState.conversationHistory;
    conv.updated_at = new Date().toISOString();

    // Auto-generate title from first user message
    if (conv.title === 'New Chat' && conv.messages.length > 0) {
        const firstUserMsg = conv.messages.find(m => m.role === 'user');
        if (firstUserMsg) {
            conv.title = firstUserMsg.content.substring(0, 50) + (firstUserMsg.content.length > 50 ? '...' : '');
        }
    }

    saveConversationsToStorage();
    renderConversationsList();
}

// Load conversation by ID
function loadConversation(conversationId) {
    // Save current first
    if (chatbotState.currentConversationId) {
        saveCurrentConversation();
    }

    const conv = chatbotState.conversations.find(c => c.id === conversationId);
    if (!conv) return;

    chatbotState.currentConversationId = conversationId;
    chatbotState.conversationHistory = [...conv.messages];

    clearChatMessages();

    // Render all messages
    conv.messages.forEach(msg => {
        addMessage(msg.role, msg.content, null, false); // false = don't save to history
    });

    updateConversationTitle(conv.title);
    renderConversationsList();
    scrollToBottom();

    // Close sidebar on mobile
    if (window.innerWidth <= 768) {
        toggleConversationsSidebar();
    }
}

// Delete conversation
function deleteConversation(conversationId, event) {
    event.stopPropagation(); // Prevent loading conversation

    // Show custom confirm dialog
    showConfirmDialog('Hapus conversation ini?', () => {
        chatbotState.conversations = chatbotState.conversations.filter(c => c.id !== conversationId);

        // If deleting current conversation, create new one
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

// Render conversations list
function renderConversationsList() {
    const container = document.getElementById('conversationsList');
    if (!container) return;

    console.log('[Chatbot] Rendering conversations:', chatbotState.conversations.length);

    if (chatbotState.conversations.length === 0) {
        container.innerHTML = '<p class="text-center text-muted" style="font-size: 12px; margin-top: 20px;">Belum ada conversation</p>';
        return;
    }

    // Group by date
    const groups = {
        today: [],
        yesterday: [],
        thisWeek: [],
        older: [],
    };

    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);
    const weekAgo = new Date(today);
    weekAgo.setDate(weekAgo.getDate() - 7);

    chatbotState.conversations.forEach(conv => {
        const convDate = new Date(conv.updated_at);
        const convDay = new Date(convDate.getFullYear(), convDate.getMonth(), convDate.getDate());

        if (convDay >= today) {
            groups.today.push(conv);
        } else if (convDay >= yesterday) {
            groups.yesterday.push(conv);
        } else if (convDay >= weekAgo) {
            groups.thisWeek.push(conv);
        } else {
            groups.older.push(conv);
        }
    });

    let html = '';

    if (groups.today.length > 0) {
        html += '<div class="conversation-date-group">Today</div>';
        groups.today.forEach(conv => {
            const item = renderConversationItem(conv);
            if (item) html += item;
        });
    }

    if (groups.yesterday.length > 0) {
        html += '<div class="conversation-date-group">Yesterday</div>';
        groups.yesterday.forEach(conv => {
            const item = renderConversationItem(conv);
            if (item) html += item;
        });
    }

    if (groups.thisWeek.length > 0) {
        html += '<div class="conversation-date-group">Last 7 Days</div>';
        groups.thisWeek.forEach(conv => {
            const item = renderConversationItem(conv);
            if (item) html += item;
        });
    }

    if (groups.older.length > 0) {
        html += '<div class="conversation-date-group">Older</div>';
        groups.older.forEach(conv => {
            const item = renderConversationItem(conv);
            if (item) html += item;
        });
    }

    // Clear and set innerHTML
    container.innerHTML = '';
    container.innerHTML = html;
}

// Render single conversation item
function renderConversationItem(conv) {
    if (!conv || !conv.id) return '';

    const isActive = conv.id === chatbotState.currentConversationId;
    const date = new Date(conv.updated_at || new Date());
    const timeStr = date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    const title = conv.title || 'New Chat';

    return `
        <div class="conversation-item ${isActive ? 'active' : ''}" onclick="loadConversation('${conv.id}')" data-conv-id="${conv.id}">
            <div class="conversation-title">${escapeHtml(title)}</div>
            <div class="conversation-date">${timeStr}</div>
            <button class="conversation-delete" onclick="deleteConversation('${conv.id}', event)" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
}

// Update conversation title in footer
function updateConversationTitle(title) {
    const titleEl = document.getElementById('currentConversationTitle');
    if (titleEl) {
        titleEl.textContent = title;
    }
}

// Clear chat messages (keep welcome message and quick actions)
function clearChatMessages() {
    const container = document.getElementById('chatMessages');
    if (!container) return;

    // Remove all messages except first 2 (welcome + quick actions)
    const messages = container.querySelectorAll('.message-group');
    messages.forEach(msg => msg.remove());

    // Re-add welcome message
    const welcomeHtml = `
        <div class="message-group ai-message">
            <div class="message-avatar">AI</div>
            <div class="message-content">
                <div class="message-bubble">
                    Halo! 👋 Saya asisten AI SIPADUHOK. Ada yang bisa saya bantu?
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('afterbegin', welcomeHtml);

    // Reload quick actions if needed
    const quickActionsContainer = document.getElementById('quickActionsContainer');
    if (quickActionsContainer && !chatbotState.quickActionsLoaded) {
        loadQuickActions();
    }
}

// Toggle conversations sidebar
function toggleConversationsSidebar() {
    const sidebar = document.getElementById('conversationsSidebar');
    const chatWindow = document.getElementById('aiChatbotWindow');
    if (!sidebar || !chatWindow) return;

    sidebar.classList.toggle('active');
    chatbotState.isSidebarOpen = sidebar.classList.contains('active');

    // Toggle chat window width
    if (chatbotState.isSidebarOpen) {
        chatWindow.classList.add('sidebar-open');
    } else {
        chatWindow.classList.remove('sidebar-open');
    }

    // Load conversations list if opening for first time
    if (chatbotState.isSidebarOpen && chatbotState.conversations.length > 0) {
        renderConversationsList();
    }
}

// ==================== Load Available Models ====================
async function loadAvailableModels() {
    try {
        console.log('[AI Chatbot] Loading models from /ai-chatbot/models...');

        const response = await fetch('/ai-chatbot/models', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        console.log('[AI Chatbot] Response status:', response.status, response.statusText);

        const data = await response.json();
        console.log('[AI Chatbot] Response data:', data);

        if (response.ok && data.success && data.models && data.models.length > 0) {
            console.log('[AI Chatbot] ✓ Models loaded:', data.models.length);
            chatbotState.availableModels = data.models;
            populateModelSelector(data.models);
        } else {
            console.error('[AI Chatbot] ✗ No models available. Response:', data);
            throw new Error(data.error || 'No models available');
        }
    } catch (error) {
        console.error('[AI Chatbot] ✗ Error loading models:', error);

        // Fallback: Use default models
        console.log('[AI Chatbot] Using fallback default models...');
        const defaultModels = [
            {
                id: 'llama-3.3-70b-versatile',
                name: 'Llama 3.3 70B (Recommended)',
                provider: 'groq',
                supports_vision: false,
                supports_pdf: false,
                default: true
            },
            {
                id: 'llama-3.1-8b-instant',
                name: 'Llama 3.1 8B (Fastest)',
                provider: 'groq',
                supports_vision: false,
                supports_pdf: false,
                default: false
            },
            {
                id: 'meta-llama/llama-4-scout-17b-16e-instruct',
                name: 'Llama 4 Scout (Vision)',
                provider: 'groq',
                supports_vision: true,
                supports_pdf: false,
                default: false
            }
        ];

        chatbotState.availableModels = defaultModels;
        populateModelSelector(defaultModels);

        showChatError('⚠️ Menggunakan model default. Jika mengalami masalah, hubungi admin.');
    }
}

// ==================== Populate Model Selector ====================
function populateModelSelector(models) {
    const selector = document.getElementById('modelSelector');
    if (!selector) return;

    // FORCE DEFAULT: Always prefer Llama 3.3 70B (Groq) for chatbot to save Gemini quota
    // Override backend default setting
    const groqDefaultModel = models.find(m => m.id === 'llama-3.3-70b-versatile');
    const forcedDefaultId = groqDefaultModel ? 'llama-3.3-70b-versatile' : models[0]?.id;

    selector.innerHTML = models.map(m => {
        let icons = '';
        if (m.supports_vision) icons += '📷';
        if (m.supports_pdf) icons += '📄';

        // Force Llama 3.3 70B as selected, ignore backend default
        const isDefault = m.id === forcedDefaultId;

        return `<option value="${m.id}" ${isDefault ? 'selected' : ''}>
            ${m.name} ${icons}
        </option>`;
    }).join('');

    // Set initial selected model to Groq default (Llama 3.3 70B)
    chatbotState.selectedModel = forcedDefaultId;

    // Restore saved model from localStorage ONLY if it's a Groq model (not Gemini)
    // Gemini should only be used for vision/PDF, not as default chat model
    const savedModel = localStorage.getItem('selectedChatModel');
    if (savedModel && models.some(m => m.id === savedModel)) {
        const savedModelInfo = models.find(m => m.id === savedModel);

        // Only restore if saved model is Groq (Llama/Qwen/etc), NOT Gemini
        // Gemini is for vision/PDF use case only, always reset to Llama for text chat
        if (savedModelInfo && savedModelInfo.provider === 'groq') {
            chatbotState.selectedModel = savedModel;
            selector.value = savedModel;
            console.log(`[AI Chatbot] ✓ Restored saved Groq model: ${savedModel}`);
        } else {
            // Saved model is Gemini or invalid - ignore and use Llama default
            console.log(`[AI Chatbot] ⚠ Ignoring saved Gemini model, using Llama 3.3 70B default`);
            localStorage.removeItem('selectedChatModel'); // Clear invalid saved model
        }
    } else {
        // No saved model - use Llama 3.3 70B as default
        console.log(`[AI Chatbot] ✓ Using forced default model: ${forcedDefaultId} (Groq - Free & Fast)`);
    }
}

// ==================== Open/Close Chat Window ====================
async function openChatWindow() {
    const window = document.getElementById('aiChatbotWindow');
    const fab = document.getElementById('aiChatbotFab');

    if (!window || !fab) return;

    window.classList.add('active');
    fab.classList.add('hidden');
    chatbotState.isOpen = true;

    // Load models only when user first opens chat
    if (!chatbotState.modelsLoaded) {
        await loadAvailableModels();
        chatbotState.modelsLoaded = true;
    }

    if (!chatbotState.quickActionsLoaded) {
        loadQuickActions();
    }

    // Create first conversation if none exists
    if (chatbotState.conversations.length === 0 && !chatbotState.currentConversationId) {
        createNewConversation();
    }

    localStorage.setItem('aiChatbotOpen', 'true');
    scrollToBottom();
}

function closeChatWindow() {
    const window = document.getElementById('aiChatbotWindow');
    const fab = document.getElementById('aiChatbotFab');

    if (!window || !fab) return;

    // Save current conversation before closing
    if (chatbotState.currentConversationId) {
        saveCurrentConversation();
    }

    window.classList.remove('active');
    fab.classList.remove('hidden');
    chatbotState.isOpen = false;

    localStorage.setItem('aiChatbotOpen', 'false');
}

// ==================== Load Quick Actions ====================
async function loadQuickActions() {
    try {
        const response = await fetch('/ai-chatbot/quick-actions', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) throw new Error('Failed to load quick actions');

        const data = await response.json();
        if (data.success) {
            renderQuickActions(data.quick_actions);
            chatbotState.quickActionsLoaded = true;
        }
    } catch (error) {
        console.error('Error loading quick actions:', error);
    }
}

// ==================== Render Quick Actions ====================
function renderQuickActions(actions) {
    const container = document.getElementById('quickActionsContainer');
    if (!container || !actions || actions.length === 0) return;

    container.innerHTML = actions.map(action =>
        `<button class="quick-action-btn" onclick="sendQuickAction('${escapeHtml(action)}')">${action}</button>`
    ).join('');
}

// ==================== Send Quick Action ====================
function sendQuickAction(message) {
    const input = document.getElementById('chatInput');
    if (input) {
        input.value = message;
        sendMessage();
    }
}

// ==================== Handle Multiple File Attachments ====================
function handleFileAttachment(event) {
    const files = Array.from(event.target.files);
    if (files.length === 0) return;

    // Max 5 files
    if (chatbotState.attachedFiles.length + files.length > 5) {
        showToastChatbot('warning', 'Maksimal 5 file');
        event.target.value = '';
        return;
    }

    // Process each file
    files.forEach(file => {
        // Validate file type
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'application/pdf'];
        if (!validTypes.includes(file.type)) {
            showToastChatbot('error', `${file.name}: Format tidak didukung`);
            return;
        }

        // Validate file size (max 4MB)
        if (file.size > 4 * 1024 * 1024) {
            showToastChatbot('error', `${file.name}: File terlalu besar (max 4MB)`);
            return;
        }

        // Add to attached files
        const fileObj = { file, preview: file.name, dataUrl: null };

        // Generate preview for images
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                fileObj.dataUrl = e.target.result;
                renderAttachmentsPreview();
            };
            reader.readAsDataURL(file);
        }

        chatbotState.attachedFiles.push(fileObj);
    });

    // Smart model switching for vision/PDF support
    const hasPdf = chatbotState.attachedFiles.some(f => f.file.type === 'application/pdf');
    const hasImage = chatbotState.attachedFiles.some(f => f.file.type.startsWith('image/'));

    if (hasPdf || hasImage) {
        const selectedModel = chatbotState.availableModels.find(m => m.id === chatbotState.selectedModel);

        if (hasPdf) {
            // PDF only supported by Gemini - force switch to Gemini 2.5 Flash
            const geminiModel = chatbotState.availableModels.find(m => m.id === 'gemini-2.5-flash');

            if (geminiModel && chatbotState.selectedModel !== 'gemini-2.5-flash') {
                chatbotState.selectedModel = geminiModel.id;
                const selector = document.getElementById('modelSelector');
                if (selector) selector.value = geminiModel.id;
                showToastChatbot('info', `🔄 PDF hanya support Gemini 2.5 Flash. Model beralih otomatis.`);
            } else if (!geminiModel) {
                showToastChatbot('error', 'PDF memerlukan Gemini 2.5 Flash. Hubungi admin untuk konfigurasi API key.');
            }
        } else if (hasImage && selectedModel && !selectedModel.supports_vision) {
            // Image - switch to any vision model (Llama 4 Scout or Gemini)
            const visionModel = chatbotState.availableModels.find(m => m.supports_vision);
            if (visionModel) {
                chatbotState.selectedModel = visionModel.id;
                const selector = document.getElementById('modelSelector');
                if (selector) selector.value = visionModel.id;
                showToastChatbot('info', `✓ Beralih ke ${visionModel.name}`);
            } else {
                showToastChatbot('error', 'Tidak ada model vision tersedia');
            }
        }
    }

    renderAttachmentsPreview();
    event.target.value = ''; // Reset input
}

// ==================== Render Attachments Preview ====================
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
                    <img src="${fileObj.dataUrl}" alt="${fileObj.file.name}" onclick="openLightbox('${fileObj.dataUrl}')">
                    <button class="attachment-remove-btn" onclick="removeAttachmentByIndex(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        } else {
            return `
                <div class="attachment-preview-item file-preview">
                    <i class="fas fa-file-pdf"></i>
                    <button class="attachment-remove-btn" onclick="removeAttachmentByIndex(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        }
    }).join('');

    container.innerHTML = html;
}

// ==================== Remove Attachment by Index ====================
function removeAttachmentByIndex(index) {
    chatbotState.attachedFiles.splice(index, 1);
    renderAttachmentsPreview();
}

// ==================== Image Lightbox ====================
function openLightbox(dataUrl) {
    const lightbox = document.getElementById('imageLightbox');
    const lightboxImg = document.getElementById('lightboxImage');

    if (lightbox && lightboxImg) {
        lightboxImg.src = dataUrl;
        lightbox.classList.remove('d-none');
    }
}

function closeLightbox() {
    const lightbox = document.getElementById('imageLightbox');
    if (lightbox) {
        lightbox.classList.add('d-none');
    }
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

    // Create conversation if first message
    if (!chatbotState.currentConversationId) {
        createNewConversation();
    }

    // Add user message to UI
    addMessage('user', message, chatbotState.attachedFiles);

    // Clear input
    input.value = '';
    const currentFiles = [...chatbotState.attachedFiles];
    chatbotState.attachedFiles = [];
    renderAttachmentsPreview();
    updateCharCount(0);

    // Show typing indicator
    showTypingIndicator();

    // Call API
    chatbotState.isWaitingResponse = true;
    const response = await sendMessageToApi(message, currentFiles);
    chatbotState.isWaitingResponse = false;

    // Hide typing indicator
    hideTypingIndicator();

    // Add AI response
    if (response.success) {
        addMessage('assistant', response.response);
    } else {
        addMessage('assistant', `❌ Error: ${response.error}`);
    }

    // Save conversation
    saveCurrentConversation();

    scrollToBottom();
}

// ==================== Send Message to API ====================
async function sendMessageToApi(message, attachedFiles) {
    const formData = new FormData();
    formData.append('message', message);
    formData.append('model', chatbotState.selectedModel);
    formData.append('history', JSON.stringify(chatbotState.conversationHistory));

    // Attach multiple files
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
            // Update conversation history
            chatbotState.conversationHistory.push(
                { role: 'user', content: message },
                { role: 'assistant', content: data.response }
            );

            // Trim history to last 20 messages (10 exchanges)
            if (chatbotState.conversationHistory.length > 20) {
                chatbotState.conversationHistory = chatbotState.conversationHistory.slice(-20);
            }
        } else if (data.switch_to_gemini) {
            // Backend detected PDF with Groq model - auto-switch and retry
            const geminiModel = chatbotState.availableModels.find(m => m.id === 'gemini-2.5-flash');
            if (geminiModel) {
                chatbotState.selectedModel = geminiModel.id;
                const selector = document.getElementById('modelSelector');
                if (selector) selector.value = geminiModel.id;

                showToastChatbot('info', '🔄 Beralih ke Gemini untuk membaca PDF. Mengirim ulang...');

                // Retry with Gemini
                return await sendMessageToApi(message, attachedFiles);
            }
        }

        return data;
    } catch (error) {
        console.error('API Error:', error);
        return { success: false, error: error.message || 'Koneksi gagal. Coba lagi.' };
    }
}

// ==================== Add Message to UI ====================
function addMessage(role, content, attachments = null, saveToHistory = true) {
    const messagesContainer = document.getElementById('chatMessages');
    if (!messagesContainer) return;

    const isUser = role === 'user';
    const now = new Date();
    const time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

    // Render attachments
    let attachmentsHtml = '';
    if (attachments && attachments.length > 0) {
        const items = attachments.map(fileObj => {
            const isImage = fileObj.file.type.startsWith('image/');
            const icon = isImage ? 'fa-image' : 'fa-file-pdf';
            return `<div class="message-attachment-item"><i class="fas ${icon}"></i> ${fileObj.file.name}</div>`;
        }).join('');

        attachmentsHtml = `<div class="message-attachments">${items}</div>`;
    }

    const messageHtml = `
        <div class="message-group ${isUser ? 'user-message' : 'ai-message'}">
            ${!isUser ? '<div class="message-avatar">AI</div>' : ''}
            <div class="message-content">
                ${attachmentsHtml}
                <div class="message-bubble">${escapeHtml(content)}</div>
                <div class="message-meta">
                    <span class="message-time">${time}</span>
                    ${!isUser ? `<button class="btn-copy" onclick="copyMessage(this)" title="Copy"><i class="fas fa-copy"></i></button>` : ''}
                </div>
            </div>
            ${isUser ? '<div class="message-avatar"><i class="fas fa-user"></i></div>' : ''}
        </div>
    `;

    messagesContainer.insertAdjacentHTML('beforeend', messageHtml);

    // Add to conversation history
    if (saveToHistory) {
        chatbotState.conversationHistory.push({ role, content });
    }

    scrollToBottom();
}

// ==================== Show/Hide Typing Indicator ====================
function showTypingIndicator() {
    const indicator = document.getElementById('typingIndicator');
    if (indicator) {
        indicator.classList.remove('d-none');
        scrollToBottom();
    }
}

function hideTypingIndicator() {
    const indicator = document.getElementById('typingIndicator');
    if (indicator) {
        indicator.classList.add('d-none');
    }
}

// ==================== Copy Message ====================
function copyMessage(button) {
    const messageBubble = button.closest('.message-content').querySelector('.message-bubble');
    if (!messageBubble) return;

    const text = messageBubble.textContent;

    navigator.clipboard.writeText(text).then(() => {
        showToastChatbot('success', 'Pesan disalin');
    }).catch(err => {
        console.error('Copy failed:', err);
        showToastChatbot('error', 'Gagal menyalin pesan');
    });
}

// ==================== Auto-Resize Textarea ====================
function autoResizeTextarea(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
}

// ==================== Update Character Count ====================
function updateCharCount(length) {
    const charCountEl = document.getElementById('charCount');
    if (charCountEl) {
        charCountEl.textContent = length;
    }
}

// ==================== Scroll to Bottom ====================
function scrollToBottom() {
    const chatBody = document.getElementById('chatMessages');
    if (chatBody) {
        setTimeout(() => {
            chatBody.scrollTop = chatBody.scrollHeight;
        }, 100);
    }
}

// ==================== Restore Chat State ====================
function restoreChatState() {
    const wasOpen = localStorage.getItem('aiChatbotOpen') === 'true';
    if (wasOpen) {
        openChatWindow();
    }

    const savedModel = localStorage.getItem('selectedChatModel');
    if (savedModel) {
        chatbotState.selectedModel = savedModel;
    }
}

// ==================== Setup Event Listeners ====================
function setupEventListeners() {
    // FAB click handled in initDraggableFab()

    // Model selector change
    const modelSelector = document.getElementById('modelSelector');
    if (modelSelector) {
        modelSelector.addEventListener('change', (e) => {
            chatbotState.selectedModel = e.target.value;

            // Only save to localStorage if it's a Groq model (not Gemini)
            // Gemini should only be used temporarily for vision/PDF, not as default preference
            const selectedModelInfo = chatbotState.availableModels.find(m => m.id === e.target.value);
            if (selectedModelInfo && selectedModelInfo.provider === 'groq') {
                localStorage.setItem('selectedChatModel', e.target.value);
                console.log(`[AI Chatbot] ✓ Saved Groq model preference: ${e.target.value}`);
            } else if (selectedModelInfo && selectedModelInfo.provider === 'gemini') {
                // Don't save Gemini to localStorage - it's for temporary vision/PDF use only
                console.log(`[AI Chatbot] ℹ Gemini selected (temporary for vision/PDF) - not saved as default`);
            }
        });
    }

    // File attachment (multiple)
    const fileInput = document.getElementById('fileAttachment');
    if (fileInput) {
        fileInput.addEventListener('change', handleFileAttachment);
    }

    // Chat input - Enter key
    const chatInput = document.getElementById('chatInput');
    if (chatInput) {
        chatInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Auto-resize and char count
        chatInput.addEventListener('input', (e) => {
            autoResizeTextarea(e.target);
            updateCharCount(e.target.value.length);
        });
    }

    // Send button
    const sendButton = document.getElementById('sendButton');
    if (sendButton) {
        sendButton.addEventListener('click', () => sendMessage());
    }
}

// ==================== Initialize Draggable FAB ====================
function initDraggableFab() {
    const fab = document.getElementById('aiChatbotFab');
    if (!fab) return;

    // Always start at default position (reset on every page load)
    // Default position is set in CSS: right: 96px
    chatbotState.fabPosition = { right: 96 };

    let isDraggingFab = false;
    let hasMoved = false;
    let startX = 0;
    let startRight = 0;

    // Mouse events
    fab.addEventListener('mousedown', onMouseDown);
    document.addEventListener('mousemove', onMouseMove);
    document.addEventListener('mouseup', onMouseUp);

    // Touch events
    fab.addEventListener('touchstart', onTouchStart, { passive: false });
    document.addEventListener('touchmove', onTouchMove, { passive: false });
    document.addEventListener('touchend', onTouchEnd);

    function onMouseDown(e) {
        isDraggingFab = true;
        hasMoved = false;
        startX = e.clientX;
        startRight = chatbotState.fabPosition.right;
        fab.style.cursor = 'grabbing';
        fab.style.transition = 'none';
        e.preventDefault();
    }

    function onMouseMove(e) {
        if (!isDraggingFab) return;

        const deltaX = startX - e.clientX;
        const newRight = startRight + deltaX;

        const viewportWidth = window.innerWidth;
        const fabWidth = fab.offsetWidth;
        const minRight = 24;
        const maxRight = viewportWidth - fabWidth - 24;

        const boundedRight = Math.max(minRight, Math.min(newRight, maxRight));

        chatbotState.fabPosition.right = boundedRight;
        fab.style.right = `${boundedRight}px`;

        if (Math.abs(deltaX) > 5) {
            hasMoved = true;
        }
    }

    function onMouseUp(e) {
        if (!isDraggingFab) return;

        isDraggingFab = false;
        fab.style.cursor = 'grab';
        fab.style.transition = '';

        // Position is NOT saved to localStorage (resets on page refresh)

        if (!hasMoved) {
            openChatWindow();
        }

        hasMoved = false;
    }

    function onTouchStart(e) {
        isDraggingFab = true;
        hasMoved = false;
        startX = e.touches[0].clientX;
        startRight = chatbotState.fabPosition.right;
        fab.style.cursor = 'grabbing';
        fab.style.transition = 'none';
        e.preventDefault();
    }

    function onTouchMove(e) {
        if (!isDraggingFab) return;

        const deltaX = startX - e.touches[0].clientX;
        const newRight = startRight + deltaX;

        const viewportWidth = window.innerWidth;
        const fabWidth = fab.offsetWidth;
        const minRight = 24;
        const maxRight = viewportWidth - fabWidth - 24;

        const boundedRight = Math.max(minRight, Math.min(newRight, maxRight));

        chatbotState.fabPosition.right = boundedRight;
        fab.style.right = `${boundedRight}px`;

        if (Math.abs(deltaX) > 5) {
            hasMoved = true;
        }

        e.preventDefault();
    }

    function onTouchEnd(e) {
        if (!isDraggingFab) return;

        isDraggingFab = false;
        fab.style.cursor = 'grab';
        fab.style.transition = '';

        // Position is NOT saved to localStorage (resets on page refresh)

        if (!hasMoved) {
            openChatWindow();
        }

        hasMoved = false;
    }
}

// ==================== Utility: Escape HTML ====================
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ==================== Utility: Show Toast ====================
function showToastChatbot(type, message) {
    if (typeof window.showToast === 'function') {
        window.showToast(type, message);
        return;
    }

    const icons = {
        success: '✅',
        error: '❌',
        warning: '⚠️',
        info: 'ℹ️',
    };

    console.log(`${icons[type] || 'ℹ️'} ${message}`);
}

// ==================== Show Error Inside Chat Window ====================
function showChatError(message) {
    const messagesContainer = document.getElementById('chatMessages');
    if (!messagesContainer) {
        console.error('Chat error:', message);
        return;
    }

    const errorHtml = `
        <div class="message-group ai-message">
            <div class="message-avatar" style="background: #f59e0b;">
                <i class="fas fa-exclamation-triangle" style="font-size: 14px;"></i>
            </div>
            <div class="message-content">
                <div class="message-bubble" style="background: #fef3c7; color: #92400e; border: 1px solid #fbbf24; border-left: 3px solid #f59e0b;">
                    ${escapeHtml(message)}
                </div>
            </div>
        </div>
    `;

    messagesContainer.insertAdjacentHTML('beforeend', errorHtml);
    scrollToBottom();
}

// Clear all conversations (debugging)
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

        // Auto create new conversation
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

    if (confirmCallback) {
        confirmCallback();
        confirmCallback = null;
    }
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
