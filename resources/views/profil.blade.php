<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil, Pengaturan & Koleksi Badge Siswa - Sinau Jowo Web</title>
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

    <div class="pl-0 lg:pl-72 flex flex-col min-h-screen pb-24 lg:pb-8">
        <!-- HEADER -->
        <header class="fixed top-0 left-0 lg:left-72 right-0 h-16 lg:h-20 bg-surface-container-lowest/90 backdrop-blur-xl z-40 shadow-[0_1px_8px_rgba(0,0,0,0.04)] px-4 lg:px-space-xl flex items-center justify-between">
            <div class="flex items-center flex-1 max-w-md">
                <div class="flex items-center w-full bg-gray-50 rounded-full px-space-md py-space-xs gap-space-sm border border-gray-200/60 focus-within:border-primary-500 transition-colors">
                    <span class="material-symbols-outlined text-gray-500 text-[20px]">search</span>
                    <input type="text" placeholder="Cari materi aksara, peribahasa, tata bahasa..." class="w-full bg-transparent border-none outline-none font-body text-body text-on-surface placeholder:text-gray-500 text-xs sm:text-sm">
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
        <main class="flex-1 pt-20 lg:pt-24 w-full px-3.5 sm:px-8 py-4 sm:py-6 bg-background max-w-6xl mx-auto">
            <!-- UNIFIED SINGLE PROFILE CARD (§1 & §5.10) -->
            <div class="w-full bg-surface-container-lowest rounded-[20px] sm:rounded-[28px] shadow-sm border border-gray-100 overflow-hidden flex flex-col">

                <!-- 1. Profile Hero Banner & Identity Header (Purple Gradient with Pure White Text & Verified) -->
                <div class="w-full bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 relative p-4 sm:p-6 lg:p-8 text-white overflow-hidden">
                    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>

                    <!-- Sub-header bar -->
                    <div class="relative z-10 flex items-center justify-between mb-4 sm:mb-6">
                        <div class="flex items-center gap-1.5 sm:gap-2 text-white/90 font-label-upper text-[10px] sm:text-xs uppercase tracking-wider font-semibold truncate">
                            <span class="material-symbols-outlined text-[16px] sm:text-[18px] shrink-0">school</span>
                            <span class="truncate">Portal Belajar Siswa • Sinau Jowo</span>
                        </div>
                        <div class="hidden sm:flex items-center gap-1.5 text-white/80 text-caption font-caption text-xs">
                            <span class="material-symbols-outlined text-sm">event</span>
                            <span>Semester Ganjil 2024/2025</span>
                        </div>
                    </div>

                    <!-- Identity & Action Row -->
                    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-6">
                        <div class="flex flex-col sm:flex-row items-center sm:items-center gap-4 sm:gap-5 text-center sm:text-left w-full sm:w-auto">
                            <!-- Avatar -->
                            <div class="relative w-20 h-20 sm:w-28 sm:h-28 rounded-2xl bg-white/20 p-1 shadow-lg shrink-0 border-2 border-white/40 backdrop-blur-sm">
                                @if($siswa->foto_url)
                                    <img src="{{ $siswa->foto_url }}" alt="{{ $siswa->nama_lengkap }}" class="w-full h-full object-cover rounded-xl" />
                                @elseif($siswa->foto && file_exists(storage_path('image/siswa/'.$siswa->foto)))
                                    <img src="{{ route('siswa.image', $siswa->foto) }}" alt="{{ $siswa->nama_lengkap }}" class="w-full h-full object-cover rounded-xl" />
                                @else
                                    <div class="w-full h-full rounded-xl bg-white/25 text-white font-bold text-2xl sm:text-3xl flex items-center justify-center font-heading">
                                        {{ mb_strtoupper(mb_substr($siswa->nama_lengkap, 0, 2)) }}
                                    </div>
                                @endif
                                <span class="absolute bottom-1 right-1 w-3.5 h-3.5 sm:w-4 sm:h-4 bg-green-400 rounded-full border-2 border-white shadow-sm" title="Aktif Belajar"></span>
                            </div>

                            <!-- Text Details (Username, Verified Icon & Details) -->
                            <div class="flex flex-col min-w-0">
                                <div class="flex items-center justify-center sm:justify-start gap-2 flex-wrap">
                                    <h1 class="font-heading text-display text-white font-extrabold tracking-tight text-xl sm:text-2xl lg:text-3xl leading-snug drop-shadow-sm">
                                        {{ $siswa->nama_lengkap }}
                                    </h1>
                                    <span class="material-symbols-outlined text-white text-xl sm:text-2xl shrink-0 drop-shadow-sm" style="font-variation-settings: 'FILL' 1;" title="Siswa Terverifikasi">verified</span>
                                </div>
                                <div class="font-caption text-[11px] sm:text-xs text-white/90 mt-1 font-semibold flex flex-wrap items-center justify-center sm:justify-start gap-1.5 sm:gap-2">
                                    <span>NIS: {{ $siswa->nis ?? '-' }}</span>
                                    <span>•</span>
                                    <span>KELAS {{ $siswa->kelas ? $siswa->kelas : 'SISWA' }}</span>
                                    <span>•</span>
                                    <span>SINAU JOWO</span>
                                </div>
                                <p class="font-body text-xs sm:text-sm text-white/85 mt-1 sm:mt-1.5 max-w-xl leading-relaxed">
                                    “Siswa rajin mempelajari tata krama bahasa dan aksara Jawa.”
                                </p>
                            </div>
                        </div>

                        <!-- Tombol Lengkapi / Edit Data Diri -->
                        @php
                            $isDataLengkap = !empty($siswa->foto) && !empty($siswa->nis) && !empty($siswa->no_telpon) && !empty($siswa->jenis_kelamin);
                        @endphp
                        <div class="flex items-center justify-center sm:justify-start gap-3 shrink-0 self-stretch sm:self-center md:self-center w-full sm:w-auto mt-1 sm:mt-0">
                            <a href="{{ route('siswa.profil.data') }}"
                               class="inline-flex items-center justify-center gap-2 px-5 py-2 sm:px-6 sm:py-2.5 rounded-full font-body text-xs sm:text-sm font-bold bg-white text-primary-700 hover:bg-white/95 hover:text-primary-800 shadow-md hover:shadow-lg transition-all duration-200 w-full sm:w-auto"
                               title="{{ $isDataLengkap ? 'Ubah atau perbarui data diri Anda' : 'Lengkapi foto profil dan data diri Anda' }}">
                                <span class="material-symbols-outlined text-[18px] sm:text-[20px] text-primary-700">{{ $isDataLengkap ? 'edit_square' : 'assignment_ind' }}</span>
                                <span>{{ $isDataLengkap ? 'Edit Data Diri' : 'Lengkapi Data Diri' }}</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 3. Key 3-Column Performance Stats Metrics (§5.10) -->
                <div class="px-4 sm:px-8 py-4 sm:py-6 border-b border-gray-100">
                    <div class="bg-surface-container-low p-3.5 sm:p-5 rounded-2xl border border-primary-100/50 grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4 md:gap-0 md:divide-x md:divide-gray-200/80">
                        <!-- Stat 1: EXP -->
                        <div class="flex items-center gap-3 sm:gap-4 md:px-5">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-primary-fixed flex items-center justify-center text-primary-700 shrink-0 shadow-sm">
                                <span class="material-symbols-outlined text-xl sm:text-2xl">stars</span>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="font-label-upper text-[10px] sm:text-xs text-gray-500 uppercase tracking-wider font-bold">Total Poin Belajar</span>
                                <div class="flex items-baseline gap-1 mt-0.5">
                                    <span class="font-stat-number text-black-900 font-extrabold text-xl sm:text-2xl">{{ number_format($totalExp) }}</span>
                                    <span class="font-body text-xs sm:text-sm font-bold text-primary-600">XP</span>
                                </div>
                                <span class="font-caption text-[10px] sm:text-xs text-gray-500 truncate">Tingkat: {{ $totalExp >= 1000 ? 'Wasasis (Mahir)' : ($totalExp >= 300 ? 'Madya' : 'Pratama (Pemula)') }}</span>
                            </div>
                        </div>

                        <!-- Stat 2: Class Rank -->
                        <div class="flex items-center gap-3 sm:gap-4 md:px-5">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-yellow-300/40 flex items-center justify-center text-amber-800 shrink-0 shadow-sm">
                                <span class="material-symbols-outlined text-xl sm:text-2xl">military_tech</span>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="font-label-upper text-[10px] sm:text-xs text-gray-500 uppercase tracking-wider font-bold">Peringkat Pembelajaran</span>
                                <div class="flex items-baseline gap-1 mt-0.5">
                                    <span class="font-stat-number text-black-900 font-extrabold text-xl sm:text-2xl">#{{ $myRank }}</span>
                                </div>
                                <span class="font-caption text-[10px] sm:text-xs text-green-600 font-semibold truncate">{{ $completedLevels }} Level Selesai</span>
                            </div>
                        </div>

                        <!-- Stat 3: Daily Streak -->
                        <div class="flex items-center gap-3 sm:gap-4 md:px-5">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-orange-300/40 flex items-center justify-center text-orange-600 shrink-0 shadow-sm">
                                <span class="material-symbols-outlined text-xl sm:text-2xl">local_fire_department</span>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="font-label-upper text-[10px] sm:text-xs text-gray-500 uppercase tracking-wider font-bold">Streak Konsistensi</span>
                                <div class="flex items-baseline gap-1 mt-0.5">
                                    <span class="font-stat-number text-black-900 font-extrabold text-xl sm:text-2xl">{{ $currentStreak }} Hari</span>
                                </div>
                                <span class="font-caption text-[10px] sm:text-xs text-gray-500 truncate">Rekor tertinggi: {{ $highestStreak }} Hari</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Section Tabs Bar (§5.11 Underline Style - Centered) -->
                <div class="px-4 sm:px-8 border-b border-gray-200 flex items-center justify-center gap-4 sm:gap-8 lg:gap-12 overflow-x-auto bg-surface-container-lowest pt-2">
                    <button type="button" id="tab-btn-badge" onclick="switchProfileTab('badge')"
                            class="pb-3 sm:pb-3.5 font-heading text-xs sm:text-sm font-bold text-primary-600 border-b-2 border-primary-600 flex items-center gap-1.5 sm:gap-2 whitespace-nowrap transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-[16px] sm:text-[18px]">workspace_premium</span>
                        <span>Koleksi Badge & Piagam</span>
                    </button>
                    <button type="button" id="tab-btn-akademik" onclick="switchProfileTab('akademik')"
                            class="pb-3 sm:pb-3.5 font-heading text-xs sm:text-sm font-medium text-gray-500 hover:text-black-900 border-b-2 border-transparent flex items-center gap-1.5 sm:gap-2 whitespace-nowrap transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-[16px] sm:text-[18px]">school</span>
                        <span>Rincian Profil & Akademik</span>
                    </button>
                </div>

                <!-- 5. Tab Panels Container (Inside the Single Card) -->
                <div class="p-4 sm:p-6 lg:p-8 flex flex-col gap-5 sm:gap-6">

                <!-- TAB PANEL 1: Koleksi Badge & Piagam -->
                <div id="panel-badge" class="flex flex-col gap-5 sm:gap-6">
                    <!-- Badge Summary Header Card -->
                    <div class="bg-surface-container-low p-4 sm:p-6 rounded-2xl border border-primary-100/50 flex flex-col md:flex-row md:items-center justify-between gap-3 sm:gap-4">
                        <div class="flex flex-col min-w-0">
                            <h2 class="font-heading text-sm sm:text-base lg:text-heading text-black-900 font-bold">Koleksi Piagam & Lencana Belajar</h2>
                            <p class="font-body text-xs sm:text-sm text-gray-500 mt-0.5">Lencana otomatis diraih ketika menyelesaikan penelusuran aksara, percakapan krama, dan kuis kebudayaan.</p>
                        </div>
                        <!-- Badge Completion Counter Bar -->
                        @php $persenLevel = $totalLevels > 0 ? (int) round(($completedLevels / $totalLevels) * 100) : 0; @endphp
                        <div class="flex flex-col w-full md:w-80 bg-white p-3 sm:p-3.5 rounded-xl border border-gray-100 shadow-sm shrink-0">
                            <div class="flex justify-between items-center mb-1.5 text-[11px] sm:text-xs">
                                <span class="text-gray-500 font-semibold">Progres Pembelajaran</span>
                                <span class="font-bold text-primary-600">{{ $completedLevels }} dari {{ $totalLevels }} Level ({{ $persenLevel }}%)</span>
                            </div>
                            <div class="w-full h-2 bg-gray-200/80 rounded-full overflow-hidden">
                                <div class="h-full bg-primary-600 rounded-full transition-all duration-500" style="width: {{ $persenLevel }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Active / Earned Badges Section -->
                    <div class="flex flex-col gap-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 sm:gap-2">
                            <div class="flex items-center gap-1.5 sm:gap-2">
                                <span class="material-symbols-outlined text-green-500 text-base sm:text-lg shrink-0">verified</span>
                                <h2 class="font-heading text-xs sm:text-sm md:text-base lg:text-heading text-on-surface font-bold">Lencana yang Telah Diraih ({{ $completedLevels }})</h2>
                            </div>
                            <span class="font-label-upper text-[9px] sm:text-xs text-gray-500 uppercase font-semibold">BERHASIL DIRAIH • STATUS AKTIF</span>
                        </div>

                        <!-- 3-Column Grid with Distinct Geometric Clip-Path Badge Containers (§5.12 Compliant) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-4">

                            <!-- Earned Badge 1: Jawara Hanacaraka (HEXAGON) -->
                            <div class="bg-surface-container-lowest p-4 sm:p-6 rounded-2xl shadow-sm flex flex-col justify-between items-center hover:shadow-md transition-all border border-gray-100 group">
                                <div class="flex flex-col items-center text-center gap-3 sm:gap-3.5 w-full">
                                    <!-- Geometric Container: HEXAGON clip-path with Gold Solid Fill -->
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-[#F6D98B] flex items-center justify-center shrink-0 text-amber-900 shadow-sm clip-hexagon transition-transform duration-200 group-hover:scale-105">
                                        <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5m14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex flex-col items-center text-center min-w-0 w-full">
                                        <span class="font-label-upper text-[10px] sm:text-[11px] text-gray-500 uppercase font-semibold">LEVEL 1 <span class="mx-0.5">•</span> <span class="text-green-500 font-bold">SELESAI</span></span>
                                        <h3 class="font-heading text-sm sm:text-base text-on-surface font-bold mt-1">Jawara Hanacaraka</h3>
                                        <p class="font-caption text-xs text-on-surface-variant mt-1 leading-relaxed">Sukses menulis dan menghafal 14 aksara nglegena dasar dengan presisi tinggi.</p>
                                    </div>
                                </div>
                                <div class="w-full mt-3 sm:mt-4 pt-2 bg-surface-container-low p-2 sm:p-2.5 rounded-xl flex items-center justify-between text-[11px] sm:text-xs text-gray-500 border border-primary-100/30">
                                    <span>Akurasi Tracing: 96%</span>
                                    <span class="text-primary-700 font-bold">+150 XP</span>
                                </div>
                            </div>

                            <!-- Earned Badge 2: Tatas Unggah-Ungguh (PENTAGON) -->
                            <div class="bg-surface-container-lowest p-4 sm:p-6 rounded-2xl shadow-sm flex flex-col justify-between items-center hover:shadow-md transition-all border border-gray-100 group">
                                <div class="flex flex-col items-center text-center gap-3 sm:gap-3.5 w-full">
                                    <!-- Geometric Container: PENTAGON clip-path with Green Solid Fill -->
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-[#4CAF6D] flex items-center justify-center shrink-0 text-white shadow-sm clip-pentagon transition-transform duration-200 group-hover:scale-105">
                                        <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex flex-col items-center text-center min-w-0 w-full">
                                        <span class="font-label-upper text-[10px] sm:text-[11px] text-gray-500 uppercase font-semibold">WICARA KRAMA <span class="mx-0.5">•</span> <span class="text-green-500 font-bold">SELESAI</span></span>
                                        <h3 class="font-heading text-sm sm:text-base text-on-surface font-bold mt-1">Tatas Unggah-Ungguh</h3>
                                        <p class="font-caption text-xs text-on-surface-variant mt-1 leading-relaxed">Menuntaskan percakapan Krama Inggil kepada guru dan orang tua dengan skor 92%.</p>
                                    </div>
                                </div>
                                <div class="w-full mt-3 sm:mt-4 pt-2 bg-surface-container-low p-2 sm:p-2.5 rounded-xl flex items-center justify-between text-[11px] sm:text-xs text-gray-500 border border-primary-100/30">
                                    <span>Pelafalan STT: 92%</span>
                                    <span class="text-primary-700 font-bold">+200 XP</span>
                                </div>
                            </div>

                            <!-- Earned Badge 3: Prajurit Sandhangan (SHIELD) -->
                            <div class="bg-surface-container-lowest p-4 sm:p-6 rounded-2xl shadow-sm flex flex-col justify-between items-center hover:shadow-md transition-all border border-gray-100 group">
                                <div class="flex flex-col items-center text-center gap-3 sm:gap-3.5 w-full">
                                    <!-- Geometric Container: SHIELD clip-path with Primary Purple Solid Fill -->
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-[#6C5CE8] flex items-center justify-center shrink-0 text-white shadow-sm clip-shield transition-transform duration-200 group-hover:scale-105">
                                        <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex flex-col items-center text-center min-w-0 w-full">
                                        <span class="font-label-upper text-[10px] sm:text-[11px] text-gray-500 uppercase font-semibold">AKSARA JAWA <span class="mx-0.5">•</span> <span class="text-green-500 font-bold">SELESAI</span></span>
                                        <h3 class="font-heading text-sm sm:text-base text-on-surface font-bold mt-1">Prajurit Sandhangan</h3>
                                        <p class="font-caption text-xs text-on-surface-variant mt-1 leading-relaxed">Paham penggunaan Wulu, Suku, Taling, dan Tarung dalam 20 kalimat latihan.</p>
                                    </div>
                                </div>
                                <div class="w-full mt-3 sm:mt-4 pt-2 bg-surface-container-low p-2 sm:p-2.5 rounded-xl flex items-center justify-between text-[11px] sm:text-xs text-gray-500 border border-primary-100/30">
                                    <span>Kuis Pasangan: 100/100</span>
                                    <span class="text-primary-700 font-bold">+180 XP</span>
                                </div>
                            </div>

                            <!-- Earned Badge 4: Busana Gagrag Anyar (RHOMBUS / DIAMOND) -->
                            <div class="bg-surface-container-lowest p-4 sm:p-6 rounded-2xl shadow-sm flex flex-col justify-between items-center hover:shadow-md transition-all border border-gray-100 group">
                                <div class="flex flex-col items-center text-center gap-3 sm:gap-3.5 w-full">
                                    <!-- Geometric Container: RHOMBUS/DIAMOND clip-path with Orange Solid Fill -->
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-[#F0955A] flex items-center justify-center shrink-0 text-white shadow-sm clip-rhombus transition-transform duration-200 group-hover:scale-105">
                                        <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex flex-col items-center text-center min-w-0 w-full">
                                        <span class="font-label-upper text-[10px] sm:text-[11px] text-gray-500 uppercase font-semibold">BUDAYA JAWA <span class="mx-0.5">•</span> <span class="text-green-500 font-bold">SELESAI</span></span>
                                        <h3 class="font-heading text-sm sm:text-base text-on-surface font-bold mt-1">Busana Gagrag Anyar</h3>
                                        <p class="font-caption text-xs text-on-surface-variant mt-1 leading-relaxed">Menyelesaikan tebak busana adat: Jarik, Beskap, dan Blangkon Jawa Timur.</p>
                                    </div>
                                </div>
                                <div class="w-full mt-3 sm:mt-4 pt-2 bg-surface-container-low p-2 sm:p-2.5 rounded-xl flex items-center justify-between text-[11px] sm:text-xs text-gray-500 border border-primary-100/30">
                                    <span>TTS Budaya: Selesai</span>
                                    <span class="text-primary-700 font-bold">+120 XP</span>
                                </div>
                            </div>

                            <!-- Earned Badge 5: Wicara Prigel (OCTAGON) -->
                            <div class="bg-surface-container-lowest p-4 sm:p-6 rounded-2xl shadow-sm flex flex-col justify-between items-center hover:shadow-md transition-all border border-gray-100 group">
                                <div class="flex flex-col items-center text-center gap-3 sm:gap-3.5 w-full">
                                    <!-- Geometric Container: OCTAGON clip-path with Indigo Solid Fill -->
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-[#7B6CF0] flex items-center justify-center shrink-0 text-white shadow-sm clip-octagon transition-transform duration-200 group-hover:scale-105">
                                        <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm5.3-3c0 3-2.54 5.1-5.3 5.1S6.7 14 6.7 11H5c0 3.41 2.72 6.23 6 6.72V21h2v-3.28c3.28-.48 6-3.3 6-6.72h-1.7z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex flex-col items-center text-center min-w-0 w-full">
                                        <span class="font-label-upper text-[10px] sm:text-[11px] text-gray-500 uppercase font-semibold">AI STT RECOGNITION <span class="mx-0.5">•</span> <span class="text-green-500 font-bold">SELESAI</span></span>
                                        <h3 class="font-heading text-sm sm:text-base text-on-surface font-bold mt-1">Wicara Prigel</h3>
                                        <p class="font-caption text-xs text-on-surface-variant mt-1 leading-relaxed">Latihan pelafalan suara Jawa dengan kecerdasan buatan sebanyak 10 kali berturut-turut.</p>
                                    </div>
                                </div>
                                <div class="w-full mt-3 sm:mt-4 pt-2 bg-surface-container-low p-2 sm:p-2.5 rounded-xl flex items-center justify-between text-[11px] sm:text-xs text-gray-500 border border-primary-100/30">
                                    <span>Evaluasi Suara AI</span>
                                    <span class="text-primary-700 font-bold">+220 XP</span>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Locked Badges Section -->
                    <div class="flex flex-col gap-3 pt-2">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 sm:gap-2">
                            <div class="flex items-center gap-1.5 sm:gap-2">
                                <span class="material-symbols-outlined text-gray-500 text-base sm:text-lg shrink-0">lock</span>
                                <h2 class="font-heading text-xs sm:text-sm md:text-base lg:text-heading text-on-surface font-bold">Lencana yang Masih Terkunci (7)</h2>
                            </div>
                            <span class="font-label-upper text-[9px] sm:text-xs text-gray-500 uppercase font-semibold">SYARAT PEROLEHAN • TINGKAT LANJUT</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-4">
                            <!-- Locked Badge 1: Empu Aksara Murda & Swara (HEXAGON) -->
                            <div class="bg-surface-container-lowest/70 p-4 sm:p-6 rounded-2xl shadow-sm flex flex-col justify-between items-center opacity-80 border border-gray-200 group">
                                <div class="flex flex-col items-center text-center gap-3 sm:gap-3.5 w-full">
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-[#E4E4EC] flex items-center justify-center shrink-0 text-gray-500 clip-hexagon shadow-inner">
                                        <span class="material-symbols-outlined text-2xl sm:text-3xl">lock</span>
                                    </div>
                                    <div class="flex flex-col items-center text-center min-w-0 w-full">
                                        <span class="font-label-upper text-[10px] sm:text-[11px] text-gray-500 uppercase font-semibold">LEVEL 3 <span class="mx-0.5">•</span> TERKUNCI</span>
                                        <h3 class="font-heading text-sm sm:text-base text-on-surface-variant font-bold mt-1">Empu Aksara Murda & Swara</h3>
                                        <p class="font-caption text-xs text-gray-500 mt-1 leading-relaxed">Selesaikan pembelajaran aksara murda, swara, dan rekan dengan nilai minimal 85.</p>
                                    </div>
                                </div>
                                <div class="w-full mt-3 sm:mt-4 pt-2 bg-surface-container-low p-2 sm:p-2.5 rounded-xl text-[11px] sm:text-xs text-gray-500 flex items-center justify-center gap-1.5 border border-gray-100">
                                    <span class="material-symbols-outlined text-xs">info</span>
                                    <span>Syarat: Tuntaskan Bab 3 Terlebih Dahulu</span>
                                </div>
                            </div>

                            <!-- Locked Badge 2: Pujangga Paribasan (PENTAGON) -->
                            <div class="bg-surface-container-lowest/70 p-4 sm:p-6 rounded-2xl shadow-sm flex flex-col justify-between items-center opacity-80 border border-gray-200 group">
                                <div class="flex flex-col items-center text-center gap-3 sm:gap-3.5 w-full">
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-[#E4E4EC] flex items-center justify-center shrink-0 text-gray-500 clip-pentagon shadow-inner">
                                        <span class="material-symbols-outlined text-2xl sm:text-3xl">lock</span>
                                    </div>
                                    <div class="flex flex-col items-center text-center min-w-0 w-full">
                                        <span class="font-label-upper text-[10px] sm:text-[11px] text-gray-500 uppercase font-semibold">PARIBASAN <span class="mx-0.5">•</span> TERKUNCI</span>
                                        <h3 class="font-heading text-sm sm:text-base text-on-surface-variant font-bold mt-1">Pujangga Paribasan</h3>
                                        <p class="font-caption text-xs text-gray-500 mt-1 leading-relaxed">Dapat menyelesaikan kuis peribahasa, bebasan, dan saloka dengan skor sempurna 100.</p>
                                    </div>
                                </div>
                                <div class="w-full mt-3 sm:mt-4 pt-2 bg-surface-container-low p-2 sm:p-2.5 rounded-xl text-[11px] sm:text-xs text-gray-500 flex items-center justify-center gap-1.5 border border-gray-100">
                                    <span class="material-symbols-outlined text-xs">info</span>
                                    <span>Syarat: Skor Kuis Peribahasa &gt; 95</span>
                                </div>
                            </div>

                            <!-- Locked Badge 3: Gathutkaca Streak Master (SHIELD) -->
                            <div class="bg-surface-container-lowest/70 p-4 sm:p-6 rounded-2xl shadow-sm flex flex-col justify-between items-center opacity-80 border border-gray-200 group">
                                <div class="flex flex-col items-center text-center gap-3 sm:gap-3.5 w-full">
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-[#E4E4EC] flex items-center justify-center shrink-0 text-gray-500 clip-shield shadow-inner">
                                        <span class="material-symbols-outlined text-2xl sm:text-3xl">lock</span>
                                    </div>
                                    <div class="flex flex-col items-center text-center min-w-0 w-full">
                                        <span class="font-label-upper text-[10px] sm:text-[11px] text-gray-500 uppercase font-semibold">KONSISTENSI <span class="mx-0.5">•</span> TERKUNCI</span>
                                        <h3 class="font-heading text-sm sm:text-base text-on-surface-variant font-bold mt-1">Gathutkaca Streak Master</h3>
                                        <p class="font-caption text-xs text-gray-500 mt-1 leading-relaxed">Raih streak belajar aktif tanpa henti selama 15 hari berturut-turut.</p>
                                    </div>
                                </div>
                                <div class="w-full mt-3 sm:mt-4 pt-2 bg-surface-container-low p-2 sm:p-2.5 rounded-xl text-[11px] sm:text-xs text-gray-500 flex items-center justify-center gap-1.5 border border-gray-100">
                                    <span class="material-symbols-outlined text-xs">info</span>
                                    <span>Syarat: 10 Hari lagi (Saat ini {{ $currentStreak }}/15)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB PANEL 2: Rincian Profil & Akademik (Initially Hidden) -->
                <div id="panel-akademik" class="hidden flex-col gap-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Data Siswa Card -->
                        <div class="bg-surface-container-low/50 p-4 sm:p-6 rounded-2xl flex flex-col gap-3.5 sm:gap-4 border border-gray-100">
                            <div class="flex items-center justify-between border-b border-gray-200/80 pb-3 gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary-600 text-[20px] sm:text-[24px]">person</span>
                                    <h2 class="font-heading text-xs sm:text-sm md:text-base text-black-900 font-bold">Data Pribadi Siswa</h2>
                                </div>
                                <a href="{{ route('siswa.profil.data') }}" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 sm:px-3 sm:py-1.5 rounded-lg bg-primary-50 text-primary-700 hover:bg-primary-100 font-semibold text-[11px] sm:text-xs transition-colors shrink-0">
                                    <span class="material-symbols-outlined text-[15px] sm:text-[16px]">edit_square</span>
                                    <span>Edit</span>
                                </a>
                            </div>
                            <div class="flex flex-col gap-2 sm:gap-2.5 font-body">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-2 sm:py-2.5 bg-white px-3 sm:px-4 rounded-xl border border-gray-100 shadow-sm gap-0.5 sm:gap-2">
                                    <span class="text-gray-500 font-medium text-xs sm:text-sm shrink-0">Nama Lengkap</span>
                                    <span class="font-bold text-black-900 text-xs sm:text-sm text-left sm:text-right break-words">{{ $siswa->nama_lengkap }}</span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-2 sm:py-2.5 bg-white px-3 sm:px-4 rounded-xl border border-gray-100 shadow-sm gap-0.5 sm:gap-2">
                                    <span class="text-gray-500 font-medium text-xs sm:text-sm shrink-0">Nomor Induk Siswa (NIS)</span>
                                    <span class="font-bold text-black-900 text-xs sm:text-sm text-left sm:text-right">{{ $siswa->nis ?? '-' }}</span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-2 sm:py-2.5 bg-white px-3 sm:px-4 rounded-xl border border-gray-100 shadow-sm gap-0.5 sm:gap-2">
                                    <span class="text-gray-500 font-medium text-xs sm:text-sm shrink-0">Jenis Kelamin</span>
                                    <span class="font-bold text-black-900 text-xs sm:text-sm text-left sm:text-right">{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki (L)' : ($siswa->jenis_kelamin === 'P' ? 'Perempuan (P)' : '-') }}</span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-2 sm:py-2.5 bg-white px-3 sm:px-4 rounded-xl border border-gray-100 shadow-sm gap-0.5 sm:gap-2">
                                    <span class="text-gray-500 font-medium text-xs sm:text-sm shrink-0">Kelas</span>
                                    <span class="font-bold text-black-900 text-xs sm:text-sm text-left sm:text-right">{{ $siswa->kelas ? 'Kelas '.$siswa->kelas : '-' }}</span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-2 sm:py-2.5 bg-white px-3 sm:px-4 rounded-xl border border-gray-100 shadow-sm gap-0.5 sm:gap-2">
                                    <span class="text-gray-500 font-medium text-xs sm:text-sm shrink-0">Email Akun Belajar</span>
                                    <span class="font-bold text-black-900 text-xs sm:text-sm text-left sm:text-right break-all sm:break-normal">{{ $siswa->email }}</span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-2 sm:py-2.5 bg-white px-3 sm:px-4 rounded-xl border border-gray-100 shadow-sm gap-0.5 sm:gap-2">
                                    <span class="text-gray-500 font-medium text-xs sm:text-sm shrink-0">No. Telepon</span>
                                    <span class="font-bold text-black-900 text-xs sm:text-sm text-left sm:text-right">{{ $siswa->no_telpon ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Data Informasi Akademik & Sekolah -->
                        <div class="bg-surface-container-low/50 p-4 sm:p-6 rounded-2xl flex flex-col gap-3.5 sm:gap-4 border border-gray-100">
                            <div class="flex items-center justify-between border-b border-gray-200/80 pb-3 gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary-600 text-[20px] sm:text-[24px]">school</span>
                                    <h2 class="font-heading text-xs sm:text-sm md:text-base text-black-900 font-bold">Informasi Akademik & Sekolah</h2>
                                </div>
                                <span class="font-label-upper text-primary-600 font-bold uppercase text-[10px] sm:text-xs shrink-0">Aktif</span>
                            </div>
                            <div class="flex flex-col gap-2 sm:gap-2.5 font-body">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-2 sm:py-2.5 bg-white px-3 sm:px-4 rounded-xl border border-gray-100 shadow-sm gap-0.5 sm:gap-2">
                                    <span class="text-gray-500 font-medium text-xs sm:text-sm shrink-0">Asal Sekolah</span>
                                    <span class="font-bold text-black-900 text-xs sm:text-sm text-left sm:text-right break-words">{{ $siswa->sekolah ?? 'Sinau Jowo Academy' }}</span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-2 sm:py-2.5 bg-white px-3 sm:px-4 rounded-xl border border-gray-100 shadow-sm gap-0.5 sm:gap-2">
                                    <span class="text-gray-500 font-medium text-xs sm:text-sm shrink-0">Kurikulum & Muatan</span>
                                    <span class="font-bold text-black-900 text-xs sm:text-sm text-left sm:text-right break-words">Bahasa, Sastra & Aksara Jawa</span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-2 sm:py-2.5 bg-white px-3 sm:px-4 rounded-xl border border-gray-100 shadow-sm gap-0.5 sm:gap-2">
                                    <span class="text-gray-500 font-medium text-xs sm:text-sm shrink-0">Tahun Ajaran</span>
                                    <span class="font-bold text-black-900 text-xs sm:text-sm text-left sm:text-right">2024 / 2025</span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-2 sm:py-2.5 bg-white px-3 sm:px-4 rounded-xl border border-gray-100 shadow-sm gap-0.5 sm:gap-2">
                                    <span class="text-gray-500 font-medium text-xs sm:text-sm shrink-0">Semester Aktif</span>
                                    <span class="font-bold text-black-900 text-xs sm:text-sm text-left sm:text-right">Semester Ganjil</span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-2 sm:py-2.5 bg-white px-3 sm:px-4 rounded-xl border border-gray-100 shadow-sm gap-0.5 sm:gap-2">
                                    <span class="text-gray-500 font-medium text-xs sm:text-sm shrink-0">Status Pembelajaran</span>
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] sm:text-xs font-bold bg-green-50 text-green-700 border border-green-200/60 self-start sm:self-auto mt-0.5 sm:mt-0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                        <span>Siswa Aktif Belajar</span>
                                    </span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-2 sm:py-2.5 bg-white px-3 sm:px-4 rounded-xl border border-gray-100 shadow-sm gap-0.5 sm:gap-2">
                                    <span class="text-gray-500 font-medium text-xs sm:text-sm shrink-0">Terdaftar Sejak</span>
                                    <span class="font-bold text-black-900 text-xs sm:text-sm text-left sm:text-right">{{ $siswa->created_at ? $siswa->created_at->translatedFormat('d F Y') : '12 Agustus 2024' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                </div> <!-- End of Tab Panels Container -->
            </div> <!-- End of Single Unified Profile Card -->
        </main>
    </div>

    <!-- Floating Toast Notifications (Pojok Kanan Bawah) -->
    <div id="toastContainer" class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none max-w-sm w-full">
        @if (session('sukses'))
            <div id="toastNotification"
                 class="pointer-events-auto bg-surface-container-lowest border border-green-500/40 rounded-2xl p-4 shadow-[0_10px_35px_rgba(0,0,0,0.15)] flex items-start gap-3.5 overflow-hidden relative transition-all duration-300">
                <div class="w-10 h-10 rounded-xl bg-green-500/15 text-green-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">check_circle</span>
                </div>
                <div class="flex-1 min-w-0 pt-0.5">
                    <h5 class="font-heading text-sm font-bold text-green-700 leading-tight">Berhasil</h5>
                    <p class="font-body text-xs text-on-surface-variant mt-0.5 leading-relaxed">{{ session('sukses') }}</p>
                </div>
                <button type="button" onclick="dismissToast('toastNotification')" title="Tutup Notifikasi" class="w-7 h-7 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-700 flex items-center justify-center transition-colors shrink-0">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
        @endif
        @if (session('error') || $errors->any())
            <div id="toastErrorNotification"
                 class="pointer-events-auto bg-surface-container-lowest border border-red-500/40 rounded-2xl p-4 shadow-[0_10px_35px_rgba(0,0,0,0.15)] flex items-start gap-3.5 overflow-hidden relative transition-all duration-300">
                <div class="w-10 h-10 rounded-xl bg-red-500/15 text-red-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">error</span>
                </div>
                <div class="flex-1 min-w-0 pt-0.5">
                    <h5 class="font-heading text-sm font-bold text-red-700 leading-tight">Terjadi Kesalahan</h5>
                    <p class="font-body text-xs text-on-surface-variant mt-0.5 leading-relaxed">{{ session('error') ?? $errors->first() }}</p>
                </div>
                <button type="button" onclick="dismissToast('toastErrorNotification')" title="Tutup Notifikasi" class="w-7 h-7 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-700 flex items-center justify-center transition-colors shrink-0">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Tab Switcher & Toast Script -->
    <script>
        function switchProfileTab(tabName) {
            const panels = ['badge', 'akademik'];
            const activeClasses = ['text-primary-600', 'border-primary-600', 'font-bold'];
            const inactiveClasses = ['text-gray-500', 'border-transparent', 'font-medium'];

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

        function dismissToast(id) {
            const el = document.getElementById(id);
            if (el) {
                el.style.opacity = '0';
                el.style.transform = 'translateX(100%)';
                setTimeout(() => el.remove(), 300);
            }
        }

        @if(session('sukses') || session('error') || $errors->any())
        setTimeout(() => {
            dismissToast('toastNotification');
            dismissToast('toastErrorNotification');
        }, 5000);
        @endif
    </script>
</body>
</html>