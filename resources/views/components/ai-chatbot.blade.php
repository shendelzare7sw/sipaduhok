@php
    $chatbotUserName = auth()->user()->name ?? 'Pengguna';
    $chatbotUserRole = auth()->user()->role ?? 'guest';
@endphp

<div data-ai-chatbot>
    <button
        id="aiChatbotFab"
        type="button"
        class="fixed bottom-[118px] right-6 z-[1055] inline-flex h-12 w-12 touch-none select-none items-center justify-center gap-2.5 rounded-full border-0 bg-blue-500 p-0 text-sm font-semibold text-white shadow-lg shadow-blue-500/30 transition hover:-translate-y-0.5 hover:bg-blue-600 hover:shadow-xl active:cursor-grabbing min-[769px]:bottom-[76px] min-[769px]:right-[104px] min-[769px]:h-14 min-[769px]:w-[132px]"
        aria-label="Buka bantuan SIPADUHOK"
    >
        <i class="fas fa-headset" aria-hidden="true"></i>
        <span class="hidden min-[769px]:inline">Bantuan</span>
    </button>

    <section
        id="aiChatbotWindow"
        class="fixed inset-0 z-[1060] hidden min-w-0 flex-row overflow-hidden bg-white shadow-2xl md:inset-auto md:bottom-6 md:right-6 md:h-[min(650px,calc(100vh-48px))] md:w-[min(450px,calc(100vw-48px))] md:rounded-2xl"
        data-user-name="{{ $chatbotUserName }}"
        data-user-role="{{ $chatbotUserRole }}"
        role="dialog"
        aria-modal="true"
        aria-label="Asisten SIPADUHOK"
    >
        <aside id="conversationsSidebar" class="absolute inset-0 z-20 hidden min-w-0 flex-col border-r border-slate-200 bg-slate-50 md:relative md:inset-auto md:z-auto md:w-[260px] md:shrink-0">
            <header class="flex h-[66px] shrink-0 items-center justify-between border-b border-slate-200 bg-white px-4">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900">Riwayat percakapan</h2>
                    <p class="mt-0.5 text-[10px] text-slate-500">Tersimpan di perangkat ini</p>
                </div>
                <button type="button" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-blue-600" data-chatbot-action="toggle-sidebar" aria-label="Tutup riwayat">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </button>
            </header>
            <div class="min-h-0 flex-1 overflow-y-auto p-3">
                <button type="button" class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-3 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700" data-chatbot-action="new-conversation">
                    <i class="fas fa-plus" aria-hidden="true"></i> Percakapan baru
                </button>
                <button type="button" class="mt-2 inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-white px-3 text-xs font-bold text-red-600 transition hover:bg-red-50" data-chatbot-action="clear-conversations" title="Hapus semua percakapan">
                    <i class="fas fa-trash" aria-hidden="true"></i> Hapus semua riwayat
                </button>
                <div id="conversationsList" class="mt-4"></div>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col overflow-hidden">
            <header class="flex h-[66px] shrink-0 items-center justify-between border-b-2 border-blue-500 bg-white px-3 sm:px-4">
                <div class="flex min-w-0 items-center gap-2.5">
                    <button type="button" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-blue-600" data-chatbot-action="toggle-sidebar" aria-label="Buka riwayat percakapan">
                        <i class="fas fa-bars" aria-hidden="true"></i>
                    </button>
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm"><i class="fas fa-headset" aria-hidden="true"></i></span>
                    <span class="truncate text-sm font-extrabold text-slate-900 sm:text-base">Asisten SIPADUHOK</span>
                </div>
                <button type="button" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-slate-500 transition hover:bg-red-50 hover:text-red-600" data-chatbot-action="close" aria-label="Tutup bantuan">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </button>
            </header>

            <div id="chatMessages" class="flex min-h-0 flex-1 flex-col gap-4 overflow-y-auto bg-slate-50 p-3 sm:p-4" aria-live="polite">
                <div data-chatbot-message class="flex items-start gap-2.5">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs text-white"><i class="fas fa-headset" aria-hidden="true"></i></span>
                    <div class="max-w-[82%] rounded-2xl rounded-bl bg-white px-4 py-3 text-sm leading-6 text-slate-700 shadow-sm ring-1 ring-slate-200/80">
                        <i class="far fa-hand-paper text-amber-500" aria-hidden="true"></i>
                        Halo <strong>{{ $chatbotUserName }}</strong>! Saya <strong>Asisten SIPADUHOK</strong>.<br>
                        Butuh bantuan? Silakan tanyakan.
                    </div>
                </div>

                <div id="quickActionsContainer" class="mt-1 flex flex-wrap gap-2"></div>

                <div id="typingIndicator" class="hidden items-center gap-2.5">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs text-white"><i class="fas fa-headset" aria-hidden="true"></i></span>
                    <div class="flex flex-col">
                        <div class="flex w-fit gap-1 rounded-2xl bg-white px-4 py-3 shadow-sm ring-1 ring-slate-200/80">
                            <span class="h-2 w-2 animate-bounce rounded-full bg-blue-500"></span>
                            <span class="h-2 w-2 animate-bounce rounded-full bg-blue-500 [animation-delay:150ms]"></span>
                            <span class="h-2 w-2 animate-bounce rounded-full bg-blue-500 [animation-delay:300ms]"></span>
                        </div>
                        <span id="typingTimer" class="mt-1 pl-0.5 text-[10px] font-semibold text-slate-500">0.00 s</span>
                    </div>
                </div>
            </div>

            <footer class="shrink-0 border-t border-slate-200 bg-white px-3 py-3 sm:px-4">
                <div id="attachmentsPreview" class="mb-2 hidden flex-wrap gap-2 rounded-xl bg-slate-50 p-2"></div>

                <div class="flex items-end gap-2">
                    <label for="fileAttachment" class="flex h-10 w-10 shrink-0 cursor-pointer items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-slate-500 transition hover:border-blue-200 hover:text-blue-600" title="Lampirkan maksimal 5 gambar atau PDF">
                        <i class="fas fa-paperclip" aria-hidden="true"></i>
                        <input type="file" id="fileAttachment" class="hidden" accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf" multiple>
                    </label>
                    <label class="sr-only" for="chatInput">Pesan untuk asisten</label>
                    <textarea id="chatInput" class="max-h-[120px] min-h-10 min-w-0 flex-1 resize-none rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10" placeholder="Ketik pesan..." rows="1" maxlength="2000"></textarea>
                    <button id="sendButton" type="button" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-slate-300" title="Kirim (Enter)" aria-label="Kirim pesan">
                        <i class="fas fa-paper-plane" aria-hidden="true"></i>
                    </button>
                </div>

                <div class="mt-2 flex items-center justify-end gap-2">
                    <span id="currentConversationTitle" class="hidden"></span>
                    <span class="text-[10px] font-medium text-slate-400"><span id="charCount">0</span>/2000</span>
                </div>
            </footer>
        </div>
    </section>

    <div id="imageLightbox" class="fixed inset-0 z-[2000] hidden cursor-zoom-out items-center justify-center bg-slate-950/95 p-6" data-chatbot-action="close-lightbox" role="dialog" aria-modal="true" aria-label="Pratinjau lampiran">
        <button type="button" class="absolute right-5 top-5 flex h-11 w-11 items-center justify-center rounded-full bg-white/10 text-xl text-white backdrop-blur transition hover:bg-white/20" aria-label="Tutup pratinjau">&times;</button>
        <img id="lightboxImage" class="max-h-[90vh] max-w-[90vw] object-contain" src="" alt="Pratinjau lampiran">
    </div>
</div>
