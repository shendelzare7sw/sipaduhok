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
            <button class="btn btn-sm btn-icon" onclick="toggleConversationsSidebar()" title="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="conversations-body">
            {{-- New Chat Button --}}
            <button class="btn btn-primary w-100 mb-3" onclick="createNewConversation()">
                <i class="fas fa-plus me-2"></i> New Chat
            </button>

            {{-- Conversations List (loaded dynamically) --}}
            <div id="conversationsList"></div>
        </div>
    </div>

    {{-- Header --}}
    <div class="chatbot-header">
        <div class="header-left">
            {{-- Conversations Toggle Button --}}
            <button class="btn btn-sm btn-icon me-2" onclick="toggleConversationsSidebar()" title="Conversations">
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
            <button class="btn btn-sm btn-icon ms-2" onclick="closeChatWindow()" title="Close">
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
        <div id="attachmentsPreview" class="attachments-preview d-none">
            {{-- Files will be rendered here dynamically --}}
        </div>

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

{{-- Styles --}}
<style>
/* ==================== FAB (Floating Action Button) ==================== */
.ai-chatbot-fab {
    position: fixed;
    bottom: 24px;
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
    cursor: grab; /* NEW: Show draggable cursor */
    user-select: none; /* NEW: Prevent text selection during drag */
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
    cursor: grabbing; /* Show grabbing cursor when dragging */
}

.ai-chatbot-fab.hidden {
    display: none !important;
}

/* ==================== Chat Window ==================== */
.ai-chatbot-window {
    position: fixed;
    bottom: 24px;
    right: 24px;
    width: 450px;
    height: 650px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 12px 48px rgba(0, 0, 0, 0.15);
    z-index: 1060;
    display: none; /* Hidden by default */
    flex-direction: column;
    overflow: hidden;
}

.ai-chatbot-window.active {
    display: flex !important;
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

.btn-icon {
    background: transparent;
    border: none;
    padding: 4px 8px;
    cursor: pointer;
    color: #6b7280;
    transition: color 0.2s;
}

.btn-icon:hover {
    color: #3b82f6;
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

/* Message Attachment */
.message-attachment {
    background: rgba(0, 0, 0, 0.05);
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 13px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
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
}

.attachment-preview {
    background: #eff6ff;
    border-left: 3px solid #3b82f6;
    padding: 8px 12px;
    margin-bottom: 8px;
    border-radius: 6px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.preview-content {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #1f2937;
}

.btn-remove-attachment {
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 4px 8px;
    font-size: 14px;
    transition: color 0.2s;
}

.btn-remove-attachment:hover {
    color: #ef4444;
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
}

.btn-text {
    background: none;
    border: none;
    color: #666;
    font-size: 12px;
    cursor: pointer;
    padding: 4px 8px;
}

.btn-text:hover {
    color: #d32f2f;
}

.char-count {
    font-size: 12px;
    color: #999;
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
        width: 100%;
        height: 100%;
        bottom: 0;
        right: 0;
        top: 0;
        left: 0;
        border-radius: 0;
    }

    .chatbot-header {
        border-radius: 0;
    }

    .model-selector {
        max-width: 120px;
        font-size: 12px;
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
