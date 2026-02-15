/**
 * AI Chatbot General Assistant - Frontend Logic
 * SIPADUHOK - Claude/ChatGPT Style Interface
 */

// ==================== State Management ====================
const chatbotState = {
    isOpen: false,
    conversationHistory: [], // [{role, content}, ...]
    isWaitingResponse: false,
    quickActionsLoaded: false,
    modelsLoaded: false, // NEW: Track if models are loaded
    selectedModel: 'llama-3.3-70b-versatile',
    attachedFile: null, // {file: File, preview: string}
    availableModels: [],
    fabPosition: { right: 96 }, // FAB position (draggable)
    isDragging: false,
};

// ==================== Initialize Chatbot ====================
function initChatbot() {
    // Don't load models here - only load when user opens chat window
    // This prevents API errors from showing on every page load
    restoreChatState();
    setupEventListeners();
    initDraggableFab(); // NEW: Initialize draggable FAB
    console.log('AI Chatbot initialized');
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
            // API responded but no models configured
            console.error('[AI Chatbot] ✗ No models available. Response:', data);
            throw new Error(data.error || 'No models available');
        }
    } catch (error) {
        console.error('[AI Chatbot] ✗ Error loading models:', error);

        // Fallback: Use default models if fetch fails
        console.log('[AI Chatbot] Using fallback default models...');
        const defaultModels = [
            {
                id: 'llama-3.3-70b-versatile',
                name: 'Llama 3.3 70B (Fast)',
                provider: 'groq',
                supports_vision: false,
                default: true
            },
            {
                id: 'meta-llama/llama-4-scout-17b-16e-instruct',
                name: 'Llama 4 Scout (Vision)',
                provider: 'groq',
                supports_vision: true,
                default: false
            },
            {
                id: 'qwen-2.5-32b-instruct',
                name: 'Qwen 2.5 32B',
                provider: 'groq',
                supports_vision: false,
                default: false
            }
        ];

        chatbotState.availableModels = defaultModels;
        populateModelSelector(defaultModels);

        // Show warning (not blocking error)
        showChatError('⚠️ Menggunakan model default. Jika mengalami masalah, hubungi admin.');
    }
}

