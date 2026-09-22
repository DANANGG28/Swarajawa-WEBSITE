@extends('layouts.admin')

@section('aksi')
    <a href="{{ route('guru.level-materi') }}"
       class="flex items-center gap-2 px-4 py-2.5 rounded-full bg-surface-container-low hover:bg-surface-container-high text-on-surface font-body text-body font-bold transition-all shadow-sm">
        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        <span>Kembali ke Level Materi</span>
    </a>
@endsection

@section('konten')
    <div class="flex flex-col gap-6 max-w-5xl mx-auto pt-4 pb-10">
        {{-- Breadcrumb Navigasi --}}
        <nav class="flex items-center gap-2 font-caption text-caption text-gray-500 flex-wrap">
            <a href="{{ route('guru.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px] text-gray-400">chevron_right</span>
            <a href="{{ route('guru.topik') }}" class="hover:text-primary-600 transition-colors">Topik</a>
            <span class="material-symbols-outlined text-[14px] text-gray-400">chevron_right</span>
            <a href="{{ route('guru.level-materi') }}" class="hover:text-primary-600 transition-colors">Level Materi</a>
            <span class="material-symbols-outlined text-[14px] text-gray-400">chevron_right</span>
            <span class="text-on-surface font-bold text-primary-700">Tambah Level Materi</span>
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
                            Guru Pengajar
                        </span>
                    </div>
                    <p class="font-body text-body text-white/90">
                        Buat unit atau level materi pembelajaran baru lengkap dengan urutan, nama materi, dan reward EXP.
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

            <form method="POST" action="{{ route('guru.level-materi.store') }}" class="flex flex-col gap-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Nama Materi --}}
                    <label class="flex flex-col gap-1.5 sm:col-span-2">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-700 font-semibold">
                            Nama Materi Pembelajaran <span class="text-error">*</span>
                        </span>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">menu_book</span>
                            <input type="text" name="nama_materi" value="{{ old('nama_materi') }}" required
                                   placeholder="Contoh: Aksara Jawa Dasar (Nglegena)"
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">
                        </div>
                        @error('nama_materi')
                            <span class="text-error text-xs font-caption">{{ $message }}</span>
                        @enderror
                    </label>

                    {{-- Pilihan Topik --}}
                    <label class="flex flex-col gap-1.5">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-700 font-semibold">
                            Topik (Bagian Pembelajaran)
                        </span>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">topic</span>
                            <select name="topik_id"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all cursor-pointer">
                                <option value="">-- Tanpa Topik (Umum) --</option>
                                @foreach (($topikList ?? collect()) as $topik)
                                    <option value="{{ $topik->id }}" @selected(old('topik_id', $selectedTopikId ?? request('topik_id')) == $topik->id)>
                                        Topik {{ $topik->urutan }}: {{ $topik->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('topik_id')
                            <span class="text-error text-xs font-caption">{{ $message }}</span>
                        @enderror
                    </label>

                    {{-- Urutan Level --}}
                    <label class="flex flex-col gap-1.5">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-700 font-semibold">
                            Nomor Urutan Level <span class="text-error">*</span>
                        </span>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">format_list_numbered</span>
                            <input type="number" name="urutan" value="{{ old('urutan', $nextUrutan ?? 1) }}" required min="1" step="1"
                                   inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">
                        </div>
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
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">bolt</span>
                            <input type="number" name="reward_exp" value="{{ old('reward_exp', 100) }}" required min="0" step="1"
                                   inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                   placeholder="100"
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">
                        </div>
                        @error('reward_exp')
                            <span class="text-error text-xs font-caption">{{ $message }}</span>
                        @enderror
                    </label>

                    {{-- Deskripsi Materi --}}
                    <label class="flex flex-col gap-1.5 sm:col-span-2">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-700 font-semibold">
                            Deskripsi Materi
                        </span>
                        <textarea name="deskripsi" rows="4"
                                  placeholder="Tuliskan deskripsi singkat atau kisi-kisi pembelajaran pada level ini..."
                                  class="w-full rounded-xl border border-gray-200 bg-gray-50 p-4 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <span class="text-error text-xs font-caption">{{ $message }}</span>
                        @enderror
                    </label>
                </div>

                {{-- Tombol Aksi --}}
                <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-end gap-3.5">
                    <a href="{{ route('guru.level-materi') }}"
                       class="w-full sm:w-auto px-7 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-body text-body font-bold text-center border-b-4 border-slate-300 active:border-b-0 active:translate-y-1 transition-all">
                        Batal
                    </a>
                    <button type="submit"
                            class="w-full sm:w-auto flex items-center justify-center gap-2 rounded-2xl bg-primary-600 hover:bg-primary-500 text-white font-body text-body font-bold px-8 py-3 shadow-md hover:shadow-lg border-b-4 border-primary-800 active:border-b-0 active:translate-y-1 transition-all">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        <span>Simpan Level Materi</span>
                    </button>
                </div>
            </form>
        </section>
    </div>
@endsection
