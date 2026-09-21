@extends('layouts.admin')

@section('konten')
    <div class="flex flex-col gap-6 max-w-7xl mx-auto pt-2 pb-10">
        {{-- Toolbar: Pencarian & Tambah Topik --}}
        <section class="bg-surface-container-lowest rounded-3xl p-4 sm:p-5 shadow-sm border border-gray-100 flex flex-col sm:flex-row items-stretch sm:items-center gap-3.5">
            <form method="GET" action="{{ route('superadmin.topik') }}" class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]">search</span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama topik atau deskripsi..."
                       class="w-full rounded-2xl border border-gray-200 bg-gray-50 pl-11 pr-10 py-2.5 font-body text-sm outline-none focus:border-primary-500 focus:bg-white transition-all">
                @if (request('q'))
                    <a href="{{ route('superadmin.topik') }}" title="Hapus pencarian"
                       class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </a>
                @endif
            </form>

            <a href="{{ route('superadmin.topik.create') }}"
               class="flex items-center justify-center gap-2 px-6 py-2.5 rounded-2xl sm:rounded-full bg-primary-600 hover:bg-primary-700 text-white font-body text-sm font-bold shadow-sm hover:shadow-md transition-all whitespace-nowrap shrink-0">
                <span class="material-symbols-outlined text-[19px]">add_circle</span>
                <span>Tambah Topik</span>
            </a>
        </section>

        {{-- Daftar Topik --}}
        <section class="bg-surface-container-lowest rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            @if ($topikList->count() > 0)
                <div class="divide-y divide-gray-100 font-body text-sm">
                    @foreach ($topikList as $topik)
                        <div class="p-4 sm:p-5 hover:bg-surface-container-low/40 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex items-start gap-3.5 min-w-0 flex-1">
                                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-gradient-to-br from-primary-600 to-primary-700 text-white flex items-center justify-center font-heading font-extrabold text-sm sm:text-base shrink-0 shadow-sm">
                                    {{ $topik->urutan }}
                                </div>
                                <div class="flex flex-col min-w-0 flex-1">
                                    <h3 class="font-heading font-bold text-on-surface text-sm sm:text-base leading-snug">{{ $topik->nama }}</h3>
                                    @if ($topik->deskripsi)
                                        <p class="font-body text-xs text-gray-500 mt-1 line-clamp-2 leading-relaxed">{{ $topik->deskripsi }}</p>
                                    @endif
                                    <div class="flex items-center gap-3 text-xs text-gray-400 font-caption mt-2 flex-wrap">
                                        <span class="flex items-center gap-1 font-semibold text-primary-700">{{ $topik->units_count }} Unit</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0 self-end md:self-center pt-2 md:pt-0 border-t md:border-t-0 border-gray-100 w-full md:w-auto justify-end flex-wrap">
                                <a href="{{ route('superadmin.level-materi', ['topik_id' => $topik->id]) }}"
                                   title="Kelola unit pada topik ini"
                                   class="flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-primary-50 hover:bg-primary-600 text-primary-700 hover:text-white font-body text-xs font-bold transition-all border border-primary-100 shadow-xs whitespace-nowrap">
                                    <span class="material-symbols-outlined text-[16px]">stairs</span>
                                    <span>Kelola Unit</span>
                                </a>

                                <a href="{{ route('superadmin.topik.edit', $topik) }}"
                                   title="Sunting topik"
                                   class="w-8 h-8 rounded-full bg-surface-container-high hover:bg-primary-600 text-on-surface hover:text-white flex items-center justify-center transition-all shadow-xs shrink-0">
                                    <span class="material-symbols-outlined text-[16px]">edit</span>
                                </a>

                                <form method="POST" action="{{ route('superadmin.topik.destroy', $topik) }}"
                                      onsubmit="return confirm('Hapus topik {{ addslashes($topik->nama) }}?\n\nUnit sing ana ing topik iki ora bakal kehapus, mung ilang topike.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus topik"
                                            class="w-8 h-8 rounded-full bg-error-container/50 text-error flex items-center justify-center hover:bg-error-container transition-colors shadow-xs shrink-0">
                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($topikList->hasPages())
                    <div class="px-5 py-3.5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 bg-surface-container-low/20">
                        <div class="text-xs text-gray-500 font-caption">
                            Menampilkan <span class="font-bold text-on-surface">{{ $topikList->firstItem() ?? 0 }}</span> - <span class="font-bold text-on-surface">{{ $topikList->lastItem() ?? 0 }}</span> dari total <span class="font-bold text-on-surface">{{ $topikList->total() }}</span> topik
                        </div>
                        <div>{{ $topikList->links() }}</div>
                    </div>
                @endif
            @else
                <div class="p-12 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center gap-3">
                        <div class="w-14 h-14 rounded-2xl bg-surface-container-high text-gray-400 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[32px]">{{ request('q') ? 'search_off' : 'topic' }}</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <h4 class="font-heading text-sm font-bold text-on-surface">{{ request('q') ? 'Topik Tidak Ditemukan' : 'Belum Ada Topik' }}</h4>
                            <p class="font-body text-xs text-gray-500">{{ request('q') ? 'Tidak ada topik yang sesuai dengan kata kunci.' : 'Gawe topik kapisan kanggo ngelompokake unit pembelajaran.' }}</p>
                        </div>
                        @if (! request('q'))
                            <a href="{{ route('superadmin.topik.create') }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-primary-600 text-white font-body text-xs font-bold shadow-sm hover:bg-primary-700 transition-all">
                                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                                <span>Tambah Topik Sekarang</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </section>
    </div>
@endsection
