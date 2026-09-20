@extends('layouts.admin')

@section('aksi')
    <div class="flex items-center gap-2">
        <a href="{{ route('superadmin.siswa.edit', $siswa) }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-full bg-primary-600 hover:bg-primary-700 text-white font-body text-body font-bold transition-all shadow-sm">
            <span class="material-symbols-outlined text-[20px]">edit</span>
            <span>Edit Akun</span>
        </a>
        <a href="{{ route('superadmin.siswa') }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-full bg-surface-container-low hover:bg-surface-container-high text-on-surface font-body text-body font-bold transition-all shadow-sm">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            <span>Kembali</span>
        </a>
    </div>
@endsection

@section('konten')
    <div class="flex flex-col gap-6 max-w-5xl mx-auto pt-4 pb-10">
        {{-- Breadcrumb Navigasi --}}
        <nav class="flex items-center gap-2 font-caption text-caption text-gray-500">
            <a href="{{ route('superadmin.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a href="{{ route('superadmin.siswa') }}" class="hover:text-primary-600 transition-colors">Akun Siswa</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface font-bold truncate">{{ $siswa->nama_lengkap }}</span>
        </nav>

        {{-- Hero Profile Banner --}}
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 p-6 sm:p-8 text-white shadow-lg">
            <div class="absolute -right-10 -bottom-10 w-64 h-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
            <div class="absolute right-40 -top-10 w-48 h-48 rounded-full bg-yellow-300/15 blur-xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center gap-5 min-w-0">
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-white/20 backdrop-blur-md border-2 border-white/40 text-white flex items-center justify-center font-display font-extrabold text-2xl sm:text-3xl uppercase shadow-inner shrink-0 overflow-hidden">
                    @if ($siswa->foto_url)
                        <img src="{{ $siswa->foto_url }}" alt="{{ $siswa->nama_lengkap }}" class="w-full h-full object-cover">
                    @else
                        {{ \Illuminate\Support\Str::of($siswa->nama_lengkap)->substr(0, 2) }}
                    @endif
                </div>

                <div class="flex flex-col gap-2.5 min-w-0">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-white tracking-tight truncate">
                            {{ $siswa->nama_lengkap }}
                        </h2>
                        <span class="px-3 py-1 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white font-label-upper text-[11px] font-bold tracking-wider uppercase">
                            {{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap text-white/90 font-caption text-caption">
                        <span class="flex items-center gap-1.5 bg-white/10 px-3 py-1 rounded-full border border-white/20">
                            <span class="material-symbols-outlined text-[16px] text-yellow-300">badge</span>
                            NIS {{ $siswa->nis }}
                        </span>
                        <span class="flex items-center gap-1.5 bg-white/10 px-3 py-1 rounded-full border border-white/20">
                            <span class="material-symbols-outlined text-[16px] text-yellow-300">school</span>
                            {{ $siswa->kelas ? 'Kelas ' . $siswa->kelas : 'Kelas Belum Diatur' }}
                        </span>
                        <span class="flex items-center gap-1.5 bg-white/10 px-3 py-1 rounded-full border border-white/20">
                            <span class="material-symbols-outlined text-[16px] text-yellow-300">mail</span>
                            {{ $siswa->email }}
                        </span>
                    </div>
                </div>
            </div>
        </section>

        {{-- Metrik Capaian Siswa (3 KPI Cards) --}}
        <section class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-surface-container-lowest rounded-3xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-bold">Total EXP</span>
                    <div class="font-stat-number text-stat-number font-extrabold text-primary-700 mt-1">
                        {{ number_format($siswa->exp?->total_exp ?? 0) }}
                    </div>
                    <p class="font-caption text-caption text-gray-500">Poin pengalaman belajar</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-primary-fixed text-primary-700 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[26px]">bolt</span>
                </div>
            </div>

            <div class="bg-surface-container-lowest rounded-3xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-bold">Streak Saat Ini</span>
                    <div class="font-stat-number text-stat-number font-extrabold text-orange-600 mt-1">
                        {{ $siswa->strek?->current_streak ?? 0 }} Hari
                    </div>
                    <p class="font-caption text-caption text-gray-500">Belajar berturut-turut</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-orange-300/40 text-orange-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[26px]">local_fire_department</span>
                </div>
            </div>

            <div class="bg-surface-container-lowest rounded-3xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-bold">Streak Tertinggi</span>
                    <div class="font-stat-number text-stat-number font-extrabold text-tertiary mt-1">
                        {{ $siswa->strek?->highest_streak ?? 0 }} Hari
                    </div>
                    <p class="font-caption text-caption text-gray-500">Rekor belajar terpanjang</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-yellow-300/40 text-tertiary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[26px]">emoji_events</span>
                </div>
            </div>
        </section>

        {{-- Card Informasi Lengkap Siswa --}}
        <section class="bg-surface-container-lowest rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between pb-5 border-b border-gray-100 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[24px]">id_card</span>
                    </div>
                    <div>
                        <h3 class="font-heading text-heading font-bold text-on-surface">Rincian Informasi Akun Siswa</h3>
                        <p class="font-caption text-caption text-gray-500">Data pribadi, akademik, dan informasi kontak siswa</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex items-start gap-3 p-4 rounded-2xl bg-surface-container-low border border-gray-50">
                    <span class="material-symbols-outlined text-primary-600 text-[22px] mt-0.5">badge</span>
                    <div class="flex flex-col min-w-0">
                        <span class="font-label-upper text-[11px] text-gray-500 uppercase tracking-wider font-semibold">NIS</span>
                        <span class="font-body text-body font-bold text-on-surface">{{ $siswa->nis }}</span>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-4 rounded-2xl bg-surface-container-low border border-gray-50">
                    <span class="material-symbols-outlined text-primary-600 text-[22px] mt-0.5">person</span>
                    <div class="flex flex-col min-w-0">
                        <span class="font-label-upper text-[11px] text-gray-500 uppercase tracking-wider font-semibold">Nama Lengkap</span>
                        <span class="font-body text-body font-bold text-on-surface">{{ $siswa->nama_lengkap }}</span>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-4 rounded-2xl bg-surface-container-low border border-gray-50">
                    <span class="material-symbols-outlined text-primary-600 text-[22px] mt-0.5">school</span>
                    <div class="flex flex-col min-w-0">
                        <span class="font-label-upper text-[11px] text-gray-500 uppercase tracking-wider font-semibold">Kelas</span>
                        <span class="font-body text-body font-bold text-on-surface">{{ $siswa->kelas ? 'Kelas ' . $siswa->kelas : 'Belum Diatur' }}</span>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-4 rounded-2xl bg-surface-container-low border border-gray-50">
                    <span class="material-symbols-outlined text-primary-600 text-[22px] mt-0.5">wc</span>
                    <div class="flex flex-col min-w-0">
                        <span class="font-label-upper text-[11px] text-gray-500 uppercase tracking-wider font-semibold">Jenis Kelamin</span>
                        <span class="font-body text-body font-bold text-on-surface">{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki (L)' : 'Perempuan (P)' }}</span>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-4 rounded-2xl bg-surface-container-low border border-gray-50">
                    <span class="material-symbols-outlined text-primary-600 text-[22px] mt-0.5">mail</span>
                    <div class="flex flex-col min-w-0">
                        <span class="font-label-upper text-[11px] text-gray-500 uppercase tracking-wider font-semibold">Alamat Email</span>
                        <span class="font-body text-body font-bold text-on-surface truncate">{{ $siswa->email }}</span>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-4 rounded-2xl bg-surface-container-low border border-gray-50">
                    <span class="material-symbols-outlined text-primary-600 text-[22px] mt-0.5">call</span>
                    <div class="flex flex-col min-w-0">
                        <span class="font-label-upper text-[11px] text-gray-500 uppercase tracking-wider font-semibold">No. Telpon / WhatsApp</span>
                        <span class="font-body text-body font-bold text-on-surface">{{ $siswa->no_telpon ?: 'Belum diisi' }}</span>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
