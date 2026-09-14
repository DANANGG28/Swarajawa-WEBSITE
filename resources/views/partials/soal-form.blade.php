@php
    $prefix = $prefix ?? 'soal';
    $isEdit = isset($soal);
    $method = $isEdit ? 'PUT' : 'POST';
    $submitLabel = $isEdit ? 'Simpan Perubahan' : 'Simpan Soal';
    $opsiVal = $isEdit ? json_encode($soal->opsi_jawaban, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '';
    $kunciVal = $isEdit ? json_encode($soal->kunci_jawaban, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '';
@endphp

<form method="POST" action="{{ $action }}" class="flex flex-col gap-4" data-soal-form>
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
        <textarea name="pertanyaan" rows="2" required class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">{{ old('pertanyaan', $soal->pertanyaan ?? '') }}</textarea>
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

    <label class="flex flex-col gap-1.5">
        <span class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500">Media Audio URL (opsional)</span>
        <input type="text" name="media_audio_url" value="{{ old('media_audio_url', $soal->media_audio_url ?? '') }}"
            class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 font-body text-body outline-none focus:border-primary-500">
    </label>

    <button type="submit" class="self-start flex items-center gap-2 rounded-full bg-primary-600 hover:bg-primary-700 text-on-primary font-body text-body font-bold px-6 py-3 shadow-sm transition-colors">
        <span class="material-symbols-outlined text-[20px]">save</span>
        <span>{{ $submitLabel }}</span>
    </button>
</form>
