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
                <!-- HERO HEADER BANNER -->
                <section class="relative overflow-hidden rounded-[24px] bg-gradient-to-r from-primary-700 via-primary-600 to-primary text-on-primary p-8 shadow-md">
                    <div class="relative z-10 flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-space-xs font-label-upper text-label-upper tracking-wider text-yellow-300 uppercase font-bold">
                                <svg class="h-4 w-4 fill-current text-yellow-300" viewBox="0 0 24 24">
                                    <path d="M12 2l2.4 7.4h7.6l-6.2 4.5 2.4 7.4-6.2-4.5-6.2 4.5 2.4-7.4-6.2-4.5h7.6z"></path>
                                </svg>
                                <span>PAPAN SKOR &amp; KATALIS PEMBELAJARAN • {{ $scope === 'sekolah' ? 'PERINGKAT SEKOLAH' : ($kelasSiswa ? 'KELAS '.$kelasSiswa : 'SEMUA KELAS') }}</span>
                            </div>
                        </div>

                        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
                            <div class="max-w-2xl">
                                <h1 class="font-display text-display text-on-primary font-bold text-2xl lg:text-3xl mb-2">
                                    Papan Peringkat Siswa Bahasa Jawa
                                </h1>
                                <p class="font-body text-body text-primary-fixed leading-relaxed">
                                    Lihat capaian poin (XP), streak pembelajaran, dan urutan belajar siswa berdasarkan catatan aktif di sistem.
                                </p>
                            </div>

                            <!-- Opsi Tampilan: Kelas / Sekolah -->
                            <div class="flex flex-col items-start lg:items-end gap-3">
                                <div class="inline-flex p-1 bg-white/10 backdrop-blur-md rounded-xl border border-white/15 gap-1">
                                    <a href="{{ route('siswa.papan-skor') }}" class="px-4 py-2 rounded-lg text-caption font-bold transition-all {{ $scope === 'kelas' ? 'bg-white text-primary-700 shadow-sm' : 'text-primary-fixed hover:text-white' }}">
                                        Kelas {{ $kelasSiswa ?? '-' }}
                                    </a>
                                    <a href="{{ route('siswa.papan-skor', ['scope' => 'sekolah']) }}" class="px-4 py-2 rounded-lg text-caption font-bold transition-all {{ $scope === 'sekolah' ? 'bg-white text-primary-700 shadow-sm' : 'text-primary-fixed hover:text-white' }}">
                                        Sekolah
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- JUARA 1 SEKOLAH -->
                @if($scope === 'kelas' && $juaraSekolah)
                    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-amber-500 to-amber-400 text-white p-5 shadow-sm flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">emoji_events</span>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="font-label-upper text-label-upper uppercase tracking-wider text-white/80 font-bold">Juara 1 Sekolah</span>
                                <span class="font-heading text-heading font-extrabold truncate">{{ $juaraSekolah['nama'] }}</span>
                                <span class="font-caption text-caption text-white/80">{{ $juaraSekolah['kelas'] ? 'Kelas '.$juaraSekolah['kelas'] : 'Siswa' }}</span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="font-heading text-2xl font-black leading-none">{{ number_format($juaraSekolah['total_exp']) }}</span>
                            <span class="font-label-upper text-label-upper font-bold text-white/80 block mt-0.5">XP</span>
                        </div>
                    </section>
                @endif

                <!-- PODIUM TOP 3 SECTION -->
                <section class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col gap-6">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-primary-500/10 text-primary-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 fill-none stroke-currentColor stroke-2" viewBox="0 0 24 24">
                                    <path d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </div>
                            <h2 class="font-heading text-heading font-bold text-on-surface">Tiga Besar (Top 3)</h2>
                        </div>
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-semibold">{{ $scope === 'sekolah' ? 'SEKOLAH' : 'KELAS '.($kelasSiswa ?? 'SEMUA') }}</span>
                    </div>

                    <!-- 3 Pillar Podium Grid (Align Bottom) -->
                    <div class="grid grid-cols-3 gap-4 md:gap-6 items-end pt-4 pb-2">
                        <!-- RANK 2: LEFT -->
                        <div class="flex flex-col items-center text-center">
                            @if($top2)
                                @php
                                    $in2 = mb_strtoupper(mb_substr($top2['nama'], 0, 2));
                                    $isMe2 = $top2['siswa_id'] === $siswa->id;
                                @endphp
                                <div class="flex flex-col items-center mb-3">
                                    <div class="w-16 h-16 rounded-full p-1 bg-orange-300/40 shadow-sm relative mb-2">
                                        <div class="w-full h-full rounded-full bg-primary-700 text-white font-bold text-lg flex items-center justify-center">
                                            {{ $in2 }}
                                        </div>
                                    </div>
                                    <span class="font-heading text-body font-bold text-on-surface leading-tight">{{ $top2['nama'] }}</span>
                                    @if($isMe2)
                                        <span class="font-caption text-caption font-bold text-primary-600">(Anda)</span>
                                    @else
                                        <span class="font-caption text-caption text-gray-500">{{ $top2['kelas'] ? 'Kelas '.$top2['kelas'] : 'Siswa' }}</span>
                                    @endif
                                    <span class="font-heading text-heading text-primary-700 font-extrabold mt-0.5">{{ number_format($top2['total_exp']) }} XP</span>
                                </div>
                            @else
                                <div class="flex flex-col items-center mb-3 text-gray-400 font-caption">Belum ada</div>
                            @endif
                            <!-- Balok Pillar Rank 2 -->
                            <div class="w-full h-36 bg-[#F7B98A] rounded-t-2xl flex flex-col justify-between items-center py-4 shadow-sm border-t-2 border-white/40">
                                <span class="text-white font-label-upper text-label-upper font-bold uppercase tracking-widest">Runner-Up</span>
                                <span class="font-heading text-5xl leading-none text-white font-black drop-shadow-sm">2</span>
                                <div class="w-8 h-1 bg-white/40 rounded-full"></div>
                            </div>
                        </div>

                        <!-- RANK 1: CENTER -->
                        <div class="flex flex-col items-center text-center">
                            @if($top1)
                                @php
                                    $in1 = mb_strtoupper(mb_substr($top1['nama'], 0, 2));
                                    $isMe1 = $top1['siswa_id'] === $siswa->id;
                                @endphp
                                <div class="flex flex-col items-center mb-3">
                                    <div class="text-[#F0955A] mb-1">
                                        <svg class="w-7 h-7 fill-[#F6D98B] stroke-[#623814]/30 stroke-1" viewBox="0 0 24 24">
                                            <path d="M5 16L3 5L8.5 10L12 4L15.5 10L21 5L19 16H5M19 19C19 19.6 18.6 20 18 20H6C5.4 20 5 19.6 5 19V18H19V19Z"></path>
                                        </svg>
                                    </div>
                                    <div class="w-20 h-20 rounded-full p-1 bg-yellow-300 shadow-md relative mb-2 ring-4 ring-yellow-300/30">
                                        <div class="w-full h-full rounded-full bg-amber-600 text-white font-bold text-xl flex items-center justify-center">
                                            {{ $in1 }}
                                        </div>
                                    </div>
                                    <span class="font-heading text-heading font-extrabold text-on-surface leading-tight">{{ $top1['nama'] }}</span>
                                    @if($isMe1)
                                        <span class="font-caption text-caption font-bold text-primary-600">(Anda)</span>
                                    @else
                                        <span class="font-caption text-caption text-gray-500">{{ $top1['kelas'] ? 'Kelas '.$top1['kelas'] : 'Siswa' }}</span>
                                    @endif
                                    <span class="font-heading text-heading text-primary font-black mt-0.5">{{ number_format($top1['total_exp']) }} XP</span>
                                </div>
                            @else
                                <div class="flex flex-col items-center mb-3 text-gray-400 font-caption">Belum ada</div>
                            @endif
                            <!-- Balok Pillar Rank 1 -->
                            <div class="w-full h-48 bg-[#5443C9] rounded-t-2xl flex flex-col justify-between items-center py-4 shadow-md border-t-2 border-yellow-300">
                                <span class="text-yellow-300 font-label-upper text-label-upper font-extrabold uppercase tracking-widest">Juara 1</span>
                                <span class="font-heading text-6xl leading-none text-white font-black drop-shadow-sm">1</span>
                                <div class="w-10 h-1 bg-yellow-300/60 rounded-full"></div>
                            </div>
                        </div>

                        <!-- RANK 3: RIGHT -->
                        <div class="flex flex-col items-center text-center">
                            @if($top3)
                                @php
                                    $in3 = mb_strtoupper(mb_substr($top3['nama'], 0, 2));
                                    $isMe3 = $top3['siswa_id'] === $siswa->id;
                                @endphp
                                <div class="flex flex-col items-center mb-3">
                                    <div class="w-16 h-16 rounded-full p-1 bg-yellow-300/40 shadow-sm relative mb-2">
                                        <div class="w-full h-full rounded-full bg-amber-500 text-white font-bold text-lg flex items-center justify-center">
                                            {{ $in3 }}
                                        </div>
                                    </div>
                                    <span class="font-heading text-body font-bold text-on-surface leading-tight">{{ $top3['nama'] }}</span>
                                    @if($isMe3)
                                        <span class="font-caption text-caption font-bold text-primary-600">(Anda)</span>
                                    @else
                                        <span class="font-caption text-caption font-medium text-gray-500">{{ $top3['kelas'] ? 'Kelas '.$top3['kelas'] : 'Siswa' }}</span>
                                    @endif
                                    <span class="font-heading text-heading text-primary-700 font-extrabold mt-0.5">{{ number_format($top3['total_exp']) }} XP</span>
                                </div>
                            @else
                                <div class="flex flex-col items-center mb-3 text-gray-400 font-caption">Belum ada</div>
                            @endif
                            <!-- Balok Pillar Rank 3 -->
                            <div class="w-full h-28 bg-[#F6D98B] rounded-t-2xl flex flex-col justify-between items-center py-4 shadow-sm border-t-2 border-white/40">
                                <span class="text-white font-label-upper text-label-upper font-bold uppercase tracking-widest">Peringkat 3</span>
                                <span class="font-heading text-4xl leading-none text-white font-black drop-shadow-sm">3</span>
                                <div class="w-8 h-1 bg-white/40 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- DAFTAR PERINGKAT SISWA SABANJURE (Rank 4 - 8) -->
                <section class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col gap-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-100 pb-4">
                        <div>
                            <h3 class="font-heading text-heading font-bold text-on-surface">Daftar Peringkat Siswa Selanjutnya</h3>
                            <p class="font-caption text-caption text-gray-500">Peringkat {{ $scope === 'sekolah' ? 'sekolah' : 'kelas '.$kelasSiswa }} berdasarkan total XP</p>
                        </div>
                        <div class="inline-flex items-center gap-2 bg-gray-50 border border-gray-200/80 px-3 py-1.5 rounded-full self-start sm:self-auto">
                            <svg class="w-4 h-4 text-gray-500 stroke-2 fill-none stroke-currentColor" viewBox="0 0 24 24">
                                <path d="M3 4h18M7 12h10m-7 8h4" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            <span class="font-caption text-caption font-semibold text-on-surface-variant">Urutkan: Total XP</span>
                        </div>
                    </div>

                    <!-- List Data Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-gray-500 font-label-upper text-label-upper font-bold uppercase tracking-wider border-b border-gray-100">
                                    <th class="py-3 px-3">Peringkat</th>
                                    <th class="py-3 px-3">Siswa</th>
                                    <th class="py-3 px-3">Kelas</th>
                                    <th class="py-3 px-3 text-right">Total EXP</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100/80 font-body text-body">
                                @forelse($others as $item)
                                    @php
                                        $init = mb_strtoupper(mb_substr($item['nama'], 0, 2));
                                        $isMe = $item['siswa_id'] === $siswa->id;
                                    @endphp
                                    <tr class="hover:bg-surface-container-low/40 transition-colors {{ $isMe ? 'bg-primary-500/10' : '' }}">
                                        <td class="py-3.5 px-3">
                                            <span class="w-7 h-7 rounded-full bg-gray-100 font-heading font-bold text-gray-700 flex items-center justify-center text-caption">{{ $item['peringkat'] }}</span>
                                        </td>
                                        <td class="py-3.5 px-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full bg-primary-700 text-white font-bold text-xs flex items-center justify-center">
                                                    {{ $init }}
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="font-bold text-on-surface">{{ $item['nama'] }} {{ $isMe ? '(Anda)' : '' }}</span>
                                                    <span class="font-caption text-caption text-gray-500">Siswa Aktif</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-3 font-semibold text-gray-600">{{ $item['kelas'] ?? '-' }}</td>
                                        <td class="py-3.5 px-3 font-heading font-extrabold text-primary-700 text-right">{{ number_format($item['total_exp']) }} XP</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-gray-500">Belum ada siswa lainnya di papan skor.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Kartu Highlight Personal (Posisi Sampeyan) -->
                    <div class="mt-2 p-4 rounded-2xl bg-primary-500/10 border border-primary-500/20 flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <span class="w-8 h-8 rounded-full bg-primary-600 text-white font-heading font-bold flex items-center justify-center text-caption shadow-sm">{{ $myRank }}</span>
                            <div class="w-10 h-10 rounded-full bg-primary-700 text-white font-bold text-sm flex items-center justify-center border border-primary-400">
                                {{ mb_strtoupper(mb_substr($siswa->nama_lengkap, 0, 2)) }}
                            </div>
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2">
                                    <span class="font-heading font-bold text-on-surface text-body">{{ $siswa->nama_lengkap }} (Posisi Anda)</span>
                                    <span class="font-label-upper text-label-upper font-bold bg-primary-600 text-white px-2 py-0.5 rounded-full">Anda</span>
                                </div>
                                <span class="font-caption text-caption text-gray-500">{{ $siswa->kelas ? 'Kelas '.$siswa->kelas : 'Siswa' }} • Streak {{ $myStreak }} Hari</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 w-full md:w-auto justify-between md:justify-end">
                            <div class="flex flex-col text-right">
                                <span class="font-heading font-black text-primary-700 text-lg">{{ number_format($myExp) }} XP</span>
                                <span class="font-caption text-caption font-semibold text-green-500">Peringkat #{{ $myRank }}</span>
                            </div>
                            <a href="{{ route('siswa.dashboard') }}" class="px-5 py-2 rounded-full bg-primary-600 text-white font-heading font-bold text-caption hover:bg-primary-700 transition-colors shadow-sm">
                                Tambah EXP
                            </a>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>