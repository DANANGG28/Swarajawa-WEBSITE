<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tanya Basa AI - Sinau Jowo Web</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:ital,wght@0,400..800;1,400..800&family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
              "surface-container-low": "#f5f2ff",
              "yellow-300": "#F6D98B",
              "secondary-fixed-dim": "#ffb2bf",
              "primary-fixed-dim": "#c6bfff",
              "tertiary-fixed": "#ffdcc4",
              "primary-700": "#5443C9",
              "surface-bright": "#fcf8ff",
              "surface-container": "#efecfd",
              "on-error-container": "#93000a",
              "surface-variant": "#e3e0f1",
              "primary-fixed": "#e4dfff",
              "green-500": "#4CAF6D",
              "orange-300": "#F7B98A",
              "on-secondary-container": "#792d40",
              "black-900": "#1E1E2A",
              "on-surface-variant": "#474554",
              "on-primary": "#ffffff",
              "on-tertiary": "#ffffff",
              "secondary": "#964356",
              "surface-container-lowest": "#ffffff",
              "on-primary-fixed": "#160066",
              "gray-50": "#F7F7FA",
              "inverse-on-surface": "#f2efff",
              "surface-container-high": "#e9e6f7",
              "primary-600": "#6C5CE8",
              "primary-500": "#7B6CF0",
              "surface-dim": "#dbd8e9",
              "on-tertiary-container": "#ffc59b",
              "tertiary-fixed-dim": "#f8ba8b",
              "primary-container": "#5443c9",
              "on-secondary-fixed": "#3f0016",
              "gray-500": "#8A8A9A",
              "primary": "#3c25b1",
              "on-primary-fixed-variant": "#402cb5",
              "outline": "#787585",
              "pink-100": "#FBD9DE",
              "background": "#fcf8ff",
              "orange-500": "#F0955A",
              "gray-200": "#E4E4EC",
              "inverse-surface": "#2f2f3c",
              "inverse-primary": "#c6bfff",
              "on-background": "#1a1a26",
              "on-secondary": "#ffffff",
              "error": "#ba1a1a",
              "primary-400": "#A79BFF",
              "pink-500": "#F08CA0",
              "surface": "#fcf8ff",
              "outline-variant": "#c8c4d6",
              "error-container": "#ffdad6",
              "on-surface": "#1a1a26",
              "color-white": "#FFFFFF",
              "on-primary-container": "#d2cbff",
              "tertiary-container": "#7d4f29",
              "on-tertiary-fixed": "#2f1500",
              "on-secondary-fixed-variant": "#792c3f",
              "tertiary": "#623814",
              "on-error": "#ffffff",
              "on-tertiary-fixed-variant": "#673d18",
              "surface-container-highest": "#e3e0f1",
              "secondary-fixed": "#ffd9de",
              "surface-tint": "#5948ce",
              "secondary-container": "#ff98ac"
            },
            "borderRadius": {
              "DEFAULT": "0.25rem",
              "lg": "0.5rem",
              "xl": "0.75rem",
              "full": "9999px"
            },
            "fontFamily": {
              "caption": ["Manrope"],
              "heading": ["Epilogue"],
              "stat-number-sm": ["Epilogue"],
              "body": ["Manrope"],
              "display": ["Epilogue"],
              "display-mobile": ["Epilogue"],
              "label-upper": ["Manrope"],
              "stat-number": ["Epilogue"]
            }
          }
        }
    };
    </script>
    <style>
        @layer base {
            html, body { margin: 0; padding: 0; }
            body { overscroll-behavior: none; }
            main > :first-child { margin-top: 0 !important; }
            main > :last-child { margin-bottom: 0 !important; }
        }
        ::-webkit-scrollbar { display: none; }
        .typing-dot { animation: typingBounce 1.2s infinite ease-in-out; }
        .typing-dot:nth-child(2) { animation-delay: 0.15s; }
        .typing-dot:nth-child(3) { animation-delay: 0.3s; }
        @keyframes typingBounce {
            0%, 60%, 100% { transform: translateY(0); opacity: .4; }
            30% { transform: translateY(-4px); opacity: 1; }
        }
    </style>
