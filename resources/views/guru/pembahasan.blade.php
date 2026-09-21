@extends('layouts.admin')

@section('konten')
    <div class="flex flex-col gap-6 max-w-5xl mx-auto pt-2 pb-10 mt-4">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 font-caption text-caption text-gray-500 flex-wrap">
            <a href="{{ route('guru.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px] text-gray-400">chevron_right</span>
            <a href="{{ route('guru.level-materi') }}" class="hover:text-primary-600 transition-colors">Level Materi</a>
            <span class="material-symbols-outlined text-[14px] text-gray-400">chevron_right</span>
            <a href="{{ route('guru.soal', ['level_materi_id' => $level->id]) }}" class="hover:text-primary-600 transition-colors">
                Level {{ $level->urutan }}: {{ $level->nama_materi }}
            </a>
            <span class="material-symbols-outlined text-[14px] text-gray-400">chevron_right</span>
            <span class="text-on-surface font-bold text-primary-700">Kelola Pembahasan</span>
        </nav>

        {{-- Header Level --}}
        <section class="bg-surface-container-lowest rounded-3xl shadow-sm border border-gray-100 p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5 min-w-0">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary-600 to-primary-700 text-white flex items-center justify-center font-heading font-extrabold text-lg shrink-0 shadow-sm">
                    {{ $level->urutan }}
                </div>
                <div class="min-w-0">
                    <h2 class="font-heading text-lg font-bold text-on-surface leading-snug">Pembahasan Level {{ $level->urutan }} — {{ $level->nama_materi }}</h2>
                    <p class="font-body text-xs text-gray-500 mt-0.5 leading-relaxed">
                        Kelola sub-materi (pembahasan) sebelum menambahkan butir soal. Setiap pembahasan berisi kumpulan soal tersendiri.
                    </p>
                </div>
            </div>
            <a href="{{ route('guru.level-materi') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-body text-xs font-semibold transition-colors shrink-0 self-start sm:self-center">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                <span>Kembali</span>
            </a>
        </section>

        {{-- Form Tambah Pembahasan --}}
        <section class="bg-surface-container-lowest rounded-3xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-gray-100">
                <div class="w-10 h-10 rounded-2xl bg-primary-50 text-primary-700 flex items-center justify-center border border-primary-100 shrink-0">
                    <span class="material-symbols-outlined text-[22px]">add_circle</span>
                </div>
                <div>
                    <h3 class="font-heading text-base font-bold text-on-surface">Tambah Pembahasan Baru</h3>
                    <p class="font-body text-xs text-gray-500 mt-0.5">Tentukan nama, urutan, dan deskripsi singkat pembahasan.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('guru.pembahasan.store') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                @csrf
                <input type="hidden" name="level_materi_id" value="{{ $level->id }}">

                <label class="md:col-span-6 flex flex-col gap-1.5">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Nama Pembahasan</span>
                    <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="cth: Salam & Sapaan"
                           class="rounded-full border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                </label>

                <label class="md:col-span-2 flex flex-col gap-1.5">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Urutan</span>
                    <input type="number" name="urutan" min="0" value="{{ old('urutan', $pembahasanList->max('urutan') + 1) }}" required
                           class="rounded-full border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                </label>

                <label class="md:col-span-4 flex flex-col gap-1.5">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Deskripsi (Opsional)</span>
                    <input type="text" name="deskripsi" value="{{ old('deskripsi') }}" placeholder="Ringkasan singkat"
                           class="rounded-full border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                </label>

                <div class="md:col-span-12 flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-body font-bold px-6 py-3 shadow-sm transition-colors">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        <span>Simpan Pembahasan</span>
                    </button>
                </div>
            </form>
        </section>

        {{-- Daftar Pembahasan --}}
        <section class="bg-surface-container-lowest rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            @if ($pembahasanList->count() > 0)
                <div class="divide-y divide-gray-100 font-body text-sm">
                    @foreach ($pembahasanList as $pembahasan)
                        <div class="p-4 sm:p-5 hover:bg-surface-container-low/30 transition-colors flex flex-col gap-3">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="flex items-start gap-3.5 min-w-0 flex-1">
                                    <div class="w-10 h-10 rounded-2xl bg-primary-50 text-primary-700 flex items-center justify-center font-heading font-extrabold text-sm shrink-0 border border-primary-100">
                                        {{ $pembahasan->urutan }}
                                    </div>
                                    <div class="flex flex-col min-w-0 flex-1">
                                        <h3 class="font-heading font-bold text-on-surface text-sm sm:text-base leading-snug">{{ $pembahasan->nama }}</h3>
                                        @if ($pembahasan->deskripsi)
                                            <p class="font-body text-xs text-gray-500 mt-1 line-clamp-2 leading-relaxed">{{ $pembahasan->deskripsi }}</p>
                                        @endif
                                        <div class="flex items-center gap-3 text-xs text-gray-400 font-caption mt-2 flex-wrap">
                                            <span class="flex items-center gap-1 font-semibold text-primary-700">{{ $pembahasan->soal_count }} Soal Terdaftar</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0 self-end md:self-center pt-2 md:pt-0 border-t md:border-t-0 border-gray-100 w-full md:w-auto justify-end flex-wrap">
                                    <a href="{{ route('guru.soal', ['level_materi_id' => $level->id, 'pembahasan_id' => $pembahasan->id]) }}"
                                       title="Lihat dan kelola soal pada pembahasan ini"
                                       class="flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-primary-50 hover:bg-primary-600 text-primary-700 hover:text-white font-body text-xs font-bold transition-all border border-primary-100 shadow-xs whitespace-nowrap">
                                        <span class="material-symbols-outlined text-[16px]">quiz</span>
                                        <span>Kelola Soal</span>
                                    </a>

                                    <a href="{{ route('guru.soal.create', ['level_materi_id' => $level->id, 'pembahasan_id' => $pembahasan->id]) }}"
                                       title="Tambah soal baru pada pembahasan ini"
                                       class="flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-primary-600 hover:bg-primary-700 text-white font-body text-xs font-bold transition-all shadow-xs whitespace-nowrap">
                                        <span class="material-symbols-outlined text-[16px]">add_circle</span>
                                        <span>Tambah Soal</span>
                                    </a>

                                    <button type="button" data-toggle-edit="edit-pembahasan-{{ $pembahasan->id }}"
                                            title="Sunting pembahasan"
                                            class="w-9 h-9 rounded-full bg-surface-container-high hover:bg-primary-600 text-on-surface hover:text-white flex items-center justify-center transition-all shadow-xs shrink-0">
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                    </button>

                                    <form method="POST" action="{{ route('guru.pembahasan.destroy', $pembahasan) }}"
                                          onsubmit="return confirm('Hapus pembahasan {{ addslashes($pembahasan->nama) }}?\n\nSoal pada pembahasan ini tidak ikut terhapus, hanya kehilangan pembahasan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus pembahasan"
                                                class="w-9 h-9 rounded-full bg-error-container/50 text-error flex items-center justify-center hover:bg-error-container transition-colors shadow-xs shrink-0">
                                            <span class="material-symbols-outlined text-[16px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- Form Edit (disembunyikan) --}}
                            <div id="edit-pembahasan-{{ $pembahasan->id }}" class="hidden border-t border-gray-100 pt-4">
                                <form method="POST" action="{{ route('guru.pembahasan.update', $pembahasan) }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                    @csrf
                                    @method('PUT')
                                    <label class="md:col-span-6 flex flex-col gap-1.5">
                                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Nama Pembahasan</span>
                                        <input type="text" name="nama" value="{{ $pembahasan->nama }}" required
                                               class="rounded-full border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                                    </label>
                                    <label class="md:col-span-2 flex flex-col gap-1.5">
                                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Urutan</span>
                                        <input type="number" name="urutan" min="0" value="{{ $pembahasan->urutan }}" required
                                               class="rounded-full border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                                    </label>
                                    <label class="md:col-span-4 flex flex-col gap-1.5">
                                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Deskripsi</span>
                                        <input type="text" name="deskripsi" value="{{ $pembahasan->deskripsi }}"
                                               class="rounded-full border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                                    </label>
                                    <div class="md:col-span-12 flex justify-end gap-2">
                                        <button type="button" data-toggle-edit="edit-pembahasan-{{ $pembahasan->id }}"
                                                class="rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-body text-xs font-bold px-5 py-2.5 transition-colors">
                                            Batal
                                        </button>
                                        <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-xs font-bold px-5 py-2.5 shadow-sm transition-colors">
                                            <span class="material-symbols-outlined text-[18px]">save</span>
                                            <span>Simpan Perubahan</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center gap-3">
                        <div class="w-14 h-14 rounded-2xl bg-surface-container-high text-gray-400 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[32px]">topic</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <h4 class="font-heading text-sm font-bold text-on-surface">Belum Ada Pembahasan</h4>
                            <p class="font-body text-xs text-gray-500">Tambahkan pembahasan pertama untuk level ini melalui formulir di atas.</p>
                        </div>
                    </div>
                </div>
            @endif
        </section>
    </div>

    <script>
        document.querySelectorAll('[data-toggle-edit]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const target = document.getElementById(this.getAttribute('data-toggle-edit'));
                if (target) target.classList.toggle('hidden');
            });
        });
    </script>
@endsection
