{{-- AI Chatbot General Assistant --}}
{{-- Floating Action Button + Chat Window --}}

{{-- Floating Action Button (FAB) - "Tanya AI" --}}
<button id="aiChatbotFab" class="ai-chatbot-fab">
    <i class="fas fa-headset"></i>
    <span class="fab-text">Bantuan</span>
</button>

{{-- Chat Window --}}
<div id="aiChatbotWindow" class="ai-chatbot-window">
    {{-- Conversations Sidebar (Toggleable) --}}
    <div id="conversationsSidebar" class="conversations-sidebar">
        <div class="conversations-header">
            <h6 class="mb-0 fw-bold">Conversations</h6>
            <button class="btn btn-sm btn-icon-sidebar" onclick="toggleConversationsSidebar()" title="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="conversations-body">
            {{-- New Chat Button --}}
            <button class="btn btn-primary w-100 mb-2 new-chat-btn" onclick="createNewConversation()">
                <i class="fas fa-plus me-2"></i> New Chat
            </button>

            {{-- Clear All Button (for debugging) --}}
            <button class="btn btn-outline-danger w-100 mb-3" style="font-size: 12px; padding: 6px 12px;" onclick="clearAllConversations()" title="Hapus semua conversation">
                <i class="fas fa-trash me-1"></i> Clear All
            </button>

            {{-- Conversations List (loaded dynamically) --}}
            <div id="conversationsList"></div>
        </div>
    </div>

    {{-- Main Chat Container --}}
    <div class="chat-main-container">
        {{-- Header --}}
        <div class="chatbot-header">
            <div class="header-left">
                {{-- Conversations Toggle Button --}}
                <button class="btn btn-sm btn-icon-header me-2" onclick="toggleConversationsSidebar()" title="Conversations">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="ai-logo"><i class="fas fa-headset" style="font-size:14px"></i></div>
                <span class="header-title">Asisten SIPADUHOK</span>
            </div>
            <div class="header-right">
                {{-- System Mode Badge (shown when answering from KB) --}}
                <span id="systemModeBadge" class="system-mode-badge">
                    <i class="fas fa-microchip"></i> Sistem
                </span>
                {{-- Model Switcher (hidden by default, shown on LLM fallback) --}}
                <select id="modelSelector" class="model-selector form-select form-select-sm" style="display:none">
                    <option value="llama-3.3-70b-versatile">Loading...</option>
                </select>
                <button class="btn btn-sm btn-icon-header ms-2" onclick="closeChatWindow()" title="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        {{-- Chat Body (Messages) --}}
        <div id="chatMessages" class="chatbot-body">
            {{-- Welcome Message --}}
            <div class="message-group ai-message">
                <div class="message-avatar"><i class="fas fa-headset" style="font-size:14px"></i></div>
                <div class="message-content">
                    <div class="message-bubble">
                        <i class="far fa-hand-paper" style="color:#f59e0b"></i> Halo <strong>{{ auth()->user()->name ?? 'User' }}</strong>! Saya <strong>Asisten SIPADUHOK</strong>.<br>
                        Tanyakan apa saja tentang sistem ini — saya tahu seluk-beluknya! <i class="fas fa-bullseye" style="color:#ef4444"></i><br>
                        <small style="color:#64748b"><i class="fas fa-info-circle"></i> Pertanyaan di luar sistem akan dijawab oleh AI LLM otomatis.</small>
                    </div>
                </div>
            </div>

            {{-- Quick Actions (System-based, loaded inline) --}}
            <div id="quickActionsContainer" class="quick-actions-container"></div>

            {{-- Typing Indicator (hidden by default) --}}
            <div id="typingIndicator" class="typing-indicator d-none">
                <div class="message-avatar">AI</div>
                <div class="typing-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>

        {{-- Chat Footer (Input Area) --}}
        <div class="chatbot-footer">
            {{-- Multiple Files Preview (hidden by default) --}}
            <div id="attachmentsPreview" class="attachments-preview d-none"></div>

            {{-- Input Row --}}
            <div class="input-row">
                {{-- File Attachment Button (Multiple Files) --}}
                <label for="fileAttachment" class="btn-attach" title="Attach files (max 5 images/PDFs)">
                    <i class="fas fa-paperclip"></i>
                    <input type="file" id="fileAttachment" accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf" multiple style="display:none">
                </label>

                {{-- Message Input --}}
                <textarea
                    id="chatInput"
                    class="chat-input form-control"
                    placeholder="Ketik pesan..."
                    rows="1"
                    maxlength="2000"
                ></textarea>

                {{-- Send Button --}}
                <button id="sendButton" class="btn-send" onclick="sendMessage()" title="Send (Enter)">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            {{-- Footer Actions --}}
            <div class="footer-actions">
                {{-- conversationInfo removed: managed via conversation sidebar toggle --}}
                <span id="currentConversationTitle" style="display:none"></span>
                <span class="char-count">
                    <span id="charCount">0</span>/2000
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Image Lightbox Modal --}}
<div id="imageLightbox" class="image-lightbox d-none" onclick="closeLightbox()">
    <span class="lightbox-close">&times;</span>
    <img id="lightboxImage" class="lightbox-content" src="" alt="Preview">
</div>

{{-- Custom Confirmation Dialog --}}
<div id="confirmDialog" class="confirm-dialog-overlay d-none">
    <div class="confirm-dialog-box">
        <div class="confirm-dialog-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="confirm-dialog-title">Konfirmasi</div>
        <div class="confirm-dialog-message" id="confirmDialogMessage">
            Hapus conversation ini?
        </div>
        <div class="confirm-dialog-buttons">
            <button class="confirm-dialog-btn confirm-dialog-cancel" onclick="cancelConfirm()">
                Batal
            </button>
            <button class="confirm-dialog-btn confirm-dialog-confirm" onclick="confirmAction()">
                Hapus
            </button>
        </div>
    </div>
</div>

{{-- Styles --}}
<style>
/* ==================== FAB (Floating Action Button) ==================== */
.ai-chatbot-fab {
    position: fixed;
    bottom: 82px;
    right: 96px;
    height: 56px;
    padding: 0 24px;
    border-radius: 28px;
    z-index: 1055;
    background: #3b82f6;
    color: white;
    border: none;
    font-weight: 600;
    font-size: 15px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    box-shadow: 0 4px 16px rgba(59, 130, 246, 0.3);
    transition: background 0.2s ease, box-shadow 0.3s ease,
                transform 0.3s ease, opacity 0.3s ease;
    cursor: pointer;
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

.ai-chatbot-fab:hover {
    background: #2563eb;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(37, 99, 235, 0.4);
}

.ai-chatbot-fab:active {
    cursor: grabbing;
}

/* Universal: hide both FABs when scrolling down (all screen sizes) */
.ai-chatbot-fab.fab-hidden-mobile {
    transform: translateX(200px);
    opacity: 0;
    pointer-events: none;
}

.ai-chatbot-fab.hidden {
    display: none !important;
}

/* ==================== Chat Window ==================== */
.ai-chatbot-window {
    position: fixed;
    bottom: 24px;
    right: 24px;
    width: 450px; /* Default width without sidebar */
    max-width: calc(100vw - 48px);
    height: 650px;
    max-height: calc(100vh - 48px);
    background: white;
    border-radius: 16px;
    box-shadow: 0 12px 48px rgba(0, 0, 0, 0.15);
    z-index: 1060;
    display: none;
    flex-direction: row;
    overflow: hidden;
    transition: width 0.3s ease;
}

.ai-chatbot-window.active {
    display: flex !important;
}

.ai-chatbot-window.sidebar-open {
    width: 710px; /* 450px + 260px sidebar */
}

/* ==================== Conversations Sidebar ==================== */
.conversations-sidebar {
    width: 0 !important;
    min-width: 0 !important;
    background: #f9fafb !important;
    border-right: 1px solid #e5e7eb !important;
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden !important;
    transition: width 0.3s ease, min-width 0.3s ease !important;
}

.conversations-sidebar.active {
    width: 260px !important;
    min-width: 260px !important;
}

.conversations-header {
    padding: 16px !important;
    border-bottom: 1px solid #e5e7eb !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    background: white !important;
}

.conversations-header h6 {
    font-size: 14px !important;
    color: #1f2937 !important;
    margin: 0 !important;
    font-weight: 700 !important;
}

.conversations-body {
    flex: 1 !important;
    overflow-y: auto !important;
    padding: 12px !important;
}

.conversations-body::-webkit-scrollbar {
    width: 4px !important;
}

.conversations-body::-webkit-scrollbar-thumb {
    background: #d1d5db !important;
    border-radius: 2px !important;
}

/* Ensure all children render correctly */
.conversations-body > * {
    box-sizing: border-box !important;
}

.new-chat-btn {
    font-size: 13px !important;
    padding: 8px 12px !important;
    border-radius: 8px !important;
    font-weight: 600 !important;
}

/* Conversation Item */
.conversation-item {
    background: white !important;
    border: 2px solid #e5e7eb !important;
    border-radius: 8px !important;
    padding: 10px 12px !important;
    margin-bottom: 8px !important;
    cursor: pointer !important;
    transition: all 0.2s !important;
    position: relative !important;
    display: block !important;
}

.conversation-item:hover {
    background: #f3f4f6 !important;
    border-color: #3b82f6 !important;
}

.conversation-item.active {
    background: #eff6ff !important;
    border-color: #3b82f6 !important;
    border-width: 2px !important;
}

.conversation-title {
    font-size: 13px !important;
    font-weight: 500 !important;
    color: #1f2937 !important;
    margin-bottom: 4px !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
    padding-right: 30px !important; /* Space for delete button */
    line-height: 1.4 !important;
}

.conversation-date {
    font-size: 11px !important;
    color: #6b7280 !important;
    line-height: 1.3 !important;
}

.conversation-delete {
    position: absolute !important;
    top: 8px !important;
    right: 8px !important;
    background: white !important;
    border: 1px solid #e5e7eb !important;
    color: #6b7280 !important;
    width: 20px !important;
    height: 20px !important;
    border-radius: 4px !important;
    display: none !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    font-size: 10px !important;
    z-index: 1 !important;
}

.conversation-item:hover .conversation-delete {
    display: flex !important;
}

.conversation-delete:hover {
    background: #fee2e2 !important;
    border-color: #ef4444 !important;
    color: #ef4444 !important;
}

/* Date Group Header */
.conversation-date-group {
    font-size: 11px !important;
    font-weight: 600 !important;
    color: #6b7280 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    margin: 16px 0 8px 0 !important;
    padding-left: 4px !important;
    line-height: 1.4 !important;
}

.conversation-date-group:first-child {
    margin-top: 0 !important;
}

/* ==================== Main Chat Container ==================== */
.chat-main-container {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    min-width: 0; /* Allow flex shrinking */
}

/* ==================== Header ==================== */
.chatbot-header {
    background: white;
    color: #1f2937;
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 16px 16px 0 0;
    border-bottom: 2px solid #3b82f6;
    flex-shrink: 0;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    font-size: 16px;
}

.ai-logo {
    background: #3b82f6;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
    letter-spacing: 0.5px;
}

.header-title {
    color: #1f2937;
    font-weight: 600;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 8px;
}

.system-mode-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    font-size: 11px;
    font-weight: 600;
    line-height: 1;
    padding: 5px 12px;
    border-radius: 12px;
    letter-spacing: 0.3px;
    white-space: nowrap;
}

.model-selector {
    background: #f3f4f6;
    color: #1f2937;
    border: 1px solid #e5e7eb;
    font-size: 12px;
    padding: 4px 10px;
    border-radius: 6px;
    cursor: pointer;
    max-width: 180px;
    font-weight: 500;
}

.model-selector option {
    background: #fff;
    color: #1f2937;
}

.btn-icon-header, .btn-icon-sidebar {
    background: transparent;
    border: none;
    padding: 6px 8px;
    cursor: pointer;
    color: #6b7280;
    transition: all 0.2s;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-icon-header:hover, .btn-icon-sidebar:hover {
    color: #3b82f6;
    background: #f3f4f6;
}

.btn-icon-header i, .btn-icon-sidebar i {
    font-size: 16px;
}

/* ==================== Body (Messages) ==================== */
.chatbot-body {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    background: #fafafa;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.chatbot-body::-webkit-scrollbar {
    width: 6px;
}

.chatbot-body::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
}

.chatbot-body::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* Message Group */
.message-group {
    display: flex;
    gap: 10px;
    align-items: flex-start;
}

.message-group.user-message {
    flex-direction: row-reverse;
}

.message-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #3b82f6;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
    flex-shrink: 0;
    letter-spacing: 0.5px;
}

.user-message .message-avatar {
    background: #6b7280;
    font-size: 14px;
}

.message-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
    max-width: 75%;
}

.message-bubble {
    padding: 12px 16px;
    border-radius: 16px;
    font-size: 14px;
    line-height: 1.5;
    word-wrap: break-word;
    white-space: pre-wrap;
}

.ai-message .message-bubble {
    background: white;
    color: #1f2937;
    border-bottom-left-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    border: 1px solid #f3f4f6;
}

.user-message .message-bubble {
    background: #3b82f6;
    color: white;
    border-bottom-right-radius: 4px;
    box-shadow: 0 1px 3px rgba(59, 130, 246, 0.3);
}

.message-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 0 4px;
}

.message-time {
    font-size: 11px;
    color: #999;
}

.btn-copy {
    background: none;
    border: none;
    color: #999;
    cursor: pointer;
    padding: 2px 4px;
    font-size: 12px;
    opacity: 0.6;
    transition: opacity 0.2s;
}

.btn-copy:hover {
    opacity: 1;
}

/* Message Attachments (Multiple) */
.message-attachments {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 8px;
}

.message-attachment-item {
    background: rgba(0, 0, 0, 0.05);
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.message-attachment-item i {
    font-size: 14px;
}

/* Typing Indicator */
.typing-indicator {
    display: flex;
    gap: 10px;
    align-items: center;
}

.typing-dots {
    display: flex;
    gap: 4px;
    padding: 12px 16px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    border: 1px solid #f3f4f6;
}

.typing-dots span {
    width: 8px;
    height: 8px;
    background: #3b82f6;
    border-radius: 50%;
    animation: typing 1.4s infinite;
}

.typing-dots span:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-dots span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% {
        transform: translateY(0);
    }
    30% {
        transform: translateY(-10px);
    }
}

/* Quick Actions */
.quick-actions-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 8px;
}

.quick-action-btn {
    background: white;
    border: 1px solid #e5e7eb;
    color: #1f2937;
    padding: 8px 14px;
    border-radius: 20px;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
    font-weight: 500;
}

.quick-action-btn:hover {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

/* ==================== Footer (Input Area) ==================== */
.chatbot-footer {
    border-top: 1px solid #e5e7eb;
    background: white;
    padding: 12px 16px;
    flex-shrink: 0;
}

/* Multiple Attachments Preview */
.attachments-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 8px;
    padding: 8px;
    background: #f9fafb;
    border-radius: 8px;
}

.attachment-preview-item {
    position: relative;
    width: 60px;
    height: 60px;
    border-radius: 6px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    background: white;
}

.attachment-preview-item.file-preview {
    display: flex;
    align-items: center;
    justify-content: center;
    background: #eff6ff;
}

.attachment-preview-item.file-preview i {
    font-size: 24px;
    color: #3b82f6;
}

.attachment-preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    cursor: pointer;
}

.attachment-preview-item img:hover {
    opacity: 0.8;
}

.attachment-remove-btn {
    position: absolute;
    top: 2px;
    right: 2px;
    width: 18px;
    height: 18px;
    background: rgba(239, 68, 68, 0.95);
    color: white;
    border: none;
    border-radius: 50%;
    font-size: 10px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0.9;
}

.attachment-remove-btn:hover {
    opacity: 1;
    background: #dc2626;
}

.input-row {
    display: flex;
    align-items: flex-end;
    gap: 8px;
}

.btn-attach {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    flex-shrink: 0;
    color: #6b7280;
}

.btn-attach:hover {
    background: #f3f4f6;
    color: #3b82f6;
}

.chat-input {
    flex: 1;
    min-height: 40px;
    max-height: 120px;
    resize: none;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 10px 12px;
    font-size: 14px;
    font-family: inherit;
    color: #1f2937;
}

.chat-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.btn-send {
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 8px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s;
    flex-shrink: 0;
}

.btn-send:hover:not(:disabled) {
    background: #2563eb;
}

.btn-send:disabled {
    background: #ccc;
    cursor: not-allowed;
}

.footer-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 8px;
    gap: 8px;
}

#conversationInfo {
    flex: 1;
    min-width: 0; /* Allow text to shrink */
    overflow: hidden;
}

#currentConversationTitle {
    display: inline-block;
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    vertical-align: bottom;
}

.char-count {
    font-size: 12px;
    color: #999;
    flex-shrink: 0;
}

/* ==================== Image Lightbox ==================== */
.image-lightbox {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    z-index: 2000;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.lightbox-close {
    position: absolute;
    top: 20px;
    right: 30px;
    color: white;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
}

.lightbox-content {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
}

/* ==================== Custom Confirmation Dialog ==================== */
.confirm-dialog-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 2100;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.2s ease-out;
}

.confirm-dialog-box {
    background: white;
    border-radius: 16px;
    padding: 24px;
    max-width: 400px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.3s ease-out;
}

.confirm-dialog-icon {
    width: 60px;
    height: 60px;
    margin: 0 auto 16px;
    background: #fef3c7;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.confirm-dialog-icon i {
    font-size: 28px;
    color: #f59e0b;
}

.confirm-dialog-title {
    font-size: 20px;
    font-weight: 700;
    color: #1f2937;
    text-align: center;
    margin-bottom: 8px;
}

.confirm-dialog-message {
    font-size: 14px;
    color: #6b7280;
    text-align: center;
    margin-bottom: 24px;
    line-height: 1.5;
}

