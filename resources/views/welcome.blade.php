<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Siswa - Sinau Jowo Web</title>
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
    <x-sidebar active="beranda" />

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
            <div class="flex flex-col w-full pb-space-xl">
                <div class="grid grid-cols-12 gap-gutter-desktop items-start">
                    <!-- MAIN CONTENT COLUMN (12 Columns / Full Width) -->
                    <div class="col-span-12 flex flex-col gap-space-lg">
                        <!-- 1. Hero Greeting Banner -->
                        <section class="relative overflow-hidden rounded-[20px] bg-gradient-to-r from-primary-700 via-primary-600 to-primary text-on-primary p-8 shadow-md">
                            <svg class="absolute -right-8 -bottom-10 w-80 h-80 text-on-primary opacity-10 pointer-events-none select-none" fill="currentColor" viewBox="0 0 200 200">
                                <path d="M42,50 C48,28 72,24 85,42 C92,52 90,68 82,75 C70,85 54,82 50,98 C46,112 58,124 74,124 C90,124 102,108 102,92 C102,68 122,50 146,50 C168,50 184,68 184,90 C184,118 162,142 134,146 C118,148 104,158 98,172 C92,186 78,194 62,192 C40,190 26,170 30,148 C34,130 50,118 48,102 C46,88 34,74 42,50 Z M124,96 C124,106 132,114 142,114 C152,114 160,106 160,96 C160,86 152,78 142,78 C132,78 124,86 124,96 Z"></path>
                            </svg>
                            <div class="relative z-10 flex flex-col">
                                <div class="flex items-center gap-space-xs mb-2">
                                    <span class="inline-block w-2 h-2 rounded-full bg-yellow-300"></span>
                                    <span class="font-label-upper text-label-upper text-yellow-300 uppercase tracking-widest font-bold">Sinau Jowo Web • {{ $siswa->kelas ? 'Kelas '.$siswa->kelas : 'Siswa' }}</span>
                                </div>
                                <h1 class="font-display text-display text-on-primary font-extrabold tracking-tight mb-space-xs">
                                    Sugeng Rawuh, {{ $siswa->nama_lengkap }}!
                                </h1>
                                <p class="font-body text-body text-primary-fixed leading-relaxed mb-6">
                                    Ayo terusake pasinaon Basa lan Budaya Jawa dina iki. Rampungake gladhi soal lan tingkatake EXP kanggo munggah ing papan skor!
                                </p>
                                <div class="flex flex-wrap items-center gap-space-md">
                                    <a href="{{ route('siswa.latihan', ['level_materi_id' => $activeLevel?->id]) }}" class="inline-flex items-center gap-space-xs bg-color-white text-primary-700 hover:bg-surface-container-low transition-all duration-200 px-6 py-3 rounded-full font-body text-body font-bold shadow-sm hover:shadow-md">
                                        <svg class="w-4 h-4 text-primary-700" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"></path>
                                        </svg>
                                        <span>Lanjutake Pasinaon (Level {{ $activeLevel?->urutan ?? 1 }}: {{ $activeLevel?->nama_materi ?? 'Dasar' }})</span>
                                    </a>
                                    <a href="{{ route('kuis.wicara-audio') }}" class="inline-flex items-center gap-space-xs bg-on-primary/15 hover:bg-on-primary/25 text-on-primary transition-all duration-200 px-5 py-3 rounded-full font-body text-body font-semibold backdrop-blur-sm">
                                        <svg class="w-4 h-4 text-on-primary" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"></path>
                                            <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                                            <line x1="12" x2="12" y1="19" y2="22"></line>
                                        </svg>
                                        <span>Kuis Wicara (STT/Audio)</span>
                                    </a>
                                </div>
                            </div>
                        </section>

                        <!-- 2. Student Gamification Stat Cards (3 Cards) -->
                        <section class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
                            <!-- Card 1: Streak Harian -->
                            <div class="bg-orange-300 rounded-2xl p-5 flex flex-col justify-between shadow-sm hover:shadow-md transition-all relative overflow-hidden">
                                <div class="flex items-start justify-between mb-4 z-10">
                                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-tertiary font-bold">Streak Pasinaon</span>
                                    <div class="w-9 h-9 rounded-xl bg-color-white/60 flex items-center justify-center text-orange-500 shadow-sm">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12.5 2c-.3 0-.6.2-.7.5C11 5.3 8.8 8 7 10.5 5.5 12.6 5 14.2 5 16c0 3.9 3.1 7 7 7s7-3.1 7-7c0-2.8-1.5-5.6-3.2-7.8-.3-.4-.9-.3-1.1.1-.9 1.6-2 3.2-3.2 4.6-.2.2-.5.1-.6-.2-.7-1.7-1.4-4-1.4-6.2 0-2.3 1-3.6 1.4-4.5.1-.3-.1-.5-.4-.5z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="z-10">
                                    <div class="flex items-baseline gap-space-xs mb-1">
                                        <span class="font-stat-number text-stat-number font-extrabold text-on-tertiary-fixed">{{ $currentStreak }}</span>
                                        <span class="font-heading text-heading font-bold text-on-tertiary-fixed">Dina Aktif</span>
                                    </div>
                                    <p class="font-caption text-caption text-tertiary font-medium">
                                        Rekor paling dhuwur: {{ $highestStreak }} Dina
                                    </p>
                                </div>
                            </div>

                            <!-- Card 2: Total EXP & Level -->
                            <div class="bg-yellow-300 rounded-2xl p-5 flex flex-col justify-between shadow-sm hover:shadow-md transition-all relative overflow-hidden">
                                <div class="flex items-start justify-between mb-4 z-10">
                                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-tertiary font-bold">Total Celengan EXP</span>
                                    <div class="w-9 h-9 rounded-xl bg-color-white/60 flex items-center justify-center text-tertiary shadow-sm">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                        </svg>
                                    </div>
                                </div>
                                <div class="z-10">
                                    <div class="flex items-baseline gap-space-xs mb-1">
                                        <span class="font-stat-number text-stat-number font-extrabold text-on-tertiary-fixed">{{ number_format($totalExp) }}</span>
                                        <span class="font-heading text-heading font-bold text-on-tertiary-fixed">XP</span>
                                    </div>
                                    <div class="flex items-center gap-space-sm font-caption text-caption text-tertiary font-medium">
                                        <span class="font-semibold text-on-tertiary-fixed">
                                            Tingkat: {{ $totalExp >= 1000 ? 'Wasasis (Mahir)' : ($totalExp >= 300 ? 'Madya' : 'Pratama') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 3: Aksara Tracing Mastery -->
                            <div class="bg-primary-fixed rounded-2xl p-5 flex flex-col justify-between shadow-sm hover:shadow-md transition-all relative overflow-hidden">
                                <div class="flex items-start justify-between mb-4 z-10">
                                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-primary font-bold">Tracing Aksara FR-22</span>
                                    <div class="w-9 h-9 rounded-xl bg-color-white/60 flex items-center justify-center text-primary shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="m18 2 4 4-12 12H6v-4L18 2z"></path>
                                            <path d="m14 6 4 4"></path>
                                            <path d="M4 22h16"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="z-10">
                                    <div class="flex items-baseline gap-space-xs mb-1">
                                        <span class="font-stat-number text-stat-number font-extrabold text-on-primary-fixed">{{ $aksaraDikuasai }}</span>
                                        <span class="font-heading text-heading font-bold text-primary">saka {{ $totalAksara }} Aksara</span>
                                    </div>
                                    <p class="font-caption text-caption text-on-primary-fixed-variant font-medium">
                                        {{ $aksaraPersen }}% aksara kasil ditelusuri kanthi sae
                                    </p>
                                </div>
                            </div>
                        </section>

                        <!-- 3. Tantangan Hari Ini & Kuis Terakhir -->
                        <section class="grid grid-cols-1 md:grid-cols-12 gap-gutter">
                            <!-- Featured Challenge Card (7 cols) -->
                            <div class="md:col-span-7 bg-primary-600 text-on-primary rounded-2xl p-6 flex flex-col justify-between shadow-md relative overflow-hidden">
                                <div class="relative z-10 flex flex-col">
                                    <div class="flex items-center justify-between gap-space-sm mb-3">
                                        <span class="font-label-upper text-label-upper font-bold uppercase tracking-wider text-primary-fixed">
                                            @if($tantangan)
                                                Tantangan Dina Iki • Bonus +{{ $tantangan->bobot_exp }} EXP
                                            @else
                                                Tantangan Dina Iki
                                            @endif
                                        </span>
                                    </div>
                                    @if($tantangan)
                                        <h2 class="font-heading text-heading font-extrabold text-on-primary leading-snug mb-2">
                                            {{ Str::limit($tantangan->pertanyaan, 90) }}
                                        </h2>
                                        <p class="font-caption text-caption text-on-primary/80 mb-6 leading-relaxed">
                                            Level {{ $tantangan->levelMateri?->urutan }} — {{ $tantangan->levelMateri?->nama_materi }}. Coba wangsuli lan bukteake kawruhmu!
                                        </p>
                                    @else
                                        <h2 class="font-heading text-heading font-extrabold text-on-primary leading-snug mb-2">
                                            Kabeh Soal Wis Rampung Digarap!
                                        </h2>
                                        <p class="font-caption text-caption text-on-primary/80 mb-6 leading-relaxed">
                                            Sampeyan wis ngrampungake kabeh soal sing kasedhiya. Enteni soal anyar saka guru utawa gladhi maneh kanggo ngunggahake skor.
                                        </p>
                                    @endif
                                </div>
                                <div class="relative z-10 flex items-center justify-between pt-2">
                                    <span class="font-caption text-caption text-primary-fixed">
                                        {{ $siswa->kelas ? 'Kelas '.$siswa->kelas : 'Sinau Jowo' }}
                                    </span>
                                    @if($tantangan)
                                        @php
                                            $urlTantangan = app(\App\Http\Controllers\Web\KuisSesiController::class)->urlForSoal($tantangan);
                                        @endphp
                                        <a href="{{ $urlTantangan }}" class="bg-color-white text-primary-700 hover:bg-surface-container-low transition px-5 py-2.5 rounded-full font-body text-body font-bold shadow-sm">
                                            Mulai Saiki
                                        </a>
                                    @else
                                        <a href="{{ route('siswa.latihan') }}" class="bg-color-white text-primary-700 hover:bg-surface-container-low transition px-5 py-2.5 rounded-full font-body text-body font-bold shadow-sm">
                                            Daftar Soal
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Recent Activity Card (5 cols) -->
                            <div class="md:col-span-5 bg-pink-100/70 rounded-2xl p-6 flex flex-col justify-between shadow-sm">
                                <div>
                                    <span class="font-label-upper text-label-upper font-bold uppercase tracking-wider text-secondary mb-2 block">
                                        Kuis Pungkasan
                                    </span>
                                    @if($recentProgress)
                                        <h3 class="font-heading text-heading font-extrabold text-on-surface mb-2">
                                            {{ $recentProgress['nama_materi'] }}
                                        </h3>
                                        <p class="font-caption text-caption text-on-surface-variant mb-4">
                                            {{ $recentProgress['selesai_soal'] }} saka {{ $recentProgress['total_soal'] }} soal wis kasil diselesaiake.
                                        </p>
                                    @else
                                        <h3 class="font-heading text-heading font-extrabold text-on-surface mb-2">
                                            Durung Ana Aktivitas Kuis
                                        </h3>
                                        <p class="font-caption text-caption text-on-surface-variant mb-4">
                                            Pilih level materi lan miwiti gladhi soal kapisanmu saiki!
                                        </p>
                                    @endif
                                </div>
                                @if($recentProgress)
                                    <div class="flex items-center gap-space-md bg-surface-container-lowest/80 rounded-xl p-3">
                                        <div class="relative w-12 h-12 flex-shrink-0">
                                            <svg class="w-12 h-12 -rotate-90" viewBox="0 0 36 36">
                                                <path class="text-surface-container-high" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3.5"></path>
                                                <path class="text-pink-500" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-dasharray="{{ $recentProgress['persen'] }}, 100" stroke-linecap="round" stroke-width="3.5"></path>
                                            </svg>
                                            <span class="absolute inset-0 flex items-center justify-center font-label-upper text-label-upper font-bold text-on-surface">{{ $recentProgress['persen'] }}%</span>
                                        </div>
                                        <div class="flex flex-col flex-1 min-w-0">
                                            <span class="font-body text-body font-bold text-on-surface truncate">{{ $recentProgress['selesai_soal'] }} saka {{ $recentProgress['total_soal'] }} Rampung</span>
                                            <a href="{{ route('siswa.latihan', ['level_materi_id' => $recentProgress['level_id']]) }}" class="font-caption text-caption text-secondary font-bold hover:underline inline-flex items-center gap-1 mt-0.5">
                                                <span>Terusake</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"></path></svg>
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </section>

                        <!-- 4. Jalur Pasinaon Berjenjang (Level Progression) -->
                        <section class="flex flex-col gap-space-md mt-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="font-display text-heading font-extrabold text-on-surface tracking-tight">
                                        Jalur Pasinaon Berjenjang (FR-2)
                                    </h2>
                                    <p class="font-caption text-caption text-on-surface-variant">
                                        Level materi Basa lan Budaya Jawa sing kabuka manut progres pengerjaanmu.
                                    </p>
                                </div>
                                <a href="{{ route('siswa.latihan') }}" class="font-caption text-caption text-primary-600 font-bold hover:text-primary-700 hover:underline flex items-center gap-1">
                                    <span>Deleng Kabeh Soal</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"></path></svg>
                                </a>
                            </div>
                            <div class="flex flex-col gap-space-xs">
                                @forelse($levels as $lvl)
                                    @if($lvl->status === 'selesai')
                                        <!-- Level Selesai -->
                                        <div class="bg-surface-container-lowest rounded-2xl p-5 shadow-sm hover:shadow-md transition flex flex-col md:flex-row md:items-center justify-between gap-space-md">
                                            <div class="flex items-start gap-space-md">
                                                <div class="w-12 h-12 rounded-2xl bg-green-500/10 flex items-center justify-center text-green-500 flex-shrink-0 mt-1 md:mt-0">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 24 24">
                                                        <polyline points="20 6 9 17 4 12"></polyline>
                                                    </svg>
                                                </div>
                                                <div class="flex flex-col">
                                                    <div class="font-label-upper text-label-upper font-bold uppercase tracking-wider mb-1 text-gray-500">
                                                        LEVEL {{ $lvl->urutan }} <span class="mx-1.5">•</span> <span class="text-green-500">RAMPUNG</span>
                                                    </div>
                                                    <h3 class="font-heading text-heading font-bold text-on-surface mb-1">
                                                        {{ $lvl->nama_materi }}
                                                    </h3>
                                                    <p class="font-caption text-caption text-on-surface-variant">
                                                        {{ $lvl->total_soal }} Soal • {{ $lvl->deskripsi ?? 'Materi pasinaon Basa Jawa' }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-space-md justify-between md:justify-end self-end md:self-center w-full md:w-auto pt-2 md:pt-0">
                                                <div class="hidden sm:flex flex-col text-right">
                                                    <span class="font-label-upper text-label-upper font-bold text-green-500">Rata-rata: {{ $lvl->rata_skor }}/100</span>
                                                    <span class="font-caption text-caption text-gray-500">+{{ $lvl->reward_exp }} EXP Bonus</span>
                                                </div>
                                                <a href="{{ route('siswa.latihan', ['level_materi_id' => $lvl->id]) }}" class="px-5 py-2 rounded-full font-body text-body font-semibold text-on-surface-variant bg-gray-50 hover:bg-surface-container-high transition">
                                                    Gladhi Maneh
                                                </a>
                                            </div>
                                        </div>
                                    @elseif($lvl->status === 'berjalan')
                                        <!-- Level Sedang Berjalan -->
                                        <div class="bg-surface-container-lowest rounded-2xl p-5 shadow-sm hover:shadow-md transition flex flex-col md:flex-row md:items-center justify-between gap-space-md bg-gradient-to-r from-surface-container-lowest via-surface-container-low/40 to-surface-container-lowest border-2 border-primary-600/30">
                                            <div class="flex items-start gap-space-md flex-1">
                                                <div class="w-12 h-12 rounded-2xl bg-primary-600 text-on-primary flex items-center justify-center flex-shrink-0 shadow-sm mt-1 md:mt-0">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path d="M12 20h9"></path>
                                                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                                    </svg>
                                                </div>
                                                <div class="flex flex-col flex-1 max-w-lg">
                                                    <div class="font-label-upper text-label-upper font-bold uppercase tracking-wider mb-1 text-gray-500">
                                                        LEVEL {{ $lvl->urutan }} <span class="mx-1.5">•</span> <span class="text-primary-600">SEDANG BERJALAN</span>
                                                    </div>
                                                    <h3 class="font-heading text-heading font-bold text-primary-700 mb-1">
                                                        {{ $lvl->nama_materi }}
                                                    </h3>
                                                    <p class="font-caption text-caption text-on-surface-variant mb-3">
                                                        {{ $lvl->deskripsi ?? 'Rampungake kabeh gladhi kanggo mbukak level sabanjure.' }}
                                                    </p>
                                                    <div class="flex items-center gap-space-sm w-full">
                                                        <div class="w-full bg-surface-container-high h-2.5 rounded-full overflow-hidden">
                                                            <div class="bg-primary-600 h-full rounded-full transition-all" style="width: {{ $lvl->persen }}%"></div>
                                                        </div>
                                                        <span class="font-caption text-caption font-bold text-primary-600 whitespace-nowrap">{{ $lvl->lulus_count }} / {{ $lvl->total_soal }} Rampung ({{ $lvl->persen }}%)</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-end self-end md:self-center w-full md:w-auto pt-2 md:pt-0">
                                                <a href="{{ route('siswa.latihan', ['level_materi_id' => $lvl->id]) }}" class="px-6 py-2.5 rounded-full font-body text-body font-bold text-on-primary bg-primary-600 hover:bg-primary-700 transition shadow-sm">
                                                    Lanjutake Belajar
                                                </a>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Level Terkunci -->
                                        <div class="bg-surface-container-lowest/60 rounded-2xl p-5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-space-md opacity-75">
                                            <div class="flex items-start gap-space-md">
                                                <div class="w-12 h-12 rounded-2xl bg-gray-200 flex items-center justify-center text-gray-500 flex-shrink-0 mt-1 md:mt-0">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"></path>
                                                    </svg>
                                                </div>
                                                <div class="flex flex-col">
                                                    <div class="font-label-upper text-label-upper font-bold uppercase tracking-wider mb-1 text-gray-500">
                                                        LEVEL {{ $lvl->urutan }} <span class="mx-1.5">•</span> <span class="text-gray-500">TERKUNCI</span>
                                                    </div>
                                                    <h3 class="font-heading text-heading font-bold text-gray-500 mb-1">
                                                        {{ $lvl->nama_materi }}
                                                    </h3>
                                                    <p class="font-caption text-caption text-gray-500">
                                                        {{ $lvl->deskripsi ?? 'Materi iki bakal kabuka sawise level sadurunge rampung.' }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="self-end md:self-center">
                                                <span class="font-caption text-caption text-gray-500 italic bg-gray-50 px-4 py-2 rounded-full">
                                                    Terkunci: Rampungake Level {{ $lvl->urutan - 1 }} dhisik
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                @empty
                                    <div class="bg-surface-container-lowest rounded-2xl p-8 text-center text-gray-500">
                                        Durung ana level materi sing kasedhiya.
                                    </div>
                                @endforelse
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
