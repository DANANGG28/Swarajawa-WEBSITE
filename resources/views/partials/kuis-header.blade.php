@php
    $persen = ($progress['total'] ?? 0) > 0 ? (int) round((($progress['nomor'] ?? 0) / $progress['total']) * 100) : 0;
@endphp
<header class="sticky top-0 z-50 bg-surface-container-lowest/95 backdrop-blur-xl border-b border-gray-200 shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
    <div class="max-w-6xl mx-auto px-4 md:px-6 h-20 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ url('/latihan-soal') }}" class="w-11 h-11 rounded-xl bg-primary-600 flex items-center justify-center text-white shadow-sm shrink-0">
                <span class="material-symbols-outlined text-[24px]">school</span>
            </a>
            <div class="min-w-0">
                <div class="font-heading text-heading font-extrabold text-on-surface truncate">Sinau Jowo</div>
                <div class="font-label-upper text-label-upper uppercase tracking-wider text-gray-500 truncate">
                    LEVEL {{ $soal['level']['urutan'] ?? 1 }}<span class="mx-1">•</span>{{ $judul }}
                </div>
            </div>
        </div>

        <div class="hidden md:flex flex-col items-center max-w-xs w-full">
            <div class="flex items-center justify-between w-full mb-1">
                <span class="font-caption text-caption font-bold text-on-surface-variant">Soal {{ $progress['nomor'] ?? 0 }} dari {{ $progress['total'] ?? 0 }}</span>
                <span class="font-caption text-caption font-bold text-primary-600">{{ $persen }}% selesai</span>
            </div>
            <div class="w-full h-2.5 bg-surface-container-high rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-primary-600 to-primary-400 rounded-full transition-all duration-500" style="width: {{ $persen }}%"></div>
            </div>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-orange-300/30 text-tertiary shadow-sm">
                <span class="material-symbols-outlined icon-fill text-[20px] text-orange-500">local_fire_department</span>
                <span class="font-heading text-body font-extrabold">{{ $header['streak'] }} Hari</span>
            </div>
            <a href="{{ url('/latihan-soal') }}" class="hidden sm:flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-gray-50 hover:bg-surface-container-high text-on-surface border border-gray-200 transition-colors font-body text-body font-semibold">
                <span class="material-symbols-outlined text-[18px]">close</span>
                <span>Keluar</span>
            </a>
        </div>
    </div>
</header>
