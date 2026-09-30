@php
    $levelNama = $soal['level']['nama'] ?? null;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.kuis-head', ['judul' => $judul])
</head>
<body class="bg-gray-50 font-body text-on-surface antialiased min-h-screen flex flex-col">
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-xl border-b border-gray-100 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 md:px-6 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('siswa.dashboard') }}" aria-label="Metu saka gladhen"
                class="w-10 h-10 rounded-full flex items-center justify-center bg-surface-container hover:bg-surface-container-high transition-colors text-on-surface-variant shrink-0">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </a>

            <div class="flex-1 max-w-lg mx-2 flex items-center gap-3">
                <div class="w-full h-3.5 bg-surface-container-highest rounded-full overflow-hidden">
                    <div class="h-full bg-green-500 rounded-full transition-all duration-500"
                        style="width: {{ isset($progress['nomor'], $progress['total']) && $progress['total'] > 0 ? round(($progress['nomor'] / $progress['total']) * 100) : 40 }}%;"></div>
                </div>
                <span class="font-caption text-caption text-gray-500 font-semibold whitespace-nowrap">
                    {{ $progress['nomor'] ?? 1 }}/{{ $progress['total'] ?? 1 }}
                </span>
            </div>

            <button id="btn-suara" type="button" aria-label="Pilih swara"
                class="w-10 h-10 rounded-full bg-surface-container text-primary-700 hover:bg-surface-container-high transition-colors flex items-center justify-center shrink-0">
                <span id="ikon-suara" class="material-symbols-outlined text-[20px]">volume_up</span>
            </button>
        </div>
    </header>

    <main class="flex-1 w-full max-w-3xl mx-auto px-3 sm:px-4 md:px-6 py-3 sm:py-6 md:py-8 flex flex-col justify-start sm:justify-center items-center">
        @yield('konten')
    </main>

    @include('partials.kuis-runtime')
    <script>
        (function () {
            const btn = document.getElementById('btn-suara');
            const ikon = document.getElementById('ikon-suara');
            const gambar = () => { if (ikon) ikon.textContent = window.KuisFx.muted ? 'volume_off' : 'volume_up'; };
            gambar();
            btn?.addEventListener('click', () => { window.KuisFx.toggle(); gambar(); });
        })();
    </script>
    @stack('skrip')
    @include('partials.siswa-sound')
</body>
</html>
