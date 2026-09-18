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

                <label class="flex flex-col gap-1.5">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Jenis Kelamin</span>
                    <select name="jenis_kelamin" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                        <option value="L" @selected(old('jenis_kelamin') === 'L')>Laki-laki</option>
                        <option value="P" @selected(old('jenis_kelamin') === 'P')>Perempuan</option>
                    </select>
                </label>

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
</body>
</html>
