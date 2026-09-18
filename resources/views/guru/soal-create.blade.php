@extends('layouts.admin')

@section('konten')
    <div class="flex flex-col gap-6">
        {{-- Breadcrumb: Kembali ke Daftar Soal --}}
        <div class="flex items-center gap-2">
            <a href="{{ route('guru.soal', ['level_materi_id' => $level->id]) }}" class="flex items-center gap-1 text-primary-600 hover:text-primary-700 font-body text-body font-semibold transition-colors">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                <span>Kembali ke Daftar Soal</span>
            </a>
        </div>

        {{-- Section: Tambah Soal Form --}}
        <section class="bg-surface-container-lowest rounded-2xl shadow-sm border border-gray-100">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary-600">add_circle</span>
                <h3 class="font-heading text-heading font-bold text-on-surface">Tambah Soal Baru</h3>
            </div>
            <div class="px-5 pb-6 pt-4">
                @include('partials.soal-form', [
                    'action' => route('guru.soal.store'),
                    'levels' => $levels,
                    'tipeList' => $tipeList,
                    'soal' => null,
                    'prefix' => 'create',
                ])
            </div>
        </section>
    </div>

    @include('partials.soal-form-script')
@endsection
