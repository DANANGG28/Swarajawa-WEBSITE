@extends('layouts.admin')

@section('konten')
    <div class="flex flex-col gap-6 max-w-7xl mx-auto pt-2 pb-10">
        {{-- Section 4 Card Statistik Utama dengan Warna Background Berbeda & Kontras --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            @php
                $cards = [
                    [
                        'label' => 'Total Guru',
                        'value' => $stat['guru'],
                        'deskripsi' => 'Akun guru terdaftar',
                        'ikon' => 'supervisor_account',
                        'bg_card' => 'bg-primary-50/90 hover:bg-primary-50 border-primary-200/80 hover:border-primary-400',
                        'icon_box' => 'bg-white text-primary-600 border-primary-100',
                        'label_color' => 'text-primary-700',
                        'num_color' => 'text-primary-900',
                        'desc_color' => 'text-primary-600/70',
                        'link' => route('superadmin.guru'),
                    ],
                    [
                        'label' => 'Total Siswa',
                        'value' => $stat['siswa'],
                        'deskripsi' => 'Akun siswa terdaftar',
                        'ikon' => 'groups',
                        'bg_card' => 'bg-amber-50/90 hover:bg-amber-50 border-amber-200/80 hover:border-amber-400',
                        'icon_box' => 'bg-white text-amber-600 border-amber-100',
                        'label_color' => 'text-amber-700',
                        'num_color' => 'text-amber-900',
                        'desc_color' => 'text-amber-700/70',
                        'link' => route('superadmin.siswa'),
                    ],
                    [
                        'label' => 'Level Materi',
                        'value' => $stat['level'],
                        'deskripsi' => 'Kurikulum berjenjang',
                        'ikon' => 'stairs',
                        'bg_card' => 'bg-orange-50/90 hover:bg-orange-50 border-orange-200/80 hover:border-orange-400',
                        'icon_box' => 'bg-white text-orange-600 border-orange-100',
                        'label_color' => 'text-orange-700',
                        'num_color' => 'text-orange-900',
                        'desc_color' => 'text-orange-700/70',
                        'link' => route('superadmin.level-materi'),
                    ],
                    [
                        'label' => 'Bank Soal',
                        'value' => $stat['soal'],
                        'deskripsi' => 'Total butir latihan',
                        'ikon' => 'quiz',
                        'bg_card' => 'bg-emerald-50/90 hover:bg-emerald-50 border-emerald-200/80 hover:border-emerald-400',
                        'icon_box' => 'bg-white text-emerald-600 border-emerald-100',
                        'label_color' => 'text-emerald-700',
                        'num_color' => 'text-emerald-900',
                        'desc_color' => 'text-emerald-700/70',
                        'link' => route('superadmin.soal'),
                    ],
                ];
            @endphp

            @foreach ($cards as $card)
                <a href="{{ $card['link'] }}"
                   class="{{ $card['bg_card'] }} rounded-3xl p-5 sm:p-6 shadow-sm border hover:shadow-md transition-all flex flex-col justify-between group">
                    <div class="flex items-center justify-between">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider font-bold {{ $card['label_color'] }}">
                            {{ $card['label'] }}
                        </span>
                        <div class="w-11 h-11 rounded-2xl {{ $card['icon_box'] }} border shadow-sm flex items-center justify-center shrink-0 transition-transform group-hover:scale-105">
                            <span class="material-symbols-outlined text-[22px]">{{ $card['ikon'] }}</span>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-col">
                        <div class="font-stat-number text-2xl sm:text-3xl font-extrabold {{ $card['num_color'] }}">
                            {{ number_format($card['value']) }}
                        </div>
                        <p class="font-caption text-caption {{ $card['desc_color'] }} mt-1">
                            {{ $card['deskripsi'] }}
                        </p>
                    </div>
                </a>
            @endforeach
        </section>

        {{-- Section Tabel Struktur Level Materi --}}
        <section class="bg-white rounded-3xl shadow-sm border border-gray-200/80 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gray-50/60">
                <div>
                    <h2 class="font-heading text-heading font-bold text-on-surface">Struktur Level Materi</h2>
                    <p class="font-caption text-caption text-gray-500">Kurikulum pembelajaran berjenjang Bahasa & Budaya Jawa</p>
                </div>
                <a href="{{ route('superadmin.level-materi') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-primary-50 hover:bg-primary-100 text-primary-700 font-body text-xs font-bold transition-all border border-primary-100 shrink-0 self-start sm:self-center">
                    <span>Kelola Level</span>
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="font-label-upper text-[11px] uppercase tracking-wider text-gray-500 border-b border-gray-100 bg-gray-50/80">
                            <th class="py-4 px-6 w-24 whitespace-nowrap">Urutan</th>
                            <th class="py-4 px-6 min-w-[280px]">Nama Materi</th>
                            <th class="py-4 px-6 w-48 whitespace-nowrap">Reward EXP</th>
                            <th class="py-4 px-6 w-40 whitespace-nowrap">Jumlah Soal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-body text-body bg-white">
                        @forelse ($levelTerbaru as $level)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="w-8 h-8 rounded-xl bg-primary-50 text-primary-700 font-heading font-extrabold text-xs flex items-center justify-center border border-primary-100">
                                        {{ $level->urutan }}
                                    </div>
                                </td>
                                <td class="py-4 px-6 min-w-[280px]">
                                    <div class="flex flex-col">
                                        <span class="font-heading font-bold text-on-surface text-base">{{ $level->nama_materi }}</span>
                                        <span class="font-caption text-caption text-gray-500 mt-0.5">{{ $level->deskripsi }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-700 font-label-upper text-[10px] font-bold border border-amber-200/80 whitespace-nowrap">
                                        <span class="material-symbols-outlined text-[13px] text-amber-600 icon-fill shrink-0">bolt</span>
                                        <span>+{{ $level->reward_exp }} EXP</span>
                                    </span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full bg-surface-container-high text-on-surface-variant font-label-upper text-[11px] font-semibold inline-block whitespace-nowrap">
                                        {{ $level->soal_count }} Soal
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 px-6 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center gap-2.5">
                                        <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-[28px]">layers_clear</span>
                                        </div>
                                        <span class="font-heading font-bold text-on-surface text-sm">Belum Ada Level Materi</span>
                                        <p class="font-body text-xs text-gray-500">Materi kuis belum didaftarkan.</p>
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
