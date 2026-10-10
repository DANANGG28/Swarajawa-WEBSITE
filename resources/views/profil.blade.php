<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil, Pengaturan & Koleksi Badge Siswa - SINAU APP Web</title>
    @include('partials.favicon')
    @include('partials.meta-og')
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
        <!-- MAIN CONTENT -->
        <main class="flex-1 w-full px-3.5 sm:px-8 py-6 sm:py-8 bg-background max-w-6xl mx-auto">
            <!-- UNIFIED SINGLE PROFILE CARD (§1 & §5.10) -->
            <div class="w-full bg-surface-container-lowest rounded-[20px] sm:rounded-[28px] shadow-sm border border-gray-100 overflow-hidden flex flex-col">

                <!-- 1. Profile Hero Banner & Identity Header (Purple Gradient with Pure White Text & Verified) -->
                <div class="w-full bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 relative p-4 sm:p-6 lg:p-8 text-white overflow-hidden">
                    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>

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
                                    <span>SINAU APP</span>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Lengkapi / Edit Data Diri & Logout Mobile -->
                        @php
                            $isDataLengkap = !empty($siswa->foto) && !empty($siswa->nis) && !empty($siswa->no_telpon) && !empty($siswa->jenis_kelamin);
                        @endphp
                        <div class="flex flex-col sm:flex-row items-center justify-center sm:justify-start gap-2.5 shrink-0 self-stretch sm:self-center md:self-center w-full sm:w-auto mt-2 sm:mt-0">
                            <a href="{{ route('siswa.profil.data') }}"
                               class="inline-flex items-center justify-center gap-2 px-5 py-2 sm:px-6 sm:py-2.5 rounded-full font-body text-xs sm:text-sm font-bold bg-white text-primary-700 hover:bg-white/95 hover:text-primary-800 shadow-md hover:shadow-lg transition-all duration-200 w-full sm:w-auto"
                               title="{{ $isDataLengkap ? 'Ubah atau perbarui data diri Anda' : 'Lengkapi foto profil dan data diri Anda' }}">
                                <span class="material-symbols-outlined text-[18px] sm:text-[20px] text-primary-700">{{ $isDataLengkap ? 'edit_square' : 'assignment_ind' }}</span>
                                <span>{{ $isDataLengkap ? 'Edit Data Diri' : 'Lengkapi Data Diri' }}</span>
                            </a>

                            {{-- Tombol Logout Khusus Tampilan Mobile di Bawah Lengkapi Data Diri --}}
                            <form method="POST" action="{{ route('keluar') }}" class="lg:hidden w-full sm:w-auto inline-flex">
                                @csrf
                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin keluar dari akun?')"
                                        class="inline-flex items-center justify-center gap-2 px-5 py-2 sm:py-2.5 rounded-full font-body text-xs sm:text-sm font-bold bg-red-600 hover:bg-red-700 text-white shadow-md hover:shadow-lg transition-all duration-200 w-full active:scale-95 cursor-pointer"
                                        title="Keluar dari akun">
                                    <span class="material-symbols-outlined text-[18px]">logout</span>
                                    <span>Keluar Akun</span>
                                </button>
                            </form>
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
                        </div>
                        <!-- Badge Completion Counter Bar -->
                        <div class="flex flex-col w-full md:w-80 bg-white p-3 sm:p-3.5 rounded-xl border border-gray-100 shadow-sm shrink-0">
                            <div class="flex justify-between items-center mb-1.5 text-[11px] sm:text-xs">
                                <span class="text-gray-500 font-semibold">Progres Koleksi Lencana</span>
                                <span class="font-bold text-primary-600">{{ $earnedBadgesCount ?? 0 }} dari {{ $totalBadgesCount ?? 0 }} Lencana ({{ $persenLencana ?? 0 }}%)</span>
                            </div>
                            <div class="w-full h-2 bg-gray-200/80 rounded-full overflow-hidden">
                                <div class="h-full bg-primary-600 rounded-full transition-all duration-500" style="width: {{ $persenLencana ?? 0 }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Active / Earned Badges Section -->
                    <div class="flex flex-col gap-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 sm:gap-2">
                            <div class="flex items-center gap-1.5 sm:gap-2">
                                <span class="material-symbols-outlined text-green-500 text-base sm:text-lg shrink-0">verified</span>
                                <h2 class="font-heading text-xs sm:text-sm md:text-base lg:text-heading text-on-surface font-bold">Lencana yang Telah Diraih ({{ $earnedBadgesCount ?? 0 }})</h2>
                            </div>
                            <span class="font-label-upper text-[9px] sm:text-xs text-gray-500 uppercase font-semibold">BERHASIL DIRAIH • STATUS AKTIF</span>
                        </div>

                        @if(!empty($earnedBadges) && count($earnedBadges) > 0)
                            <!-- 3-Column Grid with Distinct Geometric Clip-Path Badge Containers -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-4">
                                @foreach($earnedBadges as $badge)
                                    <div class="bg-surface-container-lowest p-4 sm:p-6 rounded-2xl shadow-sm flex flex-col justify-between items-center hover:shadow-md transition-all border border-gray-100 group">
                                        <div class="flex flex-col items-center text-center gap-3 sm:gap-3.5 w-full">
                                            <!-- Geometric Container with Dynamic Clip-Path and Color -->
                                            <div class="w-16 h-16 sm:w-20 sm:h-20 flex items-center justify-center shrink-0 {{ $badge['text_color'] ?? 'text-white' }} shadow-sm {{ $badge['shape'] ?? 'clip-hexagon' }} transition-transform duration-200 group-hover:scale-105"
                                                 style="background-color: {{ $badge['bg_color'] ?? '#6C5CE8' }};">
                                                <span class="material-symbols-outlined text-2xl sm:text-3xl">{{ $badge['icon'] ?? 'military_tech' }}</span>
                                            </div>
                                            <div class="flex flex-col items-center text-center min-w-0 w-full">
                                                <span class="font-label-upper text-[10px] sm:text-[11px] text-gray-500 uppercase font-semibold">{{ $badge['kategori'] }} <span class="mx-0.5">•</span> <span class="text-green-500 font-bold">SELESAI</span></span>
                                                <h3 class="font-heading text-sm sm:text-base text-on-surface font-bold mt-1">{{ $badge['nama'] }}</h3>
                                                <p class="font-caption text-xs text-on-surface-variant mt-1 leading-relaxed">{{ $badge['deskripsi'] }}</p>
                                            </div>
                                        </div>
                                        <div class="w-full mt-3 sm:mt-4 pt-2 bg-surface-container-low p-2 sm:p-2.5 rounded-xl flex items-center justify-between text-[11px] sm:text-xs text-gray-600 border border-primary-100/30">
                                            <span class="truncate">{{ $badge['earned_stat_left'] }}</span>
                                            <span class="text-primary-700 font-bold shrink-0">{{ $badge['earned_stat_right'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-surface-container-lowest p-6 sm:p-8 rounded-2xl border border-dashed border-gray-200 text-center flex flex-col items-center justify-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-2xl">workspace_premium</span>
                                </div>
                                <div>
                                    <h4 class="font-heading text-sm sm:text-base font-bold text-on-surface">Belum Ada Lencana yang Terbuka</h4>
                                    <p class="font-body text-xs sm:text-sm text-gray-500 max-w-md mt-1">Selesaikan kuis wicara suara, latihan aksara Jawa, dan tuntaskan unit materi untuk membuka lencana pertamamu!</p>
                                </div>
                                <a href="{{ route('siswa.topik') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary-600 text-white font-semibold text-xs hover:bg-primary-700 transition-colors shadow-sm mt-1">
                                    <span class="material-symbols-outlined text-base">play_circle</span>
                                    <span>Mulai Kerjakan Kuis</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Locked Badges Section -->
                    <div class="flex flex-col gap-3 pt-2">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 sm:gap-2">
                            <div class="flex items-center gap-1.5 sm:gap-2">
                                <span class="material-symbols-outlined text-gray-500 text-base sm:text-lg shrink-0">lock</span>
                                <h2 class="font-heading text-xs sm:text-sm md:text-base lg:text-heading text-on-surface font-bold">Lencana yang Masih Terkunci ({{ count($lockedBadges ?? []) }})</h2>
                            </div>
                            <span class="font-label-upper text-[9px] sm:text-xs text-gray-500 uppercase font-semibold">SYARAT PEROLEHAN • MISI TERSEDIA</span>
                        </div>

                        @if(!empty($lockedBadges) && count($lockedBadges) > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-4">
                                @foreach($lockedBadges as $badge)
                                    <div class="bg-surface-container-lowest/80 p-4 sm:p-6 rounded-2xl shadow-sm flex flex-col justify-between items-center opacity-90 border border-gray-200 group hover:opacity-100 transition-all">
                                        <div class="flex flex-col items-center text-center gap-3 sm:gap-3.5 w-full">
                                            <!-- Geometric Container: Grayscale with Lock and Inner Icon -->
                                            <div class="relative w-16 h-16 sm:w-20 sm:h-20 bg-gray-200 flex items-center justify-center shrink-0 text-gray-400 {{ $badge['shape'] ?? 'clip-hexagon' }} shadow-inner">
                                                <span class="material-symbols-outlined text-2xl sm:text-3xl text-gray-500">{{ $badge['icon'] ?? 'lock' }}</span>
                                                <div class="absolute bottom-1 right-1 w-5 h-5 rounded-full bg-gray-600 text-white flex items-center justify-center text-[10px] shadow">
                                                    <span class="material-symbols-outlined text-[12px]">lock</span>
                                                </div>
                                            </div>
                                            <div class="flex flex-col items-center text-center min-w-0 w-full">
                                                <span class="font-label-upper text-[10px] sm:text-[11px] text-gray-500 uppercase font-semibold">{{ $badge['kategori'] }} <span class="mx-0.5">•</span> TERKUNCI</span>
                                                <h3 class="font-heading text-sm sm:text-base text-on-surface-variant font-bold mt-1">{{ $badge['nama'] }}</h3>
                                                <p class="font-caption text-xs text-gray-500 mt-1 leading-relaxed">{{ $badge['deskripsi'] }}</p>

                                                <!-- Mini Mission Progress Bar -->
                                                <div class="w-full mt-2.5 pt-1">
                                                    <div class="flex justify-between items-center text-[10px] text-gray-500 font-semibold mb-1">
                                                        <span>Progres Misi</span>
                                                        <span class="text-primary-600 font-bold">{{ $badge['progress_percent'] }}%</span>
                                                    </div>
                                                    <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                                        <div class="h-full bg-primary-500/70 rounded-full transition-all duration-300" style="width: {{ $badge['progress_percent'] }}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w-full mt-3 sm:mt-4 pt-2 bg-surface-container-low p-2 sm:p-2.5 rounded-xl text-[11px] sm:text-xs text-gray-600 flex items-center justify-center gap-1.5 border border-gray-100 text-center">
                                            <span class="material-symbols-outlined text-xs text-primary-600 shrink-0">flag</span>
                                            <span class="leading-tight">{{ $badge['syarat_text'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-green-200 text-center flex items-center justify-center gap-2 text-green-700 font-semibold text-sm">
                                <span class="material-symbols-outlined text-green-600">military_tech</span>
                                <span>Luar biasa! Seluruh lencana telah berhasil kamu raih.</span>
                            </div>
                        @endif
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
                                    <span class="font-bold text-black-900 text-xs sm:text-sm text-left sm:text-right break-words">{{ $siswa->sekolah ?? 'SINAU APP Academy' }}</span>
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
    @include('partials.siswa-sound')
</body>
</html>