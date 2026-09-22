@php
    $levelNama = $soal['level']['nama'] ?? null;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.kuis-head', ['judul' => $judul])
</head>
<body class="bg-surface-container-lowest font-body text-on-surface antialiased min-h-screen flex flex-col">
    <header class="sticky top-0 z-50 bg-surface-container-lowest/90 backdrop-blur-xl border-b border-gray-200">
        <div class="max-w-3xl mx-auto px-4 md:px-6 h-16 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('siswa.dashboard') }}" aria-label="Metu saka gladhen"
                    class="w-10 h-10 rounded-full flex items-center justify-center text-gray-500 hover:bg-surface-container-high transition-colors shrink-0">
                    <span class="material-symbols-outlined text-[22px]">close</span>
                </a>
                <div class="min-w-0">
                    <h1 class="font-heading text-base font-bold text-on-surface leading-tight truncate">Latihan Wicara Mandiri</h1>
                    <p class="font-caption text-xs text-gray-500 truncate">
                        Kalimat {{ $progress['nomor'] ?? 0 }} saka {{ $progress['total'] ?? 0 }}<span class="mx-1">•</span>{{ $levelNama ?? $judul }}
                    </p>
                </div>
            </div>

            <button id="btn-suara" type="button" aria-label="Pilih swara"
                class="w-10 h-10 rounded-full bg-surface-container text-primary-700 hover:bg-surface-container-high transition-colors flex items-center justify-center shrink-0">
                <span id="ikon-suara" class="material-symbols-outlined text-[22px]">volume_up</span>
            </button>
        </div>
    </header>

    <main class="flex-1 w-full max-w-3xl mx-auto px-4 md:px-6 py-6 md:py-8">
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
