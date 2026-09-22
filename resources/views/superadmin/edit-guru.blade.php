@extends('layouts.admin')

@section('aksi')
    <a href="{{ route('superadmin.guru') }}"
       class="flex items-center gap-2 px-4 py-2.5 rounded-full bg-surface-container-low hover:bg-surface-container-high text-on-surface font-body text-body font-bold transition-all shadow-sm">
        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        <span>Kembali ke Pengelola</span>
    </a>
@endsection

@section('konten')
    <div class="flex flex-col gap-6 max-w-5xl mx-auto pt-4 pb-10">
        {{-- Breadcrumb Navigasi --}}
        <nav class="flex items-center gap-2 font-caption text-caption text-gray-500">
            <a href="{{ route('superadmin.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a href="{{ route('superadmin.guru') }}" class="hover:text-primary-600 transition-colors">Pengelola</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface font-bold">Edit Akun {{ $isSuperadmin ? 'Superadmin' : 'Guru' }}</span>
        </nav>

        {{-- Hero Header Banner --}}
        <section class="relative overflow-hidden rounded-3xl {{ $isSuperadmin ? 'bg-gradient-to-r from-indigo-700 via-primary-700 to-primary-600' : 'bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500' }} p-6 sm:p-8 text-white shadow-lg">
            <div class="absolute -right-10 -bottom-10 w-64 h-64 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
            <div class="absolute right-40 -top-10 w-48 h-48 rounded-full bg-yellow-300/15 blur-xl pointer-events-none"></div>

            <div class="relative z-10 flex items-center gap-5">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl bg-white/20 backdrop-blur-md border-2 border-white/40 text-white flex items-center justify-center shadow-inner shrink-0 overflow-hidden">
                    @if ($guru->foto_url)
                        <img src="{{ $guru->foto_url }}" alt="{{ $guru->nama_lengkap }}" class="w-full h-full object-cover">
                    @else
                        <span class="font-display font-extrabold text-2xl uppercase">{{ \Illuminate\Support\Str::of($guru->nama_lengkap)->substr(0, 2) }}</span>
                    @endif
                </div>
                <div class="flex flex-col gap-1.5 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-white tracking-tight truncate">
                            Edit Akun: {{ $guru->nama_lengkap }}
                        </h2>
                        @if ($isSuperadmin)
                            <span class="px-3 py-1 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white font-label-upper text-[11px] font-bold tracking-wider uppercase flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px]">shield_person</span>
                                Superadmin
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white font-label-upper text-[11px] font-bold tracking-wider uppercase">
                                NIP {{ $guru->nip }}
                            </span>
                        @endif
                    </div>
                    <p class="font-body text-body text-white/90">
                        {{ $isSuperadmin ? 'Ubah data profil, foto profil, nomor telepon, email, dan kata sandi akun superadmin.' : 'Ubah data profil, informasi kontak, foto profil, dan kata sandi akun guru.' }}
                    </p>
                </div>
            </div>
        </section>

        {{-- Card Formulir Edit --}}
        <section class="bg-surface-container-lowest rounded-3xl p-6 sm:p-8 shadow-sm border-2 border-slate-200/80">
            {{-- Header Form dengan Icon dan Role Badge --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-5 border-b border-gray-100 gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl {{ $isSuperadmin ? 'bg-indigo-50 text-indigo-600' : 'bg-primary-50 text-primary-600' }} flex items-center justify-center shrink-0 shadow-sm border {{ $isSuperadmin ? 'border-indigo-100' : 'border-primary-100' }}">
                        <span class="material-symbols-outlined text-[26px]">manage_accounts</span>
                    </div>
                    <div>
                        <h3 class="font-heading text-lg sm:text-xl font-bold text-on-surface">Formulir Edit Akun {{ $isSuperadmin ? 'Superadmin' : 'Guru' }}</h3>
                        <p class="font-caption text-caption text-gray-500">Ubah data yang ingin diperbarui, lalu klik tombol simpan perubahan di bawah</p>
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

            <form method="POST" action="{{ route('superadmin.guru.update', ['guru' => $guru->id, 'role' => $isSuperadmin ? 'superadmin' : 'guru']) }}" enctype="multipart/form-data" class="flex flex-col gap-8">
                @csrf
                @method('PUT')
                <input type="hidden" name="role" value="{{ $isSuperadmin ? 'superadmin' : 'guru' }}">

                {{-- Bagian Foto Profil (Guru & Superadmin) --}}
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-2 text-primary-700 font-label-upper text-label-upper font-bold uppercase tracking-wider pb-2 border-b border-gray-100">
                        <span class="material-symbols-outlined text-[18px]">add_a_photo</span>
                        Foto Profil {{ $isSuperadmin ? 'Superadmin' : 'Guru' }}
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-5 p-5 rounded-2xl bg-surface-container-low border border-slate-200/70">
                        <div id="fotoPreviewBox" class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-2xl uppercase shrink-0 overflow-hidden border-2 border-primary-200 shadow-sm">
                            @if ($guru->foto_url)
                                <img id="fotoPreview" src="{{ $guru->foto_url }}" alt="{{ $guru->nama_lengkap }}" class="w-full h-full object-cover">
                                <span id="fotoPlaceholder" class="material-symbols-outlined text-[40px] text-primary-500 hidden">image</span>
                            @else
                                <span id="fotoPlaceholder" class="material-symbols-outlined text-[40px] text-primary-500">image</span>
                                <img id="fotoPreview" src="" alt="Preview" class="w-full h-full object-cover hidden">
                            @endif
                        </div>
                        <div class="flex-1 flex flex-col gap-2 w-full">
                            <div class="flex flex-col">
                                <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-700 font-bold">Ganti Berkas Foto Profil (Opsional)</span>
                                <span class="font-caption text-caption text-gray-500">Pilih foto jika ingin memperbarui. Format didukung: JPG, PNG, WEBP (Maks 2 MB).</span>
                            </div>
                            <input type="file" name="foto" id="fotoInput" accept="image/jpeg,image/png,image/jpg,image/webp"
                                   onchange="previewImage(this)"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-primary-600 hover:file:bg-primary-500 file:text-white cursor-pointer file:transition-colors">
                            @error('foto')
                                <span class="text-error text-xs font-caption font-semibold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Bagian 1: Data Pribadi & Identitas --}}
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-2 text-primary-700 font-label-upper text-label-upper font-bold uppercase tracking-wider pb-2 border-b border-gray-100">
                        <span class="material-symbols-outlined text-[18px]">person</span>
                        Data Profil & Identitas
                    </div>

                    @if ($isSuperadmin)
                        {{-- Superadmin Role Info Banner --}}
                        <div class="p-4 rounded-2xl bg-indigo-50/70 border border-indigo-100 flex items-start gap-3 text-indigo-950">
                            <span class="material-symbols-outlined text-indigo-600 text-[22px] shrink-0 mt-0.5">admin_panel_settings</span>
                            <div class="flex flex-col gap-0.5">
                                <span class="font-body text-sm font-bold">Akun Super Administrator Sistem</span>
                                <p class="font-caption text-xs text-indigo-800/80">
                                    Akun ini memiliki hak kendali penuh terhadap platform. Data NIP, jenis kelamin, dan kepegawaian tidak diperlukan untuk akun superadmin.
                                </p>
                            </div>
                        </div>

                        {{-- Nama Lengkap Superadmin --}}
                        <label class="flex flex-col gap-1.5">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-600 font-bold">
                                Nama Lengkap Superadmin <span class="text-error">*</span>
                            </span>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">id_card</span>
                                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $guru->nama_lengkap) }}" required
                                       autocomplete="name" maxlength="150"
                                       placeholder="Nama lengkap superadmin"
                                       class="w-full rounded-xl border border-gray-200 bg-gray-50/70 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white focus:ring-2 focus:ring-primary-500/10 transition-all">
                            </div>
                            @error('nama_lengkap')
                                <span class="text-error text-xs font-caption font-semibold">{{ $message }}</span>
                            @enderror
                        </label>
                    @else
                        {{-- Fields Khusus Guru --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Nama Lengkap Guru --}}
                            <label class="flex flex-col gap-1.5 sm:col-span-2">
                                <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-600 font-bold">
                                    Nama Lengkap Guru <span class="text-error">*</span>
                                </span>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">id_card</span>
                                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $guru->nama_lengkap) }}" required
                                           autocomplete="name" maxlength="150"
                                           placeholder="Nama lengkap guru beserta gelar jika ada"
                                           class="w-full rounded-xl border border-gray-200 bg-gray-50/70 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white focus:ring-2 focus:ring-primary-500/10 transition-all">
                                </div>
                                @error('nama_lengkap')
                                    <span class="text-error text-xs font-caption font-semibold">{{ $message }}</span>
                                @enderror
                            </label>

                            {{-- NIP Guru --}}
                            <label class="flex flex-col gap-1.5">
                                <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-600 font-bold">
                                    NIP <span class="text-error">*</span>
                                </span>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">badge</span>
                                    <input type="text" name="nip" value="{{ old('nip', $guru->nip) }}" required
                                           inputmode="numeric" pattern="[0-9]*" maxlength="25"
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                           placeholder="Contoh: 198501012010011001"
                                           class="w-full rounded-xl border border-gray-200 bg-gray-50/70 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white focus:ring-2 focus:ring-primary-500/10 transition-all">
                                </div>
                                @error('nip')
                                    <span class="text-error text-xs font-caption font-semibold">{{ $message }}</span>
                                @enderror
                            </label>

                            {{-- Jenis Kelamin --}}
                            <label class="flex flex-col gap-1.5">
                                <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-600 font-bold">
                                    Jenis Kelamin <span class="text-error">*</span>
                                </span>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">wc</span>
                                    <select name="jenis_kelamin" required
                                            class="w-full rounded-xl border border-gray-200 bg-gray-50/70 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white focus:ring-2 focus:ring-primary-500/10 transition-all cursor-pointer">
                                        <option value="L" {{ old('jenis_kelamin', $guru->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                                        <option value="P" {{ old('jenis_kelamin', $guru->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                                    </select>
                                </div>
                                @error('jenis_kelamin')
                                    <span class="text-error text-xs font-caption font-semibold">{{ $message }}</span>
                                @enderror
                            </label>

                            {{-- Status Pegawaian --}}
                            <label class="flex flex-col gap-1.5 sm:col-span-2">
                                <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-600 font-bold">
                                    Status Kepegawaian
                                </span>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">work</span>
                                    <input type="text" name="status_pegawaian" value="{{ old('status_pegawaian', $guru->status_pegawaian) }}"
                                           placeholder="Contoh: PNS / PPPK / GTT / Tetap Yayasan"
                                           class="w-full rounded-xl border border-gray-200 bg-gray-50/70 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white focus:ring-2 focus:ring-primary-500/10 transition-all">
                                </div>
                                @error('status_pegawaian')
                                    <span class="text-error text-xs font-caption font-semibold">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                    @endif
                </div>

                {{-- Bagian 2: Informasi Kontak --}}
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-2 text-primary-700 font-label-upper text-label-upper font-bold uppercase tracking-wider pb-2 border-b border-gray-100">
                        <span class="material-symbols-outlined text-[18px]">contact_mail</span>
                        Informasi Kontak
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Email --}}
                        <label class="flex flex-col gap-1.5">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-600 font-bold">
                                Alamat Email <span class="text-error">*</span>
                            </span>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">mail</span>
                                <input type="email" name="email" value="{{ old('email', $guru->email) }}" required
                                       autocomplete="email" maxlength="255"
                                       placeholder="pengelola@sekolah.sch.id"
                                       class="w-full rounded-xl border border-gray-200 bg-gray-50/70 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white focus:ring-2 focus:ring-primary-500/10 transition-all">
                            </div>
                            @error('email')
                                <span class="text-error text-xs font-caption font-semibold">{{ $message }}</span>
                            @enderror
                        </label>

                        {{-- No Telpon --}}
                        <label class="flex flex-col gap-1.5">
                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-600 font-bold">
                                No. Telpon / WhatsApp
                            </span>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">call</span>
                                <input type="tel" name="no_telpon" value="{{ old('no_telpon', $guru->no_telpon) }}"
                                       inputmode="numeric" pattern="[0-9]*" maxlength="16"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                       placeholder="Contoh: 081234567890"
                                       class="w-full rounded-xl border border-gray-200 bg-gray-50/70 pl-11 pr-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:bg-white focus:ring-2 focus:ring-primary-500/10 transition-all">
                            </div>
                            <span class="font-caption text-caption text-gray-400">Hanya angka tanpa spasi atau tanda hubung</span>
                            @error('no_telpon')
                                <span class="text-error text-xs font-caption font-semibold">{{ $message }}</span>
                            @enderror
                        </label>
                    </div>
                </div>

                {{-- Bagian 3: Kredensial & Keamanan --}}
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-2 text-primary-700 font-label-upper text-label-upper font-bold uppercase tracking-wider pb-2 border-b border-gray-100">
                        <span class="material-symbols-outlined text-[18px]">lock</span>
                        Kredensial & Akses Keamanan
                    </div>

                    <div class="p-4 sm:p-5 rounded-2xl bg-surface-container-low border border-slate-200/70 flex flex-col gap-3">
                        <label class="flex flex-col gap-1.5">
                            <div class="flex items-center justify-between flex-wrap gap-1">
                                <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-700 font-bold">
                                    Kata Sandi Baru (Opsional)
                                </span>
                                <span class="font-caption text-caption text-gray-500">Biarkan kosong jika tidak ingin mengganti</span>
                            </div>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">key</span>
                                <input type="password" name="password" id="passwordInput" minlength="6"
                                       placeholder="Ketik kata sandi baru (minimal 6 karakter)..."
                                       class="w-full rounded-xl border border-gray-200 bg-white pl-11 pr-11 py-3 font-body text-body outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/10 transition-all">
                                <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                    <span id="passwordToggleIcon" class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                            @error('password')
                                <span class="text-error text-xs font-caption font-semibold">{{ $message }}</span>
                            @enderror
                        </label>
                    </div>
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

        function togglePasswordVisibility() {
            const pass = document.getElementById('passwordInput');
            const icon = document.getElementById('passwordToggleIcon');
            if (!pass || !icon) return;

            if (pass.type === 'password') {
                pass.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                pass.type = 'password';
                icon.textContent = 'visibility';
            }
        }
    </script>
@endsection

