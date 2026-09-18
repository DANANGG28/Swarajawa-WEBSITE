@extends('layouts.admin')

@section('aksi')
    <a href="{{ route('superadmin.siswa.create') }}"
       class="flex items-center gap-2 px-5 py-2.5 rounded-full bg-primary-600 hover:bg-primary-700 text-white font-body text-body font-bold shadow-md hover:shadow-lg transition-all">
        <span class="material-symbols-outlined text-[20px]">person_add</span>
        <span>Tambah Akun Siswa</span>
    </a>
@endsection

@section('konten')
    <div class="flex flex-col gap-6 max-w-7xl mx-auto pt-2 pb-10">
        {{-- Card Terpadu: Filter Pencarian, Filter Kelas & Tombol Tambah Siswa --}}
        <section class="bg-surface-container-lowest rounded-3xl p-5 sm:p-6 shadow-sm border border-gray-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            {{-- Form Saring / Cari Nama, NIS, & Filter Kelas --}}
            <form method="GET" action="{{ route('superadmin.siswa') }}" class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1">
                {{-- Input Cari Nama / NIS --}}
                <div class="relative flex-1">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]">search</span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama lengkap atau NIS siswa..."
                           class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-2.5 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">
                </div>

                {{-- Filter Kelas Dropdown --}}
                <div class="relative sm:w-48">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]">school</span>
                    <select name="kelas"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-2.5 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelasList as $k)
                            <option value="{{ $k }}" @selected(request('kelas') === $k)>Kelas {{ $k }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tombol Filter & Reset --}}
                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit"
                            class="flex items-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-body font-bold px-6 py-2.5 shadow-sm transition-all">
                        <span class="material-symbols-outlined text-[18px]">search</span>
                        <span>Filter</span>
                    </button>
                    @if (request('q') || request('kelas'))
                        <a href="{{ route('superadmin.siswa') }}"
                           class="flex items-center gap-1.5 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-body text-body font-semibold px-4 py-2.5 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                            <span>Reset</span>
                        </a>
                    @endif
                </div>
            </form>

            {{-- Tombol Tambah Siswa di Toolbar --}}
            <div class="flex items-center gap-2 shrink-0 border-t lg:border-t-0 lg:border-l border-gray-100 pt-3 lg:pt-0 lg:pl-4">
                <a href="{{ route('superadmin.siswa.create') }}"
                   class="w-full lg:w-auto flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-primary-50 hover:bg-primary-100 text-primary-700 font-body text-body font-bold transition-all border border-primary-100">
                    <span class="material-symbols-outlined text-[20px] text-primary-600">person_add</span>
                    <span>Tambah Siswa</span>
                </a>
            </div>
        </section>

        {{-- Tabel Akun Siswa --}}
        <section class="bg-surface-container-lowest rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 border-b border-gray-100 bg-surface-container-low/50">
                            <th class="py-4 px-6">Informasi Siswa</th>
                            <th class="py-4 px-6">Kelas</th>
                            <th class="py-4 px-6">Total EXP</th>
                            <th class="py-4 px-6">Streak</th>
                            <th class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-body text-body">
                        @forelse ($siswaList as $s)
                            <tr class="hover:bg-surface-container-low/40 transition-colors">
                                {{-- Informasi Siswa --}}
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3.5">
                                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-primary-600 to-primary-700 text-white flex items-center justify-center font-heading font-extrabold text-sm shrink-0 uppercase shadow-sm">
                                            {{ \Illuminate\Support\Str::of($s->nama_lengkap)->substr(0, 2) }}
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-heading font-bold text-on-surface text-base">{{ $s->nama_lengkap }}</span>
                                                <span class="px-2 py-0.5 rounded-md bg-surface-container-high text-gray-600 font-label-upper text-[10px] font-bold uppercase">
                                                    {{ $s->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-2 text-gray-400 font-caption text-caption flex-wrap mt-0.5">
                                                <span>NIS: {{ $s->nis }}</span>
                                                <span>•</span>
                                                <span class="truncate max-w-[240px]">{{ $s->email }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kelas --}}
                                <td class="py-4 px-6">
                                    <span class="px-3 py-1 rounded-full bg-primary-50 text-primary-700 font-label-upper text-xs font-bold border border-primary-100 inline-block">
                                        {{ $s->kelas ? ' ' . $s->kelas : 'Belum Ada' }}
                                    </span>
                                </td>

                                {{-- Total EXP --}}
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[18px] text-yellow-500 icon-fill">bolt</span>
                                        <span class="font-heading font-extrabold text-primary-700 text-base">
                                            {{ number_format($s->exp?->total_exp ?? 0) }}
                                        </span>
                                        <span class="font-caption text-caption text-gray-400">EXP</span>
                                    </div>
                                </td>

                                {{-- Streak --}}
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[18px] text-orange-500 icon-fill">local_fire_department</span>
                                        <span class="font-heading font-bold text-orange-600 text-body">
                                            {{ $s->strek?->current_streak ?? 0 }} Hari
                                        </span>
                                    </div>
                                </td>

                                {{-- Kolom Aksi (Edit, Detail, Hapus) --}}
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Tombol Edit (Icon Saja) --}}
                                        <a href="{{ route('superadmin.siswa.edit', $s) }}"
                                           title="Edit Akun Siswa"
                                           class="w-10 h-10 rounded-full bg-primary-50 hover:bg-primary-600 text-primary-700 hover:text-white flex items-center justify-center transition-all shadow-sm border border-primary-100">
                                            <span class="material-symbols-outlined text-[19px]">edit</span>
                                        </a>

                                        {{-- Tombol Detail (Icon Saja) --}}
                                        <a href="{{ route('superadmin.siswa.show', $s) }}"
                                           title="Lihat Detail Siswa"
                                           class="w-10 h-10 rounded-full bg-surface-container-high hover:bg-primary-600 text-on-surface hover:text-white flex items-center justify-center transition-all shadow-sm">
                                            <span class="material-symbols-outlined text-[19px]">visibility</span>
                                        </a>

                                        {{-- Tombol Hapus (Icon Saja) --}}
                                        <form method="POST" action="{{ route('superadmin.siswa.destroy', $s) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun siswa {{ $s->nama_lengkap }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus Akun Siswa"
                                                    class="w-10 h-10 rounded-full bg-error-container/50 text-error flex items-center justify-center hover:bg-error-container transition-colors shadow-sm">
                                                <span class="material-symbols-outlined text-[19px]">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 px-6 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                        <div class="w-14 h-14 rounded-2xl bg-surface-container-high text-gray-400 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-[32px]">groups</span>
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            <h4 class="font-heading text-heading font-bold text-on-surface">Belum Ada Akun Siswa</h4>
                                            <p class="font-body text-body text-gray-500">Tidak ada data siswa yang cocok dengan filter atau belum didaftarkan.</p>
                                        </div>
                                        <div class="pt-2">
                                            <a href="{{ route('superadmin.siswa.create') }}"
                                               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-primary-600 text-white font-body text-body font-bold shadow-sm hover:bg-primary-700 transition-all">
                                                <span class="material-symbols-outlined text-[18px]">person_add</span>
                                                <span>Tambah Siswa Sekarang</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Footer --}}
            <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 bg-surface-container-low/20">
                <div class="text-caption text-gray-500 font-caption">
                    Menampilkan <span class="font-bold text-on-surface">{{ $siswaList->firstItem() ?? 0 }}</span> - <span class="font-bold text-on-surface">{{ $siswaList->lastItem() ?? 0 }}</span> dari total <span class="font-bold text-on-surface">{{ $siswaList->total() }}</span> siswa
                </div>
                <div>
                    {{ $siswaList->links() }}
                </div>
            </div>
        </section>
    </div>
@endsection
