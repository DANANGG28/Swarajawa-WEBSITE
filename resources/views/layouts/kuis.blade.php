<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.kuis-head', ['judul' => $judul])
</head>
<body class="bg-background font-body text-on-surface antialiased min-h-screen flex flex-col">
    @include('partials.kuis-header', ['judul' => $judul, 'header' => $header, 'soal' => $soal, 'progress' => $progress])

    <main class="flex-1 w-full max-w-6xl mx-auto px-4 md:px-6 py-6">
        @yield('konten')
    </main>

    <footer class="sticky bottom-0 z-40 bg-surface-container-lowest border-t border-gray-200 shadow-[0_-4px_20px_rgba(0,0,0,0.05)]">
        <div class="max-w-6xl mx-auto px-4 md:px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="font-caption text-caption text-gray-500">© 2026 Sinau Jowo <span class="mx-1">•</span> Semua Hak Dilindungi</div>
            <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                @yield('aksi')
            </div>
        </div>
    </footer>

    @include('partials.kuis-runtime')
    @stack('skrip')
    @include('partials.siswa-sound')
</body>
</html>
