<!-- Modal Popup Hasil Kuis Terpusat di Tengah (3D Duolingo Style & Compact) -->
<div id="quiz-feedback-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-300">
    <div id="quiz-feedback-card" class="bg-white rounded-[28px] p-5 sm:p-6 max-w-[360px] w-full shadow-2xl border-2 border-slate-200 border-b-[6px] border-b-slate-300 flex flex-col items-center text-center transform scale-95 transition-all duration-300">

        <!-- Emoji Reaksi 3D Badge -->
        <div id="modal-emoji-badge" class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl sm:rounded-3xl flex items-center justify-center text-3xl sm:text-4xl border-2 border-b-4 mb-2.5 shadow-sm transform transition-transform hover:scale-105 select-none bg-emerald-100 border-emerald-300 border-b-emerald-500">
            <span id="modal-emoji">🥳</span>
        </div>

        <!-- Status Pill Badge -->
        <div id="modal-status-badge" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl font-heading font-black text-[11px] tracking-wider uppercase mb-1.5 bg-emerald-500 text-white border-b-2 border-emerald-700 shadow-sm">
            <span id="modal-status-icon" class="material-symbols-outlined text-[16px]">verified</span>
            <span id="modal-status-text">Leres Sanget!</span>
        </div>

        <!-- Judul -->
        <h3 id="modal-title" class="font-heading text-lg sm:text-xl font-black text-slate-800 leading-tight">
            Jawabanmu Bener!
        </h3>

        <!-- Ringkasan Statistik 3D Tiles -->
        <div class="grid grid-cols-3 gap-2 w-full my-3.5">
            <div class="flex flex-col items-center justify-center py-2 px-1.5 rounded-xl bg-slate-50 border-2 border-slate-200 border-b-[3px] border-b-slate-300">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Skor</span>
                <span id="modal-stat-skor" class="font-heading text-sm sm:text-base font-black text-slate-800 mt-0.5">100</span>
            </div>
            <div class="flex flex-col items-center justify-center py-2 px-1.5 rounded-xl bg-yellow-50 border-2 border-yellow-200 border-b-[3px] border-b-amber-300">
                <span class="text-[10px] font-extrabold text-amber-600 uppercase tracking-wider">Hadiah</span>
                <div class="flex items-center gap-0.5 text-amber-600 mt-0.5">
                    <span class="material-symbols-outlined icon-fill text-[15px] text-amber-500">star</span>
                    <span id="modal-stat-exp" class="font-heading text-sm sm:text-base font-black">+10 XP</span>
                </div>
            </div>
            <div class="flex flex-col items-center justify-center py-2 px-1.5 rounded-xl bg-orange-50 border-2 border-orange-200 border-b-[3px] border-b-orange-300">
                <span class="text-[10px] font-extrabold text-orange-600 uppercase tracking-wider">Streak</span>
                <div class="flex items-center gap-0.5 text-orange-600 mt-0.5">
                    <span class="text-xs">🔥</span>
                    <span id="modal-stat-streak" class="font-heading text-sm sm:text-base font-black">1 Dina</span>
                </div>
            </div>
        </div>

        <!-- Penjelasan / Kunci Jawaban (jika salah) -->
        <div id="modal-kunci-box" class="w-full text-left p-3 rounded-xl bg-amber-50/80 border-2 border-amber-200 border-b-[3px] border-b-amber-300 text-xs text-amber-950 mb-3 hidden">
            <div class="font-bold text-amber-700 text-[10px] uppercase tracking-wider mb-0.5 flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">info</span>
                Kunci Jawaban:
            </div>
            <div id="modal-kunci-text" class="font-extrabold text-slate-900 leading-snug"></div>
        </div>

        <!-- Banner Level Selesai -->
        <div id="modal-level-selesai-banner" class="w-full text-left p-3 rounded-xl bg-emerald-50 border-2 border-emerald-300 border-b-[3px] border-b-emerald-500 text-emerald-900 mb-3 hidden">
            <div class="font-heading text-xs sm:text-sm font-black flex items-center gap-1.5 text-emerald-700">
                <span class="material-symbols-outlined icon-fill text-[18px] text-emerald-600">military_tech</span>
                <span>Level Rampung! +<span id="modal-bonus-exp">0</span> Bonus XP!</span>
            </div>
            <p id="modal-level-next-text" class="text-[11px] text-emerald-700 font-semibold mt-0.5">
                Level sabanjure saiki wis kabukak.
            </p>
        </div>

        <!-- Tombol Aksi 3D Duolingo Style -->
        <div id="modal-actions" class="w-full flex flex-col gap-2 mt-0.5">
            <a id="modal-btn-next" href="#" class="w-full py-3 px-5 rounded-2xl bg-primary-600 hover:bg-primary-500 text-white font-heading font-black text-sm uppercase tracking-wider border-b-[4px] border-primary-800 shadow-md active:translate-y-[2px] active:border-b-[2px] active:shadow-none transition-all flex items-center justify-center gap-2 cursor-pointer">
                <span>Soal Selanjutnya</span>
                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </a>
            <a id="modal-btn-next-level" href="#" class="w-full py-3 px-5 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-white font-heading font-black text-sm uppercase tracking-wider border-b-[4px] border-emerald-700 shadow-md active:translate-y-[2px] active:border-b-[2px] active:shadow-none transition-all flex items-center justify-center gap-2 cursor-pointer hidden">
                <span>Mulai Level Berikutnya</span>
                <span class="material-symbols-outlined text-[18px]">military_tech</span>
            </a>
            <a id="modal-btn-home" href="{{ route('siswa.dashboard') }}" class="w-full py-2 px-4 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-heading font-extrabold text-xs uppercase tracking-wider transition-colors">
                Bali menyang Beranda
            </a>
        </div>

    </div>
</div>
