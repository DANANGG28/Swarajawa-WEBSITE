@php
    $persen = ($progress['total'] ?? 0) > 0 ? (int) round((($progress['nomor'] ?? 0) / $progress['total']) * 100) : 0;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.kuis-head', ['judul' => $judul])
</head>
<body class="bg-surface-container-lowest font-body text-on-surface antialiased min-h-screen flex flex-col">
    <header class="sticky top-0 z-50 bg-surface-container-lowest/95 backdrop-blur-xl border-b border-gray-200">
        <div class="max-w-3xl mx-auto px-4 md:px-6 h-16 md:h-20 flex items-center gap-3 md:gap-5">
            <a href="{{ route('siswa.dashboard') }}" aria-label="Metu saka latihan"
                class="w-10 h-10 rounded-full flex items-center justify-center text-gray-500 hover:bg-surface-container-high transition-colors shrink-0">
                <span class="material-symbols-outlined text-[26px]">close</span>
            </a>

            <div class="flex-1 flex items-center gap-3">
                <div class="flex-1 h-3.5 md:h-4 bg-surface-container-high rounded-full overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-primary-600 to-primary-400 transition-all duration-500"
                        style="width: {{ $persen }}%"></div>
                </div>
                <span class="font-caption text-xs font-semibold text-gray-500 whitespace-nowrap">{{ $progress['nomor'] ?? 0 }}/{{ $progress['total'] ?? 0 }}</span>
            </div>

            <button id="btn-suara" type="button" aria-label="Pilih swara"
                class="w-11 h-11 rounded-xl bg-primary-fixed/60 text-primary-700 flex items-center justify-center hover:bg-primary-fixed transition-colors shrink-0">
                <span id="ikon-suara" class="material-symbols-outlined text-[24px]">volume_up</span>
            </button>
        </div>
    </header>

    <main class="flex-1 w-full max-w-3xl mx-auto px-4 md:px-6 py-8 md:py-12">
        @yield('konten')
    </main>

    <footer class="sticky bottom-0 z-40 bg-surface-container-lowest border-t border-gray-200">
        <div class="max-w-3xl mx-auto px-4 md:px-6 py-4 flex items-center justify-between gap-3">
            @yield('aksi')
        </div>
    </footer>

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
