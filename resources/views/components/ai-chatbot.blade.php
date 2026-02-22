{{-- AI Chatbot General Assistant --}}
{{-- Floating Action Button + Chat Window --}}

{{-- Floating Action Button (FAB) - "Tanya AI" --}}
<button id="aiChatbotFab" class="ai-chatbot-fab">
    <i class="fas fa-comment-dots me-2"></i>
    <span class="fab-text">Tanya AI</span>
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
                <div class="ai-logo">AI</div>
                <span class="header-title">Assistant</span>
            </div>
            <div class="header-right">
                {{-- Model Switcher Dropdown --}}
                <select id="modelSelector" class="model-selector form-select form-select-sm">
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
                <div class="message-avatar">AI</div>
                <div class="message-content">
                    <div class="message-bubble">
                        Halo! 👋 Saya asisten AI SIPADUHOK. Ada yang bisa saya bantu?
                    </div>
                </div>
            </div>

            {{-- Quick Actions Container (loaded via AJAX) --}}
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
                <small class="text-muted" id="conversationInfo">
                    <i class="fas fa-comment-dots me-1"></i> <span id="currentConversationTitle">New Chat</span>
                </small>
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
    right: 96px; /* LEFT of scroll-up button (24px + 56px + 16px gap) */
    height: 56px;
    padding: 0 24px;
    border-radius: 28px;
    z-index: 1055;
    background: #3b82f6;
    color: white;
    border: none;
    font-weight: 600;
    font-size: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 16px rgba(59, 130, 246, 0.3);
    transition: all 0.3s ease;
    cursor: grab;
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
    .ai-chatbot-fab {
        height: 48px;
        padding: 0 16px;
        font-size: 14px;
        right: 80px;
    }

    .fab-text {
        display: none;
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

{{-- Include JavaScript --}}
<script src="{{ asset('js/ai-chatbot.js') }}"></script>

{{-- Initialize on page load --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof initChatbot === 'function') {
            initChatbot();
        }
    });
</script>
