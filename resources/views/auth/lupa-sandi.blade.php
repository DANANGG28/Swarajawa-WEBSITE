<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.kuis-head', ['judul' => 'Lupa Kata Sandi'])
    <style>
        .bg-dot-pattern {
            background-color: #fcf8ff;
            background-image: radial-gradient(#dcd7f5 1.2px, transparent 1.2px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="bg-dot-pattern font-body text-on-surface antialiased min-h-screen flex flex-col justify-between">
    <nav aria-label="Navigasi Atas" class="w-full px-6 py-5 sm:px-10 flex items-center z-10">
        <a href="{{ route('masuk') }}" aria-label="Kembali ke halaman masuk"
            class="inline-flex items-center justify-center w-11 h-11 rounded-full text-gray-500 hover:text-black-900 hover:bg-white/80 border border-transparent hover:border-gray-200 transition-colors">
            <span class="material-symbols-outlined text-[26px]">arrow_back</span>
        </a>
    </nav>

    <main class="flex-1 flex items-center justify-center px-4 py-6 sm:py-10">
        <div class="w-full max-w-md bg-surface-container-lowest rounded-3xl border border-gray-200 p-6 sm:p-8 shadow-md relative overflow-hidden">
            <div aria-hidden="true" class="absolute -top-12 -right-12 w-64 h-64 rounded-full bg-primary-400/20 blur-3xl pointer-events-none"></div>

            <div class="relative flex flex-col items-center text-center">
                <div class="w-14 h-14 mb-3 rounded-2xl bg-primary-600 flex items-center justify-center text-white shadow-md">
                    <span class="material-symbols-outlined text-[30px]">lock_reset</span>
                </div>
                <h1 class="font-display text-2xl font-bold text-black-900 tracking-tight mb-2">Lupa Kata Sandi?</h1>
                <p class="font-body text-body text-on-surface-variant leading-relaxed max-w-xs mx-auto">
                    Masukkan alamat email yang terdaftar. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi akun Anda.
                </p>
            </div>

            @if ($errors->any())
                <div class="relative mt-6 px-4 py-3 rounded-2xl bg-error-container text-on-error-container font-body text-body">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('status'))
                <div class="relative mt-6 flex flex-col items-center text-center p-4 bg-surface-container-low rounded-2xl" id="success-state">
                    <div class="w-10 h-10 rounded-full bg-green-500/15 text-green-500 flex items-center justify-center mb-2">
                        <span class="material-symbols-outlined text-[22px]">check</span>
                    </div>
                    <p class="font-heading text-heading font-bold text-black-900 mb-1">Tautan Telah Dikirim</p>
                    <p class="font-caption text-caption text-gray-500 mb-4">
                        Instruksi atur ulang kata sandi sudah dikirim ke email Anda. Periksa juga folder Spam atau Promosi.
                    </p>
                    <form method="POST" action="{{ route('lupa-sandi.kirim') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ old('email') }}">
                        <button type="submit" class="font-caption text-caption font-bold text-primary-700 hover:underline">
                            Kirim ulang tautan
                        </button>
                    </form>
                </div>
            @else
                <form method="POST" action="{{ route('lupa-sandi.kirim') }}" class="relative mt-6 flex flex-col gap-4" id="reset-form">
                    @csrf
                    <div class="flex flex-col gap-1.5">
                        <label for="email" class="font-label-upper text-label-upper text-gray-500 uppercase tracking-wider">Email Siswa</label>
                        <div class="relative flex items-center">
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required maxlength="255"
                                placeholder="nama@sekolah.sch.id" autofocus
                                class="w-full bg-gray-50 text-black-900 font-body text-body rounded-xl px-4 py-3.5 pr-11 outline-none transition-all placeholder:text-gray-500/70 focus:bg-white focus:shadow-[0_0_0_2px_#5443C9]">
                            <span class="absolute right-3.5 text-gray-500 pointer-events-none flex items-center">
                                <span class="material-symbols-outlined text-[20px]">mail</span>
                            </span>
                        </div>
                    </div>

                    <div class="bg-surface-container rounded-xl p-3 flex items-start gap-2.5">
                        <span class="material-symbols-outlined text-[18px] text-primary-700 shrink-0 mt-0.5">info</span>
                        <p class="font-caption text-caption text-on-surface-variant leading-snug">
                            Periksa folder Spam atau Promosi jika Anda tidak menerima email pemulihan dalam 5 menit.
                        </p>
                    </div>

                    <button type="submit"
                        class="w-full bg-primary-600 hover:bg-primary-700 text-white font-body text-body font-bold tracking-wide py-3.5 px-6 rounded-2xl shadow-sm transition-all active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer">
                        <span class="material-symbols-outlined text-[20px]">send</span>
                        <span>Kirim Tautan Reset</span>
                    </button>
                </form>
            @endif

            <div class="relative text-center pt-6">
                <p class="font-body text-body text-gray-500">
                    Ingat kata sandi Anda?
                    <a href="{{ route('masuk') }}" class="font-bold text-primary-700 hover:text-primary hover:underline ml-1">Masuk di sini</a>
                </p>
            </div>
        </div>
    </main>

    <footer class="w-full py-4 text-center text-xs text-gray-500">
        <p>&copy; 2025 Sinau Jowo. Hak Cipta Dilindungi. Pembelajaran Bahasa &amp; Budaya Jawa.</p>
    </footer>
</body>
</html>
