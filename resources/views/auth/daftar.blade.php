<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.kuis-head', ['judul' => 'Daftar'])
    <style>
        .bg-dot-pattern {
            background-color: #fcf8ff;
            background-image: radial-gradient(#dcd7f5 1.2px, transparent 1.2px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="bg-dot-pattern font-body text-on-surface antialiased min-h-screen flex flex-col justify-between">
    <nav aria-label="Navigasi Atas" class="w-full px-6 py-2.5 sm:px-8 sm:py-3 flex items-center justify-between z-10">
        <a href="{{ url('/') }}" aria-label="Tutup halaman daftar"
            class="inline-flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-full text-gray-500 hover:text-black-900 hover:bg-white/80 border border-transparent hover:border-gray-200 transition-colors">
            <span class="material-symbols-outlined text-[22px] sm:text-[24px]">close</span>
        </a>
    </nav>

    <main class="flex-1 flex items-center justify-center px-4 py-1.5 sm:py-2.5">
        <div class="relative w-full max-w-[500px] bg-surface-container-lowest rounded-2xl sm:rounded-3xl border border-gray-200 p-5 sm:p-6 shadow-md overflow-hidden">
            <div aria-hidden="true" class="absolute -top-16 -right-16 w-36 h-36 rounded-full bg-primary-fixed opacity-40 blur-2xl pointer-events-none"></div>
            <div aria-hidden="true" class="absolute -bottom-16 -left-16 w-36 h-36 rounded-full bg-secondary-fixed opacity-40 blur-2xl pointer-events-none"></div>

            <header class="relative text-center mb-3 sm:mb-3.5">
                <div class="w-14 h-14 sm:w-16 sm:h-16 mx-auto mb-2 rounded-2xl bg-primary-600 flex items-center justify-center text-white shadow-md p-2 transition-transform hover:scale-105">
                    <img src="{{ route('logo.image', ['filename' => 'logo_tp.png']) }}?v={{ file_exists(storage_path('logo/logo_tp.png')) ? filemtime(storage_path('logo/logo_tp.png')) : (file_exists(storage_path('logo/Logo_TP.png')) ? filemtime(storage_path('logo/Logo_TP.png')) : time()) }}" alt="Logo SINAU APP" class="w-full h-full object-contain drop-shadow-sm">
                </div>
                <h1 class="font-display text-xl sm:text-2xl font-extrabold text-black-900 tracking-tight">Daftar Akun Siswa</h1>
            </header>

            @if (session('sukses'))
                <div class="relative mb-3 px-3.5 py-2 rounded-xl bg-green-500/10 text-green-500 font-body text-xs sm:text-sm font-semibold">
                    {{ session('sukses') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="relative mb-3 px-3.5 py-2 rounded-xl bg-error-container text-on-error-container font-body text-xs sm:text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('daftar') }}" class="relative flex flex-col gap-2.5 sm:gap-3" id="form-daftar">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div class="flex flex-col gap-1">
                        <label for="nama-lengkap" class="font-label-upper text-[11px] text-gray-500 font-semibold tracking-wider uppercase">Nama Lengkap</label>
                        <input id="nama-lengkap" type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required maxlength="255" pattern="^[a-zA-Z\s]+$"
                            title="Nama hanya boleh berisi huruf dan spasi."
                            oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                            placeholder="Contoh: Budi Santoso"
                            class="w-full bg-surface-container-low text-black-900 font-body text-sm placeholder:text-gray-500 rounded-xl px-3.5 py-2 sm:py-2.5 outline-none transition-all focus:bg-white focus:shadow-[0_0_0_2px_#5443C9]">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="nis" class="font-label-upper text-[11px] text-gray-500 font-semibold tracking-wider uppercase">Nomor Induk Siswa (NIS)</label>
                        <input id="nis" type="text" name="nis" value="{{ old('nis') }}" required inputmode="numeric" pattern="[0-9]*" maxlength="10"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            placeholder="Contoh: 2026010042"
                            class="w-full bg-surface-container-low text-black-900 font-body text-sm placeholder:text-gray-500 rounded-xl px-3.5 py-2 sm:py-2.5 outline-none transition-all focus:bg-white focus:shadow-[0_0_0_2px_#5443C9]">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div class="flex flex-col gap-1">
                        <label for="kelas" class="font-label-upper text-[11px] text-gray-500 font-semibold tracking-wider uppercase">Kelas</label>
                        <div class="relative flex items-center">
                            @php $kelasTerpilih = old('kelas', ''); @endphp
                            <select id="kelas" name="kelas"
                                class="w-full bg-surface-container-low text-black-900 font-body text-sm rounded-xl px-3.5 py-2 sm:py-2.5 pr-10 outline-none appearance-none cursor-pointer transition-all focus:bg-white focus:shadow-[0_0_0_2px_#5443C9]">
                                <option value="" disabled {{ $kelasTerpilih === '' ? 'selected' : '' }}>Pilih Kelas</option>
                                @foreach (['7A', '7B', '7C', '8A', '8B', '9A'] as $kelas)
                                    <option value="{{ $kelas }}" {{ $kelasTerpilih === $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
                                @endforeach
                            </select>
                            <span class="material-symbols-outlined pointer-events-none absolute right-3 text-on-surface-variant text-[20px]">expand_more</span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="font-label-upper text-[11px] text-gray-500 font-semibold tracking-wider uppercase">Jenis Kelamin</span>
                        @php $jkTerpilih = old('jenis_kelamin', 'L'); @endphp
                        <div class="grid grid-cols-2 gap-1 h-[38px] sm:h-[40px]">
                            <label class="group relative flex items-center justify-center gap-1.5 px-2 bg-surface-container-low rounded-xl cursor-pointer select-none transition-all hover:bg-surface-container has-[:checked]:bg-primary-fixed has-[:checked]:text-on-primary-fixed font-body text-xs font-semibold text-on-surface-variant">
                                <input class="sr-only" type="radio" name="jenis_kelamin" value="L" {{ $jkTerpilih === 'L' ? 'checked' : '' }}>
                                <span class="material-symbols-outlined text-[16px]">male</span>
                                <span>Laki-laki</span>
                            </label>
                            <label class="group relative flex items-center justify-center gap-1.5 px-2 bg-surface-container-low rounded-xl cursor-pointer select-none transition-all hover:bg-surface-container has-[:checked]:bg-primary-fixed has-[:checked]:text-on-primary-fixed font-body text-xs font-semibold text-on-surface-variant">
                                <input class="sr-only" type="radio" name="jenis_kelamin" value="P" {{ $jkTerpilih === 'P' ? 'checked' : '' }}>
                                <span class="material-symbols-outlined text-[16px]">female</span>
                                <span>Perempuan</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <label for="email" class="font-label-upper text-[11px] text-gray-500 font-semibold tracking-wider uppercase">Email Siswa</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required maxlength="255" placeholder="nama@sekolah.sch.id"
                        class="w-full bg-surface-container-low text-black-900 font-body text-sm placeholder:text-gray-500 rounded-xl px-3.5 py-2 sm:py-2.5 outline-none transition-all focus:bg-white focus:shadow-[0_0_0_2px_#5443C9]">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div class="flex flex-col gap-1">
                        <label for="password" class="font-label-upper text-[11px] text-gray-500 font-semibold tracking-wider uppercase">Kata Sandi</label>
                        <div class="relative flex items-center">
                            <input id="password" type="password" name="password" required minlength="8" placeholder="Minimal 8 karakter"
                                class="w-full bg-surface-container-low text-black-900 font-body text-sm placeholder:text-gray-500 rounded-xl pl-3.5 pr-10 py-2 sm:py-2.5 outline-none transition-all focus:bg-white focus:shadow-[0_0_0_2px_#5443C9]">
                            <button type="button" aria-label="Tampilkan kata sandi" data-toggle-password="password" data-toggle-icon="pwd-icon"
                                class="absolute right-2.5 p-1 rounded-lg text-on-surface-variant hover:text-black-900 transition-colors">
                                <span id="pwd-icon" class="material-symbols-outlined text-[18px] leading-none">visibility</span>
                            </button>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="password_confirmation" class="font-label-upper text-[11px] text-gray-500 font-semibold tracking-wider uppercase">Konfirmasi Kata Sandi</label>
                        <div class="relative flex items-center">
                            <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8" placeholder="Ulangi kata sandi"
                                class="w-full bg-surface-container-low text-black-900 font-body text-sm placeholder:text-gray-500 rounded-xl pl-3.5 pr-10 py-2 sm:py-2.5 outline-none transition-all focus:bg-white focus:shadow-[0_0_0_2px_#5443C9]">
                            <button type="button" aria-label="Tampilkan konfirmasi kata sandi" data-toggle-password="password_confirmation" data-toggle-icon="confirm-pwd-icon"
                                class="absolute right-2.5 p-1 rounded-lg text-on-surface-variant hover:text-black-900 transition-colors">
                                <span id="confirm-pwd-icon" class="material-symbols-outlined text-[18px] leading-none">visibility</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pt-0.5">
                    <button type="submit"
                        class="w-full py-2.5 sm:py-3 px-5 rounded-xl sm:rounded-2xl bg-primary-600 hover:bg-primary-700 text-white font-body text-xs sm:text-sm font-bold tracking-wide shadow-md transition-all active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer">
                        <span>Daftar Akun</span>
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </button>
                </div>

                <div class="relative my-0.5 flex items-center justify-center">
                    <div aria-hidden="true" class="absolute inset-0 flex items-center">
                        <div class="w-full h-px bg-surface-container-high"></div>
                    </div>
                    <div class="relative bg-surface-container-lowest px-3">
                        <span class="font-label-upper text-[11px] text-gray-500 tracking-wider uppercase">atau</span>
                    </div>
                </div>

                <a href="{{ route('google.redirect') }}"
                    class="w-full flex items-center justify-center gap-2.5 py-2 sm:py-2.5 px-4 rounded-xl bg-surface-container-low text-on-surface font-body text-xs sm:text-sm font-semibold transition-all hover:bg-surface-container hover:shadow-sm active:scale-[0.99]">
                    @include('partials.google-icon')
                    <span>Daftar dengan Google</span>
                </a>

                <div class="text-center">
                    <p class="font-body text-xs text-on-surface-variant">
                        Sudah punya akun?
                        <a href="{{ route('masuk') }}" class="font-bold text-primary-600 hover:text-primary-700 transition-colors ml-1">Masuk di sini</a>
                    </p>
                </div>
            </form>
        </div>
    </main>

    <footer class="w-full py-2 text-center text-[11px] text-gray-500">
        <p>&copy; 2025 SINAU APP. Hak Cipta Dilindungi. Untuk Pembelajaran Bahasa &amp; Aksara Jawa.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const input = document.getElementById(btn.getAttribute('data-toggle-password'));
                    const icon = document.getElementById(btn.getAttribute('data-toggle-icon'));
                    if (!input || !icon) return;

                    const tersembunyi = input.type === 'password';
                    input.type = tersembunyi ? 'text' : 'password';
                    icon.textContent = tersembunyi ? 'visibility_off' : 'visibility';
                });
            });
        });
    </script>
</body>
</html>
