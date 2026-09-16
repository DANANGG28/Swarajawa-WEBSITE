@extends('layouts.admin')

@section('aksi')
    <a href="{{ route('superadmin.guru') }}"
       class="flex items-center gap-2 px-4 py-2.5 rounded-full bg-surface-container-low hover:bg-surface-container-high text-on-surface font-body text-body font-bold transition-all shadow-sm">
        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        <span>Bali menyang Akun Guru</span>
    </a>
@endsection

@section('konten')
    <div class="flex flex-col gap-6 max-w-5xl mx-auto pt-4 pb-10">
        {{-- Breadcrumb Navigasi --}}
        <nav class="flex items-center gap-2 font-caption text-caption text-gray-500">
            <a href="{{ route('superadmin.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a href="{{ route('superadmin.guru') }}" class="hover:text-primary-600 transition-colors">Akun Guru</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface font-bold">Sunting Akun Guru</span>
        </nav>

        {{-- Hero Header Banner --}}
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 p-6 sm:p-8 text-white shadow-lg">
            <div class="absolute -right-10 -bottom-10 w-64 h-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
            <div class="absolute right-40 -top-10 w-48 h-48 rounded-full bg-yellow-300/15 blur-xl pointer-events-none"></div>

            <div class="relative z-10 flex items-center gap-5">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl bg-white/20 backdrop-blur-md border-2 border-white/40 text-white flex items-center justify-center shadow-inner shrink-0 overflow-hidden">
                    @if ($guru->foto_url)
                        <img src="{{ $guru->foto_url }}" alt="{{ $guru->nama_lengkap }}" class="w-full h-full object-cover">
                    @else
                        <span class="material-symbols-outlined text-[36px] sm:text-[44px]">edit_note</span>
                    @endif
                </div>
                <div class="flex flex-col gap-1.5 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-white tracking-tight truncate">
                            Sunting Akun: {{ $guru->nama_lengkap }}
                        </h2>
                        <span class="px-3 py-1 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white font-label-upper text-[11px] font-bold tracking-wider uppercase">
                            NIP {{ $guru->nip }}
                        </span>
                    </div>
                    <p class="font-body text-body text-white/90">
                        Owahi data profil, informasi kontak, foto profil, lan tembung sandi akun guru.
                    </p>
                </div>
            </div>
        </section>

        {{-- Card Formulir Sunting --}}
        <section class="bg-surface-container-lowest rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between pb-5 border-b border-gray-100 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[24px]">manage_accounts</span>
                    </div>
                    <div>
                        <h3 class="font-heading text-heading font-bold text-on-surface">Formulir Sunting Akun Guru</h3>
                        <p class="font-caption text-caption text-gray-500">Owahi data sing pengin dianyari, banjur klik simpan owahan</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('superadmin.guru.update', $guru) }}" enctype="multipart/form-data" class="flex flex-col gap-6">
                @csrf
                @method('PUT')

                {{-- Bagian Foto Profil Guru --}}
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-2 text-primary-700 font-label-upper text-label-upper font-bold uppercase tracking-wider pb-1 border-b border-gray-50">
                        <span class="material-symbols-outlined text-[18px]">add_a_photo</span>
                        Foto Profil Guru (Opsional)
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-5 p-4 rounded-2xl bg-surface-container-low border border-gray-100">
                        <div id="fotoPreviewBox" class="w-20 h-20 rounded-2xl bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-2xl uppercase shrink-0 overflow-hidden border border-primary-200">
                            @if ($guru->foto_url)
                                <img id="fotoPreview" src="{{ $guru->foto_url }}" alt="{{ $guru->nama_lengkap }}" class="w-full h-full object-cover">
                                <span id="fotoPlaceholder" class="material-symbols-outlined text-[36px] text-primary-500 hidden">image</span>
                            @else
                                <span id="fotoPlaceholder" class="material-symbols-outlined text-[36px] text-primary-500">image</span>
                                <img id="fotoPreview" src="" alt="Preview" class="w-full h-full object-cover hidden">
                            @endif
                        </div>
                        <div class="flex-1 flex flex-col gap-1.5 w-full">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-700 font-semibold">Ganti Berkas Foto Profil</span>
                            <input type="file" name="foto" id="fotoInput" accept="image/jpeg,image/png,image/jpg,image/webp"
                                   onchange="previewImage(this)"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-600 file:text-white hover:file:bg-primary-700 cursor-pointer">
                            <span class="font-caption text-caption text-gray-400">Kosongake menawa ora pengin ngganti foto. Format: JPG, PNG, WEBP (Maks 2 MB).</span>
                        </div>
                    </div>
                </div>

                {{-- Bagian 1: Data Pribadi & Kepegawaian --}}
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-2 text-primary-700 font-label-upper text-label-upper font-bold uppercase tracking-wider pb-1 border-b border-gray-50">
                        <span class="material-symbols-outlined text-[18px]">person</span>
                        Data Pribadi & Kepegawaian
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- NIP --}}
                        <label class="flex flex-col gap-1.5">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-semibold">
                                NIP <span class="text-error">*</span>
                            </span>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">badge</span>
                                <input type="text" name="nip" value="{{ old('nip', $guru->nip) }}" required
                                       placeholder="Nomer Induk Pegawai"
                                       class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">
                            </div>
                        </label>

                        {{-- Nama Lengkap --}}
                        <label class="flex flex-col gap-1.5">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-semibold">
                                Nama Jangkep <span class="text-error">*</span>
                            </span>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">id_card</span>
                                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $guru->nama_lengkap) }}" required
                                       placeholder="Nama lengkap guru"
                                       class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">
                            </div>
                        </label>

                        {{-- Jenis Kelamin --}}
                        <label class="flex flex-col gap-1.5">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-semibold">
                                Jenis Kelamin <span class="text-error">*</span>
                            </span>
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
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-semibold">
                                Status Pegawaian
                            </span>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">work</span>
                                <input type="text" name="status_pegawaian" value="{{ old('status_pegawaian', $guru->status_pegawaian) }}"
                                       placeholder="PNS / PPPK / GTT / Tetap Yayasan"
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
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-semibold">
                                Alamat Email <span class="text-error">*</span>
                            </span>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">mail</span>
                                <input type="email" name="email" value="{{ old('email', $guru->email) }}" required
                                       placeholder="guru@sekolah.sch.id"
                                       class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">
                            </div>
                        </label>

                        {{-- No Telpon --}}
                        <label class="flex flex-col gap-1.5">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-semibold">
                                No. Telpon / WhatsApp
                            </span>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">call</span>
                                <input type="text" name="no_telpon" value="{{ old('no_telpon', $guru->no_telpon) }}"
                                       placeholder="08xxxxxxxxxx"
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
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-semibold">
                                Tembung Sandi Anyar (Opsional)
                            </span>
                            <span class="font-caption text-caption text-gray-400">Kosongake menawa ora pengin ngganti sandi</span>
                        </div>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">key</span>
                            <input type="password" name="password"
                                   placeholder="Ketik sandi anyar minimal 6 karakter..."
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
                        <span>Simpan Owahan Guru</span>
                    </button>
                </div>
            </form>
        </section>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('fotoPreview');
            const placeholder = document.getElementById('fotoPlaceholder');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    if (placeholder) placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
