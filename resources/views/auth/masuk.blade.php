<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.kuis-head', ['judul' => 'Masuk'])
    <style>
        .bg-dot-pattern {
            background-color: #fcf8ff;
            background-image: radial-gradient(#dcd7f5 1.2px, transparent 1.2px);
            background-size: 24px 24px;
        }
        .btn-3d {
            box-shadow: 0 4px 0 var(--btn-3d-shadow, #4334a6);
        }
        .btn-3d:active {
            transform: translateY(3px);
            box-shadow: 0 1px 0 var(--btn-3d-shadow, #4334a6);
        }
        .btn-3d-google { --btn-3d-shadow: #d1d5db; }
        .btn-3d-outline { --btn-3d-shadow: #c6bfff; }
    </style>
</head>
<body class="bg-dot-pattern font-body text-on-surface antialiased min-h-screen flex flex-col justify-between">
    <nav aria-label="Navigasi Atas" class="w-full px-6 py-5 sm:px-10 flex items-center justify-between z-10">
        <a href="{{ url('/') }}" aria-label="Tutup halaman masuk"
            class="inline-flex items-center justify-center w-11 h-11 rounded-full text-gray-500 hover:text-black-900 hover:bg-white/80 border border-transparent hover:border-gray-200 transition-colors">
            <span class="material-symbols-outlined text-[26px]">close</span>
        </a>
        <a href="{{ route('daftar') }}"
            class="btn-3d btn-3d-outline inline-flex items-center justify-center px-5 py-2 rounded-full border-2 border-primary-600 text-primary-600 hover:bg-primary-fixed font-bold text-sm tracking-wide uppercase transition-all">
            Daftar
        </a>
    </nav>

    <main class="flex-1 flex items-center justify-center px-4 py-6 sm:py-10">
        <div class="w-full max-w-[460px] bg-surface-container-lowest rounded-3xl border border-gray-200 p-7 sm:p-10 shadow-md"
            data-purpose="kartu-masuk">
            <header class="text-center mb-8">
                <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-primary-600 flex items-center justify-center text-white shadow-md">
                    <span class="material-symbols-outlined text-[30px]">school</span>
                </div>
                <h1 class="font-display text-2xl sm:text-3xl font-extrabold text-black-900 tracking-tight">Masuk</h1>
                <p class="font-caption text-caption text-gray-500 mt-1">Masukkan email dan kata sandi akun Sinau Jowo Anda.</p>
            </header>

            @if (session('sukses'))
                <div class="mb-4 px-4 py-3 rounded-2xl bg-green-500/10 text-green-500 font-body text-body font-semibold">
                    {{ session('sukses') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 px-4 py-3 rounded-2xl bg-error-container text-on-error-container font-body text-body">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('masuk') }}" class="space-y-4" id="form-masuk">
                @csrf
                <div class="space-y-1.5">
                    <label for="email" class="block font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus maxlength="255"
                        autocomplete="username" placeholder="nama@sekolah.sch.id"
                        class="w-full bg-surface-container-low border border-transparent text-black-900 placeholder:text-gray-500/70 text-sm font-medium rounded-2xl px-4 py-3.5 outline-none transition-all focus:bg-white focus:border-primary-600 focus:shadow-[0_0_0_2px_#7B6CF0]">
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Kata Sandi</label>
                        <a href="{{ route('lupa-sandi') }}" class="font-label-upper text-label-upper uppercase tracking-wider font-bold text-primary-600 hover:text-primary-700 transition-colors">Lupa?</a>
                    </div>
                    <div class="relative">
                        <input id="password" type="password" name="password" required maxlength="255"
                            autocomplete="current-password" placeholder="Masukkan kata sandi"
                            class="w-full bg-surface-container-low border border-transparent text-black-900 placeholder:text-gray-500/70 text-sm font-medium rounded-2xl pl-4 pr-12 py-3.5 outline-none transition-all focus:bg-white focus:border-primary-600 focus:shadow-[0_0_0_2px_#7B6CF0]">
                        <button type="button" id="toggle-password" aria-label="Tampilkan kata sandi"
                            class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-gray-500 hover:text-black-900 transition-colors">
                            <span id="eye-icon" class="material-symbols-outlined text-[20px] leading-none">visibility</span>
                        </button>
                    </div>
                </div>

                <label class="flex items-center gap-2 font-caption text-caption text-gray-500 select-none">
                    <input type="checkbox" name="remember" value="1" class="w-4 h-4 rounded accent-primary-600">
                    <span>Ingat saya</span>
                </label>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold text-sm sm:text-base tracking-wider uppercase py-3.5 px-6 rounded-2xl btn-3d transition-all flex items-center justify-center gap-2 cursor-pointer">
                        Masuk
                    </button>
                </div>
            </form>

            <div class="relative my-7 flex items-center justify-center">
                <div aria-hidden="true" class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <span class="relative bg-surface-container-lowest px-4 font-label-upper text-label-upper tracking-widest text-gray-500 uppercase">atau</span>
            </div>

            <a href="{{ route('google.redirect') }}"
                class="btn-3d btn-3d-google w-full bg-white hover:bg-gray-50 border border-gray-200 hover:border-primary-400 text-black-900 font-bold text-sm tracking-wide uppercase py-3.5 px-5 rounded-2xl transition-all flex items-center justify-center gap-3.5">
                @include('partials.google-icon')
                <span>Masuk dengan Google</span>
            </a>

            <footer class="mt-8 space-y-3 text-center text-[11px] text-gray-500 leading-relaxed">
                <p>
                    Dengan masuk ke Sinau Jowo, Anda menyetujui
                    <a href="#" class="font-semibold text-black-900 hover:text-primary-600 underline underline-offset-2">Ketentuan Layanan</a>
                    dan
                    <a href="#" class="font-semibold text-black-900 hover:text-primary-600 underline underline-offset-2">Kebijakan Privasi</a>
                    kami.
                </p>
                <p class="text-gray-500/80">
                    Situs ini dilindungi reCAPTCHA Enterprise dan
                    <a href="#" class="underline hover:text-black-900">Kebijakan Privasi</a> serta
                    <a href="#" class="underline hover:text-black-900">Ketentuan Layanan</a> Google berlaku.
                </p>
                <div class="pt-3 border-t border-gray-200">
                    <p class="font-bold text-gray-500">Guru dan administrator didaftarkan oleh sekolah.</p>
                </div>
            </footer>

            <p class="mt-5 text-center font-caption text-caption text-gray-500">
                Belum punya akun?
                <a href="{{ route('daftar') }}" class="text-primary-600 font-bold hover:underline">Daftar akun siswa</a>
            </p>
        </div>
    </main>

    <footer class="w-full py-4 text-center text-xs text-gray-500">
        <p>&copy; 2025 Sinau Jowo. Platform Pembelajaran Bahasa Jawa Interaktif.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('toggle-password');
            const input = document.getElementById('password');
            const icon = document.getElementById('eye-icon');

            if (toggle && input && icon) {
                toggle.addEventListener('click', function () {
                    const tersembunyi = input.type === 'password';
                    input.type = tersembunyi ? 'text' : 'password';
                    icon.textContent = tersembunyi ? 'visibility_off' : 'visibility';
                    toggle.setAttribute('aria-label', tersembunyi ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
                });
            }
        });
    </script>
</body>
</html>
