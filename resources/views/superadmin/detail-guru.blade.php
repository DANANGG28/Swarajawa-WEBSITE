@extends('layouts.admin')

@section('aksi')
    <div class="flex items-center gap-2">
        <a href="{{ route('superadmin.guru.edit', $guru) }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-full bg-primary-600 hover:bg-primary-700 text-white font-body text-body font-bold transition-all shadow-sm">
            <span class="material-symbols-outlined text-[20px]">edit</span>
            <span>Sunting Akun</span>
        </a>
        <a href="{{ route('superadmin.guru') }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-full bg-surface-container-low hover:bg-surface-container-high text-on-surface font-body text-body font-bold transition-all shadow-sm">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            <span>Bali</span>
        </a>
    </div>
@endsection

@section('konten')
    <div class="flex flex-col gap-6 max-w-5xl mx-auto pt-4 pb-10">
        {{-- Breadcrumb Navigasi --}}
        <nav class="flex items-center gap-2 font-caption text-caption text-gray-500">
            <a href="{{ route('superadmin.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a href="{{ route('superadmin.guru') }}" class="hover:text-primary-600 transition-colors">Akun Guru</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface font-bold truncate">{{ $guru->nama_lengkap }}</span>
        </nav>

        {{-- Hero Profile Banner --}}
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 p-6 sm:p-8 text-white shadow-lg">
            {{-- Background Decorative Shapes --}}
            <div class="absolute -right-10 -bottom-10 w-64 h-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
            <div class="absolute right-40 -top-10 w-48 h-48 rounded-full bg-yellow-300/15 blur-xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center gap-5 min-w-0">
                {{-- Avatar Inisial / Foto Besar dengan Zoom --}}
                <div title="Klik kanggo ndeleng foto luwih gedhe"
                     onclick="openPhotoModal('{{ $guru->foto_url ?? '' }}', '{{ addslashes($guru->nama_lengkap) }}', '{{ $guru->nip }}', '{{ $guru->status_pegawaian ?: '-' }}', '{{ \Illuminate\Support\Str::of($guru->nama_lengkap)->substr(0, 2) }}')"
                     class="group relative w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-white/20 backdrop-blur-md border-2 border-white/40 text-white flex items-center justify-center font-display font-extrabold text-2xl sm:text-3xl uppercase shadow-inner shrink-0 overflow-hidden cursor-pointer hover:scale-105 active:scale-95 transition-all">
                    @if ($guru->foto_url)
                        <img src="{{ $guru->foto_url }}" alt="{{ $guru->nama_lengkap }}" class="w-full h-full object-cover">
                    @else
                        {{ \Illuminate\Support\Str::of($guru->nama_lengkap)->substr(0, 2) }}
                    @endif
                    <div class="absolute inset-0 bg-black/40 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <span class="material-symbols-outlined text-[28px]">zoom_in</span>
                    </div>
                </div>

                <div class="flex flex-col gap-2.5 min-w-0">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-white tracking-tight truncate">
                            {{ $guru->nama_lengkap }}
                        </h2>
                        <span class="px-3 py-1 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white font-label-upper text-[11px] font-bold tracking-wider uppercase">
                            {{ $guru->jenis_kelamin === 'L' ? 'Lanang' : 'Wadon' }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap text-white/90 font-caption text-caption">
                        <span class="flex items-center gap-1.5 bg-white/10 px-3 py-1 rounded-full border border-white/20">
                            <span class="material-symbols-outlined text-[16px] text-yellow-300">badge</span>
                            NIP {{ $guru->nip }}
                        </span>
                        <span class="flex items-center gap-1.5 bg-white/10 px-3 py-1 rounded-full border border-white/20">
                            <span class="material-symbols-outlined text-[16px] text-yellow-300">school</span>
                            Guru Basa Jawa
                        </span>
                        <span class="flex items-center gap-1.5 bg-white/10 px-3 py-1 rounded-full border border-white/20">
                            <span class="material-symbols-outlined text-[16px] text-yellow-300">work</span>
                            {{ $guru->status_pegawaian ?: 'Status Pegawai Belum Diatur' }}
                        </span>
                    </div>
                </div>
            </div>
        </section>

        {{-- Card Utama: Informasi & Form Sunting Profil & Kredensial --}}
        <section class="bg-surface-container-lowest rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between pb-5 border-b border-gray-100 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[24px]">manage_accounts</span>
                    </div>
                    <div>
                        <h3 class="font-heading text-heading font-bold text-on-surface">Informasi & Sunting Akun Guru</h3>
                        <p class="font-caption text-caption text-gray-500">Ngatur data pribadi, informasi kontak, lan kredensial akses guru</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('superadmin.guru.update', $guru) }}" class="flex flex-col gap-6">
                @csrf
                @method('PUT')

                {{-- Bagian 1: Data Pribadi & Kepegawaian --}}
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-2 text-primary-700 font-label-upper text-label-upper font-bold uppercase tracking-wider pb-1 border-b border-gray-50">
                        <span class="material-symbols-outlined text-[18px]">person</span>
                        Data Pribadi & Kepegawaian
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- NIP --}}
                        <label class="flex flex-col gap-1.5">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-semibold">NIP</span>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">badge</span>
                                <input type="text" name="nip" value="{{ old('nip', $guru->nip) }}" required
                                       placeholder="Nomer Induk Pegawai"
                                       class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">
                            </div>
                        </label>

                        {{-- Nama Lengkap --}}
                        <label class="flex flex-col gap-1.5">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-semibold">Nama Jangkep</span>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">id_card</span>
                                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $guru->nama_lengkap) }}" required
                                       placeholder="Nama lengkap guru"
                                       class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">
                            </div>
                        </label>

                        {{-- Jenis Kelamin --}}
                        <label class="flex flex-col gap-1.5">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-semibold">Jenis Kelamin</span>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">wc</span>
                                <select name="jenis_kelamin"
                                        class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">
                                    <option value="L" {{ old('jenis_kelamin', $guru->jenis_kelamin) === 'L' ? 'selected' : '' }}>Lanang (L)</option>
                                    <option value="P" {{ old('jenis_kelamin', $guru->jenis_kelamin) === 'P' ? 'selected' : '' }}>Wadon (P)</option>
                                </select>
                            </div>
                        </label>

                        {{-- Status Pegawaian --}}
                        <label class="flex flex-col gap-1.5">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-semibold">Status Pegawaian</span>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">work</span>
                                <input type="text" name="status_pegawaian" value="{{ old('status_pegawaian', $guru->status_pegawaian) }}" placeholder="PNS / PPPK / GTT / Tetap"
                                       class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Bagian 2: Informasi Kontak --}}
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-2 text-primary-700 font-label-upper text-label-upper font-bold uppercase tracking-wider pb-1 border-b border-gray-50">
                        <span class="material-symbols-outlined text-[18px]">contact_mail</span>
                        Informasi Kontak
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Email --}}
                        <label class="flex flex-col gap-1.5">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-semibold">Email</span>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">mail</span>
                                <input type="email" name="email" value="{{ old('email', $guru->email) }}" required
                                       placeholder="guru@sekolah.sch.id"
                                       class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">
                            </div>
                        </label>

                        {{-- No Telpon --}}
                        <label class="flex flex-col gap-1.5">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-semibold">No. Telpon / WhatsApp</span>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">call</span>
                                <input type="text" name="no_telpon" value="{{ old('no_telpon', $guru->no_telpon) }}" placeholder="08xxxxxxxxxx"
                                       class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Bagian 3: Kredensial & Keamanan --}}
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-2 text-primary-700 font-label-upper text-label-upper font-bold uppercase tracking-wider pb-1 border-b border-gray-50">
                        <span class="material-symbols-outlined text-[18px]">lock</span>
                        Kredensial & Keamanan
                    </div>

                    <label class="flex flex-col gap-1.5">
                        <div class="flex items-center justify-between">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-semibold">Tembung Sandi Anyar (Opsional)</span>
                            <span class="font-caption text-caption text-gray-400">Kosongake menawa ora pengin ngganti sandi</span>
                        </div>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">key</span>
                            <input type="password" name="password" placeholder="Ketik sandi anyar minimal 6 karakter..."
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">
                        </div>
                    </label>
                </div>

                {{-- Tombol Aksi --}}
                <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-end gap-3">
                    <a href="{{ route('superadmin.guru') }}"
                       class="w-full sm:w-auto px-6 py-3 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-body text-body font-bold text-center transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="w-full sm:w-auto flex items-center justify-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 text-white font-body text-body font-bold px-8 py-3 shadow-md hover:shadow-lg transition-all">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        <span>Simpan Owahan Profil</span>
                    </button>
                </div>
            </form>
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
                <span class="font-caption">Klik area njaba kanggo nutup</span>
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
