<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Beranda Siswa - Sinau Jowo Web</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,400;0,600;0,700;0,800;0,900;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
              "brand": {
                50: '#f5f3ff', 100: '#ede9fe', 200: '#ddd6fe', 300: '#c4b5fd',
                400: '#a78bfa', 500: '#6c5ce8', 600: '#5443c9', 700: '#4338ca',
                800: '#3730a3', 950: '#1e1b4b',
              },
              "surface-container-low": "#f5f2ff",
              "surface-bright": "#fcf8ff",
              "surface-container": "#efecfd",
              "surface-container-lowest": "#ffffff",
              "surface-container-high": "#e9e6f7",
              "surface-container-highest": "#e3e0f1",
              "surface-variant": "#e3e0f1",
              "surface-dim": "#dbd8e9",
              "on-surface": "#1a1a26",
              "on-surface-variant": "#474554",
              "on-primary": "#ffffff",
              "primary": "#3c25b1",
              "primary-400": "#A79BFF",
              "primary-500": "#7B6CF0",
              "primary-600": "#6C5CE8",
              "primary-700": "#5443C9",
              "error": "#ba1a1a",
              "error-container": "#ffdad6",
              "green-500": "#4CAF6D",
              "gray-50": "#F7F7FA",
              "gray-200": "#E4E4EC",
              "gray-500": "#8A8A9A"
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
              "space-lg": "1.25rem",
              "space-xs": "0.25rem"
            },
            "fontFamily": {
              "sans": ["Nunito", "sans-serif"],
              "heading": ["Nunito", "sans-serif"],
              "body": ["Nunito", "sans-serif"],
              "display": ["Nunito", "sans-serif"],
              "caption": ["Nunito", "sans-serif"],
              "label-upper": ["Nunito", "sans-serif"],
              "stat-number": ["Nunito", "sans-serif"]
            }
          }
        }
    };
    </script>
    <style>
        ::-webkit-scrollbar { display: none; }
        .sj-app {
            background-color: #f7f3ff;
        }
        .sj-widgets {
            background-color: #f7f3ff;
        }
        .connector-dashed-line {
            stroke-dasharray: 6, 6;
            animation: dashMove 20s linear infinite;
        }
        @keyframes dashMove { to { stroke-dashoffset: -100; } }
        .animate-in { animation: fadeZoom .2s ease-out; }
        @keyframes fadeZoom { from { opacity: 0; transform: scale(.96); } to { opacity: 1; transform: scale(1); } }
        .sj-card {
            box-shadow: 0 4px 0 rgba(15, 23, 42, 0.07), 0 2px 4px rgba(15, 23, 42, 0.05);
        }
        .sj-card-hover:hover {
            box-shadow: 0 6px 0 rgba(15, 23, 42, 0.09), 0 3px 10px rgba(15, 23, 42, 0.06);
        }
    </style>
