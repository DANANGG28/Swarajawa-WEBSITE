<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Asisten Tanya Bahasa AI - Sinau Jowo Web</title>
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
            "spacing": {
              "margin": "1.25rem",
              "space-xl": "1.75rem",
              "space-md": "1rem",
              "space-sm": "0.5rem",
              "gutter": "0.75rem",
              "gutter-desktop": "1.5rem",
              "margin-desktop": "2.5rem",
              "space-xs": "0.25rem"
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
<body class="bg-background font-body text-on-surface antialiased">
    @php
        $siswaUser = \App\Support\AuthContext::currentUser(request()) ?? auth('siswa')->user();
        $userNama = $siswaUser?->nama_lengkap ?? 'Siswa';
        $userWords = array_values(array_filter(explode(' ', trim($userNama))));
        $userInisial = count($userWords) >= 2
            ? mb_strtoupper(mb_substr($userWords[0], 0, 1) . mb_substr($userWords[count($userWords) - 1], 0, 1))
            : mb_strtoupper(mb_substr($userNama, 0, 2));
    @endphp

    <!-- ASIDE SIDEBAR COMPONENT -->
    <x-sidebar active="asisten" />

    <div class="pl-72 flex flex-col min-h-screen">
        <!-- HEADER -->
        <header class="fixed top-0 left-72 right-0 h-20 bg-surface-container-lowest/90 backdrop-blur-xl z-40 shadow-[0_1px_8px_rgba(0,0,0,0.04)] px-space-xl flex items-center justify-between">
            <div class="flex items-center flex-1 max-w-md">
                <div class="flex items-center w-full bg-gray-50 rounded-full px-space-md py-space-xs gap-space-sm border border-gray-200/60 focus-within:border-primary-500 transition-colors">
                    <span class="material-symbols-outlined text-gray-500 text-[20px]">search</span>
                    <input type="text" placeholder="Cari materi aksara, peribahasa, tata bahasa..." class="w-full bg-transparent border-none outline-none font-body text-body text-on-surface placeholder:text-gray-500">
                </div>
            </div>
            <div class="flex items-center gap-space-lg">
                <button type="button" class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-50 text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors relative">
                    <span class="material-symbols-outlined text-[20px]">notifications</span>
                    <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-secondary"></span>
                </button>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="flex-1 pt-20 w-full px-6 pb-[16rem] bg-background">
            <div class="flex flex-col w-full gap-space-lg">

                <!-- CHAT CONTAINER CARD -->
                <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm flex flex-col gap-6 w-full border border-gray-100">

                    <!-- Chat Header Bar -->
                    <div class="flex items-center justify-between pb-3 bg-surface-container-low/60 -mx-6 -mt-6 px-6 pt-4 rounded-t-2xl border-b border-primary-100/50">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary-600 text-xl" style="font-variation-settings: 'FILL' 1;">forum</span>
                            <span class="font-heading text-heading font-bold text-on-surface">Diskusi Pembelajaran Aktif</span>
                        </div>
                        <div class="flex items-center gap-2 text-green-500">
                            <span class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse"></span>
                            <span class="font-caption text-caption font-bold text-green-500 uppercase">ONLINE AI RAG</span>
                        </div>
                    </div>

                    <!-- DAFTAR PESAN OBROLAN -->
                    <div id="chat-messages" class="flex flex-col gap-6 w-full">
                        <!-- Bot Welcome Message -->
                        <div class="flex items-start gap-4 w-full">
                            <div class="w-10 h-10 rounded-xl bg-primary-700 flex items-center justify-center text-white shrink-0 shadow-sm">
                                <span class="material-symbols-outlined text-2xl">neurology</span>
                            </div>
                            <div class="flex flex-col gap-1 flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-body text-body font-bold text-on-surface">Teman Belajar Jawa AI</span>
                                    <span class="font-caption text-caption text-gray-500">sekarang</span>
                                </div>
                                <div class="bg-surface-container-low p-4 rounded-2xl text-on-surface space-y-2 border border-primary-100/40">
                                    <p class="font-body text-body leading-relaxed">
                                        Selamat datang, {{ $userNama }}! Saya adalah <strong class="text-primary-700">Teman Belajar Jawa</strong>. Saya siap membantu Anda mempelajari tata krama, unggah-ungguh bahasa (Ngoko, Ngoko Alus, Krama, Krama Alus), aksara Jawa, tembung saroja, peribahasa, hingga seputar budaya Jawa.
                                    </p>
                                    <p class="font-body text-body text-on-surface-variant leading-relaxed">
                                        Silakan tanyakan hal seputar bahasa atau budaya Jawa yang masih membingungkan!
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- FIXED COMPOSER (Pitakon Populer + Input) -->
                <div class="fixed bottom-0 left-72 right-0 z-40 bg-background/95 backdrop-blur-xl border-t border-gray-200/60 shadow-[0_-4px_20px_rgba(0,0,0,0.05)] px-6 py-4">
                    <div class="flex flex-col gap-space-lg w-full">
                <!-- Popular Suggestion Chips -->
                <div class="flex flex-col gap-2 w-full">
                    <div class="flex items-center justify-between px-1">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-bold">Pertanyaan Populer Pembelajaran</span>
                    </div>
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
                        <button type="button" data-prompt="Krama Inggil tembung 'Turu' punapa nggih?" class="suggestion-chip whitespace-nowrap px-4 py-2 bg-surface-container-lowest hover:bg-surface-container-high text-on-surface rounded-full font-body text-body text-primary-700 transition-all shadow-sm flex items-center gap-1.5 border border-gray-100">
                            <span class="material-symbols-outlined text-base text-primary-600">help_outline</span>
                            <span>Krama Inggil kata "Turu"?</span>
                        </button>
                        <button type="button" data-prompt="Bentenipun Aksara Murda kaliyan Aksara Swara kados pundi?" class="suggestion-chip whitespace-nowrap px-4 py-2 bg-surface-container-lowest hover:bg-surface-container-high text-on-surface rounded-full font-body text-body text-primary-700 transition-all shadow-sm flex items-center gap-1.5 border border-gray-100">
                            <span class="material-symbols-outlined text-base text-primary-600">spellcheck</span>
                            <span>Perbedaan Aksara Murda & Swara</span>
                        </button>
                        <button type="button" data-prompt="Punapa tegesipun paribasan 'Becik ketitik, ala ketara'?" class="suggestion-chip whitespace-nowrap px-4 py-2 bg-surface-container-lowest hover:bg-surface-container-high text-on-surface rounded-full font-body text-body text-primary-700 transition-all shadow-sm flex items-center gap-1.5 border border-gray-100">
                            <span class="material-symbols-outlined text-base text-primary-600">auto_awesome</span>
                            <span>Arti Peribahasa "Becik Ketitik"</span>
                        </button>
                        <button type="button" data-prompt="Kados pundi caranipun nyuwun idin marang Guru ingkang leres miturut krama alus?" class="suggestion-chip whitespace-nowrap px-4 py-2 bg-surface-container-lowest hover:bg-surface-container-high text-on-surface rounded-full font-body text-body text-primary-700 transition-all shadow-sm flex items-center gap-1.5 border border-gray-100">
                            <span class="material-symbols-outlined text-base text-primary-600">record_voice_over</span>
                            <span>Tata krama izin kepada Guru</span>
                        </button>
                    </div>
                </div>

                <!-- Chat Input Card -->
                <div class="bg-surface-container-lowest rounded-2xl p-4 shadow-md flex flex-col gap-2 w-full border border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-1 shrink-0">
                            <button type="button" class="w-10 h-10 flex items-center justify-center rounded-full bg-surface-container-low text-on-surface-variant hover:bg-surface-container-high hover:text-primary-600 transition-colors" title="Unggah dokumen / gambar soal aksara">
                                <span class="material-symbols-outlined text-xl">attach_file</span>
                            </button>
                        </div>
                        <div class="flex-1 min-w-0 bg-surface-container-low rounded-xl px-4 py-1.5 flex items-center border border-primary-100/50">
                            <input type="text" id="chat-input-field" placeholder="Ketik pertanyaan seputar bahasa atau budaya Jawa di sini..." autocomplete="off" class="w-full bg-transparent border-none outline-none font-body text-body text-on-surface placeholder:text-gray-400 py-1">
                        </div>
                        <button type="button" id="chat-send-btn" class="shrink-0 inline-flex items-center gap-2 px-6 py-2.5 bg-primary-600 hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-full font-body text-body font-bold transition-all shadow-md">
                            <span>Kirim Pertanyaan</span>
                            <span class="material-symbols-outlined text-lg">send</span>
                        </button>
                    </div>
                    <div class="flex items-center justify-between px-1 pt-1 border-t border-gray-100/60">
                        <div class="flex items-center gap-1.5 text-gray-500">
                            <span class="material-symbols-outlined text-sm text-green-500">verified</span>
                            <span class="font-caption text-caption text-on-surface-variant">Asisten Tanya Bahasa menjawab berdasarkan basis data korpus resmi sekolah. Tidak mengarang jawaban di luar materi.</span>
                        </div>
                        <span class="font-caption text-caption text-gray-400 hidden sm:inline">Enter untuk mengirim</span>
                    </div>
                </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        window.AsistenAI = (function () {
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrf = csrfMeta ? csrfMeta.content : null;
            const chatUrl = @json(route('kuis.chat'));
            const sesiUrlTemplate = @json(route('kuis.chat.sesi.show', ['chatSession' => 'SESSION_ID']));
            const sesiDeleteTemplate = @json(route('kuis.chat.sesi.destroy', ['chatSession' => 'SESSION_ID']));
            const userInitial = @json($userInisial);
            const container = document.getElementById('chat-messages');
            const input = document.getElementById('chat-input-field');
            const sendBtn = document.getElementById('chat-send-btn');
            const historyList = document.getElementById('sidebar-chat-history');
            const welcomeHtml = container ? container.innerHTML : '';
            let activeSessionId = null;
            let busy = false;

            function nowWib() {
                return new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
            }

            function fromTemplate(html) {
                const tpl = document.createElement('template');
                tpl.innerHTML = html.trim();
                return tpl.content.firstElementChild;
            }

            function scrollToBottom() {
                if (!container) {
                    return;
                }
                if (container.lastElementChild) {
                    window.scrollTo({ top: document.documentElement.scrollHeight, behavior: 'smooth' });
                }
            }

            function userBubble(text, waktu) {
                const node = fromTemplate(`
                    <div class="flex items-start justify-end gap-4 w-full">
                        <div class="flex flex-col items-end gap-1 max-w-3xl lg:max-w-4xl">
                            <div class="flex items-center gap-2">
                                <span class="font-caption text-caption text-gray-500" data-waktu>${waktu || nowWib()}</span>
                                <span class="font-body text-body font-bold text-on-surface">Anda</span>
                                <div class="w-8 h-8 rounded-full bg-primary-700 text-white font-bold text-xs flex items-center justify-center">${userInitial}</div>
                            </div>
                            <div class="bg-primary-600 text-white p-4 rounded-2xl shadow-sm leading-relaxed">
                                <p class="font-body text-body" data-text></p>
                            </div>
                        </div>
                    </div>
                `);
                node.querySelector('[data-text]').textContent = text;
                return node;
            }

            function botBubble(waktu) {
                const node = fromTemplate(`
                    <div class="flex items-start gap-4 w-full">
                        <div class="w-10 h-10 rounded-xl bg-primary-700 flex items-center justify-center text-white shrink-0 shadow-sm">
                            <span class="material-symbols-outlined text-2xl">neurology</span>
                        </div>
                        <div class="flex flex-col gap-1 flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-body text-body font-bold text-on-surface">Teman Belajar Jawa AI</span>
                                <span class="font-caption text-caption text-gray-500" data-waktu>${waktu || nowWib()}</span>
                            </div>
                            <div class="bg-surface-container-low p-4 rounded-2xl text-on-surface space-y-3 border border-primary-100/40">
                                <p class="font-body text-body leading-relaxed whitespace-pre-wrap" data-text></p>
                                <div class="hidden flex-col gap-1 pt-2 border-t border-gray-100" data-sources></div>
                            </div>
                        </div>
                    </div>
                `);
                return node;
            }

            function typingIndicator() {
                return fromTemplate(`
                    <div class="flex items-start gap-4 w-full" data-typing>
                        <div class="w-10 h-10 rounded-xl bg-primary-700 flex items-center justify-center text-white shrink-0 shadow-sm">
                            <span class="material-symbols-outlined text-2xl">neurology</span>
                        </div>
                        <div class="bg-surface-container-low px-5 py-4 rounded-2xl border border-primary-100/40 flex items-center gap-1.5">
                            <span class="typing-dot w-2 h-2 rounded-full bg-primary-500"></span>
                            <span class="typing-dot w-2 h-2 rounded-full bg-primary-500"></span>
                            <span class="typing-dot w-2 h-2 rounded-full bg-primary-500"></span>
                        </div>
                    </div>
                `);
            }

            function renderSources(node, sources) {
                if (!Array.isArray(sources) || sources.length === 0) {
                    return;
                }
                const wrap = node.querySelector('[data-sources]');
                const title = document.createElement('div');
                title.className = 'flex items-center gap-1.5 text-gray-500 font-caption text-caption';
                title.innerHTML = '<span class="material-symbols-outlined text-sm">menu_book</span><span data-judul></span>';
                title.querySelector('[data-judul]').textContent = 'Sumber korpus: ' + sources.map(function (s) {
                    return s.judul;
                }).filter(Boolean).join('; ');
                wrap.appendChild(title);
                wrap.classList.remove('hidden');
                wrap.classList.add('flex');
            }

            function appendReply(text, sources, waktu) {
                const node = botBubble(waktu);
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

            function sessionNode(id) {
                return historyList ? historyList.querySelector('[data-session-id="' + id + '"]') : null;
            }

            function setActiveHighlight(id) {
                if (!historyList) {
                    return;
                }
                historyList.querySelectorAll('[data-session-id]').forEach(function (node) {
                    const isActive = String(node.dataset.sessionId) === String(id);
                    node.classList.toggle('bg-surface-container-low', isActive);
                    const title = node.querySelector('[data-judul], .truncate');
                    if (title) {
                        title.classList.toggle('font-bold', isActive);
                        title.classList.toggle('text-primary-700', isActive);
                    }
                });
            }

            function removeEmptyState() {
                if (!historyList) {
                    return;
                }
                const empty = historyList.querySelector('[data-empty-state]');
                if (empty) {
                    empty.remove();
                }
            }

            function upsertHistoryItem(id, judul) {
                if (!historyList) {
                    return;
                }
                removeEmptyState();
                let node = sessionNode(id);

                if (!node) {
                    node = fromTemplate(`
                        <div class="group flex items-center gap-1 rounded-xl hover:bg-surface-container-high transition-colors" data-session-id="${id}">
                            <button type="button" data-action="load-sesi" data-session-id="${id}" class="flex-1 min-w-0 flex items-center gap-2 px-3 py-2 text-left text-on-surface-variant hover:text-on-surface transition-colors truncate">
                                <span class="material-symbols-outlined text-[16px] text-gray-400 group-hover:text-primary-600 shrink-0">chat_bubble_outline</span>
                                <span class="truncate" data-judul></span>
                            </button>
                            <button type="button" data-action="hapus-sesi" data-session-id="${id}" class="hidden group-hover:flex items-center justify-center w-7 h-7 mr-1 rounded-full text-gray-400 hover:text-error hover:bg-error-container/50 transition-colors shrink-0" title="Hapus riwayat">
                                <span class="material-symbols-outlined text-[16px]">delete</span>
                            </button>
                        </div>
                    `);
                    node.querySelector('[data-judul]').textContent = judul || 'Obrolan baru';
                    historyList.prepend(node);
                } else {
                    const title = node.querySelector('[data-judul]');
                    if (title && judul) {
                        title.textContent = judul;
                    }
                }

                setActiveHighlight(id);
            }

            function resetView() {
                if (container) {
                    container.innerHTML = welcomeHtml;
                }
            }

            function newChat() {
                activeSessionId = null;
                resetView();
                setActiveHighlight(null);
                if (input) {
                    input.focus();
                }
            }

            async function loadSession(id) {
                if (busy) {
                    return;
                }
                setBusy(true);
                try {
                    const res = await fetch(sesiUrlTemplate.replace('SESSION_ID', id), {
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin',
                    });
                    if (!res.ok) {
                        throw new Error('gagal');
                    }
                    const data = await res.json();
                    activeSessionId = data.id;
                    resetView();
                    (data.messages || []).forEach(function (m) {
                        if (m.role === 'user') {
                            container.appendChild(userBubble(m.pesan, m.waktu));
                        } else {
                            appendReply(m.pesan, m.sumber || [], m.waktu);
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
                if (busy) {
                    return;
                }
                if (!window.confirm('Hapus riwayat obrolan ini?')) {
                    return;
                }
                setBusy(true);
                try {
                    const res = await fetch(sesiDeleteTemplate.replace('SESSION_ID', id), {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                        },
                        credentials: 'same-origin',
                    });
                    if (!res.ok) {
                        throw new Error('gagal');
                    }
                    const node = sessionNode(id);
                    if (node) {
                        node.remove();
                    }
                    if (String(activeSessionId) === String(id)) {
                        newChat();
                    }
                    if (historyList && !historyList.querySelector('[data-session-id]')) {
                        historyList.innerHTML = '<div class="px-3 py-2 text-gray-400 italic text-xs" data-empty-state>Belum ada riwayat obrolan</div>';
                    }
                } catch (err) {
                    window.alert('Gagal menghapus riwayat. Silakan coba lagi.');
                } finally {
                    setBusy(false);
                }
            }

            async function ask(question) {
                const text = (question || '').trim();
                if (text === '' || busy) {
                    return;
                }

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

                    if (!res.ok) {
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

            function init() {
                if (!container) {
                    return;
                }

                sendBtn.addEventListener('click', function () {
                    ask(input.value);
                });

                input.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter' && !event.shiftKey) {
                        event.preventDefault();
                        ask(input.value);
                    }
                });

                document.querySelectorAll('.suggestion-chip').forEach(function (chip) {
                    chip.addEventListener('click', function () {
                        ask(chip.dataset.prompt);
                    });
                });

                document.addEventListener('click', function (event) {
                    const trigger = event.target.closest('[data-action]');
                    if (!trigger) {
                        return;
                    }

                    const action = trigger.dataset.action;
                    const id = trigger.dataset.sessionId;

                    if (action === 'chat-baru') {
                        event.preventDefault();
                        newChat();
                    } else if (action === 'load-sesi' && id) {
                        event.preventDefault();
                        loadSession(id);
                    } else if (action === 'hapus-sesi' && id) {
                        event.preventDefault();
                        event.stopPropagation();
                        deleteSession(id);
                    }
                });
            }

            init();

            return { ask: ask, newChat: newChat, loadSession: loadSession, deleteSession: deleteSession };
        })();
    </script>
</body>
</html>
