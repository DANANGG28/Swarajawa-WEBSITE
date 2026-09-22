@extends('layouts.admin')

@section('konten')
    <div class="flex flex-col gap-6 max-w-7xl mx-auto pt-2 pb-10 mt-4">
        {{-- Card Terpadu 3D Ringkasan Statistik Sistem --}}
        <section class="relative overflow-hidden rounded-[28px] bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 p-6 sm:p-7 text-white border-b-[6px] border-[#3c25b1] shadow-[0_12px_24px_-4px_rgba(60,37,177,0.35)]">
            {{-- Elemen Dekorasi Cahaya Halus --}}
            <div class="absolute -right-12 -bottom-12 w-64 h-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
            <div class="absolute left-1/3 -top-12 w-56 h-56 rounded-full bg-primary-400/20 blur-xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col gap-5">
                {{-- Header Card Ringkasan --}}
                <div class="flex items-center justify-between pb-4 border-b border-white/15">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-white/15 backdrop-blur-md flex items-center justify-center text-white border border-white/25 shadow-inner shrink-0">
                            <span class="material-symbols-outlined text-[22px]">analytics</span>
                        </div>
                        <div>
                            <h2 class="font-heading text-base sm:text-lg font-extrabold text-white tracking-tight">Ringkasan Sistem Sinau Jowo</h2>
                            <p class="font-caption text-xs text-white/80">Pantauan menyeluruh akun pengelola, peserta didik, kurikulum, dan bank soal</p>
                        </div>
                    </div>

                </div>

                {{-- 4 Modul Statistik Terpisah Rapih dengan Efek 3D --}}
                @php
                    $cards = [
                        [
                            'label' => 'Total Guru',
                            'value' => $stat['guru'],
                            'deskripsi' => 'Akun guru terdaftar',
                            'ikon' => 'supervisor_account',
                            'link' => route('superadmin.guru'),
                        ],
                        [
                            'label' => 'Total Siswa',
                            'value' => $stat['siswa'],
                            'deskripsi' => 'Akun siswa terdaftar',
                            'ikon' => 'groups',
                            'link' => route('superadmin.siswa'),
                        ],
                        [
                            'label' => 'Topik',
                            'value' => $stat['topik'] ?? \App\Models\Topik::count(),
                            'deskripsi' => 'Topik materi pembelajaran',
                            'ikon' => 'topic',
                            'link' => route('superadmin.topik'),
                        ],
                        [
                            'label' => 'Bank Soal',
                            'value' => $stat['soal'],
                            'deskripsi' => 'Total butir latihan',
                            'ikon' => 'quiz',
                            'link' => route('superadmin.soal'),
                        ],
                    ];
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
                    @foreach ($cards as $card)
                        <a href="{{ $card['link'] }}"
                           class="group relative flex flex-col justify-between p-4 sm:p-5 rounded-2xl bg-white/15 hover:bg-white/25 active:bg-white/20 backdrop-blur-md border border-white/25 border-b-[4px] border-b-[#34209c] shadow-[0_4px_12px_rgba(0,0,0,0.12)] hover:border-b-[2px] hover:translate-y-0.5 active:border-b-0 active:translate-y-1 transition-all duration-150 cursor-pointer select-none">
                            <div class="flex items-center justify-between">
                                <span class="font-label-upper text-label-upper uppercase tracking-wider font-bold text-white/90">
                                    {{ $card['label'] }}
                                </span>
                                <div class="w-10 h-10 rounded-xl bg-white/20 text-white border border-white/30 shadow-inner flex items-center justify-center shrink-0 transition-transform duration-200 group-hover:scale-110 group-hover:bg-white group-hover:text-primary-700">
                                    <span class="material-symbols-outlined text-[20px]">{{ $card['ikon'] }}</span>
                                </div>
                            </div>

                            <div class="mt-4 flex flex-col">
                                <div class="font-stat-number text-3xl sm:text-4xl font-black text-white tracking-tight">
                                    {{ number_format($card['value']) }}
                                </div>
                                <p class="font-caption text-xs text-white/75 mt-1 line-clamp-1">
                                    {{ $card['deskripsi'] }}
                                </p>
                            </div>

                            <div class="mt-4 pt-3 border-t border-white/15 flex items-center justify-between text-white/80 group-hover:text-white font-caption text-xs font-semibold">
                                <span>Lihat Detail</span>
                                <span class="material-symbols-outlined text-[16px] transition-transform duration-200 group-hover:translate-x-1">arrow_forward</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Section Tabel Struktur Topik Pembelajaran --}}
        <section class="bg-white rounded-3xl shadow-sm border border-gray-200/80 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gray-50/60">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-primary-50 text-primary-700 flex items-center justify-center border border-primary-100 shrink-0">
                        <span class="material-symbols-outlined text-[22px]">topic</span>
                    </div>
                    <div>
                        <h2 class="font-heading text-heading font-bold text-on-surface">Topik Pembelajaran</h2>
                        <p class="font-caption text-caption text-gray-500">Struktur alur kurikulum pembelajaran Bahasa & Budaya Jawa</p>
                    </div>
                </div>
                <a href="{{ route('superadmin.topik') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-primary-50 hover:bg-primary-100 text-primary-700 font-body text-xs font-bold transition-all border border-primary-100 shrink-0 self-start sm:self-center">
                    <span>Kelola Topik</span>
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="font-label-upper text-[11px] uppercase tracking-wider text-gray-500 border-b border-gray-100 bg-gray-50/80">
                            <th class="py-4 px-6 w-24 whitespace-nowrap">Urutan</th>
                            <th class="py-4 px-6 min-w-[300px]">Topik Materi</th>
                            <th class="py-4 px-6 w-48 whitespace-nowrap">Jumlah Unit / Level</th>
                            <th class="py-4 px-6 w-40 text-right whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-body text-body bg-white">
                        @php
                            $listTopik = $topikList ?? \App\Models\Topik::withCount('units')->orderBy('urutan')->get();
                        @endphp
                        @forelse ($listTopik as $topik)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="w-8 h-8 rounded-xl bg-primary-50 text-primary-700 font-heading font-extrabold text-xs flex items-center justify-center border border-primary-100">
                                        {{ $topik->urutan }}
                                    </div>
                                </td>
                                <td class="py-4 px-6 min-w-[300px]">
                                    <div class="flex flex-col">
                                        <span class="font-heading font-bold text-on-surface text-base">{{ $topik->nama }}</span>
                                        @if ($topik->deskripsi)
                                            <span class="font-caption text-caption text-gray-500 mt-0.5 line-clamp-1">{{ $topik->deskripsi }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full bg-primary-50 text-primary-700 border border-primary-100 font-label-upper text-[11px] font-bold inline-flex items-center gap-1.5 whitespace-nowrap">
                                        <span class="material-symbols-outlined text-[14px]">stairs</span>
                                        <span>{{ $topik->units_count ?? $topik->units->count() }} Unit Level</span>
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <a href="{{ route('superadmin.level-materi', ['topik_id' => $topik->id]) }}"
                                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-gray-100 hover:bg-primary-600 hover:text-white text-gray-700 font-body text-xs font-semibold transition-colors">
                                        <span>Buka Unit</span>
                                        <span class="material-symbols-outlined text-[15px]">chevron_right</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 px-6 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center gap-2.5">
                                        <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-[28px]">topic</span>
                                        </div>
                                        <span class="font-heading font-bold text-on-surface text-sm">Belum Ada Topik Pembelajaran</span>
                                        <p class="font-body text-xs text-gray-500">Silakan buat topik pertama untuk mengelompokkan materi kuis.</p>
                                        <a href="{{ route('superadmin.topik.create') }}"
                                           class="mt-2 inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-primary-600 hover:bg-primary-700 text-white font-body text-xs font-bold transition-all shadow-xs">
                                            <span class="material-symbols-outlined text-[16px]">add_circle</span>
                                            <span>Tambah Topik</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
