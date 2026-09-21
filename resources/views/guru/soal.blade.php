@extends('layouts.admin')

@section('konten')
    <div class="flex flex-col gap-6 mt-6">
        {{-- Breadcrumb Navigasi (Teks Saja) --}}
        <nav class="flex items-center gap-2 font-caption text-caption text-gray-500 flex-wrap">
            <a href="{{ route('guru.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px] text-gray-400">chevron_right</span>
            <a href="{{ route('guru.topik') }}" class="hover:text-primary-600 transition-colors">Topik</a>
            <span class="material-symbols-outlined text-[14px] text-gray-400">chevron_right</span>
            <a href="{{ route('guru.level-materi', $level->topik_id ? ['topik_id' => $level->topik_id] : []) }}" class="hover:text-primary-600 transition-colors">Level Materi</a>
            <span class="material-symbols-outlined text-[14px] text-gray-400">chevron_right</span>
            <span class="text-on-surface font-bold text-primary-700">
                Level {{ $level->urutan }}: Bank Soal
            </span>
        </nav>

        {{-- Section: Toolbar (Filter, Search & Tambah Soal) --}}
        <section class="bg-surface-container-lowest rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col lg:flex-row lg:items-end justify-between gap-4">
            <form method="GET" class="flex flex-col sm:flex-row sm:items-end gap-3 flex-1">
                <input type="hidden" name="level_materi_id" value="{{ request('level_materi_id') }}">
                
                {{-- Input Pencarian Terpadu dengan Tombol Cari di dalamnya --}}
                <div class="flex flex-col gap-1.5 flex-1 max-w-sm">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Pencarian</span>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[20px] pointer-events-none">search</span>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari pertanyaan soal..."
                               class="w-full rounded-full border border-gray-200 bg-gray-50 pl-10 pr-24 py-2.5 font-body text-body text-gray-800 outline-none focus:border-primary-500 focus:bg-white transition-all shadow-xs">
                        @if (request('q'))
                            <a href="{{ route('guru.soal', array_filter(['level_materi_id' => $level->id, 'pembahasan_id' => request('pembahasan_id'), 'tipe_soal' => request('tipe_soal')])) }}"
                               title="Hapus pencarian"
                               class="absolute right-16 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 flex items-center justify-center p-1">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                            </a>
                        @endif
                        <button type="submit"
                                class="absolute right-1.5 top-1/2 -translate-y-1/2 flex items-center justify-center gap-1 rounded-full bg-primary-600 hover:bg-primary-700 text-white font-body text-xs font-bold px-3.5 py-1.5 shadow-xs transition-all">
                            <span>Cari</span>
                        </button>
                    </div>
                </div>

                {{-- Dropdown Filter Pembahasan --}}
                <div class="relative flex flex-col gap-1.5 sm:w-56" id="filter-pembahasan-wrapper">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Pembahasan</span>
                    <input type="hidden" name="pembahasan_id" id="filter-pembahasan-input" value="{{ request('pembahasan_id') }}">

                    @php
                        $selectedPembahasan = $pembahasanList->firstWhere('id', request('pembahasan_id'));
                    @endphp

                    <button type="button" id="btn-filter-pembahasan"
                        class="flex items-center justify-between w-full rounded-full border border-gray-200 bg-gray-50 hover:bg-gray-100 hover:border-primary-400 px-4 py-2.5 font-body text-body text-gray-700 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        <span id="filter-pembahasan-label" class="truncate font-medium text-gray-800">
                            {{ $selectedPembahasan ? $selectedPembahasan->urutan . '. ' . $selectedPembahasan->nama : 'Semua Pembahasan' }}
                        </span>
                        <span class="inline-flex items-center justify-center shrink-0 w-5 h-5 text-gray-400 ml-2">
                            <span id="filter-pembahasan-chevron" class="material-symbols-outlined text-[20px] leading-none transition-transform duration-200">expand_more</span>
                        </span>
                    </button>

                    {{-- Dropdown Menu Pembahasan --}}
                    <div id="filter-pembahasan-menu" class="hidden absolute top-full left-0 mt-2 w-full min-w-[240px] max-h-72 overflow-y-auto bg-white rounded-2xl shadow-xl border border-gray-100 py-1.5 z-50 divide-y divide-gray-100">
                        <button type="button" data-val="" class="filter-pembahasan-item w-full flex items-center justify-between px-4 py-2.5 text-left font-body text-body {{ request('pembahasan_id') == '' ? 'bg-primary-50 text-primary-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }} transition-colors">
                            <span>Semua Pembahasan</span>
                            @if(request('pembahasan_id') == '')
                                <span class="material-symbols-outlined text-[18px] text-primary-600">check</span>
                            @endif
                        </button>
                        @foreach ($pembahasanList as $pembahasan)
                            <button type="button" data-val="{{ $pembahasan->id }}" class="filter-pembahasan-item w-full flex items-center justify-between px-4 py-2.5 text-left font-body text-body {{ (string) request('pembahasan_id') === (string) $pembahasan->id ? 'bg-primary-50 text-primary-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }} transition-colors">
                                <span class="truncate">{{ $pembahasan->urutan }}. {{ $pembahasan->nama }}</span>
                                @if((string) request('pembahasan_id') === (string) $pembahasan->id)
                                    <span class="material-symbols-outlined text-[18px] text-primary-600 shrink-0 ml-2">check</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="relative flex flex-col gap-1.5 sm:w-52" id="filter-tipe-wrapper">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Tipe Soal</span>
                    <input type="hidden" name="tipe_soal" id="filter-tipe-input" value="{{ request('tipe_soal') }}">
                    
                    <button type="button" id="btn-filter-tipe"
                        class="flex items-center justify-between w-full rounded-full border border-gray-200 bg-gray-50 hover:bg-gray-100 hover:border-primary-400 px-4 py-2.5 font-body text-body text-gray-700 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        <span id="filter-tipe-label" class="truncate font-medium text-gray-800">
                            {{ $tipeList[request('tipe_soal')] ?? 'Semua Tipe' }}
                        </span>
                        <span class="inline-flex items-center justify-center shrink-0 w-5 h-5 text-gray-400 ml-2">
                            <span id="filter-tipe-chevron" class="material-symbols-outlined text-[20px] leading-none transition-transform duration-200">expand_more</span>
                        </span>
                    </button>

                    {{-- Dropdown Menu dengan Pembatas Antar Opsi --}}
                    <div id="filter-tipe-menu" class="hidden absolute top-full left-0 mt-2 w-full min-w-[220px] bg-white rounded-2xl shadow-xl border border-gray-100 py-1.5 z-50 overflow-hidden divide-y divide-gray-100">
                        <button type="button" data-val="" class="filter-tipe-item w-full flex items-center justify-between px-4 py-2.5 text-left font-body text-body {{ request('tipe_soal') == '' ? 'bg-primary-50 text-primary-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }} transition-colors">
                            <span>Semua Tipe</span>
                            @if(request('tipe_soal') == '')
                                <span class="material-symbols-outlined text-[18px] text-primary-600">check</span>
                            @endif
                        </button>
                        @foreach ($tipeList as $value => $label)
                            <button type="button" data-val="{{ $value }}" class="filter-tipe-item w-full flex items-center justify-between px-4 py-2.5 text-left font-body text-body {{ request('tipe_soal') === $value ? 'bg-primary-50 text-primary-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }} transition-colors">
                                <span>{{ $label }}</span>
                                @if(request('tipe_soal') === $value)
                                    <span class="material-symbols-outlined text-[18px] text-primary-600">check</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            </form>
            
            <a href="{{ route('guru.soal.create', array_filter(['level_materi_id' => $level->id, 'pembahasan_id' => request('pembahasan_id')])) }}" class="flex items-center justify-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-body font-bold px-6 py-2.5 shadow-sm transition-colors whitespace-nowrap">
                <span class="material-symbols-outlined text-[20px]">add_circle</span>
                <span>Tambah Soal</span>
            </a>
        </section>

        {{-- Section: Daftar Soal Dikelompokkan Berdasarkan Pembahasan --}}
        @if ($soalList->count() > 0)
            @php
                $groupedByPembahasan = $soalList->groupBy('pembahasan_id');
                // Pembahasan yang relevan untuk ditampilkan
                $displayPembahasans = request('pembahasan_id')
                    ? $pembahasanList->where('id', request('pembahasan_id'))
                    : $pembahasanList;
                $soalTanpaPembahasan = $groupedByPembahasan->get('', $groupedByPembahasan->get(null, collect()));
            @endphp

            <div class="flex flex-col gap-6">
                @if ($displayPembahasans->isNotEmpty())
                    @foreach ($displayPembahasans as $pembahasan)
                        @php
                            $soalInPembahasan = $groupedByPembahasan->get($pembahasan->id, collect());
                        @endphp

                        {{-- Jika filter teks pencarian atau filter tipe soal aktif dan tidak ada soal di pembahasan ini, lewati --}}
                        @if ((request('q') || request('tipe_soal')) && $soalInPembahasan->isEmpty())
                            @continue
                        @endif

                        <section class="bg-surface-container-lowest rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                            {{-- Header Pembahasan --}}
                            <div class="px-5 sm:px-6 py-4 bg-gradient-to-r from-gray-50/90 via-surface-container-low/40 to-white border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-start sm:items-center gap-3.5 min-w-0">
                                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-primary-600 to-primary-700 text-white flex items-center justify-center font-heading font-extrabold text-sm shrink-0 shadow-sm">
                                        {{ $pembahasan->urutan }}
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h3 class="font-heading font-bold text-on-surface text-base leading-snug">{{ $pembahasan->nama }}</h3>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-primary-50 text-primary-700 font-label-upper text-[11px] font-bold border border-primary-100">
                                                <span class="material-symbols-outlined text-[13px]">quiz</span>
                                                {{ $soalInPembahasan->count() }} Soal
                                            </span>
                                        </div>
                                        @if ($pembahasan->deskripsi)
                                            <p class="font-body text-xs text-gray-500 line-clamp-1 mt-0.5">{{ $pembahasan->deskripsi }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                                    <a href="{{ route('guru.soal.create', ['level_materi_id' => $level->id, 'pembahasan_id' => $pembahasan->id]) }}"
                                       title="Tambah butir soal untuk pembahasan {{ $pembahasan->nama }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-primary-600 hover:bg-primary-700 text-white font-body text-xs font-bold transition-all shadow-xs whitespace-nowrap">
                                        <span class="material-symbols-outlined text-[16px]">add_circle</span>
                                        <span>Tambah Soal</span>
                                    </a>
                                </div>
                            </div>

                            {{-- List Soal dalam Pembahasan --}}
                            @if ($soalInPembahasan->count() > 0)
                                <div class="divide-y divide-gray-100 font-body text-sm">
                                    @foreach ($soalInPembahasan as $s)
                                        <div class="p-4 sm:p-5 hover:bg-surface-container-low/30 transition-colors flex flex-col gap-3">
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                                {{-- Kiri: Nomor Urut & Info Soal --}}
                                                <div class="flex items-start gap-3.5 min-w-0 flex-1">
                                                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-gray-100 text-gray-700 flex items-center justify-center font-heading font-bold text-xs sm:text-sm shrink-0">
                                                        {{ $loop->iteration }}
                                                    </div>

                                                    <div class="flex flex-col min-w-0 flex-1">
                                                        <h4 class="font-heading font-bold text-on-surface text-sm sm:text-base leading-snug">
                                                            {{ $s->pertanyaan }}
                                                        </h4>

                                                        <div class="flex items-center gap-2.5 flex-wrap mt-2">
                                                            <span class="px-2.5 py-0.5 rounded-full bg-surface-container-high text-gray-700 font-label-upper text-[11px] font-bold uppercase">
                                                                {{ str_replace('_', ' ', strtoupper($s->tipe_soal)) }}
                                                            </span>
                                                            <span class="text-gray-300">•</span>
                                                            <span class="inline-flex items-center gap-1 font-caption text-xs font-bold text-amber-700">
                                                                <span class="material-symbols-outlined text-[14px] text-amber-500 icon-fill">bolt</span>
                                                                +{{ $s->bobot_exp }} EXP
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Kanan: Aksi Edit & Hapus --}}
                                                <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                                                    <a href="{{ route('guru.soal.edit', $s) }}"
                                                       title="Sunting Soal"
                                                       class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-primary-50 hover:bg-primary-600 text-primary-700 hover:text-white flex items-center justify-center transition-all shadow-xs border border-primary-100 shrink-0">
                                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                                    </a>

                                                    <form method="POST" action="{{ route('guru.soal.destroy', $s) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus butir soal ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                title="Hapus Soal"
                                                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-error-container/50 text-error flex items-center justify-center hover:bg-error-container transition-colors shadow-xs shrink-0">
                                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-8 text-center text-gray-400 font-body text-xs flex flex-col items-center justify-center gap-2.5">
                                    <span class="material-symbols-outlined text-gray-300 text-[28px]">quiz</span>
                                    <span>Belum ada butir soal untuk pembahasan ini.</span>
                                    <a href="{{ route('guru.soal.create', ['level_materi_id' => $level->id, 'pembahasan_id' => $pembahasan->id]) }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-primary-50 hover:bg-primary-100 text-primary-700 font-bold transition-colors">
                                        <span class="material-symbols-outlined text-[15px]">add</span>
                                        <span>Tambah Soal Sekarang</span>
                                    </a>
                                </div>
                            @endif
                        </section>
                    @endforeach
                @endif

                {{-- Soal Tanpa Pembahasan --}}
                @if ($soalTanpaPembahasan->isNotEmpty() && !request('pembahasan_id'))
                    <section class="bg-surface-container-lowest rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                        <div class="px-5 sm:px-6 py-4 bg-gray-50/80 border-b border-gray-100 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-gray-200 text-gray-600 flex items-center justify-center font-heading font-extrabold text-sm shrink-0">
                                    —
                                </div>
                                <div>
                                    <h3 class="font-heading font-bold text-on-surface text-base">Soal Tanpa Pembahasan</h3>
                                    <p class="font-body text-xs text-gray-500">{{ $soalTanpaPembahasan->count() }} Butir Soal (Umum)</p>
                                </div>
                            </div>
                        </div>
                        <div class="divide-y divide-gray-100 font-body text-sm">
                            @foreach ($soalTanpaPembahasan as $s)
                                <div class="p-4 sm:p-5 hover:bg-surface-container-low/30 transition-colors flex flex-col gap-3">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <div class="flex items-start gap-3.5 min-w-0 flex-1">
                                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-gray-100 text-gray-700 flex items-center justify-center font-heading font-bold text-xs sm:text-sm shrink-0">
                                                {{ $loop->iteration }}
                                            </div>
                                            <div class="flex flex-col min-w-0 flex-1">
                                                <h4 class="font-heading font-bold text-on-surface text-sm sm:text-base leading-snug">
                                                    {{ $s->pertanyaan }}
                                                </h4>
                                                <div class="flex items-center gap-2.5 flex-wrap mt-2">
                                                    <span class="px-2.5 py-0.5 rounded-full bg-surface-container-high text-gray-700 font-label-upper text-[11px] font-bold uppercase">
                                                        {{ str_replace('_', ' ', strtoupper($s->tipe_soal)) }}
                                                    </span>
                                                    <span class="text-gray-300">•</span>
                                                    <span class="inline-flex items-center gap-1 font-caption text-xs font-bold text-amber-700">
                                                        <span class="material-symbols-outlined text-[14px] text-amber-500 icon-fill">bolt</span>
                                                        +{{ $s->bobot_exp }} EXP
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                                            <a href="{{ route('guru.soal.edit', $s) }}"
                                               title="Sunting Soal"
                                               class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-primary-50 hover:bg-primary-600 text-primary-700 hover:text-white flex items-center justify-center transition-all shadow-xs border border-primary-100 shrink-0">
                                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                            </a>
                                            <form method="POST" action="{{ route('guru.soal.destroy', $s) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus butir soal ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        title="Hapus Soal"
                                                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-error-container/50 text-error flex items-center justify-center hover:bg-error-container transition-colors shadow-xs shrink-0">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Pagination Footer Terpadu --}}
                <div class="bg-surface-container-lowest rounded-2xl px-5 py-3.5 border border-gray-100 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="text-xs text-gray-500 font-caption">
                        Menampilkan <span class="font-bold text-on-surface">{{ $soalList->firstItem() ?? 0 }}</span> - <span class="font-bold text-on-surface">{{ $soalList->lastItem() ?? 0 }}</span> dari total <span class="font-bold text-on-surface">{{ $soalList->total() }}</span> butir soal
                    </div>
                    <div>
                        {{ $soalList->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="bg-surface-container-lowest rounded-3xl shadow-sm border border-gray-100 p-12 text-center text-gray-500">
                <div class="flex flex-col items-center justify-center gap-3">
                    <div class="w-14 h-14 rounded-2xl bg-surface-container-high text-gray-400 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[32px]">{{ request('q') || request('tipe_soal') ? 'search_off' : 'quiz' }}</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <h4 class="font-heading text-sm font-bold text-on-surface">
                            {{ request('q') || request('tipe_soal') ? 'Soal Tidak Ditemukan' : 'Belum Ada Butir Soal' }}
                        </h4>
                        <p class="font-body text-xs text-gray-500">
                            {{ request('q') || request('tipe_soal') ? 'Tidak ada butir soal yang sesuai dengan filter pencarian Anda.' : 'Belum ada butir soal terdaftar untuk level ini.' }}
                        </p>
                    </div>
                    <div class="pt-2">
                        @if (request('q') || request('tipe_soal'))
                            <a href="{{ route('guru.soal', ['level_materi_id' => $level->id]) }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-body text-xs font-bold transition-all">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                                <span>Reset Filter</span>
                            </a>
                        @else
                            <a href="{{ route('guru.soal.create', array_filter(['level_materi_id' => $level->id, 'pembahasan_id' => request('pembahasan_id')])) }}"
                               class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-primary-600 text-white font-body text-xs font-bold shadow-sm hover:bg-primary-700 transition-all">
                                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                                <span>Tambah Soal Anyar</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function setupCustomDropdown({ btnId, menuId, chevronId, inputId, itemClass }) {
                const btn = document.getElementById(btnId);
                const menu = document.getElementById(menuId);
                const chevron = document.getElementById(chevronId);
                const input = document.getElementById(inputId);
                const form = btn?.closest('form');

                if (!btn || !menu) return;

                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    // Tutup dropdown lain yang sedang terbuka
                    document.querySelectorAll('[id$="-menu"]').forEach(otherMenu => {
                        if (otherMenu !== menu) {
                            otherMenu.classList.add('hidden');
                            const otherId = otherMenu.id.replace('-menu', '');
                            document.getElementById(otherId + '-chevron')?.classList.remove('rotate-180');
                        }
                    });

                    const isHidden = menu.classList.toggle('hidden');
                    if (!isHidden) {
                        chevron?.classList.add('rotate-180');
                    } else {
                        chevron?.classList.remove('rotate-180');
                    }
                });

                menu.querySelectorAll('.' + itemClass).forEach(function (item) {
                    item.addEventListener('click', function () {
                        const val = this.getAttribute('data-val');
                        input.value = val;
                        menu.classList.add('hidden');
                        chevron?.classList.remove('rotate-180');
                        form?.submit();
                    });
                });
            }

            setupCustomDropdown({
                btnId: 'btn-filter-pembahasan',
                menuId: 'filter-pembahasan-menu',
                chevronId: 'filter-pembahasan-chevron',
                inputId: 'filter-pembahasan-input',
                itemClass: 'filter-pembahasan-item'
            });

            setupCustomDropdown({
                btnId: 'btn-filter-tipe',
                menuId: 'filter-tipe-menu',
                chevronId: 'filter-tipe-chevron',
                inputId: 'filter-tipe-input',
                itemClass: 'filter-tipe-item'
            });

            document.addEventListener('click', function (e) {
                const pembahasanWrapper = document.getElementById('filter-pembahasan-wrapper');
                const tipeWrapper = document.getElementById('filter-tipe-wrapper');

                if (pembahasanWrapper && !pembahasanWrapper.contains(e.target)) {
                    document.getElementById('filter-pembahasan-menu')?.classList.add('hidden');
                    document.getElementById('filter-pembahasan-chevron')?.classList.remove('rotate-180');
                }

                if (tipeWrapper && !tipeWrapper.contains(e.target)) {
                    document.getElementById('filter-tipe-menu')?.classList.add('hidden');
                    document.getElementById('filter-tipe-chevron')?.classList.remove('rotate-180');
                }
            });
        });
    </script>
@endsection
