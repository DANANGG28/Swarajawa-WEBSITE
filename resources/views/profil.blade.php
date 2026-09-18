<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil, Setelan & Koleksi Badge Siswa - Sinau Jowo Web</title>
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
        .clip-hexagon {
            clip-path: polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%);
        }
        .clip-pentagon {
            clip-path: polygon(50% 0%, 100% 38%, 81% 100%, 19% 100%, 0% 38%);
        }
        .clip-shield {
            clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
        }
        .clip-rhombus {
            clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%);
        }
        .clip-octagon {
            clip-path: polygon(30% 0%, 70% 0%, 100% 30%, 100% 70%, 70% 100%, 30% 100%, 0% 70%, 0% 30%);
        }
    </style>
</head>
<body class="bg-background font-body text-on-surface antialiased">
    <!-- ASIDE SIDEBAR COMPONENT -->
    <x-sidebar active="profil" />

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
            <div class="flex flex-col w-full gap-space-lg">

                <!-- Profile Hero / Header Card -->
                <section class="w-full bg-surface-container-lowest rounded-2xl shadow-sm overflow-hidden flex flex-col border border-gray-100">
                    <!-- Top Decorative Banner -->
                    <div class="w-full bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 relative flex items-end px-8 h-28">
                        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:16px_16px]"></div>
                        <div class="absolute right-6 top-4 flex items-center gap-1.5 text-white/80 text-caption font-caption">
                            <span class="material-symbols-outlined text-sm">school</span>
                            <span>Semester Ganjil 2024/2025</span>
                        </div>
                    </div>
                    <!-- Main Profile Content Area -->
                    <div class="px-8 pb-6 pt-0 relative flex flex-col md:flex-row md:items-end justify-between gap-4">
                        <div class="flex flex-col sm:flex-row items-start gap-5 -mt-14 z-10">
                            <div class="relative w-28 h-28 rounded-2xl bg-surface-container-lowest p-1 shadow-md shrink-0">
                                <div class="w-full h-full rounded-xl bg-primary-700 text-white font-bold text-3xl flex items-center justify-center">
                                    {{ mb_strtoupper(mb_substr($siswa->nama_lengkap, 0, 2)) }}
                                </div>
                                <span class="absolute bottom-2 right-2 w-4 h-4 bg-green-500 rounded-full border-2 border-surface-container-lowest" title="Aktif Sinau"></span>
                            </div>
                            <div class="flex flex-col min-w-0 pt-16 sm:pt-16">
                                <div class="flex items-center gap-2">
                                    <h1 class="font-heading text-display text-on-surface font-extrabold tracking-tight text-2xl">{{ $siswa->nama_lengkap }}</h1>
                                    <span class="material-symbols-outlined text-primary-600 text-xl" title="Siswa Terverifikasi">verified</span>
                                </div>
                                <div class="font-caption text-caption text-gray-500 mt-0.5">NIS: {{ $siswa->nis ?? '-' }} • KELAS {{ $siswa->kelas ? $siswa->kelas : 'SISWA' }} • SINAU JOWO</div>
                                <p class="font-body text-body text-on-surface-variant mt-1 italic max-w-xl">“Siswa sregep nyinau unggah-ungguh basa lan aksara Jawa.”</p>
                            </div>
                        </div>
                    </div>

                    <!-- 3-Column Key Performance Stats Metrics -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 px-8 pb-6 pt-2 border-t border-gray-100">
                        <!-- Stat 1: EXP -->
                        <div class="bg-surface-container-low p-4 rounded-xl flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-primary-fixed flex items-center justify-center text-primary-700 shrink-0">
                                <span class="material-symbols-outlined text-2xl">stars</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-label-upper text-label-upper text-gray-500 uppercase tracking-wider font-bold">Total Poin Sinau</span>
                                <div class="flex items-baseline gap-1">
                                    <span class="font-stat-number text-stat-number text-on-surface font-extrabold">{{ number_format($totalExp) }}</span>
                                    <span class="font-body text-body font-bold text-primary-600">XP</span>
                                </div>
                                <span class="font-caption text-caption text-gray-500">Tingkat: {{ $totalExp >= 1000 ? 'Wasasis (Mahir)' : ($totalExp >= 300 ? 'Madya' : 'Pratama') }}</span>
                            </div>
                        </div>
                        <!-- Stat 2: Class Rank -->
                        <div class="bg-surface-container-low p-4 rounded-xl flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-yellow-300/40 flex items-center justify-center text-tertiary shrink-0">
                                <span class="material-symbols-outlined text-2xl">military_tech</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-label-upper text-label-upper text-gray-500 uppercase tracking-wider font-bold">Peringkat Pasinaon</span>
                                <div class="flex items-baseline gap-1">
                                    <span class="font-stat-number text-stat-number text-on-surface font-extrabold">#{{ $myRank }}</span>
                                </div>
                                <span class="font-caption text-caption text-green-500 font-semibold">{{ $completedLevels }} Level Rampung</span>
                            </div>
                        </div>
                        <!-- Stat 3: Daily Streak -->
                        <div class="bg-surface-container-low p-4 rounded-xl flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-orange-300/40 flex items-center justify-center text-orange-500 shrink-0">
                                <span class="material-symbols-outlined text-2xl">local_fire_department</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-label-upper text-label-upper text-gray-500 uppercase tracking-wider font-bold">Streak Konsistensi</span>
                                <div class="flex items-baseline gap-1">
                                    <span class="font-stat-number text-stat-number text-on-surface font-extrabold">{{ $currentStreak }} Dina</span>
                                </div>
                                <span class="font-caption text-caption text-gray-500">Rekor paling dhuwur: {{ $highestStreak }} Dina</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section Tabs Switcher -->
                <div class="flex items-center gap-2 bg-surface-container-lowest p-2 rounded-2xl shadow-sm border border-gray-100 overflow-x-auto">
                    <button type="button" id="tab-btn-badge" onclick="switchProfileTab('badge')" class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-body text-body font-bold bg-primary-600 text-white shadow-sm transition-all whitespace-nowrap">
                        <span class="material-symbols-outlined text-base">workspace_premium</span>
                        <span>Koleksi Badge & Piagam</span>
                    </button>
                    <button type="button" id="tab-btn-akademik" onclick="switchProfileTab('akademik')" class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-body text-body font-medium text-on-surface-variant hover:bg-surface-container-high transition-all whitespace-nowrap">
                        <span class="material-symbols-outlined text-base">school</span>
                        <span>Rincian Profil & Akademik</span>
                    </button>
                    <button type="button" id="tab-btn-audio" onclick="switchProfileTab('audio')" class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-body text-body font-medium text-on-surface-variant hover:bg-surface-container-high transition-all whitespace-nowrap">
                        <span class="material-symbols-outlined text-base">graphic_eq</span>
                        <span>Setelan Pasinaon & Swara AI</span>
                    </button>
                    <button type="button" id="tab-btn-keamanan" onclick="switchProfileTab('keamanan')" class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-body text-body font-medium text-on-surface-variant hover:bg-surface-container-high transition-all whitespace-nowrap">
                        <span class="material-symbols-outlined text-base">lock</span>
                        <span>Keamanan Akun</span>
                    </button>
                </div>

                <!-- TAB PANEL 1: Koleksi Badge & Piagam -->
                <div id="panel-badge" class="flex flex-col gap-6">
                    <!-- Badge Summary Header Card -->
                    <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 border border-gray-100">
                        <div class="flex flex-col">
                            <h2 class="font-heading text-heading text-on-surface font-bold">Koleksi Piagam & Lencana Sinau</h2>
                            <p class="font-body text-body text-on-surface-variant mt-0.5">Lencana otomatis kagayuh nalika ngrampungake tracing aksara, wicara krama, lan kuis kabudayan.</p>
                        </div>
                        <!-- Badge Completion Counter Bar -->
                        @php $persenLevel = $totalLevels > 0 ? (int) round(($completedLevels / $totalLevels) * 100) : 0; @endphp
                        <div class="flex flex-col w-full md:w-80 bg-surface-container-low p-3 rounded-xl border border-primary-100/50">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-caption text-caption text-gray-500 font-semibold">Progres Pasinaon</span>
                                <span class="font-caption text-caption font-bold text-primary-700">{{ $completedLevels }} saka {{ $totalLevels }} Level ({{ $persenLevel }}%)</span>
                            </div>
                            <div class="w-full h-2.5 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-primary-600 rounded-full transition-all" style="width: {{ $persenLevel }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Active / Earned Badges Section -->
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-green-500 text-lg">verified</span>
                                <h2 class="font-heading text-heading text-on-surface font-bold">Lencana Sing Wis Dikantongi ({{ $completedLevels }})</h2>
                            </div>
                            <span class="font-label-upper text-label-upper text-gray-500 uppercase font-semibold">KASIL DIGAYUH • STATUS AKTIF</span>
                        </div>

                        <!-- 3-Column Grid with Distinct Geometric Clip-Path Badge Containers (§5.12 Compliant) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                            <!-- Earned Badge 1: Jawara Hanacaraka (HEXAGON) -->
                            <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow border border-gray-100">
                                <div class="flex items-start gap-4">
                                    <!-- Geometric Container: HEXAGON clip-path with Gold Solid Fill -->
                                    <div class="w-16 h-16 bg-[#F6D98B] flex items-center justify-center shrink-0 text-amber-900 shadow-sm clip-hexagon">
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5m14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-label-upper text-label-upper text-gray-500 uppercase font-semibold">LEVEL 1 <span class="mx-0.5">•</span> <span class="text-green-500 font-bold">RAMPUNG</span></span>
                                        <h3 class="font-heading text-heading text-on-surface font-bold truncate mt-0.5">Jawara Hanacaraka</h3>
                                        <p class="font-caption text-caption text-on-surface-variant mt-1 leading-relaxed">Sukses nulis lan ngapalake 14 aksara nglegena dhasar kanthi presisi dhuwur.</p>
                                    </div>
                                </div>
                                <div class="mt-4 pt-2 bg-surface-container-low p-2 rounded-xl flex items-center justify-between text-caption font-caption text-gray-500 border border-primary-100/30">
                                    <span>Akurasi Tracing: 96%</span>
                                    <span class="text-primary-700 font-bold">+150 XP</span>
                                </div>
                            </div>

                            <!-- Earned Badge 2: Tatas Unggah-Ungguh (PENTAGON) -->
                            <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow border border-gray-100">
                                <div class="flex items-start gap-4">
                                    <!-- Geometric Container: PENTAGON clip-path with Green Solid Fill -->
                                    <div class="w-16 h-16 bg-[#4CAF6D] flex items-center justify-center shrink-0 text-white shadow-sm clip-pentagon">
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-label-upper text-label-upper text-gray-500 uppercase font-semibold">WICARA KRAMA <span class="mx-0.5">•</span> <span class="text-green-500 font-bold">RAMPUNG</span></span>
                                        <h3 class="font-heading text-heading text-on-surface font-bold truncate mt-0.5">Tatas Unggah-Ungguh</h3>
                                        <p class="font-caption text-caption text-on-surface-variant mt-1 leading-relaxed">Nuntasake pacelathon Krama Inggil marang guru lan tiyang sepuh skor 92%.</p>
                                    </div>
                                </div>
                                <div class="mt-4 pt-2 bg-surface-container-low p-2 rounded-xl flex items-center justify-between text-caption font-caption text-gray-500 border border-primary-100/30">
                                    <span>Pelafalan STT: 92%</span>
                                    <span class="text-primary-700 font-bold">+200 XP</span>
                                </div>
                            </div>

                            <!-- Earned Badge 3: Prajurit Sandhangan (SHIELD) -->
                            <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow border border-gray-100">
                                <div class="flex items-start gap-4">
                                    <!-- Geometric Container: SHIELD clip-path with Primary Purple Solid Fill -->
                                    <div class="w-16 h-16 bg-[#6C5CE8] flex items-center justify-center shrink-0 text-white shadow-sm clip-shield">
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-label-upper text-label-upper text-gray-500 uppercase font-semibold">AKSARA JAWA <span class="mx-0.5">•</span> <span class="text-green-500 font-bold">RAMPUNG</span></span>
                                        <h3 class="font-heading text-heading text-on-surface font-bold truncate mt-0.5">Prajurit Sandhangan</h3>
                                        <p class="font-caption text-caption text-on-surface-variant mt-1 leading-relaxed">Paham panggunaan Wulu, Suku, Taling, lan Tarung ing 20 ukara latihan.</p>
                                    </div>
                                </div>
                                <div class="mt-4 pt-2 bg-surface-container-low p-2 rounded-xl flex items-center justify-between text-caption font-caption text-gray-500 border border-primary-100/30">
                                    <span>Kuis Pasangan: 100/100</span>
                                    <span class="text-primary-700 font-bold">+180 XP</span>
                                </div>
                            </div>

                            <!-- Earned Badge 4: Busana Gagrag Anyar (RHOMBUS / DIAMOND) -->
                            <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow border border-gray-100">
                                <div class="flex items-start gap-4">
                                    <!-- Geometric Container: RHOMBUS/DIAMOND clip-path with Orange Solid Fill -->
                                    <div class="w-16 h-16 bg-[#F0955A] flex items-center justify-center shrink-0 text-white shadow-sm clip-rhombus">
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-label-upper text-label-upper text-gray-500 uppercase font-semibold">BUDAYA JAWA <span class="mx-0.5">•</span> <span class="text-green-500 font-bold">RAMPUNG</span></span>
                                        <h3 class="font-heading text-heading text-on-surface font-bold truncate mt-0.5">Busana Gagrag Anyar</h3>
                                        <p class="font-caption text-caption text-on-surface-variant mt-1 leading-relaxed">Ngrampungake tebak busana adat: Jarik, Beskap, lan Blangkon Jawa Wetan.</p>
                                    </div>
                                </div>
                                <div class="mt-4 pt-2 bg-surface-container-low p-2 rounded-xl flex items-center justify-between text-caption font-caption text-gray-500 border border-primary-100/30">
                                    <span>Teka-Teki Silang Budaya</span>
                                    <span class="text-primary-700 font-bold">+120 XP</span>
                                </div>
                            </div>

                            <!-- Earned Badge 5: Wicara Prigel (OCTAGON) -->
                            <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow border border-gray-100">
                                <div class="flex items-start gap-4">
                                    <!-- Geometric Container: OCTAGON clip-path with Indigo Solid Fill -->
                                    <div class="w-16 h-16 bg-[#7B6CF0] flex items-center justify-center shrink-0 text-white shadow-sm clip-octagon">
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm5.3-3c0 3-2.54 5.1-5.3 5.1S6.7 14 6.7 11H5c0 3.41 2.72 6.23 6 6.72V21h2v-3.28c3.28-.48 6-3.3 6-6.72h-1.7z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-label-upper text-label-upper text-gray-500 uppercase font-semibold">AI STT RECOGNITION <span class="mx-0.5">•</span> <span class="text-green-500 font-bold">RAMPUNG</span></span>
                                        <h3 class="font-heading text-heading text-on-surface font-bold truncate mt-0.5">Wicara Prigel</h3>
                                        <p class="font-caption text-caption text-on-surface-variant mt-1 leading-relaxed">Latihan pelafalan swara Jawa mawa kecerdasan buatan kaping 10 berturut-turut.</p>
                                    </div>
                                </div>
                                <div class="mt-4 pt-2 bg-surface-container-low p-2 rounded-xl flex items-center justify-between text-caption font-caption text-gray-500 border border-primary-100/30">
                                    <span>Evaluasi Swara AI</span>
                                    <span class="text-primary-700 font-bold">+220 XP</span>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Locked Badges Section -->
                    <div class="flex flex-col gap-3 pt-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-gray-500 text-lg">lock</span>
                                <h2 class="font-heading text-heading text-on-surface font-bold">Lencana Sing Isih Kunci (7)</h2>
                            </div>
                            <span class="font-label-upper text-label-upper text-gray-500 uppercase font-semibold">SYARAT GAYUH • TINGKAT LANJUT</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- Locked Badge 1: Empu Aksara Murda & Swara (HEXAGON) -->
                            <div class="bg-surface-container-lowest/70 p-5 rounded-2xl shadow-sm flex flex-col justify-between opacity-80 border border-gray-200">
                                <div class="flex items-start gap-4">
                                    <div class="w-16 h-16 bg-[#E4E4EC] flex items-center justify-center shrink-0 text-gray-500 clip-hexagon">
                                        <span class="material-symbols-outlined text-2xl">lock</span>
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-label-upper text-label-upper text-gray-500 uppercase font-semibold">LEVEL 3 <span class="mx-0.5">•</span> KUNCI</span>
                                        <h3 class="font-heading text-heading text-on-surface-variant font-bold truncate mt-0.5">Empu Aksara Murda & Swara</h3>
                                        <p class="font-caption text-caption text-gray-500 mt-1 leading-relaxed">Rampungake piwulangan aksara murda, swara, lan rekan kanthi biji minimal 85.</p>
                                    </div>
                                </div>
                                <div class="mt-4 pt-2 bg-surface-container-low p-2 rounded-xl text-caption font-caption text-gray-500 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">info</span>
                                    <span>Syarat: Tuntasake Bab 3 Dhisik</span>
                                </div>
                            </div>

                            <!-- Locked Badge 2: Pujangga Paribasan (PENTAGON) -->
                            <div class="bg-surface-container-lowest/70 p-5 rounded-2xl shadow-sm flex flex-col justify-between opacity-80 border border-gray-200">
                                <div class="flex items-start gap-4">
                                    <div class="w-16 h-16 bg-[#E4E4EC] flex items-center justify-center shrink-0 text-gray-500 clip-pentagon">
                                        <span class="material-symbols-outlined text-2xl">lock</span>
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-label-upper text-label-upper text-gray-500 uppercase font-semibold">PARIBASAN <span class="mx-0.5">•</span> KUNCI</span>
                                        <h3 class="font-heading text-heading text-on-surface-variant font-bold truncate mt-0.5">Pujangga Paribasan</h3>
                                        <p class="font-caption text-caption text-gray-500 mt-1 leading-relaxed">Bisa ngrampungake kuis unen-unen, bebasan, lan saloka kanthi skor sampurna 100.</p>
                                    </div>
                                </div>
                                <div class="mt-4 pt-2 bg-surface-container-low p-2 rounded-xl text-caption font-caption text-gray-500 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">info</span>
                                    <span>Syarat: Skor Kuis Paribasan &gt; 95</span>
                                </div>
                            </div>

                            <!-- Locked Badge 3: Gathutkaca Streak Master (SHIELD) -->
                            <div class="bg-surface-container-lowest/70 p-5 rounded-2xl shadow-sm flex flex-col justify-between opacity-80 border border-gray-200">
                                <div class="flex items-start gap-4">
                                    <div class="w-16 h-16 bg-[#E4E4EC] flex items-center justify-center shrink-0 text-gray-500 clip-shield">
                                        <span class="material-symbols-outlined text-2xl">lock</span>
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-label-upper text-label-upper text-gray-500 uppercase font-semibold">KONSISTENSI <span class="mx-0.5">•</span> KUNCI</span>
                                        <h3 class="font-heading text-heading text-on-surface-variant font-bold truncate mt-0.5">Gathutkaca Streak Master</h3>
                                        <p class="font-caption text-caption text-gray-500 mt-1 leading-relaxed">Gayuh streak sinau aktif tanpa pedhot sajrone 15 dina berturut-turut.</p>
                                    </div>
                                </div>
                                <div class="mt-4 pt-2 bg-surface-container-low p-2 rounded-xl text-caption font-caption text-gray-500 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">info</span>
                                    <span>Syarat: 10 Dina maneh (Saiki 5/15)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB PANEL 2: Rincian Profil & Akademik (Initially Hidden) -->
                <div id="panel-akademik" class="hidden flex-col gap-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Data Siswa Card -->
                        <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm flex flex-col gap-4 border border-gray-100">
                            <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                                <span class="material-symbols-outlined text-primary-600">person</span>
                                <h2 class="font-heading text-heading text-on-surface font-bold">Data Pribadi Siswa</h2>
                            </div>
                            <div class="flex flex-col gap-2 text-body font-body">
                                <div class="flex justify-between py-2 bg-surface-container-low px-4 rounded-xl">
                                    <span class="text-gray-500">Jeneng Jangkep</span>
                                    <span class="font-bold text-on-surface">{{ $siswa->nama_lengkap }}</span>
                                </div>
                                <div class="flex justify-between py-2 bg-surface-container-low px-4 rounded-xl">
                                    <span class="text-gray-500">Nomor Induk Siswa (NIS)</span>
                                    <span class="font-bold text-on-surface">{{ $siswa->nis ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between py-2 bg-surface-container-low px-4 rounded-xl">
                                    <span class="text-gray-500">Jenis Kelamin</span>
                                    <span class="font-bold text-on-surface">{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki (L)' : 'Perempuan (P)' }}</span>
                                </div>
                                <div class="flex justify-between py-2 bg-surface-container-low px-4 rounded-xl">
                                    <span class="text-gray-500">Kelas</span>
                                    <span class="font-bold text-on-surface">{{ $siswa->kelas ? 'Kelas '.$siswa->kelas : '-' }}</span>
                                </div>
                                <div class="flex justify-between py-2 bg-surface-container-low px-4 rounded-xl">
                                    <span class="text-gray-500">Email Akun Belajar</span>
                                    <span class="font-bold text-on-surface">{{ $siswa->email }}</span>
                                </div>
                                <div class="flex justify-between py-2 bg-surface-container-low px-4 rounded-xl">
                                    <span class="text-gray-500">No. Telepon</span>
                                    <span class="font-bold text-on-surface">{{ $siswa->no_telpon ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Data Guru Pangampu -->
                        <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm flex flex-col gap-4 border border-gray-100">
                            <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                                <span class="material-symbols-outlined text-primary-600">family_restroom</span>
                                <h2 class="font-heading text-heading text-on-surface font-bold">Guru Pangampu</h2>
                            </div>
                            <div class="flex flex-col gap-2 text-body font-body">
                                @forelse($guruPangampu as $g)
                                    <div class="flex justify-between py-2 bg-surface-container-low px-4 rounded-xl">
                                        <span class="text-gray-500">Guru Pangampu{{ $g->pivot->mata_pelajaran ? ' ('.$g->pivot->mata_pelajaran.')' : '' }}</span>
                                        <span class="font-bold text-on-surface">{{ $g->nama_lengkap }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 bg-surface-container-low px-4 rounded-xl">
                                        <span class="text-gray-500">NIP</span>
                                        <span class="font-bold text-on-surface">{{ $g->nip ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 bg-surface-container-low px-4 rounded-xl">
                                        <span class="text-gray-500">Kelas Diampu</span>
                                        <span class="font-bold text-on-surface">{{ $g->pivot->kelas ? 'Kelas '.$g->pivot->kelas : '-' }}</span>
                                    </div>
                                @empty
                                    <div class="py-3 text-gray-500 text-center">Durung ana guru pangampu sing kacathet kanggo siswa iki.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB PANEL 3: Setelan Pasinaon & Swara AI (Initially Hidden) -->
                <div id="panel-audio" class="hidden flex-col gap-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Voice AI Settings -->
                        <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm flex flex-col gap-4 border border-gray-100">
                            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary-600">record_voice_over</span>
                                    <h2 class="font-heading text-heading text-on-surface font-bold">Setelan Wicara & Swara (AI TTS & STT)</h2>
                                </div>
                                <span class="font-label-upper text-label-upper text-primary-600 font-bold uppercase">AZURE & GOOGLE SPEECH</span>
                            </div>
                            <!-- Voice Model Selector -->
                            <div class="flex flex-col gap-1.5">
                                <label class="font-body text-body font-bold text-on-surface">Model Swara Pangucap (Text-to-Speech)</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <button type="button" class="p-3 rounded-xl bg-primary-600 text-white font-body text-body font-bold flex items-center justify-between shadow-sm">
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-base">face_3</span>
                                            <span>Siti Neural (Wanita)</span>
                                        </div>
                                        <span class="material-symbols-outlined text-base">check</span>
                                    </button>
                                    <button type="button" class="p-3 rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface font-body text-body font-medium flex items-center justify-between transition-all border border-gray-100">
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-base">face</span>
                                            <span>Dimas Neural (Pria)</span>
                                        </div>
                                    </button>
                                </div>
                            </div>
                            <!-- Speed Selector -->
                            <div class="flex flex-col gap-1.5">
                                <label class="font-body text-body font-bold text-on-surface">Kacepetan Swara (Playback Speed)</label>
                                <div class="grid grid-cols-3 gap-3">
                                    <button type="button" class="py-2 rounded-xl bg-surface-container-low text-on-surface font-body text-body font-medium hover:bg-surface-container text-center border border-gray-100">0.8x (Alon)</button>
                                    <button type="button" class="py-2 rounded-xl bg-primary-600 text-white font-body text-body font-bold text-center shadow-sm">1.0x (Standar)</button>
                                    <button type="button" class="py-2 rounded-xl bg-surface-container-low text-on-surface font-body text-body font-medium hover:bg-surface-container text-center border border-gray-100">1.2x (Cepet)</button>
                                </div>
                            </div>
                            <!-- Mic Sensitivity Test -->
                            <div class="bg-surface-container-low p-4 rounded-xl flex flex-col gap-2 border border-primary-100/40">
                                <div class="flex items-center justify-between">
                                    <span class="font-body text-body font-bold text-on-surface">Tes Mikrofon STT Siswa</span>
                                    <span class="font-caption text-caption text-green-500 font-bold">Aktif & Siap</span>
                                </div>
                                <p class="font-caption text-caption text-gray-500">Pencet kanggo mriksa sensitivitas mikrofon sadurunge miwiti kuis wicara unggah-ungguh.</p>
                                <div class="flex items-center gap-4 mt-1">
                                    <button type="button" class="px-4 py-2 rounded-full bg-primary-600 text-white font-body text-caption font-bold flex items-center gap-1 shadow-sm">
                                        <span class="material-symbols-outlined text-sm">mic</span>
                                        <span>Coba Swara</span>
                                    </button>
                                    <div class="flex-1 h-2.5 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="w-2/3 h-full bg-green-500 rounded-full"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tracing & Learning Assistant Settings -->
                        <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm flex flex-col gap-4 border border-gray-100">
                            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary-600">tune</span>
                                    <h2 class="font-heading text-heading text-on-surface font-bold">Setelan Kanvas & Asisten AI</h2>
                                </div>
                                <span class="font-label-upper text-label-upper text-primary-600 font-bold uppercase">FR-6 & FR-13</span>
                            </div>
                            <!-- Tracing Tolerance -->
                            <div class="flex flex-col gap-1.5">
                                <label class="font-body text-body font-bold text-on-surface">Toleransi Stroke Kanvas Aksara ($1 Recognizer)</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <button type="button" class="p-3 rounded-xl bg-primary-600 text-white font-body text-body font-bold flex items-center justify-between shadow-sm">
                                        <span>Standar (Toleran SMP)</span>
                                        <span class="material-symbols-outlined text-base">check</span>
                                    </button>
                                    <button type="button" class="p-3 rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface font-body text-body font-medium flex items-center justify-between transition-all border border-gray-100">
                                        <span>Ketat (Presisi Luhur)</span>
                                    </button>
                                </div>
                            </div>
                            <!-- Toggle Reminders -->
                            <div class="flex items-center justify-between p-3 bg-surface-container-low rounded-xl border border-gray-100">
                                <div class="flex flex-col">
                                    <span class="font-body text-body font-bold text-on-surface">Pangeling Sinau Saben Dina</span>
                                    <span class="font-caption text-caption text-gray-500">Notifikasi otomatis jam 19.00 WIB</span>
                                </div>
                                <div class="w-12 h-6 bg-primary-600 rounded-full flex items-center justify-end px-1 cursor-pointer">
                                    <div class="w-4 h-4 bg-white rounded-full"></div>
                                </div>
                            </div>
                            <!-- Toggle RAG Smart Hints -->
                            <div class="flex items-center justify-between p-3 bg-surface-container-low rounded-xl border border-gray-100">
                                <div class="flex flex-col">
                                    <span class="font-body text-body font-bold text-on-surface">Saran Pitakon Asisten Tanya Basa</span>
                                    <span class="font-caption text-caption text-gray-500">Rekomendasi pitakonan otomatis adhedhasar bab</span>
                                </div>
                                <div class="w-12 h-6 bg-primary-600 rounded-full flex items-center justify-end px-1 cursor-pointer">
                                    <div class="w-4 h-4 bg-white rounded-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB PANEL 4: Keamanan Akun (Initially Hidden) -->
                <div id="panel-keamanan" class="hidden flex-col gap-6">
                    <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm flex flex-col gap-4 max-w-2xl border border-gray-100">
                        <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                            <span class="material-symbols-outlined text-primary-600">lock</span>
                            <h2 class="font-heading text-heading text-on-surface font-bold">Keamanan & Sandi Akun Siswa</h2>
                        </div>
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-col gap-1">
                                <label class="font-body text-body font-bold text-on-surface">Sandi Saiki</label>
                                <input type="password" value="••••••••••••" readonly class="bg-surface-container-low rounded-xl px-4 py-2.5 font-body text-body text-on-surface outline-none border border-gray-200/60">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="font-body text-body font-bold text-on-surface">Ganti Sandi Anyar</label>
                                <input type="password" placeholder="Lebokake sandi anyar minimal 8 karakter" class="bg-surface-container-low rounded-xl px-4 py-2.5 font-body text-body text-on-surface outline-none border border-gray-200/60 focus:border-primary-500">
                            </div>
                            <div class="flex items-center justify-between pt-2">
                                <span class="font-caption text-caption text-gray-500">Pungkasan dianyari: 12 Agustus 2024</span>
                                <button type="button" class="px-6 py-2.5 rounded-full bg-primary-600 text-white font-body text-body font-semibold shadow-md hover:bg-primary-700 transition-all">
                                    Simpen Sandi Anyar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Tab Switcher Script -->
    <script>
        function switchProfileTab(tabName) {
            const panels = ['badge', 'akademik', 'audio', 'keamanan'];
            const activeClasses = ['bg-primary-600', 'text-white', 'font-bold', 'shadow-sm'];
            const inactiveClasses = ['text-on-surface-variant', 'hover:bg-surface-container-high', 'font-medium'];

            panels.forEach(p => {
                const panelEl = document.getElementById('panel-' + p);
                const btnEl = document.getElementById('tab-btn-' + p);
                if (panelEl && btnEl) {
                    if (p === tabName) {
                        panelEl.classList.remove('hidden');
                        panelEl.classList.add('flex');
                        btnEl.classList.remove(...inactiveClasses);
                        btnEl.classList.add(...activeClasses);
                    } else {
                        panelEl.classList.add('hidden');
                        panelEl.classList.remove('flex');
                        btnEl.classList.remove(...activeClasses);
                        btnEl.classList.add(...inactiveClasses);
                    }
                }
            });
        }
    </script>
</body>
</html>