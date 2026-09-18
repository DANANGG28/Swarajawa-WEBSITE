<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.kuis-head', ['judul' => 'Masuk'])
</head>
<body class="bg-background font-body text-on-surface antialiased min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-md">
        <div class="flex flex-col items-center gap-2 mb-6">
            <div class="w-14 h-14 rounded-2xl bg-primary-600 flex items-center justify-center text-white shadow-md">
                <span class="material-symbols-outlined text-[30px]">school</span>
            </div>
            <h1 class="font-display text-display font-extrabold text-on-surface">Masuk Sinau Jowo</h1>
            <p class="font-caption text-caption text-gray-500">Masukkan email dan kata sandi Anda.</p>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border border-gray-100">
            @if (session('sukses'))
                <div class="mb-4 px-4 py-3 rounded-xl bg-green-500/10 text-green-500 font-body text-body font-semibold">
                    {{ session('sukses') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 px-4 py-3 rounded-xl bg-error-container text-on-error-container font-body text-body">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('masuk') }}" class="flex flex-col gap-4" id="form-masuk">
                @csrf
                <label class="flex flex-col gap-1.5">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Email</span>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500 transition-colors"
                        placeholder="andi@sinaujowo.test">
                </label>

                <label class="flex flex-col gap-1.5">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Kata Sandi</span>
                    <input type="password" name="password" required
                        class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500 transition-colors"
                        placeholder="••••••••">
                </label>

                <label class="flex items-center gap-2 font-caption text-caption text-gray-500">
                    <input type="checkbox" name="remember" value="1" class="rounded border-gray-200">
                    <span>Ingat saya</span>
                </label>

                <button type="submit" class="mt-1 flex items-center justify-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-body font-bold px-6 py-3 shadow-sm transition-colors">
                    <span class="material-symbols-outlined text-[20px]">login</span>
                    <span>Masuk</span>
                </button>
            </form>

            <p class="mt-4 text-center font-caption text-caption text-gray-500">
                Belum punya akun?
                <a href="{{ route('daftar') }}" class="text-primary-600 font-bold hover:underline">Daftar akun siswa</a>
            </p>
            <p class="mt-1 text-center font-caption text-caption text-gray-400">
                Pendaftaran mandiri hanya untuk siswa. Guru dan superadmin didaftarkan oleh sekolah.
            </p>
        </div>

        <p class="mt-4 text-center font-caption text-caption text-gray-500">
            Akun demo: <span class="font-bold">andi@sinaujowo.test</span> / <span class="font-bold">password</span>
        </p>
    </div>
</body>
</html>
