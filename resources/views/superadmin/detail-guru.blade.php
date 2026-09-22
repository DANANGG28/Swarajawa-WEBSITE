@extends('layouts.admin')

@section('aksi')
    <div class="flex items-center gap-2">
        <a href="{{ route('superadmin.guru.edit', ['guru' => $guru->id, 'role' => $isSuperadmin ? 'superadmin' : 'guru']) }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-full bg-primary-600 hover:bg-primary-700 text-white font-body text-body font-bold transition-all shadow-sm">
            <span class="material-symbols-outlined text-[20px]">edit</span>
            <span>Edit Akun</span>
        </a>
        <a href="{{ route('superadmin.guru') }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-full bg-surface-container-low hover:bg-surface-container-high text-on-surface font-body text-body font-bold transition-all shadow-sm">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            <span>Kembali</span>
        </a>
    </div>
@endsection

@section('konten')
    <div class="flex flex-col gap-6 max-w-5xl mx-auto pt-4 pb-10">
        {{-- Breadcrumb Navigasi --}}
        <nav class="flex items-center gap-2 font-caption text-caption text-gray-500">
            <a href="{{ route('superadmin.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a href="{{ route('superadmin.guru') }}" class="hover:text-primary-600 transition-colors">Pengelola</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface font-bold truncate">{{ $guru->nama_lengkap }}</span>
        </nav>

        {{-- Hero Profile Banner --}}
        <section class="relative overflow-hidden rounded-3xl {{ $isSuperadmin ? 'bg-gradient-to-r from-indigo-700 via-primary-700 to-primary-600' : 'bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500' }} p-6 sm:p-8 text-white shadow-lg">
            {{-- Background Decorative Shapes --}}
            <div class="absolute -right-10 -bottom-10 w-64 h-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
            <div class="absolute right-40 -top-10 w-48 h-48 rounded-full bg-yellow-300/15 blur-xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center gap-5 min-w-0">
                {{-- Avatar Inisial / Foto Besar dengan Zoom --}}
                <div title="Klik untuk melihat foto lebih besar"
                     onclick="openPhotoModal('{{ $guru->foto_url ?? '' }}', '{{ addslashes($guru->nama_lengkap) }}', '{{ $guru->nip ?? ($isSuperadmin ? 'Superadmin System' : '-') }}', '{{ $isSuperadmin ? 'Super Administrator' : ($guru->status_pegawaian ?: '-') }}', '{{ \Illuminate\Support\Str::of($guru->nama_lengkap)->substr(0, 2) }}')"
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
                        @if ($isSuperadmin)
                            <span class="px-3 py-1 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white font-label-upper text-[11px] font-bold tracking-wider uppercase flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px]">shield_person</span>
                                Superadmin
                            </span>
                        @elseif ($guru->jenis_kelamin)
                            <span class="px-3 py-1 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white font-label-upper text-[11px] font-bold tracking-wider uppercase">
                                {{ $guru->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 flex-wrap text-white/90 font-caption text-caption">
                        @if ($isSuperadmin)
                            <span class="flex items-center gap-1.5 bg-white/10 px-3 py-1 rounded-full border border-white/20">
                                <span class="material-symbols-outlined text-[16px] text-yellow-300">admin_panel_settings</span>
                                Hak Akses Penuh Sistem
                            </span>
                            @if ($guru->no_telpon)
                                <span class="flex items-center gap-1.5 bg-white/10 px-3 py-1 rounded-full border border-white/20">
                                    <span class="material-symbols-outlined text-[16px] text-yellow-300">call</span>
                                    {{ $guru->no_telpon }}
                                </span>
                            @endif
                        @else
                            <span class="flex items-center gap-1.5 bg-white/10 px-3 py-1 rounded-full border border-white/20">
                                <span class="material-symbols-outlined text-[16px] text-yellow-300">badge</span>
                                NIP {{ $guru->nip }}
                            </span>
                            <span class="flex items-center gap-1.5 bg-white/10 px-3 py-1 rounded-full border border-white/20">
                                <span class="material-symbols-outlined text-[16px] text-yellow-300">school</span>
                                Guru Bahasa Jawa
                            </span>
                            <span class="flex items-center gap-1.5 bg-white/10 px-3 py-1 rounded-full border border-white/20">
                                <span class="material-symbols-outlined text-[16px] text-yellow-300">work</span>
                                {{ $guru->status_pegawaian ?: 'Status Pegawai Belum Diatur' }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        {{-- Card Utama: Informasi & Form Sunting Profil & Kredensial --}}
        <section class="bg-surface-container-lowest rounded-3xl p-6 sm:p-8 shadow-sm border-2 border-slate-200/80">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-5 border-b border-gray-100 gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl {{ $isSuperadmin ? 'bg-indigo-50 text-indigo-600' : 'bg-primary-50 text-primary-600' }} flex items-center justify-center shrink-0 shadow-sm border {{ $isSuperadmin ? 'border-indigo-100' : 'border-primary-100' }}">
                        <span class="material-symbols-outlined text-[26px]">manage_accounts</span>
                    </div>
                    <div>
                        <h3 class="font-heading text-lg sm:text-xl font-bold text-on-surface">Informasi & Edit Akun {{ $isSuperadmin ? 'Superadmin' : 'Guru' }}</h3>
                        <p class="font-caption text-caption text-gray-500">Kelola data pribadi, informasi kontak, dan kredensial akses akun</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 self-start sm:self-center">
                    @if ($isSuperadmin)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200 shadow-sm">
                            <span class="material-symbols-outlined text-[16px]">shield_person</span>
                            Superadmin
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm">
                            <span class="material-symbols-outlined text-[16px]">school</span>
                            Guru Pengajar
                        </span>
                    @endif
                </div>
            </div>

            @if ($errors->any())
                <div class="p-4 rounded-2xl bg-error-container/20 border border-error/30 text-error flex items-start gap-3 mb-6 shadow-sm">
                    <span class="material-symbols-outlined text-[22px] shrink-0 mt-0.5">error</span>
                    <div class="flex flex-col gap-1">
                        <span class="font-body text-sm font-bold">Terdapat kesalahan pada formulir pengubahan:</span>
                        <ul class="list-disc list-inside text-xs space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('superadmin.guru.update', ['guru' => $guru->id, 'role' => $isSuperadmin ? 'superadmin' : 'guru']) }}" class="flex flex-col gap-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="role" value="{{ $isSuperadmin ? 'superadmin' : 'guru' }}">

                {{-- Bagian 1: Data Pribadi & Kepegawaian --}}
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-2 text-primary-700 font-label-upper text-label-upper font-bold uppercase tracking-wider pb-1 border-b border-gray-50">
                        <span class="material-symbols-outlined text-[18px]">person</span>
                        Data Pribadi & Identitas
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @if (!$isSuperadmin)
                            {{-- NIP --}}
                            <label class="flex flex-col gap-1.5">
                                <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-semibold">NIP</span>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">badge</span>
                                    <input type="text" name="nip" value="{{ old('nip', $guru->nip) }}" required
                                           inputmode="numeric" pattern="[0-9]*" maxlength="25"
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                           placeholder="Contoh: 198501012010011001"
                                           class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">
                                </div>
                                @error('nip')
                                    <span class="text-error text-xs font-caption">{{ $message }}</span>
                                @enderror
                            </label>
                        @endif

                        {{-- Nama Lengkap --}}
                        <label class="flex flex-col gap-1.5 {{ $isSuperadmin ? 'sm:col-span-2' : '' }}">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-semibold">Nama Lengkap</span>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">id_card</span>
                                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $guru->nama_lengkap) }}" required
                                       autocomplete="name" maxlength="150"
                                       placeholder="Nama lengkap pengelola"
                                       class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">
                            </div>
                            @error('nama_lengkap')
                                <span class="text-error text-xs font-caption">{{ $message }}</span>
                            @enderror
                        </label>

                        @if (!$isSuperadmin)
                            {{-- Jenis Kelamin --}}
                            <label class="flex flex-col gap-1.5">
                                <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-semibold">Jenis Kelamin</span>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">wc</span>
                                    <select name="jenis_kelamin" required
                                            class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">
                                        <option value="L" {{ old('jenis_kelamin', $guru->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                                        <option value="P" {{ old('jenis_kelamin', $guru->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                                    </select>
                                </div>
                                @error('jenis_kelamin')
                                    <span class="text-error text-xs font-caption">{{ $message }}</span>
                                @enderror
                            </label>

                            {{-- Status Pegawaian --}}
                            <label class="flex flex-col gap-1.5">
                                <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-semibold">Status Pegawaian</span>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">work</span>
                                    <input type="text" name="status_pegawaian" value="{{ old('status_pegawaian', $guru->status_pegawaian) }}" placeholder="PNS / PPPK / GTT / Tetap"
                                           class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">
                                </div>
                                @error('status_pegawaian')
                                    <span class="text-error text-xs font-caption">{{ $message }}</span>
                                @enderror
                            </label>
                        @endif
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
                                       autocomplete="email" maxlength="255"
                                       placeholder="pengelola@sekolah.sch.id"
                                       class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">
                            </div>
                            @error('email')
                                <span class="text-error text-xs font-caption">{{ $message }}</span>
                            @enderror
                        </label>

                        {{-- No Telpon --}}
                        <label class="flex flex-col gap-1.5">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-semibold">No. Telpon / WhatsApp</span>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">call</span>
                                <input type="tel" name="no_telpon" value="{{ old('no_telpon', $guru->no_telpon) }}"
                                       inputmode="numeric" pattern="[0-9]*" maxlength="16"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                       placeholder="08xxxxxxxxxx"
                                       class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">
                            </div>
                            @error('no_telpon')
                                <span class="text-error text-xs font-caption">{{ $message }}</span>
                            @enderror
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
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-semibold">Kata Sandi Baru (Opsional)</span>
                            <span class="font-caption text-caption text-gray-400">Kosongkan jika tidak ingin mengganti kata sandi</span>
                        </div>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">key</span>
                            <input type="password" name="password" minlength="6"
                                   placeholder="Ketik kata sandi baru minimal 6 karakter..."
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">
                        </div>
                        @error('password')
                            <span class="text-error text-xs font-caption">{{ $message }}</span>
                        @enderror
                    </label>
                </div>

                {{-- Tombol Aksi 3D Duolingo / Swarajawa Style --}}
                <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-end gap-3.5">
                    <a href="{{ route('superadmin.guru') }}"
                       class="w-full sm:w-auto px-7 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-body text-body font-bold text-center border-b-4 border-slate-300 active:border-b-0 active:translate-y-1 transition-all">
                        Batal
                    </a>
                    <button type="submit"
                            class="w-full sm:w-auto flex items-center justify-center gap-2 rounded-2xl {{ $isSuperadmin ? 'bg-indigo-600 hover:bg-indigo-500 border-indigo-800' : 'bg-primary-600 hover:bg-primary-500 border-primary-800' }} text-white font-body text-body font-bold px-8 py-3 shadow-md hover:shadow-lg border-b-4 active:border-b-0 active:translate-y-1 transition-all">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        <span>Simpan Perubahan {{ $isSuperadmin ? 'Superadmin' : 'Guru' }}</span>
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
