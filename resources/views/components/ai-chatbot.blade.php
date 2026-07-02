{{-- AI Chatbot General Assistant --}}
{{-- Floating Action Button + Chat Window --}}

@php
    $chatbotUserName = auth()->user()->name ?? 'User';
    $chatbotUserRole = auth()->user()->role ?? 'guest';
@endphp

{{-- Floating Action Button (FAB) - "Tanya AI" --}}
<button id="aiChatbotFab" class="ai-chatbot-fab">
    <i class="fas fa-headset"></i>
    <span class="fab-text">Bantuan</span>
</button>

{{-- Chat Window --}}
<div id="aiChatbotWindow" class="ai-chatbot-window"
    data-user-name="{{ $chatbotUserName }}"
    data-user-role="{{ $chatbotUserRole }}">
    {{-- Conversations Sidebar (Toggleable) --}}
    <div id="conversationsSidebar" class="conversations-sidebar">
        <div class="conversations-header">
            <h6 class="mb-0 fw-bold">Conversations</h6>
            <button class="btn btn-sm btn-icon-sidebar" data-chatbot-action="toggle-sidebar" title="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="conversations-body">
            {{-- New Chat Button --}}
            <button class="btn btn-primary w-100 mb-2 new-chat-btn" data-chatbot-action="new-conversation">
                <i class="fas fa-plus me-2"></i> New Chat
            </button>

            {{-- Clear All Button (for debugging) --}}
            <button class="btn btn-outline-danger w-100 mb-3 chatbot-clear-all-btn"
                data-chatbot-action="clear-conversations"
                title="Hapus semua conversation">
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
                <button class="btn btn-sm btn-icon-header me-2" data-chatbot-action="toggle-sidebar" title="Conversations">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="ai-logo"><i class="fas fa-headset ai-chatbot-icon-sm"></i></div>
                <span class="header-title">Asisten SIPADUHOK</span>
            </div>
            <div class="header-right">
                <button class="btn btn-sm btn-icon-header ms-2" data-chatbot-action="close" title="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        {{-- Chat Body (Messages) --}}
        <div id="chatMessages" class="chatbot-body">
            {{-- Welcome Message --}}
            <div class="message-group ai-message">
                <div class="message-avatar"><i class="fas fa-headset ai-chatbot-icon-sm"></i></div>
                <div class="message-content">
                    <div class="message-bubble">
                        <i class="far fa-hand-paper ai-chatbot-wave-icon"></i> Halo <strong>{{ $chatbotUserName }}</strong>! Saya <strong>Asisten SIPADUHOK</strong>.<br>
                        Butuh Bantuan? Silahkan Tanyakan
                    </div>
                </div>
            </div>

            {{-- Quick Actions (System-based, loaded inline) --}}
            <div id="quickActionsContainer" class="quick-actions-container"></div>

            {{-- Typing Indicator (hidden by default) --}}
            <div id="typingIndicator" class="typing-indicator d-none">
                <div class="message-avatar"><i class="fas fa-headset ai-chatbot-icon-sm"></i></div>
                <div class="typing-content">
                    <div class="typing-dots typing-dots-fit">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <div id="typingTimer" class="typing-timer">0.00 s</div>
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
                    <input type="file"
                        id="fileAttachment"
                        class="file-attachment-input"
                        accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf"
                        multiple>
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
                <button id="sendButton" class="btn-send" title="Send (Enter)">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            {{-- Footer Actions --}}
            <div class="footer-actions">
                {{-- conversationInfo removed: managed via conversation sidebar toggle --}}
                <span id="currentConversationTitle" class="current-conversation-title"></span>
                <span class="char-count">
                    <span id="charCount">0</span>/2000
                </span>
            </div>
        </div>
    </div>
</div>
{{-- Image Lightbox Modal --}}
<div id="imageLightbox" class="image-lightbox d-none" data-chatbot-action="close-lightbox">
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
            <button class="confirm-dialog-btn confirm-dialog-cancel" data-chatbot-action="cancel-confirm">
                Batal
            </button>
            <button class="confirm-dialog-btn confirm-dialog-confirm" data-chatbot-action="confirm">
                Hapus
            </button>
        </div>
    </div>
</div>
