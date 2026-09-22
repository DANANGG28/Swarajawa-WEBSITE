@php
    $prefix = $prefix ?? 'soal';
    $isEdit = isset($soal);
    $method = $isEdit ? 'PUT' : 'POST';
    $submitLabel = $isEdit ? 'Simpan Perubahan' : 'Simpan Soal';
    $opsiVal = $isEdit ? json_encode($soal->opsi_jawaban, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '';
    $kunciVal = $isEdit ? json_encode($soal->kunci_jawaban, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '';
    $ttsRoute = $ttsRoute ?? route('guru.soal.tts');
    $previewRoute = $previewRoute ?? route('guru.soal.preview');
    $disableLevel = $disableLevel ?? false;
    $disablePembahasan = $disablePembahasan ?? false;
@endphp

<form method="POST" action="{{ $action }}" class="flex flex-col gap-4" data-soal-form data-preview-route="{{ $previewRoute }}" data-prefix="{{ $prefix }}" enctype="multipart/form-data">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-error/30 bg-error-container/40 px-4 py-3">
            <p class="font-heading text-sm font-bold text-error">Periksa kembali isian berikut:</p>
            <ul class="mt-1 list-disc list-inside font-body text-xs text-on-error-container space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Custom Dropdown Level Materi --}}
        <div class="relative flex flex-col gap-1.5" id="custom-level-wrapper-{{ $prefix }}">
            <div class="flex items-center justify-between">
                <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Level Materi</span>
                @if ($disableLevel)
                    <span class="inline-flex items-center gap-1 text-[11px] font-medium text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full border border-gray-200">
                        <span class="material-symbols-outlined text-[13px]">lock</span>
                        Terkunci
                    </span>
                @endif
            </div>

            @php
                $selectedLevelId = old('level_materi_id', $selectedLevelId ?? ($soal->level_materi_id ?? ($level->id ?? ($levels->first()->id ?? ''))));
                $selectedLevel = $levels->firstWhere('id', $selectedLevelId) ?? $levels->first();
                $selectedLevelText = $selectedLevel ? 'Level '.$selectedLevel->urutan.' — '.$selectedLevel->nama_materi : 'Pilih Level';
            @endphp

            @if ($disableLevel)
                <input type="hidden" name="level_materi_id" value="{{ $selectedLevelId }}">
                <button type="button" id="btn-level-{{ $prefix }}" disabled
                    class="flex items-center justify-between w-full rounded-full border border-gray-200 bg-gray-100/90 px-4 py-3 font-body text-body text-gray-500 shadow-none cursor-not-allowed select-none transition-all">
                    <span id="label-level-{{ $prefix }}" class="truncate font-medium text-gray-600">
                        {{ $selectedLevelText }}
                    </span>
                    <span class="inline-flex items-center justify-center shrink-0 w-5 h-5 text-gray-400 ml-2">
                        <span class="material-symbols-outlined text-[18px] leading-none">lock</span>
                    </span>
                </button>
            @else
                <select name="level_materi_id" required id="select-level-{{ $prefix }}" class="hidden">
                    @foreach ($levels as $level)
                        <option value="{{ $level->id }}" @selected(old('level_materi_id', $soal->level_materi_id ?? '') == $level->id)>
                            Level {{ $level->urutan }} — {{ $level->nama_materi }}
                        </option>
                    @endforeach
                </select>

                <button type="button" id="btn-level-{{ $prefix }}"
                    class="flex items-center justify-between w-full rounded-full border border-gray-200 bg-gray-50 hover:bg-gray-100 hover:border-primary-400 px-4 py-3 font-body text-body text-gray-700 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    <span id="label-level-{{ $prefix }}" class="truncate font-medium text-gray-800">
                        {{ $selectedLevelText }}
                    </span>
                    <span class="inline-flex items-center justify-center shrink-0 w-5 h-5 text-gray-400 ml-2">
                        <span id="chevron-level-{{ $prefix }}" class="material-symbols-outlined text-[20px] leading-none transition-transform duration-200">expand_more</span>
                    </span>
                </button>

                {{-- Dropdown Menu Level Materi dengan Pembatas --}}
                <div id="menu-level-{{ $prefix }}" class="hidden absolute top-full left-0 mt-2 w-full bg-white rounded-2xl shadow-xl border border-gray-100 py-1.5 z-50 overflow-hidden divide-y divide-gray-100 max-h-60 overflow-y-auto">
                    @foreach ($levels as $level)
                        @php $isLvlSelected = ($selectedLevelId == $level->id); @endphp
                        <button type="button" data-val="{{ $level->id }}" data-text="Level {{ $level->urutan }} — {{ $level->nama_materi }}"
                            class="opt-level-item-{{ $prefix }} w-full flex items-center justify-between px-4 py-2.5 text-left font-body text-body {{ $isLvlSelected ? 'bg-primary-50 text-primary-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }} transition-colors">
                            <span>Level {{ $level->urutan }} — {{ $level->nama_materi }}</span>
                            <span class="check-icon material-symbols-outlined text-[18px] text-primary-600 {{ $isLvlSelected ? '' : 'hidden' }}">check</span>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Custom Dropdown Pembahasan --}}
        <div class="relative flex flex-col gap-1.5" id="custom-pembahasan-wrapper-{{ $prefix }}">
            @php
                $selectedPembahasanVal = old('pembahasan_id', $soal->pembahasan_id ?? ($selectedPembahasanId ?? ''));
                $currentPembahasanObj = ($pembahasanList ?? collect())->firstWhere('id', $selectedPembahasanVal);
                $selectedPembahasanLabel = $currentPembahasanObj 
                    ? ($currentPembahasanObj->urutan . '. ' . $currentPembahasanObj->nama) 
                    : 'Tanpa Pembahasan';
            @endphp

            <div class="flex items-center justify-between">
                <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Pembahasan</span>
                @if ($disablePembahasan && $currentPembahasanObj)
                    <span class="inline-flex items-center gap-1 text-[11px] font-medium text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full border border-gray-200">
                        <span class="material-symbols-outlined text-[13px]">lock</span>
                        Terkunci
                    </span>
                @endif
            </div>

            @if ($disablePembahasan && $currentPembahasanObj)
                <input type="hidden" name="pembahasan_id" value="{{ $currentPembahasanObj->id }}">
                <button type="button" id="btn-pembahasan-{{ $prefix }}" disabled
                    class="flex items-center justify-between w-full rounded-full border border-gray-200 bg-gray-100/90 px-4 py-3 font-body text-body text-gray-500 shadow-none cursor-not-allowed select-none transition-all">
                    <span id="label-pembahasan-{{ $prefix }}" class="truncate font-medium text-gray-600">
                        {{ $selectedPembahasanLabel }}
                    </span>
                    <span class="inline-flex items-center justify-center shrink-0 w-5 h-5 text-gray-400 ml-2">
                        <span class="material-symbols-outlined text-[18px] leading-none">lock</span>
                    </span>
                </button>
            @else
                {{-- Real hidden select for form submission --}}
                <select name="pembahasan_id" id="select-pembahasan-{{ $prefix }}" class="hidden">
                    <option value="">Tanpa Pembahasan</option>
                    @foreach (($pembahasanList ?? collect()) as $pembahasan)
                        <option value="{{ $pembahasan->id }}" @selected((string) $selectedPembahasanVal === (string) $pembahasan->id)>
                            {{ $pembahasan->urutan }}. {{ $pembahasan->nama }}
                        </option>
                    @endforeach
                </select>

                <button type="button" id="btn-pembahasan-{{ $prefix }}"
                    class="flex items-center justify-between w-full rounded-full border border-gray-200 bg-gray-50 hover:bg-gray-100 hover:border-primary-400 px-4 py-3 font-body text-body text-gray-700 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                    <span id="label-pembahasan-{{ $prefix }}" class="truncate font-medium text-gray-800">
                        {{ $selectedPembahasanLabel }}
                    </span>
                    <span class="inline-flex items-center justify-center shrink-0 w-5 h-5 text-gray-400 ml-2">
                        <span id="chevron-pembahasan-{{ $prefix }}" class="material-symbols-outlined text-[20px] leading-none transition-transform duration-200">expand_more</span>
                    </span>
                </button>

                {{-- Dropdown Menu Pembahasan dengan Pembatas --}}
                <div id="menu-pembahasan-{{ $prefix }}" class="hidden absolute top-full left-0 mt-2 w-full bg-white rounded-2xl shadow-xl border border-gray-100 py-1.5 z-50 overflow-hidden divide-y divide-gray-100 max-h-60 overflow-y-auto">
                    @php $isNoneSelected = empty($selectedPembahasanVal); @endphp
                    <button type="button" data-val="" data-label="Tanpa Pembahasan"
                        class="opt-pembahasan-item-{{ $prefix }} w-full flex items-center justify-between px-4 py-2.5 text-left font-body text-body {{ $isNoneSelected ? 'bg-primary-50 text-primary-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }} transition-colors">
                        <span>Tanpa Pembahasan</span>
                        <span class="check-icon material-symbols-outlined text-[18px] text-primary-600 {{ $isNoneSelected ? '' : 'hidden' }}">check</span>
                    </button>
                    @foreach (($pembahasanList ?? collect()) as $pembahasan)
                        @php $isPembSelected = ((string) $selectedPembahasanVal === (string) $pembahasan->id); @endphp
                        <button type="button" data-val="{{ $pembahasan->id }}" data-label="{{ $pembahasan->urutan }}. {{ $pembahasan->nama }}"
                            class="opt-pembahasan-item-{{ $prefix }} w-full flex items-center justify-between px-4 py-2.5 text-left font-body text-body {{ $isPembSelected ? 'bg-primary-50 text-primary-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }} transition-colors">
                            <span class="truncate">{{ $pembahasan->urutan }}. {{ $pembahasan->nama }}</span>
                            <span class="check-icon material-symbols-outlined text-[18px] text-primary-600 {{ $isPembSelected ? '' : 'hidden' }}">check</span>
                        </button>
                    @endforeach
                </div>

                @if (($pembahasanList ?? collect())->isEmpty())
                    <span class="text-caption text-gray-400">Belum ada pembahasan pada level ini.</span>
                @endif
            @endif
        </div>

        {{-- Custom Dropdown Tipe Soal --}}
        <div class="relative flex flex-col gap-1.5" id="custom-tipe-wrapper-{{ $prefix }}">
            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Tipe Soal</span>
            
            {{-- Real select retained for form submission & soal-form-script.blade.php compatibility --}}
            <select name="tipe_soal" required data-tipe id="select-tipe-{{ $prefix }}" class="hidden">
                @foreach ($tipeList as $value => $label)
                    <option value="{{ $value }}" @selected(old('tipe_soal', $soal->tipe_soal ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            @php
                $currentTipe = old('tipe_soal', $soal->tipe_soal ?? array_key_first($tipeList));
            @endphp

            <button type="button" id="btn-tipe-{{ $prefix }}"
                class="flex items-center justify-between w-full rounded-full border border-gray-200 bg-gray-50 hover:bg-gray-100 hover:border-primary-400 px-4 py-3 font-body text-body text-gray-700 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                <span id="label-tipe-{{ $prefix }}" class="truncate font-medium text-gray-800">
                    {{ $tipeList[$currentTipe] ?? 'Pilih Tipe Soal' }}
                </span>
                <span class="inline-flex items-center justify-center shrink-0 w-5 h-5 text-gray-400 ml-2">
                    <span id="chevron-tipe-{{ $prefix }}" class="material-symbols-outlined text-[20px] leading-none transition-transform duration-200">expand_more</span>
                </span>
            </button>

            {{-- Dropdown Menu Tipe Soal dengan Pembatas --}}
            <div id="menu-tipe-{{ $prefix }}" class="hidden absolute top-full left-0 mt-2 w-full bg-white rounded-2xl shadow-xl border border-gray-100 py-1.5 z-50 overflow-hidden divide-y divide-gray-100">
                @foreach ($tipeList as $value => $label)
                    @php $isSelected = ($currentTipe === $value); @endphp
                    <button type="button" data-val="{{ $value }}" data-label="{{ $label }}"
                        class="opt-tipe-item-{{ $prefix }} w-full flex items-center justify-between px-4 py-2.5 text-left font-body text-body {{ $isSelected ? 'bg-primary-50 text-primary-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-primary-600' }} transition-colors">
                        <span>{{ $label }}</span>
                        <span class="check-icon material-symbols-outlined text-[18px] text-primary-600 {{ $isSelected ? '' : 'hidden' }}">check</span>
                    </button>
                @endforeach
            </div>
        </div>

        <label class="flex flex-col gap-1.5">
            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Bobot EXP</span>
            <input type="number" name="bobot_exp" min="0" max="1000" step="1" inputmode="numeric"
                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                required value="{{ old('bobot_exp', $soal->bobot_exp ?? 10) }}"
                class="rounded-full border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
            @error('bobot_exp')<span class="text-error text-xs font-caption">{{ $message }}</span>@enderror
        </label>
    </div>

    <label class="flex flex-col gap-1.5">
        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Pertanyaan / Instruksi</span>
        <textarea name="pertanyaan" id="pertanyaan-{{ $prefix }}" rows="2" required maxlength="5000" class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">{{ old('pertanyaan', $soal->pertanyaan ?? '') }}</textarea>
        @error('pertanyaan')<span class="text-error text-xs font-caption">{{ $message }}</span>@enderror
    </label>
    
    <label class="flex flex-col gap-1.5">
        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Gambar Pendukung (Opsional)</span>
        <input type="file" name="file_gambar" accept="image/*" class="rounded-full border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
        @if(isset($soal) && $soal->media_gambar_url)
            <span class="text-caption text-gray-500">Gambar saat ini tersimpan: <a href="{{ Storage::url($soal->media_gambar_url) }}" target="_blank" class="text-primary-600 underline">Lihat Gambar</a></span>
        @endif
    </label>

    <!-- Hidden inputs for backend JSON submission -->
    <textarea name="opsi_jawaban_raw" data-opsi class="hidden">{{ old('opsi_jawaban_raw', $opsiVal) }}</textarea>
    <textarea name="kunci_jawaban_raw" data-kunci class="hidden">{{ old('kunci_jawaban_raw', $kunciVal) }}</textarea>

    <!-- Hidden inputs khusus tracing aksara (diisi oleh live preview) -->
    <input type="hidden" name="soal_latin" data-soal-latin value="{{ old('soal_latin', $soal->soal_latin ?? '') }}">
    <input type="hidden" name="soal_aksara" data-soal-aksara value="{{ old('soal_aksara', $soal->soal_aksara ?? '') }}">

    <!-- Dynamic Form Builder Container -->
    <div id="dynamic-form-builder-{{ $prefix }}" class="flex flex-col gap-4 p-5 border border-gray-200 rounded-xl bg-white shadow-sm mt-2">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-2">
            <div class="flex flex-col">
                <h4 class="font-heading text-heading font-bold text-on-surface">Detail Opsi & Kunci Jawaban</h4>
                <span class="text-caption text-gray-500">Form di bawah menyesuaikan dengan Tipe Soal yang dipilih.</span>
            </div>
        </div>
        <div id="dynamic-fields-{{ $prefix }}" class="flex flex-col gap-4">
            <!-- Fields injected by JS -->
        </div>
    </div>

    <div class="flex flex-col gap-3 p-4 rounded-xl border border-gray-100 bg-gray-50/50">
        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Media Audio</span>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="flex flex-col gap-1.5">
                <span class="text-caption text-gray-500">Upload File Audio Manual</span>
                <input type="file" name="file_audio" accept="audio/*" class="rounded-full border border-gray-200 bg-white px-4 py-3 font-body text-body outline-none focus:border-primary-500">
            </label>
            <div class="flex flex-col gap-1.5">
                <span class="text-caption text-gray-500">Atau Buat Otomatis via AI Text-to-Speech</span>
                <button type="button" id="btn-tts-{{ $prefix }}" class="flex items-center justify-center gap-2 rounded-full bg-primary-100 hover:bg-primary-200 text-primary-800 font-body text-body font-bold px-4 py-3 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">record_voice_over</span> Generate dari Teks
                </button>
            </div>
        </div>
        
        <label class="flex flex-col gap-1.5 mt-2">
            <span class="text-caption text-gray-500">URL Audio (Otomatis terisi jika upload file / pakai TTS)</span>
            <input type="text" name="media_audio_url" id="audio-url-{{ $prefix }}" value="{{ old('media_audio_url', $soal->media_audio_url ?? '') }}"
                maxlength="2048"
                class="rounded-full border border-gray-200 bg-white px-4 py-3 font-body text-body outline-none focus:border-primary-500" placeholder="Path ke file audio...">
            @error('media_audio_url')<span class="text-error text-xs font-caption">{{ $message }}</span>@enderror
        </label>

        <!-- Preview Audio -->
        <div class="mt-2">
            <audio id="audio-preview-{{ $prefix }}" controls 
                class="w-full h-10 {{ old('media_audio_url', $soal->media_audio_url ?? '') ? '' : 'hidden' }}"
                src="{{ old('media_audio_url', $soal->media_audio_url ?? '') ? Storage::url(old('media_audio_url', $soal->media_audio_url ?? '')) : '' }}">
            </audio>
        </div>
    </div>

    <button type="submit" class="self-start flex items-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-body font-bold px-6 py-3 shadow-sm transition-colors mt-2">
        <span class="material-symbols-outlined text-[20px]">save</span>
        <span>{{ $submitLabel }}</span>
    </button>
</form>

{{-- Modal konfirmasi Aksara Jawa (FR-22): guru melihat & menyetujui sebelum simpan --}}
<div id="aksara-confirm-{{ $prefix }}" class="hidden fixed inset-0 z-[100] items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
        <div class="flex items-center gap-2 mb-1">
            <span class="material-symbols-outlined text-primary-600">spellcheck</span>
            <h3 class="font-heading text-heading font-bold text-on-surface">Konfirmasi Aksara Jawa</h3>
        </div>
        <p class="font-caption text-caption text-gray-500 mb-4">Priksa asil konversi sadurunge disimpen — manungsa dadi verifikator pungkasan.</p>
        <div class="rounded-2xl border border-gray-100 bg-surface-container-low p-6 text-center">
            <div data-aksara-confirm-text class="font-javanese text-4xl leading-loose text-on-surface break-words"></div>
        </div>
        <div class="flex justify-end gap-2 mt-5">
            <button type="button" data-aksara-cancel class="rounded-full bg-gray-100 hover:bg-gray-200 text-on-surface px-5 py-2.5 font-body text-body font-bold transition-colors">Batal</button>
            <button type="button" data-aksara-approve class="rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary px-5 py-2.5 font-body text-body font-bold transition-colors">Setuju &amp; Simpan</button>
        </div>
    </div>
</div>

<script>
    document.getElementById('btn-tts-{{ $prefix }}')?.addEventListener('click', async function() {
        const text = document.getElementById('pertanyaan-{{ $prefix }}').value;
        if (!text || text.trim() === '') {
            alert('Silakan isi Pertanyaan / Instruksi terlebih dahulu.');
            return;
        }
        
        const btn = this;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[20px]">sync</span> Memproses...';
        btn.disabled = true;
        
        try {
            const res = await fetch('{{ $ttsRoute }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({text: text})
            });
            const data = await res.json();
            
            if (res.ok && data.success) {
                document.getElementById('audio-url-{{ $prefix }}').value = data.path;
                
                const audioPreview = document.getElementById('audio-preview-{{ $prefix }}');
                if (audioPreview) {
                    audioPreview.src = data.url;
                    audioPreview.classList.remove('hidden');
                }
                
                alert('Audio berhasil digenerate! Path otomatis terisi ke URL Audio.');
            } else {
                alert(data.message || 'Terjadi kesalahan saat generate audio.');
            }
        } catch (e) {
            alert('Kesalahan jaringan. Pastikan koneksi internet stabil.');
        }
        
        btn.innerHTML = originalHtml;
        btn.disabled = false;
    });

    // Custom Dropdown logic for Tipe Soal, Level Materi & Pembahasan (prefix: {{ $prefix }})
    (function() {
        // Tipe Soal Dropdown
        const btnTipe = document.getElementById('btn-tipe-{{ $prefix }}');
        const menuTipe = document.getElementById('menu-tipe-{{ $prefix }}');
        const chevronTipe = document.getElementById('chevron-tipe-{{ $prefix }}');
        const selectTipe = document.getElementById('select-tipe-{{ $prefix }}');
        const labelTipe = document.getElementById('label-tipe-{{ $prefix }}');

        if (btnTipe && menuTipe && selectTipe) {
            btnTipe.addEventListener('click', function(e) {
                e.stopPropagation();
                document.querySelectorAll('[id^="menu-tipe-"], [id^="menu-level-"], [id^="menu-pembahasan-"]').forEach(m => {
                    if (m !== menuTipe) m.classList.add('hidden');
                });
                const isHidden = menuTipe.classList.toggle('hidden');
                if (!isHidden) {
                    chevronTipe.classList.add('rotate-180');
                } else {
                    chevronTipe.classList.remove('rotate-180');
                }
            });

            menuTipe.querySelectorAll('.opt-tipe-item-{{ $prefix }}').forEach(function(opt) {
                opt.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const val = this.getAttribute('data-val');
                    const label = this.getAttribute('data-label');

                    selectTipe.value = val;
                    labelTipe.textContent = label;

                    menuTipe.querySelectorAll('.opt-tipe-item-{{ $prefix }}').forEach(o => {
                        o.classList.remove('bg-primary-50', 'text-primary-700', 'font-bold');
                        o.classList.add('text-gray-700');
                        o.querySelector('.check-icon')?.classList.add('hidden');
                    });
                    this.classList.add('bg-primary-50', 'text-primary-700', 'font-bold');
                    this.classList.remove('text-gray-700');
                    this.querySelector('.check-icon')?.classList.remove('hidden');

                    menuTipe.classList.add('hidden');
                    chevronTipe.classList.remove('rotate-180');

                    // Notify dynamic form builder in soal-form-script.blade.php
                    selectTipe.dispatchEvent(new Event('change'));
                });
            });
        }

        // Pembahasan Dropdown
        const btnPembahasan = document.getElementById('btn-pembahasan-{{ $prefix }}');
        const menuPembahasan = document.getElementById('menu-pembahasan-{{ $prefix }}');
        const chevronPembahasan = document.getElementById('chevron-pembahasan-{{ $prefix }}');
        const selectPembahasan = document.getElementById('select-pembahasan-{{ $prefix }}');
        const labelPembahasan = document.getElementById('label-pembahasan-{{ $prefix }}');

        if (btnPembahasan && menuPembahasan && selectPembahasan) {
            btnPembahasan.addEventListener('click', function(e) {
                e.stopPropagation();
                document.querySelectorAll('[id^="menu-tipe-"], [id^="menu-level-"], [id^="menu-pembahasan-"]').forEach(m => {
                    if (m !== menuPembahasan) m.classList.add('hidden');
                });
                const isHidden = menuPembahasan.classList.toggle('hidden');
                if (!isHidden) {
                    chevronPembahasan.classList.add('rotate-180');
                } else {
                    chevronPembahasan.classList.remove('rotate-180');
                }
            });

            menuPembahasan.querySelectorAll('.opt-pembahasan-item-{{ $prefix }}').forEach(function(opt) {
                opt.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const val = this.getAttribute('data-val');
                    const label = this.getAttribute('data-label');

                    selectPembahasan.value = val;
                    labelPembahasan.textContent = label;

                    menuPembahasan.querySelectorAll('.opt-pembahasan-item-{{ $prefix }}').forEach(o => {
                        o.classList.remove('bg-primary-50', 'text-primary-700', 'font-bold');
                        o.classList.add('text-gray-700');
                        o.querySelector('.check-icon')?.classList.add('hidden');
                    });
                    this.classList.add('bg-primary-50', 'text-primary-700', 'font-bold');
                    this.classList.remove('text-gray-700');
                    this.querySelector('.check-icon')?.classList.remove('hidden');

                    menuPembahasan.classList.add('hidden');
                    chevronPembahasan.classList.remove('rotate-180');

                    selectPembahasan.dispatchEvent(new Event('change'));
                });
            });
        }

        // Level Materi Dropdown
        const btnLevel = document.getElementById('btn-level-{{ $prefix }}');
        const menuLevel = document.getElementById('menu-level-{{ $prefix }}');
        const chevronLevel = document.getElementById('chevron-level-{{ $prefix }}');
        const selectLevel = document.getElementById('select-level-{{ $prefix }}');
        const labelLevel = document.getElementById('label-level-{{ $prefix }}');

        if (btnLevel && menuLevel && selectLevel) {
            btnLevel.addEventListener('click', function(e) {
                e.stopPropagation();
                document.querySelectorAll('[id^="menu-tipe-"], [id^="menu-level-"], [id^="menu-pembahasan-"]').forEach(m => {
                    if (m !== menuLevel) m.classList.add('hidden');
                });
                const isHidden = menuLevel.classList.toggle('hidden');
                if (!isHidden) {
                    chevronLevel.classList.add('rotate-180');
                } else {
                    chevronLevel.classList.remove('rotate-180');
                }
            });

            menuLevel.querySelectorAll('.opt-level-item-{{ $prefix }}').forEach(function(opt) {
                opt.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const val = this.getAttribute('data-val');
                    const text = this.getAttribute('data-text');

                    selectLevel.value = val;
                    labelLevel.textContent = text;

                    menuLevel.querySelectorAll('.opt-level-item-{{ $prefix }}').forEach(o => {
                        o.classList.remove('bg-primary-50', 'text-primary-700', 'font-bold');
                        o.classList.add('text-gray-700');
                        o.querySelector('.check-icon')?.classList.add('hidden');
                    });
                    this.classList.add('bg-primary-50', 'text-primary-700', 'font-bold');
                    this.classList.remove('text-gray-700');
                    this.querySelector('.check-icon')?.classList.remove('hidden');

                    menuLevel.classList.add('hidden');
                    chevronLevel.classList.remove('rotate-180');
                });
            });
        }

        document.addEventListener('click', function(e) {
            if (menuTipe && !btnTipe?.contains(e.target) && !menuTipe?.contains(e.target)) {
                menuTipe.classList.add('hidden');
                chevronTipe?.classList.remove('rotate-180');
            }
            if (menuPembahasan && !btnPembahasan?.contains(e.target) && !menuPembahasan?.contains(e.target)) {
                menuPembahasan.classList.add('hidden');
                chevronPembahasan?.classList.remove('rotate-180');
            }
            if (menuLevel && !btnLevel?.contains(e.target) && !menuLevel?.contains(e.target)) {
                menuLevel.classList.add('hidden');
                chevronLevel?.classList.remove('rotate-180');
            }
        });
    })();
</script>
