@extends('layouts.admin')

@section('konten')
    <div class="flex flex-col gap-6 max-w-7xl mx-auto pt-2 pb-10 mt-4">
        {{-- Toolbar: Filter Pencarian Level Materi (Khusus Guru: Tanpa Tambah Level Materi) --}}
        <section class="bg-surface-container-lowest rounded-3xl p-4 sm:p-5 shadow-sm border border-gray-100 flex flex-col sm:flex-row items-stretch sm:items-center gap-3.5">
            {{-- Form Input Pencarian Level Materi --}}
            <form method="GET" action="{{ route('guru.level-materi') }}" class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]">search</span>
                <input type="text" name="q" value="{{ request('q') }}" id="searchLevelInput" oninput="filterLevels()" placeholder="Cari nama materi atau deskripsi level..."
                       class="w-full rounded-2xl border border-gray-200 bg-gray-50 pl-11 pr-10 py-2.5 font-body text-sm outline-none focus:border-primary-500 focus:bg-white transition-all">
                @if (request('q'))
                    <a href="{{ route('guru.level-materi') }}" title="Hapus pencarian"
                       class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </a>
                @endif
            </form>
        </section>

        {{-- Daftar List Level Materi --}}
        <section class="bg-surface-container-lowest rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            @if ($levels->count() > 0)
                <div id="levelListContainer" class="divide-y divide-gray-100 font-body text-sm">
                    @foreach ($levels as $level)
                        <div class="level-item p-4 sm:p-5 hover:bg-surface-container-low/40 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4"
                             data-search="{{ strtolower($level->nama_materi . ' ' . $level->deskripsi . ' level ' . $level->urutan) }}">
                            
                            {{-- Info Level: Urutan, Nama Materi, Deskripsi, Meta --}}
                            <div class="flex items-start gap-3.5 min-w-0 flex-1">
                                {{-- Badge Nomor Urutan Level --}}
                                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-gradient-to-br from-primary-600 to-primary-700 text-white flex items-center justify-center font-heading font-extrabold text-sm sm:text-base shrink-0 uppercase shadow-sm">
                                    {{ $level->urutan }}
                                </div>

                                <div class="flex flex-col min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="font-heading font-bold text-on-surface text-sm sm:text-base leading-snug">
                                            {{ $level->nama_materi }}
                                        </h3>
                                    </div>

                                    @if ($level->deskripsi)
                                        <p class="font-body text-xs text-gray-500 mt-1 line-clamp-2 leading-relaxed">
                                            {{ $level->deskripsi }}
                                        </p>
                                    @endif

                                    <div class="flex items-center gap-3 text-xs text-gray-400 font-caption mt-2 flex-wrap">
                                        {{-- Jumlah Soal Milik Guru --}}
                                        <span class="flex items-center gap-1 font-semibold text-primary-700">
                                            {{ $level->soal_count }} Soal Terdaftar
                                        </span>
                                        <span class="text-gray-300">•</span>
                                        {{-- Reward EXP --}}
                                        <span class="flex items-center gap-1 font-semibold text-amber-700">
                                            <span class="material-symbols-outlined text-[15px] text-amber-500 icon-fill">bolt</span>
                                            +{{ number_format($level->reward_exp) }} EXP
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Aksi Guru: Kelola Soal & Tambah Soal --}}
                            <div class="flex items-center gap-2 shrink-0 self-end md:self-center pt-2 md:pt-0 border-t md:border-t-0 border-gray-100 w-full md:w-auto justify-end">
                                {{-- Tombol Kelola Soal --}}
                                <a href="{{ route('guru.soal', ['level_materi_id' => $level->id]) }}"
                                   title="Lihat dan kelola soal pada level ini"
                                   class="flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-primary-50 hover:bg-primary-600 text-primary-700 hover:text-white font-body text-xs font-bold transition-all border border-primary-100 shadow-xs whitespace-nowrap">
                                    <span class="material-symbols-outlined text-[16px]">quiz</span>
                                    <span>Kelola Soal</span>
                                </a>

                                {{-- Tombol Tambah Soal --}}
                                <a href="{{ route('guru.soal.create', ['level_materi_id' => $level->id]) }}"
                                   title="Tambah butir soal baru pada level ini"
                                   class="flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-primary-600 hover:bg-primary-700 text-white font-body text-xs font-bold transition-all shadow-xs whitespace-nowrap">
                                    <span class="material-symbols-outlined text-[16px]">add_circle</span>
                                    <span>Tambah Soal</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- State ketika pencarian tidak menemukan hasil di halaman aktif --}}
                <div id="noSearchMatch" class="p-12 text-center text-gray-500 hidden">
                    <span class="material-symbols-outlined text-[36px] text-gray-400">search_off</span>
                    <p class="font-body text-sm text-gray-600 font-semibold mt-2">Level materi tidak ditemukan pada halaman ini</p>
                    <p class="font-caption text-xs text-gray-400 mt-0.5">Tekan tombol <strong>Enter</strong> untuk mencari ke seluruh data level materi.</p>
                </div>

                @if ($levels instanceof \Illuminate\Pagination\LengthAwarePaginator && $levels->hasPages())
                    {{-- Pagination Footer --}}
                    <div class="px-5 py-3.5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 bg-surface-container-low/20">
                        <div class="text-xs text-gray-500 font-caption">
                            Menampilkan <span class="font-bold text-on-surface">{{ $levels->firstItem() ?? 0 }}</span> - <span class="font-bold text-on-surface">{{ $levels->lastItem() ?? 0 }}</span> dari total <span class="font-bold text-on-surface">{{ $levels->total() }}</span> level
                        </div>
                        <div>
                            {{ $levels->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="p-12 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center gap-3">
                        <div class="w-14 h-14 rounded-2xl bg-surface-container-high text-gray-400 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[32px]">{{ request('q') ? 'search_off' : 'layers_clear' }}</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <h4 class="font-heading text-sm font-bold text-on-surface">
                                {{ request('q') ? 'Level Materi Tidak Ditemukan' : 'Belum Ada Level Materi' }}
                            </h4>
                            <p class="font-body text-xs text-gray-500">
                                {{ request('q') ? 'Tidak ada level materi yang sesuai dengan kata kunci "' . request('q') . '".' : 'Materi pembelajaran dan level kuis belum didaftarkan oleh Superadmin.' }}
                            </p>
                        </div>
                        @if (request('q'))
                            <div class="pt-2">
                                <a href="{{ route('guru.level-materi') }}"
                                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-body text-xs font-bold transition-all">
                                    <span class="material-symbols-outlined text-[18px]">close</span>
                                    <span>Reset Pencarian</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </section>
    </div>

    <script>
        function filterLevels() {
            const query = document.getElementById('searchLevelInput').value.toLowerCase().trim();
            const items = document.querySelectorAll('.level-item');
            const noMatch = document.getElementById('noSearchMatch');
            let visibleCount = 0;

            items.forEach(item => {
                const text = item.getAttribute('data-search') || '';
                if (query === '' || text.includes(query)) {
                    item.classList.remove('hidden');
                    visibleCount++;
                } else {
                    item.classList.add('hidden');
                }
            });

            if (noMatch) {
                if (visibleCount === 0 && items.length > 0) {
                    noMatch.classList.remove('hidden');
                } else {
                    noMatch.classList.add('hidden');
                }
            }
        }
    </script>
@endsection
