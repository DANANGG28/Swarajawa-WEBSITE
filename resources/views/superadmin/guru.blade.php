@extends('layouts.admin')

@section('aksi')
    <a href="{{ route('superadmin.guru.create') }}"
       class="flex items-center gap-2 px-5 py-2.5 rounded-full bg-primary-600 hover:bg-primary-700 text-white font-body text-body font-bold shadow-md hover:shadow-lg transition-all">
        <span class="material-symbols-outlined text-[20px]">person_add</span>
        <span>Daftarake Guru Anyar</span>
    </a>
@endsection

@section('konten')
    <div class="flex flex-col gap-6 max-w-7xl mx-auto pt-2 pb-10">
        {{-- Card Terpadu: Filter Pencarian & Tombol Tambah Guru --}}
        <section class="bg-surface-container-lowest rounded-3xl p-5 sm:p-6 shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            {{-- Form Saring / Cari Nama & NIP --}}
            <form method="GET" action="{{ route('superadmin.guru') }}" class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1">
                <div class="relative flex-1">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]">search</span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama lengkap utawa NIP guru..."
                           class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-2.5 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit"
                            class="flex items-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-body font-bold px-6 py-2.5 shadow-sm transition-all">
                        <span class="material-symbols-outlined text-[18px]">search</span>
                        <span>Saring</span>
                    </button>
                    @if (request('q'))
                        <a href="{{ route('superadmin.guru') }}"
                           class="flex items-center gap-1.5 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-body text-body font-semibold px-4 py-2.5 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                            <span>Reset</span>
                        </a>
                    @endif
                </div>
            </form>

            {{-- Tombol Tambah Guru Anyar di dalam Card Toolbar --}}
            <div class="flex items-center gap-2 shrink-0 border-t md:border-t-0 md:border-l border-gray-100 pt-3 md:pt-0 md:pl-4">
                <a href="{{ route('superadmin.guru.create') }}"
                   class="w-full md:w-auto flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-primary-50 hover:bg-primary-100 text-primary-700 font-body text-body font-bold transition-all border border-primary-100">
                    <span class="material-symbols-outlined text-[20px] text-primary-600">person_add</span>
                    <span>Tambah Guru</span>
                </a>
            </div>
        </section>

        {{-- Daftar Kartu Guru --}}
        <section class="flex flex-col gap-3">
            @forelse ($guruList as $g)
                <div class="bg-surface-container-lowest rounded-2xl p-5 shadow-sm border border-gray-100 hover:border-primary-200 transition-all hover:shadow-md">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        {{-- Info Profil Guru --}}
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary-600 to-primary-700 text-white flex items-center justify-center font-heading font-extrabold text-base shrink-0 uppercase shadow-sm">
                                {{ \Illuminate\Support\Str::of($g->nama_lengkap)->substr(0, 2) }}
                            </div>
                            <div class="flex flex-col min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-heading text-heading font-bold text-on-surface truncate">{{ $g->nama_lengkap }}</h3>
                                    <span class="px-2.5 py-0.5 rounded-full bg-surface-container-high text-gray-700 font-label-upper text-[11px] font-bold uppercase">
                                        {{ $g->jenis_kelamin === 'L' ? 'Lanang' : 'Wadon' }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 text-gray-500 font-caption text-caption flex-wrap mt-0.5">
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[15px] text-gray-400">badge</span>
                                        NIP {{ $g->nip }}
                                    </span>
                                    <span>•</span>
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[15px] text-gray-400">work</span>
                                        {{ $g->status_pegawaian ?: 'Status belum diatur' }}
                                    </span>
                                    <span>•</span>
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[15px] text-gray-400">mail</span>
                                        {{ $g->email }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                            <span class="px-3 py-1.5 rounded-full bg-primary-fixed text-primary-700 font-caption text-caption font-bold flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">quiz</span>
                                {{ $g->soal_count }} Soal
                            </span>

                            {{-- Tombol Lihat Detail --}}
                            <a href="{{ route('superadmin.guru.show', $g) }}"
                               class="flex items-center gap-1.5 px-4 py-2 rounded-full bg-primary-600 hover:bg-primary-700 text-white font-body text-body font-bold shadow-sm transition-all">
                                <span class="material-symbols-outlined text-[18px]">manage_accounts</span>
                                <span>Detail Akun</span>
                            </a>

                            {{-- Tombol Hapus --}}
                            <form method="POST" action="{{ route('superadmin.guru.destroy', $g) }}" onsubmit="return confirm('Apa panjenengan yakin pengin mbusak akun guru {{ $g->nama_lengkap }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" title="Busak Akun" class="w-9 h-9 rounded-full bg-error-container/50 text-error flex items-center justify-center hover:bg-error-container transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-surface-container-lowest rounded-3xl p-12 text-center border border-gray-100 shadow-sm flex flex-col items-center justify-center gap-3">
                    <div class="w-16 h-16 rounded-2xl bg-surface-container-high text-gray-400 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[36px]">group_off</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <h4 class="font-heading text-heading font-bold text-on-surface">Durung Ana Akun Guru</h4>
                        <p class="font-body text-body text-gray-500">Ora ana data guru sing cocog karo saringan utawa durung didaftarake.</p>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('superadmin.guru.create') }}"
                           class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-primary-600 text-white font-body text-body font-bold shadow-sm hover:bg-primary-700 transition-all">
                            <span class="material-symbols-outlined text-[18px]">person_add</span>
                            <span>Daftarake Guru Anyar Saiki</span>
                        </a>
                    </div>
                </div>
            @endforelse

            @if ($guruList->hasPages())
                <div class="mt-4">{{ $guruList->links() }}</div>
            @endif
        </section>
    </div>
@endsection
