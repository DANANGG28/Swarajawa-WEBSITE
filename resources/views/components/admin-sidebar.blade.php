@props(['role' => 'guru', 'active' => 'dashboard'])

@php
    $nav = $role === 'superadmin'
        ? [
            ['key' => 'dashboard', 'label' => 'Dashboard', 'ikon' => 'dashboard', 'url' => route('superadmin.dashboard')],
            ['key' => 'guru', 'label' => 'Akun Guru', 'ikon' => 'supervisor_account', 'url' => route('superadmin.guru')],
            ['key' => 'siswa', 'label' => 'Akun Siswa', 'ikon' => 'groups', 'url' => route('superadmin.siswa')],
            ['key' => 'level-materi', 'label' => 'Level Materi', 'ikon' => 'stairs', 'url' => route('superadmin.level-materi')],
            ['key' => 'soal', 'label' => 'Bank Soal', 'ikon' => 'quiz', 'url' => route('superadmin.soal')],
        ]
        : [
            ['key' => 'dashboard', 'label' => 'Pemantauan Siswa', 'ikon' => 'monitoring', 'url' => route('guru.dashboard')],
            ['key' => 'soal', 'label' => 'Manajemen Soal', 'ikon' => 'quiz', 'url' => route('guru.soal')],
            ['key' => 'test', 'label' => 'Paket Test', 'ikon' => 'playlist_add_check', 'url' => route('guru.test')],
        ];

    $user = \App\Support\AuthContext::currentUser(request());
    $nama = $user?->nama_lengkap ?? 'Pengguna';
    $peran = $role === 'superadmin' ? 'Superadmin' : 'Guru Basa Jawa';
    $inisial = collect(explode(' ', $nama))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');
@endphp

<aside class="fixed left-0 top-0 h-full w-72 bg-surface-container-lowest z-50 flex flex-col justify-between shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
    <div class="flex flex-col flex-1 overflow-y-auto">
        <div class="h-20 px-space-xl flex items-center gap-space-md shrink-0">
            <div class="w-10 h-10 rounded-xl bg-primary-600 flex items-center justify-center text-white font-bold font-heading text-xl shadow-sm">SJ</div>
            <div class="flex flex-col">
                <span class="font-heading text-heading text-primary leading-tight">Sinau Jowo</span>
                <span class="font-caption text-caption text-on-surface-variant">{{ $peran }}</span>
            </div>
        </div>
        <div class="px-space-md py-space-sm">
            <nav class="flex flex-col gap-2">
                @foreach ($nav as $item)
                    <a href="{{ $item['url'] }}"
                        class="flex items-center gap-space-md px-4 py-3 rounded-xl transition-all duration-200 {{ $active === $item['key'] ? 'bg-primary-600 text-on-primary font-heading shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <span class="material-symbols-outlined text-[22px]">{{ $item['ikon'] }}</span>
                        <span class="font-body text-body font-semibold">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </div>
    </div>

    <div class="p-space-md border-t border-gray-100 shrink-0">
        <div class="flex items-center justify-between p-3 rounded-2xl bg-surface-container-low">
            <div class="flex items-center gap-3 min-w-0">
                <div class="relative flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-primary-700 text-white flex items-center justify-center font-bold text-sm uppercase">{{ $inisial }}</div>
                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-green-500 border-2 border-surface-container-lowest"></span>
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="font-heading text-body font-bold text-on-surface truncate">{{ $nama }}</span>
                    <span class="font-caption text-caption text-on-surface-variant truncate">{{ $peran }}</span>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('keluar') }}" class="mt-2">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gray-50 hover:bg-surface-container-high text-on-surface-variant hover:text-error font-body text-body font-semibold transition-colors">
                <span class="material-symbols-outlined text-[20px]">logout</span>
                <span>Metu</span>
            </button>
        </form>
    </div>
</aside>
