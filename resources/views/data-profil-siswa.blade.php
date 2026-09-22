<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lengkapi & Edit Data Diri Siswa - Sinau Jowo Web</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:ital,wght@0,400..800;1,400..800&family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
              "surface-container-low": "#f5f2ff",
              "yellow-300": "#F6D98B",
              "secondary-fixed-dim": "#ffb2bf",
              "primary-fixed-dim": "#c6bfff",
              "tertiary-fixed": "#ffdcc4",
              "primary-700": "#5443C9",
              "surface-bright": "#fcf8ff",
              "surface-container": "#efecfd",
              "on-error-container": "#93000a",
              "surface-variant": "#e3e0f1",
              "primary-fixed": "#e4dfff",
              "green-500": "#4CAF6D",
              "orange-300": "#F7B98A",
              "on-secondary-container": "#792d40",
              "black-900": "#1E1E2A",
              "on-surface-variant": "#474554",
              "on-primary": "#ffffff",
              "on-tertiary": "#ffffff",
              "secondary": "#964356",
              "surface-container-lowest": "#ffffff",
              "on-primary-fixed": "#160066",
              "gray-50": "#F7F7FA",
              "inverse-on-surface": "#f2efff",
              "surface-container-high": "#e9e6f7",
              "primary-600": "#6C5CE8",
              "primary-500": "#7B6CF0",
              "surface-dim": "#dbd8e9",
              "on-tertiary-container": "#ffc59b",
              "tertiary-fixed-dim": "#f8ba8b",
              "primary-container": "#5443c9",
              "on-secondary-fixed": "#3f0016",
              "gray-500": "#8A8A9A",
              "primary": "#3c25b1",
              "on-primary-fixed-variant": "#402cb5",
              "outline": "#787585",
              "pink-100": "#FBD9DE",
              "background": "#fcf8ff",
              "orange-500": "#F0955A",
              "gray-200": "#E4E4EC",
              "inverse-surface": "#2f2f3c",
              "inverse-primary": "#c6bfff",
              "on-background": "#1a1a26",
              "on-secondary": "#ffffff",
              "error": "#ba1a1a",
              "error-container": "#ffdad6"
            },
            "spacing": {
              "space-md": "1rem",
              "space-sm": "0.75rem",
              "space-xs": "0.5rem",
              "margin-desktop": "2rem",
              "space-xl": "1.5rem",
              "space-lg": "1.25rem"
            },
            "fontFamily": {
              "heading": ["Epilogue"],
              "stat-number-sm": ["Epilogue"],
              "body": ["Manrope"],
              "display": ["Epilogue"],
              "display-mobile": ["Epilogue"],
              "label-upper": ["Manrope"],
              "stat-number": ["Epilogue"]
            }
          }
        }
    };
    </script>
    <style>
        @layer base {
            html, body { margin: 0; padding: 0; }
            body { overscroll-behavior: none; }
            main > :first-child { margin-top: 0 !important; }
            main > :last-child { margin-bottom: 0 !important; }
        }
        ::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-background font-body text-on-surface antialiased">
    <!-- SIDEBAR -->
    <x-sidebar active="profil" />

    <div class="pl-0 lg:pl-72 flex flex-col min-h-screen pb-24 lg:pb-8">
        <!-- HEADER -->
        <header class="fixed top-0 left-0 lg:left-72 right-0 h-16 lg:h-20 bg-surface-container-lowest/90 backdrop-blur-xl z-40 shadow-[0_1px_8px_rgba(0,0,0,0.04)] px-4 lg:px-space-xl flex items-center justify-between">
            <div class="flex items-center gap-3 sm:gap-4">
                <a href="{{ route('siswa.profil') }}" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center bg-gray-50 text-on-surface-variant hover:bg-surface-container hover:text-primary-600 transition-colors shrink-0" title="Kembali ke Halaman Profil">
                    <span class="material-symbols-outlined text-[20px] sm:text-[22px]">arrow_back</span>
                </a>
                <div class="flex flex-col min-w-0">
                    <div class="flex items-center gap-1.5 text-xs font-semibold text-gray-500 truncate">
                        <a href="{{ route('siswa.profil') }}" class="hover:text-primary-600 transition-colors">Profil</a>
                        <span>/</span>
                        <span class="text-primary-600 truncate">Edit Data</span>
                    </div>
                    <h1 class="font-heading text-base sm:text-lg font-extrabold text-on-surface truncate">Lengkapi Data Diri</h1>
                </div>
            </div>
            <div class="flex items-center gap-space-lg">
                <a href="{{ route('siswa.profil') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-on-surface-variant hover:bg-surface-container-high transition-colors">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    <span>Kembali ke Profil</span>
                </a>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="flex-1 pt-20 lg:pt-24 w-full px-4 sm:px-6 lg:px-margin-desktop py-space-md lg:py-space-xl bg-background max-w-5xl mx-auto">
            <div class="flex flex-col w-full gap-space-lg">

                <!-- Completion Status Banner -->
                <section class="w-full bg-surface-container-lowest rounded-2xl shadow-sm p-6 border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-5 relative overflow-hidden">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-2xl {{ $persenLengkap === 100 ? 'bg-green-50 text-green-600' : 'bg-primary-50 text-primary-600' }} flex items-center justify-center shrink-0 shadow-sm">
                            <span class="material-symbols-outlined text-3xl">{{ $persenLengkap === 100 ? 'task_alt' : 'assignment_ind' }}</span>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <div class="flex items-center gap-2">
                                <h2 class="font-heading text-lg font-bold text-on-surface">Status Kelengkapan Data Diri</h2>
                                @if($persenLengkap === 100)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                        <span class="material-symbols-outlined text-[14px]">verified</span> Lengkap 100%
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">
                                        {{ $persenLengkap }}% Selesai
                                    </span>
                                @endif
                            </div>
                            <p class="font-body text-xs sm:text-sm text-on-surface-variant mt-1">
                                @if($persenLengkap === 100)
                                    Semua data profil Anda sudah terisi lengkap. Anda tetap dapat memperbarui informasi kapan saja.
                                @else
                                    Lengkapi foto profil dan data diri Anda untuk memaksimalkan pengalaman belajar di Sinau Jowo.
                                @endif
                            </p>
                            @if(count($belumLengkap) > 0)
                                <div class="flex flex-wrap items-center gap-1.5 mt-2.5">
                                    <span class="text-xs text-gray-500 font-semibold">Belum dilengkapi:</span>
                                    @foreach($belumLengkap as $item)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-red-50 text-red-700 border border-red-200/60">
                                            {{ $item }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Progress Bar Column -->
                    <div class="flex flex-col w-full md:w-64 bg-surface-container-low p-3.5 rounded-xl border border-gray-100 shrink-0">
                        <div class="flex justify-between items-center text-xs mb-1.5 font-bold">
                            <span class="text-gray-500">Kemajuan Profil</span>
                            <span class="{{ $persenLengkap === 100 ? 'text-green-600' : 'text-primary-600' }}">{{ $completedFields }} / {{ $totalFields }} Data</span>
                        </div>
                        <div class="w-full h-2.5 bg-gray-200/80 rounded-full overflow-hidden">
                            <div class="h-full {{ $persenLengkap === 100 ? 'bg-green-500' : 'bg-gradient-to-r from-primary-600 to-primary-500' }} rounded-full transition-all duration-500" style="width: {{ $persenLengkap }}%"></div>
                        </div>
                    </div>
                </section>

                <!-- FORM UTAMA -->
                <form action="{{ route('siswa.profil.update') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-6">
                    @csrf
                    @method('PUT')

                    <!-- SECTION 1: FOTO PROFIL SISWA -->
                    <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col gap-5">
                        <div class="flex items-center gap-2.5 border-b border-gray-100 pb-3.5">
                            <span class="material-symbols-outlined text-primary-600 text-2xl">account_box</span>
                            <div>
                                <h3 class="font-heading text-base font-bold text-on-surface">Foto Profil Siswa</h3>
                                <p class="font-body text-xs text-on-surface-variant">Gunakan foto wajah yang jelas, sopan, atau berseragam sekolah.</p>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 pt-2">
                            <!-- Avatar Preview Container -->
                            <div class="relative w-32 h-32 rounded-2xl bg-surface-container-low p-1 shadow-md shrink-0 border border-gray-200/70 overflow-hidden group">
                                <div id="avatarContainer" class="w-full h-full rounded-xl overflow-hidden flex items-center justify-center bg-primary-700 text-white font-bold text-3xl">
                                    @if($siswa->foto_url)
                                        <img id="avatarPreview" src="{{ $siswa->foto_url }}" alt="{{ $siswa->nama_lengkap }}" class="w-full h-full object-cover" />
                                    @elseif($siswa->foto && file_exists(storage_path('image/siswa/'.$siswa->foto)))
                                        <img id="avatarPreview" src="{{ route('siswa.image', $siswa->foto) }}" alt="{{ $siswa->nama_lengkap }}" class="w-full h-full object-cover" />
                                    @else
                                        <div id="avatarInitials" class="w-full h-full flex items-center justify-center bg-primary-700 text-white font-bold text-3xl">
                                            {{ mb_strtoupper(mb_substr($siswa->nama_lengkap, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-2xl cursor-pointer" onclick="document.getElementById('fotoInput').click()">
                                    <span class="material-symbols-outlined text-white text-3xl">photo_camera</span>
                                </div>
                            </div>

                            <!-- Upload Controls -->
                            <div class="flex flex-col justify-between gap-3 min-w-0 flex-1 text-center sm:text-left">
                                <div>
                                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2.5">
                                        <input type="file" id="fotoInput" name="foto" accept="image/jpeg,image/png,image/webp" class="hidden" onchange="previewSelectedImage(event)">
                                        <button type="button" onclick="document.getElementById('fotoInput').click()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-body text-sm font-semibold shadow-sm transition-all duration-200">
                                            <span class="material-symbols-outlined text-[18px]">cloud_upload</span>
                                            <span>Pilih Foto Baru</span>
                                        </button>
                                        <button type="button" id="btnResetFoto" onclick="resetSelectedImage()" class="hidden items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-on-surface-variant font-body text-sm font-medium transition-colors">
                                            <span class="material-symbols-outlined text-[18px]">undo</span>
                                            <span>Batal Pilih</span>
                                        </button>
                                    </div>
                                    <p class="font-body text-xs text-gray-500 mt-2.5 leading-relaxed">
                                        Mendukung format JPG, PNG, atau WEBP. Ukuran file maksimal 2 MB.
                                    </p>
                                </div>
                                <div id="selectedFileInfo" class="hidden items-center gap-2 text-xs font-semibold text-primary-700 bg-primary-50 px-3 py-1.5 rounded-lg border border-primary-200/60 max-w-fit">
                                    <span class="material-symbols-outlined text-sm">attachment</span>
                                    <span id="selectedFileName">file_name.jpg</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: IDENTITAS & DATA AKADEMIK -->
                    <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col gap-5">
                        <div class="flex items-center gap-2.5 border-b border-gray-100 pb-3.5">
                            <span class="material-symbols-outlined text-primary-600 text-2xl">badge</span>
                            <div>
                                <h3 class="font-heading text-base font-bold text-on-surface">Data Pribadi & Akademik</h3>
                                <p class="font-body text-xs text-on-surface-variant">Informasi identitas resmi siswa di lingkungan sekolah.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Nama Lengkap -->
                            <div class="flex flex-col gap-1.5 md:col-span-2">
                                <label for="nama_lengkap" class="font-body text-xs font-bold text-on-surface uppercase tracking-wider">
                                    Nama Lengkap <span class="text-error">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-3 text-gray-400 material-symbols-outlined text-[20px]">person</span>
                                    <input type="text" id="nama_lengkap" name="nama_lengkap" required value="{{ old('nama_lengkap', $siswa->nama_lengkap) }}" placeholder="Masukkan nama lengkap siswa" class="w-full pl-11 pr-4 py-2.5 rounded-xl bg-surface-container-low border border-gray-200/80 focus:border-primary-500 focus:bg-white text-on-surface font-body text-sm outline-none transition-all">
                                </div>
                                @error('nama_lengkap')
                                    <span class="text-xs text-error font-medium">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- NIS -->
                            <div class="flex flex-col gap-1.5">
                                <label for="nis" class="font-body text-xs font-bold text-on-surface uppercase tracking-wider">
                                    Nomor Induk Siswa (NIS)
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-3 text-gray-400 material-symbols-outlined text-[20px]">pin</span>
                                    <input type="text" id="nis" name="nis" value="{{ old('nis', $siswa->nis) }}"
                                           inputmode="numeric" pattern="[0-9]*" maxlength="20"
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                           placeholder="Contoh: 20241001" class="w-full pl-11 pr-4 py-2.5 rounded-xl bg-surface-container-low border border-gray-200/80 focus:border-primary-500 focus:bg-white text-on-surface font-body text-sm outline-none transition-all">
                                </div>
                                @error('nis')
                                    <span class="text-xs text-error font-medium">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Kelas -->
                            <div class="flex flex-col gap-1.5">
                                <label for="kelas" class="font-body text-xs font-bold text-on-surface uppercase tracking-wider">
                                    Kelas Siswa
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-3 text-gray-400 material-symbols-outlined text-[20px]">class</span>
                                    <input type="text" id="kelas" name="kelas" list="kelasList" value="{{ old('kelas', $siswa->kelas) }}" placeholder="Pilih atau ketik kelas (contoh: X-A, XI-IPA)" class="w-full pl-11 pr-4 py-2.5 rounded-xl bg-surface-container-low border border-gray-200/80 focus:border-primary-500 focus:bg-white text-on-surface font-body text-sm outline-none transition-all">
                                    <datalist id="kelasList">
                                        <option value="X">
                                        <option value="X-A">
                                        <option value="X-B">
                                        <option value="X-C">
                                        <option value="XI">
                                        <option value="XI-IPA 1">
                                        <option value="XI-IPA 2">
                                        <option value="XI-IPS 1">
                                        <option value="XI-IPS 2">
                                        <option value="XII">
                                        <option value="XII-IPA 1">
                                        <option value="XII-IPA 2">
                                        <option value="XII-IPS 1">
                                    </datalist>
                                </div>
                                @error('kelas')
                                    <span class="text-xs text-error font-medium">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Jenis Kelamin -->
                            <div class="flex flex-col gap-1.5 md:col-span-2">
                                <label class="font-body text-xs font-bold text-on-surface uppercase tracking-wider">
                                    Jenis Kelamin
                                </label>
                                @php
                                    $currentJk = old('jenis_kelamin', $siswa->jenis_kelamin);
                                @endphp
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                                    <label class="flex items-center gap-3.5 p-3.5 rounded-xl border border-gray-200/80 bg-surface-container-low hover:border-primary-400 cursor-pointer transition-all has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50/50 has-[:checked]:ring-1 has-[:checked]:ring-primary-500">
                                        <input type="radio" name="jenis_kelamin" value="L" {{ $currentJk === 'L' ? 'checked' : '' }} class="w-4 h-4 text-primary-600 accent-primary-600">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                                                <span class="material-symbols-outlined text-[18px]">male</span>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="font-body text-sm font-bold text-on-surface">Laki-laki</span>
                                                <span class="font-caption text-[11px] text-gray-500">Siswa (L)</span>
                                            </div>
                                        </div>
                                    </label>
                                    <label class="flex items-center gap-3.5 p-3.5 rounded-xl border border-gray-200/80 bg-surface-container-low hover:border-primary-400 cursor-pointer transition-all has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50/50 has-[:checked]:ring-1 has-[:checked]:ring-primary-500">
                                        <input type="radio" name="jenis_kelamin" value="P" {{ $currentJk === 'P' ? 'checked' : '' }} class="w-4 h-4 text-primary-600 accent-primary-600">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-pink-100 text-pink-700 flex items-center justify-center">
                                                <span class="material-symbols-outlined text-[18px]">female</span>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="font-body text-sm font-bold text-on-surface">Perempuan</span>
                                                <span class="font-caption text-[11px] text-gray-500">Siswi (P)</span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                @error('jenis_kelamin')
                                    <span class="text-xs text-error font-medium">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3: KONTAK & KOMUNIKASI -->
                    <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col gap-5">
                        <div class="flex items-center gap-2.5 border-b border-gray-100 pb-3.5">
                            <span class="material-symbols-outlined text-primary-600 text-2xl">call</span>
                            <div>
                                <h3 class="font-heading text-base font-bold text-on-surface">Informasi Kontak</h3>
                                <p class="font-body text-xs text-on-surface-variant">Digunakan untuk notifikasi pembelajaran dan pemulihan akun.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Email -->
                            <div class="flex flex-col gap-1.5">
                                <label for="email" class="font-body text-xs font-bold text-on-surface uppercase tracking-wider">
                                    Alamat Email <span class="text-error">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-3 text-gray-400 material-symbols-outlined text-[20px]">mail</span>
                                    <input type="email" id="email" name="email" required value="{{ old('email', $siswa->email) }}" placeholder="nama@siswa.sekolah.id" class="w-full pl-11 pr-4 py-2.5 rounded-xl bg-surface-container-low border border-gray-200/80 focus:border-primary-500 focus:bg-white text-on-surface font-body text-sm outline-none transition-all">
                                </div>
                                @error('email')
                                    <span class="text-xs text-error font-medium">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- No Telepon / WhatsApp -->
                            <div class="flex flex-col gap-1.5">
                                <label for="no_telpon" class="font-body text-xs font-bold text-on-surface uppercase tracking-wider">
                                    Nomor WhatsApp / Telepon
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-3 text-gray-400 material-symbols-outlined text-[20px]">phone_iphone</span>
                                    <input type="tel" id="no_telpon" name="no_telpon" value="{{ old('no_telpon', $siswa->no_telpon) }}"
                                           inputmode="numeric" pattern="[0-9]*" maxlength="16"
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                           placeholder="Contoh: 081234567890" class="w-full pl-11 pr-4 py-2.5 rounded-xl bg-surface-container-low border border-gray-200/80 focus:border-primary-500 focus:bg-white text-on-surface font-body text-sm outline-none transition-all">
                                </div>
                                @error('no_telpon')
                                    <span class="text-xs text-error font-medium">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 4: GANTI KATA SANDI (OPSIONAL) -->
                    <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col gap-5">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3.5">
                            <div class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-primary-600 text-2xl">lock</span>
                                <div>
                                    <h3 class="font-heading text-base font-bold text-on-surface">Ubah Kata Sandi (Opsional)</h3>
                                    <p class="font-body text-xs text-on-surface-variant">Kosongkan jika Anda tidak bermaksud mengganti kata sandi akun.</p>
                                </div>
                            </div>
                            <span class="text-xs px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 font-semibold">Opsional</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Kata Sandi Baru -->
                            <div class="flex flex-col gap-1.5">
                                <label for="password" class="font-body text-xs font-bold text-on-surface uppercase tracking-wider">
                                    Kata Sandi Baru
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-3 text-gray-400 material-symbols-outlined text-[20px]">lock_reset</span>
                                    <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" class="w-full pl-11 pr-11 py-2.5 rounded-xl bg-surface-container-low border border-gray-200/80 focus:border-primary-500 focus:bg-white text-on-surface font-body text-sm outline-none transition-all">
                                    <button type="button" onclick="togglePasswordVisibility('password', 'eyeIcon1')" class="absolute right-3.5 top-3 text-gray-400 hover:text-gray-600 transition-colors">
                                        <span id="eyeIcon1" class="material-symbols-outlined text-[18px]">visibility</span>
                                    </button>
                                </div>
                                @error('password')
                                    <span class="text-xs text-error font-medium">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Konfirmasi Kata Sandi -->
                            <div class="flex flex-col gap-1.5">
                                <label for="password_confirmation" class="font-body text-xs font-bold text-on-surface uppercase tracking-wider">
                                    Ulangi Kata Sandi Baru
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-3 text-gray-400 material-symbols-outlined text-[20px]">lock_clock</span>
                                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi kata sandi di samping" class="w-full pl-11 pr-11 py-2.5 rounded-xl bg-surface-container-low border border-gray-200/80 focus:border-primary-500 focus:bg-white text-on-surface font-body text-sm outline-none transition-all">
                                    <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'eyeIcon2')" class="absolute right-3.5 top-3 text-gray-400 hover:text-gray-600 transition-colors">
                                        <span id="eyeIcon2" class="material-symbols-outlined text-[18px]">visibility</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-2 pb-8">
                        <a href="{{ route('siswa.profil') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-on-surface-variant font-body text-sm font-semibold transition-colors">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                            <span>Batal & Kembali</span>
                        </a>
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-body text-sm font-bold shadow-md hover:shadow-lg transition-all duration-200">
                            <span class="material-symbols-outlined text-[20px]">save</span>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>

            </div>
        </main>
    </div>

    <!-- Floating Toast Notifications (Pojok Kanan Bawah) -->
    <div id="toastContainer" class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none max-w-sm w-full">
        @if (session('sukses'))
            <div id="toastNotification"
                 class="pointer-events-auto bg-surface-container-lowest border border-green-500/40 rounded-2xl p-4 shadow-[0_10px_35px_rgba(0,0,0,0.15)] flex items-start gap-3.5 overflow-hidden relative transition-all duration-300">
                <div class="w-10 h-10 rounded-xl bg-green-500/15 text-green-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">check_circle</span>
                </div>
                <div class="flex-1 min-w-0 pt-0.5">
                    <h5 class="font-heading text-sm font-bold text-green-700 leading-tight">Berhasil</h5>
                    <p class="font-body text-xs text-on-surface-variant mt-0.5 leading-relaxed">{{ session('sukses') }}</p>
                </div>
                <button type="button" onclick="dismissToast('toastNotification')" title="Tutup Notifikasi" class="w-7 h-7 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-700 flex items-center justify-center transition-colors shrink-0">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
        @endif
        @if (session('error') || $errors->any())
            <div id="toastErrorNotification"
                 class="pointer-events-auto bg-surface-container-lowest border border-red-500/40 rounded-2xl p-4 shadow-[0_10px_35px_rgba(0,0,0,0.15)] flex items-start gap-3.5 overflow-hidden relative transition-all duration-300">
                <div class="w-10 h-10 rounded-xl bg-red-500/15 text-red-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">error</span>
                </div>
                <div class="flex-1 min-w-0 pt-0.5">
                    <h5 class="font-heading text-sm font-bold text-red-700 leading-tight">Terjadi Kesalahan</h5>
                    <p class="font-body text-xs text-on-surface-variant mt-0.5 leading-relaxed">{{ session('error') ?? $errors->first() }}</p>
                </div>
                <button type="button" onclick="dismissToast('toastErrorNotification')" title="Tutup Notifikasi" class="w-7 h-7 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-700 flex items-center justify-center transition-colors shrink-0">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Scripts -->
    <script>
        // Original avatar HTML fallback
        const originalAvatarHtml = document.getElementById('avatarContainer').innerHTML;

        function previewSelectedImage(event) {
            const input = event.target;
            if (input.files && input.files[0]) {
                const file = input.files[0];

                // Validate file size (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran foto melebihi 2 MB. Silakan pilih foto dengan ukuran lebih kecil.');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const avatarContainer = document.getElementById('avatarContainer');
                    avatarContainer.innerHTML = `<img src="${e.target.result}" alt="Preview Foto" class="w-full h-full object-cover" />`;
                    document.getElementById('btnResetFoto').classList.remove('hidden');
                    document.getElementById('btnResetFoto').classList.add('inline-flex');

                    const fileInfo = document.getElementById('selectedFileInfo');
                    const fileName = document.getElementById('selectedFileName');
                    if (fileInfo && fileName) {
                        fileName.textContent = file.name;
                        fileInfo.classList.remove('hidden');
                        fileInfo.classList.add('inline-flex');
                    }
                };
                reader.readAsDataURL(file);
            }
        }

        function resetSelectedImage() {
            const input = document.getElementById('fotoInput');
            input.value = '';
            document.getElementById('avatarContainer').innerHTML = originalAvatarHtml;
            document.getElementById('btnResetFoto').classList.add('hidden');
            document.getElementById('btnResetFoto').classList.remove('inline-flex');

            const fileInfo = document.getElementById('selectedFileInfo');
            if (fileInfo) {
                fileInfo.classList.add('hidden');
                fileInfo.classList.remove('inline-flex');
            }
        }

        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        }

        function dismissToast(id) {
            const el = document.getElementById(id);
            if (el) {
                el.style.opacity = '0';
                el.style.transform = 'translateX(100%)';
                setTimeout(() => el.remove(), 300);
            }
        }

        @if(session('sukses') || session('error') || $errors->any())
        setTimeout(() => {
            dismissToast('toastNotification');
            dismissToast('toastErrorNotification');
        }, 5000);
        @endif
    </script>
    @include('partials.siswa-sound')
</body>
</html>
