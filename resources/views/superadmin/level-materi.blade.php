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
                        <input type="text" name="nama_materi" required class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex flex-col gap-1.5">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Reward EXP</span>
                            <input type="number" name="reward_exp" min="0" value="100" required class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                        </label>
                        <label class="flex flex-col gap-1.5">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Urutan</span>
                            <input type="number" name="urutan" min="0" value="{{ ($levelList->max('urutan') ?? 0) + 1 }}" required class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                        </label>
                    </div>
                    <label class="flex flex-col gap-1.5 sm:col-span-2">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Deskripsi</span>
                        <textarea name="deskripsi" rows="2" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500"></textarea>
                    </label>
                    <button type="submit" class="sm:col-span-2 self-start flex items-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-body font-bold px-6 py-3 shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">save</span> Simpan Level
                    </button>
                </form>
            </details>
        </section>

        <section class="flex flex-col gap-3">
            @foreach ($levelList as $level)
                <div class="bg-surface-container-lowest rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-11 h-11 rounded-2xl bg-primary-600 text-white flex items-center justify-center font-heading font-extrabold shrink-0">{{ $level->urutan }}</div>
                            <div class="flex flex-col min-w-0">
                                <span class="font-heading text-heading font-bold text-on-surface truncate">{{ $level->nama_materi }}</span>
                                <span class="font-caption text-caption text-gray-500">{{ $level->deskripsi }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-3 py-1.5 rounded-full bg-yellow-300/50 text-tertiary font-caption text-caption font-bold">+{{ $level->reward_exp }} XP</span>
                            <span class="px-3 py-1.5 rounded-full bg-primary-fixed text-primary-700 font-caption text-caption font-bold">{{ $level->soal_count }} soal • {{ $level->test_count }} test</span>
                            <form method="POST" action="{{ route('superadmin.level-materi.destroy', $level) }}" onsubmit="return confirm('Busak level materi iki? Kabeh soal lan test ing level iki uga bakal kebusak.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-9 h-9 rounded-full bg-error-container/60 text-error flex items-center justify-center hover:bg-error-container transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    <details class="group mt-3">
                        <summary class="cursor-pointer list-none font-caption text-caption font-bold text-primary-600 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px] group-open:rotate-180 transition-transform">edit</span> Sunting level
                        </summary>
                        <form method="POST" action="{{ route('superadmin.level-materi.update', $level) }}" class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @csrf @method('PUT')
                            <label class="flex flex-col gap-1.5">
                                <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Nama Materi</span>
                                <input type="text" name="nama_materi" value="{{ $level->nama_materi }}" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 font-body text-body outline-none focus:border-primary-500">
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex flex-col gap-1.5">
                                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Reward EXP</span>
                                    <input type="number" name="reward_exp" min="0" value="{{ $level->reward_exp }}" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 font-body text-body outline-none focus:border-primary-500">
                                </label>
                                <label class="flex flex-col gap-1.5">
                                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Urutan</span>
                                    <input type="number" name="urutan" min="0" value="{{ $level->urutan }}" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 font-body text-body outline-none focus:border-primary-500">
                                </label>
                            </div>
                            <label class="flex flex-col gap-1.5 sm:col-span-2">
                                <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Deskripsi</span>
                                <textarea name="deskripsi" rows="2" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 font-body text-body outline-none focus:border-primary-500">{{ $level->deskripsi }}</textarea>
                            </label>
                            <button type="submit" class="sm:col-span-2 self-start flex items-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-body font-bold px-5 py-2.5 shadow-sm">
                                <span class="material-symbols-outlined text-[18px]">save</span> Simpan Perubahan
                            </button>
                        </form>
                    </details>
                </div>
            @endforeach
        </section>
    </div>
@endsection