// ==================== Populate Model Selector ====================
function populateModelSelector(models) {
    const selector = document.getElementById('modelSelector');
    if (!selector) return;

    selector.innerHTML = models.map(m =>
        `<option value="${m.id}" ${m.default ? 'selected' : ''}>
            ${m.name} ${m.supports_vision ? '📷' : ''}
        </option>`
    ).join('');

    // Set initial selected model
    const defaultModel = models.find(m => m.default);
    if (defaultModel) {
        chatbotState.selectedModel = defaultModel.id;
    }

    // Restore saved model from localStorage
    const savedModel = localStorage.getItem('selectedChatModel');
    if (savedModel && models.some(m => m.id === savedModel)) {
        chatbotState.selectedModel = savedModel;
        selector.value = savedModel;
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

    // Load models only when user first opens chat (not on page load)
    if (!chatbotState.modelsLoaded) {
        await loadAvailableModels();
        chatbotState.modelsLoaded = true;
    }

    if (!chatbotState.quickActionsLoaded) {
        loadQuickActions();
    }

    localStorage.setItem('aiChatbotOpen', 'true');
    scrollToBottom();
}

function closeChatWindow() {
    const window = document.getElementById('aiChatbotWindow');
    const fab = document.getElementById('aiChatbotFab');

    if (!window || !fab) return;

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

// ==================== Handle File Attachment ====================
function handleFileAttachment(event) {
    const file = event.target.files[0];
    if (!file) return;

    // Validate file type
    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'application/pdf'];
    if (!validTypes.includes(file.type)) {
        showToastChatbot('error', 'Format file tidak didukung. Gunakan JPG, PNG, atau PDF.');
        event.target.value = '';
        return;
    }

    // Validate file size (max 4MB)
    if (file.size > 4 * 1024 * 1024) {
        showToastChatbot('error', 'File terlalu besar. Maksimal 4MB.');
        event.target.value = '';
        return;
    }

    // Smart model switching based on file type
    const selectedModel = chatbotState.availableModels.find(m => m.id === chatbotState.selectedModel);

    // Handle IMAGE files
    if (file.type.startsWith('image/')) {
        if (selectedModel && !selectedModel.supports_vision) {
            showToastChatbot('warning', 'Model text-only tidak support gambar');

            // Auto-switch to vision model
            const visionModel = chatbotState.availableModels.find(m => m.supports_vision);
            if (visionModel) {
                chatbotState.selectedModel = visionModel.id;
                const selector = document.getElementById('modelSelector');
                if (selector) selector.value = visionModel.id;
                showToastChatbot('info', `✓ Beralih ke ${visionModel.name}`);
            } else {
                showToastChatbot('error', 'Tidak ada model vision tersedia. Hubungi admin.');
                event.target.value = '';
                return;
            }
        }
    }

    // Handle PDF files - require Gemini for best support
    if (file.type === 'application/pdf') {
        // PDF works best with Gemini
        const geminiModel = chatbotState.availableModels.find(m => m.provider === 'gemini');

        if (!selectedModel || !selectedModel.supports_vision) {
            showToastChatbot('warning', 'PDF memerlukan model vision');

            if (geminiModel) {
                chatbotState.selectedModel = geminiModel.id;
                const selector = document.getElementById('modelSelector');
                if (selector) selector.value = geminiModel.id;
                showToastChatbot('info', `✓ Beralih ke ${geminiModel.name} (Best for PDF)`);
            } else {
                // Fallback to any vision model
                const visionModel = chatbotState.availableModels.find(m => m.supports_vision);
                if (visionModel) {
                    chatbotState.selectedModel = visionModel.id;
                    const selector = document.getElementById('modelSelector');
                    if (selector) selector.value = visionModel.id;
                    showToastChatbot('warning', `⚠️ Using ${visionModel.name} for PDF (Limited support)`);
                } else {
                    showToastChatbot('error', 'PDF memerlukan Gemini API. Hubungi admin.');
                    event.target.value = '';
                    return;
                }
            }
        } else if (selectedModel.provider !== 'gemini') {
            // Current model is vision but not Gemini - suggest switch
            if (geminiModel) {
                showToastChatbot('info', 'Gemini recommended for PDF');
                // Optional auto-switch (uncomment if you want)
                // chatbotState.selectedModel = geminiModel.id;
                // document.getElementById('modelSelector').value = geminiModel.id;
            }
        }
    }

    // Store file
    chatbotState.attachedFile = {file, preview: file.name};

    // Show preview
    showAttachmentPreview(file);
}

// ==================== Show Attachment Preview ====================
function showAttachmentPreview(file) {
    const preview = document.getElementById('attachmentPreview');
    const nameEl = document.getElementById('attachmentName');

    if (!preview || !nameEl) return;

    const icon = file.type.startsWith('image/') ? 'fa-file-image' : 'fa-file-pdf';
    const iconEl = preview.querySelector('i');
    if (iconEl) {
        iconEl.className = `fas ${icon} me-2`;
    }

    nameEl.textContent = file.name;
    preview.classList.remove('d-none');
}

// ==================== Remove Attachment ====================
function removeAttachment() {
    chatbotState.attachedFile = null;
    const fileInput = document.getElementById('fileAttachment');
    const preview = document.getElementById('attachmentPreview');

    if (fileInput) fileInput.value = '';
    if (preview) preview.classList.add('d-none');
}

// ==================== Send Message ====================
async function sendMessage(messageText = null) {
    const input = document.getElementById('chatInput');
    if (!input) return;

    const message = messageText || input.value.trim();

    if (!message && !chatbotState.attachedFile) {
        showToastChatbot('warning', 'Ketik pesan atau lampirkan file');
        return;
    }

    if (chatbotState.isWaitingResponse) return;

    // Add user message to UI
    addMessage('user', message, chatbotState.attachedFile);

    // Clear input
    input.value = '';
    const currentFile = chatbotState.attachedFile;
    chatbotState.attachedFile = null;
    const preview = document.getElementById('attachmentPreview');
    if (preview) preview.classList.add('d-none');
    const fileInput = document.getElementById('fileAttachment');
    if (fileInput) fileInput.value = '';
    updateCharCount(0);

    // Show typing indicator
    showTypingIndicator();

    // Call API
    chatbotState.isWaitingResponse = true;
    const response = await sendMessageToApi(message, currentFile);
    chatbotState.isWaitingResponse = false;

    // Hide typing indicator
    hideTypingIndicator();

    // Add AI response
    if (response.success) {
        addMessage('assistant', response.response);
    } else {
        addMessage('assistant', `❌ Error: ${response.error}`);
    }

    scrollToBottom();
}

// ==================== Send Message to API ====================
async function sendMessageToApi(message, attachedFile) {
    const formData = new FormData();
    formData.append('message', message);
    formData.append('model', chatbotState.selectedModel);
    formData.append('history', JSON.stringify(chatbotState.conversationHistory));

    if (attachedFile && attachedFile.file) {
        formData.append('attachment', attachedFile.file);
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
                {role: 'user', content: message},
                {role: 'assistant', content: data.response}
            );

            // Trim history to last 20 messages (10 exchanges)
            if (chatbotState.conversationHistory.length > 20) {
                chatbotState.conversationHistory = chatbotState.conversationHistory.slice(-20);
            }
        }

        return data;
    } catch (error) {
        console.error('API Error:', error);
        return {success: false, error: error.message || 'Koneksi gagal. Coba lagi.'};
    }
}

