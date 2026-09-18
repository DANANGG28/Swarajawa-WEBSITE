@extends('layouts.admin')

@section('aksi')
    <a href="{{ route('superadmin.siswa') }}"
       class="flex items-center gap-2 px-4 py-2.5 rounded-full bg-surface-container-low hover:bg-surface-container-high text-on-surface font-body text-body font-bold transition-all shadow-sm">
        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        <span>Kembali ke Akun Siswa</span>
    </a>
@endsection

@section('konten')
    <div class="flex flex-col gap-6 max-w-5xl mx-auto pt-4 pb-10">
        {{-- Breadcrumb Navigasi --}}
        <nav class="flex items-center gap-2 font-caption text-caption text-gray-500">
            <a href="{{ route('superadmin.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a href="{{ route('superadmin.siswa') }}" class="hover:text-primary-600 transition-colors">Akun Siswa</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface font-bold">Edit Akun Siswa</span>
        </nav>

        {{-- Hero Header Banner --}}
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 p-6 sm:p-8 text-white shadow-lg">
            <div class="absolute -right-10 -bottom-10 w-64 h-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
            <div class="absolute right-40 -top-10 w-48 h-48 rounded-full bg-yellow-300/15 blur-xl pointer-events-none"></div>

            <div class="relative z-10 flex items-center gap-5">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl bg-white/20 backdrop-blur-md border-2 border-white/40 text-white flex items-center justify-center font-display font-extrabold text-2xl uppercase shadow-inner shrink-0">
                    {{ \Illuminate\Support\Str::of($siswa->nama_lengkap)->substr(0, 2) }}
                </div>
                <div class="flex flex-col gap-1.5 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-white tracking-tight truncate">
                            Edit Akun: {{ $siswa->nama_lengkap }}
                        </h2>
                        <span class="px-3 py-1 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white font-label-upper text-[11px] font-bold tracking-wider uppercase">
                            NIS {{ $siswa->nis }}
                        </span>
                    </div>
                    <p class="font-body text-body text-white/90">
                        Ubah data profil, kelas, informasi kontak, dan kata sandi akun siswa.
                    </p>
                </div>
            </div>
        </section>

        {{-- Card Formulir Edit Siswa --}}
        <section class="bg-surface-container-lowest rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between pb-5 border-b border-gray-100 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[24px]">manage_accounts</span>
                    </div>
                    <div>
                        <h3 class="font-heading text-heading font-bold text-on-surface">Formulir Edit Akun Siswa</h3>
                        <p class="font-caption text-caption text-gray-500">Ubah data yang ingin diperbarui, lalu klik simpan perubahan</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('superadmin.siswa.update', $siswa) }}" class="flex flex-col gap-6">
                @csrf
                @method('PUT')

                {{-- Bagian 1: Data Pribadi & Akademik --}}
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-2 text-primary-700 font-label-upper text-label-upper font-bold uppercase tracking-wider pb-1 border-b border-gray-50">
                        <span class="material-symbols-outlined text-[18px]">person</span>
                        Data Pribadi & Akademik
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- NIS --}}
                        <label class="flex flex-col gap-1.5">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-semibold">
                                NIS <span class="text-error">*</span>
                            </span>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">badge</span>
                                <input type="text" name="nis" value="{{ old('nis', $siswa->nis) }}" required
                                       placeholder="Nomer Induk Siswa"
                                       class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">
                            </div>
                        </label>

                        {{-- Nama Lengkap --}}
                        <label class="flex flex-col gap-1.5">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-semibold">
                                Nama Lengkap <span class="text-error">*</span>
                            </span>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">id_card</span>
                                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $siswa->nama_lengkap) }}" required
                                       placeholder="Nama lengkap siswa"
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
                                    <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                                    <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                                </select>
                            </div>
                        </label>

                        {{-- Kelas --}}
                        <label class="flex flex-col gap-1.5">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-semibold">
                                Kelas
                            </span>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">school</span>
                                <input type="text" name="kelas" value="{{ old('kelas', $siswa->kelas) }}"
                                       placeholder="Contoh: 7A / 8B / 9C"
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
                                <input type="email" name="email" value="{{ old('email', $siswa->email) }}" required
                                       placeholder="siswa@sekolah.sch.id"
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
                                <input type="text" name="no_telpon" value="{{ old('no_telpon', $siswa->no_telpon) }}"
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
                                Kata Sandi Baru (Opsional)
                            </span>
                            <span class="font-caption text-caption text-gray-400">Kosongkan jika tidak ingin mengganti kata sandi</span>
                        </div>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">key</span>
                            <input type="password" name="password"
                                   placeholder="Ketik kata sandi baru minimal 6 karakter..."
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white transition-all">
                        </div>
                    </label>
                </div>

                {{-- Tombol Aksi --}}
                <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-end gap-3">
                    <a href="{{ route('superadmin.siswa') }}"
                       class="w-full sm:w-auto px-6 py-3 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-body text-body font-bold text-center transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="w-full sm:w-auto flex items-center justify-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 text-white font-body text-body font-bold px-8 py-3 shadow-md hover:shadow-lg transition-all">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        <span>Simpan Perubahan Siswa</span>
                    </button>
                </div>
            </form>
        </section>
    </div>
@endsection
