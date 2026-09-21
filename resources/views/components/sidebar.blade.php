@props(['active' => 'beranda'])

@php
    $siswaUser = \App\Support\AuthContext::currentUser(request()) ?? auth('siswa')->user();
    $siswaNama = $siswaUser?->nama_lengkap ?? $siswaUser?->nama ?? 'Siswa';
    $siswaKelas = isset($siswaUser->kelas) && $siswaUser->kelas !== '' ? 'Siswa • Kelas '.$siswaUser->kelas : 'Siswa';
    $namaWords = array_values(array_filter(explode(' ', trim($siswaNama))));
    $siswaInisial = count($namaWords) >= 2
        ? mb_strtoupper(mb_substr($namaWords[0], 0, 1) . mb_substr($namaWords[count($namaWords) - 1], 0, 1))
        : mb_strtoupper(mb_substr($siswaNama, 0, 2));

    $nav = [
        [
            'key' => 'beranda',
            'label' => 'Beranda',
            'url' => url('/'),
            'alias' => [],
            'svg' => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>',
        ],
        [
            'key' => 'papan-skor',
            'label' => 'Papan Skor & Peringkat',
            'url' => url('/papan-skor'),
            'alias' => ['skor'],
            'svg' => '<circle cx="12" cy="8" r="6"></circle><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"></path>',
        ],
        [
            'key' => 'profil',
            'label' => 'Pengaturan Profil',
            'url' => url('/profil'),
            'alias' => ['profile'],
            'svg' => '<circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>',
        ],
    ];
    $isAktif = fn ($item) => $active === $item['key'] || in_array($active, $item['alias'], true);
@endphp

<aside class="fixed left-0 top-0 h-full w-72 bg-white z-50 flex flex-col justify-between shadow-sm border-r-2 border-slate-300">
    <div class="flex flex-col flex-1 overflow-y-auto px-5 py-6">
        <!-- Brand -->
        <div class="flex items-center gap-3.5 px-2 mb-8" data-purpose="brand-header">
            <div class="w-11 h-11 rounded-2xl bg-primary-600 flex items-center justify-center text-white shadow-md shadow-primary-600/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-black tracking-tight text-primary uppercase leading-none">Sinau Jowo</h1>
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-primary-600 mt-1 block">Platform Pasinaon</span>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="space-y-1.5" data-purpose="main-navigation">
            @foreach ($nav as $item)
                <a href="{{ $item['url'] }}"
                    class="flex items-center gap-3.5 px-4 py-3.5 rounded-2xl transition-all border-2 {{ $isAktif($item) ? 'bg-primary-fixed/60 border-primary-fixed-dim text-primary-600 font-black uppercase shadow-[0_3px_0_rgba(84,67,201,0.18)]' : 'border-transparent text-slate-600 hover:text-primary-600 hover:bg-primary-fixed/30 font-bold uppercase' }}">
                    <svg class="w-5 h-5 {{ $isAktif($item) ? 'text-primary-600' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" viewBox="0 0 24 24">
                        {!! $item['svg'] !!}
                    </svg>
                    <span class="text-sm tracking-wide">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        @if(($active === 'asisten' || $active === 'asisten-ai') && $siswaUser)
            @php
                $chatSessions = $siswaUser->chatSessions()->latest('updated_at')->take(15)->get(['id', 'judul', 'updated_at']);
            @endphp
            <div class="mt-4 px-1 py-3 border-t-2 border-slate-200 flex flex-col gap-2">
                <div class="flex items-center justify-between px-2 text-slate-400 text-[10px] font-extrabold uppercase tracking-widest">
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
                        <div class="group flex items-center gap-1 rounded-xl hover:bg-primary-fixed/30 transition-colors" data-session-id="{{ $sesi->id }}">
                            <button type="button" data-action="load-sesi" data-session-id="{{ $sesi->id }}" class="flex-1 min-w-0 flex items-center gap-2 px-3 py-2 text-left text-slate-600 hover:text-primary-600 transition-colors truncate">
                                <span class="material-symbols-outlined text-[16px] text-slate-400 group-hover:text-primary-600 shrink-0">chat_bubble_outline</span>
                                <span class="truncate text-xs font-semibold" data-judul>{{ $sesi->judul }}</span>
                            </button>
                            <button type="button" data-action="hapus-sesi" data-session-id="{{ $sesi->id }}" class="hidden group-hover:flex items-center justify-center w-7 h-7 mr-1 rounded-full text-slate-400 hover:text-error hover:bg-error-container/50 transition-colors shrink-0" title="Hapus riwayat">
                                <span class="material-symbols-outlined text-[16px]">delete</span>
                            </button>
                        </div>
                    @empty
                        <div class="px-3 py-2 text-slate-400 italic text-xs" data-empty-state>Belum ada riwayat chat</div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>

    <!-- Footer profile -->
    <div class="pt-4 border-t-2 border-slate-200 px-5 pb-6 shrink-0" data-purpose="sidebar-footer">
        <div class="flex items-center gap-3 px-2 py-2 mb-3 bg-primary-fixed/30 rounded-2xl border-2 border-primary-fixed/70">
            <div class="w-10 h-10 rounded-xl overflow-hidden bg-primary-fixed border-2 border-white shadow-sm shrink-0 flex items-center justify-center">
                @if($siswaUser?->foto_url)
                    <img src="{{ $siswaUser->foto_url }}" alt="{{ $siswaNama }}" class="w-full h-full object-cover">
                @elseif(!empty($siswaUser?->foto) && file_exists(storage_path('image/siswa/'.$siswaUser->foto)))
                    <img src="{{ route('siswa.image', $siswaUser->foto) }}" alt="{{ $siswaNama }}" class="w-full h-full object-cover">
                @else
                    <span class="font-black text-sm uppercase text-primary-700">{{ $siswaInisial }}</span>
                @endif
            </div>
            <div class="truncate">
                <div class="text-xs font-black text-slate-900 truncate">{{ $siswaNama }}</div>
                <div class="text-[10px] font-extrabold uppercase tracking-wider text-primary-600 truncate">{{ $siswaKelas }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('keluar') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 text-xs font-black uppercase tracking-wide text-slate-600 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all border-2 border-slate-300 hover:border-red-300 cursor-pointer active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" viewBox="0 0 24 24">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" x2="9" y1="12" y2="12"></line>
                </svg>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>
