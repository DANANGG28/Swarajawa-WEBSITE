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

    <script>
        window.KUIS = {
            csrf: document.querySelector('meta[name="csrf-token"]')?.content ?? null,
            jawabUrl: @json(route('kuis.jawab')),
            ttsUrl: @json(route('kuis.tts')),
            sttUrl: @json(route('kuis.stt')),
            stsUrl: @json(route('kuis.sts')),
        };
        window.postJSON = async function (url, body) {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.KUIS.csrf,
                },
                credentials: 'same-origin',
                body: JSON.stringify(body),
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                throw Object.assign(new Error(data.message || 'Gagal mengirim data.'), { data, status: res.status });
            }
            return data;
        };
    </script>
    @stack('skrip')
</body>
</html>
