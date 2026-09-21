@extends('layouts.admin')

@section('konten')
    <div class="flex flex-col gap-6 mt-6">
        {{-- Breadcrumb Navigasi (Teks Saja) --}}
        <nav class="flex items-center gap-2 font-caption text-caption text-gray-500 flex-wrap">
            <a href="{{ route('guru.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px] text-gray-400">chevron_right</span>
            <a href="{{ route('guru.level-materi') }}" class="hover:text-primary-600 transition-colors">Level Materi</a>
            <span class="material-symbols-outlined text-[14px] text-gray-400">chevron_right</span>
            <a href="{{ route('guru.soal', ['level_materi_id' => $level->id]) }}" class="hover:text-primary-600 transition-colors">
                Level {{ $level->urutan }}: {{ $level->nama_materi }}
            </a>
            <span class="material-symbols-outlined text-[14px] text-gray-400">chevron_right</span>
            <span class="text-on-surface font-bold text-primary-700">
                @if (request('tipe_soal') && isset($tipeList[request('tipe_soal')]))
                    Soal Tipe {{ $tipeList[request('tipe_soal')] }}
                @else
                    Semua Tipe Soal
                @endif
            </span>
        </nav>

        {{-- Section: Toolbar (Filter, Search & Tambah Soal) --}}
        <section class="bg-surface-container-lowest rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col lg:flex-row lg:items-end justify-between gap-4">
            <form method="GET" class="flex flex-col sm:flex-row sm:items-end gap-3 flex-1">
                <input type="hidden" name="level_materi_id" value="{{ request('level_materi_id') }}">
                
                <label class="flex flex-col gap-1.5 flex-1 max-w-sm">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Pencarian</span>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span class="material-symbols-outlined text-gray-400 text-[20px]">search</span>
                        </div>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari pertanyaan soal..." class="w-full rounded-full border border-gray-200 bg-gray-50 pl-10 pr-4 py-2.5 font-body text-body outline-none focus:border-primary-500">
                    </div>
                </label>

                <button type="submit" class="flex items-center gap-2 rounded-full border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-700 font-body text-body font-bold px-6 py-2.5 shadow-sm transition-colors">
                    <span class="material-symbols-outlined text-[18px]">search</span> Cari
                </button>

                <label class="flex flex-col gap-1.5 sm:w-52">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Pembahasan</span>
                    <select name="pembahasan_id" onchange="this.form.submit()"
                        class="w-full rounded-full border border-gray-200 bg-gray-50 px-4 py-2.5 font-body text-body outline-none focus:border-primary-500">
                        <option value="">Semua Pembahasan</option>
                        @foreach ($pembahasanList as $pembahasan)
                            <option value="{{ $pembahasan->id }}" @selected((string) request('pembahasan_id') === (string) $pembahasan->id)>
                                {{ $pembahasan->urutan }}. {{ $pembahasan->nama }}
                            </option>
                        @endforeach
                    </select>
                </label>

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

        {{-- Section: Daftar Soal Terpadu dalam 1 Card --}}
        <section class="bg-surface-container-lowest rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            @if ($soalList->count() > 0)
                <div class="divide-y divide-gray-100 font-body text-sm">
                    @foreach ($soalList as $s)
                        @php
                            $nomor = ($soalList->currentPage() - 1) * $soalList->perPage() + $loop->iteration;
                        @endphp
                        <div class="p-4 sm:p-5 hover:bg-surface-container-low/30 transition-colors flex flex-col gap-3">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                {{-- Kiri: Nomor Urut & Info Soal --}}
                                <div class="flex items-start gap-3.5 min-w-0 flex-1">
                                    {{-- Badge Nomor Urut --}}
                                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-gradient-to-br from-primary-600 to-primary-700 text-white flex items-center justify-center font-heading font-extrabold text-sm sm:text-base shrink-0 shadow-sm">
                                        {{ $nomor }}
                                    </div>

                                    <div class="flex flex-col min-w-0 flex-1">
                                        <h3 class="font-heading font-bold text-on-surface text-sm sm:text-base leading-snug">
                                            {{ $s->pertanyaan }}
                                        </h3>

                                        <div class="flex items-center gap-2.5 flex-wrap mt-2">
                                            <span class="px-2.5 py-0.5 rounded-full bg-surface-container-high text-gray-700 font-label-upper text-[11px] font-bold uppercase">
                                                {{ str_replace('_', ' ', strtoupper($s->tipe_soal)) }}
                                            </span>
                                            @if ($s->pembahasan)
                                                <span class="text-gray-300">•</span>
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-primary-50 text-primary-700 font-label-upper text-[11px] font-bold uppercase">
                                                    <span class="material-symbols-outlined text-[13px]">topic</span>
                                                    {{ $s->pembahasan->nama }}
                                                </span>
                                            @endif
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
                                    {{-- Tombol Edit Soal (Buka Halaman Sunting Baru) --}}
                                    <a href="{{ route('guru.soal.edit', $s) }}"
                                       title="Sunting Soal #{{ $nomor }}"
                                       class="w-10 h-10 rounded-full bg-primary-50 hover:bg-primary-600 text-primary-700 hover:text-white flex items-center justify-center transition-all shadow-xs border border-primary-100 shrink-0">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>

                                    {{-- Tombol Hapus Soal --}}
                                    <form method="POST" action="{{ route('guru.soal.destroy', $s) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus butir soal nomor {{ $nomor }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                title="Hapus Soal #{{ $nomor }}"
                                                class="w-10 h-10 rounded-full bg-error-container/50 text-error flex items-center justify-center hover:bg-error-container transition-colors shadow-xs shrink-0">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination Footer Terpadu di Bagian Bawah Card --}}
                <div class="px-5 py-3.5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 bg-surface-container-low/20">
                    <div class="text-xs text-gray-500 font-caption">
                        Menampilkan <span class="font-bold text-on-surface">{{ $soalList->firstItem() ?? 0 }}</span> - <span class="font-bold text-on-surface">{{ $soalList->lastItem() ?? 0 }}</span> dari total <span class="font-bold text-on-surface">{{ $soalList->total() }}</span> butir soal
                    </div>
                    <div>
                        {{ $soalList->links() }}
                    </div>
                </div>
            @else
                <div class="p-12 text-center text-gray-500">
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
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('btn-filter-tipe');
            const menu = document.getElementById('filter-tipe-menu');
            const chevron = document.getElementById('filter-tipe-chevron');
            const input = document.getElementById('filter-tipe-input');
            const form = btn?.closest('form');

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

                menu.querySelectorAll('.filter-tipe-item').forEach(function (item) {
                    item.addEventListener('click', function () {
                        const val = this.getAttribute('data-val');
                        input.value = val;
                        menu.classList.add('hidden');
                        chevron.classList.remove('rotate-180');
                        form.submit();
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
@endsection
