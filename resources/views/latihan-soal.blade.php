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
                <form action="{{ route('siswa.latihan') }}" method="GET" class="flex items-center w-full bg-gray-50 rounded-full px-space-md py-space-xs gap-space-sm border border-gray-200/60 focus-within:border-primary-500 transition-colors">
                    <span class="material-symbols-outlined text-gray-500 text-[20px]">search</span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari level materi..." class="w-full bg-transparent border-none outline-none font-body text-body text-on-surface placeholder:text-gray-500">
                </form>
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
                                <span>PUSAT EVALUASI & TANTANGAN SISWA • {{ $siswa->kelas ? 'KELAS '.$siswa->kelas : 'SINAU JOWO' }}</span>
                            </div>
                            <h1 class="font-display text-display text-on-primary font-bold text-2xl lg:text-3xl">
                                Latihan Soal & Asesmen Bahasa Jawa
                            </h1>
                            <p class="font-body text-body text-primary-fixed leading-relaxed">
                                Pilih format latihan pembelajaran favoritmu: tes pilihan ganda, kuis wicara (STT/TTS), penelusuran aksara Jawa di kanvas, atau puzzle busana adat dengan panduan terstruktur.
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
                                    <span class="font-heading text-heading text-on-primary font-bold">{{ $totalSelesai }}</span>
                                    <span class="font-caption text-caption text-primary-fixed">/ {{ $totalSoal }} Soal</span>
                                </div>
                                <div class="h-4 w-[1px] bg-white/20 hidden sm:block"></div>
                                <div class="flex items-center gap-space-xs">
                                    <svg class="h-4 w-4 text-yellow-300" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path>
                                    </svg>
                                    <span class="font-caption text-caption text-primary-fixed">Rata-rata Skor:</span>
                                    <span class="font-heading text-heading text-yellow-300 font-bold">{{ $rataSkor }}%</span>
                                </div>
                                <div class="h-4 w-[1px] bg-white/20 hidden sm:block"></div>
                                <div class="flex items-center gap-space-xs">
                                    <svg class="h-4 w-4 text-orange-300" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" viewBox="0 0 24 24">
                                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                                    </svg>
                                    <span class="font-caption text-caption text-primary-fixed">Bonus EXP:</span>
                                    <span class="font-heading text-heading text-orange-300 font-bold">+{{ $totalExpDiperoleh }} XP</span>
                                    <span class="font-caption text-caption text-primary-fixed">Diperoleh</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-space-md lg:flex-col lg:items-end">
                            <a href="{{ $firstSoalUrl }}" class="inline-flex items-center justify-center gap-space-sm rounded-full bg-color-white font-body text-body font-bold text-primary-700 shadow-md transition-all hover:bg-surface-bright hover:shadow-lg px-6 py-3">
                                <svg class="h-4 w-4 fill-current text-primary-700" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"></path>
                                </svg>
                                <span>Mulai Latihan Harian Campuran</span>
                            </a>
                            <span class="font-caption text-caption text-primary-fixed text-center lg:text-right mt-1">
                                Mode adaptif cerdas • Menyesuaikan materi
                            </span>
                        </div>
                    </div>
                </section>

                <!-- JALUR PEMBELAJARAN: Satu kartu setiap level materi -->
                <section class="flex flex-col gap-space-sm">
                    <div class="flex items-center justify-between">
                        <span class="font-label-upper text-label-upper tracking-wider text-gray-500 uppercase font-bold">JALUR PEMBELAJARAN BERJENJANG</span>
                        <span class="font-caption text-caption text-gray-500">Pilih level untuk memulai latihan</span>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-gutter">
                        @forelse($levelCards as $lvl)
                            @php
                                $badge = $lvl->badge;
                                $border = $badge === 'sedang'
                                     ? 'border-primary-600/40 ring-2 ring-primary-600/20'
                                    : ($badge === 'terkunci' ? 'border-gray-200 opacity-75' : 'border-gray-100');
                            @endphp

                            @if($lvl->is_locked)
                                <div class="flex flex-col gap-4 rounded-[24px] bg-surface-container-lowest p-6 shadow-sm border {{ $border }}">
                            @else
                                <a href="{{ $lvl->mulai_url }}" class="group flex flex-col gap-4 rounded-[24px] bg-surface-container-lowest p-6 shadow-sm hover:shadow-md transition-all border {{ $border }}">
                            @endif
                                    {{-- Header: level & status (teks polos, tanpa pill) --}}
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex flex-col min-w-0">
                                            <span class="font-label-upper text-label-upper uppercase tracking-wider font-bold text-gray-500">
                                                LEVEL {{ $lvl->urutan }} <span class="mx-1">•</span>
                                                @if($badge === 'selesai')
                                                    <span class="text-green-500">SELESAI</span>
                                                @elseif($badge === 'sedang')
                                                    <span class="text-orange-500">SEDANG BERJALAN</span>
                                                @elseif($badge === 'terkunci')
                                                    <span class="text-gray-500">TERKUNCI</span>
                                                @else
                                                    <span class="text-primary-700">BARU</span>
                                                @endif
                                            </span>
                                            <h3 class="font-heading text-heading font-bold mt-1 {{ $badge === 'terkunci' ? 'text-gray-500' : 'text-on-surface' }}">
                                                {{ $lvl->nama_materi }}
                                            </h3>
                                            <p class="font-caption text-caption text-on-surface-variant mt-1 leading-relaxed line-clamp-2">
                                                {{ $lvl->deskripsi ?? 'Materi pembelajaran Bahasa dan Budaya Jawa.' }}
                                            </p>
                                        </div>
                                        @if($badge === 'selesai')
                                            <span class="material-symbols-outlined icon-fill text-green-500 shrink-0">verified</span>
                                        @elseif($badge === 'terkunci')
                                            <span class="material-symbols-outlined text-gray-400 shrink-0">lock</span>
                                        @else
                                            <span class="material-symbols-outlined text-primary-600 shrink-0 group-hover:translate-x-1 transition-transform">arrow_forward</span>
                                        @endif
                                    </div>

                                    {{-- Tipe soal yang ada di level (teks polos) --}}
                                    @if($lvl->tipe_list->isNotEmpty())
                                        <span class="font-caption text-caption text-gray-500">{{ $lvl->tipe_list->implode(' • ') }}</span>
                                    @endif

                                    {{-- Progress --}}
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 h-2.5 bg-surface-container-high rounded-full overflow-hidden">
                                            <div class="h-full rounded-full {{ $badge === 'selesai' ? 'bg-green-500' : 'bg-primary-600' }} transition-all" style="width: {{ $lvl->persen }}%"></div>
                                        </div>
                                        <span class="font-caption text-caption font-bold text-on-surface-variant whitespace-nowrap">{{ $lvl->lulus_count }}/{{ $lvl->total_soal }} Soal ({{ $lvl->persen }}%)</span>
                                    </div>

                                    {{-- Footer: reward + aksi --}}
                                    <div class="flex items-center justify-between gap-3 pt-1">
                                        <span class="font-label-upper text-label-upper font-bold text-tertiary">+{{ $lvl->reward_exp }} EXP</span>
                                        @if($lvl->is_locked)
                                            <span class="rounded-full bg-gray-200 px-5 py-2 font-body text-body font-semibold text-gray-500 cursor-not-allowed">Terkunci</span>
                                        @elseif($badge === 'selesai')
                                            <span class="rounded-full bg-surface-container-high px-5 py-2 font-body text-body font-semibold text-on-surface group-hover:bg-surface-dim transition-colors">Latihan Lagi</span>
                                        @elseif($badge === 'sedang')
                                            <span class="rounded-full bg-primary-600 px-5 py-2 font-body text-body font-semibold text-on-primary group-hover:bg-primary-700 transition-colors shadow-sm">Lanjutkan</span>
                                        @else
                                            <span class="rounded-full bg-primary-600 px-5 py-2 font-body text-body font-semibold text-on-primary group-hover:bg-primary-700 transition-colors shadow-sm">Mulai Kuis</span>
                                        @endif
                                    </div>
                            @if($lvl->is_locked)
                                </div>
                            @else
                                </a>
                            @endif
                        @empty
                            <div class="lg:col-span-2 bg-surface-container-lowest rounded-2xl p-10 text-center border border-gray-100 shadow-sm text-gray-500">
                                <span class="material-symbols-outlined text-[48px] text-gray-400">layers</span>
                                <p class="font-heading text-heading font-bold text-on-surface mt-2">Belum ada level materi yang tersedia.</p>
                                <p class="font-body text-body mt-1">Hubungi bapak/ibu guru untuk menambahkan level dan soal baru.</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                <!-- LATIHAN NGOMONG: latihan bebas tanpa EXP -->
                <section class="flex flex-col gap-space-sm">
                    <span class="font-label-upper text-label-upper tracking-wider text-gray-500 uppercase font-bold">LATIHAN BEBAS</span>
                    <div class="flex flex-col gap-4 rounded-[24px] bg-secondary-fixed/30 p-6 shadow-sm border border-secondary/30 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-start gap-4">
                            <span class="material-symbols-outlined text-[36px] text-secondary">record_voice_over</span>
                            <div>
                                <h3 class="font-heading text-heading font-bold text-on-surface">Latihan Ngomong</h3>
                                <p class="font-body text-body text-on-surface-variant mt-1 leading-relaxed max-w-xl">
                                    Ucapkan kalimat contoh, lalu dapatkan masukan pelafalan dari AI. Cocok untuk latihan bebas —
                                    <strong>tidak memengaruhi EXP maupun skor</strong>.
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('kuis.latihan-ngomong') }}" class="inline-flex shrink-0 items-center justify-center gap-space-sm rounded-full bg-secondary px-6 py-3 font-body text-body font-bold text-on-secondary shadow-sm transition-colors hover:opacity-90">
                            <span class="material-symbols-outlined text-[18px]">mic</span>
                            <span>Mulai Latihan Ngomong</span>
                        </a>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>