<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asisten Tanya Basa AI - Sinau Jowo Web</title>
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
              "primary-400": "#A79BFF",
              "pink-500": "#F08CA0",
              "surface": "#fcf8ff",
              "outline-variant": "#c8c4d6",
              "error-container": "#ffdad6",
              "on-surface": "#1a1a26",
              "color-white": "#FFFFFF",
              "on-primary-container": "#d2cbff",
              "tertiary-container": "#7d4f29",
              "on-tertiary-fixed": "#2f1500",
              "on-secondary-fixed-variant": "#792c3f",
              "tertiary": "#623814",
              "on-error": "#ffffff",
              "on-tertiary-fixed-variant": "#673d18",
              "surface-container-highest": "#e3e0f1",
              "secondary-fixed": "#ffd9de",
              "surface-tint": "#5948ce",
              "secondary-container": "#ff98ac"
            },
            "borderRadius": {
              "DEFAULT": "0.25rem",
              "lg": "0.5rem",
              "xl": "0.75rem",
              "full": "9999px"
            },
            "spacing": {
              "margin": "1.25rem",
              "space-xl": "1.75rem",
              "space-md": "1rem",
              "space-sm": "0.5rem",
              "gutter": "0.75rem",
              "gutter-desktop": "1.5rem",
              "margin-desktop": "2.5rem",
              "space-xs": "0.25rem"
            },
            "fontFamily": {
              "caption": ["Manrope"],
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
            html {
                font-size: 90%;
                zoom: 90%;
            }
            html, body { margin: 0; padding: 0; }
            body { overscroll-behavior: none; }
            main > :first-child { margin-top: 0 !important; }
            main > :last-child { margin-bottom: 0 !important; }
        }
        ::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-background font-body text-on-surface antialiased">
    <!-- ASIDE SIDEBAR COMPONENT -->
    <x-sidebar active="asisten" />

    <div class="pl-72 flex flex-col min-h-screen">
        <!-- HEADER -->
        <header class="fixed top-0 left-72 right-0 h-20 bg-surface-container-lowest/90 backdrop-blur-xl z-40 shadow-[0_1px_8px_rgba(0,0,0,0.04)] px-space-xl flex items-center justify-between">
            <div class="flex items-center flex-1 max-w-md">
                <div class="flex items-center w-full bg-gray-50 rounded-full px-space-md py-space-xs gap-space-sm border border-gray-200/60 focus-within:border-primary-500 transition-colors">
                    <span class="material-symbols-outlined text-gray-500 text-[20px]">search</span>
                    <input type="text" placeholder="Cari materi aksara, peribahasa, tata bahasa..." class="w-full bg-transparent border-none outline-none font-body text-body text-on-surface placeholder:text-gray-500">
                </div>
            </div>
            <div class="flex items-center gap-space-lg">
                <button type="button" class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-50 text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors relative">
                    <span class="material-symbols-outlined text-[20px]">notifications</span>
                    <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-secondary"></span>
                </button>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="flex-1 pt-20 w-full px-6 py-space-xl bg-background">
            <div class="flex flex-col w-full gap-space-lg">

                <!-- CHAT CONTAINER CARD -->
                <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm flex flex-col gap-6 w-full border border-gray-100">

                    <!-- Chat Header Bar -->
                    <div class="flex items-center justify-between pb-3 bg-surface-container-low/60 -mx-6 -mt-6 px-6 pt-4 rounded-t-2xl border-b border-primary-100/50">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary-600 text-xl" style="font-variation-settings: 'FILL' 1;">forum</span>
                            <span class="font-heading text-heading font-bold text-on-surface">Rembugan Pasinaon Aktif</span>
                        </div>
                        <div class="flex items-center gap-2 text-green-500">
                            <span class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse"></span>
                            <span class="font-caption text-caption font-bold text-green-500 uppercase">ONLINE AI RAG</span>
                        </div>
                    </div>

                    <!-- Bot Welcome Message -->
                    <div class="flex items-start gap-4 w-full">
                        <div class="w-10 h-10 rounded-xl bg-primary-700 flex items-center justify-center text-white shrink-0 shadow-sm">
                            <span class="material-symbols-outlined text-2xl">neurology</span>
                        </div>
                        <div class="flex flex-col gap-1 flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-body text-body font-bold text-on-surface">Kanca Sinau Jawa AI</span>
                                <span class="font-caption text-caption text-gray-500">10:14 WIB</span>
                            </div>
                            <div class="bg-surface-container-low p-4 rounded-2xl text-on-surface space-y-2 border border-primary-100/40">
                                <p class="font-body text-body leading-relaxed">
                                    Sugeng rawuh, Andi Prasetyo! Kula minangka <strong class="text-primary-700">Kanca Sinau Jawa</strong>. Kula siyaga mbiyantu panjenengan nyinau tatakrama, unggah-ungguh basa (Ngoko Lugu, Ngoko Alus, Krama Lugu, Krama Alus), aksara Jawa, tembung saroja, paribasan, ngantos tegese crita pawayangan adhedhasar buku standar paramasastra Jawa.
                                </p>
                                <p class="font-body text-body text-on-surface-variant leading-relaxed">
                                    Wonten babagan pasinaon basa ingkang taksih ndadosaken bingung ing pamulangan dinten punika? Sumangga nyuwun pirsa!
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- User Message 1 -->
                    <div class="flex items-start justify-end gap-4 w-full">
                        <div class="flex flex-col items-end gap-1 max-w-3xl lg:max-w-4xl">
                            <div class="flex items-center gap-2">
                                <span class="font-caption text-caption text-gray-500">10:15 WIB</span>
                                <span class="font-body text-body font-bold text-on-surface">Andi Prasetyo (Siswa)</span>
                                <div class="w-8 h-8 rounded-full bg-primary-700 text-white font-bold text-xs flex items-center justify-center">
                                    AP
                                </div>
                            </div>
                            <div class="bg-primary-600 text-white p-4 rounded-2xl shadow-sm leading-relaxed">
                                <p class="font-body text-body">
                                    Kula badhe nyuwun pirsa, punapa bentenipun tembung <em>"Dhahar"</em>, <em>"Nedha"</em>, kaliyan <em>"Mangan"</em> wonten ing unggah-ungguh basa? Kados pundi tuladha panggunaanipun ing ukara?
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Bot Response 1 -->
                    <div class="flex items-start gap-4 w-full">
                        <div class="w-10 h-10 rounded-xl bg-primary-700 flex items-center justify-center text-white shrink-0 shadow-sm">
                            <span class="material-symbols-outlined text-2xl">neurology</span>
                        </div>
                        <div class="flex flex-col gap-3 flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-body text-body font-bold text-on-surface">Kanca Sinau Jawa AI</span>
                                <span class="font-caption text-caption text-gray-500">10:15 WIB</span>
                            </div>

                            <div class="bg-surface-container-low p-5 rounded-2xl text-on-surface flex flex-col gap-4 border border-primary-100/40">
                                <div>
                                    <p class="font-body text-body leading-relaxed">
                                        Pitakon ingkang sae sanget, Mas Andi! Wonten ing unggah-ungguh basa Jawa, tembung kangge ngandharaken pakaryan nglebetaken tetedhan wonten ing cangkem dipunperang manut trap-trapaning tata krama kurmat:
                                    </p>
                                </div>

                                <!-- 3 Grid Cards -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 w-full">
                                    <!-- Undha-Usuk 1 -->
                                    <div class="bg-surface-container-lowest p-4 rounded-xl flex flex-col justify-between gap-2 shadow-sm border border-gray-100">
                                        <div class="flex flex-col gap-0.5">
                                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-bold">Undha-Usuk 1</span>
                                            <span class="font-heading text-heading font-bold text-primary-700">Mangan</span>
                                            <span class="font-caption text-caption text-on-surface-variant font-bold">Ngoko Lugu</span>
                                        </div>
                                        <p class="font-caption text-caption text-on-surface-variant">
                                            Kangge nyritakaken awake dhewe utawa tiyang ingkang sapantaran/luwih enom.
                                        </p>
                                        <div class="pt-2 bg-surface-container-low p-2 rounded text-on-surface">
                                            <span class="font-caption text-caption font-bold text-primary-700 block mb-0.5">Tuladha:</span>
                                            <p class="font-caption text-caption italic text-on-surface-variant">"Aku mangan sega liwet ing pawon."</p>
                                        </div>
                                    </div>

                                    <!-- Undha-Usuk 2 -->
                                    <div class="bg-surface-container-lowest p-4 rounded-xl flex flex-col justify-between gap-2 shadow-sm border border-gray-100">
                                        <div class="flex flex-col gap-0.5">
                                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-bold">Undha-Usuk 2</span>
                                            <span class="font-heading text-heading font-bold text-primary-700">Nedha</span>
                                            <span class="font-caption text-caption text-on-surface-variant font-bold">Krama Madya / Andhap</span>
                                        </div>
                                        <p class="font-caption text-caption text-on-surface-variant">
                                            Kangge awake dhewe nalika matur marang tiyang sepuh, utawi basa krama lugu.
                                        </p>
                                        <div class="pt-2 bg-surface-container-low p-2 rounded text-on-surface">
                                            <span class="font-caption text-caption font-bold text-primary-700 block mb-0.5">Tuladha:</span>
                                            <p class="font-caption text-caption italic text-on-surface-variant">"Kula nembe nedha sekul goreng, Pak."</p>
                                        </div>
                                    </div>

                                    <!-- Undha-Usuk 3 -->
                                    <div class="bg-surface-container-lowest p-4 rounded-xl flex flex-col justify-between gap-2 shadow-sm border border-gray-100">
                                        <div class="flex flex-col gap-0.5">
                                            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-bold">Undha-Usuk 3</span>
                                            <span class="font-heading text-heading font-bold text-primary-700">Dhahar</span>
                                            <span class="font-caption text-caption text-on-surface-variant font-bold">Krama Inggil (Pakurmatan)</span>
                                        </div>
                                        <p class="font-caption text-caption text-on-surface-variant">
                                            Mligi kangge ngurmati tiyang sanes/sepuh (Bapak, Ibu, Simbah, Bapak/Ibu Guru).
                                        </p>
                                        <div class="pt-2 bg-surface-container-low p-2 rounded text-on-surface">
                                            <span class="font-caption text-caption font-bold text-primary-700 block mb-0.5">Tuladha:</span>
                                            <p class="font-caption text-caption italic text-on-surface-variant">"Bapak Guru saweg dhahar wonten kantor."</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Warning info box -->
                                <div class="bg-primary-fixed/40 p-4 rounded-xl flex items-start gap-3 border border-primary-300/30">
                                    <span class="material-symbols-outlined text-primary-700 text-xl shrink-0" style="font-variation-settings: 'FILL' 1;">info</span>
                                    <p class="font-body text-body text-on-surface leading-relaxed">
                                        <strong class="text-primary-700">Weling Wigati:</strong> Sampun ngantos ngagem tembung <em>"Dhahar"</em> kangge ngandharaken pakaryanipun piyambak (conto lepat: <em>"Kula sampun dhahar"</em>). Kedahipun matur: <em>"Kula sampun nedha"</em>.
                                    </p>
                                </div>

                                <!-- Audio Player & Source Footer -->
                                <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                                    <button type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-surface-container-high hover:bg-primary-700 hover:text-white text-primary-700 rounded-full font-body text-body font-bold transition-all shadow-sm" onclick="alert('Muter pelafalan baku TTS Azure...')">
                                        <span class="material-symbols-outlined text-lg">volume_up</span>
                                        <span>Rungokake Swara (TTS Azure)</span>
                                    </button>
                                    <div class="flex items-center gap-1.5 text-gray-500 font-caption text-caption">
                                        <span class="material-symbols-outlined text-sm">menu_book</span>
                                        <span>Sumber Korpus: Paramasastra Jawa & Tata Bahasa Baku Basa Jawa SMP Kelas 7</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Message 2 -->
                    <div class="flex items-start justify-end gap-4 w-full">
                        <div class="flex flex-col items-end gap-1 max-w-3xl lg:max-w-4xl">
                            <div class="flex items-center gap-2">
                                <span class="font-caption text-caption text-gray-500">10:17 WIB</span>
                                <span class="font-body text-body font-bold text-on-surface">Andi Prasetyo (Siswa)</span>
                                <div class="w-8 h-8 rounded-full bg-primary-700 text-white font-bold text-xs flex items-center justify-center">
                                    AP
                                </div>
                            </div>
                            <div class="bg-primary-600 text-white p-4 rounded-2xl shadow-sm leading-relaxed">
                                <p class="font-body text-body">
                                    Matur nuwun cethanipun! Nyuwun pirsa malih, yen kangge kucing utawa kewan sanesipun, punapa pareng ngagem tembung <em>"nedha"</em> supados langkung alus?
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Bot Response 2 -->
                    <div class="flex items-start gap-4 w-full">
                        <div class="w-10 h-10 rounded-xl bg-primary-700 flex items-center justify-center text-white shrink-0 shadow-sm">
                            <span class="material-symbols-outlined text-2xl">neurology</span>
                        </div>
                        <div class="flex flex-col gap-1 flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-body text-body font-bold text-on-surface">Kanca Sinau Jawa AI</span>
                                <span class="font-caption text-caption text-gray-500">10:17 WIB</span>
                            </div>
                            <div class="bg-surface-container-low p-4 rounded-2xl text-on-surface space-y-3 border border-primary-100/40">
                                <p class="font-body text-body leading-relaxed">
                                    <strong class="text-error font-bold">Boten pareng, Mas Andi.</strong> Wonten paugeran basa Jawa, sedaya solah bawa utawa pakaryanipun kewan (kados ta kucing, jaran, maesa) tansah ngginakaken <strong class="text-primary-700">Basa Ngoko Lugu</strong>.
                                </p>
                                <p class="font-body text-body text-on-surface-variant leading-relaxed">
                                    Tembung <em>"nedha"</em> punapa malih <em>"dhahar"</em> mligi dipuncawisaken kangge titah manungsa minangka wujud tata krama pakurmatan. Sanadyan kita remen utawa ngopeni kucing punika kanthi asih, ukaranipun tetep ngagem:
                                </p>
                                <div class="p-3 bg-surface-container-lowest rounded-xl font-body text-body text-primary-700 font-bold border border-gray-100">
                                    "Kucingku lagi mangan pindhang ing latar." (Bener)
                                    <br>
                                    <span class="font-caption text-caption text-error font-medium">"Kucing kula nembe nedha / dhahar" (Salah kaprah miturut tata basa).</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Popular Suggestion Chips -->
                <div class="flex flex-col gap-2 w-full">
                    <div class="flex items-center justify-between px-1">
                        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 font-bold">Pitakon Populer Pasinaon</span>
                        <button type="button" class="font-caption text-caption text-primary-600 cursor-pointer hover:underline">Tuduhna liyane</button>
                    </div>
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
                        <button type="button" class="whitespace-nowrap px-4 py-2 bg-surface-container-lowest hover:bg-surface-container-high text-on-surface rounded-full font-body text-body text-primary-700 transition-all shadow-sm flex items-center gap-1.5 border border-gray-100" onclick="const i = document.getElementById('chat-input-field'); if(i){ i.value='Krama Inggil tembung \'Turu\' punapa nggih?'; i.focus(); }">
                            <span class="material-symbols-outlined text-base text-primary-600">help_outline</span>
                            <span>Krama Inggil tembung "Turu"?</span>
                        </button>
                        <button type="button" class="whitespace-nowrap px-4 py-2 bg-surface-container-lowest hover:bg-surface-container-high text-on-surface rounded-full font-body text-body text-primary-700 transition-all shadow-sm flex items-center gap-1.5 border border-gray-100" onclick="const i = document.getElementById('chat-input-field'); if(i){ i.value='Bentenipun Aksara Murda kaliyan Aksara Swara kados pundi?'; i.focus(); }">
                            <span class="material-symbols-outlined text-base text-primary-600">spellcheck</span>
                            <span>Bedane Aksara Murda & Swara</span>
                        </button>
                        <button type="button" class="whitespace-nowrap px-4 py-2 bg-surface-container-lowest hover:bg-surface-container-high text-on-surface rounded-full font-body text-body text-primary-700 transition-all shadow-sm flex items-center gap-1.5 border border-gray-100" onclick="const i = document.getElementById('chat-input-field'); if(i){ i.value='Punapa tegesipun paribasan \'Becik ketitik, ala ketara\'?'; i.focus(); }">
                            <span class="material-symbols-outlined text-base text-primary-600">auto_awesome</span>
                            <span>Tegese Paribasan "Becik Ketitik"</span>
                        </button>
                        <button type="button" class="whitespace-nowrap px-4 py-2 bg-surface-container-lowest hover:bg-surface-container-high text-on-surface rounded-full font-body text-body text-primary-700 transition-all shadow-sm flex items-center gap-1.5 border border-gray-100" onclick="const i = document.getElementById('chat-input-field'); if(i){ i.value='Kados pundi caranipun nyuwun idin marang Guru ingkang leres miturut krama alus?'; i.focus(); }">
                            <span class="material-symbols-outlined text-base text-primary-600">record_voice_over</span>
                            <span>Unggah-ungguh marang Guru</span>
                        </button>
                    </div>
                </div>

                <!-- Chat Input Card -->
                <div class="bg-surface-container-lowest rounded-2xl p-4 shadow-md flex flex-col gap-2 w-full border border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-1 shrink-0">
                            <button type="button" class="w-10 h-10 flex items-center justify-center rounded-full bg-surface-container-low text-on-surface-variant hover:bg-surface-container-high hover:text-primary-600 transition-colors" title="Unggah dokumen / gambar soal aksara">
                                <span class="material-symbols-outlined text-xl">attach_file</span>
                            </button>
                            <button type="button" class="w-10 h-10 flex items-center justify-center rounded-full bg-surface-container-low text-on-surface-variant hover:bg-primary-fixed hover:text-primary-700 transition-colors" title="Matur nganggo swara (STT Wicara)">
                                <span class="material-symbols-outlined text-xl">mic</span>
                            </button>
                        </div>
                        <div class="flex-1 min-w-0 bg-surface-container-low rounded-xl px-4 py-1.5 flex items-center border border-primary-100/50">
                            <input type="text" id="chat-input-field" placeholder="Ketik pitakon babagan basa utawa budaya Jawa ing kene..." class="w-full bg-transparent border-none outline-none font-body text-body text-on-surface placeholder:text-gray-400 py-1">
                        </div>
                        <button type="button" class="shrink-0 inline-flex items-center gap-2 px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-full font-body text-body font-bold transition-all shadow-md">
                            <span>Kirim Pitakon</span>
                            <span class="material-symbols-outlined text-lg">send</span>
                        </button>
                    </div>
                    <div class="flex items-center justify-between px-1 pt-1 border-t border-gray-100/60">
                        <div class="flex items-center gap-1.5 text-gray-500">
                            <span class="material-symbols-outlined text-sm text-green-500">verified</span>
                            <span class="font-caption text-caption text-on-surface-variant">Asisten Tanya Basa njawab adhedhasar basis data korpus resmi sekolah. Ora ngarang wangsulan ing sanjabane materi pasinaon.</span>
                        </div>
                        <span class="font-caption text-caption text-gray-400 hidden sm:inline">Shift + Enter kangge garis anyar</span>
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>
</html>