// ==================== Add Message to UI ====================
function addMessage(role, content, attachment = null) {
    const messagesContainer = document.getElementById('chatMessages');
    if (!messagesContainer) return;

    const isUser = role === 'user';
    const now = new Date();
    const time = now.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'});

    const attachmentHtml = attachment
        ? `<div class="message-attachment">
               <i class="fas ${attachment.file.type.startsWith('image/') ? 'fa-image' : 'fa-file-pdf'}"></i>
               ${attachment.preview}
           </div>`
        : '';

    const messageHtml = `
        <div class="message-group ${isUser ? 'user-message' : 'ai-message'}">
            ${!isUser ? '<div class="message-avatar">AI</div>' : ''}
            <div class="message-content">
                ${attachmentHtml}
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

// ==================== Clear Conversation ====================
function clearConversation() {
    if (!confirm('Yakin ingin menghapus semua percakapan?')) return;

    chatbotState.conversationHistory = [];

    const messagesContainer = document.getElementById('chatMessages');
    if (!messagesContainer) return;

    // Clear all messages except welcome and quick actions
    const messages = messagesContainer.querySelectorAll('.message-group:not(:first-child)');
    messages.forEach(msg => msg.remove());

    showToastChatbot('success', 'Percakapan dihapus');
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
    // FAB click handled in initDraggableFab() to prevent conflict with drag

    // Model selector change
    const modelSelector = document.getElementById('modelSelector');
    if (modelSelector) {
        modelSelector.addEventListener('change', (e) => {
            chatbotState.selectedModel = e.target.value;
            localStorage.setItem('selectedChatModel', e.target.value);
        });
    }

    // File attachment
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

    // Restore saved position from localStorage
    const savedPosition = localStorage.getItem('aiChatbotFabPosition');
    if (savedPosition) {
        chatbotState.fabPosition = JSON.parse(savedPosition);
        fab.style.right = `${chatbotState.fabPosition.right}px`;
    }

    let isDraggingFab = false;
    let hasMoved = false;
    let startX = 0;
    let startRight = 0;

    // Mouse events (desktop)
    fab.addEventListener('mousedown', onMouseDown);
    document.addEventListener('mousemove', onMouseMove);
    document.addEventListener('mouseup', onMouseUp);

    // Touch events (mobile)
    fab.addEventListener('touchstart', onTouchStart, { passive: false });
    document.addEventListener('touchmove', onTouchMove, { passive: false });
    document.addEventListener('touchend', onTouchEnd);

    function onMouseDown(e) {
        isDraggingFab = true;
        hasMoved = false;
        startX = e.clientX;
        startRight = chatbotState.fabPosition.right;
        fab.style.cursor = 'grabbing';
        fab.style.transition = 'none'; // Disable transition during drag
        e.preventDefault(); // Prevent text selection
    }

    function onMouseMove(e) {
        if (!isDraggingFab) return;

        const deltaX = startX - e.clientX; // Distance moved (right is positive)
        const newRight = startRight + deltaX;

        // Limit drag area
        const viewportWidth = window.innerWidth;
        const fabWidth = fab.offsetWidth;
        const minRight = 24;
        const maxRight = viewportWidth - fabWidth - 24;

        const boundedRight = Math.max(minRight, Math.min(newRight, maxRight));

        chatbotState.fabPosition.right = boundedRight;
        fab.style.right = `${boundedRight}px`;

        // Mark as moved if dragged more than 5px
        if (Math.abs(deltaX) > 5) {
            hasMoved = true;
        }
    }

    function onMouseUp(e) {
        if (!isDraggingFab) return;

        isDraggingFab = false;
        fab.style.cursor = 'grab';
        fab.style.transition = ''; // Re-enable transition

        // Save position
        localStorage.setItem('aiChatbotFabPosition', JSON.stringify(chatbotState.fabPosition));

        // If not moved significantly, treat as click
        if (!hasMoved) {
            openChatWindow();
        }

        // Reset
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

        localStorage.setItem('aiChatbotFabPosition', JSON.stringify(chatbotState.fabPosition));

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
    // Check if global showToast function exists (from other scripts)
    if (typeof window.showToast === 'function') {
        window.showToast(type, message);
        return;
    }

    // Fallback: only log to console (NO ALERT to prevent disrupting user on every page)
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

// ==================== Make Functions Global ====================
window.initChatbot = initChatbot;
window.openChatWindow = openChatWindow;
window.closeChatWindow = closeChatWindow;
window.sendMessage = sendMessage;
window.sendQuickAction = sendQuickAction;
window.removeAttachment = removeAttachment;
window.clearConversation = clearConversation;
window.copyMessage = copyMessage;
