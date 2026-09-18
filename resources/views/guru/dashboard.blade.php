@extends('layouts.admin')

@section('konten')
    <div class="flex flex-col gap-6 pt-4 sm:pt-5 pb-10">
        <section class="grid grid-cols-1 sm:grid-cols-3 gap-gutter">
            <div class="bg-primary-50/90 border border-primary-200/80 hover:border-primary-400 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all flex items-center justify-between gap-4">
                <div class="flex flex-col min-w-0">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-primary-700 font-bold">Total Siswa Diampu</span>
                    <p class="font-caption text-caption text-primary-900/60 mt-1">Siswa pada kelas/mapel Anda</p>
                </div>
                <div class="min-w-[56px] h-14 px-3 rounded-2xl bg-white border border-primary-100 shadow-sm flex items-center justify-center shrink-0">
                    <span class="font-stat-number text-2xl sm:text-3xl font-extrabold text-primary-700">
                        {{ $siswa->total() }}
                    </span>
                </div>
            </div>

            <div class="bg-amber-50/90 border border-amber-200/80 hover:border-amber-400 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all flex items-center justify-between gap-4">
                <div class="flex flex-col min-w-0">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-amber-800 font-bold">Total Level Materi</span>
                    <p class="font-caption text-caption text-amber-900/60 mt-1">Struktur kurikulum aktif</p>
                </div>
                <div class="min-w-[56px] h-14 px-3 rounded-2xl bg-white border border-amber-100 shadow-sm flex items-center justify-center shrink-0">
                    <span class="font-stat-number text-2xl sm:text-3xl font-extrabold text-amber-700">
                        {{ $totalLevel }}
                    </span>
                </div>
            </div>

            <div class="bg-orange-50/90 border border-orange-200/80 hover:border-orange-400 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all flex items-center justify-between gap-4">
                <div class="flex flex-col min-w-0">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-orange-800 font-bold">Kelas Diampu</span>
                    <p class="font-caption text-caption text-orange-900/60 mt-1 truncate" title="{{ $kelasList->implode(', ') ?: 'Belum ada kelas' }}">
                        {{ $kelasList->implode(', ') ?: 'Belum ada kelas' }}
                    </p>
                </div>
                <div class="min-w-[56px] h-14 px-3 rounded-2xl bg-white border border-orange-100 shadow-sm flex items-center justify-center shrink-0">
                    <span class="font-stat-number text-2xl sm:text-3xl font-extrabold text-orange-700">
                        {{ $kelasList->count() }}
                    </span>
                </div>
            </div>
        </section>

        <section class="bg-surface-container-lowest rounded-2xl p-5 shadow-sm border border-gray-100">
            <form method="GET" action="{{ route('guru.dashboard') }}" class="flex flex-col sm:flex-row sm:items-end gap-3">
                <label class="flex flex-col gap-1.5 flex-1">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Cari Nama / NIS</span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Andi / 00928..."
                        class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-2.5 font-body text-body outline-none focus:border-primary-500">
                </label>
                <div class="relative flex flex-col gap-1.5 sm:w-52" id="filter-kelas-wrapper">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Kelas</span>
                    <input type="hidden" name="kelas" id="filter-kelas-input" value="{{ request('kelas') }}">
                    
                    <button type="button" id="btn-filter-kelas"
                        class="flex items-center justify-between w-full rounded-2xl border border-gray-200 bg-gray-50 hover:bg-gray-100 hover:border-primary-400 px-4 py-2.5 font-body text-body text-gray-700 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                        <span id="filter-kelas-label" class="truncate font-medium text-gray-800">
                            {{ request('kelas') ?: 'Semua Kelas' }}
                        </span>
                        <span class="inline-flex items-center justify-center shrink-0 w-5 h-5 text-gray-400 ml-2">
                            <span id="filter-kelas-chevron" class="material-symbols-outlined text-[20px] leading-none transition-transform duration-200">expand_more</span>
                        </span>
                    </button>

                    {{-- Dropdown Menu dengan Pembatas Antar Opsi --}}
                    <div id="filter-kelas-menu" class="hidden absolute top-full left-0 mt-2 w-full min-w-[200px] bg-white rounded-2xl shadow-xl border border-gray-100 py-1.5 z-50 overflow-hidden divide-y divide-gray-100">
                        <button type="button" data-val="" class="filter-kelas-item w-full flex items-center justify-between px-4 py-2.5 text-left font-body text-body {{ request('kelas') == '' ? 'bg-primary-50 text-primary-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }} transition-colors">
                            <span>Semua Kelas</span>
                            @if(request('kelas') == '')
                                <span class="material-symbols-outlined text-[18px] text-primary-600">check</span>
                            @endif
                        </button>
                        @foreach ($kelasList as $k)
                            <button type="button" data-val="{{ $k }}" class="filter-kelas-item w-full flex items-center justify-between px-4 py-2.5 text-left font-body text-body {{ request('kelas') === $k ? 'bg-primary-50 text-primary-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }} transition-colors">
                                <span>{{ $k }}</span>
                                @if(request('kelas') === $k)
                                    <span class="material-symbols-outlined text-[18px] text-primary-600">check</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
                <button type="submit" class="flex items-center gap-2 rounded-2xl bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-body font-bold px-6 py-2.5 shadow-sm transition-colors">
                    <span class="material-symbols-outlined text-[18px]">search</span> Filter
                </button>
                @if (request('q') || request('kelas'))
                    <a href="{{ route('guru.dashboard') }}" class="flex items-center gap-1.5 rounded-2xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-body text-body font-semibold px-4 py-2.5 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                        <span>Reset</span>
                    </a>
                @endif
            </form>
        </section>

        <section class="bg-surface-container-lowest rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-heading text-heading font-bold text-on-surface">Daftar Progres Siswa</h2>
                <p class="font-caption text-caption text-gray-500">Hanya siswa pada kelas/mata pelajaran yang Anda ampu (FR-11)</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 border-b border-gray-100">
                            <th class="px-5 py-3">Siswa</th>
                            <th class="px-5 py-3">Kelas</th>
                            <th class="px-5 py-3">Mapel</th>
                            <th class="px-5 py-3">Level Selesai</th>
                            <th class="px-5 py-3">EXP</th>
                            <th class="px-5 py-3">Streak</th>
                            <th class="px-5 py-3">Aktivitas Terakhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-body text-body">
                        @forelse ($siswa as $s)
                            <tr class="hover:bg-surface-container-low/40 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-on-surface">{{ $s->nama_lengkap }}</span>
                                        <span class="font-caption text-caption text-gray-500">NIS {{ $s->nis }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 font-semibold text-on-surface-variant">{{ $s->pivot->kelas }}</td>
                                <td class="px-5 py-3.5 text-on-surface-variant">{{ $s->pivot->mata_pelajaran }}</td>
                                <td class="px-5 py-3.5">
                                    @php $selesai = $s->progres->where('status', \App\Models\ProgresSiswa::STATUS_SELESAI)->count(); @endphp
                                    <div class="flex items-center gap-2">
                                        <div class="w-24 h-2 rounded-full bg-surface-container-high overflow-hidden">
                                            <div class="h-full bg-primary-600 rounded-full" style="width: {{ $totalLevel ? round($selesai / $totalLevel * 100) : 0 }}%"></div>
                                        </div>
                                        <span class="font-caption text-caption font-bold text-on-surface-variant">{{ $selesai }}/{{ $totalLevel }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 font-heading font-extrabold text-primary-700">{{ number_format($s->exp?->total_exp ?? 0) }}</td>
                                <td class="px-5 py-3.5 font-semibold text-orange-500">{{ $s->strek?->current_streak ?? 0 }} hari</td>
                                <td class="px-5 py-3.5 text-on-surface-variant">{{ $s->strek?->last_activity_date?->translatedFormat('d M Y') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-gray-500 font-body text-body">
                                    Belum ada siswa yang diampu. Hubungi superadmin untuk mengatur kelas/mapel.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($siswa->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">{{ $siswa->links() }}</div>
            @endif
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('btn-filter-kelas');
            const menu = document.getElementById('filter-kelas-menu');
            const chevron = document.getElementById('filter-kelas-chevron');
            const input = document.getElementById('filter-kelas-input');
            const form = btn?.closest('form');

            if (btn && menu) {
                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const isHidden = menu.classList.toggle('hidden');
                    if (!isHidden) {
                        chevron.classList.add('rotate-180');
                    } else {
                        chevron.classList.remove('rotate-180');
                    }
                });

                menu.querySelectorAll('.filter-kelas-item').forEach(function (item) {
                    item.addEventListener('click', function () {
                        const val = this.getAttribute('data-val');
                        input.value = val;
                        menu.classList.add('hidden');
                        chevron.classList.remove('rotate-180');
                        form.submit();
                    });
                });

                document.addEventListener('click', function (e) {
                    if (!btn.contains(e.target) && !menu.contains(e.target)) {
                        menu.classList.add('hidden');
                        chevron.classList.remove('rotate-180');
                    }
                });
            }
        });
    </script>
@endsection
