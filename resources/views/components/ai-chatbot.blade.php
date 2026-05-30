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
                {{-- Model Switcher --}}
                <select id="modelSelector" class="model-selector form-select form-select-sm">
                    <option value="llama-3.3-70b-versatile">Memuat...</option>
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
                        Butuh Bantuan? Silahkan Tanyakan
                    </div>
                </div>
            </div>

            {{-- Quick Actions (System-based, loaded inline) --}}
            <div id="quickActionsContainer" class="quick-actions-container"></div>

            {{-- Typing Indicator (hidden by default) --}}
            <div id="typingIndicator" class="typing-indicator d-none">
                <div class="message-avatar"><i class="fas fa-headset" style="font-size:14px"></i></div>
                <div style="display: flex; flex-direction: column;">
                    <div class="typing-dots" style="width: fit-content;">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <div id="typingTimer" style="font-size: 11px; color: #64748b; font-weight: 600; margin-top: 4px; padding-left: 2px;">0.00 s</div>
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

/* ==================== Structured Response Template ==================== */
.chatbot-response-text {
    font-size: 13.5px;
    line-height: 1.6;
    color: #1f2937;
}
.chatbot-callout {
    margin-top: 10px;
    padding: 9px 12px;
    background: #fef3c7;
    border-left: 3px solid #f59e0b;
    border-radius: 0 8px 8px 0;
    font-size: 12px;
    color: #92400e;
    display: flex;
    gap: 8px;
    align-items: flex-start;
    line-height: 1.5;
}
.chatbot-callout i {
    margin-top: 2px;
    flex-shrink: 0;
}
.chatbot-cta-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 10px;
    padding: 9px 16px;
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    color: white !important;
    border-radius: 22px;
    font-size: 12.5px;
    font-weight: 600;
    text-decoration: none !important;
    box-shadow: 0 2px 8px rgba(67, 97, 238, 0.3);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.chatbot-cta-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(67, 97, 238, 0.4);
    color: white !important;
}
.chatbot-cta-btn i {
    font-size: 11px;
}
.chatbot-related-chips {
    margin-top: 12px;
    padding-top: 10px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    align-items: center;
}
.chatbot-related-label {
    font-size: 11px;
    color: #64748b;
    margin-right: 4px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.chatbot-chip {
    background: #eff6ff;
    color: #4361ee !important;
    border: 1px solid #bfdbfe;
    border-radius: 12px;
    padding: 4px 11px;
    font-size: 11.5px;
    font-weight: 500;
    text-decoration: none !important;
    transition: background 0.15s ease;
}
.chatbot-chip:hover {
    background: #dbeafe;
    color: #4361ee !important;
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

// Current user role (injected from Blade)
const USER_ROLE = '{{ auth()->user()->role ?? "guest" }}';

// ==================== Initialize Chatbot ====================
const CHATBOT_DATA_VERSION = '2';

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
                            return { role: msg.role, content: (tmp.textContent || '').trim() };
                        }
                        return { role: msg.role, content: msg.content };
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
        showChatError('⚠️ Menggunakan model default. Jika mengalami masalah, hubungi admin.');
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

    // ═══ Always send to LLM API (rule-based engine removed) ═══
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
                text: '⚠️ Fitur Tidak Tersedia\n' + errorMsg,
                callout: null,
                button: null,
                related: null,
            };
        } else if (errorMsg.includes('503') || errorMsg.includes('UNAVAILABLE') || errorMsg.includes('high demand') || errorMsg.includes('overloaded')) {
            errStructured = {
                text: '⚠️ Layanan sedang tidak tersedia\nServer sedang mengalami gangguan. Silakan coba lagi dalam beberapa saat.',
                callout: 'Server sedang kelebihan beban. Coba lagi dalam 1-2 menit atau ganti model AI di pengaturan.',
                button: null,
                related: null,
            };
        } else {
            errStructured = {
                text: '⚠️ Layanan sedang tidak tersedia\nServer sedang mengalami gangguan. Silakan coba lagi dalam beberapa saat.',
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
    const cleanHistory = chatbotState.conversationHistory
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
        chatbotState.conversationHistory.push({ role, content });
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
