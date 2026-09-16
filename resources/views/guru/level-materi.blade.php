@extends('layouts.admin')

@section('konten')
    <div class="flex flex-col gap-6">
        <section>

            @if ($levels->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($levels as $level)
                        <a href="{{ route('guru.soal', ['level_materi_id' => $level->id]) }}"
                            class="group bg-surface-container-lowest rounded-2xl p-6 shadow-sm border border-gray-100 hover:border-primary-500 hover:shadow-md transition-all duration-200 flex flex-col gap-3">
                            
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-primary-600 font-bold">
                                        Level {{ $level->urutan }}
                                    </span>
                                    <h3 class="font-heading text-heading font-bold text-on-surface mt-1">
                                        {{ $level->nama_materi }}
                                    </h3>
                                </div>
                                <span class="material-symbols-outlined text-primary-600 group-hover:translate-x-1 transition-transform">
                                    arrow_forward
                                </span>
                            </div>

                            @if ($level->deskripsi)
                                <p class="font-body text-body text-on-surface-variant line-clamp-2">
                                    {{ $level->deskripsi }}
                                </p>
                            @endif

                            <div class="flex items-center gap-3 pt-2">
                                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary-100">
                                    <span class="material-symbols-outlined text-[16px] text-primary-600">quiz</span>
                                    <span class="font-body text-body font-bold text-primary-600">
                                        {{ $level->soal_count }} {{ $level->soal_count === 1 ? 'soal' : 'soal' }}
                                    </span>
                                </div>

                                @if ($level->reward_exp > 0)
                                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-yellow-300/50">
                                        <span class="material-symbols-outlined text-[16px] text-tertiary">star</span>
                                        <span class="font-caption text-caption font-bold text-tertiary">
                                            +{{ $level->reward_exp }} XP
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="bg-surface-container-lowest rounded-2xl p-10 text-center border border-gray-100">
                    <span class="material-symbols-outlined text-[40px] text-gray-500">layers</span>
                    <p class="font-body text-body text-gray-500 mt-2">Durung ana level materi. Hubungi superadmin kanggo nggawe level.</p>
                </div>
            @endif
        </section>
    </div>
@endsection
