@extends('layouts.admin')

@section('konten')
    <div class="flex flex-col gap-6">
        <section class="bg-surface-container-lowest rounded-2xl shadow-sm border border-gray-100">
            <details class="group">
                <summary class="cursor-pointer list-none px-5 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary-600">add_circle</span>
                        <span class="font-heading text-heading font-bold text-on-surface">Tambah Level Materi Anyar</span>
                    </div>
                    <span class="material-symbols-outlined text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                </summary>
                <form method="POST" action="{{ route('superadmin.level-materi.store') }}" class="px-5 pb-6 pt-2 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @csrf
                    <label class="flex flex-col gap-1.5">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Nama Materi</span>
                        <input type="text" name="nama_materi" required placeholder="Tuladha: Basa Krama Alus" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex flex-col gap-1.5">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Reward EXP</span>
                            <input type="number" name="reward_exp" min="0" value="100" required class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                        </label>
                        <label class="flex flex-col gap-1.5">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Urutan Level</span>
                            <input type="number" name="urutan" min="0" value="{{ ($levels->max('urutan') ?? 0) + 1 }}" required class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                        </label>
                    </div>
                    <label class="flex flex-col gap-1.5 sm:col-span-2">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Deskripsi Pasinaon</span>
                        <textarea name="deskripsi" rows="2" placeholder="Katrangan ringkes ngenani materi pasinaon iki..." class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500"></textarea>
                    </label>
                    <button type="submit" class="sm:col-span-2 self-start flex items-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-body font-bold px-6 py-3 shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">save</span> Simpan Level Materi
                    </button>
                </form>
            </details>
        </section>

        <section>
            @if ($levels->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($levels as $level)
                        <a href="{{ route('superadmin.soal', ['level_materi_id' => $level->id]) }}"
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
                    <p class="font-body text-body text-gray-500 mt-2">Durung ana level materi. Tambah level anyar ing dhuwur.</p>
                </div>
            @endif
        </section>
    </div>
@endsection
