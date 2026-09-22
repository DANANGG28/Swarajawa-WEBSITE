<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papan Skor & Peringkat Siswa - Sinau Jowo Web</title>
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
    <x-sidebar active="papan-skor" />

    <div class="pl-0 lg:pl-72 flex flex-col min-h-screen pb-24 lg:pb-0">
        <!-- HEADER -->
        <header class="fixed top-0 left-0 lg:left-72 right-0 h-16 lg:h-20 bg-surface-container-lowest/90 backdrop-blur-xl z-40 shadow-[0_1px_8px_rgba(0,0,0,0.04)] px-4 lg:px-space-xl flex items-center justify-between">
            <div class="flex items-center flex-1 max-w-md">
                <div class="flex items-center w-full bg-gray-50 rounded-full px-3 py-1.5 sm:px-space-md sm:py-space-xs gap-2 sm:gap-space-sm border border-gray-200/60 focus-within:border-primary-500 transition-colors">
                    <span class="material-symbols-outlined text-gray-500 text-[18px] sm:text-[20px]">search</span>
                    <input type="text" placeholder="Cari materi, aksara, siswa..." class="w-full bg-transparent border-none outline-none font-body text-on-surface placeholder:text-gray-500 text-xs sm:text-sm">
                </div>
            </div>
            <div class="flex items-center gap-space-lg ml-3">
                <button type="button" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center bg-gray-50 text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors relative">
                    <span class="material-symbols-outlined text-[18px] sm:text-[20px]">notifications</span>
                    <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-secondary"></span>
                </button>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="flex-1 pt-16 lg:pt-20 w-full px-3.5 sm:px-6 lg:px-margin-desktop py-4 lg:py-space-xl bg-background">
            <div class="flex flex-col w-full pb-space-xl gap-4 sm:gap-space-lg">
                <!-- HERO HEADER BANNER -->
                <section class="relative overflow-hidden rounded-[20px] sm:rounded-[24px] bg-gradient-to-r from-primary-700 via-primary-600 to-primary text-on-primary p-4 sm:p-6 lg:p-8 shadow-md">
                    <div class="relative z-10 flex flex-col gap-3 sm:gap-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1.5 font-label-upper tracking-wider text-yellow-300 uppercase font-bold text-[10px] sm:text-xs">
                                <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4 fill-current text-yellow-300 shrink-0" viewBox="0 0 24 24">
                                    <path d="M12 2l2.4 7.4h7.6l-6.2 4.5 2.4 7.4-6.2-4.5-6.2 4.5 2.4-7.4-6.2-4.5h7.6z"></path>
                                </svg>
                                <span class="truncate">PAPAN SKOR • {{ $scope === 'sekolah' ? 'PERINGKAT SEKOLAH' : ($kelasSiswa ? 'KELAS '.$kelasSiswa : 'SEMUA KELAS') }}</span>
                            </div>
                        </div>

                        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4 sm:gap-6">
                            <div class="max-w-2xl">
                                <h1 class="font-display text-on-primary font-bold text-lg sm:text-2xl lg:text-3xl mb-1 sm:mb-2 leading-snug">
                                    Papan Peringkat Siswa
                                </h1>
                                <p class="font-body text-xs sm:text-sm lg:text-base text-primary-fixed leading-relaxed">
                                    Lihat capaian poin (XP), streak pembelajaran, dan urutan peringkat siswa.
                                </p>
                            </div>

                            <!-- Opsi Tampilan: Kelas / Sekolah -->
                            <div class="shrink-0 flex items-center self-start lg:self-end">
                                <div class="inline-flex items-center p-1 bg-black/20 backdrop-blur-md rounded-xl sm:rounded-2xl border border-white/20 shadow-inner gap-1">
                                    <a href="{{ route('siswa.papan-skor') }}" class="inline-flex items-center justify-center gap-1.5 px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg sm:rounded-xl text-xs sm:text-sm font-caption font-bold whitespace-nowrap transition-all duration-200 {{ $scope === 'kelas' ? 'bg-white text-primary-700 shadow-md' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                                        <span class="material-symbols-outlined text-[16px] sm:text-[18px] shrink-0">groups</span>
                                        <span>Kelas {{ $kelasSiswa ?? '-' }}</span>
                                    </a>
                                    <a href="{{ route('siswa.papan-skor', ['scope' => 'sekolah']) }}" class="inline-flex items-center justify-center gap-1.5 px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg sm:rounded-xl text-xs sm:text-sm font-caption font-bold whitespace-nowrap transition-all duration-200 {{ $scope === 'sekolah' ? 'bg-white text-primary-700 shadow-md' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                                        <span class="material-symbols-outlined text-[16px] sm:text-[18px] shrink-0">school</span>
                                        <span>Sekolah</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- JUARA 1 SEKOLAH -->
                @if($scope === 'kelas' && $juaraSekolah)
                    <section class="relative overflow-hidden rounded-xl sm:rounded-2xl bg-gradient-to-r from-amber-500 to-amber-400 text-white p-3.5 sm:p-5 shadow-sm flex items-center justify-between gap-3 sm:gap-4">
                        <div class="flex items-center gap-2.5 sm:gap-4 min-w-0">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-white/20 flex items-center justify-center shrink-0 overflow-hidden relative shadow-sm border border-white/30">
                                @if(!empty($juaraSekolah['foto_url']))
                                    <img src="{{ $juaraSekolah['foto_url'] }}" alt="{{ $juaraSekolah['nama'] }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.classList.add('hidden'); this.nextElementSibling?.classList.remove('hidden');">
                                    <span class="hidden material-symbols-outlined text-[22px] sm:text-[28px]" style="font-variation-settings: 'FILL' 1;">emoji_events</span>
                                @else
                                    <span class="material-symbols-outlined text-[22px] sm:text-[28px]" style="font-variation-settings: 'FILL' 1;">emoji_events</span>
                                @endif
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="font-label-upper text-[10px] sm:text-xs uppercase tracking-wider text-white/90 font-bold">Juara 1 Sekolah</span>
                                <span class="font-heading text-xs sm:text-base font-extrabold truncate">{{ $juaraSekolah['nama'] }}</span>
                                <span class="font-caption text-[10px] sm:text-xs text-white/80">{{ $juaraSekolah['kelas'] ? 'Kelas '.$juaraSekolah['kelas'] : 'Siswa' }}</span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="font-heading text-base sm:text-2xl font-black leading-none">{{ number_format($juaraSekolah['total_exp']) }}</span>
                            <span class="font-label-upper text-[9px] sm:text-xs font-bold text-white/80 block mt-0.5">XP</span>
                        </div>
                    </section>
                @endif

                <!-- PODIUM TOP 3 SECTION -->
                <section class="bg-surface-container-lowest rounded-xl sm:rounded-2xl p-3.5 sm:p-5 lg:p-6 shadow-sm border border-gray-100 flex flex-col gap-4 sm:gap-6">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3 sm:pb-4">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-primary-500/10 text-primary-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 fill-none stroke-currentColor stroke-2" viewBox="0 0 24 24">
                                    <path d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </div>
                            <h2 class="font-heading text-xs sm:text-base lg:text-heading font-bold text-on-surface">Tiga Besar (Top 3)</h2>
                        </div>
                        <span class="font-label-upper text-[10px] sm:text-xs uppercase tracking-wider text-gray-500 font-semibold truncate">{{ $scope === 'sekolah' ? 'SEKOLAH' : 'KELAS '.($kelasSiswa ?? 'SEMUA') }}</span>
                    </div>

                    <!-- 3 Pillar Podium Grid (Align Bottom) -->
                    <div class="grid grid-cols-3 gap-2 sm:gap-4 md:gap-6 items-end pt-2 sm:pt-4 pb-1 sm:pb-2">
                        <!-- RANK 2: LEFT -->
                        <div class="flex flex-col items-center text-center min-w-0">
                            @if($top2)
                                @php
                                    $in2 = mb_strtoupper(mb_substr($top2['nama'], 0, 2));
                                    $isMe2 = $top2['siswa_id'] === $siswa->id;
                                @endphp
                                <div class="flex flex-col items-center mb-2 sm:mb-3 w-full px-0.5">
                                    <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full p-0.5 sm:p-1 bg-orange-300/40 shadow-sm relative mb-1.5 sm:mb-2 shrink-0">
                                        @if(!empty($top2['foto_url']))
                                            <img src="{{ $top2['foto_url'] }}" alt="{{ $top2['nama'] }}" class="w-full h-full rounded-full object-cover border-2 border-white shadow-sm" onerror="this.onerror=null; this.classList.add('hidden'); this.nextElementSibling?.classList.remove('hidden');">
                                            <div class="hidden w-full h-full rounded-full bg-primary-700 text-white font-bold text-xs sm:text-lg flex items-center justify-center">
                                                {{ $in2 }}
                                            </div>
                                        @else
                                            <div class="w-full h-full rounded-full bg-primary-700 text-white font-bold text-xs sm:text-lg flex items-center justify-center">
                                                {{ $in2 }}
                                            </div>
                                        @endif
                                    </div>
                                    <span class="font-heading text-[11px] sm:text-sm font-bold text-on-surface leading-tight truncate w-full block" title="{{ $top2['nama'] }}">{{ $top2['nama'] }}</span>
                                    @if($isMe2)
                                        <span class="font-caption text-[10px] sm:text-xs font-bold text-primary-600 truncate w-full block">(Anda)</span>
                                    @else
                                        <span class="font-caption text-[10px] sm:text-xs text-gray-500 truncate w-full block">{{ $top2['kelas'] ? 'Kelas '.$top2['kelas'] : 'Siswa' }}</span>
                                    @endif
                                    <span class="font-heading text-[11px] sm:text-sm md:text-base text-primary-700 font-extrabold mt-0.5 whitespace-nowrap">{{ number_format($top2['total_exp']) }} XP</span>
                                </div>
                            @else
                                <div class="flex flex-col items-center mb-3 text-gray-400 font-caption text-xs">Belum ada</div>
                            @endif
                            <!-- Balok Pillar Rank 2 -->
                            <div class="w-full h-24 sm:h-36 bg-[#F7B98A] rounded-t-xl sm:rounded-t-2xl flex flex-col justify-between items-center py-2 sm:py-4 shadow-sm border-t-2 border-white/40">
                                <span class="text-white font-label-upper text-[8px] sm:text-xs font-bold uppercase tracking-tight sm:tracking-widest">Runner-Up</span>
                                <span class="font-heading text-3xl sm:text-5xl leading-none text-white font-black drop-shadow-sm">2</span>
                                <div class="w-6 sm:w-8 h-0.5 sm:h-1 bg-white/40 rounded-full"></div>
                            </div>
                        </div>

                        <!-- RANK 1: CENTER -->
                        <div class="flex flex-col items-center text-center min-w-0">
                            @if($top1)
                                @php
                                    $in1 = mb_strtoupper(mb_substr($top1['nama'], 0, 2));
                                    $isMe1 = $top1['siswa_id'] === $siswa->id;
                                @endphp
                                <div class="flex flex-col items-center mb-2 sm:mb-3 w-full px-0.5">
                                    <div class="text-[#F0955A] mb-0.5 sm:mb-1">
                                        <svg class="w-5 h-5 sm:w-7 sm:h-7 fill-[#F6D98B] stroke-[#623814]/30 stroke-1" viewBox="0 0 24 24">
                                            <path d="M5 16L3 5L8.5 10L12 4L15.5 10L21 5L19 16H5M19 19C19 19.6 18.6 20 18 20H6C5.4 20 5 19.6 5 19V18H19V19Z"></path>
                                        </svg>
                                    </div>
                                    <div class="w-14 h-14 sm:w-20 sm:h-20 rounded-full p-0.5 sm:p-1 bg-yellow-300 shadow-md relative mb-1.5 sm:mb-2 ring-2 sm:ring-4 ring-yellow-300/30 shrink-0">
                                        @if(!empty($top1['foto_url']))
                                            <img src="{{ $top1['foto_url'] }}" alt="{{ $top1['nama'] }}" class="w-full h-full rounded-full object-cover border-2 border-white shadow-sm" onerror="this.onerror=null; this.classList.add('hidden'); this.nextElementSibling?.classList.remove('hidden');">
                                            <div class="hidden w-full h-full rounded-full bg-amber-600 text-white font-bold text-sm sm:text-xl flex items-center justify-center">
                                                {{ $in1 }}
                                            </div>
                                        @else
                                            <div class="w-full h-full rounded-full bg-amber-600 text-white font-bold text-sm sm:text-xl flex items-center justify-center">
                                                {{ $in1 }}
                                            </div>
                                        @endif
                                    </div>
                                    <span class="font-heading text-xs sm:text-base font-extrabold text-on-surface leading-tight truncate w-full block" title="{{ $top1['nama'] }}">{{ $top1['nama'] }}</span>
                                    @if($isMe1)
                                        <span class="font-caption text-[10px] sm:text-xs font-bold text-primary-600 truncate w-full block">(Anda)</span>
                                    @else
                                        <span class="font-caption text-[10px] sm:text-xs text-gray-500 truncate w-full block">{{ $top1['kelas'] ? 'Kelas '.$top1['kelas'] : 'Siswa' }}</span>
                                    @endif
                                    <span class="font-heading text-xs sm:text-base md:text-lg text-primary-700 font-black mt-0.5 whitespace-nowrap">{{ number_format($top1['total_exp']) }} XP</span>
                                </div>
                            @else
                                <div class="flex flex-col items-center mb-3 text-gray-400 font-caption text-xs">Belum ada</div>
                            @endif
                            <!-- Balok Pillar Rank 1 -->
                            <div class="w-full h-32 sm:h-48 bg-[#5443C9] rounded-t-xl sm:rounded-t-2xl flex flex-col justify-between items-center py-2 sm:py-4 shadow-md border-t-2 border-yellow-300">
                                <span class="text-yellow-300 font-label-upper text-[9px] sm:text-xs font-extrabold uppercase tracking-tight sm:tracking-widest">Juara 1</span>
                                <span class="font-heading text-4xl sm:text-6xl leading-none text-white font-black drop-shadow-sm">1</span>
                                <div class="w-8 sm:w-10 h-0.5 sm:h-1 bg-yellow-300/60 rounded-full"></div>
                            </div>
                        </div>

                        <!-- RANK 3: RIGHT -->
                        <div class="flex flex-col items-center text-center min-w-0">
                            @if($top3)
                                @php
                                    $in3 = mb_strtoupper(mb_substr($top3['nama'], 0, 2));
                                    $isMe3 = $top3['siswa_id'] === $siswa->id;
                                @endphp
                                <div class="flex flex-col items-center mb-2 sm:mb-3 w-full px-0.5">
                                    <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full p-0.5 sm:p-1 bg-yellow-300/40 shadow-sm relative mb-1.5 sm:mb-2 shrink-0">
                                        @if(!empty($top3['foto_url']))
                                            <img src="{{ $top3['foto_url'] }}" alt="{{ $top3['nama'] }}" class="w-full h-full rounded-full object-cover border-2 border-white shadow-sm" onerror="this.onerror=null; this.classList.add('hidden'); this.nextElementSibling?.classList.remove('hidden');">
                                            <div class="hidden w-full h-full rounded-full bg-amber-500 text-white font-bold text-xs sm:text-lg flex items-center justify-center">
                                                {{ $in3 }}
                                            </div>
                                        @else
                                            <div class="w-full h-full rounded-full bg-amber-500 text-white font-bold text-xs sm:text-lg flex items-center justify-center">
                                                {{ $in3 }}
                                            </div>
                                        @endif
                                    </div>
                                    <span class="font-heading text-[11px] sm:text-sm font-bold text-on-surface leading-tight truncate w-full block" title="{{ $top3['nama'] }}">{{ $top3['nama'] }}</span>
                                    @if($isMe3)
                                        <span class="font-caption text-[10px] sm:text-xs font-bold text-primary-600 truncate w-full block">(Anda)</span>
                                    @else
                                        <span class="font-caption text-[10px] sm:text-xs font-medium text-gray-500 truncate w-full block">{{ $top3['kelas'] ? 'Kelas '.$top3['kelas'] : 'Siswa' }}</span>
                                    @endif
                                    <span class="font-heading text-[11px] sm:text-sm md:text-base text-primary-700 font-extrabold mt-0.5 whitespace-nowrap">{{ number_format($top3['total_exp']) }} XP</span>
                                </div>
                            @else
                                <div class="flex flex-col items-center mb-3 text-gray-400 font-caption text-xs">Belum ada</div>
                            @endif
                            <!-- Balok Pillar Rank 3 -->
                            <div class="w-full h-20 sm:h-28 bg-[#F6D98B] rounded-t-xl sm:rounded-t-2xl flex flex-col justify-between items-center py-2 sm:py-4 shadow-sm border-t-2 border-white/40">
                                <span class="text-white font-label-upper text-[8px] sm:text-xs font-bold uppercase tracking-tight sm:tracking-widest">Peringkat 3</span>
                                <span class="font-heading text-2xl sm:text-4xl leading-none text-white font-black drop-shadow-sm">3</span>
                                <div class="w-6 sm:w-8 h-0.5 sm:h-1 bg-white/40 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- DAFTAR PERINGKAT SISWA SABANJURE (Rank 4 - 8) -->
                <section class="bg-surface-container-lowest rounded-xl sm:rounded-2xl p-3.5 sm:p-5 lg:p-6 shadow-sm border border-gray-100 flex flex-col gap-4 sm:gap-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-100 pb-3 sm:pb-4">
                        <div>
                            <h3 class="font-heading text-xs sm:text-base lg:text-heading font-bold text-on-surface">Daftar Peringkat Siswa Selanjutnya</h3>
                            <p class="font-caption text-[10px] sm:text-xs text-gray-500">Peringkat {{ $scope === 'sekolah' ? 'sekolah' : 'kelas '.$kelasSiswa }} berdasarkan total XP</p>
                        </div>
                        <div class="inline-flex items-center gap-1.5 bg-gray-50 border border-gray-200/80 px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-full self-start sm:self-auto">
                            <svg class="w-3.5 h-3.5 text-gray-500 stroke-2 fill-none stroke-currentColor shrink-0" viewBox="0 0 24 24">
                                <path d="M3 4h18M7 12h10m-7 8h4" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            <span class="font-caption text-[10px] sm:text-xs font-semibold text-on-surface-variant">Urutkan: Total XP</span>
                        </div>
                    </div>

                    <!-- List Data Table -->
                    <div class="overflow-x-auto -mx-3.5 sm:mx-0 px-3.5 sm:px-0">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-gray-500 font-label-upper text-[10px] sm:text-xs font-bold uppercase tracking-wider border-b border-gray-100">
                                    <th class="py-2.5 sm:py-3 px-2 sm:px-3 whitespace-nowrap">Peringkat</th>
                                    <th class="py-2.5 sm:py-3 px-2 sm:px-3">Siswa</th>
                                    <th class="py-2.5 sm:py-3 px-2 sm:px-3 whitespace-nowrap">Kelas</th>
                                    <th class="py-2.5 sm:py-3 px-2 sm:px-3 text-right whitespace-nowrap">Total EXP</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100/80 font-body">
                                @forelse($others as $item)
                                    @php
                                        $init = mb_strtoupper(mb_substr($item['nama'], 0, 2));
                                        $isMe = $item['siswa_id'] === $siswa->id;
                                    @endphp
                                    <tr class="hover:bg-surface-container-low/40 transition-colors {{ $isMe ? 'bg-primary-500/10' : '' }}">
                                        <td class="py-2.5 sm:py-3.5 px-2 sm:px-3">
                                            <span class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-gray-100 font-heading font-bold text-gray-700 flex items-center justify-center text-[11px] sm:text-xs">{{ $item['peringkat'] }}</span>
                                        </td>
                                        <td class="py-2.5 sm:py-3.5 px-2 sm:px-3">
                                            <div class="flex items-center gap-2 sm:gap-3">
                                                @if(!empty($item['foto_url']))
                                                    <img src="{{ $item['foto_url'] }}" alt="{{ $item['nama'] }}" class="w-7 h-7 sm:w-9 sm:h-9 rounded-full object-cover shrink-0 border border-gray-200" onerror="this.onerror=null; this.classList.add('hidden'); this.nextElementSibling?.classList.remove('hidden');">
                                                    <div class="hidden w-7 h-7 sm:w-9 sm:h-9 rounded-full bg-primary-700 text-white font-bold text-[10px] sm:text-xs flex items-center justify-center shrink-0">
                                                        {{ $init }}
                                                    </div>
                                                @else
                                                    <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-full bg-primary-700 text-white font-bold text-[10px] sm:text-xs flex items-center justify-center shrink-0">
                                                        {{ $init }}
                                                    </div>
                                                @endif
                                                <div class="flex flex-col min-w-0">
                                                    <span class="font-bold text-on-surface text-xs sm:text-sm truncate max-w-[110px] sm:max-w-none block">{{ $item['nama'] }} {{ $isMe ? '(Anda)' : '' }}</span>
                                                    <span class="font-caption text-[10px] sm:text-xs text-gray-500 hidden sm:block">Siswa Aktif</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-2.5 sm:py-3.5 px-2 sm:px-3 font-semibold text-gray-600 text-xs sm:text-sm whitespace-nowrap">{{ $item['kelas'] ?? '-' }}</td>
                                        <td class="py-2.5 sm:py-3.5 px-2 sm:px-3 font-heading font-extrabold text-primary-700 text-right text-xs sm:text-sm whitespace-nowrap">{{ number_format($item['total_exp']) }} XP</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-gray-500 text-xs sm:text-sm">Belum ada siswa lainnya di papan skor.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Kartu Highlight Personal (Posisi Sampeyan) -->
                    <div class="mt-2 p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-primary-500/10 border border-primary-500/20 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 sm:gap-3.5 min-w-0">
                            <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-primary-600 text-white font-heading font-bold flex items-center justify-center text-xs shadow-sm shrink-0">{{ $myRank }}</span>
                            @if($siswa->foto_url)
                                <img src="{{ $siswa->foto_url }}" alt="{{ $siswa->nama_lengkap }}" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full object-cover shrink-0 border-2 border-primary-400 shadow-sm" onerror="this.onerror=null; this.classList.add('hidden'); this.nextElementSibling?.classList.remove('hidden');">
                                <div class="hidden w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-primary-700 text-white font-bold text-xs sm:text-sm flex items-center justify-center border border-primary-400 shrink-0">
                                    {{ mb_strtoupper(mb_substr($siswa->nama_lengkap, 0, 2)) }}
                                </div>
                            @else
                                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-primary-700 text-white font-bold text-xs sm:text-sm flex items-center justify-center border border-primary-400 shrink-0">
                                    {{ mb_strtoupper(mb_substr($siswa->nama_lengkap, 0, 2)) }}
                                </div>
                            @endif
                            <div class="flex flex-col min-w-0">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <span class="font-heading font-bold text-on-surface text-xs sm:text-sm truncate max-w-[120px] sm:max-w-none">{{ $siswa->nama_lengkap }} (Posisi Anda)</span>
                                    <span class="font-label-upper text-[9px] sm:text-[10px] font-bold bg-primary-600 text-white px-1.5 py-0.5 rounded-full shrink-0">Anda</span>
                                </div>
                                <span class="font-caption text-[10px] sm:text-xs text-gray-500 truncate">{{ $siswa->kelas ? 'Kelas '.$siswa->kelas : 'Siswa' }} • Streak {{ $myStreak }} Hari</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 sm:gap-4 shrink-0 text-right">
                            <div class="flex flex-col">
                                <span class="font-heading font-black text-primary-700 text-xs sm:text-base md:text-lg whitespace-nowrap">{{ number_format($myExp) }} XP</span>
                                <span class="font-caption text-[10px] sm:text-xs font-semibold text-green-600 whitespace-nowrap">Peringkat #{{ $myRank }}</span>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>