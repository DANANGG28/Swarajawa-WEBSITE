<!-- Modal Popup Hasil Kuis Terpusat di Tengah (Compact & Borderless) -->
<div id="quiz-feedback-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-300">
    <div id="quiz-feedback-card" class="bg-white rounded-[28px] p-5 sm:p-6 max-w-[360px] w-full shadow-2xl flex flex-col items-center text-center transform scale-95 transition-all duration-300">

        <!-- Emoji / Gambar Reaksi Badge -->
        <div id="modal-emoji-badge" class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl sm:rounded-3xl flex items-center justify-center p-2 mb-2.5 shadow-sm transform transition-transform hover:scale-105 select-none bg-emerald-100 overflow-hidden">
            <img id="modal-emoji-img" src="{{ route('ekpresi.image', ['filename' => 'Benar.png']) }}" alt="Ekspresi Jawaban" class="w-full h-full object-contain">
            <span id="modal-emoji" class="hidden">🥳</span>
        </div>

        <!-- Status Pill Badge -->
        <div id="modal-status-badge" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl font-heading font-black text-[11px] tracking-wider uppercase mb-1.5 bg-emerald-500 text-white shadow-sm text-center">
            <span id="modal-status-icon" class="material-symbols-outlined text-[16px]">verified</span>
            <span id="modal-status-text">Leres Sanget!</span>
        </div>

        <!-- Judul -->
        <h3 id="modal-title" class="font-heading text-lg sm:text-xl font-black text-slate-800 leading-tight text-center">
            Jawabanmu Bener!
        </h3>

        <!-- Ringkasan Statistik Tiles -->
        <div class="grid grid-cols-3 gap-2 w-full my-3.5">
            <div class="flex flex-col items-center justify-center py-2.5 px-1.5 rounded-xl bg-primary-fixed/40 text-center">
                <span class="text-[10px] font-extrabold text-primary-600 uppercase tracking-wider text-center">Skor</span>
                <span id="modal-stat-skor" class="font-heading text-sm sm:text-base font-black text-primary-900 mt-0.5 text-center">100</span>
            </div>
            <div class="flex flex-col items-center justify-center py-2.5 px-1.5 rounded-xl bg-primary-fixed/40 text-center">
                <span class="text-[10px] font-extrabold text-primary-600 uppercase tracking-wider text-center">Hadiah</span>
                <div class="flex items-center justify-center gap-0.5 text-primary-700 mt-0.5 text-center">
                    <span class="material-symbols-outlined icon-fill text-[15px] text-primary-600">star</span>
                    <span id="modal-stat-exp" class="font-heading text-sm sm:text-base font-black text-primary-900 text-center">+10 XP</span>
                </div>
            </div>
            <div class="flex flex-col items-center justify-center py-2.5 px-1.5 rounded-xl bg-primary-fixed/40 text-center">
                <span class="text-[10px] font-extrabold text-primary-600 uppercase tracking-wider text-center">Streak</span>
                <div class="flex items-center justify-center gap-0.5 text-primary-700 mt-0.5 text-center">
                    <span class="text-xs">🔥</span>
                    <span id="modal-stat-streak" class="font-heading text-sm sm:text-base font-black text-primary-900 text-center">1 Dina</span>
                </div>
            </div>
        </div>

        <!-- Penjelasan / Kunci Jawaban (jika salah) -->
        <div id="modal-kunci-box" class="w-full text-center p-3 rounded-xl bg-primary-fixed/40 text-xs text-primary-950 mb-3 hidden flex flex-col items-center justify-center">
            <div class="font-bold text-primary-700 text-[10px] uppercase tracking-wider mb-0.5 flex items-center justify-center gap-1 text-center">
                <span class="material-symbols-outlined text-[14px]">info</span>
                Kunci Jawaban:
            </div>
            <div id="modal-kunci-text" class="font-extrabold text-primary-900 leading-snug text-center"></div>
        </div>

        <!-- Banner Level Selesai -->
        <div id="modal-level-selesai-banner" class="w-full text-center p-3 rounded-xl bg-emerald-50 text-emerald-900 mb-3 hidden flex flex-col items-center justify-center">
            <div class="font-heading text-xs sm:text-sm font-black flex items-center justify-center gap-1.5 text-emerald-700 text-center">
                <span class="material-symbols-outlined icon-fill text-[18px] text-emerald-600">military_tech</span>
                <span>Level Rampung! +<span id="modal-bonus-exp">0</span> Bonus XP!</span>
            </div>
            <p id="modal-level-next-text" class="text-[11px] text-emerald-700 font-semibold mt-0.5 text-center">
                Level sabanjure saiki wis kabukak.
            </p>
        </div>

        <!-- Tombol Aksi -->
        <div id="modal-actions" class="w-full flex flex-col gap-2 mt-0.5">
            <a id="modal-btn-next" href="#" class="w-full py-3 px-5 rounded-2xl bg-primary-600 hover:bg-primary-500 text-white font-heading font-black text-sm uppercase tracking-wider shadow-md active:translate-y-[1px] active:shadow-none transition-all flex items-center justify-center gap-2 cursor-pointer text-center">
                <span>Soal Selanjutnya</span>
                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </a>
            <a id="modal-btn-next-level" href="#" class="w-full py-3 px-5 rounded-2xl bg-primary-600 hover:bg-primary-500 text-white font-heading font-black text-sm uppercase tracking-wider shadow-md active:translate-y-[1px] active:shadow-none transition-all flex items-center justify-center gap-2 cursor-pointer hidden text-center">
                <span>Mulai Level Berikutnya</span>
                <span class="material-symbols-outlined text-[18px]">military_tech</span>
            </a>
            <a id="modal-btn-home" href="{{ route('siswa.dashboard') }}" class="w-full py-2 px-4 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-heading font-extrabold text-xs uppercase tracking-wider transition-colors text-center">
                Bali menyang Beranda
            </a>
        </div>

    </div>
</div>
