@php
    $prefix = $prefix ?? 'soal';
    $isEdit = isset($soal);
    $method = $isEdit ? 'PUT' : 'POST';
    $submitLabel = $isEdit ? 'Simpan Perubahan' : 'Simpan Soal';
    $opsiVal = $isEdit ? json_encode($soal->opsi_jawaban, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '';
    $kunciVal = $isEdit ? json_encode($soal->kunci_jawaban, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '';
@endphp

<form method="POST" action="{{ $action }}" class="flex flex-col gap-4" data-soal-form enctype="multipart/form-data">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <label class="flex flex-col gap-1.5">
            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Level Materi</span>
            <select name="level_materi_id" required class="custom-select rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500/30 transition-all cursor-pointer">
                @foreach ($levels as $level)
                    <option value="{{ $level->id }}" @selected(old('level_materi_id', $soal->level_materi_id ?? '') == $level->id)>
                        Level {{ $level->urutan }} — {{ $level->nama_materi }}
                    </option>
                @endforeach
            </select>
        </label>

        <label class="flex flex-col gap-1.5">
            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Tipe Soal</span>
            <select name="tipe_soal" required data-tipe class="custom-select rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500/30 transition-all cursor-pointer">
                @foreach ($tipeList as $value => $label)
                    <option value="{{ $value }}" @selected(old('tipe_soal', $soal->tipe_soal ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>

        <label class="flex flex-col gap-1.5">
            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Bobot EXP</span>
            <input type="number" name="bobot_exp" min="0" max="1000" required value="{{ old('bobot_exp', $soal->bobot_exp ?? 10) }}"
                class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500/30 transition-all">
        </label>
    </div>

    <label class="flex flex-col gap-1.5">
        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Pertanyaan / Instruksi</span>
        <textarea name="pertanyaan" id="pertanyaan-{{ $prefix }}" rows="2" required class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">{{ old('pertanyaan', $soal->pertanyaan ?? '') }}</textarea>
    </label>
    
    <label class="flex flex-col gap-1.5">
        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Gambar Pendukung (Opsional)</span>
        <input type="file" name="file_gambar" accept="image/*" class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500/30 transition-all">
        @if(isset($soal) && $soal->media_gambar_url)
            <span class="text-caption text-gray-500">Gambar saat ini tersimpan: <a href="{{ Storage::url($soal->media_gambar_url) }}" target="_blank" class="text-primary-600 underline">Lihat Gambar</a></span>
        @endif
    </label>

    <!-- Hidden inputs for backend JSON submission -->
    <textarea name="opsi_jawaban_raw" data-opsi class="hidden">{{ old('opsi_jawaban_raw', $opsiVal) }}</textarea>
    <textarea name="kunci_jawaban_raw" data-kunci class="hidden">{{ old('kunci_jawaban_raw', $kunciVal) }}</textarea>

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
                <input type="file" name="file_audio" accept="audio/*" class="rounded-lg border border-gray-200 bg-white px-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500/30 transition-all">
            </label>
            <div class="flex flex-col gap-1.5">
                <span class="text-caption text-gray-500">Atau Buat Otomatis via AI Text-to-Speech</span>
                <button type="button" id="btn-tts-{{ $prefix }}" class="flex items-center justify-center gap-2 rounded-lg bg-primary-100 hover:bg-primary-200 text-primary-800 font-body text-body font-bold px-4 py-3 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">record_voice_over</span> Generate dari Teks
                </button>
            </div>
        </div>
        
        <label class="flex flex-col gap-1.5 mt-2">
            <span class="text-caption text-gray-500">URL Audio (Otomatis terisi jika upload file / pakai TTS)</span>
            <input type="text" name="media_audio_url" id="audio-url-{{ $prefix }}" value="{{ old('media_audio_url', $soal->media_audio_url ?? '') }}"
                class="rounded-lg border border-gray-200 bg-white px-4 py-3 font-body text-body outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500/30 transition-all" placeholder="Path ke file audio...">
        </label>

        <!-- Preview Audio -->
        <div class="mt-2">
            <audio id="audio-preview-{{ $prefix }}" controls 
                class="w-full h-10 {{ old('media_audio_url', $soal->media_audio_url ?? '') ? '' : 'hidden' }}"
                src="{{ old('media_audio_url', $soal->media_audio_url ?? '') ? Storage::url(old('media_audio_url', $soal->media_audio_url ?? '')) : '' }}">
            </audio>
        </div>
    </div>

    <button type="submit" class="self-start flex items-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-body font-bold px-6 py-3 shadow-sm transition-colors mt-2 hover:shadow-md">
        <span class="material-symbols-outlined text-[20px]">save</span>
        <span>{{ $submitLabel }}</span>
    </button>
</form>

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
            const res = await fetch('{{ route("guru.soal.tts") }}', {
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
</script>