.confirm-dialog-buttons {
    display: flex;
    gap: 12px;
}

.confirm-dialog-btn {
    flex: 1;
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.confirm-dialog-cancel {
    background: #f3f4f6;
    color: #6b7280;
}

.confirm-dialog-cancel:hover {
    background: #e5e7eb;
}

.confirm-dialog-confirm {
    background: #ef4444;
    color: white;
}

.confirm-dialog-confirm:hover {
    background: #dc2626;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideUp {
    from {
        transform: translateY(30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* ==================== Mobile Responsive ==================== */
@media (max-width: 768px) {
    /* Mobile FAB: circular icon-only, stacked with scroll-to-top at right: 24px */
    /* bottom: 82px is kept exactly as desktop — DO NOT change */
    .ai-chatbot-fab {
        right: 24px;
        width: 48px;
        height: 48px;
        padding: 0;
        border-radius: 50%;
        justify-content: center;
    }

    .fab-text {
        display: none !important;
    }

    /* Mobile: shorter slide distance */
    .ai-chatbot-fab.fab-hidden-mobile {
        transform: translateX(80px);
    }

    .ai-chatbot-window {
        width: 100% !important;
        max-width: 100%;
        height: 100%;
        max-height: 100%;
        bottom: 0;
        right: 0;
        top: 0;
        left: 0;
        border-radius: 0;
    }

    .ai-chatbot-window.sidebar-open {
        width: 100% !important;
    }

    .chatbot-header {
        border-radius: 0;
    }

    .model-selector {
        max-width: 100px;
        font-size: 11px;
    }

    .conversations-sidebar.active {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10;
        background: white;
    }
}

/* ==================== Animations ==================== */
@keyframes slideIn {
    from {
        transform: translateY(100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.ai-chatbot-window.active {
    animation: slideIn 0.3s ease-out;
}
</style>

{{-- AI Chatbot JavaScript (inline - always fresh, no caching issues) --}}
<script>
/**
 * AI Chatbot General Assistant - Frontend Logic with Conversation History
 * SIPADUHOK - Claude/ChatGPT Style Interface
 */

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

// ╔══════════════════════════════════════════════════════════════════╗
// ║  SIPADUHOK SYSTEM CHATBOT — Knowledge Base & Keyword Engine    ║
// ║  Default mode: cek KB dulu → jika tidak ada → fallback ke LLM  ║
// ╚══════════════════════════════════════════════════════════════════╝

// Current user role (injected from Blade)
const USER_ROLE = '{{ auth()->user()->role ?? "guest" }}';
const IS_LLM_MODE_ENABLED = {{ isLlmModeEnabled() ? 'true' : 'false' }};

const SYSTEM_KB = [
    // ═══ DASHBOARD ═══
    { kw:['dashboard','beranda','halaman utama','tampilan awal','home','statistik'], title:'🏠 Dashboard', text:'Dashboard adalah halaman utama setelah login yang menampilkan ringkasan statistik sistem.', steps:['Login ke sistem dengan akun Anda','Sistem otomatis mengarahkan ke Dashboard','Lihat statistik: total siswa, guru, kelas, dan keuangan'], link:'/admin/dashboard', linkText:'Buka Dashboard', note:'Setiap role memiliki tampilan dashboard yang berbeda sesuai kebutuhannya.' },
    // ═══ LANDING PAGE ═══
    { kw:['landing page','halaman website','konten website','edit website','kelola website','halaman depan'], title:'🌐 Landing Page', text:'Kelola konten halaman publik website sekolah (halaman depan, profil, program, dll).', steps:['Sidebar → "Landing Page"','Pilih halaman yang ingin diedit','Ubah konten teks, gambar, atau informasi','Klik "Simpan"'], link:'/admin/landing-pages', linkText:'Kelola Landing Page', note:'Menu ini hanya tersedia untuk Admin.', related:['berita','pengumuman','flyer'] },
    // ═══ MANAJEMEN USER ═══
    { kw:['user','pengguna','akun','manajemen user','kelola user','daftar akun','semua user'], title:'👥 Manajemen User', text:'Kelola seluruh akun pengguna sistem: Tenaga Pendidik, Siswa, dan Wali Murid.', steps:['Sidebar → "Manajemen User"','Pilih kategori: Tenaga Pendidik, Siswa, atau Wali Murid','Tambah, edit, hapus, atau import data'], link:'/admin/users/tenaga-pendidik', linkText:'Buka Manajemen User', related:['tenaga pendidik','siswa','wali murid'] },
    { kw:['tenaga pendidik','guru','data guru','tambah guru','import guru','daftar guru','staff','pegawai'], title:'👨‍🏫 Data Tenaga Pendidik', text:'Kelola data guru dan tenaga kependidikan.', steps:['Sidebar → "Manajemen User" → "Tenaga Pendidik"','Klik "Tambah" untuk input manual satu per satu','Atau klik "Import" untuk upload data via file Excel','Download template Excel tersedia di tombol "Template"'], link:'/admin/users/tenaga-pendidik', linkText:'Kelola Tenaga Pendidik', note:'Data mencakup: nama, NIP/NIK, jabatan, role sistem, dan kontak.', related:['wali kelas','guru pengajar','import data'] },
    { kw:['siswa','murid','data siswa','tambah siswa','import siswa','daftar siswa','pelajar','peserta didik','update siswa','edit siswa','ubah siswa','hapus siswa','kelola siswa'], title:'👨‍🎓 Data Siswa', text:'Kelola data seluruh siswa yang terdaftar di sekolah.', steps:['Sidebar → "Manajemen User" → "Siswa"','Klik "Tambah Siswa" untuk input manual','Atau klik "Import" untuk bulk upload via Excel','Gunakan "Template" untuk download format Excel yang benar'], link:'/admin/users/siswa', linkText:'Kelola Data Siswa', note:'Setelah siswa ditambahkan, assign ke kelas melalui menu "Manajemen Siswa".', related:['manajemen siswa','kelas','wali murid'] },
    { kw:['wali murid','orang tua','parent','ortu','data orang tua','tambah orang tua'], title:'👪 Data Wali Murid / Orang Tua', text:'Kelola data wali murid/orang tua siswa.', steps:['Sidebar → "Manajemen User" → "Wali Murid"','Klik "Tambah" untuk input manual','Hubungkan orang tua dengan siswa melalui menu Manajemen Siswa'], link:'/admin/users/orang-tua', linkText:'Kelola Wali Murid', note:'Satu orang tua bisa terhubung ke beberapa siswa (anak).', related:['siswa','manajemen siswa'] },
    // ═══ TIKET PEMULIHAN AKUN ═══
    { kw:['tiket pemulihan','recovery','lupa password','reset password','akun terkunci','pemulihan akun','password baru','tidak bisa login','lupa','password','reset','ganti password'], title:'🔓 Tiket Pemulihan Akun', text:'Kelola permintaan reset password dari pengguna yang lupa kata sandi.', steps:['Sidebar → "Tiket Pemulihan Akun"','Lihat daftar tiket masuk (status: pending, gagal)','Klik "Proses" untuk kirim link reset via WhatsApp/Email','User menerima link → klik → buat password baru'], link:'/admin/recovery-tickets', linkText:'Kelola Tiket Pemulihan', note:'Link reset password berlaku 24 jam. Badge merah menunjukkan jumlah tiket pending.', related:['login','ganti password','pengaturan akun'] },
    // ═══ PENGATURAN LMS ═══
    { kw:['pengaturan lms','setting lms','konfigurasi lms','lms setting','atur lms'], title:'⚙️ Pengaturan LMS', text:'Konfigurasi fitur Learning Management System (LMS) untuk pembelajaran online.', steps:['Sidebar → "Pengaturan LMS"','Atur parameter LMS sesuai kebutuhan','Klik "Simpan"'], link:'/admin/lms-settings', linkText:'Buka Pengaturan LMS', related:['lms','materi','tugas','ujian'] },
    // ═══ PENGATURAN AI ═══
    { kw:['pengaturan ai','ai setting','groq','api key','konfigurasi ai','setting chatbot','model ai'], title:'🤖 Pengaturan AI', text:'Konfigurasi API key dan model AI untuk fitur chatbot LLM.', steps:['Sidebar → "Pengaturan AI"','Masukkan Groq API Key','Pilih model AI (Llama, Mixtral, dll.)','Atur role yang bisa akses chatbot','Klik "Test Koneksi" → Simpan'], link:'/admin/ai-settings', linkText:'Buka Pengaturan AI', note:'API Key bisa didapat gratis di console.groq.com.', related:['chatbot','lms'] },
    // ═══ GOOGLE SHEETS ═══
    { kw:['google sheets','sync','sinkronisasi','spreadsheet','ekspor sheet','push data','pull data'], title:'📊 Google Sheets Sync', text:'Sinkronkan data sistem ke Google Sheets untuk backup & pelaporan.', steps:['Sidebar → "Google Sheets Sync"','Setup: upload Google API credential (JSON)','Test Connection','Pilih modul data → "Push" untuk kirim ke Sheets','"Pull" untuk tarik data dari Sheets ke sistem'], link:'/admin/google-sheets', linkText:'Buka Google Sheets', note:'Diperlukan Google Cloud Project dengan Sheets API aktif.', related:['cetak laporan','export data'] },
    // ═══ TAHUN AJARAN ═══
    { kw:['tahun ajaran','semester','periode','ganti semester','aktifkan tahun ajaran','buat tahun ajaran','tahun pelajaran'], title:'🗓️ Tahun Ajaran', text:'Kelola periode tahun ajaran dan semester.', steps:['Sidebar → "Tahun Ajaran"','Klik "Tambah" untuk buat tahun ajaran baru','Isi nama (contoh: 2024/2025), semester, tanggal mulai-selesai','Klik "Aktifkan" pada tahun ajaran yang ingin digunakan'], link:'/admin/tahun-ajaran', linkText:'Kelola Tahun Ajaran', note:'⚠️ Hanya SATU tahun ajaran yang bisa aktif bersamaan.', related:['kelas','jadwal pelajaran','kenaikan kelas'] },
    // ═══ CABANG ═══
    { kw:['cabang','manajemen cabang','unit sekolah','lokasi sekolah','branch','tambah cabang'], title:'🏢 Manajemen Cabang', text:'Kelola data cabang/unit sekolah.', steps:['Sidebar → "Manajemen Cabang"','Klik "Tambah" untuk buat cabang baru','Isi nama cabang, alamat, dan informasi','Toggle status aktif/nonaktif'], link:'/admin/cabang', linkText:'Kelola Cabang', related:['kelas','guru pengajar'] },
    // ═══ DATA KELAS ═══
    { kw:['kelas','data kelas','tambah kelas','buat kelas','daftar kelas','kelola kelas','ruang kelas'], title:'🏫 Data Kelas', text:'Kelola data kelas, termasuk tambah/edit kelas dan assign siswa.', steps:['Sidebar → "Data Kelas"','Klik "Tambah" untuk buat kelas baru','Isi nama kelas, tingkat, cabang','Klik kelas → "Kelola Siswa" untuk menambah/hapus siswa','Klik "Assign Wali" untuk menentukan wali kelas'], link:'/admin/kelas', linkText:'Kelola Data Kelas', note:'Bisa import via Excel dan salin kelas dari tahun ajaran sebelumnya.', related:['wali kelas','manajemen siswa','jadwal pelajaran'] },
    // ═══ WALI KELAS ═══
    { kw:['wali kelas','assign wali','ganti wali kelas','homeroom','data wali kelas','tentukan wali'], title:'👩‍💼 Data Wali Kelas', text:'Lihat dan assign guru sebagai wali kelas.', steps:['Sidebar → "Data Wali Kelas"','Lihat daftar kelas dan wali kelasnya','Klik kelas yang belum ada wali → "Assign Wali Kelas"','Pilih guru dari dropdown → Simpan'], link:'/admin/wali-kelas', linkText:'Kelola Wali Kelas', note:'Satu guru bisa menjadi wali lebih dari satu kelas.', related:['kelas','guru pengajar','presensi'] },
    // ═══ GURU PENGAJAR ═══
    { kw:['guru pengajar','pengajar','data guru pengajar','guru mapel','guru mata pelajaran'], title:'👨‍🏫 Data Guru Pengajar', text:'Lihat rekap guru pengajar dan mata pelajaran yang diajar. Data otomatis dari jadwal pelajaran.', steps:['Sidebar → "Data Guru Pengajar"','Lihat daftar guru dan kelas/mapel yang diajar','Klik nama guru untuk detail','Klik "Rebuild" jika data tidak sinkron'], link:'/admin/guru-pengajar', linkText:'Lihat Guru Pengajar', note:'Read-only (derived dari jadwal pelajaran). Untuk mengubah, edit jadwal.', related:['jadwal pelajaran','mata pelajaran','wali kelas'] },
    // ═══ MATA PELAJARAN ═══
    { kw:['mata pelajaran','mapel','tambah mapel','data mapel','daftar mapel','subject','pelajaran'], title:'📖 Mata Pelajaran', text:'Kelola data mata pelajaran yang tersedia di sekolah.', steps:['Sidebar → "Mata Pelajaran"','Klik "Tambah" untuk input manual','Isi kode mapel, nama, kelompok, dan KKM','Atau "Import" untuk bulk upload via Excel'], link:'/admin/mata-pelajaran', linkText:'Kelola Mata Pelajaran', note:'Kode mapel bisa auto-generate via "Suggest Kode". Bisa cetak daftar mapel.', related:['jadwal pelajaran','guru pengajar','nilai','lms'] },
    // ═══ JADWAL PELAJARAN ═══
    { kw:['jadwal pelajaran','jadwal','schedule','jam pelajaran','atur jadwal','buat jadwal','jadwal mengajar','jadwal kelas'], title:'📅 Jadwal Pelajaran', text:'Kelola jadwal pelajaran untuk setiap kelas.', steps:['Sidebar → "Jadwal Pelajaran"','Klik "Tambah" untuk buat jadwal baru per slot','Pilih kelas, hari, jam, mata pelajaran, dan guru','Atau "Import" untuk bulk upload via Excel','Lihat jadwal per kelas dengan klik nama kelas'], link:'/admin/jadwal-pelajaran', linkText:'Kelola Jadwal', note:'Fitur: cetak PDF, export Excel, duplikat jadwal, ganti guru massal, atur jam istirahat.', related:['mata pelajaran','guru pengajar','kelas','istirahat'] },
    // ═══ MANAJEMEN SISWA ═══
    { kw:['manajemen siswa','kelola siswa','assign siswa ke kelas','pindah kelas','hubungkan orang tua','kartu siswa'], title:'🎓 Manajemen Siswa', text:'Kelola penempatan siswa: assign ke kelas, hubungkan orang tua, cetak kartu.', steps:['Sidebar → "Manajemen Siswa"','Lihat siswa per kelas','Klik siswa → assign ke kelas','Hubungkan siswa dengan akun orang tua','Cetak kartu siswa'], link:'/admin/manajemen-siswa', linkText:'Kelola Manajemen Siswa', note:'Bulk assign tersedia. Juga bisa lihat detail lengkap siswa.', related:['kelas','siswa','wali murid'] },
    // ═══ TAGIHAN ═══
    { kw:['tagihan','biaya','spp','uang sekolah','buat tagihan','kelola tagihan','generate spp','tagihan siswa','tagiihan','tagian'], title:'💰 Tagihan', text:'Kelola tagihan pembayaran siswa: SPP bulanan, pendaftaran, dan tagihan custom.', steps:['Sidebar → "Tagihan" (bagian Keuangan)','Lihat daftar tagihan per siswa','"Bulk Create" untuk tagihan massal per kelas','"Generate SPP" untuk auto-generate SPP bulanan','"Custom" untuk tagihan khusus individual'], link:'/admin/keuangan/tagihan', linkText:'Kelola Tagihan', note:'Fitur: import Excel, duplikat tagihan, cetak laporan, reset tagihan.', related:['pembayaran','laporan keuangan','validasi akses'] },
    // ═══ PEMBAYARAN ═══
    { kw:['pembayaran','bayar','payment','transaksi','validasi pembayaran','konfirmasi pembayaran','midtrans','transfer','bayar spp','kwitansi','bukti bayar','pembayran'], title:'💳 Pembayaran', text:'Kelola dan validasi pembayaran masuk dari siswa/orang tua.', steps:['Sidebar → "Pembayaran" (bagian Keuangan)','Lihat daftar pembayaran masuk','Klik detail → Validasi (setujui/tolak)','Input pembayaran manual jika perlu','Cetak kwitansi untuk yang sudah divalidasi'], link:'/admin/keuangan/pembayaran', linkText:'Kelola Pembayaran', note:'Pembayaran online via Midtrans (QRIS, Transfer, e-wallet) otomatis dicatat. Manual harus diinput Admin/Bendahara.', related:['tagihan','laporan keuangan','kwitansi'] },
    // ═══ LAPORAN KEUANGAN ═══
    { kw:['laporan keuangan','rekap keuangan','rekap pembayaran','laporan pembayaran','tunggakan','belum lunas','belum bayar','rekap tagihan'], title:'📊 Laporan Keuangan', text:'Lihat dan cetak laporan keuangan: rekap pembayaran, tagihan, tunggakan.', steps:['Sidebar → "Laporan Keuangan" (bagian Keuangan)','Pilih jenis: Pembayaran, Rekap Tagihan, atau Belum Lunas','Filter berdasarkan periode, kelas, atau jenis','Klik "Cetak" untuk export PDF'], link:'/admin/keuangan/laporan', linkText:'Buka Laporan Keuangan', related:['tagihan','pembayaran','cetak laporan'] },
    // ═══ CONFIG PEMBAYARAN ═══
    { kw:['config pembayaran','info pembayaran','rekening bank','api midtrans','konfigurasi midtrans','payment gateway','setting pembayaran'], title:'⚙️ Config Pembayaran', text:'Konfigurasi metode pembayaran: API Midtrans dan rekening bank.', steps:['Sidebar → "Config Pembayaran" (bagian Keuangan)','Masukkan Midtrans Server Key dan Client Key','Isi data rekening bank sekolah','Klik "Simpan"'], link:'/admin/keuangan/info-pembayaran', linkText:'Buka Config Pembayaran', note:'Midtrans mendukung: QRIS, Transfer Bank, GoPay, OVO, Dana.', related:['pembayaran','tagihan'] },
    // ═══ VALIDASI DISPENSASI ═══
    { kw:['validasi dispensasi','dispensasi','keringanan','dispens'], title:'🎫 Validasi Dispensasi', text:'Kelola dispensasi pembayaran untuk siswa dengan keringanan biaya.', steps:['Sidebar → "Validasi Dispensasi" (bagian Keuangan)','Lihat daftar siswa mengajukan dispensasi','Approve atau reject permohonan','Lihat history dispensasi'], link:'/admin/keuangan/promotion/validation', linkText:'Kelola Dispensasi', related:['tagihan','pembayaran','kenaikan kelas'] },
    // ═══ VALIDASI AKSES ═══
    { kw:['validasi akses','akses ujian','akses rapor','izin ujian','blokir ujian','unlock ujian','batas pembayaran'], title:'🔑 Validasi Akses', text:'Kontrol akses siswa ke ujian dan rapor berdasarkan status pembayaran.', steps:['Sidebar → "Validasi Akses" (bagian Keuangan)','Lihat status validasi per siswa','Klik "Validasi Ujian" / "Validasi Rapor"','Bulk validasi untuk proses massal per kelas','Atur batas minimum pembayaran untuk akses otomatis'], link:'/admin/keuangan/validasi-akses', linkText:'Kelola Validasi Akses', note:'Dispensasi bisa diajukan untuk siswa kondisi khusus. Reset validasi tersedia.', related:['ujian','rapor','pembayaran','dispensasi'] },
    // ═══ PENGATURAN KKM ═══
    { kw:['kkm','kriteria ketuntasan','nilai minimum','batas lulus','setting kkm','pengaturan kkm'], title:'📏 Pengaturan KKM', text:'Atur Kriteria Ketuntasan Minimal (KKM) per mata pelajaran.', steps:['Sidebar → "Pengaturan KKM" (bagian Kenaikan Kelas)','Lihat daftar mapel dan KKM-nya','Edit nilai KKM per mapel','Klik "Simpan"'], link:'/admin/akademik/promotion/kkm', linkText:'Atur KKM', note:'KKM digunakan sebagai acuan proses kenaikan kelas otomatis.', related:['kenaikan kelas','mata pelajaran','nilai'] },
    // ═══ PENGATURAN KENAIKAN ═══
    { kw:['pengaturan kenaikan','setting promosi','aturan naik kelas','syarat naik kelas','konfigurasi kenaikan'], title:'⚙️ Pengaturan Kenaikan Kelas', text:'Konfigurasi aturan dan syarat kenaikan kelas.', steps:['Sidebar → "Pengaturan Kenaikan" (bagian Kenaikan Kelas)','Atur parameter: persentase kehadiran, KKM, dll.','Simpan pengaturan'], link:'/admin/akademik/promotion/settings', linkText:'Atur Pengaturan Kenaikan', related:['kkm','proses kenaikan','tahun ajaran'] },
    // ═══ PROSES & REKAP KENAIKAN ═══
    { kw:['naik kelas','kenaikan kelas','proses kenaikan','rekap kenaikan','promosi siswa','eksekusi kenaikan','tinggal kelas'], title:'🎓 Proses & Rekap Kenaikan Kelas', text:'Eksekusi dan monitor proses kenaikan kelas siswa.', steps:['Sidebar → "Proses & Rekap" (bagian Kenaikan Kelas)','Sistem cek ketuntasan berdasarkan KKM dan kehadiran','Lihat rekap: lulus/tidak lulus','"Eksekusi Promosi" untuk proses massal','Ketua PKBM melakukan persetujuan akhir'], link:'/admin/akademik/promotion/report', linkText:'Buka Proses Kenaikan', note:'Fitur: rollback, promote manual, jadwalkan eksekusi.', related:['kkm','pengaturan kenaikan','rapor','nilai'] },
    // ═══ KALENDER AKADEMIK ═══
    { kw:['kalender akademik','kalender sekolah','jadwal libur','event sekolah','agenda','hari libur','kegiatan sekolah'], title:'📆 Kalender Akademik', text:'Kelola kalender akademik: event, libur, dan kegiatan.', steps:['Sidebar → "Kalender Akademik" (bagian Manajemen Akademik)','Lihat kalender tahunan atau bulanan','Klik "Tambah" untuk event baru','Isi judul, tanggal, kategori, deskripsi','Toggle visibility untuk publik/internal'], link:'/admin/akademik/kalender', linkText:'Kelola Kalender', note:'Event publik terlihat di LMS siswa. Bisa cetak kalender.', related:['pengumuman','jadwal pelajaran'] },
    // ═══ PENGUMUMAN ═══
    { kw:['pengumuman','announcement','buat pengumuman','info sekolah','pemberitahuan'], title:'📢 Pengumuman', text:'Buat dan kelola pengumuman sekolah.', steps:['Sidebar → "Pengumuman" (bagian Manajemen Akademik)','Klik "Tambah Pengumuman"','Isi judul, konten, dan target audience','Simpan dan publikasikan'], link:'/admin/akademik/pengumuman', linkText:'Kelola Pengumuman', related:['berita','kalender akademik','notifikasi'] },
    // ═══ BERITA ═══
    { kw:['berita','news','artikel','buat berita','kelola berita','post'], title:'📰 Berita', text:'Kelola berita/artikel sekolah yang ditampilkan di website publik.', steps:['Sidebar → "Berita" (bagian Manajemen Akademik)','Klik "Tambah Berita"','Isi judul, konten, gambar cover','Toggle "Featured" untuk berita unggulan','Simpan'], link:'/admin/akademik/berita', linkText:'Kelola Berita', related:['pengumuman','flyer','landing page'] },
    // ═══ FLYER ═══
    { kw:['flyer','poster','banner','pamflet','brosur'], title:'🖼️ Flyer', text:'Kelola flyer/poster digital sekolah.', steps:['Sidebar → "Flyer" (bagian Manajemen Akademik)','Klik "Tambah Flyer"','Upload gambar flyer, isi judul dan deskripsi','Simpan'], link:'/admin/akademik/flyer', linkText:'Kelola Flyer', related:['berita','pengumuman'] },
    // ═══ MONITORING ═══
    { kw:['monitoring','pantau','statistik pengguna','analitik','insight'], title:'📈 Monitoring', text:'Pantau aktivitas dan statistik pengguna sistem.', steps:['Sidebar → "Monitoring"','Pilih: Pengguna, Wali Kelas, Guru Pengajar, atau Siswa','Lihat statistik aktivitas dan rekap data'], link:'/admin/monitoring/pengguna', linkText:'Buka Monitoring', related:['laporan','catatan'] },
    // ═══ LAPORAN ═══
    { kw:['laporan','cetak laporan','print laporan','export laporan','rekap data','report','cetak data','cetak','print','export','print data'], title:'📄 Laporan & Cetak', text:'Cetak dan export berbagai laporan data sekolah.', steps:['Sidebar → "Laporan" (bagian Monitoring & Analitik)','Pilih jenis: Siswa, Tenaga Pendidik, Kelas, Wali Kelas, Guru Pengajar, Rekap','Klik untuk generate dan cetak/export'], link:'/admin/laporan', linkText:'Buka Laporan', note:'Laporan keuangan terpisah di menu Keuangan → Laporan Keuangan.', related:['laporan keuangan','google sheets','cetak rapor'] },
    // ═══ CATATAN ═══
    { kw:['catatan','notes','teguran','memo','catatan admin'], title:'📝 Catatan', text:'Buat dan kelola catatan/memo internal.', steps:['Sidebar → "Catatan"','Klik "Buat Catatan Baru"','Isi judul dan isi catatan','Simpan'], link:'/admin/catatan', linkText:'Kelola Catatan', related:['monitoring','laporan'] },
    // ═══ PROFIL & AKUN ═══
    { kw:['profil','edit profil','foto profil','data pribadi','profil saya'], title:'👤 Profil Saya', text:'Lihat dan edit informasi profil Anda.', steps:['Klik foto/nama di pojok kanan atas navbar','Pilih "Profil Saya"','Edit data atau upload foto','Klik "Simpan"'], link:'/profile', linkText:'Buka Profil', related:['pengaturan akun','ganti password'] },
    { kw:['pengaturan akun','setting akun','ganti password','ubah password','keamanan akun'], title:'⚙️ Pengaturan Akun', text:'Ubah password dan pengaturan keamanan akun.', steps:['Klik foto/nama di pojok kanan atas','Pilih "Pengaturan"','Tab "Keamanan" → isi password lama dan baru','Klik "Simpan Perubahan"'], link:'/account/settings', linkText:'Buka Pengaturan Akun', related:['profil','login','lupa password'] },
    // ═══ LOGIN / LOGOUT ═══
    { kw:['login','masuk','sign in','cara login','akses sistem','gagal login','lupa password','lupa','password'], title:'🔑 Login ke Sistem', text:'Cara masuk ke sistem SIPADUHOK.', steps:['Buka halaman /login','Masukkan Username dan Password','Selesaikan verifikasi CAPTCHA','Klik "Masuk"'], link:'/login', linkText:'Halaman Login', note:'Jika gagal login, pastikan username dan password benar. Hubungi Admin jika akun terkunci.', related:['lupa password','profil'] },
    { kw:['logout','keluar','sign out','cara keluar'], title:'🚪 Logout / Keluar', text:'Cara keluar dari sistem.', steps:['Klik foto/nama di pojok kanan atas','Pilih "Logout"','Konfirmasi "Ya, Logout"','Diarahkan ke halaman Login'] },
    // ═══ NOTIFIKASI ═══
    { kw:['notifikasi','notification','pemberitahuan','lonceng','bell','notif'], title:'🔔 Notifikasi', text:'Lihat semua notifikasi aktivitas sistem.', steps:['Klik ikon lonceng 🔔 di pojok kanan atas','Lihat notifikasi terbaru','Klik notifikasi untuk detail','"Lihat Semua" untuk halaman lengkap','"Tandai Semua Dibaca" untuk clear badge'], link:'/notifications', linkText:'Lihat Notifikasi', related:['dashboard','pengumuman'] },
    // ═══ PRESENSI ═══
    { kw:['presensi','absensi','absen','kehadiran','daftar hadir','input presensi','rekap absensi','catat kehadiran'], title:'✅ Presensi Siswa', text:'Presensi harian dilakukan oleh Wali Kelas.', steps:['Login sebagai Wali Kelas','Menu "Presensi" di sidebar','Pilih tanggal dan kelas','Isi status: Hadir / Sakit / Izin / Alpha','Klik "Simpan Presensi"'], link:'/wali/presensi', linkText:'Buka Presensi', note:'Siswa lihat di SIA → Presensi. Izin diajukan melalui Orang Tua.', related:['izin siswa','rapor','wali kelas'],
      owner:'wali_kelas', adminNote:'⚠️ Presensi hanya bisa diinput oleh Wali Kelas. Admin dapat memantau melalui menu Monitoring atau Data Wali Kelas.', adminLink:'/admin/wali-kelas', adminLinkText:'Lihat Data Wali Kelas' },
    // ═══ IZIN ═══
    { kw:['izin','ajukan izin','izin sakit','surat izin','tidak masuk','pengajuan izin'], title:'📝 Pengajuan Izin Siswa', text:'Izin tidak masuk sekolah diajukan oleh Orang Tua.', steps:['Login sebagai Orang Tua','Menu "Presensi" → pilih anak','Klik "Ajukan Izin"','Isi tanggal, jenis izin, keterangan, lampiran','Wali Kelas akan memvalidasi'], link:'/orang-tua/dashboard', linkText:'Dashboard Orang Tua', note:'⚠️ Siswa tidak bisa ajukan izin sendiri — harus melalui Orang Tua.', related:['presensi','orang tua'],
      owner:'orang_tua', adminNote:'⚠️ Pengajuan izin dilakukan oleh akun Orang Tua. Admin dapat memantau data presensi melalui Monitoring.', adminLink:'/admin/monitoring/pengguna', adminLinkText:'Buka Monitoring' },
    // ═══ RAPOR ═══
    { kw:['rapor','raport','rapot','cetak rapor','download rapor','generate rapor','hasil belajar','cetak rapor siswa'], title:'📄 Rapor Siswa', text:'Sistem rapor melalui alur validasi bertingkat.', steps:['Wali Kelas → generate rapor dan input catatan','Wali Kelas → kirim validasi ke Ketua PKBM','Ketua PKBM → validasi dan tanda tangan','Orang Tua → lihat dan minta download rapor'], link:'/wali/rapor', linkText:'Kelola Rapor', note:'Rapor hanya bisa diakses jika pembayaran divalidasi (Validasi Akses).', related:['nilai','validasi akses','wali kelas'],
      owner:'wali_kelas', adminNote:'⚠️ Cetak dan generate rapor hanya bisa dilakukan oleh Wali Kelas. Admin dapat mengelola Data Wali Kelas atau memantau melalui menu Monitoring.', adminLink:'/admin/wali-kelas', adminLinkText:'Lihat Data Wali Kelas' },
    // ═══ NILAI ═══
    { kw:['nilai','input nilai','rekap nilai','nilai siswa','grade','skor','penilaian'], title:'📝 Nilai Siswa', text:'Input dan kelola nilai per mata pelajaran.', steps:['Guru: LMS → pilih kelas & mapel → "Nilai"','Wali Kelas: sidebar → "Nilai Siswa" → pilih siswa','Input nilai tugas, UTS, UAS','Bisa import via Excel'], link:'/wali/nilai', linkText:'Input Nilai', related:['rapor','kkm','lms'],
      owner:'wali_kelas', adminNote:'⚠️ Input nilai dilakukan oleh Guru Pengajar dan Wali Kelas. Admin dapat memantau melalui menu Monitoring Guru Pengajar atau Pengaturan KKM.', adminLink:'/admin/guru-pengajar', adminLinkText:'Lihat Guru Pengajar' },
    // ═══ LMS ═══
    { kw:['lms','e-learning','belajar online','platform belajar','learning management','kelas virtual'], title:'📚 LMS (Learning Management System)', text:'Platform pembelajaran online untuk guru dan siswa.', steps:['Guru: sidebar → pilih kelas → masuk LMS','Siswa: sidebar → LMS Dashboard','Fitur: Materi, Tugas, Ujian, Forum, Meeting Virtual'], link:'/siswa/lms/dashboard', linkText:'Buka LMS', note:'Akses LMS memerlukan validasi pembayaran.', related:['materi','tugas','ujian','pengaturan lms'],
      owner:'guru_pengajar', adminNote:'⚠️ LMS diakses oleh Guru dan Siswa. Admin dapat mengatur LMS melalui menu Pengaturan LMS atau memantau aktivitas guru di Monitoring.', adminLink:'/admin/lms-settings', adminLinkText:'Pengaturan LMS' },
    // ═══ TUGAS ═══
    { kw:['tugas','assignment','kumpulkan tugas','submit tugas','deadline','buat tugas','koreksi tugas'], title:'📝 Tugas (LMS)', text:'Fitur tugas di LMS untuk buat dan kumpulkan tugas.', steps:['Guru: LMS → Kelas → Mapel → "Tugas" → "Buat"','Siswa: LMS → Mata Pelajaran → "Tugas"','Upload jawaban → "Kumpulkan"','Guru mengoreksi dan memberi nilai'], link:'/siswa/lms/tugas', linkText:'Lihat Tugas', note:'Perhatikan deadline! Setelah deadline tidak bisa kumpul.', related:['lms','ujian','nilai'],
      owner:'guru_pengajar', adminNote:'⚠️ Tugas dibuat oleh Guru dan dikerjakan Siswa melalui LMS. Admin dapat mengatur LMS di Pengaturan LMS.', adminLink:'/admin/lms-settings', adminLinkText:'Pengaturan LMS' },
    // ═══ UJIAN ═══
    { kw:['ujian','exam','tes online','mulai ujian','soal ujian','buat ujian','kuis','latihan'], title:'📋 Ujian Online (LMS)', text:'Fitur ujian dan latihan online di LMS.', steps:['Guru: LMS → "Ujian" → "Buat Ujian"','Tambah soal (manual, import, atau AI Generate)','Siswa: buka mapel → "Ujian" → "Mulai"','Jawab semua soal → "Submit"'], link:'/siswa/lms/dashboard', linkText:'Buka LMS', note:'Ujian terkunci = validasi pembayaran belum disetujui.', related:['lms','validasi akses','tugas','nilai'],
      owner:'guru_pengajar', adminNote:'⚠️ Ujian dibuat oleh Guru dan dikerjakan Siswa. Admin mengontrol akses ujian melalui Validasi Akses Pembayaran.', adminLink:'/admin/keuangan/validasi-akses', adminLinkText:'Validasi Akses' },
    // ═══ MATERI ═══
    { kw:['materi','bahan ajar','modul','download materi','baca materi','konten pelajaran'], title:'📖 Materi Pelajaran', text:'Akses dan kelola materi/bahan ajar di LMS.', steps:['Guru: LMS → "Materi" → "Tambah"','Upload file atau tulis konten','Siswa: LMS → Mata Pelajaran → "Materi"','Baca online atau download'], link:'/siswa/lms/dashboard', linkText:'Buka LMS', related:['lms','tugas','forum'],
      owner:'guru_pengajar', adminNote:'⚠️ Materi di-upload oleh Guru Pengajar di LMS. Admin dapat memantau di Monitoring Guru Pengajar.', adminLink:'/admin/monitoring/guru-pengajar', adminLinkText:'Monitoring Guru' },
    // ═══ FORUM ═══
    { kw:['forum','diskusi','tanya jawab','forum diskusi','tanya guru'], title:'💬 Forum Diskusi', text:'Forum tanya-jawab di LMS.', steps:['LMS → Mata Pelajaran → "Forum"','"Buat Diskusi Baru" untuk bertanya','Guru bisa pin, tutup, atau moderasi'], link:'/siswa/lms/dashboard', linkText:'Buka LMS', related:['lms','meeting','materi'],
      owner:'guru_pengajar', adminNote:'⚠️ Forum diskusi ada di LMS, dikelola oleh Guru dan diakses Siswa.', adminLink:'/admin/lms-settings', adminLinkText:'Pengaturan LMS' },
    // ═══ MEETING ═══
    { kw:['meeting','kelas virtual','zoom','google meet','video call','pertemuan online'], title:'🎥 Meeting / Kelas Virtual', text:'Fitur kelas virtual/pertemuan online di LMS.', steps:['Guru: LMS → "Meeting" → "Buat Meeting"','Isi judul, link Zoom/Meet, jadwal','Siswa: LMS → Mapel → "Meeting" → klik link'], link:'/siswa/lms/dashboard', linkText:'Buka LMS', related:['lms','jadwal pelajaran'],
      owner:'guru_pengajar', adminNote:'⚠️ Meeting dijadwalkan oleh Guru melalui LMS. Admin memantau jadwal di Jadwal Pelajaran.', adminLink:'/admin/jadwal-pelajaran', adminLinkText:'Lihat Jadwal' },
    // ═══ ORANG TUA ═══
    { kw:['orang tua','parent','fitur orang tua','menu orang tua'], title:'👪 Fitur Orang Tua', text:'Fitur untuk akun Orang Tua.', steps:['💳 Tagihan & Pembayaran — bayar SPP online','📄 Rapor Anak — lihat & minta download','✅ Presensi Anak — pantau kehadiran & ajukan izin'], link:'/orang-tua/dashboard', linkText:'Dashboard Orang Tua', note:'Satu akun bisa punya beberapa anak.', related:['tagihan','rapor','presensi','izin'],
      owner:'orang_tua', adminNote:'⚠️ Dashboard Orang Tua hanya diakses oleh akun Orang Tua. Admin dapat mengelola data orang tua di Manajemen User.', adminLink:'/admin/users/orang-tua', adminLinkText:'Kelola Wali Murid' },
    // ═══ IMPORT DATA ═══
    { kw:['import','import excel','upload excel','bulk upload','template excel','download template'], title:'📥 Import Data via Excel', text:'Banyak data bisa di-import massal via Excel.', steps:['Buka menu data yang ingin di-import','Klik tombol "Import"','Download "Template" Excel terlebih dahulu','Isi data sesuai format template','Upload file dan klik "Import"'], note:'Template berisi header dan contoh data. Pastikan format sesuai.', related:['siswa','tenaga pendidik','kelas','jadwal pelajaran'] },
    // ═══ ISTIRAHAT ═══
    { kw:['istirahat','jam istirahat','pengaturan istirahat','break','waktu istirahat'], title:'☕ Pengaturan Istirahat', text:'Atur jam istirahat yang muncul di jadwal pelajaran.', steps:['Admin/Wakasek → menu terkait jadwal pelajaran','Tambah, edit, atau toggle status jam istirahat'], link:'/admin/jadwal-pelajaran', linkText:'Buka Jadwal Pelajaran', related:['jadwal pelajaran'] },

    // ╔══════════════════════════════════════════════════════════╗
    // ║  SISWA-SPECIFIC KB ENTRIES                              ║
    // ╚══════════════════════════════════════════════════════════╝

    // ═══ SISWA: DASHBOARD SIA ═══
    { kw:['dashboard sia','sia','sistem informasi akademik','beranda sia','halaman utama siswa'], title:'🏠 Dashboard SIA', text:'Halaman utama Sistem Informasi Akademik (SIA) yang menampilkan ringkasan data Anda.', steps:['Login ke sistem dengan akun siswa','Sistem otomatis mengarahkan ke Dashboard SIA','Lihat statistik: kehadiran, jadwal hari ini, tugas, dan nilai terbaru'], link:'/siswa/sia/dashboard', linkText:'Buka Dashboard SIA', note:'Dashboard menampilkan data presensi, jadwal hari ini, daftar tugas, nilai terbaru, dan progres belajar.', related:['presensi','jadwal pelajaran','tugas','nilai'] },

    // ═══ SISWA: PRESENSI (LIHAT) ═══
    { kw:['presensi saya','absensi saya','kehadiran saya','rekap kehadiran','lihat presensi','data presensi'], title:'✅ Presensi Saya', text:'Lihat rekap kehadiran Anda (Hadir, Sakit, Izin, Alpha).', steps:['Sidebar SIA → "Presensi"','Lihat rekap kehadiran per bulan','Cek detail status: Hadir, Sakit, Izin, Alpha','Persentase kehadiran terlihat di dashboard'], link:'/siswa/sia/presensi', linkText:'Lihat Presensi', note:'Presensi diinput oleh Wali Kelas. Jika ada kesalahan data, hubungi Wali Kelas Anda.', related:['izin','dashboard sia'] },

    // ═══ SISWA: DATA PENILAIAN ═══
    { kw:['penilaian','data penilaian','nilai saya','lihat nilai','rekap nilai saya','skor saya','grade saya'], title:'📊 Data Penilaian', text:'Lihat rekap nilai Anda per mata pelajaran (Tugas, UTS, UAS).', steps:['Sidebar SIA → "Data Penilaian"','Lihat daftar nilai per mata pelajaran','Cek jenis nilai: Tugas, UTS, UAS','Lihat rata-rata dan status ketuntasan'], link:'/siswa/sia/penilaian', linkText:'Lihat Data Penilaian', note:'Nilai diinput oleh Guru Pengajar dan Wali Kelas. Hubungi guru jika ada pertanyaan.', related:['tugas','ujian','rapor'] },

    // ═══ SISWA: LMS DASHBOARD ═══
    { kw:['lms siswa','hok lms','dashboard lms','belajar online','e-learning siswa','platform belajar siswa','masuk lms'], title:'🎓 HOK-LMS (Learning Management System)', text:'Platform belajar online — akses materi, tugas, ujian, forum, dan meeting virtual.', steps:['Sidebar SIA → "HOK-LMS" atau klik banner LMS di dashboard','Pilih mata pelajaran yang ingin dipelajari','Akses: Materi, Tugas, Ujian, Forum'], link:'/siswa/lms/dashboard', linkText:'Masuk LMS', note:'LMS hanya tersedia untuk siswa jenjang SMP/SMA. Akses memerlukan validasi pembayaran.', related:['tugas','ujian','materi','jadwal pelajaran'] },

    // ═══ SISWA: MATA PELAJARAN ═══
    { kw:['mata pelajaran saya','mapel saya','daftar mapel siswa','pelajaran saya','kelas saya'], title:'📖 Mata Pelajaran Saya', text:'Lihat daftar mata pelajaran yang Anda ikuti berdasarkan kelas dan jadwal.', steps:['Masuk ke LMS','Di sidebar kiri, lihat daftar "Mata Pelajaran"','Klik mata pelajaran untuk masuk','Akses: Materi, Tugas, Ujian, Forum'], link:'/siswa/lms/dashboard', linkText:'Buka LMS', note:'Daftar mapel otomatis berdasarkan jadwal pelajaran kelas Anda.', related:['materi','tugas','ujian','jadwal pelajaran'] },

    // ═══ SISWA: TUGAS ═══
    { kw:['tugas saya','lihat tugas','kumpulkan tugas','kerjakan tugas','deadline tugas','submit tugas siswa'], title:'📝 Tugas Saya', text:'Kerjakan dan kumpulkan tugas yang diberikan guru.', steps:['LMS → pilih Mata Pelajaran → tab "Tugas"','Lihat daftar tugas dan deadline-nya','Klik tugas → baca instruksi','Upload jawaban → klik "Kumpulkan"'], link:'/siswa/lms/dashboard', linkText:'Buka LMS', note:'⚠️ Perhatikan deadline! Setelah deadline lewat, tugas tidak bisa dikumpulkan. Cek dashboard untuk tugas urgent.', related:['ujian','nilai','materi'] },

    // ═══ SISWA: UJIAN ═══
    { kw:['ujian saya','mulai ujian','tes online siswa','kuis saya','latihan soal','kerjakan ujian'], title:'📋 Ujian Saya', text:'Kerjakan ujian dan latihan online yang dibuat guru.', steps:['LMS → pilih Mata Pelajaran → tab "Ujian"','Lihat daftar ujian yang tersedia','Klik ujian → pastikan siap → "Mulai Ujian"','Jawab semua soal → klik "Submit/Selesai"'], link:'/siswa/lms/dashboard', linkText:'Buka LMS', note:'⚠️ Ujian yang terkunci berarti validasi pembayaran belum disetujui. Hubungi Orang Tua atau Admin.', related:['tugas','nilai','validasi akses'] },

    // ═══ SISWA: MATERI ═══
    { kw:['materi saya','baca materi','download materi siswa','bahan pelajaran','modul pelajaran siswa'], title:'📖 Materi Pelajaran Saya', text:'Akses materi/bahan ajar yang di-upload guru.', steps:['LMS → pilih Mata Pelajaran → tab "Materi"','Lihat daftar materi yang tersedia','Klik untuk baca online atau download file'], link:'/siswa/lms/dashboard', linkText:'Buka LMS', related:['tugas','forum','mata pelajaran'] },

    // ═══ SISWA: FORUM ═══
    { kw:['forum saya','diskusi siswa','tanya jawab siswa','tanya guru siswa','buat diskusi'], title:'💬 Forum Diskusi Saya', text:'Forum tanya-jawab per mata pelajaran di LMS.', steps:['LMS → pilih Mata Pelajaran → tab "Forum"','Lihat diskusi yang ada atau buat diskusi baru','Klik "Buat Diskusi Baru" → isi judul dan pertanyaan','Guru dan teman sekelas bisa membalas'], link:'/siswa/lms/dashboard', linkText:'Buka LMS', related:['materi','meeting','mata pelajaran'] },

    // ═══ SISWA: MEETING ═══
    { kw:['meeting saya','kelas virtual siswa','zoom siswa','google meet siswa','video call siswa','join meeting'], title:'🎥 Meeting / Kelas Virtual Saya', text:'Ikuti pertemuan online/kelas virtual yang dijadwalkan guru.', steps:['LMS → klik "Meeting" di sidebar atau di halaman kelas','Lihat jadwal meeting yang tersedia','Klik link Zoom/Google Meet untuk bergabung','Pastikan masuk tepat waktu'], link:'/siswa/lms/dashboard', linkText:'Buka LMS', related:['jadwal pelajaran','mata pelajaran'] },

    // ═══ SISWA: KALENDER AKADEMIK ═══
    { kw:['kalender siswa','jadwal libur siswa','event sekolah siswa','agenda siswa','kegiatan sekolah siswa'], title:'📆 Kalender Akademik Siswa', text:'Lihat kalender akademik: event, libur, dan kegiatan sekolah.', steps:['LMS → sidebar → "Kalender Akademik"','Lihat kalender bulanan','Klik event untuk lihat detail'], link:'/siswa/lms/kalender', linkText:'Lihat Kalender', related:['jadwal pelajaran','pengumuman'] },

    // ═══ SISWA: JADWAL PELAJARAN ═══
    { kw:['jadwal saya','jadwal pelajaran saya','jadwal kelas saya','jam pelajaran saya','jadwal hari ini'], title:'📅 Jadwal Pelajaran Saya', text:'Lihat jadwal pelajaran kelas Anda per hari.', steps:['LMS → sidebar → "Jadwal Pelajaran"','Lihat jadwal lengkap per hari','Jadwal hari ini juga terlihat di Dashboard SIA'], link:'/siswa/lms/jadwal', linkText:'Lihat Jadwal', note:'Jadwal diatur oleh Admin/Wakasek. Jika ada perubahan, cek pengumuman.', related:['mata pelajaran','dashboard sia','kalender'] },

    // ═══ SISWA: DAFTAR GURU ═══
    { kw:['daftar guru','guru saya','guru pengajar saya','siapa guru','info guru'], title:'👨‍🏫 Daftar Guru Saya', text:'Lihat daftar guru pengajar di kelas Anda.', steps:['LMS → sidebar → "Daftar Guru"','Lihat nama guru dan mata pelajaran yang diajar'], link:'/siswa/lms/guru', linkText:'Lihat Daftar Guru', related:['jadwal pelajaran','mata pelajaran'] },

    // ═══ SISWA: RAPOR (INFORMASI) ═══
    { kw:['rapor saya','lihat rapor','download rapor siswa','hasil belajar saya','rapor semester'], title:'📄 Rapor Saya', text:'Rapor semester Anda — lihat dan download melalui akun Orang Tua.', steps:['Rapor di-generate oleh Wali Kelas','Wali Kelas mengirim ke Ketua PKBM untuk validasi','Setelah divalidasi, Orang Tua bisa lihat dan download','Minta Orang Tua Anda untuk cek di Dashboard Orang Tua'], link:'/siswa/sia/dashboard', linkText:'Kembali ke Dashboard',
      note:'⚠️ Download rapor hanya tersedia melalui akun Orang Tua. Minta orang tua Anda untuk mengakses dan mendownload rapor.', related:['nilai','presensi'] },

    // ═══ SISWA: PEMBAYARAN (INFORMASI) ═══
    { kw:['pembayaran saya','bayar spp siswa','tagihan saya','biaya sekolah','uang sekolah siswa','cara bayar'], title:'💳 Pembayaran & Tagihan', text:'Informasi pembayaran SPP dan tagihan sekolah.', steps:['Pembayaran dan tagihan dikelola oleh Orang Tua','Orang Tua membayar melalui Dashboard Orang Tua','Pembayaran online tersedia via Midtrans (QRIS, Transfer, e-wallet)','Status pembayaran mempengaruhi akses ujian dan rapor'], link:'/siswa/sia/dashboard', linkText:'Kembali ke Dashboard',
      note:'⚠️ Pembayaran dan tagihan hanya bisa diakses melalui akun Orang Tua. Minta orang tua Anda untuk mengecek tagihan dan melakukan pembayaran.', related:['presensi','dashboard sia'] },

    // ═══ SISWA: IZIN (INFORMASI) ═══
    { kw:['izin saya','izin tidak masuk siswa','cara izin siswa','izin sakit siswa'], title:'📝 Pengajuan Izin', text:'Izin tidak masuk sekolah hanya bisa diajukan oleh Orang Tua.', steps:['Orang Tua login ke Dashboard Orang Tua','Menu "Presensi" → pilih anak','Klik "Ajukan Izin"','Isi tanggal, jenis izin, keterangan, lampiran','Wali Kelas akan memvalidasi'], link:'/siswa/sia/dashboard', linkText:'Kembali ke Dashboard',
      note:'⚠️ Siswa tidak bisa ajukan izin sendiri. Minta Orang Tua Anda untuk mengajukan izin melalui akun mereka.', related:['presensi','dashboard sia'] },

    // ╔══════════════════════════════════════════════════════════╗
    // ║  GURU PENGAJAR-SPECIFIC KB ENTRIES                      ║
    // ╚══════════════════════════════════════════════════════════╝

    // ═══ GURU: DASHBOARD ═══
    { kw:['dashboard guru','beranda guru','halaman utama guru'], title:'🏠 Dashboard Guru', text:'Halaman utama guru yang menampilkan ringkasan jadwal mengajar, kelas, dan aktivitas LMS.', steps:['Login ke sistem dengan akun guru','Sistem mengarahkan ke Dashboard','Lihat ringkasan: jadwal hari ini, kelas yang diajar, statistik'], link:'/guru/dashboard', linkText:'Buka Dashboard', note:'Dashboard menampilkan jadwal mengajar hari ini dan daftar kelas Anda.', related:['jadwal mengajar','semua kelas'] },

    // ═══ GURU: JADWAL MENGAJAR ═══
    { kw:['jadwal mengajar','jadwal guru','jadwal saya guru','jam mengajar','schedule guru'], title:'📅 Jadwal Mengajar', text:'Lihat jadwal mengajar Anda per hari dan per kelas.', steps:['Sidebar → "Jadwal Mengajar"','Lihat jadwal lengkap per hari','Cek kelas, mata pelajaran, dan jam mengajar'], link:'/guru/jadwal', linkText:'Lihat Jadwal Mengajar', note:'Jadwal diatur oleh Admin/Wakasek. Jika ada perubahan, hubungi admin.', related:['semua kelas','dashboard guru'] },

    // ═══ GURU: SEMUA KELAS ═══
    { kw:['semua kelas','kelas saya guru','daftar kelas guru','kelas yang diajar','pilih kelas'], title:'📋 Semua Kelas Saya', text:'Lihat daftar semua kelas dan mata pelajaran yang Anda ajar.', steps:['Sidebar → "Semua Kelas"','Lihat daftar kelas beserta mata pelajaran','Klik kelas → pilih mapel → masuk LMS untuk mengelola pembelajaran'], link:'/guru/kelas', linkText:'Lihat Semua Kelas', note:'Sidebar juga menampilkan shortcut "Kelas Saya" untuk akses cepat ke setiap kelas + mapel.', related:['jadwal mengajar','lms guru'] },

    // ═══ GURU: LMS DASHBOARD ═══
    { kw:['lms guru','dashboard lms guru','beranda lms guru','masuk lms guru','kelola lms'], title:'🎓 LMS Guru — Beranda', text:'Dashboard LMS per kelas dan mapel — kelola pembelajaran online dari sini.', steps:['Sidebar → pilih kelas → pilih mata pelajaran','Atau: "Semua Kelas" → klik kelas → masuk LMS','Lihat ringkasan: jumlah materi, tugas, ujian, forum'], link:'/guru/kelas', linkText:'Pilih Kelas', note:'Setiap kombinasi kelas + mata pelajaran memiliki LMS terpisah. Pilih dari sidebar.', related:['materi guru','tugas guru','ujian guru','nilai guru'] },

    // ═══ GURU: MATERI ═══
    { kw:['materi guru','upload materi','buat materi','tambah materi','kelola materi guru','bahan ajar guru'], title:'📖 Kelola Materi (Guru)', text:'Upload dan kelola materi/bahan ajar untuk siswa.', steps:['Masuk LMS kelas → sidebar "Materi"','Klik "Tambah Materi" untuk upload baru','Isi judul, deskripsi, upload file/konten','Klik "Simpan" — siswa langsung bisa mengakses'], link:'/guru/kelas', linkText:'Pilih Kelas', note:'Format yang didukung: PDF, DOC, PPT, gambar. Siswa bisa baca online atau download.', related:['tugas guru','ujian guru','forum guru'] },

    // ═══ GURU: TUGAS ═══
    { kw:['tugas guru','buat tugas guru','koreksi tugas','kelola tugas guru','deadline tugas guru','cek tugas'], title:'📝 Kelola Tugas (Guru)', text:'Buat, kelola, dan koreksi tugas siswa.', steps:['Masuk LMS kelas → sidebar "Tugas"','Klik "Buat Tugas" → isi judul, instruksi, deadline','Upload file lampiran jika perlu','Siswa mengumpulkan → klik "Koreksi" untuk menilai','Beri nilai dan feedback per siswa'], link:'/guru/kelas', linkText:'Pilih Kelas', note:'Badge notifikasi muncul jika ada tugas yang belum dikoreksi. Perhatikan deadline yang sudah diset.', related:['koreksi tugas','nilai guru','ujian guru'] },

    // ═══ GURU: LATIHAN ═══
    { kw:['latihan guru','buat latihan','kelola latihan','soal latihan','latihan soal guru'], title:'✏️ Kelola Latihan (Guru)', text:'Buat latihan soal untuk siswa sebagai persiapan ujian.', steps:['Masuk LMS kelas → sidebar "Latihan"','Klik "Buat Latihan"','Tambah soal (manual atau AI Generate)','Atur waktu dan pengaturan lainnya','Simpan dan publikasikan'], link:'/guru/kelas', linkText:'Pilih Kelas', related:['ujian guru','tugas guru','nilai guru'] },

    // ═══ GURU: UJIAN ═══
    { kw:['ujian guru','buat ujian guru','kelola ujian','soal ujian guru','koreksi ujian','hasil ujian'], title:'📋 Kelola Ujian (Guru)', text:'Buat, kelola soal, dan koreksi ujian online.', steps:['Masuk LMS kelas → sidebar "Ujian"','Klik "Buat Ujian" → isi judul, waktu, pengaturan','"Kelola Soal" → tambah soal (manual, import, AI Generate)','Jenis soal: Pilgan, Pilgan Kompleks, Isian, Uraian, Benar/Salah','Setelah siswa selesai → "Koreksi" untuk menilai'], link:'/guru/kelas', linkText:'Pilih Kelas', note:'Soal pilihan ganda auto-koreksi. Soal uraian harus dikoreksi manual.', related:['latihan guru','nilai guru','tugas guru'] },

    // ═══ GURU: FORUM ═══
    { kw:['forum guru','diskusi guru','moderasi forum','kelola forum guru','balas diskusi'], title:'💬 Kelola Forum Diskusi (Guru)', text:'Moderasi forum tanya-jawab per mata pelajaran.', steps:['Masuk LMS kelas → sidebar "Forum Diskusi"','Lihat diskusi yang dibuat siswa','Balas pertanyaan siswa','Bisa pin diskusi penting, tutup diskusi, atau hapus'], link:'/guru/kelas', linkText:'Pilih Kelas', related:['materi guru','meeting guru'] },

    // ═══ GURU: MEETING / KELAS VIRTUAL ═══
    { kw:['meeting guru','buat meeting guru','kelas virtual guru','zoom guru','google meet guru','jadwalkan meeting'], title:'🎥 Kelola Meeting / Kelas Virtual (Guru)', text:'Jadwalkan dan kelola pertemuan online (Zoom/Google Meet).', steps:['Masuk LMS kelas → sidebar "Kelas Virtual"','Klik "Buat Meeting"','Isi judul, link Zoom/Google Meet, tanggal, waktu','Simpan — siswa bisa lihat dan join via link'], link:'/guru/kelas', linkText:'Pilih Kelas', related:['jadwal mengajar','forum guru'] },

    // ═══ GURU: NILAI SISWA ═══
    { kw:['nilai guru','input nilai guru','kelola nilai','rekap nilai guru','beri nilai','penilaian guru'], title:'📊 Kelola Nilai Siswa (Guru)', text:'Input dan kelola nilai siswa per mata pelajaran.', steps:['Masuk LMS kelas → sidebar "Nilai Siswa"','Lihat rekap nilai seluruh siswa di kelas','Input/edit nilai: Tugas, UTS, UAS','Bisa import nilai via Excel'], link:'/guru/kelas', linkText:'Pilih Kelas', note:'Nilai yang diinput akan terlihat di rapor siswa dan Data Penilaian siswa.', related:['tugas guru','ujian guru','rapor'] },

    // ═══ GURU: KOREKSI TUGAS ═══
    { kw:['koreksi tugas','koreksi jawaban','review tugas','cek jawaban siswa','nilai tugas guru'], title:'✅ Koreksi Tugas (Guru)', text:'Review dan beri nilai jawaban tugas siswa.', steps:['LMS → "Tugas" → pilih tugas','Klik "Koreksi" untuk melihat jawaban siswa','Download/buka file jawaban siswa','Beri nilai dan feedback','Klik "Simpan Nilai"'], link:'/guru/kelas', linkText:'Pilih Kelas', note:'Badge merah pada menu Tugas menunjukkan jumlah tugas yang belum dikoreksi.', related:['tugas guru','nilai guru'] },

    // ╔══════════════════════════════════════════════════════════╗
    // ║  ORANG TUA-SPECIFIC KB ENTRIES                          ║
    // ╚══════════════════════════════════════════════════════════╝

    // ═══ ORTU: DASHBOARD ═══
    { kw:['dashboard orang tua','beranda orang tua','halaman utama ortu','menu utama ortu'], title:'🏠 Dashboard Orang Tua', text:'Halaman utama Orang Tua — pantau semua informasi anak dari sini.', steps:['Login ke sistem dengan akun Orang Tua','Sistem mengarahkan ke Dashboard','Lihat daftar anak beserta ringkasan: presensi, tagihan, rapor','Klik nama anak di sidebar untuk akses detail'], link:'/orang-tua/dashboard', linkText:'Buka Dashboard', note:'Satu akun Orang Tua bisa terhubung ke beberapa anak. Semua anak terlihat di sidebar.', related:['presensi anak','tagihan anak','rapor anak'] },

    // ═══ ORTU: PRESENSI ANAK ═══
    { kw:['presensi anak','kehadiran anak','absensi anak','lihat presensi anak','cek kehadiran','rekap kehadiran anak'], title:'✅ Presensi Anak', text:'Pantau kehadiran anak Anda di sekolah (Hadir, Sakit, Izin, Alpha).', steps:['Sidebar → pilih nama anak → "Presensi"','Lihat rekap kehadiran per bulan','Cek detail status: Hadir, Sakit, Izin, Alpha','Persentase kehadiran terlihat di halaman'], link:'/orang-tua/dashboard', linkText:'Buka Dashboard', note:'Presensi diinput oleh Wali Kelas setiap hari. Data otomatis tersinkron.', related:['izin anak','dashboard orang tua'] },

    // ═══ ORTU: AJUKAN IZIN ═══
    { kw:['izin anak','ajukan izin anak','izin tidak masuk','izin sakit anak','surat izin anak','pengajuan izin anak','anak tidak masuk'], title:'📝 Ajukan Izin Anak', text:'Ajukan izin tidak masuk sekolah untuk anak Anda.', steps:['Sidebar → pilih nama anak → "Presensi"','Klik tombol "Ajukan Izin"','Pilih tanggal izin','Pilih jenis: Sakit / Izin','Isi keterangan dan lampirkan bukti (opsional)','Klik "Kirim" — Wali Kelas akan memvalidasi'], link:'/orang-tua/dashboard', linkText:'Buka Dashboard', note:'⚠️ Hanya Orang Tua yang bisa mengajukan izin. Siswa tidak bisa mengajukan sendiri. Cek "Riwayat Izin" untuk status.', related:['presensi anak','riwayat izin'] },

    // ═══ ORTU: RIWAYAT IZIN ═══
    { kw:['riwayat izin','status izin','izin disetujui','izin ditolak','histori izin'], title:'📋 Riwayat Izin Anak', text:'Cek status dan riwayat pengajuan izin anak.', steps:['Sidebar → pilih nama anak → "Presensi"','Klik "Riwayat Izin" atau scroll ke bagian riwayat','Lihat status: Menunggu, Disetujui, Ditolak','Bisa edit izin yang masih Menunggu'], link:'/orang-tua/dashboard', linkText:'Buka Dashboard', related:['izin anak','presensi anak'] },

    // ═══ ORTU: TAGIHAN & PEMBAYARAN ═══
    { kw:['tagihan anak','pembayaran anak','bayar spp','bayar tagihan','spp anak','biaya sekolah anak','cek tagihan','uang sekolah','cara bayar spp'], title:'💳 Tagihan & Pembayaran Anak', text:'Lihat dan bayar tagihan sekolah (SPP, pendaftaran, dll) anak Anda.', steps:['Sidebar → pilih nama anak → "Tagihan"','Lihat daftar tagihan: Belum Bayar, Menunggu Validasi, Lunas','Klik tagihan → "Bayar" untuk pembayaran online','Pilih metode: QRIS, Transfer Bank, GoPay, OVO, Dana, dll','Selesaikan pembayaran → status otomatis ter-update'], link:'/orang-tua/dashboard', linkText:'Buka Dashboard', note:'Pembayaran online melalui Midtrans. Jika transfer manual, upload bukti dan tunggu validasi Admin/Bendahara.', related:['rapor anak','validasi akses'] },

    // ═══ ORTU: INVOICE / CETAK BUKTI BAYAR ═══
    { kw:['invoice','cetak invoice','bukti pembayaran','kwitansi','cetak kwitansi','bukti bayar'], title:'🧾 Invoice / Bukti Pembayaran', text:'Cetak invoice atau bukti pembayaran untuk tagihan anak.', steps:['Sidebar → pilih nama anak → "Tagihan"','Klik tagihan yang sudah lunas','Klik "Cetak Invoice" atau "Download PDF"'], link:'/orang-tua/dashboard', linkText:'Buka Dashboard', related:['tagihan anak','pembayaran anak'] },

    // ═══ ORTU: RAPOR ANAK ═══
    { kw:['rapor anak','lihat rapor anak','download rapor anak','hasil belajar anak','rapor semester anak','cetak rapor anak'], title:'📄 Rapor Anak', text:'Lihat dan download rapor semester anak Anda.', steps:['Sidebar → pilih nama anak → "Rapor"','Lihat daftar rapor per semester','Klik rapor → lihat detail nilai','Klik "Download / Cetak" untuk download PDF rapor','Rapor Tengah Semester dan Akhir Semester tersedia'], link:'/orang-tua/dashboard', linkText:'Buka Dashboard', note:'⚠️ Rapor hanya bisa didownload jika sudah divalidasi oleh Wali Kelas dan Ketua PKBM. Pembayaran juga harus sudah divalidasi (Validasi Akses).', related:['tagihan anak','presensi anak'] },

    // ═══ ORTU: NILAI ANAK (INFORMASI) ═══
    { kw:['nilai anak','penilaian anak','skor anak','grade anak','lihat nilai anak'], title:'📊 Nilai Anak', text:'Informasi tentang nilai anak Anda.', steps:['Nilai anak bisa dilihat melalui menu "Rapor"','Rapor berisi rekap semua nilai per mata pelajaran','Untuk detail nilai tugas/ujian, anak bisa cek di Data Penilaian (akun siswa)'], link:'/orang-tua/dashboard', linkText:'Buka Dashboard', note:'Nilai lengkap tertera di rapor. Hubungi Wali Kelas untuk pertanyaan terkait nilai.', related:['rapor anak','presensi anak'] },

    // ═══ ORTU: LMS ANAK (INFORMASI) ═══
    { kw:['lms anak','e-learning anak','belajar online anak','tugas anak','ujian anak'], title:'🎓 LMS Anak (Informasi)', text:'Informasi tentang LMS (Learning Management System) anak Anda.', steps:['LMS diakses langsung oleh siswa melalui akun mereka','Fitur LMS: Materi, Tugas, Ujian, Forum, Meeting Virtual','Orang Tua tidak memiliki akses langsung ke LMS','Pantau progres anak melalui menu Rapor dan Presensi'], link:'/orang-tua/dashboard', linkText:'Buka Dashboard', note:'⚠️ LMS hanya bisa diakses oleh akun Siswa. Pastikan anak Anda login ke akunnya sendiri untuk mengakses LMS.', related:['rapor anak','tagihan anak'] },

    // ╔══════════════════════════════════════════════════════════╗
    // ║  WALI KELAS-SPECIFIC KB ENTRIES                         ║
    // ╚══════════════════════════════════════════════════════════╝

    // ═══ WALI: DASHBOARD ═══
    { kw:['dashboard wali','beranda wali','halaman utama wali kelas'], title:'🏠 Dashboard Wali Kelas', text:'Halaman utama untuk Wali Kelas mengelola akademik kelas.', steps:['Login dengan akun Wali Kelas','Lihat ringkasan: jumlah siswa, persentase kehadiran, rapor'], link:'/wali/dashboard', linkText:'Buka Dashboard', related:['pilih kelas','presensi wali'] },

    // ═══ WALI: PILIH KELAS ═══
    { kw:['pilih kelas wali','ganti kelas wali','kelas waili kelas'], title:'🏫 Pilih / Ganti Kelas', text:'Beralih akses antar kelas yang Anda wali-kan.', steps:['Sidebar → "Pilih Kelas" (jika mengelola lebih dari 1 kelas)','Klik kelas untuk dikelola'], link:'/wali/pilih-kelas', linkText:'Pilih Kelas', note:'Pilihan menu akademik (Presensi, Rapor, Nilai) akan berubah sesuai kelas aktif.', related:['dashboard wali'] },

    // ═══ WALI: JADWAL PELAJARAN ═══
    { kw:['jadwal kelas wali','jadwal pelajaran wali','jadwal wali'], title:'📅 Jadwal Pelajaran Kelas', text:'Lihat jadwal pelajaran kelas yang sedang aktif dikelola.', steps:['Sidebar → "Jadwal Pelajaran"','Lihat jadwal per hari untuk kelas tersebut'], link:'/wali/jadwal', linkText:'Lihat Jadwal', related:['presensi wali'] },

    // ═══ WALI: PRESENSI (INPUT HARIAN) ═══
    { kw:['presensi wali','input presensi harian','absen siswa wali','kehadiran siswa wali','input absen','absen kelas'], title:'✅ Input Presensi Harian', text:'Input kehadiran siswa (Hadir, Sakit, Izin, Alpha) setiap harinya.', steps:['Sidebar → Presensi Siswa → "Input Harian"','Pilih tanggal','Pilih status tiap siswa (default: Hadir)','Klik "Simpan"'], link:'/wali/presensi', linkText:'Input Presensi', note:'Input presensi WAJIB dilakukan setiap hari. Jika terlewat, gunakan menu Riwayat & Edit.', related:['validasi izin wali','rekap presensi wali'] },

    // ═══ WALI: VALIDASI IZIN ═══
    { kw:['validasi izin','setujui izin','izin ortu','izin sakit wali','cek izin','acc izin'], title:'✅ Validasi Izin Siswa', text:'Setujui atau tolak pengajuan izin (Sakit/Izin) dari Orang Tua.', steps:['Sidebar → Presensi Siswa → "Validasi Izin"','Lihat daftar pengajuan izin yang "Menunggu"','Klik "Validasi" → Setujui / Tolak','Jika disetujui, kehadiran hari tersebut otomatis berubah jadi Sakit/Izin'], link:'/wali/presensi/validasi-izin', linkText:'Validasi Izin', related:['presensi wali','riwayat presensi wali'] },

    // ═══ WALI: REKAP PRESENSI ═══
    { kw:['rekap presensi wali','rekap absen wali','laporan kehadiran kelas'], title:'📅 Rekap Presensi Harian', text:'Lihat rekapitulasi kehadiran kelas per tanggal/bulan.', steps:['Sidebar → Presensi Siswa → "Rekap Harian"','Lihat total Hadir, Sakit, Izin, Alpha per hari'], link:'/wali/presensi/rekap-harian', linkText:'Lihat Rekap', related:['presensi wali'] },

    // ═══ WALI: RIWAYAT & EDIT PRESENSI ═══
    { kw:['riwayat presensi wali','edit presensi','edit absen','ubah presensi','ubah kehadiran'], title:'📋 Riwayat & Edit Presensi', text:'Edit data presensi hari-hari sebelumnya jika ada kesalahan.', steps:['Sidebar → Presensi Siswa → "Riwayat & Edit"','Pilih tanggal yang ingin diedit','Ubah status kehadiran siswa → Simpan'], link:'/wali/presensi/riwayat', linkText:'Riwayat & Edit', related:['presensi wali'] },

    // ═══ WALI: NILAI SISWA ═══
    { kw:['nilai siswa wali','rekap nilai kelas','nilai wali kelas','cek nilai uas','cek nilai uts'], title:'📊 Nilai Siswa (Wali Kelas)', text:'Lihat rekap nilai dari semua mata pelajaran di kelas ini.', steps:['Sidebar → "Nilai Siswa"','Lihat daftar rekap nilai Tugas, UTS, UAS per siswa','Bahan referensi untuk Kelola Rapor'], link:'/wali/nilai', linkText:'Lihat Nilai', note:'Nilai di-input oleh masing-masing Guru Pengajar. Wali kelas hanya merekap untuk rapor.', related:['kelola rapor wali'] },

    // ═══ WALI: KELOLA RAPOR ═══
    { kw:['rapor wali','kelola rapor','buat rapor','cetak rapor wali','rapor pts','rapor pas'], title:'📄 Kelola Rapor', text:'Kelola pencetakan rapor UTS (PTS) dan UAS (PAS).', steps:['Sidebar → "Kelola Rapor"','Pilih jenis rapor (PTS / PAS)','Generate rapor dari nilai mata pelajaran','Kirim rapor untuk divalidasi Ketua PKBM'], link:'/wali/rapor', linkText:'Kelola Rapor', note:'⚠️ Pastikan nilai siswa sudah lengkap dari guru pengajar sebelum rekap rapor.', related:['request download rapor','nilai siswa wali'] },

    // ═══ WALI: REQUEST DOWNLOAD RAPOR ═══
    { kw:['request rapor','izin download rapor','request download','setujui download rapor','request ortu rapor'], title:'📥 Request Download Rapor', text:'Setujui permintaan Orang Tua untuk download rapor fisik.', steps:['Sidebar → "Request Download"','Lihat daftar permintaan dari Orang Tua','Klik Setujui atau Tolak'], link:'/wali/rapor/request-download', linkText:'Request Download', related:['kelola rapor wali'] },

    // ═══ WALI: PREDIKSI KENAIKAN KELAS ═══
    { kw:['prediksi kenaikan','kenaikan kelas','syarat naik kelas','status naik kelas'], title:'📈 Prediksi Kenaikan Kelas', text:'Lihat prediksi apakah siswa memenuhi syarat kenaikan kelas.', steps:['Sidebar → "Prediksi Kenaikan"','Sistem menampilkan analisis nilai & kehadiran','Status: Aman / Perlu Perhatian / Rawan'], link:'/wali/promotion/prediction', linkText:'Lihat Prediksi', related:['kelola rapor wali','presensi wali'] },

    // ═══ WALI: VALIDASI AKSES ═══
    { kw:['validasi akses wali','akses lms wali','akses ujian wali','buka ujian siswa','buka lms'], title:'🔓 Validasi Akses Ujian / Rapor', text:'Validasi akses ujian & rapor siswa jika ada kebijakan relaksasi.', steps:['Sidebar → "Validasi Akses"','Lihat status akses siswa (LMS, Ujian, Rapor)','Gunakan menu ini jika siswa bermasalah dengan SPP tapi diizinkan ikut ujian atas kebijakan khusus'], link:'/wali/validasi-akses', linkText:'Validasi Akses', related:['kelola rapor wali'] },

    // ╔══════════════════════════════════════════════════════════╗
    // ║  BENDAHARA-SPECIFIC KB ENTRIES                          ║
    // ╚══════════════════════════════════════════════════════════╝

    // ═══ BENDAHARA: DASHBOARD ═══
    { kw:['dashboard bendahara','beranda bendahara','halaman utama bendahara'], title:'🏠 Dashboard Bendahara', text:'Halaman utama untuk Bendahara mengelola keuangan.', steps:['Login dengan akun Bendahara','Sistem mengarahkan ke Dashboard','Lihat ringkasan: total pemasukan, tagihan belum lunas, dll'], link:'/bendahara/dashboard', linkText:'Buka Dashboard', related:['kelola tagihan bendahara','laporan pembayaran bendahara'] },

    // ═══ BENDAHARA: KELOLA TAGIHAN ═══
    { kw:['kelola tagihan','buat tagihan sekolah','tagihan spp','tambah tagihan','edit tagihan','hapus tagihan'], title:'🧾 Kelola Tagihan Sekolah', text:'Buat, edit, dan kelola tagihan siswa (SPP, DSP, dll).', steps:['Sidebar → "Kelola Tagihan"','Klik "Tambah Tagihan" untuk membuat tagihan baru','Pilih jenis tagihan, jumlah, batasan waktu, & sasaran siswa','Klik "Simpan"'], link:'/bendahara/tagihan', linkText:'Kelola Tagihan', related:['kelola pembayaran bendahara','rekap tagihan bendahara'] },

    // ═══ BENDAHARA: KELOLA PEMBAYARAN ═══
    { kw:['kelola pembayaran','validasi pembayaran','acc pembayaran','pembayaran manual','cek transfer'], title:'💳 Kelola Pembayaran', text:'Validasi pembayaran manual atau kelola pembayaran yang masuk.', steps:['Sidebar → "Kelola Pembayaran"','Lihat daftar pembayaran masuk','Jika transfer manual, klik "Validasi" lalu Setujui / Tolak','Pembayaran via Midtrans otomatis divalidasi'], link:'/bendahara/pembayaran', linkText:'Kelola Pembayaran', related:['kelola tagihan bendahara','laporan pembayaran bendahara'] },

    // ═══ BENDAHARA: CONFIG PEMBAYARAN ═══
    { kw:['config pembayaran','pengaturan pembayaran','info rekening','ubah rekening bank','setting midtrans'], title:'⚙️ Config Pembayaran', text:'Atur informasi rekening bank manual dan informasi pembayaran lainnya.', steps:['Sidebar → "Config Pembayaran"','Ubah data rekening penerima transfer manual','Klik Simpan'], link:'/bendahara/info-pembayaran', linkText:'Config Pembayaran', related:['kelola pembayaran bendahara'] },

    // ═══ BENDAHARA: VALIDASI AKSES UJIAN / RAPOR ═══
    { kw:['validasi akses bendahara','akses ujian bendahara','buka ujian','buka akses rapor','izin ujian spp'], title:'🔓 Validasi Akses Ujian & Rapor', text:'Berikan atau cabut izin akses LMS/Ujian/Rapor berdasarkan status pembayaran.', steps:['Sidebar → "Validasi Akses Ujian & Rapor"','Cari nama siswa','Ubah status akses: Diizinkan / Diblokir'], link:'/bendahara/validasi-akses', linkText:'Validasi Akses', note:'Penting bagi siswa yang belum lunas tapi diberikan keringanan sementara.', related:['kelola tagihan bendahara'] },

    // ═══ BENDAHARA: VALIDASI DISPENSASI ═══
    { kw:['validasi dispensasi','acc dispensasi','izin kenaikan kelas','dispensasi keuangan'], title:'🎫 Validasi Dispensasi', text:'Validasi kelonggaran / dispensasi keuangan bagi siswa.', steps:['Sidebar → "Validasi Dispensasi"','Cek pengajuan dispensasi','Setujui / Tolak pengajuan'], link:'/bendahara/promotion/validation', linkText:'Validasi Dispensasi', related:['siswa belum lunas'] },

    // ═══ BENDAHARA: LAPORAN PEMBAYARAN ═══
    { kw:['laporan pembayaran bendahara','cetak laporan keuangan','rekap keuangan','laporan spp'], title:'📊 Laporan Pembayaran', text:'Lihat dan cetak laporan pembayaran masuk.', steps:['Sidebar → Laporan → "Laporan Pembayaran"','Filter berdasarkan tanggal atau bulan','Lihat rekap atau klik "Cetak Laporan" (PDF / Excel)'], link:'/bendahara/laporan', linkText:'Laporan Pembayaran', related:['rekap tagihan bendahara'] },

    // ═══ BENDAHARA: REKAP TAGIHAN ═══
    { kw:['rekap tagihan bendahara','rekap hutang','laporan tagihan'], title:'📊 Rekap Tagihan', text:'Lihat rekapitulasi seluruh tagihan siswa (yang sudah & belum bayar).', steps:['Sidebar → Laporan → "Rekap Tagihan"','Filter berdasarkan bulan atau tahun ajaran','Klik cetak untuk download'], link:'/bendahara/laporan/rekap-tagihan', linkText:'Rekap Tagihan', related:['laporan pembayaran bendahara','siswa belum lunas'] },

    // ═══ BENDAHARA: SISWA BELUM LUNAS ═══
    { kw:['siswa belum lunas','daftar nunggak','siswa nunggak','belum bayar spp','cek tunggakan'], title:'⚠️ Siswa Belum Lunas', text:'Daftar siswa yang belum melunasi tagihannya.', steps:['Sidebar → Laporan → "Siswa Belum Lunas"','Lihat daftar siswa dan nominal tunggakan','Bisa diekspor untuk follow-up ke Orang Tua'], link:'/bendahara/laporan/belum-lunas', linkText:'Lihat Siswa Belum Lunas', related:['kelola tagihan bendahara','rekap tagihan bendahara'] },

    // ╔══════════════════════════════════════════════════════════╗
    // ║  KETUA PKBM-SPECIFIC KB ENTRIES                         ║
    // ╚══════════════════════════════════════════════════════════╝

    // ═══ KETUA: DASHBOARD ═══
    { kw:['dashboard ketua','beranda ketua','halaman utama ketua'], title:'🏠 Dashboard Ketua PKBM', text:'Halaman utama untuk Ketua PKBM mengelola & memonitor yayasan.', steps:['Login dengan akun Ketua PKBM','Lihat ringkasan operasional'], link:'/ketua/dashboard', linkText:'Buka Dashboard', related:['monitoring data pengguna'] },

    // ═══ KETUA: APPROVAL DISPENSASI & KEUANGAN ═══
    { kw:['approval dispensasi','acc dispensasi ketua','dispensasi keuangan ketua'], title:'✅ Approval Dispensasi', text:'Beri persetujuan akhir pada pengajuan dispensasi.', steps:['Sidebar → Kenaikan Kelas → "Approval Dispensasi" / "Dispensasi Keuangan"','Cek daftar pengajuan dari siswa yang butuh dispensasi','Approved atau tolak pengajuan tsb'], link:'/ketua/promotion/approval', linkText:'Beri Approval', note:'Ketua bertindak sebagai validator final.', related:['validasi rapor ketua'] },

    // ═══ KETUA: VALIDASI RAPOR ═══
    { kw:['validasi rapor ketua','acc rapor','ttd rapor','persetujuan rapor'], title:'✅ Validasi Rapor', text:'Validasi akhir rapor sebelum bisa diakses Orang Tua.', steps:['Sidebar → "Validasi Rapor"','Cek draft rapor yang di-submit Wali Kelas','Validasi dan setujui untuk finalisasi'], link:'/ketua/validasi-rapor', linkText:'Validasi Rapor', related:['approval dispensasi'] },

    // ═══ KETUA: MONITORING ═══
    { kw:['monitoring pengguna','monitoring ketua','data pengguna ketua','data wali kelas ketua','data guru ketua','data siswa ketua'], title:'📊 Monitoring SDM & Siswa', text:'Pantau data Pengguna, Wali Kelas, Guru Pengajar, dan Siswa.', steps:['Sidebar → "Monitoring"','Pilih kategori yang ingin di-monitor (Pengguna / Wali Kelas / Guru / Siswa)','Lihat riwayat aktivitas dan statistik performa'], link:'/ketua/monitoring/pengguna', linkText:'Buka Monitoring', related:['dashboard ketua','cetak laporan ketua'] },

    // ═══ KETUA: LAPORAN & CATATAN ═══
    { kw:['laporan ketua','cetak laporan ketua','kirim catatan ketua','teguran ketua'], title:'📄 Laporan & Kirim Catatan', text:'Cetak laporan periodik atau kirim catatan/teguran ke staf.', steps:['Sidebar → Laporan & Catatan','Untuk cetak: klik "Cetak Laporan"','Untuk teguran: klik "Kirim Catatan" → isi pesan → kirim'], link:'/ketua/laporan', linkText:'Buka Laporan', related:['monitoring pengguna'] },

    // ╔══════════════════════════════════════════════════════════╗
    // ║  SEKRETARIS-SPECIFIC KB ENTRIES                         ║
    // ╚══════════════════════════════════════════════════════════╝

    // ═══ SEKRETARIS: DASHBOARD ═══
    { kw:['dashboard sekretaris','beranda sekretaris'], title:'🏠 Dashboard Sekretaris', text:'Halaman utama Sekretaris.', steps:['Login dengan akun Sekretaris','Lihat ringkasan kegiatan dan status pengumuman'], link:'/sekretaris/dashboard', linkText:'Buka Dashboard', related:['kelola berita'] },

    // ═══ SEKRETARIS: KALENDER AKADEMIK ═══
    { kw:['kelola kalender','atur kalender akademik','jadwal libur','tambah event kalender'], title:'📅 Kelola Kalender Akademik', text:'Atur tanggal penting, event, ujian, dan hari libur sekolah.', steps:['Sidebar → "Kalender Akademik"','Klik tanggal untuk menambah event','Atau klik "Tambah Event" di atas','Event ini akan terlihat di seluruh dashboard pengguna'], link:'/sekretaris/kalender', linkText:'Kelola Kalender', related:['buat pengumuman'] },

    // ═══ SEKRETARIS: PENGUMUMAN & FLYER ═══
    { kw:['buat pengumuman','kelola pengumuman','flyer','iklan sekolah','banner sekolah'], title:'📢 Kelola Pengumuman & Flyer', text:'Buat pengumuman atau upload flyer untuk ditampilkan di aplikasi.', steps:['Sidebar → "Pengumuman" atau "Flyer / Iklan"','Isi konten (gambar, judul, isi)','Publikasikan ke semua user atau role tertentu'], link:'/sekretaris/pengumuman', linkText:'Kelola Pengumuman', related:['kelola berita'] },

    // ═══ SEKRETARIS: KELOLA BERITA ═══
    { kw:['kelola berita','bikin artikel','berita sekolah','berita landing page'], title:'📰 Kelola Berita / Artikel', text:'Buat dan kelola berita untuk ditampilkan di website publik sekolah.', steps:['Sidebar → "Kelola Berita"','Klik "Tulis Berita"','Isi judul, cover, konten artikel','Klik Simpan & Publish'], link:'/sekretaris/berita', linkText:'Kelola Berita', related:['buat pengumuman'] },

    // ╔══════════════════════════════════════════════════════════╗
    // ║  WAKA-SPECIFIC KB ENTRIES                               ║
    // ╚══════════════════════════════════════════════════════════╝

    // ═══ WAKA: DASHBOARD ═══
    { kw:['dashboard waka','beranda waka','wakil kepsek'], title:'🏠 Dashboard Waka', text:'Halaman utama Wakil Kepala Sekolah Bidang Kurikulum/Kesiswaan.', steps:['Login dengan akun Waka','Lihat ringkasan kurikulum dan akademik'], link:'/waka/dashboard', linkText:'Buka Dashboard', related:['manajemen tahun ajaran','manajemen kelas waka'] },

    // ═══ WAKA: MANAJEMEN AKADEMIK (TAHUN, MAPEL, KELAS) ═══
    { kw:['manajemen tahun ajaran','atur tahun ajaran','mata pelajaran waka','manajemen kelas waka','atur kelas'], title:'📚 Manajemen Akademik (Tahun Ajaran, Mapel, Kelas)', text:'Kelola struktur dasar akademik sekolah.', steps:['Sidebar → "Manajemen Akademik"','Pilih Tahun Ajaran, Mata Pelajaran, atau Manajemen Kelas','Tambah/Edit/Hapus entitas sesuai kurikulum terbaru'], link:'/waka/mata-pelajaran', linkText:'Kelola Akademik', related:['penugasan wali kelas','jadwal pelajaran waka'] },

    // ═══ WAKA: MANAJEMEN SISWA & WALI & GURU ═══
    { kw:['manajemen siswa waka','penugasan wali kelas','ploting wali kelas','guru pengajar waka','ploting guru'], title:'👥 Manajemen SDM & Siswa', text:'Atur siswa, tugaskan wali kelas, dan tentukan guru pengajar untuk tiap kelas.', steps:['Sidebar → Manajemen Akademik','Pilih Manajemen Siswa / Penugasan Wali Kelas / Guru Pengajar','Plotting guru/wali ke kelas yang sesuai','Pindahkan siswa antar kelas jika perlu'], link:'/waka/wali-kelas', linkText:'Kelola Penugasan', related:['jadwal pelajaran waka'] },

    // ═══ WAKA: JADWAL PELAJARAN ═══
    { kw:['jadwal pelajaran waka','buat jadwal waka','plotting jadwal','atur jadwal kelas'], title:'📅 Kelola Jadwal Pelajaran', text:'Atur jadwal mata pelajaran, guru, dan jam mengajar untuk semua kelas.', steps:['Sidebar → "Jadwal Pelajaran"','Pilih Kelas','Tambahkan jadwal per hari untuk mata pelajaran dan gurunya'], link:'/waka/jadwal-pelajaran', linkText:'Kelola Jadwal', related:['manajemen akademik','pengaturan kkm'] },

    // ═══ WAKA: PENGATURAN KENAikan & KKM ═══
    { kw:['pengaturan kkm','nilai kkm','pengaturan naik kelas','rekap kenaikan waka','syarat naik kelas'], title:'📈 Pengaturan KKM & Kenaikan Kelas', text:'Tentukan nilai KKM mata pelajaran dan syarat kenaikan kelas siswa.', steps:['Sidebar → Kenaikan Kelas','Pilih "Pengaturan KKM" untuk set standar nilai minimal','Pilih "Pengaturan Naik Kelas" untuk set syarat kehadiran dll','Lihat hasil di menu "Proses & Rekap"'], link:'/waka/promotion/kkm', linkText:'Atur KKM', related:['jadwal pelajaran waka'] },

    // ═══ WAKA: MONITORING & CATATAN ═══
    { kw:['monitoring waka','monitor wali','monitor guru waka','catatan waka','kirim teguran waka'], title:'📊 Monitoring & Catatan', text:'Pantau performa Guru / Wali Kelas dan kirim catatan dinas.', steps:['Sidebar → "Monitoring" atau "Kirim Catatan"','Cek aktivitas mengajar guru atau presensi wali kelas','Kirim catatan langsung jika ada hal yang kurang sesuai'], link:'/waka/monitoring/guru-pengajar', linkText:'Buka Monitoring', related:['dashboard waka'] },
];

// ═══ Role-Based KB Filter ═══
// null = universal (semua role), array = only those roles
// Entries NOT in this map → admin-only by default
const KB_ROLE_FILTER = {
    // Universal — semua role bisa lihat
    '👤 Profil Saya': null,
    '⚙️ Pengaturan Akun': null,
    '🔑 Login ke Sistem': null,
    '🚪 Logout / Keluar': null,
    '🔔 Notifikasi': null,
    '🏠 Dashboard': null,
    // Cross-role (admin + other roles)
    '✅ Presensi Siswa': ['admin','wali_kelas'],
    '📝 Pengajuan Izin Siswa': ['admin','orang_tua'],
    '📄 Rapor Siswa': ['admin','wali_kelas','orang_tua','ketua_pkbm'],
    '📝 Nilai Siswa': ['admin','wali_kelas','guru_pengajar'],
    '📚 LMS (Learning Management System)': ['admin','guru_pengajar','wali_kelas'],
    '📝 Tugas (LMS)': ['admin','guru_pengajar'],
    '📋 Ujian Online (LMS)': ['admin','guru_pengajar'],
    '📖 Materi Pelajaran': ['admin','guru_pengajar'],
    '💬 Forum Diskusi': ['admin','guru_pengajar'],
    '🎥 Meeting / Kelas Virtual': ['admin','guru_pengajar'],
    '👪 Fitur Orang Tua': ['admin','orang_tua'],
    '📥 Import Data via Excel': ['admin','sekretaris'],
    // Siswa-specific entries
    '🏠 Dashboard SIA': ['siswa'],
    '✅ Presensi Saya': ['siswa'],
    '📊 Data Penilaian': ['siswa'],
    '🎓 HOK-LMS (Learning Management System)': ['siswa'],
    '📖 Mata Pelajaran Saya': ['siswa'],
    '📝 Tugas Saya': ['siswa'],
    '📋 Ujian Saya': ['siswa'],
    '📖 Materi Pelajaran Saya': ['siswa'],
    '💬 Forum Diskusi Saya': ['siswa'],
    '🎥 Meeting / Kelas Virtual Saya': ['siswa'],
    '📆 Kalender Akademik Siswa': ['siswa'],
    '📅 Jadwal Pelajaran Saya': ['siswa'],
    '👨‍🏫 Daftar Guru Saya': ['siswa'],
    '📄 Rapor Saya': ['siswa'],
    '💳 Pembayaran & Tagihan': ['siswa'],
    '📝 Pengajuan Izin': ['siswa'],
    // Guru-specific entries
    '🏠 Dashboard Guru': ['guru_pengajar'],
    '📅 Jadwal Mengajar': ['guru_pengajar'],
    '📋 Semua Kelas Saya': ['guru_pengajar'],
    '🎓 LMS Guru — Beranda': ['guru_pengajar'],
    '📖 Kelola Materi (Guru)': ['guru_pengajar'],
    '📝 Kelola Tugas (Guru)': ['guru_pengajar'],
    '✏️ Kelola Latihan (Guru)': ['guru_pengajar'],
    '📋 Kelola Ujian (Guru)': ['guru_pengajar'],
    '💬 Kelola Forum Diskusi (Guru)': ['guru_pengajar'],
    '🎥 Kelola Meeting / Kelas Virtual (Guru)': ['guru_pengajar'],
    '📊 Kelola Nilai Siswa (Guru)': ['guru_pengajar'],
    '✅ Koreksi Tugas (Guru)': ['guru_pengajar'],
    // Orang Tua-specific entries
    '🏠 Dashboard Orang Tua': ['orang_tua'],
    '✅ Presensi Anak': ['orang_tua'],
    '📝 Ajukan Izin Anak': ['orang_tua'],
    '📋 Riwayat Izin Anak': ['orang_tua'],
    '💳 Tagihan & Pembayaran Anak': ['orang_tua'],
    '🧾 Invoice / Bukti Pembayaran': ['orang_tua'],
    '📄 Rapor Anak': ['orang_tua'],
    '📊 Nilai Anak': ['orang_tua'],
    '🎓 LMS Anak (Informasi)': ['orang_tua'],
    // Wali Kelas-specific entries
    '🏠 Dashboard Wali Kelas': ['wali_kelas'],
    '🏫 Pilih / Ganti Kelas': ['wali_kelas'],
    '📅 Jadwal Pelajaran Kelas': ['wali_kelas'],
    '✅ Input Presensi Harian': ['wali_kelas'],
    '✅ Validasi Izin Siswa': ['wali_kelas'],
    '📅 Rekap Presensi Harian': ['wali_kelas'],
    '📋 Riwayat & Edit Presensi': ['wali_kelas'],
    '📊 Nilai Siswa (Wali Kelas)': ['wali_kelas'],
    '📄 Kelola Rapor': ['wali_kelas'],
    '📥 Request Download Rapor': ['wali_kelas'],
    '📈 Prediksi Kenaikan Kelas': ['wali_kelas'],
    '🔓 Validasi Akses Ujian / Rapor': ['wali_kelas','bendahara'],
    // Bendahara-specific entries
    '🏠 Dashboard Bendahara': ['bendahara'],
    '🧾 Kelola Tagihan Sekolah': ['bendahara'],
    '💳 Kelola Pembayaran': ['bendahara'],
    '⚙️ Config Pembayaran': ['bendahara'],
    '🎫 Validasi Dispensasi': ['bendahara'],
    '📊 Laporan Pembayaran': ['bendahara'],
    '📊 Rekap Tagihan': ['bendahara'],
    '⚠️ Siswa Belum Lunas': ['bendahara'],
    // Ketua-specific entries
    '🏠 Dashboard Ketua PKBM': ['ketua_pkbm'],
    '✅ Approval Dispensasi': ['ketua_pkbm'],
    '✅ Validasi Rapor': ['ketua_pkbm'],
    '📊 Monitoring SDM & Siswa': ['ketua_pkbm','wakil_kepala_sekolah'],
    '📄 Laporan & Kirim Catatan': ['ketua_pkbm'],
    // Sekretaris-specific entries
    '🏠 Dashboard Sekretaris': ['sekretaris'],
    '📅 Kelola Kalender Akademik': ['sekretaris'],
    '📢 Kelola Pengumuman & Flyer': ['sekretaris'],
    '📰 Kelola Berita / Artikel': ['sekretaris'],
    // Waka-specific entries
    '🏠 Dashboard Waka': ['wakil_kepala_sekolah'],
    '📚 Manajemen Akademik (Tahun Ajaran, Mapel, Kelas)': ['wakil_kepala_sekolah'],
    '👥 Manajemen SDM & Siswa': ['wakil_kepala_sekolah'],
    '📅 Kelola Jadwal Pelajaran': ['wakil_kepala_sekolah'],
    '📈 Pengaturan KKM & Kenaikan Kelas': ['wakil_kepala_sekolah'],
    '📊 Monitoring & Catatan': ['wakil_kepala_sekolah'],
};

function isEntryAllowedForRole(entry) {
    const filter = KB_ROLE_FILTER[entry.title];
    if (filter === null) return true; // universal
    if (filter === undefined) return USER_ROLE === 'admin'; // not in map = admin-only
    return filter.includes(USER_ROLE); // check role list
}

const TYPO_MAP = {
    // ═══ Password & Login ═══
    'pasword':'password','pword':'password','passwod':'password','passowrd':'password',
    'paswodr':'password','passwrd':'password','paswrod':'password','psword':'password',
    'passwordd':'password','passwor':'password','pasword':'password',
    'lgoin':'login','lgin':'login','logn':'login','logiin':'login','logi':'login',
    'mausk':'masuk','msuk':'masuk','masukk':'masuk',
    // ═══ Keuangan ═══
    'pembayran':'pembayaran','pmbayaran':'pembayaran','bayran':'bayaran',
    'tagiihan':'tagihan','tagian':'tagihan','tagihn':'tagihan','tgihan':'tagihan',
    'keungan':'keuangan','kuangan':'keuangan','keuangn':'keuangan',
    // ═══ Presensi & Absensi ═══
    'presensei':'presensi','presnsi':'presensi','presenssi':'presensi',
    'abesnsi':'absensi','abensi':'absensi','absesni':'absensi',
    // ═══ Rapor ═══
    'rapot':'rapor','raport':'rapor','repor':'rapor','rapoor':'rapor','rappor':'rapor',
    // ═══ Jadwal ═══
    'jadal':'jadwal','jadwl':'jadwal','jadwla':'jadwal','jadwaal':'jadwal',
    // ═══ Ujian ═══
    'ujiaan':'ujian','ujin':'ujian','ujain':'ujian','ujina':'ujian',
    // ═══ Siswa & Guru ═══
    'siwa':'siswa','sisw':'siswa','siswaa':'siswa',
    'guuru':'guru','gurru':'guru',
    'kelsa':'kelas','kellas':'kelas','kels':'kelas',
    // ═══ Pelajaran & Mapel ═══
    'pelajran':'pelajaran','plajaran':'pelajaran','pelajaraan':'pelajaran',
    'mapeel':'mapel','maple':'mapel','mpel':'mapel',
    // ═══ Pengaturan ═══
    'pengatuaran':'pengaturan','pengturan':'pengaturan','pengatuaran':'pengaturan',
    // ═══ Notifikasi & Monitoring ═══
    'notifkasi':'notifikasi','notifksi':'notifikasi',
    'monitroing':'monitoring','monitorng':'monitoring','monitoing':'monitoring',
    // ═══ Dispensasi & Validasi ═══
    'dispenssi':'dispensasi','dispen':'dispensasi','dipensasi':'dispensasi','dispesasi':'dispensasi',
    'validsi':'validasi','valdasi':'validasi',
    // ═══ Laporan ═══
    'laporaran':'laporan','laporaan':'laporan','lapran':'laporan',
    // ═══ Penilaian & Koreksi ═══
    'penilain':'penilaian','penilian':'penilaian','penlaian':'penilaian',
    'korkesi':'koreksi','koreski':'koreksi','korekis':'koreksi',
    // ═══ Latihan & Tugas ═══
    'latian':'latihan','latihn':'latihan','latiahan':'latihan',
    'tugass':'tugas','tugaas':'tugas','tgas':'tugas',
    // ═══ Materi & Modul ═══
    'materri':'materi','matrei':'materi','matreri':'materi',
    // ═══ Dashboard ═══
    'dashbord':'dashboard','dashboad':'dashboard','dasbor':'dashboard','dashboar':'dashboard',
    // ═══ Forum & Meeting ═══
    'forrum':'forum','frum':'forum',
    'meetinng':'meeting','metin':'meeting',
    // ═══ Kalender ═══
    'kaleder':'kalender','kalander':'kalender','kalener':'kalender',
    // ═══ Role-specific typos ═══
    'pengumumn':'pengumuman','pngumuman':'pengumuman','pengumuman':'pengumuman',
    'akademk':'akademik','akdemik':'akademik',
    'berit':'berita','brita':'berita',
    'wkala':'waka','wakil kepesek':'wakil kepsek',
    'seketaris':'sekretaris','sekeretaris':'sekretaris','sekertaris':'sekretaris',
    'bendahra':'bendahara','bndahara':'bendahara',
    'kenaikan':'kenaikan','kenaikn':'kenaikan',
    // ═══ Misc common ═══
    'profill':'profil','proflie':'profil',
    'settin':'setting','setitng':'setting',
    'downlod':'download','donwload':'download',
    'uploaad':'upload','uplod':'upload',
    'proses':'proses','prses':'proses',
};

// ═══ Emoji → FontAwesome Icon Map ═══
const EMOJI_TO_FA = {
    '🏠':'<i class="fas fa-home"></i>','🌐':'<i class="fas fa-globe"></i>',
    '👥':'<i class="fas fa-users"></i>','👨‍🏫':'<i class="fas fa-chalkboard-teacher"></i>',
    '👨‍🎓':'<i class="fas fa-user-graduate"></i>','👪':'<i class="fas fa-users"></i>',
    '🔓':'<i class="fas fa-unlock-alt"></i>','⚙️':'<i class="fas fa-cog"></i>',
    '🤖':'<i class="fas fa-robot"></i>','📊':'<i class="fas fa-chart-bar"></i>',
    '🗓️':'<i class="fas fa-calendar-alt"></i>','🏢':'<i class="fas fa-building"></i>',
    '🏫':'<i class="fas fa-school"></i>','👩‍💼':'<i class="fas fa-user-tie"></i>',
    '📖':'<i class="fas fa-book"></i>','📅':'<i class="fas fa-calendar-week"></i>',
    '🎓':'<i class="fas fa-graduation-cap"></i>','💰':'<i class="fas fa-coins"></i>',
    '💳':'<i class="fas fa-credit-card"></i>','🎫':'<i class="fas fa-ticket-alt"></i>',
    '🔑':'<i class="fas fa-key"></i>','📏':'<i class="fas fa-ruler"></i>',
    '📆':'<i class="fas fa-calendar"></i>','📢':'<i class="fas fa-bullhorn"></i>',
    '📰':'<i class="fas fa-newspaper"></i>','🖼️':'<i class="fas fa-images"></i>',
    '📈':'<i class="fas fa-chart-line"></i>','📄':'<i class="fas fa-file-alt"></i>',
    '📝':'<i class="fas fa-edit"></i>','👤':'<i class="fas fa-user"></i>',
    '🚪':'<i class="fas fa-sign-out-alt"></i>','🔔':'<i class="fas fa-bell"></i>',
    '✅':'<i class="fas fa-check-circle" style="color:#10b981"></i>',
    '📋':'<i class="fas fa-clipboard-list"></i>','📚':'<i class="fas fa-book-open"></i>',
    '💬':'<i class="fas fa-comments"></i>','🎥':'<i class="fas fa-video"></i>',
    '📥':'<i class="fas fa-file-import"></i>','☕':'<i class="fas fa-coffee"></i>',
    '⚠️':'<i class="fas fa-exclamation-triangle" style="color:#f59e0b"></i>',
    '👋':'<i class="far fa-hand-paper"></i>','🎯':'<i class="fas fa-bullseye"></i>',
    '❌':'<i class="fas fa-times-circle" style="color:#ef4444"></i>',
    '✏️':'<i class="fas fa-pencil-ruler"></i>','🧾':'<i class="fas fa-receipt"></i>',
};
function replaceEmojis(text) {
    if (!text) return text;
    for (const [emoji, icon] of Object.entries(EMOJI_TO_FA)) {
        text = text.replaceAll(emoji, icon);
    }
    return text;
}

function normalizeQuery(text) {
    let q = text.toLowerCase().trim();
    // Remove common punctuation to ensure clean word boundaries
    q = q.replace(/[.,!?()[\]{}"':;]/g, ' ');
    
    // Sort typo keys by length descending to match longer phrases first
    const sortedEntries = Object.entries(TYPO_MAP).sort((a, b) => b[0].length - a[0].length);
    for (const [typo, fix] of sortedEntries) {
        // Use word boundary for all typos to prevent partial replacements
        q = q.replace(new RegExp('\\b' + typo.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\b', 'gi'), fix);
    }
    // Strip common filler words for better keyword matching
    const fillers = ['cara','bagaimana','gimana','dimana','dmn','apa itu','apakah','tolong','bisa','mohon','mau','ingin','saya','aku','menu','fitur','yang','dan','atau','di','ke','dari','untuk','dengan','ini','itu','nya'];
    for (const f of fillers) {
        q = q.replace(new RegExp('\\b' + f + '\\b', 'gi'), ' ');
    }
    q = q.replace(/\s+/g, ' ').trim();
    return q;
}

function findSystemAnswer(query) {
    if (!query || query.trim().length < 2) return null;
    const q = normalizeQuery(query);
    const qWords = q.split(/\s+/).filter(w => w.length >= 2);
    let bestMatch = null;
    let bestScore = 0;
    let suggestions = []; // for partial matches

    for (const entry of SYSTEM_KB) {
        if (!isEntryAllowedForRole(entry)) continue;

        let entryMaxScore = 0;

        for (const kw of entry.kw) {
            let kwScore = 0;
            const nkw = kw.toLowerCase();
            const kwWords = nkw.split(/\s+/);

            // Full keyword match in query
            if (q.includes(nkw)) {
                kwScore += nkw.length + 2;
                if (q === nkw) kwScore += 15;
            }

            // Single word matching: strict array includes, not substring
            let wordMatches = 0;
            for (const w of kwWords) {
                if (w.length >= 3 && qWords.includes(w)) {
                    wordMatches++;
                    kwScore += 1; 
                }
            }
            if (wordMatches >= 2) kwScore += wordMatches * 2;

            // Reverse check: strict array includes
            for (const qw of qWords) {
                if (qw.length >= 3 && kwWords.includes(qw)) {
                    kwScore += 1;
                }
            }

            if (kwScore > entryMaxScore) {
                entryMaxScore = kwScore;
            }
        }

        // Also check title (without emoji) for partial matches
        const titleClean = entry.title.replace(/[^\w\s]/gi, '').toLowerCase().trim();
        const titleWords = titleClean.split(/\s+/);
        for (const qw of qWords) {
            if (qw.length >= 3 && titleWords.includes(qw)) {
                entryMaxScore += 2;
            }
        }

        if (entryMaxScore > bestScore) {
            bestScore = entryMaxScore;
            bestMatch = entry;
        }

        // Collect suggestions for partial matches (score 1-3)
        if (entryMaxScore >= 1 && entryMaxScore < 4) {
            // Check if entry already exists in suggestions to prevent duplicates
            if (!suggestions.some(s => s.entry.title === entry.title)) {
                suggestions.push({ entry, score: entryMaxScore });
            }
        }
    }

    // Strong match → return directly
    if (bestScore >= 4) return bestMatch;

    // Weak match (1-3) → return suggestions instead of falling back to LLM
    if (suggestions.length > 0) {
        suggestions.sort((a, b) => b.score - a.score);
        const topSuggestions = suggestions.slice(0, 6);
        return {
            _isSuggestion: true,
            title: '<i class="fas fa-search"></i> Mungkin yang Anda maksud:',
            text: 'Saya menemukan beberapa topik yang mungkin sesuai. Pilih salah satu:',
            suggestions: topSuggestions.map(s => ({
                title: s.entry.title,
                kw: s.entry.kw[0] || s.entry.title,
            })),
        };
    }

    return null;
}

// Render suggestion list (partial match)
function renderSuggestionAnswer(result) {
    let h = '<div style="font-size:13.5px;line-height:1.6">';
    h += '<div style="font-weight:700;color:#4361ee;margin-bottom:8px;font-size:14px">' + result.title + '</div>';
    h += '<div style="margin-bottom:10px;color:#334155">' + result.text + '</div>';
    h += '<div style="display:flex;flex-wrap:wrap;gap:6px">';
    for (const s of result.suggestions) {
        const label = replaceEmojis(s.title);
        h += '<button onclick="sendQuickAction(\'' + escapeHtml(s.kw) + '\')" style="background:#eff6ff;color:#4361ee;border:1px solid #bfdbfe;border-radius:12px;padding:6px 14px;font-size:12px;cursor:pointer;font-weight:500;text-align:left">' + label + '</button>';
    }
    h += '</div>';
    h += '</div>';
    return h;
}

function renderSystemAnswer(entry) {
    // Determine if current user is the owner of this feature
    const isOwnerRole = !entry.owner || entry.owner === USER_ROLE || USER_ROLE === entry.owner;
    const isAdmin = USER_ROLE === 'admin';
    const showAdminAlt = entry.owner && !isOwnerRole && entry.adminLink;

    let h = '<div style="font-size:13.5px;line-height:1.6">';
    h += '<div style="font-weight:700;color:#4361ee;margin-bottom:8px;font-size:14px">' + replaceEmojis(entry.title) + '</div>';
    h += '<div style="margin-bottom:8px;color:#334155">' + replaceEmojis(entry.text) + '</div>';
    if (entry.steps && entry.steps.length > 0) {
        h += '<ol style="padding-left:18px;margin:8px 0;color:#475569">';
        for (const s of entry.steps) h += '<li style="margin-bottom:4px">' + replaceEmojis(s) + '</li>';
        h += '</ol>';
    }
    if (entry.note) {
        h += '<div style="margin-top:10px;padding:8px 12px;background:#fef3c7;border-left:3px solid #f59e0b;border-radius:0 8px 8px 0;font-size:12px;color:#92400e">' + replaceEmojis(entry.note) + '</div>';
    }

    // ═══ Role-aware link rendering ═══
    if (showAdminAlt) {
        // Show admin warning + admin alternative link
        h += '<div style="margin-top:10px;padding:8px 12px;background:#fef2f2;border-left:3px solid #ef4444;border-radius:0 8px 8px 0;font-size:12px;color:#991b1b"><i class="fas fa-info-circle" style="margin-right:4px"></i>' + entry.adminNote + '</div>';
        h += '<a href="' + entry.adminLink + '" style="display:inline-flex;align-items:center;gap:6px;background:linear-gradient(135deg,#4361ee,#3a0ca3);color:white;padding:7px 14px;border-radius:20px;font-size:12.5px;font-weight:600;text-decoration:none;margin-top:10px;box-shadow:0 2px 8px rgba(67,97,238,0.3)"><i class="fas fa-arrow-right"></i> ' + (entry.adminLinkText || 'Buka Halaman') + '</a>';
    } else if (entry.link) {
        // Show direct link (user is the owner role)
        h += '<a href="' + entry.link + '" style="display:inline-flex;align-items:center;gap:6px;background:linear-gradient(135deg,#4361ee,#3a0ca3);color:white;padding:7px 14px;border-radius:20px;font-size:12.5px;font-weight:600;text-decoration:none;margin-top:10px;box-shadow:0 2px 8px rgba(67,97,238,0.3)"><i class="fas fa-arrow-right"></i> ' + (entry.linkText || 'Buka Halaman') + '</a>';
    }

    if (entry.related && entry.related.length > 0) {
        h += '<div style="margin-top:12px;padding-top:10px;border-top:1px solid #e2e8f0;display:flex;flex-wrap:wrap;gap:6px;align-items:center">';
        h += '<span style="font-size:11px;color:#64748b"><i class="fas fa-link" style="margin-right:3px"></i>Terkait:</span>';
        for (const r of entry.related) {
            h += '<button onclick="sendQuickAction(\'' + r + '\')" style="background:#eff6ff;color:#4361ee;border:1px solid #bfdbfe;border-radius:12px;padding:3px 10px;font-size:11.5px;cursor:pointer;font-weight:500">' + r + '</button>';
        }
        h += '</div>';
    }
    h += '</div>';
    return h;
}

// ==================== Initialize Chatbot ====================
function initChatbot() {
    loadConversationsFromStorage();
    restoreChatState();
    setupEventListeners();
    initDraggableFab();
    console.log('SIPADUHOK Chatbot initialized (System KB + LLM fallback)');
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
    // Only CLOSE the sidebar (never auto-open it) — sidebar may have been opened by "+ New Chat" button
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
        addMessage(msg.role, msg.content, null, false, msg.isHtml || false);
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
        container.innerHTML = '<p class="text-center text-muted" style="font-size: 12px; margin-top: 20px;">Belum ada conversation</p>';
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
        <div class="conversation-item ${isActive ? 'active' : ''}" onclick="loadConversation('${conv.id}')" data-conv-id="${conv.id}">
            <div class="conversation-title">${escapeHtml(title)}</div>
            <div class="conversation-date">${timeStr}</div>
            <button class="conversation-delete" onclick="deleteConversation('${conv.id}', event)" title="Delete">
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
            <div class="message-avatar"><i class="fas fa-headset" style="font-size:14px"></i></div>
            <div class="message-content">
                <div class="message-bubble">
                    <i class="far fa-hand-paper" style="color:#f59e0b"></i> Halo <strong>{{ auth()->user()->name ?? 'User' }}</strong>! Saya <strong>Asisten SIPADUHOK</strong>.<br>
                    Tanyakan apa saja tentang sistem ini — saya tahu seluk-beluknya! <i class="fas fa-bullseye" style="color:#ef4444"></i><br>
                    <small style="color:#64748b">Pertanyaan di luar sistem akan dijawab oleh AI LLM otomatis.</small>
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
            { id: 'llama-3.3-70b-versatile', name: 'Llama 3.3 70B (Recommended)', provider: 'groq', supports_vision: false, supports_pdf: false, default: true },
            { id: 'llama-3.1-8b-instant', name: 'Llama 3.1 8B (Fastest)', provider: 'groq', supports_vision: false, supports_pdf: false, default: false },
            { id: 'meta-llama/llama-4-scout-17b-16e-instruct', name: 'Llama 4 Scout (Vision)', provider: 'groq', supports_vision: true, supports_pdf: false, default: false }
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
    const groqDefaultModel = models.find(m => m.id === 'llama-3.3-70b-versatile');
    const forcedDefaultId = groqDefaultModel ? 'llama-3.3-70b-versatile' : models[0]?.id;
    selector.innerHTML = models.map(m => {
        let icons = '';
        if (m.supports_vision) icons += '📷';
        if (m.supports_pdf) icons += '📄';
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
            // No conversations at all — create a fresh one
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
        `<button class="quick-action-btn" onclick="sendQuickAction('${escapeHtml(a.t)}')"><i class="fas ${a.i}" style="margin-right:5px;font-size:11px;opacity:0.8"></i>${a.t}</button>`
    ).join('');
    chatbotState.quickActionsLoaded = true;
}

function renderQuickActions(actions) {
    const container = document.getElementById('quickActionsContainer');
    if (!container || !actions || actions.length === 0) return;
    container.innerHTML = actions.map(action =>
        `<button class="quick-action-btn" onclick="sendQuickAction('${escapeHtml(action)}')">${action}</button>`
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
                showToastChatbot('info', `🔄 PDF hanya support Gemini 2.5 Flash. Model beralih otomatis.`);
            } else if (!geminiModel) {
                showToastChatbot('error', 'PDF memerlukan Gemini 2.5 Flash. Hubungi admin untuk konfigurasi API key.');
            }
        } else if (hasImage && selectedModel && !selectedModel.supports_vision) {
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
                    <img src="${fileObj.dataUrl}" alt="${fileObj.file.name}" onclick="openLightbox('${fileObj.dataUrl}')">
                    <button class="attachment-remove-btn" onclick="removeAttachmentByIndex(${index})"><i class="fas fa-times"></i></button>
                </div>`;
        } else {
            return `
                <div class="attachment-preview-item file-preview">
                    <i class="fas fa-file-pdf"></i>
                    <button class="attachment-remove-btn" onclick="removeAttachmentByIndex(${index})"><i class="fas fa-times"></i></button>
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

    // ═══ SYSTEM CHATBOT: check knowledge base FIRST (if no file attachments) ═══
    if (currentFiles.length === 0 && message) {
        const kbMatch = findSystemAnswer(message);
        if (kbMatch) {
            // System mode: hide model selector, show badge
            document.getElementById('modelSelector').style.display = 'none';
            document.getElementById('systemModeBadge').style.display = 'inline-flex';
            showTypingIndicator();
            chatbotState.isWaitingResponse = true;
            await new Promise(r => setTimeout(r, 300 + Math.random() * 400));
            chatbotState.isWaitingResponse = false;
            hideTypingIndicator();

            // Handle suggestion mode vs direct answer
            if (kbMatch._isSuggestion) {
                addMessage('assistant', renderSuggestionAnswer(kbMatch), null, true, true);
            } else {
                addMessage('assistant', renderSystemAnswer(kbMatch), null, true, true);
            }
            saveCurrentConversation();
            scrollToBottom();
            return; // answered by system — no LLM API call
        }
    }

    // ═══ No KB match → fallback to LLM API ═══
    if (!IS_LLM_MODE_ENABLED) {
        document.getElementById('modelSelector').style.display = 'none';
        document.getElementById('systemModeBadge').style.display = 'inline-flex';
        showTypingIndicator();
        chatbotState.isWaitingResponse = true;
        await new Promise(r => setTimeout(r, 400 + Math.random() * 500));
        chatbotState.isWaitingResponse = false;
        hideTypingIndicator();
        
        const noLlmResponse = '<div style="font-size:13.5px;line-height:1.6">'
            + '<div style="font-weight:700;color:#f59e0b;margin-bottom:6px"><i class="fas fa-info-circle" style="margin-right:4px"></i> Di Luar Konteks Sistem</div>'
            + '<div style="color:#475569;margin-bottom:8px">Maaf, Asisten Sistem saat ini diatur untuk hanya melayani pertanyaan seputar navigasi dan fitur SIPADUHOK.</div>'
            + '<div style="color:#475569;">Untuk pertanyaan pengetahuan umum di luar sistem, fitur <strong>AI Generatif</strong> sedang dinonaktifkan oleh Administrator.</div>'
            + '</div>';
            
        addMessage('assistant', noLlmResponse, null, true, true);
        saveCurrentConversation();
        scrollToBottom();
        return;
    }

    // LLM mode: show model selector, hide badge
    document.getElementById('modelSelector').style.display = '';
    document.getElementById('systemModeBadge').style.display = 'none';
    showTypingIndicator();
    chatbotState.isWaitingResponse = true;
    const response = await sendMessageToApi(message, currentFiles);
    chatbotState.isWaitingResponse = false;
    hideTypingIndicator();
    if (response.success) {
        addMessage('assistant', response.response);
    } else {
        // Show user-friendly error message instead of raw JSON
        let errorMsg = response.error || 'Terjadi kesalahan tidak diketahui.';
        let friendlyMsg = '<div style="font-size:13.5px;line-height:1.6">'
            + '<div style="font-weight:700;color:#ef4444;margin-bottom:6px"><i class="fas fa-exclamation-triangle" style="margin-right:4px"></i> AI sedang tidak tersedia</div>'
            + '<div style="color:#475569;margin-bottom:8px">Server AI sedang mengalami gangguan. Silakan coba lagi dalam beberapa saat.</div>';
        // If it's a 503/overload error, show specific advice
        if (errorMsg.includes('503') || errorMsg.includes('UNAVAILABLE') || errorMsg.includes('high demand') || errorMsg.includes('overloaded')) {
            friendlyMsg += '<div style="padding:8px 12px;background:#fef3c7;border-left:3px solid #f59e0b;border-radius:0 8px 8px 0;font-size:12px;color:#92400e"><i class="fas fa-info-circle" style="margin-right:4px"></i> Server AI sedang kelebihan beban. Coba lagi dalam 1-2 menit atau ganti model AI di pengaturan.</div>';
        }
        friendlyMsg += '</div>';
        addMessage('assistant', friendlyMsg, null, true, true);
    }
    saveCurrentConversation();
    scrollToBottom();
}

// ==================== Send Message to API ====================
async function sendMessageToApi(message, attachedFiles) {
    const formData = new FormData();
    formData.append('message', message);
    formData.append('model', chatbotState.selectedModel);
    // Filter HTML from conversation history before sending to LLM
    // System KB answers contain HTML which bloats the payload and confuses LLM
    const cleanHistory = chatbotState.conversationHistory
        .filter(msg => !msg.isHtml) // Skip system HTML responses entirely
        .slice(-10) // Keep last 10 text-only messages
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
            chatbotState.conversationHistory.push(
                { role: 'user', content: message },
                { role: 'assistant', content: data.response }
            );
            if (chatbotState.conversationHistory.length > 20) {
                chatbotState.conversationHistory = chatbotState.conversationHistory.slice(-20);
            }
        } else if (data.switch_to_gemini) {
            const geminiModel = chatbotState.availableModels.find(m => m.id === 'gemini-2.5-flash');
            if (geminiModel) {
                chatbotState.selectedModel = geminiModel.id;
                const selector = document.getElementById('modelSelector');
                if (selector) selector.value = geminiModel.id;
                showToastChatbot('info', '🔄 Beralih ke Gemini untuk membaca PDF. Mengirim ulang...');
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
function addMessage(role, content, attachments = null, saveToHistory = true, isHtml = false) {
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
    const bubbleContent = isHtml ? content : escapeHtml(content);
    const avatarIcon = !isUser ? '<div class="message-avatar"><i class="fas fa-headset" style="font-size:14px"></i></div>' : '';
    const messageHtml = `
        <div class="message-group ${isUser ? 'user-message' : 'ai-message'}">
            ${avatarIcon}
            <div class="message-content">
                ${attachmentsHtml}
                <div class="message-bubble">${bubbleContent}</div>
                <div class="message-meta">
                    <span class="message-time">${time}</span>
                    ${!isUser ? `<button class="btn-copy" onclick="copyMessage(this)" title="Copy"><i class="fas fa-copy"></i></button>` : ''}
                </div>
            </div>
            ${isUser ? '<div class="message-avatar"><i class="fas fa-user"></i></div>' : ''}
        </div>
    `;
    messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
    if (saveToHistory) {
        chatbotState.conversationHistory.push({ role, content, isHtml: isHtml || false });
    }
    scrollToBottom();
}

// ==================== Show/Hide Typing Indicator ====================
function showTypingIndicator() {
    const indicator = document.getElementById('typingIndicator');
    if (indicator) { indicator.classList.remove('d-none'); scrollToBottom(); }
}

function hideTypingIndicator() {
    const indicator = document.getElementById('typingIndicator');
    if (indicator) indicator.classList.add('d-none');
}

// ==================== Copy Message ====================
function copyMessage(button) {
    const messageBubble = button.closest('.message-content').querySelector('.message-bubble');
    if (!messageBubble) return;
    navigator.clipboard.writeText(messageBubble.textContent).then(() => {
        showToastChatbot('success', 'Pesan disalin');
    }).catch(() => {
        showToastChatbot('error', 'Gagal menyalin pesan');
    });
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

    // ── Click: fallback for desktop mouse clicks ──
    fab.addEventListener('click', function() {
        if (hasMoved) { hasMoved = false; return; }
        openChatWindow(); // Works for all modes: desktop large, desktop resized, real mobile (touchend suppresses synthetic click via e.preventDefault)
    });

    // ── Mouse drag (desktop only) ──
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

    // ── Touch: ALWAYS record start position ──
    fab.addEventListener('touchstart', function(e) {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
        if (isMobileView()) {
            // Mobile: touchend on FAB handles tap explicitly
            return;
        }
        // Desktop touch: setup drag
        isDraggingFab = true;
        hasMoved = false;
        startRight = chatbotState.fabPosition.right;
        fab.style.transition = 'none';
        e.preventDefault();
    }, { passive: false });

    // ── Mobile tap: touchend on FAB (reliable iOS + Android) ──
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

    // ── Desktop touch drag ──
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
</script>
