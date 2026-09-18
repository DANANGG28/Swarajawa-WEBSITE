@props(['active' => 'beranda'])

@php
    $siswaUser = \App\Support\AuthContext::currentUser(request()) ?? auth('siswa')->user();
    $siswaNama = $siswaUser?->nama_lengkap ?? $siswaUser?->nama ?? 'Siswa';
    $siswaKelas = isset($siswaUser->kelas) && $siswaUser->kelas !== '' ? 'Kelas '.$siswaUser->kelas.' • Siswa' : 'Siswa';
    $namaWords = array_values(array_filter(explode(' ', trim($siswaNama))));
    $siswaInisial = count($namaWords) >= 2
        ? mb_strtoupper(mb_substr($namaWords[0], 0, 1) . mb_substr($namaWords[count($namaWords) - 1], 0, 1))
        : mb_strtoupper(mb_substr($siswaNama, 0, 2));

    $nav = [
        ['key' => 'beranda', 'label' => 'Beranda / Pembelajaran', 'ikon' => 'menu_book', 'url' => url('/'), 'alias' => []],
        ['key' => 'latihan', 'label' => 'Latihan Soal & Kuis', 'ikon' => 'stylus_note', 'url' => url('/latihan-soal'), 'alias' => []],
        ['key' => 'papan-skor', 'label' => 'Papan Skor', 'ikon' => 'leaderboard', 'url' => url('/papan-skor'), 'alias' => ['skor']],
        ['key' => 'asisten', 'label' => 'Asisten Tanya Bahasa', 'ikon' => 'smart_toy', 'url' => url('/asisten-ai'), 'alias' => ['asisten-ai']],
        ['key' => 'profil', 'label' => 'Profil & Pengaturan', 'ikon' => 'manage_accounts', 'url' => url('/profil'), 'alias' => ['profile']],
    ];
    $isAktif = fn ($item) => $active === $item['key'] || in_array($active, $item['alias'], true);
@endphp

<aside class="fixed left-0 top-0 h-full w-72 bg-surface-container-lowest z-50 flex flex-col justify-between shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
    <div class="flex flex-col flex-1 overflow-y-auto">
        <div class="h-20 px-space-xl flex items-center gap-space-md shrink-0">
            <div class="w-10 h-10 rounded-xl bg-primary-600 flex items-center justify-center text-white font-bold font-heading text-xl shadow-sm">SJ</div>
            <div class="flex flex-col">
                <span class="font-heading text-heading text-primary leading-tight">Sinau Jowo</span>
                <span class="font-caption text-caption text-on-surface-variant">Portal Belajar Siswa</span>
            </div>
        </div>
        <div class="px-space-md py-space-sm">
            <nav class="flex flex-col gap-2">
                @foreach ($nav as $item)
                    <a href="{{ $item['url'] }}"
                        class="flex items-center gap-space-md px-4 py-3 rounded-xl transition-all duration-200 {{ $isAktif($item) ? 'bg-primary-600 text-on-primary font-heading shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <span class="material-symbols-outlined text-[22px]">{{ $item['ikon'] }}</span>
                        <span class="font-body text-body font-semibold">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </div>

        @if(($active === 'asisten' || $active === 'asisten-ai') && $siswaUser)
            @php
                $chatSessions = $siswaUser->chatSessions()->latest('updated_at')->take(15)->get(['id', 'judul', 'updated_at']);
            @endphp
            <div class="mt-2 px-space-md py-2 border-t border-gray-100 flex flex-col gap-2">
                <div class="flex items-center justify-between px-2 text-gray-500 font-label-upper text-label-upper font-bold uppercase tracking-wider">
                    <span class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">history</span>
                        Histori Chat AI
                    </span>
                    <button type="button" id="btn-tambah-chat" data-action="chat-baru" class="hover:text-primary-600 transition-colors" title="Tambah Chat Baru">
                        <span class="material-symbols-outlined text-[16px]">add</span>
                    </button>
                </div>
                <div class="flex flex-col gap-1 text-caption font-body" id="sidebar-chat-history">
                    @forelse($chatSessions as $sesi)
                        <div class="group flex items-center gap-1 rounded-xl hover:bg-surface-container-high transition-colors" data-session-id="{{ $sesi->id }}">
                            <button type="button" data-action="load-sesi" data-session-id="{{ $sesi->id }}" class="flex-1 min-w-0 flex items-center gap-2 px-3 py-2 text-left text-on-surface-variant hover:text-on-surface transition-colors truncate">
                                <span class="material-symbols-outlined text-[16px] text-gray-400 group-hover:text-primary-600 shrink-0">chat_bubble_outline</span>
                                <span class="truncate" data-judul>{{ $sesi->judul }}</span>
                            </button>
                            <button type="button" data-action="hapus-sesi" data-session-id="{{ $sesi->id }}" class="hidden group-hover:flex items-center justify-center w-7 h-7 mr-1 rounded-full text-gray-400 hover:text-error hover:bg-error-container/50 transition-colors shrink-0" title="Busak riwayat">
                                <span class="material-symbols-outlined text-[16px]">delete</span>
                            </button>
                        </div>
                    @empty
                        <div class="px-3 py-2 text-gray-400 italic text-xs" data-empty-state>Durung ana riwayat chat</div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>

    <div class="p-space-md border-t border-gray-100 shrink-0">
        <div class="flex items-center justify-between p-3 rounded-2xl bg-surface-container-low">
            <div class="flex items-center gap-3 min-w-0">
                <div class="relative flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-primary-700 text-white flex items-center justify-center font-bold text-sm uppercase">{{ $siswaInisial }}</div>
                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-green-500 border-2 border-surface-container-lowest"></span>
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="font-heading text-body font-bold text-on-surface truncate">{{ $siswaNama }}</span>
                    <span class="font-caption text-caption text-on-surface-variant truncate">{{ $siswaKelas }}</span>
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
