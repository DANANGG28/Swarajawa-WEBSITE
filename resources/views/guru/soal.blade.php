@extends('layouts.admin')

@section('konten')
    <div class="flex flex-col gap-6">
        {{-- Breadcrumb: Kembali ke Pilihan Level --}}
        <div class="flex items-center gap-2">
            <a href="{{ route('guru.level-materi') }}" class="flex items-center gap-1 text-primary-600 hover:text-primary-700 font-body text-body font-semibold transition-colors">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                <span>Kembali ke Pilihan Level</span>
            </a>
        </div>

        {{-- Section: Tambah Soal Anyar --}}
        <section class="bg-surface-container-lowest rounded-2xl shadow-sm border border-gray-100">
            <details class="group">
                <summary class="cursor-pointer list-none px-5 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary-600">add_circle</span>
                        <span class="font-heading text-heading font-bold text-on-surface">Tambah Soal Anyar</span>
                    </div>
                    <span class="material-symbols-outlined text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                </summary>
                <div class="px-5 pb-6 pt-2 border-t border-gray-100">
                    @include('partials.soal-form', [
                        'action' => route('guru.soal.store'),
                        'levels' => $levels,
                        'tipeList' => $tipeList,
                        'prefix' => 'create',
                    ])
                </div>
            </details>
        </section>

        {{-- Section: Filter Tipe Soal (opsional, tanpa level filter) --}}
        <section class="bg-surface-container-lowest rounded-2xl p-5 shadow-sm border border-gray-100">
            <form method="GET" class="flex flex-col sm:flex-row sm:items-end gap-3">
                <input type="hidden" name="level_materi_id" value="{{ request('level_materi_id') }}">
                
                <label class="flex flex-col gap-1.5 sm:w-56">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Tipe Soal</span>
                    <select name="tipe_soal" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 font-body text-body outline-none focus:border-primary-500">
                        <option value="">Kabeh Tipe</option>
                        @foreach ($tipeList as $value => $label)
                            <option value="{{ $value }}" @selected(request('tipe_soal') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <button type="submit" class="flex items-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-body font-bold px-6 py-2.5 shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">filter_alt</span> Saring
                </button>
            </form>
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
                            <form method="POST" action="{{ route('guru.soal.destroy', $s) }}" onsubmit="return confirm('Busak soal iki?')">
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
        </section>
    </div>

    @include('partials.soal-form-script')
@endsection
