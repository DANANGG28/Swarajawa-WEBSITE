@extends('layouts.admin')

@section('aksi')
    <a href="{{ route('superadmin.level-materi') }}"
       class="flex items-center gap-2 px-4 py-2.5 rounded-full bg-surface-container-low hover:bg-surface-container-high text-on-surface font-body text-body font-bold transition-all shadow-sm">
        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        <span>Kembali ke Level Materi</span>
    </a>
@endsection

@section('konten')
    <div class="flex flex-col gap-6 max-w-5xl mx-auto pt-4 pb-10">
        {{-- Breadcrumb Navigasi --}}
        <nav class="flex items-center gap-2 font-caption text-caption text-gray-500">
            <a href="{{ route('superadmin.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a href="{{ route('superadmin.level-materi') }}" class="hover:text-primary-600 transition-colors">Level Materi</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface font-bold">Tambah Level Materi</span>
        </nav>

        {{-- Hero Header Banner --}}
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 p-6 sm:p-8 text-white shadow-lg">
            <div class="absolute -right-10 -bottom-10 w-64 h-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
            <div class="absolute right-40 -top-10 w-48 h-48 rounded-full bg-yellow-300/15 blur-xl pointer-events-none"></div>

            <div class="relative z-10 flex items-center gap-5">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl bg-white/20 backdrop-blur-md border-2 border-white/40 text-white flex items-center justify-center font-display font-extrabold text-2xl sm:text-3xl uppercase shadow-inner shrink-0">
                    <span class="material-symbols-outlined text-[36px] sm:text-[42px]">add_circle</span>
                </div>
                <div class="flex flex-col gap-1.5 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-white tracking-tight truncate">
                            Tambah Level Materi Baru
                        </h2>
                        <span class="px-3 py-1 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white font-label-upper text-[11px] font-bold tracking-wider uppercase">
                            Superadmin
                        </span>
                    </div>
                    <p class="font-body text-body text-white/90">
                        Buat level materi pembelajaran anyar kanthi urutan, jeneng materi, reward EXP, lan katrangan lengkap.
                    </p>
                </div>
            </div>
        </section>

        {{-- Card Formulir Pendaftaran Level Materi --}}
        <section class="bg-surface-container-lowest rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between pb-5 border-b border-gray-100 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[24px]">stairs</span>
                    </div>
                    <div>
                        <h3 class="font-heading text-heading font-bold text-on-surface">Formulir Data Level Materi</h3>
                        <p class="font-caption text-caption text-gray-500">Semua kolom bertanda (*) wajib diisi dengan benar</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('superadmin.level-materi.store') }}" class="flex flex-col gap-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Nama Materi --}}
                    <label class="flex flex-col gap-1.5 sm:col-span-2">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-700 font-semibold">
                            Nama Materi Pembelajaran <span class="text-error">*</span>
                        </span>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]">menu_book</span>
                            <input type="text" name="nama_materi" value="{{ old('nama_materi') }}" required
                                   placeholder="Contoh: Aksara Jawa Nglegena & Pasangan"
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-3 font-body text-sm outline-none focus:border-primary-500 focus:bg-white transition-all">
                        </div>
                        @error('nama_materi')
                            <span class="text-error text-xs font-caption">{{ $message }}</span>
                        @enderror
                    </label>

                    {{-- Urutan Level --}}
                    <label class="flex flex-col gap-1.5">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-700 font-semibold">
                            Nomor Urutan Level <span class="text-error">*</span>
                        </span>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]">format_list_numbered</span>
                            <input type="number" name="urutan" min="1" value="{{ old('urutan', $nextUrutan ?? 1) }}" required
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-3 font-body text-sm outline-none focus:border-primary-500 focus:bg-white transition-all">
                        </div>
                        <span class="font-caption text-xs text-gray-400">Urutan level menentukan posisi alur belajar siswa</span>
                        @error('urutan')
                            <span class="text-error text-xs font-caption">{{ $message }}</span>
                        @enderror
                    </label>

                    {{-- Reward EXP --}}
                    <label class="flex flex-col gap-1.5">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-700 font-semibold">
                            Reward EXP Siswa <span class="text-error">*</span>
                        </span>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-amber-500 text-[20px] icon-fill">bolt</span>
                            <input type="number" name="reward_exp" min="0" value="{{ old('reward_exp', 100) }}" required
                                   placeholder="100"
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-3 font-body text-sm outline-none focus:border-primary-500 focus:bg-white transition-all">
                        </div>
                        <span class="font-caption text-xs text-gray-400">Poin pengalaman yang didapatkan siswa setelah menyelesaikan level ini</span>
                        @error('reward_exp')
                            <span class="text-error text-xs font-caption">{{ $message }}</span>
                        @enderror
                    </label>

                    {{-- Deskripsi Materi --}}
                    <label class="flex flex-col gap-1.5 sm:col-span-2">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-700 font-semibold">
                            Deskripsi Materi Pembelajaran
                        </span>
                        <textarea name="deskripsi" rows="3"
                                  placeholder="Keterangan ringkas mengenai cakupan materi, kompetensi, atau petunjuk belajar siswa..."
                                  class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-sm outline-none focus:border-primary-500 focus:bg-white transition-all">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <span class="text-error text-xs font-caption">{{ $message }}</span>
                        @enderror
                    </label>
                </div>

                {{-- Tombol Aksi --}}
                <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-end gap-3">
                    <a href="{{ route('superadmin.level-materi') }}"
                       class="w-full sm:w-auto px-6 py-3 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-body text-sm font-bold text-center transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="w-full sm:w-auto flex items-center justify-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 text-white font-body text-sm font-bold px-8 py-3 shadow-md hover:shadow-lg transition-all">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        <span>Simpan Level Materi</span>
                    </button>
                </div>
            </form>
        </section>
    </div>
@endsection
