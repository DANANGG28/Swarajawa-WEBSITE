@extends('layouts.admin')

@section('konten')
    <div class="flex flex-col gap-6">
        <section class="grid grid-cols-1 sm:grid-cols-3 gap-gutter">
            <div class="bg-primary-fixed rounded-2xl p-5 shadow-sm">
                <span class="font-label-upper text-label-upper uppercase tracking-wider text-primary font-bold">Total Siswa Diampu</span>
                <div class="font-stat-number text-stat-number font-extrabold text-on-primary-fixed mt-1">{{ $siswa->total() }}</div>
                <p class="font-caption text-caption text-on-primary-fixed-variant">Siswa pada kelas/mapel Anda</p>
            </div>
            <div class="bg-yellow-300/50 rounded-2xl p-5 shadow-sm">
                <span class="font-label-upper text-label-upper uppercase tracking-wider text-tertiary font-bold">Total Level Materi</span>
                <div class="font-stat-number text-stat-number font-extrabold text-tertiary mt-1">{{ $totalLevel }}</div>
                <p class="font-caption text-caption text-tertiary">Struktur kurikulum aktif</p>
            </div>
            <div class="bg-orange-300/40 rounded-2xl p-5 shadow-sm">
                <span class="font-label-upper text-label-upper uppercase tracking-wider text-tertiary font-bold">Kelas Diampu</span>
                <div class="font-stat-number text-stat-number font-extrabold text-tertiary mt-1">{{ $kelasList->count() }}</div>
                <p class="font-caption text-caption text-tertiary">{{ $kelasList->implode(', ') ?: 'Belum ada kelas' }}</p>
            </div>
        </section>

        <section class="bg-surface-container-lowest rounded-2xl p-5 shadow-sm border border-gray-100">
            <form method="GET" class="flex flex-col sm:flex-row sm:items-end gap-3">
                <label class="flex flex-col gap-1.5 flex-1">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Cari Nama / NIS</span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Andi / 00928..."
                        class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 font-body text-body outline-none focus:border-primary-500">
                </label>
                <label class="flex flex-col gap-1.5 sm:w-48">
                    <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Kelas</span>
                    <select name="kelas" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 font-body text-body outline-none focus:border-primary-500">
                        <option value="">Kabeh Kelas</option>
                        @foreach ($kelasList as $k)
                            <option value="{{ $k }}" @selected(request('kelas') === $k)>{{ $k }}</option>
                        @endforeach
                    </select>
                </label>
                <button type="submit" class="flex items-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-body font-bold px-6 py-2.5 shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">search</span> Saring
                </button>
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
                                <td class="px-5 py-3.5 font-semibold text-orange-500">{{ $s->strek?->current_streak ?? 0 }} dina</td>
                                <td class="px-5 py-3.5 text-on-surface-variant">{{ $s->strek?->last_activity_date?->translatedFormat('d M Y') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-gray-500 font-body text-body">
                                    Durung ana siswa sing diampu. Hubungi superadmin kanggo ngatur kelas/mapel.
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
@endsection
