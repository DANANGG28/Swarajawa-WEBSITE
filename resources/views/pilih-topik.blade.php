<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Topik - Sinau Jowo Web</title>
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
              "surface-container-lowest": "#ffffff",
              "surface-container-high": "#e9e6f7",
              "on-surface": "#1a1a26",
              "on-surface-variant": "#474554",
              "on-primary": "#ffffff",
              "primary": "#3c25b1",
              "primary-600": "#6C5CE8",
              "primary-700": "#5443C9",
              "error": "#ba1a1a",
              "error-container": "#ffdad6",
              "green-500": "#4CAF6D",
              "gray-50": "#F7F7FA",
              "gray-200": "#E4E4EC",
              "gray-500": "#8A8A9A"
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
        .sj-app { background-color: #f7f3ff; }
        .sj-card { box-shadow: 0 4px 0 rgba(15, 23, 42, 0.07), 0 2px 4px rgba(15, 23, 42, 0.05); }
        .sj-card-hover:hover { box-shadow: 0 6px 0 rgba(15, 23, 42, 0.09), 0 3px 10px rgba(15, 23, 42, 0.06); }
    </style>
</head>
<body class="bg-[#f7f3ff] antialiased text-slate-900">
    <x-sidebar active="beranda" />

    <div class="pl-0 lg:pl-72 min-h-screen pb-24 lg:pb-8 sj-app">
        <div class="max-w-3xl mx-auto px-4 sm:px-8 py-6 sm:py-7">
            <!-- Header -->
            <div class="flex items-center gap-3 mb-6">
                <a href="{{ route('siswa.dashboard') }}" title="Bali menyang beranda"
                   class="w-10 h-10 rounded-xl border-2 border-slate-200 text-slate-500 hover:text-brand-600 hover:border-brand-300 flex items-center justify-center shrink-0 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 24 24">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                </a>
                <div class="min-w-0">
                    <h1 class="text-2xl font-black text-slate-900 leading-tight">Pilih Topik</h1>
                    <p class="text-sm font-bold text-slate-500">Pilih bagian pasinaon sing arep disinaoni.</p>
                </div>
            </div>

            <div class="space-y-5">
                <!-- Kembali (paling atas) -->
                <a href="{{ route('siswa.dashboard') }}"
                   class="block bg-white rounded-[28px] p-5 border-2 border-slate-200 sj-card sj-card-hover transition-all active:scale-[0.99]">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 24 24">
                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                <polyline points="12 19 5 12 12 5"></polyline>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <div class="text-lg font-black text-slate-900">Kembali</div>
                            <div class="text-xs font-bold text-slate-500">Bali menyang topik sadurunge (ora ngganti topik).</div>
                        </div>
                    </div>
                </a>

                {{-- Dhaptar topik --}}
                @forelse($topikCards as $topik)
                    @php $aktif = $topik->id === $topikAktifId; @endphp
                    <section class="bg-white rounded-[28px] p-6 border-2 border-slate-200 sj-card sj-card-hover relative overflow-hidden">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <span class="text-[11px] font-black uppercase tracking-widest text-brand-600">Bagian {{ $topik->urutan }}</span>
                                <h2 class="text-2xl font-black text-slate-900 leading-tight mt-0.5">{{ $topik->nama }}</h2>
                                @if($topik->deskripsi)
                                    <p class="text-sm font-bold text-slate-500 mt-1 leading-relaxed">{{ $topik->deskripsi }}</p>
                                @endif
                            </div>
                            @if($aktif)
                                <span class="shrink-0 text-[10px] font-black uppercase tracking-wider bg-brand-600 text-white px-3 py-1 rounded-full shadow-sm">Aktif</span>
                            @endif
                        </div>

                        <div class="mt-4">
                            <div class="flex items-center justify-between text-xs font-bold text-slate-500 mb-1.5">
                                <span>{{ $topik->total_unit }} Unit &middot; {{ $topik->lulus_count }}/{{ $topik->total_soal }} soal</span>
                                <span class="font-black text-brand-600">{{ $topik->persen }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                                <div class="bg-emerald-500 h-3 rounded-full transition-all" style="width: {{ $topik->persen }}%"></div>
                            </div>
                        </div>

                        <a href="{{ route('siswa.topik.pilih', $topik->id) }}"
                           class="mt-5 inline-flex items-center justify-center gap-2 w-full py-3 px-4 rounded-2xl text-sm font-black transition-all active:scale-95 {{ $aktif ? 'bg-brand-600 hover:bg-brand-700 text-white shadow-md shadow-brand-600/30' : 'bg-white border-2 border-brand-200 text-brand-600 hover:bg-brand-50' }}">
                            <span>{{ $aktif ? 'Lanjutkan' : 'Pilih Topik Iki' }}</span>
                        </a>
                    </section>
                @empty
                    <div class="bg-white rounded-[28px] p-8 text-center text-slate-500 font-bold border-2 border-slate-200 sj-card">
                        Belum ana topik. Hubungi guru utawa admin.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    @include('partials.siswa-sound')
</body>
</html>
