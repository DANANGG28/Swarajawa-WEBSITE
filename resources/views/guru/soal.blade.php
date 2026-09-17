@extends('layouts.admin')

@section('konten')
    {{-- Custom styles for dropdown --}}
    <style>
        .dropdown-wrapper {
            position: relative;
        }
        .dropdown-trigger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            width: 100%;
            padding: 10px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #f9fafb;
            font-size: inherit;
            cursor: pointer;
            transition: all 0.15s ease;
            user-select: none;
        }
        .dropdown-trigger:hover {
            border-color: #d1d5db;
            background: #f3f4f6;
        }
        .dropdown-trigger:focus,
        .dropdown-trigger.active {
            border-color: var(--color-primary-500, #7c3aed);
            box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.15);
            outline: none;
        }
        .dropdown-trigger .chevron-icon {
            transition: transform 0.2s ease;
            font-size: 18px;
            color: #6b7280;
            flex-shrink: 0;
        }
        .dropdown-trigger.active .chevron-icon {
            transform: rotate(180deg);
        }
        .dropdown-menu {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            z-index: 50;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
            max-height: 240px;
            overflow-y: auto;
            display: none;
        }
        .dropdown-menu.open {
            display: block;
            animation: dropdownFadeIn 0.15s ease;
        }
        @keyframes dropdownFadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .dropdown-item {
            padding: 10px 16px;
            cursor: pointer;
            transition: background 0.1s ease;
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
            color: #374151;
        }
        .dropdown-item:last-child {
            border-bottom: none;
        }
        .dropdown-item:hover {
            background: #f5f3ff;
        }
        .dropdown-item.selected {
            background: #ede9fe;
            color: #5b21b6;
            font-weight: 600;
        }
        .dropdown-menu::-webkit-scrollbar {
            width: 6px;
        }
        .dropdown-menu::-webkit-scrollbar-track {
            background: transparent;
        }
        .dropdown-menu::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }

        /* Keep custom-select for form partial selects */
        .custom-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 36px;
        }
        .custom-select::-ms-expand {
            display: none;
        }
    </style>

    <div class="flex flex-col gap-6">


        {{-- Section: Toolbar (Filter, Search & Tambah Soal) --}}
        <section class="bg-surface-container-lowest rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
                <form method="GET" id="filter-form" class="flex flex-col sm:flex-row sm:items-end gap-4 flex-1">
                    <input type="hidden" name="level_materi_id" value="{{ request('level_materi_id') }}">
                    <input type="hidden" name="tipe_soal" id="tipe-soal-hidden" value="{{ request('tipe_soal') }}">
                    
                    {{-- Pencarian --}}
                    <div class="flex flex-col gap-1.5 flex-1 min-w-0">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Pencarian</span>
                        <div class="flex items-stretch gap-2">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <span class="material-symbols-outlined text-gray-400 text-[20px]">search</span>
                                </div>
                                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari pertanyaan soal..." class="w-full h-full rounded-lg border border-gray-200 bg-gray-50 pl-10 pr-4 py-2.5 font-body text-body outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500/30 transition-all">
                            </div>
                            <button type="submit" class="flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-700 font-body text-body font-bold px-5 py-2.5 shadow-sm transition-colors">
                                <span class="material-symbols-outlined text-[18px]">search</span> Cari
                            </button>
                        </div>
                    </div>

                    {{-- Tipe Soal - Custom Dropdown --}}
                    <div class="flex flex-col gap-1.5 sm:w-56">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Tipe Soal</span>
                        <div class="dropdown-wrapper" id="tipe-soal-dropdown">
                            <div class="dropdown-trigger font-body text-body" tabindex="0" id="tipe-soal-trigger">
                                <span id="tipe-soal-label">{{ request('tipe_soal') ? ($tipeList[request('tipe_soal')] ?? 'Kabeh Tipe') : 'Kabeh Tipe' }}</span>
                                <span class="material-symbols-outlined chevron-icon">expand_more</span>
                            </div>
                            <div class="dropdown-menu" id="tipe-soal-menu">
                                <div class="dropdown-item {{ !request('tipe_soal') ? 'selected' : '' }}" data-value="">Kabeh Tipe</div>
                                @foreach ($tipeList as $value => $label)
                                    <div class="dropdown-item {{ request('tipe_soal') === $value ? 'selected' : '' }}" data-value="{{ $value }}">{{ $label }}</div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </form>
                
                {{-- Tambah Soal --}}
                <a href="{{ route('guru.soal.create', ['level_materi_id' => $level->id]) }}" class="flex items-center justify-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-body font-bold px-6 py-2.5 shadow-sm transition-colors whitespace-nowrap hover:shadow-md">
                    <span class="material-symbols-outlined text-[20px]">add_circle</span>
                    <span>Tambah Soal</span>
                </a>
            </div>
        </section>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const trigger = document.getElementById('tipe-soal-trigger');
        const menu = document.getElementById('tipe-soal-menu');
        const label = document.getElementById('tipe-soal-label');
        const hidden = document.getElementById('tipe-soal-hidden');
        const form = document.getElementById('filter-form');

        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            const isOpen = menu.classList.contains('open');
            menu.classList.toggle('open');
            trigger.classList.toggle('active');
        });

        trigger.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                trigger.click();
            }
        });

        menu.querySelectorAll('.dropdown-item').forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                const value = this.getAttribute('data-value');
                const text = this.textContent.trim();

                // Update UI
                label.textContent = text;
                menu.querySelectorAll('.dropdown-item').forEach(el => el.classList.remove('selected'));
                this.classList.add('selected');

                // Close dropdown
                menu.classList.remove('open');
                trigger.classList.remove('active');

                // Submit form
                hidden.value = value;
                form.submit();
            });
        });

        // Close on outside click
        document.addEventListener('click', function() {
            menu.classList.remove('open');
            trigger.classList.remove('active');
        });
    });
    </script>

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
                            <span class="px-3 py-1.5 rounded-lg bg-yellow-300/50 text-tertiary font-caption text-caption font-bold">+{{ $s->bobot_exp }} XP</span>
                            <form method="POST" action="{{ route('guru.soal.destroy', $s) }}" onsubmit="return confirm('Busak soal iki?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-9 h-9 rounded-lg bg-error-container/60 text-error flex items-center justify-center hover:bg-error-container transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    <details class="group">
                        <summary class="cursor-pointer list-none font-caption text-caption font-bold text-primary-600 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px] group-open:rotate-180 transition-transform">edit</span>
                            <span>Sunting soal iki</span>
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
                    <p class="font-body text-body text-gray-500 mt-2">Durung ana soal. Tambah soal anyar ing dhuwur.</p>
                </div>
            @endforelse

            @if ($soalList->hasPages())
                <div>{{ $soalList->links() }}</div>
            @endif

            {{-- Tombol Kembali ke Pilihan Level (dipindah ke bawah) --}}
            <div class="flex justify-start mt-4">
                <a href="{{ route('guru.level-materi') }}" class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-body text-body font-bold px-6 py-2.5 shadow-sm transition-colors">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    <span>Kembali ke Pilihan Level</span>
                </a>
            </div>
        </section>
    </div>

    @include('partials.soal-form-script')
@endsection
