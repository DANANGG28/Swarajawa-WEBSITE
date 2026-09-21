@props(['role' => 'guru', 'active' => 'dashboard'])

@php
    $nav = $role === 'superadmin'
        ? [
            ['key' => 'dashboard', 'label' => 'Dashboard', 'ikon' => 'dashboard', 'url' => route('superadmin.dashboard'), 'alias' => []],
            ['key' => 'guru', 'label' => 'Akun Guru', 'ikon' => 'supervisor_account', 'url' => route('superadmin.guru'), 'alias' => []],
            ['key' => 'siswa', 'label' => 'Akun Siswa', 'ikon' => 'groups', 'url' => route('superadmin.siswa'), 'alias' => []],
            ['key' => 'topik', 'label' => 'Topik', 'ikon' => 'topic', 'url' => route('superadmin.topik'), 'alias' => []],
            ['key' => 'level-materi', 'label' => 'Level Materi', 'ikon' => 'stairs', 'url' => route('superadmin.level-materi'), 'alias' => ['soal']],
        ]
        : [
            ['key' => 'dashboard', 'label' => 'Pemantauan Siswa', 'ikon' => 'monitoring', 'url' => route('guru.dashboard'), 'alias' => []],
            ['key' => 'soal', 'label' => 'Manajemen Soal', 'ikon' => 'quiz', 'url' => route('guru.soal'), 'alias' => ['level-materi']],
        ];

    $isItemActive = fn ($item) => $active === $item['key'] || in_array($active, $item['alias'] ?? [], true);

    $user = \App\Support\AuthContext::currentUser(request());
    $nama = $user?->nama_lengkap ?? 'Pengguna';
    $peran = $role === 'superadmin' ? 'Superadmin' : 'Guru Bahasa Jawa';
    $inisial = collect(explode(' ', $nama))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');
@endphp

<aside class="fixed left-0 top-0 h-full w-72 bg-white z-50 flex flex-col justify-between shadow-sm border-r-2 border-slate-300">
    <div class="flex flex-col flex-1 overflow-y-auto">
        <div class="h-20 px-space-xl flex items-center gap-space-md shrink-0">
            <div class="w-10 h-10 rounded-xl bg-primary-600 flex items-center justify-center text-white font-bold font-heading text-xl shadow-sm">SJ</div>
            <div class="flex flex-col">
                <span class="font-heading text-heading font-black uppercase text-primary leading-tight">Sinau Jowo</span>
                <span class="font-caption text-caption font-bold uppercase tracking-wide text-primary-600">{{ $peran }}</span>
            </div>
        </div>
        <div class="px-space-md py-space-sm">
            <nav class="flex flex-col gap-2">
                @foreach ($nav as $item)
                    @php
                        $isActive = $isItemActive($item);
                    @endphp
                    <a href="{{ $item['url'] }}"
                        class="flex items-center gap-space-md px-4 py-3 rounded-2xl transition-all duration-200 border-2 {{ $isActive ? 'bg-primary-600 border-primary-700 text-on-primary font-black uppercase shadow-[0_3px_0_rgba(60,37,177,0.35)]' : 'border-transparent text-slate-600 hover:bg-primary-fixed/30 hover:text-primary-600 font-bold uppercase' }}">
                        <span class="material-symbols-outlined text-[22px]">{{ $item['ikon'] }}</span>
                        <span class="font-body text-sm font-black uppercase tracking-wide">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </div>
    </div>

    <div class="p-space-md border-t-2 border-slate-200 shrink-0">
        <div class="flex items-center justify-between p-3 rounded-2xl bg-surface-container-low border-2 border-slate-200">
            <div class="flex items-center gap-3 min-w-0">
                <div class="relative flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-primary-700 text-white flex items-center justify-center font-bold text-sm uppercase">{{ $inisial }}</div>
                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-green-500 border-2 border-surface-container-lowest"></span>
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="font-heading text-body font-black text-on-surface truncate">{{ $nama }}</span>
                    <span class="font-caption text-caption font-bold uppercase tracking-wide text-primary-600 truncate">{{ $peran }}</span>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('keluar') }}" class="mt-2">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gray-50 hover:bg-surface-container-high text-slate-600 hover:text-error font-body text-sm font-black uppercase tracking-wide transition-colors border-2 border-slate-300">
                <span class="material-symbols-outlined text-[20px]">logout</span>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>
