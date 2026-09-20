@extends('layouts.admin')
@use('App\Models\Soal')

@section('konten')
    <div class="flex flex-col gap-6 max-w-5xl mx-auto pt-2 pb-10 mt-4">
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
            <span class="text-on-surface font-bold text-primary-700">Sunting Soal</span>
        </nav>

        {{-- Section: Form Sunting Soal --}}
        <section class="bg-surface-container-lowest rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 mb-6 border-b border-gray-100">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-primary-50 text-primary-700 flex items-center justify-center border border-primary-100 shrink-0 shadow-xs">
                        <span class="material-symbols-outlined text-[24px]">edit_note</span>
                    </div>
                    <div>
                        <h3 class="font-heading text-lg font-bold text-on-surface">Sunting Butir Soal</h3>
                        <p class="font-body text-xs text-gray-500 mt-0.5">Perbarui teks pertanyaan, media, opsi jawaban, atau bobot EXP untuk level ini.</p>
                    </div>
                </div>

                <a href="{{ route('guru.soal', ['level_materi_id' => $level->id]) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-body text-xs font-semibold transition-colors shrink-0 self-start sm:self-center">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    <span>Kembali ke Daftar Soal</span>
                </a>
            </div>

            @include('partials.soal-form', [
                'action' => route('guru.soal.update', $soal),
                'levels' => $levels,
                'tipeList' => $tipeList,
                'soal' => $soal,
                'prefix' => 'edit-'.$soal->id,
                'ttsRoute' => route('guru.soal.tts'),
                'previewRoute' => route('guru.soal.preview'),
                'disableLevel' => true,
            ])
        </section>
    </div>

    @include('partials.soal-form-script')
@endsection
