<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.kuis-head', ['judul' => $judul])
</head>
<body class="bg-background font-body text-on-surface antialiased">
    <x-admin-sidebar :role="$role" :active="$active" />

    <div class="pl-72 flex flex-col min-h-screen">
        <header class="fixed top-0 left-72 right-0 h-20 bg-surface-container-lowest/90 backdrop-blur-xl z-40 shadow-[0_1px_8px_rgba(0,0,0,0.04)] px-space-xl flex items-center justify-between">
            <div class="flex flex-col min-w-0">
                <h1 class="font-display text-display font-extrabold text-on-surface truncate">{{ $judul }}</h1>
                @isset($subjudul)
                    <span class="font-caption text-caption text-gray-500 truncate">{{ $subjudul }}</span>
                @endisset
            </div>
            <div class="flex items-center gap-3 shrink-0">
                @yield('aksi')
            </div>
        </header>

        <main class="flex-1 pt-20 w-full px-margin-desktop py-space-xl bg-background">
            @if (session('sukses'))
                <div class="mb-5 px-5 py-3.5 rounded-2xl bg-green-500/10 text-green-500 font-body text-body font-semibold flex items-center gap-2">
                    <span class="material-symbols-outlined icon-fill text-[20px]">check_circle</span>
                    {{ session('sukses') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 px-5 py-3.5 rounded-2xl bg-error-container text-on-error-container font-body text-body">
                    {{ $errors->first() }}
                </div>
            @endif

            @yield('konten')
        </main>
    </div>
</body>
</html>
