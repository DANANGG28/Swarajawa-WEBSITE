<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.kuis-head', ['judul' => $judul])
    <style>
        .toast-slide {
            transition: transform 0.45s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.45s ease;
        }
        .toast-hidden {
            transform: translateX(130%);
            opacity: 0;
        }
        .toast-visible {
            transform: translateX(0);
            opacity: 1;
        }
        @keyframes progressDrain {
            from { width: 100%; }
            to { width: 0%; }
        }
        .progress-animate {
            animation: progressDrain 4s linear forwards;
        }
        .progress-animate-error {
            animation: progressDrain 5s linear forwards;
        }
    </style>
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
            @yield('konten')
        </main>
    </div>

    {{-- Floating Toast Notifications di Pojok Kanan Bawah (Slide In dari Kanan & Slide Out ke Kanan) --}}
    <div id="toastContainer" class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none max-w-sm w-full">
        @if (session('sukses'))
            <div id="toastNotification"
                 class="pointer-events-auto bg-surface-container-lowest border border-green-500/40 rounded-2xl p-4 shadow-[0_10px_35px_rgba(0,0,0,0.15)] flex items-start gap-3.5 toast-slide toast-hidden overflow-hidden relative">
                <div class="w-10 h-10 rounded-xl bg-green-500/15 text-green-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined icon-fill text-[24px]">check_circle</span>
                </div>
                <div class="flex-1 min-w-0 pt-0.5">
                    <h5 class="font-heading text-sm font-bold text-green-700 leading-tight">Berhasil</h5>
                    <p class="font-body text-xs text-on-surface-variant mt-0.5 leading-relaxed">{{ session('sukses') }}</p>
                </div>
                <button type="button" onclick="dismissToast('toastNotification')" title="Tutup Notifikasi" class="w-7 h-7 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-700 flex items-center justify-center transition-colors shrink-0">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-green-500/20">
                    <div id="toastProgressBar" class="h-full bg-green-500 w-full"></div>
                </div>
            </div>
        @endif

        @if (session('error') || $errors->any())
            <div id="toastErrorNotification"
                 class="pointer-events-auto bg-surface-container-lowest border border-error/40 rounded-2xl p-4 shadow-[0_10px_35px_rgba(0,0,0,0.15)] flex items-start gap-3.5 toast-slide toast-hidden overflow-hidden relative">
                <div class="w-10 h-10 rounded-xl bg-error/15 text-error flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined icon-fill text-[24px]">error</span>
                </div>
                <div class="flex-1 min-w-0 pt-0.5">
                    <h5 class="font-heading text-sm font-bold text-error leading-tight">Terjadi Kesalahan</h5>
                    <p class="font-body text-xs text-on-surface-variant mt-0.5 leading-relaxed">{{ session('error') ?? $errors->first() }}</p>
                </div>
                <button type="button" onclick="dismissToast('toastErrorNotification')" title="Tutup Notifikasi" class="w-7 h-7 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-700 flex items-center justify-center transition-colors shrink-0">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-error/20">
                    <div id="toastErrorProgressBar" class="h-full bg-error w-full"></div>
                </div>
            </div>
        @endif
    </div>

    <script>
        function showToast(id, duration = 4000) {
            const toast = document.getElementById(id);
            if (!toast) return;

            // Slide in dari kanan ke kiri
            setTimeout(() => {
                toast.classList.remove('toast-hidden');
                toast.classList.add('toast-visible');

                const bar = toast.querySelector('[id$="ProgressBar"]');
                if (bar) {
                    if (id === 'toastNotification') {
                        bar.classList.add('progress-animate');
                    } else {
                        bar.classList.add('progress-animate-error');
                    }
                }
            }, 80);

            // Slide out kembali ke kanan setelah durasi selesai
            setTimeout(() => {
                dismissToast(id);
            }, duration);
        }

        function dismissToast(id) {
            const toast = document.getElementById(id);
            if (!toast) return;

            // Slide out kembali ke kanan
            toast.classList.remove('toast-visible');
            toast.classList.add('toast-hidden');

            setTimeout(() => {
                toast.remove();
            }, 500);
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (document.getElementById('toastNotification')) {
                showToast('toastNotification', 4000);
            }
            if (document.getElementById('toastErrorNotification')) {
                showToast('toastErrorNotification', 5000);
            }
        });
    </script>
</body>
</html>
