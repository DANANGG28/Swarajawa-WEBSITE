@extends('layouts.admin')

@section('konten')
    <div class="flex flex-col gap-6 max-w-7xl mx-auto pt-2 pb-10 mt-4">
        {{-- Breadcrumb Navigasi --}}
        @php
            $currentTopik = ! empty($filterTopikId) ? ($topikList ?? collect())->firstWhere('id', $filterTopikId) : null;
        @endphp
        <nav class="flex items-center gap-2 font-caption text-caption text-gray-500 flex-wrap">
            <a href="{{ route('superadmin.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px] text-gray-400">chevron_right</span>
            <a href="{{ route('superadmin.topik') }}" class="hover:text-primary-600 transition-colors">Topik</a>
            <span class="material-symbols-outlined text-[14px] text-gray-400">chevron_right</span>
            @if ($currentTopik)
                <span class="text-gray-500 font-medium">{{ $currentTopik->nama }}</span>
                <span class="material-symbols-outlined text-[14px] text-gray-400">chevron_right</span>
                <span class="text-on-surface font-bold text-primary-700">Level Materi</span>
            @else
                <span class="text-on-surface font-bold text-primary-700">Level Materi</span>
            @endif
        </nav>

        {{-- Toolbar: Filter Pencarian, Filter Topik (Custom Dropdown), & Tombol Tambah Level Materi --}}
        <section class="bg-surface-container-lowest rounded-3xl p-4 sm:p-5 shadow-sm border border-gray-100">
            <form method="GET" action="{{ route('guru.level-materi') }}" id="filter-level-form" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3.5">
                {{-- Form Input Pencarian Level Materi --}}
                <div class="relative flex-1">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]">search</span>
                    <input type="text" name="q" value="{{ request('q') }}" id="searchLevelInput" oninput="filterLevels()" placeholder="Cari nama materi atau deskripsi level..."
                           class="w-full rounded-2xl border border-gray-200 bg-gray-50 pl-11 pr-10 py-2.5 font-body text-sm outline-none focus:border-primary-500 focus:bg-white transition-all">
                    @if (request('q'))
                        <a href="{{ route('guru.level-materi', request('topik_id') ? ['topik_id' => request('topik_id')] : []) }}" title="Hapus pencarian"
                           class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </a>
                    @endif
                </div>

                {{-- Filter Topik: Custom Dropdown Modern & Rapi --}}
                <div class="relative sm:w-72 shrink-0" id="filter-topik-wrapper">
                    <input type="hidden" name="topik_id" id="filter-topik-input" value="{{ request('topik_id') }}">

                    <button type="button" id="btn-filter-topik"
                            class="flex items-center justify-between w-full rounded-2xl border border-gray-200 bg-gray-50 hover:bg-white hover:border-primary-400 px-4 py-2.5 font-body text-sm text-gray-700 shadow-2xs transition-all focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 cursor-pointer">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-6 h-6 rounded-lg bg-primary-100/70 text-primary-700 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[16px]">topic</span>
                            </div>
                            <span id="filter-topik-label" class="truncate font-medium text-gray-800">
                                @if ($currentTopik)
                                    Topik {{ $currentTopik->urutan }}: {{ $currentTopik->nama }}
                                @else
                                    Semua Topik
                                @endif
                            </span>
                        </div>
                        <span class="inline-flex items-center justify-center shrink-0 w-5 h-5 text-gray-400 ml-2">
                            <span id="filter-topik-chevron" class="material-symbols-outlined text-[20px] leading-none transition-transform duration-200">expand_more</span>
                        </span>
                    </button>

                    {{-- Dropdown Menu Popover --}}
                    <div id="filter-topik-menu" class="hidden absolute top-full left-0 sm:left-auto sm:right-0 mt-2 w-full min-w-[280px] sm:min-w-[320px] bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50 divide-y divide-gray-100 max-h-80 overflow-y-auto">
                        {{-- Opsi: Semua Topik --}}
                        <div class="px-1.5 pb-1">
                            <button type="button" data-val="" class="filter-topik-item w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-left font-body text-sm {{ request('topik_id') == '' ? 'bg-primary-50 text-primary-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }} transition-colors cursor-pointer">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-6 h-6 rounded-lg {{ request('topik_id') == '' ? 'bg-primary-200/60 text-primary-800' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-[16px]">layers</span>
                                    </div>
                                    <span>Semua Topik</span>
                                </div>
                                @if(request('topik_id') == '')
                                    <span class="material-symbols-outlined text-[18px] text-primary-600 shrink-0">check</span>
                                @endif
                            </button>
                        </div>

                        {{-- Loop Opsi Topik --}}
                        <div class="px-1.5 pt-1 space-y-0.5">
                            @forelse (($topikList ?? collect()) as $topik)
                                <button type="button" data-val="{{ $topik->id }}" class="filter-topik-item w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-left font-body text-sm {{ (string) request('topik_id') === (string) $topik->id ? 'bg-primary-50 text-primary-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }} transition-colors cursor-pointer">
                                    <div class="flex items-center gap-2.5 min-w-0 pr-2">
                                        <div class="w-6 h-6 rounded-lg {{ (string) request('topik_id') === (string) $topik->id ? 'bg-primary-200 text-primary-800' : 'bg-gray-100 text-gray-600' }} flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ $topik->urutan }}
                                        </div>
                                        <span class="truncate">{{ $topik->nama }}</span>
                                    </div>
                                    @if((string) request('topik_id') === (string) $topik->id)
                                        <span class="material-symbols-outlined text-[18px] text-primary-600 shrink-0">check</span>
                                    @endif
                                </button>
                            @empty
                                <div class="px-3 py-3 text-center text-xs text-gray-400 font-body">
                                    Belum ada data topik.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Tombol Tambah Level Materi --}}
                <a href="{{ route('guru.level-materi.create', request('topik_id') ? ['topik_id' => request('topik_id')] : []) }}"
                   class="flex items-center justify-center gap-2 px-6 py-2.5 rounded-2xl sm:rounded-full bg-primary-600 hover:bg-primary-700 text-white font-body text-sm font-bold shadow-sm hover:shadow-md transition-all whitespace-nowrap shrink-0 border-b-2 border-primary-800 active:border-b-0 active:translate-y-0.5">
                    <span class="material-symbols-outlined text-[19px]">add_circle</span>
                    <span>Tambah Level Materi</span>
                </a>
            </form>
        </section>

        @if (! empty($filterTopikId))
            @php $filterTopik = ($topikList ?? collect())->firstWhere('id', $filterTopikId); @endphp
            <div class="flex items-center gap-2 text-xs font-body">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-primary-50 text-primary-700 font-bold border border-primary-100">
                    <span class="material-symbols-outlined text-[16px]">topic</span>
                    Unit dari topik: {{ $filterTopik?->nama ?? '#'.$filterTopikId }}
                </span>
                <a href="{{ route('guru.level-materi') }}" class="inline-flex items-center gap-1 text-gray-500 hover:text-primary-600 font-semibold">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                    Reset Filter Topik
                </a>
            </div>
        @endif

        {{-- Daftar List Level Materi --}}
        <section class="bg-surface-container-lowest rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            @if ($levels->count() > 0)
                <div id="levelListContainer" class="divide-y divide-gray-100 font-body text-sm">
                    @foreach ($levels as $level)
                        <div class="level-item p-4 sm:p-5 hover:bg-surface-container-low/40 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4"
                             data-search="{{ strtolower($level->nama_materi . ' ' . $level->deskripsi . ' level ' . $level->urutan) }}">
                            
                            {{-- Info Level: Urutan, Nama Materi, Deskripsi, Meta --}}
                            <div class="flex items-start gap-3.5 min-w-0 flex-1">
                                {{-- Badge Nomor Urutan Level --}}
                                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-gradient-to-br from-primary-600 to-primary-700 text-white flex items-center justify-center font-heading font-extrabold text-sm sm:text-base shrink-0 uppercase shadow-sm">
                                    {{ $level->urutan }}
                                </div>

                                <div class="flex flex-col min-w-0 flex-1">
                                    <h3 class="font-heading font-bold text-on-surface text-sm sm:text-base leading-snug">
                                        {{ $level->nama_materi }}
                                    </h3>

                                    @if ($level->deskripsi)
                                        <p class="font-body text-xs text-gray-500 mt-1 line-clamp-2 leading-relaxed">
                                            {{ $level->deskripsi }}
                                        </p>
                                    @endif

                                    <div class="flex items-center gap-3 text-xs text-gray-400 font-caption mt-2 flex-wrap">
                                        @if ($level->topik)
                                            <span class="flex items-center gap-1 font-semibold text-primary-700">
                                                <span class="material-symbols-outlined text-[15px]">topic</span>
                                                {{ $level->topik->nama }}
                                            </span>
                                            <span class="text-gray-300">•</span>
                                        @endif
                                        {{-- Jumlah Soal Terdaftar --}}
                                        <span class="flex items-center gap-1 font-semibold text-primary-700">
                                            {{ $level->soal_count }} Soal Terdaftar
                                        </span>
                                        <span class="text-gray-300">•</span>
                                        {{-- Jumlah Pembahasan --}}
                                        <span class="flex items-center gap-1 font-semibold text-secondary">
                                            {{ $level->pembahasan_count }} Pembahasan
                                        </span>
                                        <span class="text-gray-300">•</span>
                                        {{-- Reward EXP --}}
                                        <span class="flex items-center gap-1 font-semibold text-amber-700">
                                            <span class="material-symbols-outlined text-[15px] text-amber-500 icon-fill">bolt</span>
                                            +{{ number_format($level->reward_exp) }} EXP
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Aksi Guru: Kelola Pembahasan, Kelola Soal, Tambah Soal, Edit, Hapus --}}
                            <div class="flex items-center gap-2 shrink-0 self-end md:self-center pt-2 md:pt-0 border-t md:border-t-0 border-gray-100 w-full md:w-auto justify-end flex-wrap">
                                {{-- Tombol Kelola Pembahasan --}}
                                <a href="{{ route('guru.pembahasan', ['level_materi_id' => $level->id]) }}"
                                   title="Kelola pembahasan (sub-materi) level ini"
                                   class="flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-surface-container-high hover:bg-primary-600 text-on-surface hover:text-white font-body text-xs font-bold transition-all shadow-xs whitespace-nowrap">
                                    <span class="material-symbols-outlined text-[16px]">topic</span>
                                    <span>Kelola Pembahasan</span>
                                </a>

                                {{-- Tombol Kelola Soal --}}
                                <!-- <a href="{{ route('guru.soal', ['level_materi_id' => $level->id]) }}"
                                   title="Lihat dan kelola bank soal pada level ini"
                                   class="flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-primary-50 hover:bg-primary-600 text-primary-700 hover:text-white font-body text-xs font-bold transition-all border border-primary-100 shadow-xs whitespace-nowrap">
                                    <span class="material-symbols-outlined text-[16px]">quiz</span>
                                    <span>Kelola Soal</span>
                                </a> -->

                                {{-- Tombol Edit Level --}}
                                <a href="{{ route('guru.level-materi.edit', $level) }}"
                                   title="Sunting Level Materi"
                                   class="w-8 h-8 rounded-full bg-surface-container-high hover:bg-primary-600 text-on-surface hover:text-white flex items-center justify-center transition-all shadow-xs shrink-0">
                                    <span class="material-symbols-outlined text-[16px]">edit</span>
                                </a>

                                {{-- Tombol Hapus Level --}}
                                <form method="POST" action="{{ route('guru.level-materi.destroy', $level) }}"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus Level {{ $level->urutan }}: {{ addslashes($level->nama_materi) }}?\n\nPerhatian: Semua soal yang terkait dengan level ini juga akan dihapus!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus Level Materi"
                                            class="w-8 h-8 rounded-full bg-error-container/50 text-error flex items-center justify-center hover:bg-error-container transition-colors shadow-xs shrink-0">
                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- State ketika pencarian tidak menemukan hasil di halaman aktif --}}
                <div id="noSearchMatch" class="p-12 text-center text-gray-500 hidden">
                    <span class="material-symbols-outlined text-[36px] text-gray-400">search_off</span>
                    <p class="font-body text-sm text-gray-600 font-semibold mt-2">Level materi tidak ditemukan pada halaman ini</p>
                    <p class="font-caption text-xs text-gray-400 mt-0.5">Tekan tombol <strong>Enter</strong> untuk mencari ke seluruh data level materi.</p>
                </div>

                @if ($levels instanceof \Illuminate\Pagination\LengthAwarePaginator && $levels->hasPages())
                    {{-- Pagination Footer --}}
                    <div class="px-5 py-3.5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 bg-surface-container-low/20">
                        <div class="text-xs text-gray-500 font-caption">
                            Menampilkan <span class="font-bold text-on-surface">{{ $levels->firstItem() ?? 0 }}</span> - <span class="font-bold text-on-surface">{{ $levels->lastItem() ?? 0 }}</span> dari total <span class="font-bold text-on-surface">{{ $levels->total() }}</span> level
                        </div>
                        <div>
                            {{ $levels->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="p-12 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center gap-3">
                        <div class="w-14 h-14 rounded-2xl bg-surface-container-high text-gray-400 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[32px]">{{ request('q') ? 'search_off' : 'layers_clear' }}</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <h4 class="font-heading text-sm font-bold text-on-surface">
                                {{ request('q') ? 'Level Materi Tidak Ditemukan' : 'Belum Ada Level Materi' }}
                            </h4>
                            <p class="font-body text-xs text-gray-500">
                                {{ request('q') ? 'Tidak ada level materi yang sesuai dengan kata kunci "' . request('q') . '".' : 'Belum ada level pembelajaran yang didaftarkan.' }}
                            </p>
                        </div>
                        @if (request('q'))
                            <div class="pt-2">
                                <a href="{{ route('guru.level-materi', request('topik_id') ? ['topik_id' => request('topik_id')] : []) }}"
                                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-body text-xs font-bold transition-all">
                                    <span class="material-symbols-outlined text-[18px]">close</span>
                                    <span>Reset Pencarian</span>
                                </a>
                            </div>
                        @else
                            <div class="pt-2">
                                <a href="{{ route('guru.level-materi.create', request('topik_id') ? ['topik_id' => request('topik_id')] : []) }}"
                                   class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-primary-600 hover:bg-primary-700 text-white font-body text-xs font-bold shadow-sm transition-all">
                                    <span class="material-symbols-outlined text-[18px]">add_circle</span>
                                    <span>Tambah Level Materi Sekarang</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Dropdown Filter Topik Interaktif
            const btn = document.getElementById('btn-filter-topik');
            const menu = document.getElementById('filter-topik-menu');
            const chevron = document.getElementById('filter-topik-chevron');
            const input = document.getElementById('filter-topik-input');
            const form = document.getElementById('filter-level-form');

            if (btn && menu) {
                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const isHidden = menu.classList.toggle('hidden');
                    if (!isHidden) {
                        chevron.classList.add('rotate-180');
                    } else {
                        chevron.classList.remove('rotate-180');
                    }
                });

                menu.querySelectorAll('.filter-topik-item').forEach(function (item) {
                    item.addEventListener('click', function () {
                        const val = this.getAttribute('data-val');
                        if (input) input.value = val;
                        menu.classList.add('hidden');
                        chevron.classList.remove('rotate-180');
                        if (form) form.submit();
                    });
                });

                document.addEventListener('click', function (e) {
                    if (!btn.contains(e.target) && !menu.contains(e.target)) {
                        menu.classList.add('hidden');
                        chevron.classList.remove('rotate-180');
                    }
                });

                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape' && !menu.classList.contains('hidden')) {
                        menu.classList.add('hidden');
                        chevron.classList.remove('rotate-180');
                    }
                });
            }
        });

        function filterLevels() {
            const query = document.getElementById('searchLevelInput').value.toLowerCase().trim();
            const items = document.querySelectorAll('.level-item');
            const noMatch = document.getElementById('noSearchMatch');
            let visibleCount = 0;

            items.forEach(item => {
                const text = item.getAttribute('data-search') || '';
                if (query === '' || text.includes(query)) {
                    item.classList.remove('hidden');
                    visibleCount++;
                } else {
                    item.classList.add('hidden');
                }
            });

            if (noMatch) {
                if (visibleCount === 0 && items.length > 0) {
                    noMatch.classList.remove('hidden');
                } else {
                    noMatch.classList.add('hidden');
                }
            }
        }
    </script>
@endsection
