@extends('layouts.admin')

@section('konten')
    <div class="flex flex-col gap-6 max-w-3xl mx-auto pt-2 pb-10 mt-4">
        <nav class="flex items-center gap-2 font-caption text-caption text-gray-500 flex-wrap">
            <a href="{{ route('superadmin.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px] text-gray-400">chevron_right</span>
            <a href="{{ route('superadmin.topik') }}" class="hover:text-primary-600 transition-colors">Topik</a>
            <span class="material-symbols-outlined text-[14px] text-gray-400">chevron_right</span>
            <span class="text-on-surface font-bold text-primary-700">Tambah Topik</span>
        </nav>

        <section class="bg-surface-container-lowest rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
            <div class="flex items-center gap-3.5 pb-5 mb-6 border-b border-gray-100">
                <div class="w-12 h-12 rounded-2xl bg-primary-50 text-primary-700 flex items-center justify-center border border-primary-100 shrink-0">
                    <span class="material-symbols-outlined text-[24px]">topic</span>
                </div>
                <div>
                    <h3 class="font-heading text-lg font-bold text-on-surface">Tambah Topik Baru</h3>
                    <p class="font-body text-xs text-gray-500 mt-0.5">Topik dadi kelompok paling dhuwur; saben topik bisa ngemot pirang-pirang unit.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('superadmin.topik.store') }}" class="flex flex-col gap-5">
                @csrf

                <label class="flex flex-col gap-1.5">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-700 font-semibold">Nama Topik <span class="text-error">*</span></span>
                    <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Contoh: Basa Jawa Saben Dina"
                           class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-sm outline-none focus:border-primary-500 focus:bg-white transition-all">
                    @error('nama')<span class="text-error text-xs font-caption">{{ $message }}</span>@enderror
                </label>

                <label class="flex flex-col gap-1.5">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-700 font-semibold">Nomor Urutan <span class="text-error">*</span></span>
                    <input type="number" name="urutan" min="0" value="{{ old('urutan', $nextUrutan ?? 1) }}" required
                           class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-sm outline-none focus:border-primary-500 focus:bg-white transition-all">
                    @error('urutan')<span class="text-error text-xs font-caption">{{ $message }}</span>@enderror
                </label>

                <label class="flex flex-col gap-1.5">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-700 font-semibold">Deskripsi</span>
                    <textarea name="deskripsi" rows="3" placeholder="Katerangan ringkes babagan topik iki..."
                              class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-sm outline-none focus:border-primary-500 focus:bg-white transition-all">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')<span class="text-error text-xs font-caption">{{ $message }}</span>@enderror
                </label>

                <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-end gap-3">
                    <a href="{{ route('superadmin.topik') }}"
                       class="w-full sm:w-auto px-6 py-3 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-body text-sm font-bold text-center transition-colors">Batal</a>
                    <button type="submit"
                            class="w-full sm:w-auto px-6 py-3 rounded-full bg-primary-600 hover:bg-primary-700 text-white font-body text-sm font-bold shadow-sm transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        <span>Simpan Topik</span>
                    </button>
                </div>
            </form>
        </section>
    </div>
@endsection
