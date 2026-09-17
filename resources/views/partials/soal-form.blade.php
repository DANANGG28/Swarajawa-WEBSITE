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
            <select name="level_materi_id" required class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                @foreach ($levels as $level)
                    <option value="{{ $level->id }}" @selected(old('level_materi_id', $soal->level_materi_id ?? '') == $level->id)>
                        Level {{ $level->urutan }} — {{ $level->nama_materi }}
                    </option>
                @endforeach
            </select>
        </label>

        <label class="flex flex-col gap-1.5">
            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Tipe Soal</span>
            <select name="tipe_soal" required data-tipe class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
                @foreach ($tipeList as $value => $label)
                    <option value="{{ $value }}" @selected(old('tipe_soal', $soal->tipe_soal ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>

        <label class="flex flex-col gap-1.5">
            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Bobot EXP</span>
            <input type="number" name="bobot_exp" min="0" max="1000" required value="{{ old('bobot_exp', $soal->bobot_exp ?? 10) }}"
                class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
        </label>
    </div>

    <label class="flex flex-col gap-1.5">
        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Pertanyaan / Instruksi</span>
        <textarea name="pertanyaan" id="pertanyaan-{{ $prefix }}" rows="2" required class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">{{ old('pertanyaan', $soal->pertanyaan ?? '') }}</textarea>
    </label>
    
    <label class="flex flex-col gap-1.5">
        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Gambar Pendukung (Opsional)</span>
        <input type="file" name="file_gambar" accept="image/*" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
        @if(isset($soal) && $soal->media_gambar_url)
            <span class="text-caption text-gray-500">Gambar saat ini tersimpan: <a href="{{ Storage::url($soal->media_gambar_url) }}" target="_blank" class="text-primary-600 underline">Lihat Gambar</a></span>
        @endif
    </label>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <label class="flex flex-col gap-1.5">
            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Opsi Jawaban (JSON)</span>
            <textarea name="opsi_jawaban_raw" rows="5" spellcheck="false" data-opsi
                class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-mono text-caption outline-none focus:border-primary-500">{{ old('opsi_jawaban_raw', $opsiVal) }}</textarea>
        </label>

        <label class="flex flex-col gap-1.5">
            <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Kunci Jawaban (JSON)</span>
            <textarea name="kunci_jawaban_raw" rows="5" required spellcheck="false" data-kunci
                class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-mono text-caption outline-none focus:border-primary-500">{{ old('kunci_jawaban_raw', $kunciVal) }}</textarea>
        </label>
    </div>

    <div class="flex flex-col gap-3 p-4 rounded-xl border border-gray-100 bg-gray-50/50">
        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Media Audio</span>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="flex flex-col gap-1.5">
                <span class="text-caption text-gray-500">Upload File Audio Manual</span>
                <input type="file" name="file_audio" accept="audio/*" class="rounded-xl border border-gray-200 bg-white px-4 py-3 font-body text-body outline-none focus:border-primary-500">
            </label>
            <div class="flex flex-col gap-1.5">
                <span class="text-caption text-gray-500">Atau Buat Otomatis via AI Text-to-Speech</span>
                <button type="button" id="btn-tts-{{ $prefix }}" class="flex items-center justify-center gap-2 rounded-xl bg-primary-100 hover:bg-primary-200 text-primary-800 font-body text-body font-bold px-4 py-3 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">record_voice_over</span> Generate dari Teks
                </button>
            </div>
        </div>
        
        <label class="flex flex-col gap-1.5 mt-2">
            <span class="text-caption text-gray-500">URL Audio (Otomatis terisi jika upload file / pakai TTS)</span>
            <input type="text" name="media_audio_url" id="audio-url-{{ $prefix }}" value="{{ old('media_audio_url', $soal->media_audio_url ?? '') }}"
                class="rounded-xl border border-gray-200 bg-white px-4 py-3 font-body text-body outline-none focus:border-primary-500" placeholder="Path ke file audio...">
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