</head>
<body class="bg-[#f7f3ff] antialiased text-slate-900">
    <x-sidebar active="beranda" />

    <!-- MOBILE TOP HEADER (Hanya tampil di mobile/tablet < lg) -->
    <header class="lg:hidden sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b-2 border-slate-200/80 px-4 py-3 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-primary-600 flex items-center justify-center text-white shadow-sm shadow-primary-600/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-base font-black tracking-tight text-primary uppercase leading-none">Sinau Jowo</h1>
                <span class="text-[9px] font-extrabold uppercase tracking-widest text-primary-600 block mt-0.5">Platform Pasinaon</span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" id="btn-open-streak-modal-mobile" class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-orange-50 border border-orange-200 text-orange-600 text-xs font-black active:scale-95 transition-transform" title="Streak Belajar">
                <svg class="w-4 h-4 fill-current text-orange-500" viewBox="0 0 24 24"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 3.5z"></path></svg>
                <span>{{ $currentStreak }}</span>
            </button>
            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-brand-50 border border-brand-200 text-brand-600 text-xs font-black" title="Total EXP">
                <svg class="w-4 h-4 fill-current text-brand-600" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path></svg>
                <span>{{ number_format($totalExp) }}</span>
            </div>
        </div>
    </header>

    <div class="pl-0 lg:pl-72 min-h-screen pb-24 lg:pb-8 sj-app">
        <div class="flex flex-col xl:flex-row max-w-[1200px] mx-auto">
            <!-- BEGIN: Center Learning Column -->
            <main class="flex-1 min-w-0 px-4 sm:px-8 py-5 sm:py-7">
                <div class="max-w-3xl mx-auto space-y-6 sm:space-y-8">

                    <!-- Welcome Alert Banner -->
                    <section id="welcome-alert" class="bg-emerald-50/80 border-2 border-emerald-200 rounded-[28px] px-5 py-3.5 flex items-center gap-3 text-emerald-900 sj-card" data-purpose="welcome-alert">
                        <div class="w-7 h-7 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" viewBox="0 0 24 24">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                        <p class="text-sm font-bold flex-1">
                            <strong class="font-black text-emerald-950">Selamat datang {{ $siswa->nama_lengkap }}!</strong>
                            @if($currentStreak > 0)
                                Lanjutkan belajar hari ini untuk mempertahankan streak {{ $currentStreak }} hari.
                            @else
                                Ayo mulai belajar hari ini dan raih streak pertamamu!
                            @endif
                        </p>
                        <button type="button" id="welcome-alert-close" title="Tutup notifikasi"
                            class="shrink-0 w-7 h-7 rounded-full text-emerald-600/40 hover:text-emerald-700 hover:bg-emerald-100 flex items-center justify-center transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 24 24">
                                <line x1="18" x2="6" y1="6" y2="18"></line>
                                <line x1="6" x2="18" y1="6" y2="18"></line>
                            </svg>
                        </button>
                    </section>

                    <!-- Dhaptar Unit & Learning Path (jejer mudhun) -->
                    @forelse($levels as $lvl)
                        @php
                            $pembahasanList = $pembahasanByLevel[$lvl->id] ?? collect();
                            $xPattern = [180, 260, 100];
                            $pts = [];
                            foreach ($pembahasanList as $i => $pb) {
                                $pts[] = ['x' => $xPattern[$i % 3], 'y' => 70 + $i * 170];
                            }
                            $pathHeight = count($pts) > 0 ? (70 + max(0, count($pts) - 1) * 170 + 36 + 120) : 230;

                            $d = '';
                            if (count($pts) > 1) {
                                $d = 'M '.$pts[0]['x'].' '.$pts[0]['y'];
                                for ($i = 1; $i < count($pts); $i++) {
                                    $a = $pts[$i - 1];
                                    $b = $pts[$i];
                                    $my = ($a['y'] + $b['y']) / 2;
                                    $d .= " C {$a['x']} {$my}, {$b['x']} {$my}, {$b['x']} {$b['y']}";
                                }
                            }

                            $selesaiCount = $pembahasanList->where('status', 'selesai')->count();
                            $progressEnd = min($selesaiCount, max(count($pts) - 1, 0));
                            $dProgress = '';
                            if ($progressEnd >= 1) {
                                $dProgress = 'M '.$pts[0]['x'].' '.$pts[0]['y'];
                                for ($i = 1; $i <= $progressEnd; $i++) {
                                    $a = $pts[$i - 1];
                                    $b = $pts[$i];
                                    $my = ($a['y'] + $b['y']) / 2;
                                    $dProgress .= " C {$a['x']} {$my}, {$b['x']} {$my}, {$b['x']} {$b['y']}";
                                }
                            }

                            $firstAktifId = optional($pembahasanList->firstWhere('status', '!=', 'selesai'))->id;
                        @endphp

                        {{-- Banner unit (ringkes) --}}
                        <section class="bg-gradient-to-r from-brand-600 to-indigo-600 rounded-[24px] px-5 py-4 text-white border-2 border-brand-700 sj-card relative overflow-hidden" data-purpose="unit-banner-{{ $lvl->id }}">
                            <div class="flex items-center justify-between gap-4 relative z-10">
                                <div class="flex items-center gap-3 min-w-0">
                                    @if($loop->first)
                                        <a href="{{ route('siswa.topik') }}" title="Pilih topik"
                                           class="w-9 h-9 rounded-lg text-white hover:bg-white/15 flex items-center justify-center shrink-0 transition-colors cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 24 24">
                                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                                <polyline points="12 19 5 12 12 5"></polyline>
                                            </svg>
                                        </a>
                                    @endif
                                    <div class="min-w-0">
                                        <div class="text-[11px] font-black uppercase tracking-widest text-brand-200">
                                            @if($topikAktif)
                                                BAGIAN {{ $topikAktif->urutan }}, UNIT {{ $lvl->urutan_unit ?? $lvl->urutan }}
                                            @else
                                                UNIT {{ $lvl->urutan_unit ?? $lvl->urutan }}
                                            @endif
                                        </div>
                                        <h3 class="text-2xl font-black leading-tight truncate">
                                            {{ $lvl->nama_materi }}
                                        </h3>
                                    </div>
                                </div>

                                @if($lvl->status === 'terkunci')
                                    <span class="shrink-0 w-11 h-11 rounded-xl bg-white/10 border border-white/20 text-white/80 flex items-center justify-center" title="Terkunci">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" viewBox="0 0 24 24"><rect height="11" rx="2" ry="2" width="18" x="3" y="11"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                    </span>
                                @else
                                    <span class="shrink-0 w-11 h-11 rounded-xl bg-white/10 border border-white/20 text-white flex items-center justify-center" title="Unit iki">
                                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                    </span>
                                @endif
                            </div>
                        </section>

                        {{-- Roadmap pembahasan unit iki --}}
                        <section class="relative py-6 px-2 sm:px-4 mx-auto max-w-xl select-none flex justify-center overflow-x-auto" data-purpose="roadmap-{{ $lvl->id }}">
                            <div class="relative shrink-0" style="width: 360px; height: {{ $pathHeight }}px;">
                                @if($d)
                                    <svg class="absolute inset-0 pointer-events-none z-0" width="360" height="{{ $pathHeight }}" viewBox="0 0 360 {{ $pathHeight }}" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="{{ $d }}" stroke="#e2e8f0" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"></path>
                                        <path class="connector-dashed-line" d="{{ $d }}" stroke="#cbd5e1" stroke-linecap="round" stroke-linejoin="round" stroke-width="8"></path>
                                        @if($dProgress)
                                            <path d="{{ $dProgress }}" stroke="#10b981" stroke-linecap="round" stroke-linejoin="round" stroke-width="10"></path>
                                        @endif
                                    </svg>
                                @endif

                                @forelse($pembahasanList as $i => $pembahasan)
                                    @php
                                        $pt = $pts[$i];
                                        $terkunci = $pembahasan->terkunci;
                                        $selesai = $pembahasan->status === 'selesai';
                                        $aktifNode = ! $terkunci && ! $selesai && $pembahasan->id === $firstAktifId;
                                        $subLabel = $terkunci
                                            ? 'Terkunci'
                                            : ($selesai
                                                ? $pembahasan->lulus_count.'/'.$pembahasan->total_soal.' • Rampung'
                                                : $pembahasan->persen.'% • '.$pembahasan->lulus_count.'/'.$pembahasan->total_soal.' soal');
                                    @endphp
                                    <div class="absolute flex flex-col items-center" style="left: {{ $pt['x'] }}px; top: {{ $pt['y'] - 36 }}px; transform: translateX(-50%); width: 200px;">
                                        <div class="relative flex flex-col items-center">
                                            @if($terkunci)
                                                <div class="w-[72px] h-[72px] rounded-full bg-slate-200 text-slate-400 flex items-center justify-center ring-4 ring-white border-b-4 border-slate-200 cursor-not-allowed shadow-inner">
                                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" viewBox="0 0 24 24"><rect height="11" rx="2" ry="2" width="18" x="3" y="11"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                                </div>
                                            @elseif($selesai)
                                                <div class="relative">
                                                    <a href="{{ $pembahasan->mulai_url }}" class="w-[72px] h-[72px] rounded-full bg-emerald-500 hover:bg-emerald-600 text-white flex items-center justify-center shadow-lg shadow-emerald-500/30 transition-transform active:scale-95 ring-4 ring-white border-b-4 border-emerald-700">
                                                        <svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                    </a>
                                                    <span class="absolute -top-2 -right-3 bg-emerald-600 text-white text-[10px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full shadow-sm">RAMPUNG</span>
                                                </div>
                                            @else
                                                <div class="flex flex-col items-center relative">
                                                    @if($aktifNode)
                                                        <div class="absolute -top-10 bg-brand-600 text-white font-black text-xs py-1.5 px-4 rounded-full flex items-center gap-1.5 shadow-lg shadow-brand-600/40 animate-pulse whitespace-nowrap z-20">
                                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                                            <span>MULAI</span>
                                                            <div class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-brand-600 rotate-45"></div>
                                                        </div>
                                                    @endif
                                                    <a href="{{ $pembahasan->mulai_url }}" class="w-[72px] h-[72px] rounded-full bg-brand-600 hover:bg-brand-700 text-white flex items-center justify-center shadow-xl shadow-brand-600/40 ring-4 ring-brand-200 border-b-4 border-brand-800 transition-transform hover:scale-105 active:scale-95">
                                                        <svg class="w-9 h-9" fill="currentColor" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                                    </a>
                                                </div>
                                            @endif

                                            <div class="mt-3 text-center bg-white px-3 py-1.5 rounded-[20px] sj-card border-2 w-[200px] {{ $aktifNode ? 'border-brand-200' : 'border-slate-200' }}">
                                                <div class="text-xs font-black {{ $terkunci ? 'text-slate-500' : ($aktifNode ? 'text-brand-950' : 'text-slate-800') }} leading-snug">{{ $pembahasan->nama }}</div>
                                                <div class="text-[10px] mt-0.5 {{ $terkunci ? 'font-bold text-slate-400' : ($selesai ? 'font-extrabold text-emerald-600' : 'font-extrabold text-brand-600') }}">{{ $subLabel }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="bg-white rounded-[24px] p-6 text-center text-slate-500 font-bold text-sm border-2 border-slate-200 sj-card">
                                            Belum ada pembahasan pada unit ini.
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </section>
                    @empty
                        <div class="bg-white rounded-[28px] p-8 text-center text-slate-500 font-bold border-2 border-slate-200 sj-card">
                            Belum ada unit materi sing kasedhiya.
                        </div>
                    @endforelse
                </div>
            </main>
            <!-- END: Center Learning Column -->

            <!-- BEGIN: Right Sidebar (Widgets) -->
            <aside id="dashboard-widgets" class="hidden xl:block w-[380px] shrink-0 px-6 py-7 space-y-5 sj-widgets sticky top-0 self-start" data-purpose="right-sidebar-widgets">
                <!-- Stats Bar -->
                <div class="bg-white rounded-[32px] p-5 px-6 border-2 border-slate-200 sj-card sj-card-hover flex items-center justify-around" data-purpose="top-stats-bar">
                    <button type="button" id="btn-open-streak-modal" class="flex items-center gap-3 text-left cursor-pointer active:scale-95 transition-transform">
                        <div class="text-orange-500 shrink-0">
                            <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 3.5z"></path></svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-2xl font-black text-slate-900 leading-none">{{ $currentStreak }} Hari</span>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mt-1.5 leading-none">Streak Belajar</span>
                        </div>
                    </button>
                    <div class="h-9 w-px bg-slate-200"></div>
                    <div class="flex items-center gap-3">
                        <div class="text-brand-600 shrink-0">
                            <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path></svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-2xl font-black text-slate-900 leading-none">{{ number_format($totalExp) }}</span>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mt-1.5 leading-none">Total EXP</span>
                        </div>
                    </div>
                </div>

                <!-- Latihan Ngomong (STT & TTS) -->
                <section class="bg-white rounded-[32px] p-5 border-2 border-slate-200 sj-card sj-card-hover space-y-3" data-purpose="speaking-practice">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-pink-100 text-pink-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"></path>
                                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                                    <line x1="12" x2="12" y1="19" y2="22"></line>
                                </svg>
                            </div>
                            <h3 class="text-base font-black text-slate-900">Latihan Ngomong</h3>
                        </div>
                        <span class="inline-flex items-center gap-1 text-[10px] font-extrabold uppercase tracking-wider text-brand-600">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span> STT &amp; TTS
                        </span>
                    </div>
                    <p class="text-xs font-bold text-slate-500 leading-relaxed">
                        Latih pangucapanmu nganggo mic. Swara diowahi dadi teks (STT), banjur diwangsuli swara (TTS).
                    </p>
                    <a href="{{ route('kuis.latihan-ngomong') }}" class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-2xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-black transition-all shadow-md shadow-brand-600/30 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" viewBox="0 0 24 24">
                            <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"></path>
                            <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                            <line x1="12" x2="12" y1="19" y2="22"></line>
                        </svg>
                        <span>Mulai Latihan Ngomong</span>
                    </a>
                </section>

                <!-- Misi Harian -->
                <section class="bg-white rounded-[32px] p-6 border-2 border-slate-200 sj-card sj-card-hover space-y-4" data-purpose="daily-quests">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-brand-100 text-brand-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle>
                            </svg>
                        </div>
                        <h3 class="text-base font-black text-slate-900">Misi Harian</h3>
                    </div>
                    <div class="space-y-4">
                        @foreach($misiHarian as $misi)
                            @php
                                $persenMisi = $misi['target'] > 0 ? (int) round(($misi['progress'] / $misi['target']) * 100) : 0;
                                $bar = match ($misi['warna']) {
                                    'emerald' => 'bg-emerald-500',
                                    'brand' => 'bg-brand-600',
                                    default => 'bg-brand-300',
                                };
                            @endphp
                            <div>
                                <div class="flex justify-between text-sm font-bold text-slate-700 mb-1.5">
                                    <span>{{ $misi['label'] }}</span>
                                    <span class="font-black {{ $persenMisi >= 100 ? 'text-brand-600' : 'text-slate-900' }}">
                                        {{ $misi['progress'] }}/{{ $misi['target'] }}{{ ($misi['warna'] === 'brand') ? ' XP' : '' }}
                                    </span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                                    <div class="{{ $bar }} h-3 rounded-full transition-all" style="width: {{ $persenMisi }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <!-- Asisten Tanya Bahasa AI -->
                <section class="bg-white rounded-[32px] p-6 border-2 border-slate-200 sj-card sj-card-hover space-y-4" data-purpose="ai-assistant">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-brand-100 text-brand-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path d="M12 8V4H8"></path><rect width="16" height="12" x="4" y="8" rx="2"></rect><path d="M2 14h2"></path><path d="M20 14h2"></path><path d="M15 13v2"></path><path d="M9 13v2"></path>
                                </svg>
                            </div>
                            <h3 class="text-base font-black text-slate-900">Tanya Bahasa AI</h3>
                        </div>
                        <span class="inline-flex items-center gap-1 text-[10px] font-extrabold uppercase tracking-wider text-emerald-600">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Online
                        </span>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-8 h-8 rounded-xl bg-brand-600 text-white flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" viewBox="0 0 24 24">
                                <path d="M12 8V4H8"></path><rect width="16" height="12" x="4" y="8" rx="2"></rect><path d="M2 14h2"></path><path d="M20 14h2"></path><path d="M15 13v2"></path><path d="M9 13v2"></path>
                            </svg>
                        </div>
                        <div class="bg-brand-50 border border-brand-100 rounded-2xl rounded-tl-sm px-3.5 py-2.5 text-xs font-bold text-slate-700 leading-relaxed">
                            Sugeng rawuh! Takon wae babagan tembung, aksara, utawa unggah-ungguh basa Jawa.
                        </div>
                    </div>
                    <a href="{{ route('siswa.asisten') }}" class="w-full flex items-center justify-center gap-2 py-3.5 px-4 rounded-2xl bg-brand-600 hover:bg-brand-700 text-white text-sm font-black transition-all shadow-md shadow-brand-600/30 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" viewBox="0 0 24 24">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                        <span>Mulai Chat AI</span>
                    </a>
                </section>

            </aside>
            <!-- END: Right Sidebar -->
        </div>
    </div>

    <!-- Streak Modal -->
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm hidden" id="streak-modal-container">
        <div class="bg-white rounded-[28px] max-w-md w-full p-6 shadow-2xl border-2 border-brand-200 relative space-y-5 animate-in">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-2xl bg-orange-50 border border-orange-100 text-orange-500 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 3.5z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900 leading-tight">Streak Belajar Aktif!</h3>
                        <p class="text-[11px] font-bold text-slate-400">Rekapitulasi Konsistensi Belajar</p>
                    </div>
                </div>
                <button class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors cursor-pointer" id="btn-close-streak-x" type="button">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" x2="6" y1="6" y2="18"></line><line x1="6" x2="18" y1="6" y2="18"></line></svg>
                </button>
            </div>
            <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl p-4 border border-amber-200 text-center relative overflow-hidden">
                <div class="text-3xl font-black text-orange-600 leading-none mb-1 flex items-center justify-center gap-1.5">
                    <span>{{ $currentStreak }}</span>
                    <span class="text-lg font-black text-slate-800">Hari Berturut-turut!</span>
                </div>
                <p class="text-xs font-bold text-slate-600 mt-1.5">
                    Rekor Tertinggi: <strong class="text-slate-900 font-black">{{ $highestStreak }} Hari</strong>
                </p>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between items-center text-xs font-bold text-slate-700">
                    <span>Minggu Ini</span>
                    <span class="text-[11px] font-extrabold text-emerald-600">{{ $weekStreak->where('aktif', true)->count() }} dari 7 Hari Selesai</span>
                </div>
                <div class="grid grid-cols-7 gap-1.5 text-center">
                    @foreach($weekStreak as $hari)
                        <div class="flex flex-col items-center gap-1.5 p-2 rounded-xl border
                            {{ $hari['is_today'] ? 'bg-orange-100 border-2 border-orange-400 shadow-sm' : ($hari['aktif'] ? 'bg-emerald-50 border-emerald-200' : 'bg-slate-50 border-slate-200') }}">
                            <span class="text-[10px] font-black uppercase {{ $hari['is_today'] ? 'text-orange-950' : ($hari['aktif'] ? 'text-emerald-800' : 'text-slate-500') }}">{{ $hari['label'] }}</span>
                            @if($hari['aktif'])
                                <div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                </div>
                            @elseif($hari['is_today'])
                                <div class="w-6 h-6 rounded-full bg-orange-500 text-white flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 3.5z"></path></svg>
                                </div>
                            @else
                                <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center">
                                    <span class="text-[10px] font-black">{{ $hari['is_future'] ? '+10' : '·' }}</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            <p class="text-xs text-slate-600 text-center leading-relaxed bg-slate-50 p-2.5 rounded-xl border border-slate-100 font-semibold">
                “Belajar sethithik saben dina luwih apik tinimbang akeh nanging mung sedhela. Pertahankan streak-mu!”
            </p>
            <div class="flex items-center gap-2.5 pt-1">
                <button class="flex-1 py-2.5 px-4 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-black text-xs transition-colors cursor-pointer" id="btn-close-streak-footer" type="button">Tutup</button>
                <a href="{{ $fokusPembahasan?->mulai_url ?? ($activeLevel ? route('kuis.mulai', $activeLevel->id) : route('siswa.dashboard')) }}" class="flex-1 text-center py-2.5 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-black text-xs transition-all shadow-md shadow-brand-600/30" id="btn-continue-learning">Lanjutkan Belajar</a>
            </div>
        </div>
    </div>

    <script id="widgets-sticky-script">
        (function () {
            const aside = document.getElementById('dashboard-widgets');
            if (!aside) return;

            function apply() {
                if (window.innerWidth < 1280) {
                    aside.style.top = '';
                    return;
                }
                const offset = Math.min(0, window.innerHeight - aside.offsetHeight);
                aside.style.top = offset + 'px';
            }

            apply();
            window.addEventListener('load', apply);
            window.addEventListener('resize', apply);
        })();
    </script>

    <script id="welcome-alert-script">
        (function () {
            const alert = document.getElementById('welcome-alert');
            const close = document.getElementById('welcome-alert-close');

            if (alert && close) {
                close.addEventListener('click', function () {
                    alert.remove();
                });
            }
        })();
    </script>

    <script id="streak-modal-script">
        (function () {
            const modal = document.getElementById('streak-modal-container');
            const openBtn = document.getElementById('btn-open-streak-modal');
            const openBtnMobile = document.getElementById('btn-open-streak-modal-mobile');
            const closeX = document.getElementById('btn-close-streak-x');
            const closeFooter = document.getElementById('btn-close-streak-footer');

            function show() { if (modal) modal.classList.remove('hidden'); }
            function hide() { if (modal) modal.classList.add('hidden'); }

            if (openBtn) openBtn.addEventListener('click', show);
            if (openBtnMobile) openBtnMobile.addEventListener('click', show);
            if (closeX) closeX.addEventListener('click', hide);
            if (closeFooter) closeFooter.addEventListener('click', hide);
            if (modal) {
                modal.addEventListener('click', function (e) {
                    if (e.target === modal) hide();
                });
            }
        })();
    </script>
</body>
</html>