</head>
<body class="bg-surface font-body text-on-surface antialiased min-h-screen flex flex-col selection:bg-primary-fixed selection:text-primary">
    @php
        $siswaUser = \App\Support\AuthContext::currentUser(request()) ?? auth('siswa')->user();
        $userNama = $siswaUser?->nama_lengkap ?? 'Siswa';
        $userWords = array_values(array_filter(explode(' ', trim($userNama))));
        $userInisial = count($userWords) >= 2
            ? mb_strtoupper(mb_substr($userWords[0], 0, 1) . mb_substr($userWords[count($userWords) - 1], 0, 1))
            : mb_strtoupper(mb_substr($userNama, 0, 2));
        $chatSessions = $siswaUser
            ? $siswaUser->chatSessions()->latest('updated_at')->take(15)->get(['id', 'judul', 'updated_at'])
            : collect();
    @endphp



    <!-- HEADER KHUSUS MOBILE DENGAN TOMBOL DI POJOK KIRI ATAS -->
    <header class="lg:hidden sticky top-0 left-0 right-0 z-40 bg-surface-container-lowest/95 backdrop-blur-md px-4 py-3 border-b border-surface-container flex items-center justify-between shadow-[0_1px_8px_rgba(0,0,0,0.03)]">
        <!-- Tombol Pojok Kiri Atas: Buka Riwayat Chat -->
        <button type="button" id="btn-open-mobile-history" class="w-10 h-10 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface-variant hover:text-primary-700 flex items-center justify-center transition-all active:scale-95 shadow-sm" title="Buka Riwayat Obrolan">
            <span class="material-symbols-outlined text-[22px] text-primary-700">history</span>
        </button>

        <!-- Identitas Chat Asisten -->
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-primary-700 text-white flex items-center justify-center shadow-sm">
                <span class="material-symbols-outlined text-[18px]">smart_toy</span>
            </div>
            <div class="flex flex-col">
                <span class="font-heading text-xs font-bold text-on-surface leading-tight">Semar AI</span>
                <span class="font-caption text-[10px] text-primary-600 font-semibold">Tanya Basa Jawa</span>
            </div>
        </div>

        <!-- Tombol Kembali ke Beranda -->
        <a href="{{ route('siswa.dashboard') }}" title="Bali menyang Beranda" class="w-10 h-10 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface-variant hover:text-primary-700 flex items-center justify-center transition-all active:scale-95 shadow-sm">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
    </header>

    <!-- MOBILE OVERLAY BACKDROP & DRAWER RIWAYAT CHAT -->
    <div id="mobile-history-backdrop" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden"></div>

    <div id="mobile-history-drawer" class="fixed top-0 left-0 bottom-0 w-[85%] max-w-sm bg-surface-container-lowest z-50 shadow-2xl flex flex-col justify-between p-4 transform -translate-x-full transition-transform duration-300 ease-in-out lg:hidden border-r border-surface-container">
        <div class="flex flex-col gap-3 overflow-hidden flex-1">
            <!-- Header Drawer Riwayat dengan Tombol Kembali -->
            <div class="flex items-center justify-between pb-3 border-b border-surface-container">
                <div class="flex items-center gap-2">
                    <button type="button" id="btn-close-mobile-history" class="w-8 h-8 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface-variant hover:text-primary-700 flex items-center justify-center transition-all active:scale-95" title="Tutup Riwayat">
                        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                    </button>
                    <span class="font-heading text-sm font-bold text-on-surface">Riwayat Obrolan</span>
                </div>
                <a href="{{ route('siswa.dashboard') }}" title="Bali menyang Beranda" class="w-8 h-8 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface-variant hover:text-primary-700 flex items-center justify-center transition-all active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">home</span>
                </a>
            </div>

            <!-- Tombol Obrolan Baru -->
            <button type="button" data-action="chat-baru"
                class="flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-primary-700 hover:bg-primary text-white rounded-xl font-body text-xs font-bold shadow-sm transition-all active:scale-95">
                <span class="material-symbols-outlined text-[18px]">add</span>
                <span>Obrolan Anyar</span>
            </button>

            <!-- Daftar Riwayat Obrolan Mobile -->
            <div class="flex-1 flex flex-col gap-1 overflow-y-auto pr-1 text-on-surface chat-history-list" id="mobile-chat-history">
                @forelse($chatSessions as $sesi)
                    <div class="group flex items-center gap-1 rounded-xl transition-colors" data-session-id="{{ $sesi->id }}">
                        <button type="button" data-action="load-sesi" data-session-id="{{ $sesi->id }}"
                            class="flex-1 min-w-0 flex items-center gap-2 px-2.5 py-2 rounded-xl text-left text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface transition-colors">
                            <span class="material-symbols-outlined text-[16px] text-gray-500 group-hover:text-primary-600 shrink-0">chat_bubble_outline</span>
                            <span class="truncate text-xs font-medium" data-judul>{{ $sesi->judul }}</span>
                        </button>
                        <button type="button" data-action="hapus-sesi" data-session-id="{{ $sesi->id }}"
                            class="flex items-center justify-center w-7 h-7 mr-1 rounded-full text-gray-400 hover:text-error hover:bg-error-container/50 transition-colors shrink-0" title="Hapus riwayat">
                            <span class="material-symbols-outlined text-[16px]">delete</span>
                        </button>
                    </div>
                @empty
                    <div class="px-3 py-2 text-gray-500 italic text-xs" data-empty-state>Belum ada riwayat obrolan</div>
                @endforelse
            </div>
        </div>
    </div>

    <main class="w-full pt-4 lg:pt-8 flex-1 bg-surface">
        <div class="flex flex-col lg:flex-row w-full max-w-7xl mx-auto min-h-[calc(100vh-4rem)] px-4 sm:px-6 md:px-10 py-2 sm:py-4 gap-6">
            <!-- SIDEBAR KHUSUS DESKTOP -->
            <aside class="hidden lg:flex lg:flex-col lg:w-72 shrink-0 justify-between bg-surface-container-lowest rounded-2xl p-4 shadow-sm border border-surface-container h-[calc(100vh-7rem)] lg:sticky lg:top-20 z-10">
                <div class="flex flex-col gap-4 overflow-hidden">
                    <div class="flex items-center gap-2 pb-3 border-b border-surface-container">
                        <a href="{{ route('siswa.dashboard') }}" title="Bali menyang Beranda"
                            class="w-8 h-8 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface-variant hover:text-primary-700 flex items-center justify-center transition-all active:scale-95">
                            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                        </a>
                        <span class="font-heading text-sm font-bold text-on-surface">Riwayat Obrolan</span>
                    </div>

                    <button type="button" data-action="chat-baru"
                        class="flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-primary-700 hover:bg-primary text-white rounded-xl font-body text-xs font-bold shadow-sm transition-all active:scale-95">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                        <span>Obrolan Anyar</span>
                    </button>

                    <div class="flex flex-col gap-1 overflow-y-auto pr-1 text-on-surface chat-history-list" id="sidebar-chat-history">
                        @forelse($chatSessions as $sesi)
                            <div class="group flex items-center gap-1 rounded-xl transition-colors" data-session-id="{{ $sesi->id }}">
                                <button type="button" data-action="load-sesi" data-session-id="{{ $sesi->id }}"
                                    class="flex-1 min-w-0 flex items-center gap-2 px-2.5 py-2 rounded-xl text-left text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface transition-colors">
                                    <span class="material-symbols-outlined text-[16px] text-gray-500 group-hover:text-primary-600 shrink-0">chat_bubble_outline</span>
                                    <span class="truncate text-xs font-medium" data-judul>{{ $sesi->judul }}</span>
                                </button>
                                <button type="button" data-action="hapus-sesi" data-session-id="{{ $sesi->id }}"
                                    class="hidden group-hover:flex items-center justify-center w-7 h-7 mr-1 rounded-full text-gray-500 hover:text-error hover:bg-error-container/50 transition-colors shrink-0" title="Hapus riwayat">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                </button>
                            </div>
                        @empty
                            <div class="px-3 py-2 text-gray-500 italic text-xs" data-empty-state>Belum ada riwayat obrolan</div>
                        @endforelse
                    </div>
                </div>
            </aside>

            <div class="flex-1 flex flex-col max-w-3xl mx-auto w-full pb-8">
                <div class="flex flex-col items-center text-center mb-6 sm:mb-8">
                    <h1 class="font-heading text-xl sm:text-2xl md:text-3xl font-extrabold text-on-surface tracking-tight mb-1">Tanya apa saja seputar Basa Jawa</h1>
                    <p class="font-body text-xs sm:text-sm text-on-surface-variant max-w-lg">Tingkatan unggah-ungguh, aksara Jawa, peribahasa, atau terjemahan krama alus langsung terverifikasi.</p>
                </div>

                <div class="flex flex-col gap-6 w-full" id="chat-messages">
                    <div class="flex items-start gap-3 w-full">
                        <div class="w-9 h-9 rounded-xl bg-primary-700 text-white flex items-center justify-center shrink-0 shadow-sm">
                            <span class="material-symbols-outlined text-[20px]">smart_toy</span>
                        </div>
                        <div class="flex flex-col gap-3 flex-1 min-w-0">
                            <div class="rounded-2xl bg-surface-container-lowest p-4 sm:p-5 shadow-sm text-on-surface border border-surface-container">
                                <p class="font-body text-xs sm:text-sm leading-relaxed">
                                    Sugeng rawuh, <strong class="text-primary-700">{{ $userNama }}</strong>! Kula <strong class="text-primary-700">Semar AI</strong>, rencang panjenengan anggenipun sinau Basa Jawi. Sumangga tanglet bab unggah-ungguh basa (Ngoko, Krama, Krama Alus), aksara Jawa, tembung saroja, peribahasa, tuwin kabudayan Jawa.
                                </p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" class="suggestion-chip text-left bg-surface-container-low hover:bg-surface-container text-primary-700 px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-xl font-body text-xs font-semibold shadow-sm transition-all active:scale-95" data-prompt="Ubahlah ke Krama Alus: 'Saya mau makan bersama kakek'">Ubah ke Krama Alus: &ldquo;Saya mau makan bersama kakek&rdquo;</button>
                                <button type="button" class="suggestion-chip text-left bg-surface-container-low hover:bg-surface-container text-primary-700 px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-xl font-body text-xs font-semibold shadow-sm transition-all active:scale-95" data-prompt="Apa bedane tembung 'turu', 'tilem', lan 'sare'?">Bedane tembung &lsquo;turu&rsquo;, &lsquo;tilem&rsquo;, lan &lsquo;sare&rsquo;?</button>
                                <button type="button" class="suggestion-chip text-left bg-surface-container-low hover:bg-surface-container text-primary-700 px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-xl font-body text-xs font-semibold shadow-sm transition-all active:scale-95" data-prompt="Apa tegese bebasan 'Becik ketitik ala ketara'?">Tegese bebasan &lsquo;Becik ketitik ala ketara&rsquo;</button>
                                <button type="button" class="suggestion-chip text-left bg-surface-container-low hover:bg-surface-container text-primary-700 px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-xl font-body text-xs font-semibold shadow-sm transition-all active:scale-95" data-prompt="Piye panganggone sandhangan wulu lan suku ing aksara Jawa?">Panganggone sandhangan wulu lan suku</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sticky bottom-4 w-full pt-4 mt-6">
                    <div class="rounded-full bg-surface-container-lowest border border-surface-container p-1.5 pl-3.5 sm:pl-4 pr-1.5 flex items-center gap-2 shadow-md">
                        <button type="button" id="micBtn" title="Ketik nganggo swara"
                            class="w-9 h-9 rounded-full hover:bg-surface-container text-on-surface-variant hover:text-primary-600 flex items-center justify-center transition-colors shrink-0">
                            <span class="material-symbols-outlined text-[20px]">mic</span>
                        </button>
                        <input type="text" id="chat-input-field" placeholder="Ketik pitakon basa Jawa ing kene..." autocomplete="off"
                            class="flex-1 min-w-0 bg-transparent border-none outline-none font-body text-xs sm:text-sm text-on-surface placeholder:text-gray-500 py-1">
                        <button type="button" id="chat-send-btn" title="Kirim pitakon"
                            class="w-9 h-9 rounded-full bg-primary-700 hover:bg-primary disabled:opacity-50 disabled:cursor-not-allowed text-white flex items-center justify-center transition-all active:scale-95 shadow-sm shrink-0">
                            <span class="material-symbols-outlined text-[20px]">send</span>
                        </button>
                    </div>
                    <p class="text-center font-caption text-[10px] sm:text-[11px] text-gray-500 mt-2 px-2">
                        Asisten Tanya Bahasa menjawab berdasarkan basis data korpus resmi sekolah.
                    </p>
                </div>
            </div>
        </div>
    </main>

    <footer class="w-full bg-surface-container-low py-4 mt-auto">
        <div class="w-full px-5 md:px-10 flex flex-col sm:flex-row items-center justify-between gap-2 text-on-surface-variant font-caption text-xs">
            <span>&copy; 2026 Sinau Jowo. Kagunganipun sesarengan kangge nguri-uri kabudayan.</span>
            <span class="font-label-upper uppercase tracking-wider text-primary-700 font-bold">Ngoko &bull; Madya &bull; Krama Inggil</span>
        </div>
    </footer>

    <script>
        window.AsistenAI = (function () {
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrf = csrfMeta ? csrfMeta.content : null;
            const chatUrl = @json(route('kuis.chat'));
            const sesiUrlTemplate = @json(route('kuis.chat.sesi.show', ['chatSession' => 'SESSION_ID']));
            const sesiDeleteTemplate = @json(route('kuis.chat.sesi.destroy', ['chatSession' => 'SESSION_ID']));
            const container = document.getElementById('chat-messages');
            const input = document.getElementById('chat-input-field');
            const sendBtn = document.getElementById('chat-send-btn');
            const welcomeHtml = container ? container.innerHTML : '';
            let activeSessionId = null;
            let busy = false;

            // Mobile Drawer Controls
            const openMobileHistoryBtn = document.getElementById('btn-open-mobile-history');
            const closeMobileHistoryBtn = document.getElementById('btn-close-mobile-history');
            const mobileHistoryDrawer = document.getElementById('mobile-history-drawer');
            const mobileHistoryBackdrop = document.getElementById('mobile-history-backdrop');

            function openMobileHistory() {
                if (! mobileHistoryDrawer || ! mobileHistoryBackdrop) return;
                mobileHistoryBackdrop.classList.remove('opacity-0', 'pointer-events-none');
                mobileHistoryBackdrop.classList.add('opacity-100');
                mobileHistoryDrawer.classList.remove('-translate-x-full');
                document.body.classList.add('overflow-hidden');
            }

            function closeMobileHistory() {
                if (! mobileHistoryDrawer || ! mobileHistoryBackdrop) return;
                mobileHistoryBackdrop.classList.remove('opacity-100');
                mobileHistoryBackdrop.classList.add('opacity-0', 'pointer-events-none');
                mobileHistoryDrawer.classList.add('-translate-x-full');
                document.body.classList.remove('overflow-hidden');
            }

            if (openMobileHistoryBtn) openMobileHistoryBtn.addEventListener('click', openMobileHistory);
            if (closeMobileHistoryBtn) closeMobileHistoryBtn.addEventListener('click', closeMobileHistory);
            if (mobileHistoryBackdrop) mobileHistoryBackdrop.addEventListener('click', closeMobileHistory);

            function fromTemplate(html) {
                const tpl = document.createElement('template');
                tpl.innerHTML = html.trim();
                return tpl.content.firstElementChild;
            }

            function scrollToBottom() {
                window.scrollTo({ top: document.documentElement.scrollHeight, behavior: 'smooth' });
            }

            function userBubble(text) {
                const node = fromTemplate(`
                    <div class="flex justify-end w-full">
                        <div class="bg-primary-700 text-white px-4 sm:px-5 py-3 sm:py-3.5 rounded-2xl rounded-br-md max-w-xl shadow-md">
                            <p class="font-body text-xs sm:text-sm leading-relaxed whitespace-pre-wrap" data-text></p>
                        </div>
                    </div>
                `);
                node.querySelector('[data-text]').textContent = text;
                return node;
            }

            function botBubble(label) {
                const node = fromTemplate(`
                    <div class="flex items-start gap-2.5 sm:gap-3 w-full">
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-primary-700 text-white flex items-center justify-center shrink-0 shadow-sm">
                            <span class="material-symbols-outlined text-[18px] sm:text-[20px]">smart_toy</span>
                        </div>
                        <div class="flex flex-col gap-2 flex-1 min-w-0">
                            <div class="rounded-2xl bg-surface-container-lowest p-4 sm:p-5 shadow-sm text-on-surface border border-surface-container">
                                <span class="block font-label-upper text-[10px] uppercase tracking-wider text-gray-500 mb-1">${label}</span>
                                <p class="font-body text-xs sm:text-sm leading-relaxed whitespace-pre-wrap" data-text></p>
                                <div class="hidden flex-col gap-1 pt-2 mt-2 border-t border-surface-container" data-sources></div>
                            </div>
                        </div>
                    </div>
                `);
                return node;
            }

            function typingIndicator() {
                return fromTemplate(`
                    <div class="flex items-start gap-2.5 sm:gap-3 w-full" data-typing>
                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-primary-700 text-white flex items-center justify-center shrink-0 shadow-sm">
                            <span class="material-symbols-outlined text-[18px] sm:text-[20px]">smart_toy</span>
                        </div>
                        <div class="bg-surface-container-lowest px-4 sm:px-5 py-3 sm:py-4 rounded-2xl border border-surface-container flex items-center gap-1.5 shadow-sm">
                            <span class="typing-dot w-2 h-2 rounded-full bg-primary-500"></span>
                            <span class="typing-dot w-2 h-2 rounded-full bg-primary-500"></span>
                            <span class="typing-dot w-2 h-2 rounded-full bg-primary-500"></span>
                        </div>
                    </div>
                `);
            }

            function renderSources(node, sources) {
                if (! Array.isArray(sources) || sources.length === 0) return;
                const wrap = node.querySelector('[data-sources]');
                const title = document.createElement('div');
                title.className = 'flex items-center gap-1.5 text-gray-500 font-caption text-xs';
                title.innerHTML = '<span class="material-symbols-outlined text-[14px]">menu_book</span><span data-judul></span>';
                title.querySelector('[data-judul]').textContent = 'Sumber korpus: ' + sources.map(function (s) {
                    return s.judul;
                }).filter(Boolean).join('; ');
                wrap.appendChild(title);
                wrap.classList.remove('hidden');
                wrap.classList.add('flex');
            }

            function appendReply(text, sources) {
                const node = botBubble('Wangsulan Semar AI');
                node.querySelector('[data-text]').textContent = text;
                renderSources(node, sources);
                container.appendChild(node);
                scrollToBottom();
            }

            function setBusy(state) {
                busy = state;
                sendBtn.disabled = state;
                input.disabled = state;
            }

            function sessionNodes(id) {
                return document.querySelectorAll('.chat-history-list [data-session-id="' + id + '"]');
            }

            function setActiveHighlight(id) {
                document.querySelectorAll('.chat-history-list [data-session-id]').forEach(function (node) {
                    const isActive = String(node.dataset.sessionId) === String(id);
                    const btn = node.querySelector('[data-action="load-sesi"]');
                    if (btn) {
                        btn.classList.toggle('bg-surface-container-low', isActive);
                        btn.classList.toggle('text-primary-700', isActive);
                    }
                    const title = node.querySelector('[data-judul], .truncate');
                    if (title) {
                        title.classList.toggle('font-bold', isActive);
                        title.classList.toggle('text-primary-700', isActive);
                    }
                });
            }

            function removeEmptyState() {
                document.querySelectorAll('.chat-history-list [data-empty-state]').forEach(function (el) {
                    el.remove();
                });
            }

            function upsertHistoryItem(id, judul) {
                removeEmptyState();
                const existingNodes = sessionNodes(id);

                if (existingNodes.length === 0) {
                    document.querySelectorAll('.chat-history-list').forEach(function (list) {
                        const node = fromTemplate(`
                            <div class="group flex items-center gap-1 rounded-xl transition-colors" data-session-id="${id}">
                                <button type="button" data-action="load-sesi" data-session-id="${id}" class="flex-1 min-w-0 flex items-center gap-2 px-2.5 py-2 rounded-xl text-left text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface transition-colors">
                                    <span class="material-symbols-outlined text-[16px] text-gray-500 group-hover:text-primary-600 shrink-0">chat_bubble_outline</span>
                                    <span class="truncate text-xs font-medium" data-judul></span>
                                </button>
                                <button type="button" data-action="hapus-sesi" data-session-id="${id}" class="flex lg:hidden lg:group-hover:flex items-center justify-center w-7 h-7 mr-1 rounded-full text-gray-400 hover:text-error hover:bg-error-container/50 transition-colors shrink-0" title="Hapus riwayat">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                </button>
                            </div>
                        `);
                        node.querySelector('[data-judul]').textContent = judul || 'Obrolan anyar';
                        list.prepend(node);
                    });
                } else {
                    existingNodes.forEach(function (node) {
                        const title = node.querySelector('[data-judul]');
                        if (title && judul) title.textContent = judul;
                    });
                }

                setActiveHighlight(id);
            }

            function resetView() {
                if (container) container.innerHTML = welcomeHtml;
            }

            function newChat() {
                activeSessionId = null;
                resetView();
                setActiveHighlight(null);
                if (input) input.focus();
            }

            async function loadSession(id) {
                if (busy) return;
                setBusy(true);
                try {
                    const res = await fetch(sesiUrlTemplate.replace('SESSION_ID', id), {
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin',
                    });
                    if (! res.ok) throw new Error('gagal');
                    const data = await res.json();
                    activeSessionId = data.id;
                    resetView();
                    (data.messages || []).forEach(function (m) {
                        if (m.role === 'user') {
                            container.appendChild(userBubble(m.pesan));
                        } else {
                            appendReply(m.pesan, m.sumber || []);
                        }
                    });
                    setActiveHighlight(data.id);
                    scrollToBottom();
                } catch (err) {
                    resetView();
                    appendReply('Mohon maaf, riwayat obrolan tidak dapat dibuka. Silakan coba lagi.', []);
                } finally {
                    setBusy(false);
                    input.focus();
                }
            }

            async function deleteSession(id) {
                if (busy) return;
                if (! window.confirm('Hapus riwayat obrolan ini?')) return;
                setBusy(true);
                try {
                    const res = await fetch(sesiDeleteTemplate.replace('SESSION_ID', id), {
                        method: 'DELETE',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                        credentials: 'same-origin',
                    });
                    if (! res.ok) throw new Error('gagal');
                    sessionNodes(id).forEach(function (node) {
                        node.remove();
                    });
                    if (String(activeSessionId) === String(id)) newChat();
                    document.querySelectorAll('.chat-history-list').forEach(function (list) {
                        if (! list.querySelector('[data-session-id]')) {
                            list.innerHTML = '<div class="px-3 py-2 text-gray-500 italic text-xs" data-empty-state>Belum ada riwayat obrolan</div>';
                        }
                    });
                } catch (err) {
                    window.alert('Gagal menghapus riwayat. Silakan coba lagi.');
                } finally {
                    setBusy(false);
                }
            }

            async function ask(question) {
                const text = (question || '').trim();
                if (text === '' || busy) return;

                container.appendChild(userBubble(text));
                scrollToBottom();
                input.value = '';
                setBusy(true);

                const typing = typingIndicator();
                container.appendChild(typing);
                scrollToBottom();

                try {
                    const res = await fetch(chatUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({ pertanyaan: text, session_id: activeSessionId }),
                    });

                    const data = await res.json().catch(function () { return {}; });
                    typing.remove();

                    if (! res.ok) {
                        const message = res.status === 429
                            ? 'Mohon maaf, Anda mengirim pertanyaan terlalu cepat. Silakan tunggu beberapa saat lagi.'
                            : 'Mohon maaf, terjadi gangguan teknis. Silakan coba lagi.';
                        appendReply(message, []);
                        return;
                    }

                    if (data.session_id) {
                        activeSessionId = data.session_id;
                        upsertHistoryItem(data.session_id, data.session_title);
                    }

                    appendReply(data.jawaban || 'Mohon maaf, saya belum dapat menjawab pertanyaan tersebut.', data.sumber || []);
                } catch (err) {
                    typing.remove();
                    appendReply('Mohon maaf, koneksi ke asisten gagal. Silakan periksa koneksi internet Anda.', []);
                } finally {
                    setBusy(false);
                    input.focus();
                }
            }

            function initMic() {
                const micBtn = document.getElementById('micBtn');
                if (! micBtn) return;
                const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
                let recognition = null;
                let listening = false;

                micBtn.addEventListener('click', function () {
                    if (! SR) {
                        window.alert('Fitur swara ora kasedhiya ing browser iki. Tulisen manual wae.');
                        return;
                    }
                    if (listening && recognition) {
                        recognition.stop();
                        return;
                    }
                    recognition = new SR();
                    recognition.lang = 'id-ID';
                    recognition.interimResults = false;
                    recognition.onresult = function (e) {
                        input.value = e.results[0][0].transcript;
                        input.focus();
                    };
                    recognition.onend = function () {
                        listening = false;
                        micBtn.classList.remove('bg-error-container', 'text-error');
                    };
                    recognition.onerror = function () {
                        listening = false;
                        micBtn.classList.remove('bg-error-container', 'text-error');
                    };
                    recognition.start();
                    listening = true;
                    micBtn.classList.add('bg-error-container', 'text-error');
                });
            }

            function init() {
                if (! container) return;

                sendBtn.addEventListener('click', function () {
                    ask(input.value);
                });

                input.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter' && ! event.shiftKey) {
                        event.preventDefault();
                        ask(input.value);
                    }
                });

                document.addEventListener('click', function (event) {
                    const chip = event.target.closest('.suggestion-chip');
                    if (chip) {
                        ask(chip.dataset.prompt);
                        return;
                    }

                    const trigger = event.target.closest('[data-action]');
                    if (! trigger) return;

                    const action = trigger.dataset.action;
                    const id = trigger.dataset.sessionId;

                    if (action === 'chat-baru') {
                        event.preventDefault();
                        newChat();
                        closeMobileHistory();
                    } else if (action === 'load-sesi' && id) {
                        event.preventDefault();
                        loadSession(id);
                        closeMobileHistory();
                    } else if (action === 'hapus-sesi' && id) {
                        event.preventDefault();
                        event.stopPropagation();
                        deleteSession(id);
                    }
                });

                initMic();
            }

            init();

            return { ask: ask, newChat: newChat, loadSession: loadSession, deleteSession: deleteSession };
        })();
    </script>
    @include('partials.siswa-sound')
</body>
</html>
