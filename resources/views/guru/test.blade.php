@extends('layouts.admin')

@section('konten')
    <div class="flex flex-col gap-6">
        <section class="bg-surface-container-lowest rounded-2xl shadow-sm border border-gray-100">
            <details class="group">
                <summary class="cursor-pointer list-none px-5 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary-600">playlist_add</span>
                        <span class="font-heading text-heading font-bold text-on-surface">Susun Paket Test Anyar</span>
                    </div>
                    <span class="material-symbols-outlined text-gray-500 group-open:rotate-180 transition-transform">expand_more</span>
                </summary>
                <form method="POST" action="{{ route('guru.test.store') }}" class="px-5 pb-6 pt-2 border-t border-gray-100 flex flex-col gap-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="flex flex-col gap-1.5">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Nama Test</span>
                            <input type="text" name="nama_test" required class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500" placeholder="Latihan Campuran Level 1">
                        </label>
                        <label class="flex flex-col gap-1.5">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Level Materi</span>
                            <select name="level_materi_id" required class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                                @foreach ($levels as $level)
                                    <option value="{{ $level->id }}">Level {{ $level->urutan }} — {{ $level->nama_materi }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                    <label class="flex flex-col gap-1.5">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Deskripsi</span>
                        <textarea name="deskripsi" rows="2" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500"></textarea>
                    </label>

                    <div class="flex flex-col gap-2">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Pilih Soal (boleh campur tipe_soal — FR-23)</span>
                        <div class="max-h-80 overflow-y-auto rounded-xl border border-gray-100 divide-y divide-gray-100">
                            @forelse ($soalList as $s)
                                <label class="flex items-start gap-3 px-4 py-3 hover:bg-surface-container-low/50 cursor-pointer">
                                    <input type="checkbox" name="soal_ids[]" value="{{ $s->id }}" class="mt-1 rounded border-gray-300">
                                    <span class="flex flex-col min-w-0">
                                        <span class="font-body text-body font-semibold text-on-surface">{{ $s->pertanyaan }}</span>
                                        <span class="font-caption text-caption text-gray-500">Level {{ $s->levelMateri?->urutan }} • {{ $tipeList[$s->tipe_soal] ?? $s->tipe_soal }} • +{{ $s->bobot_exp }} XP</span>
                                    </span>
                                </label>
                            @empty
                                <p class="px-4 py-6 text-center font-body text-body text-gray-500">Durung ana soal. Gawe soal dhisik ing menu Manajemen Soal.</p>
                            @endforelse
                        </div>
                    </div>

                    <button type="submit" class="self-start flex items-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-body font-bold px-6 py-3 shadow-sm" @disabled($soalList->isEmpty())>
                        <span class="material-symbols-outlined text-[20px]">save</span> Simpan Paket Test
                    </button>
                </form>
            </details>
        </section>

        <section class="bg-surface-container-lowest rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-heading text-heading font-bold text-on-surface">Paket Test Anda</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 border-b border-gray-100">
                            <th class="px-5 py-3">Nama Test</th>
                            <th class="px-5 py-3">Level</th>
                            <th class="px-5 py-3">Jumlah Soal</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-body text-body">
                        @forelse ($testList as $t)
                            <tr class="hover:bg-surface-container-low/40 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-on-surface">{{ $t->nama_test }}</span>
                                        <span class="font-caption text-caption text-gray-500">{{ $t->deskripsi }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-on-surface-variant">Level {{ $t->levelMateri?->urutan }} — {{ $t->levelMateri?->nama_materi }}</td>
                                <td class="px-5 py-3.5"><span class="px-3 py-1 rounded-full bg-primary-fixed text-primary-700 font-caption text-caption font-bold">{{ $t->soal_count }} soal</span></td>
                                <td class="px-5 py-3.5 text-right">
                                    <form method="POST" action="{{ route('guru.test.destroy', $t) }}" onsubmit="return confirm('Busak paket test iki?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-9 h-9 rounded-full bg-error-container/60 text-error inline-flex items-center justify-center hover:bg-error-container transition-colors">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-12 text-center text-gray-500 font-body text-body">Durung ana paket test.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
