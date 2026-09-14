@extends('layouts.admin')

@section('konten')
    <div class="flex flex-col gap-6">
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-gutter">
            @php
                $cards = [
                    ['label' => 'Total Guru', 'value' => $stat['guru'], 'ikon' => 'supervisor_account', 'bg' => 'bg-primary-fixed', 'text' => 'text-primary-700'],
                    ['label' => 'Total Siswa', 'value' => $stat['siswa'], 'ikon' => 'groups', 'bg' => 'bg-yellow-300/50', 'text' => 'text-tertiary'],
                    ['label' => 'Level Materi', 'value' => $stat['level'], 'ikon' => 'stairs', 'bg' => 'bg-orange-300/40', 'text' => 'text-tertiary'],
                    ['label' => 'Bank Soal', 'value' => $stat['soal'], 'ikon' => 'quiz', 'bg' => 'bg-green-500/15', 'text' => 'text-green-500'],
                    ['label' => 'Paket Test', 'value' => $stat['test'], 'ikon' => 'playlist_add_check', 'bg' => 'bg-pink-100', 'text' => 'text-secondary'],
                ];
            @endphp
            @foreach ($cards as $card)
                <div class="{{ $card['bg'] }} rounded-2xl p-5 shadow-sm flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider {{ $card['text'] }} font-bold">{{ $card['label'] }}</span>
                        <span class="material-symbols-outlined {{ $card['text'] }}">{{ $card['ikon'] }}</span>
                    </div>
                    <div class="font-stat-number text-stat-number font-extrabold text-on-surface">{{ number_format($card['value']) }}</div>
                </div>
            @endforeach
        </section>

        <section class="bg-surface-container-lowest rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="font-heading text-heading font-bold text-on-surface">Struktur Level Materi</h2>
                    <p class="font-caption text-caption text-gray-500">Kurikulum pembelajaran berjenjang</p>
                </div>
                <a href="{{ route('superadmin.level-materi') }}" class="font-caption text-caption font-bold text-primary-600 hover:underline">Kelola Level</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 border-b border-gray-100">
                            <th class="px-5 py-3">Urutan</th>
                            <th class="px-5 py-3">Nama Materi</th>
                            <th class="px-5 py-3">Reward EXP</th>
                            <th class="px-5 py-3">Jumlah Soal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-body text-body">
                        @foreach ($levelTerbaru as $level)
                            <tr class="hover:bg-surface-container-low/40 transition-colors">
                                <td class="px-5 py-3.5 font-heading font-extrabold text-primary-700">{{ $level->urutan }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-on-surface">{{ $level->nama_materi }}</span>
                                        <span class="font-caption text-caption text-gray-500">{{ $level->deskripsi }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5"><span class="px-3 py-1 rounded-full bg-yellow-300/50 text-tertiary font-caption text-caption font-bold">+{{ $level->reward_exp }} XP</span></td>
                                <td class="px-5 py-3.5 font-semibold text-on-surface-variant">{{ $level->soal_count }} soal</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
