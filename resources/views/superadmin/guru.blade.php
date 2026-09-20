@extends('layouts.admin')

@section('aksi')

@endsection

@section('konten')
    <div class="flex flex-col gap-6 max-w-7xl mx-auto pt-2 pb-10">
        {{-- Card Terpadu: Filter Pencarian & Tombol Tambah Guru --}}
        <section class="bg-surface-container-lowest rounded-3xl p-5 sm:p-6 shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            {{-- Form Saring / Cari Nama & NIP --}}
            <form method="GET" action="{{ route('superadmin.guru') }}" class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1">
                <div class="relative flex-1">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]">search</span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama lengkap atau NIP guru..."
                           class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-2.5 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit"
                            class="flex items-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-body font-bold px-6 py-2.5 shadow-sm transition-all">
                        <span class="material-symbols-outlined text-[18px]">search</span>
                        <span>Filter</span>
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
                            {{-- Avatar / Foto Guru dengan Fitur Klik Zoom --}}
                            <div title="Klik untuk melihat foto lebih besar"
                                 onclick="openPhotoModal('{{ $g->foto_url ?? '' }}', '{{ addslashes($g->nama_lengkap) }}', '{{ $g->nip }}', '{{ $g->status_pegawaian ?: '-' }}', '{{ \Illuminate\Support\Str::of($g->nama_lengkap)->substr(0, 2) }}')"
                                 class="group relative w-12 h-12 rounded-2xl overflow-hidden bg-gradient-to-br from-primary-600 to-primary-700 text-white flex items-center justify-center font-heading font-extrabold text-base shrink-0 uppercase shadow-sm cursor-pointer hover:scale-105 active:scale-95 transition-all">
                                @if ($g->foto_url)
                                    <img src="{{ $g->foto_url }}" alt="{{ $g->nama_lengkap }}" class="w-full h-full object-cover">
                                @else
                                    {{ \Illuminate\Support\Str::of($g->nama_lengkap)->substr(0, 2) }}
                                @endif
                                <div class="absolute inset-0 bg-black/40 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="material-symbols-outlined text-[20px]">zoom_in</span>
                                </div>
                            </div>

                            <div class="flex flex-col min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-heading text-heading font-bold text-on-surface truncate">{{ $g->nama_lengkap }}</h3>
                                    <span class="px-2.5 py-0.5 rounded-full bg-surface-container-high text-gray-700 font-label-upper text-[11px] font-bold uppercase">
                                        {{ $g->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
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

                        {{-- Action Buttons (Icon Only) --}}
                        <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                            {{-- Tombol Edit (Icon Pensil Saja) --}}
                            <a href="{{ route('superadmin.guru.edit', $g) }}"
                               title="Edit Akun Guru"
                               class="w-10 h-10 rounded-full bg-primary-50 hover:bg-primary-600 text-primary-700 hover:text-white flex items-center justify-center transition-all shadow-sm border border-primary-100">
                                <span class="material-symbols-outlined text-[19px]">edit</span>
                            </a>

                            {{-- Tombol Detail Akun (Icon Saja) --}}
                            <a href="{{ route('superadmin.guru.show', $g) }}"
                               title="Lihat Detail Akun"
                               class="w-10 h-10 rounded-full bg-surface-container-high hover:bg-primary-600 text-on-surface hover:text-white flex items-center justify-center transition-all shadow-sm">
                                <span class="material-symbols-outlined text-[19px]">visibility</span>
                            </a>

                            {{-- Tombol Hapus (Icon Saja) --}}
                            <form method="POST" action="{{ route('superadmin.guru.destroy', $g) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun guru {{ $g->nama_lengkap }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" title="Hapus Akun" class="w-10 h-10 rounded-full bg-error-container/50 text-error flex items-center justify-center hover:bg-error-container transition-colors">
                                    <span class="material-symbols-outlined text-[19px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-surface-container-lowest rounded-3xl p-12 text-center border border-gray-100 shadow-sm flex flex-col items-center justify-center gap-3">
                    <div class="w-16 h-16 rounded-2xl bg-surface-container-high text-gray-400 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[36px]">{{ request('q') ? 'person_search' : 'group_off' }}</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <h4 class="font-heading text-heading font-bold text-on-surface">
                            {{ request('q') ? 'Data Guru Tidak Ditemukan' : 'Belum Ada Akun Guru' }}
                        </h4>
                        <p class="font-body text-body text-gray-500">
                            {{ request('q') ? 'Tidak ada data guru yang cocok dengan filter pencarian "' . request('q') . '".' : 'Belum ada akun guru yang terdaftar dalam sistem.' }}
                        </p>
                    </div>
                    <div class="pt-2">
                        @if (request('q'))
                            <a href="{{ route('superadmin.guru') }}"
                               class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-body text-body font-bold transition-all">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                                <span>Reset Pencarian</span>
                            </a>
                        @else
                            <a href="{{ route('superadmin.guru.create') }}"
                               class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-primary-600 text-white font-body text-body font-bold shadow-sm hover:bg-primary-700 transition-all">
                                <span class="material-symbols-outlined text-[18px]">person_add</span>
                                <span>Daftarkan Guru Baru Sekarang</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endforelse

            {{-- Pagination Footer --}}
            @if ($guruList->total() > 0)
                <div class="bg-surface-container-lowest rounded-2xl p-4 sm:px-6 shadow-sm border border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 mt-2">
                    <div class="text-xs text-gray-500 font-caption">
                        Menampilkan <span class="font-bold text-on-surface">{{ $guruList->firstItem() ?? 0 }}</span> - <span class="font-bold text-on-surface">{{ $guruList->lastItem() ?? 0 }}</span> dari total <span class="font-bold text-on-surface">{{ $guruList->total() }}</span> guru
                    </div>
                    <div>
                        {{ $guruList->links() }}
                    </div>
                </div>
            @endif
        </section>
    </div>

    {{-- Modal Pop-up Zoom Foto Guru --}}
    <div id="photoModal"
         class="fixed inset-0 z-50 bg-black/75 backdrop-blur-md flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300 ease-out"
         onclick="handleBackdropClick(event)">
        <div id="photoModalContent"
             class="relative max-w-md w-full bg-surface-container-lowest rounded-3xl overflow-hidden shadow-2xl scale-90 transition-all duration-300 ease-out border border-white/20">
            {{-- Modal Header --}}
            <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100 bg-surface-container-low/50">
                <div class="flex flex-col min-w-0 pr-3">
                    <h4 id="modalTeacherName" class="font-heading text-heading font-bold text-on-surface truncate">Nama Guru</h4>
                    <span id="modalTeacherNip" class="font-caption text-caption text-gray-500">NIP: -</span>
                </div>
                <button type="button"
                        onclick="closePhotoModal()"
                        title="Tutup (Esc)"
                        class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition-colors shrink-0">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            {{-- Modal Body: Zoom Image / Avatar --}}
            <div class="p-6 flex flex-col items-center justify-center bg-gray-900/5 min-h-[260px]">
                <div id="modalImageContainer" class="w-64 h-64 sm:w-72 sm:h-72 rounded-3xl overflow-hidden shadow-lg border-4 border-white bg-gradient-to-br from-primary-600 to-primary-700 flex items-center justify-center">
                    <img id="modalPhotoImage" src="" alt="Foto Guru" class="w-full h-full object-cover hidden">
                    <span id="modalPhotoInitials" class="text-white font-display text-6xl font-extrabold uppercase">SJ</span>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-3.5 bg-surface-container-low/40 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                <span id="modalTeacherStatus" class="font-label-upper font-bold uppercase text-primary-700">Status Pegawai</span>
                <span class="font-caption">Klik area luar untuk menutup</span>
            </div>
        </div>
    </div>

    <script>
        function openPhotoModal(imageUrl, name, nip, status, initials) {
            const modal = document.getElementById('photoModal');
            const content = document.getElementById('photoModalContent');
            const img = document.getElementById('modalPhotoImage');
            const init = document.getElementById('modalPhotoInitials');

            document.getElementById('modalTeacherName').textContent = name;
            document.getElementById('modalTeacherNip').textContent = 'NIP: ' + nip;
            document.getElementById('modalTeacherStatus').textContent = status;

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
