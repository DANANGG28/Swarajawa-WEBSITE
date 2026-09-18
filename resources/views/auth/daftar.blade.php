<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.kuis-head', ['judul' => 'Daftar'])
</head>
<body class="bg-background font-body text-on-surface antialiased min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-lg">
        <div class="flex flex-col items-center gap-2 mb-6">
            <div class="w-14 h-14 rounded-2xl bg-primary-600 flex items-center justify-center text-white shadow-md">
                <span class="material-symbols-outlined text-[30px]">person_add</span>
            </div>
            <h1 class="font-display text-display font-extrabold text-on-surface">Daftar Akun Siswa</h1>
            <p class="font-caption text-caption text-gray-500">Pendaftaran mandiri hanya untuk siswa. Guru dan superadmin didaftarkan oleh sekolah.</p>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border border-gray-100">
            @if ($errors->any())
                <div class="mb-4 px-4 py-3 rounded-xl bg-error-container text-on-error-container font-body text-body">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('daftar') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf
                <label class="flex flex-col gap-1.5 sm:col-span-2">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Nama Lengkap</span>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required
                        class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                </label>

                <label class="flex flex-col gap-1.5">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">NIS</span>
                    <input type="text" name="nis" value="{{ old('nis') }}" required
                        class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                </label>

                @php
                    $currentJk = old('jenis_kelamin', 'L');
                    $jkOptions = [
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ];
                @endphp

                <div class="relative flex flex-col gap-1.5" id="wrapper-jenis-kelamin">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Jenis Kelamin</span>
                    <input type="hidden" name="jenis_kelamin" id="input-jenis-kelamin" value="{{ $currentJk }}">

                    <button type="button" id="btn-jenis-kelamin"
                        class="flex items-center justify-between w-full rounded-xl border border-gray-200 bg-gray-50 hover:bg-gray-100 hover:border-primary-400 px-4 py-3 font-body text-body text-gray-700 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        <span id="label-jenis-kelamin" class="truncate font-medium text-gray-800">
                            {{ $jkOptions[$currentJk] ?? 'Laki-laki' }}
                        </span>
                        <span class="inline-flex items-center justify-center shrink-0 w-5 h-5 text-gray-400 ml-2">
                            <span id="chevron-jenis-kelamin" class="material-symbols-outlined text-[20px] leading-none transition-transform duration-200">expand_more</span>
                        </span>
                    </button>

                    {{-- Dropdown Menu Jenis Kelamin dengan Pembatas Antar Opsi --}}
                    <div id="menu-jenis-kelamin" class="hidden absolute top-full left-0 mt-2 w-full bg-white rounded-2xl shadow-xl border border-gray-100 py-1.5 z-50 overflow-hidden divide-y divide-gray-100">
                        @foreach ($jkOptions as $val => $text)
                            @php $isSelected = ($currentJk === $val); @endphp
                            <button type="button" data-val="{{ $val }}" data-label="{{ $text }}"
                                class="item-jenis-kelamin w-full flex items-center justify-between px-4 py-2.5 text-left font-body text-body {{ $isSelected ? 'bg-primary-50 text-primary-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }} transition-colors">
                                <span>{{ $text }}</span>
                                <span class="check-icon material-symbols-outlined text-[18px] text-primary-600 {{ $isSelected ? '' : 'hidden' }}">check</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <label class="flex flex-col gap-1.5">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Kelas</span>
                    <input type="text" name="kelas" value="{{ old('kelas') }}" placeholder="7A"
                        class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                </label>

                <label class="flex flex-col gap-1.5">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">No. Telpon</span>
                    <input type="text" name="no_telpon" value="{{ old('no_telpon') }}"
                        class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                </label>

                <label class="flex flex-col gap-1.5 sm:col-span-2">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Email</span>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                </label>

                <label class="flex flex-col gap-1.5">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Kata Sandi</span>
                    <input type="password" name="password" required
                        class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                </label>

                <label class="flex flex-col gap-1.5">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Ulangi Kata Sandi</span>
                    <input type="password" name="password_confirmation" required
                        class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                </label>

                <button type="submit" class="sm:col-span-2 mt-1 flex items-center justify-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-body font-bold px-6 py-3 shadow-sm transition-colors">
                    <span class="material-symbols-outlined text-[20px]">how_to_reg</span>
                    <span>Daftar dan Mulai Belajar</span>
                </button>
            </form>

            <p class="mt-4 text-center font-caption text-caption text-gray-500">
                Sudah punya akun?
                <a href="{{ route('masuk') }}" class="text-primary-600 font-bold hover:underline">Masuk di sini</a>
            </p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('btn-jenis-kelamin');
            const menu = document.getElementById('menu-jenis-kelamin');
            const chevron = document.getElementById('chevron-jenis-kelamin');
            const input = document.getElementById('input-jenis-kelamin');
            const label = document.getElementById('label-jenis-kelamin');

            if (btn && menu && input) {
                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const isHidden = menu.classList.toggle('hidden');
                    if (!isHidden) {
                        chevron.classList.add('rotate-180');
                    } else {
                        chevron.classList.remove('rotate-180');
                    }
                });

                menu.querySelectorAll('.item-jenis-kelamin').forEach(function (item) {
                    item.addEventListener('click', function (e) {
                        e.stopPropagation();
                        const val = this.getAttribute('data-val');
                        const labelText = this.getAttribute('data-label');

                        input.value = val;
                        label.textContent = labelText;

                        menu.querySelectorAll('.item-jenis-kelamin').forEach(function (opt) {
                            opt.classList.remove('bg-primary-50', 'text-primary-700', 'font-bold');
                            opt.classList.add('text-gray-700');
                            opt.querySelector('.check-icon')?.classList.add('hidden');
                        });

                        this.classList.add('bg-primary-50', 'text-primary-700', 'font-bold');
                        this.classList.remove('text-gray-700');
                        this.querySelector('.check-icon')?.classList.remove('hidden');

                        menu.classList.add('hidden');
                        chevron.classList.remove('rotate-180');
                    });
                });

                document.addEventListener('click', function (e) {
                    if (!btn.contains(e.target) && !menu.contains(e.target)) {
                        menu.classList.add('hidden');
                        chevron.classList.remove('rotate-180');
                    }
                });
            }
        });
    </script>
</body>
</html>
