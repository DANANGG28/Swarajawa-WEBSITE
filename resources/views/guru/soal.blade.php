@extends('layouts.admin')

@section('konten')
    <div class="flex flex-col gap-6">


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
            
            <a href="{{ route('guru.soal.create', ['level_materi_id' => $level->id]) }}" class="flex items-center justify-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-body font-bold px-6 py-2.5 shadow-sm transition-colors whitespace-nowrap">
                <span class="material-symbols-outlined text-[20px]">add_circle</span>
                <span>Tambah Soal</span>
            </a>
        </section>

        {{-- Section: Daftar Soal --}}
        <section class="flex flex-col gap-3">
            @forelse ($soalList as $s)
                <div class="bg-surface-container-lowest rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col gap-3">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                        <div class="flex flex-col min-w-0">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">
                                {{ str_replace('_', ' ', strtoupper($s->tipe_soal)) }}
                            </span>
                            <h3 class="font-heading text-heading font-bold text-on-surface mt-0.5">{{ $s->pertanyaan }}</h3>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-3 py-1.5 rounded-full bg-yellow-300/50 text-tertiary font-caption text-caption font-bold">+{{ $s->bobot_exp }} XP</span>
                            <form method="POST" action="{{ route('guru.soal.destroy', $s) }}" onsubmit="return confirm('Hapus soal ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-9 h-9 rounded-full bg-error-container/60 text-error flex items-center justify-center hover:bg-error-container transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    <details class="group">
                        <summary class="cursor-pointer list-none font-caption text-caption font-bold text-primary-600 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px] group-open:rotate-180 transition-transform">edit</span>
                            <span>Edit soal ini</span>
                        </summary>
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            @include('partials.soal-form', [
                                'action' => route('guru.soal.update', $s),
                                'levels' => $levels,
                                'tipeList' => $tipeList,
                                'soal' => $s,
                                'prefix' => 'edit-'.$s->id,
                            ])
                        </div>
                    </details>
                </div>
            @empty
                <div class="bg-surface-container-lowest rounded-2xl p-10 text-center border border-gray-100">
                    <span class="material-symbols-outlined text-[40px] text-gray-500">quiz</span>
                    <p class="font-body text-body text-gray-500 mt-2">Belum ada soal. Tambah soal baru di atas.</p>
                </div>
            @endforelse

            @if ($soalList->hasPages())
                <div>{{ $soalList->links() }}</div>
            @endif

            {{-- Tombol Kembali ke Pilihan Level (dipindah ke bawah) --}}
            <div class="flex justify-start mt-4">
                <a href="{{ route('guru.level-materi') }}" class="flex items-center gap-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-body text-body font-bold px-6 py-2.5 shadow-sm transition-colors">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    <span>Kembali ke Pilihan Level</span>
                </a>
            </div>
        </section>
    </div>

    @include('partials.soal-form-script')

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
