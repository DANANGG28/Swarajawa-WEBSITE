<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latihan Soal & Kuis - Sinau Jowo Web</title>
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
              "space-lg": "1.25rem",
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
            html {
                font-size: 90%;
                zoom: 90%;
            }
            html, body { margin: 0; padding: 0; }
            body { overscroll-behavior: none; }
            main > :first-child { margin-top: 0 !important; }
            main > :last-child { margin-bottom: 0 !important; }
        }
        ::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-background font-body text-on-surface antialiased">
    <!-- ASIDE SIDEBAR COMPONENT -->
    <x-sidebar active="latihan" />

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
        <main class="flex-1 pt-20 w-full px-margin-desktop py-space-xl bg-background">
            <div class="flex flex-col w-full pb-space-xl gap-space-lg">
                <!-- Top Hero Banner: Deep Royal Purple Gradient -->
                <section class="relative overflow-hidden rounded-[24px] bg-gradient-to-r from-primary to-primary-600 p-8 text-on-primary shadow-md">
                    <div class="pointer-events-none absolute -right-16 -top-24 h-80 w-80 rounded-full bg-surface-bright/10 blur-2xl"></div>
                    <div class="pointer-events-none absolute right-1/4 bottom-0 h-44 w-44 rounded-full bg-yellow-300/10 blur-xl"></div>
                    <div class="relative z-10 flex flex-col gap-space-lg lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex max-w-2xl flex-col gap-space-sm">
                            <div class="flex items-center gap-space-xs font-label-upper text-label-upper tracking-wider text-primary-fixed uppercase font-bold">
                                <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24">
                                    <path d="M12 2L1 21h22L12 2zm0 3.8L19.5 19h-15L12 5.8z"></path>
                                </svg>
                                <span>PUSAT EVALUASI & TANTANGAN SISWA • KELAS 7A</span>
                            </div>
                            <h1 class="font-display text-display text-on-primary font-bold text-2xl lg:text-3xl">
                                Latihan Soal & Asesmen Basa Jawa
                            </h1>
                            <p class="font-body text-body text-primary-fixed leading-relaxed">
                                Pilih format latihan pasinaon favoritmu: tes pilihan ganda, kuis wicara (STT/TTS), tracing aksara Jawa ing kanvas, utawa puzzle busana adat kanthi panuntun terstruktur.
                            </p>
                            <!-- Quick Stats Meta Bar -->
                            <div class="mt-space-sm flex flex-wrap items-center gap-space-lg text-on-primary">
                                <div class="flex items-center gap-space-xs">
                                    <svg class="h-4 w-4 text-secondary-fixed-dim" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="16" x2="8" y1="13" y2="13"></line>
                                        <line x1="16" x2="8" y1="17" y2="17"></line>
                                        <polyline points="10 9 9 9 8 9"></polyline>
                                    </svg>
                                    <span class="font-caption text-caption text-primary-fixed">Total Soal:</span>
                                    <span class="font-heading text-heading text-on-primary font-bold">{{ $totalSoal }}</span>
                                    <span class="font-caption text-caption text-primary-fixed">Soal</span>
                                </div>
                                <div class="h-4 w-[1px] bg-white/20 hidden sm:block"></div>
                                <div class="flex items-center gap-space-xs">
                                    <svg class="h-4 w-4 text-yellow-300" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path>
                                    </svg>
                                    <span class="font-caption text-caption text-primary-fixed">Rata-rata Skor:</span>
                                    <span class="font-heading text-heading text-yellow-300 font-bold">92%</span>
                                </div>
                                <div class="h-4 w-[1px] bg-white/20 hidden sm:block"></div>
                                <div class="flex items-center gap-space-xs">
                                    <svg class="h-4 w-4 text-orange-300" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" viewBox="0 0 24 24">
                                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                                    </svg>
                                    <span class="font-caption text-caption text-primary-fixed">Bonus EXP:</span>
                                    <span class="font-heading text-heading text-orange-300 font-bold">+{{ $totalExp }} XP</span>
                                    <span class="font-caption text-caption text-primary-fixed">Dina Iki</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-space-md lg:flex-col lg:items-end">
                            <a href="{{ $kuisItems->isNotEmpty() ? $kuisItems->first()['route'] : url('/kuis/pilihan-ganda') }}" class="inline-flex items-center justify-center gap-space-sm rounded-full bg-color-white font-body text-body font-bold text-primary-700 shadow-md transition-all hover:bg-surface-bright hover:shadow-lg px-6 py-3">
                                <svg class="h-4 w-4 fill-current text-primary-700" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"></path>
                                </svg>
                                <span>Mulai Latihan Harian Campuran</span>
                            </a>
                            <span class="font-caption text-caption text-primary-fixed text-center lg:text-right mt-1">
                                Mode adaptif cerdas • Menyesuaikan kelemahan materi
                            </span>
                        </div>
                    </div>
                </section>

                <!-- SESI LATIHAN INTERAKTIF (5 layar kuis) -->
                <section class="flex flex-col gap-space-sm">
                    <div class="flex items-center justify-between">
                        <span class="font-label-upper text-label-upper tracking-wider text-gray-500 uppercase font-bold">SESI LATIHAN INTERAKTIF</span>
                        @guest
                            <a href="{{ url('/masuk') }}" class="font-caption text-caption text-primary-700 font-semibold hover:underline">Mlebu kanggo miwiti</a>
                        @else
                            <span class="font-caption text-caption text-gray-500">Sugeng Rawuh, <strong class="text-primary-700">{{ $siswa?->nama ?? Auth::user()->name }}</strong></span>
                        @endguest
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-gutter">
                        @php
                            $sesi = [
                                ['href' => url('/kuis/pilihan-ganda'), 'ikon' => 'quiz', 'judul' => 'Pilihan Ganda', 'sub' => 'FR-3 Evaluasi basa'],
                                ['href' => url('/kuis/susun-ukara'), 'ikon' => 'segment', 'judul' => 'Susun Ukara', 'sub' => 'FR-4 Alih basa'],
                                ['href' => url('/kuis/wicara-audio'), 'ikon' => 'graphic_eq', 'judul' => 'Kuis Wicara', 'sub' => 'FR-8 STS/STT'],
                                ['href' => url('/kuis/speak-to-text'), 'ikon' => 'keyboard_voice', 'judul' => 'Speak to Text', 'sub' => 'FR-7 STT'],
                                ['href' => url('/kuis/tracing-aksara'), 'ikon' => 'draw', 'judul' => 'Tracing Aksara', 'sub' => 'FR-22 Kanvas'],
                            ];
                        @endphp
                        @foreach ($sesi as $item)
                            <a href="{{ $item['href'] }}" class="group flex items-center gap-3 rounded-2xl bg-surface-container-lowest p-4 shadow-sm hover:shadow-md border border-gray-100 transition-all">
                                <span class="w-11 h-11 rounded-xl bg-primary-fixed flex items-center justify-center text-primary-700 shrink-0">
                                    <span class="material-symbols-outlined text-[22px]">{{ $item['ikon'] }}</span>
                                </span>
                                <span class="flex flex-col min-w-0">
                                    <span class="font-heading text-body font-bold text-on-surface truncate">{{ $item['judul'] }}</span>
                                    <span class="font-caption text-caption text-gray-500 truncate">{{ $item['sub'] }}</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </section>

                <!-- Filter & Tab Navigasi Kategori Soal -->
                <section class="flex flex-col gap-space-sm">
                    <div class="flex items-center justify-between">
                        <span class="font-label-upper text-label-upper tracking-wider text-gray-500 uppercase font-bold">PILIHAN KATEGORI & MATERI</span>
                        <button type="button" class="font-caption text-caption text-primary-700 font-semibold hover:underline">Reset Pilihan</button>
                    </div>
                    <!-- Horizontal Scrollable Pills -->
                    <div class="flex items-center gap-2 overflow-x-auto pb-2 no-scrollbar">
                        <button class="rounded-full bg-primary-600 px-5 py-2 font-body text-body font-semibold text-on-primary whitespace-nowrap shadow-sm" type="button">
                            Kabeh Soal ({{ $kuisItems->count() }})
                        </button>
                        <button class="rounded-full bg-surface-container px-5 py-2 font-body text-body font-medium text-on-surface-variant hover:bg-surface-container-high transition-colors whitespace-nowrap" type="button">
                            Unggah-Ungguh Basa (Level 1)
                        </button>
                        <button class="rounded-full bg-surface-container px-5 py-2 font-body text-body font-medium text-on-surface-variant hover:bg-surface-container-high transition-colors whitespace-nowrap" type="button">
                            Sandhangan Swara & Aksara (Level 2)
                        </button>
                        <button class="rounded-full bg-surface-container px-5 py-2 font-body text-body font-medium text-on-surface-variant hover:bg-surface-container-high transition-colors whitespace-nowrap" type="button">
                            Busana Adat (Level 3)
                        </button>
                        <button class="rounded-full bg-surface-container px-5 py-2 font-body text-body font-medium text-on-surface-variant hover:bg-surface-container-high transition-colors whitespace-nowrap" type="button">
                            Paribasan & Cerita Rakyat (Level 4)
                        </button>
                        <button class="rounded-full bg-surface-container px-5 py-2 font-body text-body font-medium text-on-surface-variant hover:bg-surface-container-high transition-colors whitespace-nowrap flex items-center gap-1.5 text-orange-500" type="button">
                            <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24">
                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                            </svg>
                            <span>Kuis Kilat 5 Menit</span>
                        </button>
                    </div>
                </section>

                <!-- MAIN CONTENT GRID: List of Quizzes -->
                <section class="flex flex-col gap-space-md w-full">
                    @forelse ($kuisItems as $item)
                        @php
                            $isSelesai  = $item['status'] === 'selesai';
                            $isBerjalan = $item['status'] === 'berjalan';
                            $isTerkunci = $item['status'] === 'terkunci';
                        @endphp
                        <div class="flex flex-col gap-space-sm rounded-[20px] p-6 shadow-sm transition-all hover:shadow-md border
                            {{ $isTerkunci ? 'bg-surface-container-highest/60 opacity-85 border-gray-200' : ($isBerjalan ? 'bg-surface-container-lowest ring-2 ring-primary-600/30 border-gray-100' : 'bg-surface-container-lowest border-gray-100') }}
                        ">
                            {{-- Header row --}}
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-xs">
                                <div class="flex items-center gap-2">
                                    <span class="font-label-upper text-label-upper text-gray-500 uppercase font-semibold">
                                        LEVEL {{ $item['level_urutan'] }}
                                        <span class="mx-1">•</span>
                                        @if($isSelesai)
                                            <span class="text-green-500 font-bold">RAMPUNG</span>
                                        @elseif($isBerjalan)
                                            <span class="text-orange-500 font-bold">SEDANG DILAKONI</span>
                                        @elseif(!$isTerkunci)
                                            <span class="text-primary-700 font-bold">ANYAR</span>
                                        @else
                                            TERKUNCI
                                        @endif
                                    </span>
                                    <span class="font-caption text-caption text-gray-500 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">{{ $item['tipe_ikon'] }}</span>
                                        {{ $item['tipe_label'] }}
                                    </span>
                                </div>
                                @if($isSelesai)
                                    <div class="flex items-center gap-1 text-green-500">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path>
                                        </svg>
                                        <span class="font-label-upper text-label-upper text-green-500 font-semibold">RAMPUNG</span>
                                    </div>
                                @elseif($isTerkunci)
                                    <div class="flex items-center gap-1 text-gray-500">
                                        <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                                            <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"></path>
                                        </svg>
                                        <span class="font-label-upper text-label-upper font-semibold">TERKUNCI</span>
                                    </div>
                                @else
                                    <span class="font-label-upper text-label-upper text-primary-700 font-bold">{{ $item['jumlah_soal'] }} PITAKON</span>
                                @endif
                            </div>

                            {{-- Content row --}}
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-md">
                                <div class="flex flex-col max-w-xl">
                                    <h3 class="font-heading text-heading font-bold {{ $isTerkunci ? 'text-gray-500' : 'text-on-surface' }}">
                                        {{ $item['level_nama'] }}
                                    </h3>
                                    @if($item['deskripsi'])
                                        <p class="font-caption text-caption {{ $isTerkunci ? 'text-gray-500' : 'text-on-surface-variant' }} mt-1 leading-relaxed">
                                            {{ $item['deskripsi'] }}
                                        </p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-space-md shrink-0">
                                    <div class="flex flex-col items-end">
                                        <span class="font-caption text-caption text-gray-500">Ganjaran</span>
                                        <span class="font-heading text-heading {{ $isTerkunci ? 'text-gray-500' : 'text-primary-700' }} font-bold">+{{ $item['reward_exp'] }} XP</span>
                                    </div>
                                    @if($isTerkunci)
                                        <button class="rounded-full bg-gray-200 px-5 py-2 font-body text-body font-semibold text-gray-500 cursor-not-allowed" disabled type="button">
                                            Terkunci
                                        </button>
                                    @elseif($isSelesai)
                                        <a href="{{ $item['route'] }}" class="rounded-full bg-surface-container-high px-5 py-2 font-body text-body font-semibold text-on-surface hover:bg-surface-dim transition-colors">
                                            Latihan Maneh
                                        </a>
                                    @elseif($isBerjalan)
                                        <a href="{{ $item['route'] }}" class="rounded-full bg-primary-600 px-5 py-2 font-body text-body font-semibold text-on-primary hover:bg-primary-700 transition-colors shadow-sm">
                                            Terusake Garap
                                        </a>
                                    @else
                                        <a href="{{ $item['route'] }}" class="rounded-full bg-primary-600 px-5 py-2 font-body text-body font-semibold text-on-primary hover:bg-primary-700 transition-colors shadow-sm">
                                            Mulai Kuis
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-surface-container-lowest rounded-2xl p-10 text-center border border-gray-100 shadow-sm">
                            <span class="material-symbols-outlined text-[48px] text-gray-500">quiz</span>
                            <h2 class="font-heading text-heading font-bold text-on-surface mt-3">Durung ana kuis</h2>
                            <p class="font-body text-body text-gray-500 mt-1">Takon guru kanggo nambah soal ing level materi.</p>
                        </div>
                    @endforelse
                </section>
            </div>
        </main>
    </div>
</body>
</html>