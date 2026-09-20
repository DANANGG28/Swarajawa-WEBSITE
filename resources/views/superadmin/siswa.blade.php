@extends('layouts.admin')



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
                           class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-2.5 font-body text-sm outline-none focus:border-primary-500 focus:bg-white transition-all">
                </div>

                {{-- Filter Kelas Dropdown --}}
                <div class="relative sm:w-48">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]">school</span>
                    <select name="kelas"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-2.5 font-body text-sm outline-none focus:border-primary-500 focus:bg-white transition-all">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelasList as $k)
                            <option value="{{ $k }}" @selected(request('kelas') === $k)>Kelas {{ $k }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tombol Filter & Reset --}}
                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit"
                            class="flex items-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-sm font-bold px-5 py-2.5 shadow-sm transition-all">
                        <span class="material-symbols-outlined text-[18px]">search</span>
                        <span>Filter</span>
                    </button>
                    @if (request('q') || request('kelas'))
                        <a href="{{ route('superadmin.siswa') }}"
                           class="flex items-center gap-1.5 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-body text-sm font-semibold px-4 py-2.5 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                            <span>Reset</span>
                        </a>
                    @endif
                </div>
            </form>

            {{-- Tombol Tambah Siswa di Toolbar --}}
            <div class="flex items-center gap-2 shrink-0 border-t lg:border-t-0 lg:border-l border-gray-100 pt-3 lg:pt-0 lg:pl-4">
                <a href="{{ route('superadmin.siswa.create') }}"
                   class="w-full lg:w-auto flex items-center justify-center gap-2 px-4 py-2.5 rounded-full bg-primary-50 hover:bg-primary-100 text-primary-700 font-body text-sm font-bold transition-all border border-primary-100">
                    <span class="material-symbols-outlined text-[18px] text-primary-600">person_add</span>
                    <span>Tambah Siswa</span>
                </a>
            </div>
        </section>

        {{-- Tabel Akun Siswa --}}
        <section class="bg-surface-container-lowest rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="font-label-upper text-[11px] font-bold uppercase tracking-wider text-gray-500 border-b border-gray-100 bg-surface-container-low/50">
                            <th class="py-3.5 px-5">Informasi Siswa</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">Kelas</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">EXP</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">Streak</th>
                            <th class="py-3.5 px-5 text-right whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-body text-sm">
                        @forelse ($siswaList as $s)
                            <tr class="hover:bg-surface-container-low/40 transition-colors">
                                {{-- Informasi Siswa --}}
                                <td class="py-3.5 px-5">
                                    <div class="flex items-center gap-3">
                                        <div title="Klik untuk melihat foto lebih besar"
                                             onclick="openPhotoModal('{{ $s->foto_url ?? '' }}', '{{ addslashes($s->nama_lengkap) }}', '{{ $s->nis }}', '{{ $s->kelas ? 'Kelas ' . $s->kelas : '-' }}', '{{ \Illuminate\Support\Str::of($s->nama_lengkap)->substr(0, 2) }}')"
                                             class="group relative w-9 h-9 rounded-xl overflow-hidden bg-gradient-to-br from-primary-600 to-primary-700 text-white flex items-center justify-center font-heading font-extrabold text-xs shrink-0 uppercase shadow-sm cursor-pointer hover:scale-105 active:scale-95 transition-all">
                                            @if ($s->foto_url)
                                                <img src="{{ $s->foto_url }}" alt="{{ $s->nama_lengkap }}" class="w-full h-full object-cover">
                                            @else
                                                {{ \Illuminate\Support\Str::of($s->nama_lengkap)->substr(0, 2) }}
                                            @endif
                                            <div class="absolute inset-0 bg-black/40 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                <span class="material-symbols-outlined text-[16px]">zoom_in</span>
                                            </div>
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-heading font-bold text-on-surface text-sm leading-snug">{{ $s->nama_lengkap }}</span>
                                                <!-- <span class="px-2 py-0.5 rounded-md bg-surface-container-high text-gray-600 font-label-upper text-[10px] font-bold uppercase shrink-0">
                                                    {{ $s->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                                </span> -->
                                            </div>
                                            <div class="flex items-center gap-2 text-gray-400 font-caption text-xs flex-wrap mt-0.5">
                                                <span class="font-medium text-gray-500">NIS: {{ $s->nis }}</span>
                                                <span class="text-gray-300">•</span>
                                                <!-- <span class="truncate max-w-[200px] sm:max-w-[240px] text-gray-400">{{ $s->email }}</span> -->
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kelas --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full bg-primary-50 text-primary-700 font-label-upper text-xs font-bold border border-primary-100 inline-block">
                                        {{ $s->kelas ? 'Kelas ' . $s->kelas : 'Belum Ada' }}
                                    </span>
                                </td>

                                {{-- Total EXP --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[15px] text-amber-500 icon-fill shrink-0">bolt</span>
                                        <span class="font-heading font-extrabold text-primary-700 text-sm">
                                            {{ number_format($s->exp?->total_exp ?? 0) }}
                                        </span>
                                        <span class="font-caption text-xs text-gray-400 font-medium">EXP</span>
                                    </div>
                                </td>

                                {{-- Streak --}}
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[16px] text-orange-500 icon-fill shrink-0">local_fire_department</span>
                                        <span class="font-heading font-bold text-orange-600 text-xs sm:text-sm">
                                            {{ $s->strek?->current_streak ?? 0 }} Hari
                                        </span>
                                    </div>
                                </td>

                                {{-- Kolom Aksi (Edit, Detail, Hapus) --}}
                                <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        {{-- Tombol Edit (Icon Saja) --}}
                                        <a href="{{ route('superadmin.siswa.edit', $s) }}"
                                           title="Edit Akun Siswa"
                                           class="w-8 h-8 rounded-full bg-primary-50 hover:bg-primary-600 text-primary-700 hover:text-white flex items-center justify-center transition-all shadow-sm border border-primary-100">
                                            <span class="material-symbols-outlined text-[16px]">edit</span>
                                        </a>

                                        {{-- Tombol Detail (Icon Saja) --}}
                                        <a href="{{ route('superadmin.siswa.show', $s) }}"
                                           title="Lihat Detail Siswa"
                                           class="w-8 h-8 rounded-full bg-surface-container-high hover:bg-primary-600 text-on-surface hover:text-white flex items-center justify-center transition-all shadow-sm">
                                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                                        </a>

                                        {{-- Tombol Hapus (Icon Saja) --}}
                                        <form method="POST" action="{{ route('superadmin.siswa.destroy', $s) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun siswa {{ $s->nama_lengkap }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus Akun Siswa"
                                                    class="w-8 h-8 rounded-full bg-error-container/50 text-error flex items-center justify-center hover:bg-error-container transition-colors shadow-sm">
                                                <span class="material-symbols-outlined text-[16px]">delete</span>
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
                                            <h4 class="font-heading text-sm font-bold text-on-surface">Belum Ada Akun Siswa</h4>
                                            <p class="font-body text-xs text-gray-500">Tidak ada data siswa yang cocok dengan filter atau belum didaftarkan.</p>
                                        </div>
                                        <div class="pt-2">
                                            <a href="{{ route('superadmin.siswa.create') }}"
                                               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-primary-600 text-white font-body text-xs font-bold shadow-sm hover:bg-primary-700 transition-all">
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
            <div class="px-5 py-3.5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 bg-surface-container-low/20">
                <div class="text-xs text-gray-500 font-caption">
                    Menampilkan <span class="font-bold text-on-surface">{{ $siswaList->firstItem() ?? 0 }}</span> - <span class="font-bold text-on-surface">{{ $siswaList->lastItem() ?? 0 }}</span> dari total <span class="font-bold text-on-surface">{{ $siswaList->total() }}</span> siswa
                </div>
                <div>
                    {{ $siswaList->links() }}
                </div>
            </div>
        </section>
    </div>

    {{-- Modal Zoom Foto Siswa --}}
    <div id="photoModal" onclick="handleBackdropClick(event)"
         class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300">
        <div id="photoModalContent" class="bg-surface-container-lowest rounded-3xl overflow-hidden max-w-md w-full shadow-2xl scale-90 transition-all duration-300 border border-gray-100">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex flex-col min-w-0 pr-4">
                    <h4 id="modalStudentName" class="font-heading text-lg font-bold text-on-surface truncate">Nama Siswa</h4>
                    <span id="modalStudentNis" class="font-caption text-xs text-gray-500">NIS: -</span>
                </div>
                <button type="button" onclick="closePhotoModal()" class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition-colors shrink-0">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <div class="w-full h-80 bg-gray-900 flex items-center justify-center overflow-hidden relative">
                <img id="modalPhotoImage" src="" alt="Foto Siswa" class="w-full h-full object-contain hidden">
                <div id="modalPhotoInitials" class="w-28 h-28 rounded-3xl bg-primary-600 text-white flex items-center justify-center font-display font-extrabold text-4xl uppercase shadow-lg hidden">
                </div>
            </div>
            <div class="px-6 py-3.5 bg-surface-container-low/40 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                <span id="modalStudentClass" class="font-label-upper font-bold uppercase text-primary-700">Kelas</span>
                <span class="font-caption">Klik area luar untuk menutup</span>
            </div>
        </div>
    </div>

    <script>
        function openPhotoModal(imageUrl, name, nis, studentClass, initials) {
            const modal = document.getElementById('photoModal');
            const content = document.getElementById('photoModalContent');
            const img = document.getElementById('modalPhotoImage');
            const init = document.getElementById('modalPhotoInitials');

            document.getElementById('modalStudentName').textContent = name;
            document.getElementById('modalStudentNis').textContent = 'NIS: ' + nis;
            document.getElementById('modalStudentClass').textContent = studentClass;

            if (imageUrl && imageUrl.trim() !== '') {
                img.src = imageUrl;
                img.classList.remove('hidden');
                init.classList.add('hidden');
            } else {
                img.classList.add('hidden');
                init.textContent = initials;
                init.classList.remove('hidden');
            }

            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100', 'pointer-events-auto');
            content.classList.remove('scale-90');
            content.classList.add('scale-100');
            document.body.classList.add('overflow-hidden');
        }

        function closePhotoModal() {
            const modal = document.getElementById('photoModal');
            const content = document.getElementById('photoModalContent');
            if (!modal) return;

            modal.classList.remove('opacity-100', 'pointer-events-auto');
            modal.classList.add('opacity-0', 'pointer-events-none');
            content.classList.remove('scale-100');
            content.classList.add('scale-90');
            document.body.classList.remove('overflow-hidden');
        }

        function handleBackdropClick(e) {
            if (e.target.id === 'photoModal') {
                closePhotoModal();
            }
        }

        window.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePhotoModal();
            }
        });
    </script>
@endsection
