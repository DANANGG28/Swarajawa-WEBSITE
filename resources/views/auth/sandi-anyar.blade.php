<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.kuis-head', ['judul' => 'Kata Sandi Baru'])
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
        <div class="relative w-full max-w-[500px] flex flex-col items-center">
            <div aria-hidden="true" class="absolute -top-12 -left-12 w-64 h-64 bg-primary-fixed rounded-full blur-3xl opacity-40 pointer-events-none"></div>
            <div aria-hidden="true" class="absolute -bottom-10 -right-10 w-64 h-64 bg-secondary-fixed rounded-full blur-3xl opacity-30 pointer-events-none"></div>

            <div class="relative w-full bg-surface-container-lowest border border-gray-200 shadow-md rounded-3xl px-8 sm:px-10 py-9 sm:py-10 flex flex-col z-10">
                <header class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 mb-3 rounded-2xl bg-primary-600 flex items-center justify-center text-white shadow-md">
                        <span class="material-symbols-outlined text-[30px]">password</span>
                    </div>
                    <h1 class="font-display text-2xl sm:text-3xl font-extrabold text-black-900 tracking-tight mb-2">Isi Kata Sandi Baru</h1>
                    <p class="font-body text-body text-on-surface-variant max-w-sm">
                        Buat kata sandi baru yang kuat dan mudah Anda ingat untuk mengamankan akun belajar Anda.
                    </p>
                </header>

                @if ($errors->any())
                    <div class="mt-6 px-4 py-3 rounded-2xl bg-error-container text-on-error-container font-body text-body">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('sandi.simpan') }}" class="mt-8 flex flex-col gap-6" id="reset-password-form">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div class="flex flex-col gap-2">
                        <label for="password" class="font-label-upper text-label-upper text-gray-500 uppercase tracking-wider">Kata Sandi Baru</label>
                        <div class="relative flex items-center">
                            <input id="password" type="password" name="password" required minlength="8" placeholder="Minimal 8 karakter (kombinasi)"
                                class="w-full h-12 px-4 pr-12 rounded-xl bg-gray-50 text-black-900 font-body text-body placeholder:text-gray-500 outline-none transition-all focus:bg-white focus:shadow-[0_0_0_2px_#6C5CE8]">
                            <button type="button" aria-label="Tampilkan kata sandi" data-toggle-password="password" data-toggle-icon="toggle-pwd-icon"
                                class="absolute right-3 w-8 h-8 flex items-center justify-center text-on-surface-variant hover:text-black-900 transition-colors">
                                <span id="toggle-pwd-icon" class="material-symbols-outlined text-[20px] leading-none">visibility</span>
                            </button>
                        </div>

                        <div class="flex flex-col gap-1.5 mt-1">
                            <div class="flex items-center justify-between">
                                <span class="font-caption text-caption text-on-surface-variant">Kekuatan sandi: <span class="font-semibold text-gray-500" id="strength-text">Belum diisi</span></span>
                                <span class="font-caption text-caption text-gray-500 font-medium" id="strength-score">0%</span>
                            </div>
                            <div class="grid grid-cols-4 gap-1.5 w-full h-1.5 rounded-full overflow-hidden bg-gray-200">
                                <div class="h-full bg-gray-200 transition-all duration-300" id="meter-1"></div>
                                <div class="h-full bg-gray-200 transition-all duration-300" id="meter-2"></div>
                                <div class="h-full bg-gray-200 transition-all duration-300" id="meter-3"></div>
                                <div class="h-full bg-gray-200 transition-all duration-300" id="meter-4"></div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="password_confirmation" class="font-label-upper text-label-upper text-gray-500 uppercase tracking-wider">Konfirmasi Kata Sandi Baru</label>
                        <div class="relative flex items-center">
                            <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8" placeholder="Ulangi kata sandi baru"
                                class="w-full h-12 px-4 pr-12 rounded-xl bg-gray-50 text-black-900 font-body text-body placeholder:text-gray-500 outline-none transition-all focus:bg-white focus:shadow-[0_0_0_2px_#6C5CE8]">
                            <button type="button" aria-label="Tampilkan konfirmasi kata sandi" data-toggle-password="password_confirmation" data-toggle-icon="toggle-confirm-icon"
                                class="absolute right-3 w-8 h-8 flex items-center justify-center text-on-surface-variant hover:text-black-900 transition-colors">
                                <span id="toggle-confirm-icon" class="material-symbols-outlined text-[20px] leading-none">visibility</span>
                            </button>
                        </div>
                        <div class="flex items-center gap-1.5 font-caption text-caption text-gray-500" id="match-status">
                            <span class="material-symbols-outlined text-[16px] leading-none">info</span>
                            <span>Pastikan kedua kata sandi sama persis.</span>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full h-12 mt-2 px-6 rounded-xl bg-primary-700 text-white font-body text-body font-semibold tracking-wide flex items-center justify-center gap-2 shadow-sm hover:bg-primary hover:shadow-md active:shadow-lg transition-all focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 cursor-pointer">
                        <span>Simpan Kata Sandi Baru</span>
                        <span class="material-symbols-outlined text-[20px]">check</span>
                    </button>
                </form>

                <div class="mt-8 pt-6 flex flex-col items-center text-center gap-3 border-t border-gray-200">
                    <a href="{{ route('masuk') }}" class="inline-flex items-center gap-1.5 font-body text-body font-medium text-primary-700 hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                        <span>Batal dan kembali ke halaman Masuk</span>
                    </a>
                    <p class="font-caption text-caption text-gray-500 max-w-xs">
                        Jika masih menemui kendala, hubungi <span class="text-on-surface-variant font-medium">Guru Pengajar</span> atau Operator Sekolah.
                    </p>
                </div>
            </div>

            <div class="mt-6 flex items-center gap-2 font-caption text-caption text-on-surface-variant opacity-80">
                <span class="material-symbols-outlined text-green-500 text-[18px]">verified_user</span>
                <span>Enkripsi Keamanan Kata Sandi &bull; Sinau Jowo v2.4</span>
            </div>
        </div>
    </main>

    <footer class="w-full py-4 text-center text-xs text-gray-500">
        <p>&copy; 2025 Sinau Jowo. Hak Cipta Dilindungi. Pengetahuan Bahasa &amp; Aksara Jawa.</p>
    </footer>

    <script>
        function labelKekuatan(val) {
            let skor = 0;
            const cukupPanjang = val.length >= 8;
            const adaBesar = /[A-Z]/.test(val);
            const adaKecil = /[a-z]/.test(val);
            const adaHurufCampur = adaBesar && adaKecil;
            const adaAngkaAtauSimbol = /[\d\W_]/.test(val);

            if (cukupPanjang) skor += 1;
            if (adaHurufCampur) skor += 1;
            if (adaAngkaAtauSimbol) skor += 1;
            if (val.length >= 12 && adaHurufCampur && adaAngkaAtauSimbol) skor += 1;

            return skor;
        }

        function warnaKekuatan(skor) {
            if (skor <= 1) return { kelas: 'bg-error', teks: 'Lemah', warnaTeks: 'text-error', persen: '25%' };
            if (skor === 2) return { kelas: 'bg-orange-500', teks: 'Sedang', warnaTeks: 'text-orange-500', persen: '50%' };
            if (skor === 3) return { kelas: 'bg-primary-600', teks: 'Kuat', warnaTeks: 'text-primary-600', persen: '75%' };
            return { kelas: 'bg-green-500', teks: 'Sangat Kuat', warnaTeks: 'text-green-500', persen: '100%' };
        }

        function perbaruiKekuatan(val) {
            const meter = ['meter-1', 'meter-2', 'meter-3', 'meter-4'].map((id) => document.getElementById(id));
            const teks = document.getElementById('strength-text');
            const skorLabel = document.getElementById('strength-score');

            meter.forEach((m) => { m.className = 'h-full bg-gray-200 transition-all duration-300'; });

            if (val.length === 0) {
                teks.innerText = 'Belum diisi';
                teks.className = 'font-semibold text-gray-500';
                skorLabel.innerText = '0%';
            } else {
                const skor = labelKekuatan(val);
                const gaya = warnaKekuatan(skor);
                for (let i = 0; i < skor && i < meter.length; i++) {
                    meter[i].className = 'h-full ' + gaya.kelas + ' transition-all duration-300';
                }
                teks.innerText = gaya.teks;
                teks.className = 'font-semibold ' + gaya.warnaTeks;
                skorLabel.innerText = gaya.persen;
            }

            cekKecocokan();
        }

        function cekKecocokan() {
            const sandi = document.getElementById('password').value;
            const konfirmasi = document.getElementById('password_confirmation').value;
            const status = document.getElementById('match-status');

            if (!konfirmasi) {
                status.className = 'flex items-center gap-1.5 font-caption text-caption text-gray-500';
                status.innerHTML = '<span class="material-symbols-outlined text-[16px] leading-none">info</span><span>Pastikan kedua kata sandi sama persis.</span>';
                return;
            }

            if (sandi === konfirmasi) {
                status.className = 'flex items-center gap-1.5 font-caption text-caption text-green-500 font-medium';
                status.innerHTML = '<span class="material-symbols-outlined text-[16px] leading-none">check_circle</span><span>Kata sandi sudah cocok dan sama.</span>';
            } else {
                status.className = 'flex items-center gap-1.5 font-caption text-caption text-error font-medium';
                status.innerHTML = '<span class="material-symbols-outlined text-[16px] leading-none">cancel</span><span>Kata sandi belum sama, periksa kembali.</span>';
            }
        }

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

            const sandi = document.getElementById('password');
            const konfirmasi = document.getElementById('password_confirmation');
            if (sandi) sandi.addEventListener('input', function () { perbaruiKekuatan(this.value); });
            if (konfirmasi) konfirmasi.addEventListener('input', cekKecocokan);
        });
    </script>
</body>
</html>
