@props(['active' => 'beranda'])

<aside class="fixed left-0 top-0 h-full w-72 bg-surface-container-lowest z-50 flex flex-col justify-between shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
    <div class="flex flex-col flex-1 overflow-y-auto">
        <div class="h-20 px-space-xl flex items-center gap-space-md shrink-0">
            <div class="w-10 h-10 rounded-xl bg-primary-600 flex items-center justify-center text-white font-bold font-heading text-xl shadow-sm">
                SJ
            </div>
            <div class="flex flex-col">
                <span class="font-heading text-heading text-primary leading-tight">Sinau Jowo</span>
                <span class="font-caption text-caption text-on-surface-variant">Portal Belajar Siswa</span>
            </div>
        </div>
        <div class="px-space-md py-space-sm">
            <nav class="flex flex-col gap-2">
                <a href="{{ url('/') }}" class="flex items-center gap-space-md px-4 py-3 rounded-xl transition-all duration-200 {{ $active === 'beranda' ? 'bg-primary-600 text-on-primary font-heading shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                    <span class="material-symbols-outlined text-[22px]">menu_book</span>
                    <span class="font-body text-body font-semibold">Beranda / Pembelajaran</span>
                </a>
                <a href="{{ url('/latihan-soal') }}" class="flex items-center gap-space-md px-4 py-3 rounded-xl transition-all duration-200 {{ $active === 'latihan' ? 'bg-primary-600 text-on-primary font-heading shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                    <span class="material-symbols-outlined text-[22px]">stylus_note</span>
                    <span class="font-body text-body font-semibold">Latihan Soal & Kuis</span>
                </a>
                <a href="{{ url('/papan-skor') }}" class="flex items-center gap-space-md px-4 py-3 rounded-xl transition-all duration-200 {{ $active === 'papan-skor' || $active === 'skor' ? 'bg-primary-600 text-on-primary font-heading shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                    <span class="material-symbols-outlined text-[22px]">leaderboard</span>
                    <span class="font-body text-body font-semibold">Papan Skor</span>
                </a>
                <a href="{{ url('/asisten-ai') }}" class="flex items-center gap-space-md px-4 py-3 rounded-xl transition-all duration-200 {{ $active === 'asisten' || $active === 'asisten-ai' ? 'bg-primary-600 text-on-primary font-heading shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                    <span class="material-symbols-outlined text-[22px]">smart_toy</span>
                    <span class="font-body text-body font-semibold">Asisten Tanya Bahasa</span>
                </a>
                {{-- <a href="#" class="flex items-center gap-space-md px-4 py-3 rounded-xl text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-all duration-200">
                    <span class="material-symbols-outlined text-[22px]">military_tech</span>
                    <span class="font-body text-body font-semibold">Koleksi Aksara & Sertifikat</span>
                </a> --}}
                <a href="{{ url('/profil') }}" class="flex items-center gap-space-md px-4 py-3 rounded-xl transition-all duration-200 {{ $active === 'profil' || $active === 'profile' ? 'bg-primary-600 text-on-primary font-heading shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                    <span class="material-symbols-outlined text-[22px]">manage_accounts</span>
                    <span class="font-body text-body font-semibold">Profil & Pengaturan</span>
                </a>
            </nav>
        </div>

        @if($active === 'asisten' || $active === 'asisten-ai')
        <!-- Histori Chat AI Section (Mung Muncul ing Menu Asisten Tanya Bahasa) -->
        <div class="mt-2 px-space-md py-2 border-t border-gray-100 flex flex-col gap-2">
            <div class="flex items-center justify-between px-2 text-gray-500 font-label-upper text-label-upper font-bold uppercase tracking-wider">
                <span class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">history</span>
                    Histori Chat AI
                </span>
                <button type="button" class="hover:text-primary-600 transition-colors" title="Tambah Chat Baru">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                </button>
            </div>
            <div class="flex flex-col gap-1 text-caption font-body">
                <a href="#" class="flex items-center gap-2 px-3 py-2 rounded-xl text-on-surface hover:bg-surface-container-high transition-colors truncate font-medium bg-surface-container-low/80 font-bold">
                    <span class="material-symbols-outlined text-[16px] text-primary-600 shrink-0">chat_bubble_outline</span>
                    <span class="truncate">Bedane Dhahar & Nedha</span>
                </a>
                <a href="#" class="flex items-center gap-2 px-3 py-2 rounded-xl text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors truncate">
                    <span class="material-symbols-outlined text-[16px] text-gray-400 shrink-0">chat_bubble_outline</span>
                    <span class="truncate">Penulisan Aksara Murda</span>
                </a>
                <a href="#" class="flex items-center gap-2 px-3 py-2 rounded-xl text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors truncate">
                    <span class="material-symbols-outlined text-[16px] text-gray-400 shrink-0">chat_bubble_outline</span>
                    <span class="truncate">Tegese Paribasan Becik Ketitik</span>
                </a>
                <a href="#" class="flex items-center gap-2 px-3 py-2 rounded-xl text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors truncate">
                    <span class="material-symbols-outlined text-[16px] text-gray-400 shrink-0">chat_bubble_outline</span>
                    <span class="truncate">Unggah-ungguh marang Guru</span>
                </a>
            </div>
        </div>
        @endif
    </div>

    <!-- User Profile Footer -->
    <div class="p-space-md border-t border-gray-100 shrink-0">
        <div class="flex items-center justify-between p-3 rounded-2xl bg-surface-container-low hover:bg-surface-container transition-colors">
            <div class="flex items-center gap-3 min-w-0">
                <div class="relative flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-primary-700 text-white flex items-center justify-center font-bold text-sm">
                        AP
                    </div>
                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-green-500 border-2 border-surface-container-lowest"></span>
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="font-heading text-body font-bold text-on-surface truncate">Andi Prasetyo</span>
                    <span class="font-caption text-caption text-on-surface-variant truncate">Kelas 7A • Siswa</span>
                </div>
            </div>
            <button type="button" aria-label="Menu Profil" class="w-8 h-8 rounded-full flex items-center justify-center text-on-surface-variant hover:bg-surface-container-high transition-colors flex-shrink-0">
                <span class="material-symbols-outlined text-[20px]">more_vert</span>
            </button>
        </div>
    </div>
</aside>
