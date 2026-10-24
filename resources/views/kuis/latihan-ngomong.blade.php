@extends('layouts.kuis-wicara', ['judul' => 'Voice Tutor Basa Jawa — Si Swara'])

@section('konten')
<div class="w-full max-w-xl mx-auto flex flex-col gap-4 relative" id="voice-tutor-container">
    <!-- Panggung Utama AI Voice Tutor (Single Unified Container) -->
    <div id="ai-stage-container"
        class="bg-white rounded-3xl p-5 sm:p-7 border border-gray-200 shadow-sm flex flex-col items-center justify-between gap-5 relative overflow-hidden select-none transition-all duration-200">
        
        <!-- Header Bar Panggung: Status Sambungan & Pengaturan Cepat -->
        <div class="w-full flex items-center justify-between gap-3 border-b border-gray-100 pb-3.5">
            <div class="flex items-center gap-2.5">
                <span class="relative flex h-2.5 w-2.5">
                    <span id="live-indicator-dot" class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                </span>
                <div class="flex flex-col">
                    <span class="font-heading text-xs sm:text-sm font-bold text-black-900 leading-tight">Si Swara</span>
                    <span id="status-text" class="font-body text-[11px] sm:text-xs text-gray-500 font-medium">Aktif · Tutor Wicara Basa Jawa</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <!-- Kecepatan Audio (1.0x / 0.8x) -->
                <button id="btn-speed-toggle" type="button" aria-label="Ubah kecepatan suara"
                    class="inline-flex items-center px-2.5 py-1 rounded-xl bg-gray-100 hover:bg-gray-200 text-primary-700 font-heading text-xs font-bold transition-colors">
                    <span id="speed-label">1.0x</span>
                </button>

                <!-- Rekap Kosakata Sesi -->
                <button id="btn-open-vocab" type="button" aria-label="Buka rekap kosakata"
                    class="inline-flex items-center gap-1 px-3 py-1 rounded-xl bg-primary-50 hover:bg-primary-100 text-primary-700 font-heading text-xs font-bold transition-colors border border-primary-200/60">
                    <span class="material-symbols-outlined text-[16px]">auto_stories</span>
                    <span class="hidden sm:inline">Kosakata</span>
                    <span id="vocab-badge-count" class="ml-0.5 px-1.5 py-0.2 rounded-full bg-primary-600 text-white text-[10px] font-bold">2</span>
                </button>
            </div>
        </div>

        <!-- Center Mascot Stage: Maskot Kapsul Oval Interaktif (Sekaligus Tombol Mic Utama) -->
        <div class="flex flex-col items-center justify-center w-full py-2">
            <div id="mascot-interactive-btn" role="button" tabindex="0" aria-label="Ketuk Si Swara kanggo wicara utawa mandheg"
                class="relative w-[180px] h-[210px] flex items-center justify-center cursor-pointer group focus:outline-none rounded-full">
                
                <!-- Cincin Gelombang Audio Konsentris di Belakang Kapsul -->
                <svg id="pulse-rings" class="absolute inset-0 w-full h-full pointer-events-none transition-opacity duration-300 opacity-0" viewBox="0 0 180 210">
                    <circle id="pulse-r1" cx="90" cy="105" r="62" fill="none" stroke="#A79BFF" stroke-width="2.5" opacity="0.4" />
                    <circle id="pulse-r2" cx="90" cy="105" r="74" fill="none" stroke="#7B6CF0" stroke-width="2" opacity="0.25" />
                    <circle id="pulse-r3" cx="90" cy="105" r="86" fill="none" stroke="#6C5CE8" stroke-width="1.5" opacity="0.15" />
                </svg>

                <!-- Maskot Kapsul Vektor "Si Swara" -->
                <svg id="mascot-svg" class="relative z-10 w-[140px] h-[190px] drop-shadow-md transition-transform duration-200 group-hover:scale-[1.03]" viewBox="0 0 140 190">
                    <defs>
                        <linearGradient id="pill-body-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#FFFFFF" />
                            <stop offset="60%" stop-color="#F5F2FF" />
                            <stop offset="100%" stop-color="#E9E2FE" />
                        </linearGradient>
                    </defs>

                    <!-- Badan Kapsul Utama -->
                    <rect id="pill-rect-border" x="10" y="10" width="120" height="168" rx="60" fill="url(#pill-body-grad)" stroke="#A79BFF" stroke-width="2.5" class="transition-colors duration-200" />

                    <!-- Aksen Blangkon / Motif Tradisional Jawa ing Kepala Kapsul -->
                    <path d="M 32 32 Q 70 24 108 32 L 104 38 Q 70 30 36 38 Z" fill="#5443C9" opacity="0.22" />

                    <!-- Pipi Merona (Blush) -->
                    <circle id="blush-left" cx="34" cy="102" r="7.5" fill="#F08CA0" opacity="0.4" />
                    <circle id="blush-right" cx="106" cy="102" r="7.5" fill="#F08CA0" opacity="0.4" />

                    <!-- Mata Kiwa & Tengen (Gaze Tracking & Blink) -->
                    <g id="eyes-group">
                        <!-- Mata Kiwa -->
                        <g id="eye-left-container" transform="translate(48, 88)">
                            <ellipse id="eye-left-pupil" cx="0" cy="0" rx="7.5" ry="11.5" fill="#1E1E2A" />
                            <circle id="eye-left-shine" cx="-2.5" cy="-3.5" r="2.4" fill="#FFFFFF" />
                            <path id="eye-left-happy" d="M -8 3 Q 0 -6 8 3" fill="none" stroke="#1E1E2A" stroke-width="3" stroke-linecap="round" class="hidden" />
                        </g>
                        <!-- Mata Tengen -->
                        <g id="eye-right-container" transform="translate(92, 88)">
                            <ellipse id="eye-right-pupil" cx="0" cy="0" rx="7.5" ry="11.5" fill="#1E1E2A" />
                            <circle id="eye-right-shine" cx="-2.5" cy="-3.5" r="2.4" fill="#FFFFFF" />
                            <path id="eye-right-happy" d="M -8 3 Q 0 -6 8 3" fill="none" stroke="#1E1E2A" stroke-width="3" stroke-linecap="round" class="hidden" />
                        </g>
                    </g>

                    <!-- Alis (Munggah nalika mikir / curious) -->
                    <g id="eyebrows-group" opacity="0">
                        <line x1="41" y1="73" x2="55" y2="74" stroke="#1E1E2A" stroke-width="2.2" stroke-linecap="round" />
                        <path d="M 85 75 Q 92 68 100 72" fill="none" stroke="#1E1E2A" stroke-width="2.2" stroke-linecap="round" />
                    </g>

                    <!-- Mulut (Lip-Sync Real-Time) -->
                    <g id="mouth-group" transform="translate(70, 118)">
                        <!-- Mulut Biasa / Senyum Lembut -->
                        <path id="mouth-neutral" d="M -8 -1 Q 0 6 8 -1" fill="none" stroke="#1E1E2A" stroke-width="2.6" stroke-linecap="round" />

                        <!-- Mulut Terbuka Happy -->
                        <path id="mouth-happy" d="M -9 -2 Q 0 10 9 -2 Z" fill="#2D1B4E" class="hidden" />
                        <circle id="mouth-tongue" cx="0" cy="4" r="3.8" fill="#F08CA0" class="hidden" />

                        <!-- Mulut LipSync Terbuka Dinamis -->
                        <rect id="mouth-talking" x="-7" y="-3" width="14" height="6" rx="3" fill="#2D1B4E" class="hidden" />
                    </g>
                </svg>
            </div>

            <!-- Badge Petunjuk Mic (Interaktif) -->
            <div id="mascot-mic-badge" class="mt-2.5 inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary-50 text-primary-700 border border-primary-200/80 transition-all duration-200">
                <span id="mascot-mic-icon" class="material-symbols-outlined text-[18px]">mic</span>
                <span id="mascot-mic-label" class="font-heading text-xs sm:text-sm font-bold">Ketuk Si Swara kanggo wicara</span>
            </div>
        </div>

        <!-- Wadah Subtitle AI (Hanya Muncul Saat AI Berbicara & Ditempatkan di Container AI Ini) -->
        <div id="ai-subtitle-container" class="w-full hidden transition-all duration-200 animate-in fade-in zoom-in-95">
            <div class="w-full p-4 rounded-2xl bg-surface-container-low border border-primary-200/80 text-black-900 shadow-2xs flex items-start gap-3">
                <div class="w-7 h-7 rounded-full bg-primary-600 text-white flex items-center justify-center shrink-0 font-bold text-xs mt-0.5">
                    S
                </div>
                <div class="flex-1 min-w-0">
                    <p id="ai-subtitle-text" class="font-body text-xs sm:text-sm text-black-900 leading-relaxed"></p>
                    <span id="ai-subtitle-cursor" class="inline-block w-1.5 h-3.5 bg-primary-600 animate-pulse align-middle ml-0.5"></span>
                </div>
                <button id="btn-replay-subtitle" type="button" title="Putar ulang audio"
                    class="shrink-0 p-1.5 rounded-xl bg-white hover:bg-primary-50 text-primary-700 border border-primary-200/60 shadow-2xs transition-all active:scale-95 hidden">
                    <span class="material-symbols-outlined text-[16px]">volume_up</span>
                </button>
            </div>
        </div>

        <!-- Kartu Tantangan Tirukan Lafal (Repeat Challenge Card) -->
        <div id="repeat-challenge-card" class="w-full hidden p-3.5 sm:p-4 rounded-2xl bg-white border border-primary-200 shadow-2xs flex items-center justify-between gap-3 transition-all duration-200">
            <div class="flex items-start gap-3 min-w-0">
                <div class="w-8 h-8 rounded-xl bg-primary-100 text-primary-700 flex items-center justify-center shrink-0 mt-0.5">
                    <span class="material-symbols-outlined text-[18px]">record_voice_over</span>
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="font-body text-[11px] font-semibold text-gray-500">Coba tirukna ukara Jawa iki:</span>
                    <span id="repeat-challenge-text" class="font-heading text-sm sm:text-base font-bold text-primary-800 truncate">Sugeng enjang, Mas Swara</span>
                </div>
            </div>
            <button id="btn-slow-play" type="button" title="Rungokna alon (0.8x)"
                class="shrink-0 p-2 rounded-xl bg-surface-container-low hover:bg-primary-50 text-primary-700 border border-primary-200 shadow-2xs active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[18px]">slow_motion_video</span>
            </button>
        </div>

        <!-- Footer Bar Panggung: Toggle Hands-Free & Opsi Ketik -->
        <div class="w-full pt-3 border-t border-gray-100 flex items-center justify-between gap-3">
            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                <input id="toggle-handsfree" type="checkbox" checked class="sr-only peer">
                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary-600"></div>
                <span class="font-body text-xs font-semibold text-gray-700">Mode Hands-Free (Auto Stop)</span>
            </label>

            <button id="btn-toggle-input-mode" type="button"
                class="font-body text-xs font-bold text-primary-700 hover:text-primary-800 flex items-center gap-1">
                <span class="material-symbols-outlined text-[15px]">keyboard</span>
                <span id="label-toggle-input">Ketik Pertanyaan</span>
            </button>
        </div>

        <!-- Form Teks Alternatif (Muncul jika tombol ketik dipilih) -->
        <form id="form-text-input" class="w-full hidden flex items-center gap-2 pt-2 border-t border-gray-100">
            <input id="input-text-chat" type="text" placeholder="Ketik pertanyaan atau kalimat Bahasa Jawa..."
                class="flex-1 bg-surface-container-low border border-gray-200 rounded-xl px-3.5 py-2 font-body text-xs sm:text-sm text-black-900 focus:outline-none focus:border-primary-500">
            <button type="submit"
                class="px-4 py-2 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-heading text-xs sm:text-sm font-bold shadow-2xs transition-all active:scale-95 flex items-center gap-1">
                <span>Kirim</span>
                <span class="material-symbols-outlined text-[15px]">send</span>
            </button>
        </form>
    </div>
</div>

<!-- Modal Rekap Kosakata Sesi (Session Feedback) -->
<div id="modal-vocab" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs">
    <div class="bg-white rounded-3xl w-full max-w-md p-5 sm:p-6 border border-gray-200 shadow-xl flex flex-col gap-4 animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-primary-100 text-primary-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">auto_stories</span>
                </div>
                <h3 class="font-heading text-base font-bold text-black-900">Kosakata Pasinaon</h3>
            </div>
            <button id="btn-close-vocab" type="button" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        <div id="vocab-modal-list" class="flex flex-col gap-2 max-h-[320px] overflow-y-auto pr-1">
            <div class="p-3 rounded-2xl bg-surface-container-low border border-primary-100/60 flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-heading text-sm font-bold text-black-900">Matur nuwun</span>
                        <span class="px-2 py-0.5 rounded-md bg-primary-100 text-primary-800 text-[10px] font-bold">Krama</span>
                    </div>
                    <span class="font-body text-xs text-gray-600">Terima kasih</span>
                </div>
            </div>
            <div class="p-3 rounded-2xl bg-surface-container-low border border-primary-100/60 flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-heading text-sm font-bold text-black-900">Pangapunten</span>
                        <span class="px-2 py-0.5 rounded-md bg-primary-100 text-primary-800 text-[10px] font-bold">Krama Alus</span>
                    </div>
                    <span class="font-body text-xs text-gray-600">Permohonan maaf</span>
                </div>
            </div>
        </div>

        <button id="btn-dismiss-vocab" type="button"
            class="w-full py-2.5 rounded-2xl bg-primary-600 hover:bg-primary-700 text-white font-heading text-xs sm:text-sm font-bold shadow-md shadow-primary-600/25 transition-all">
            Tutup Rekap
        </button>
    </div>
</div>
@endsection

@push('skrip')
<script>
(function() {
    // 1. Elemen Panggung & Maskot
    const mascotBtn = document.getElementById('mascot-interactive-btn');
    const mascotSvg = document.getElementById('mascot-svg');
    const pillRectBorder = document.getElementById('pill-rect-border');
    const eyeLeftPupil = document.getElementById('eye-left-pupil');
    const eyeRightPupil = document.getElementById('eye-right-pupil');
    const eyeLeftShine = document.getElementById('eye-left-shine');
    const eyeRightShine = document.getElementById('eye-right-shine');
    const eyeLeftHappy = document.getElementById('eye-left-happy');
    const eyeRightHappy = document.getElementById('eye-right-happy');
    const eyebrowsGroup = document.getElementById('eyebrows-group');
    const mouthNeutral = document.getElementById('mouth-neutral');
    const mouthHappy = document.getElementById('mouth-happy');
    const mouthTongue = document.getElementById('mouth-tongue');
    const mouthTalking = document.getElementById('mouth-talking');
    const pulseRings = document.getElementById('pulse-rings');

    const mascotMicBadge = document.getElementById('mascot-mic-badge');
    const mascotMicIcon = document.getElementById('mascot-mic-icon');
    const mascotMicLabel = document.getElementById('mascot-mic-label');

    // Subtitle Container ing Jeroning Container AI
    const aiSubtitleContainer = document.getElementById('ai-subtitle-container');
    const aiSubtitleText = document.getElementById('ai-subtitle-text');
    const aiSubtitleCursor = document.getElementById('ai-subtitle-cursor');
    const btnReplaySubtitle = document.getElementById('btn-replay-subtitle');

    const repeatChallengeCard = document.getElementById('repeat-challenge-card');
    const repeatChallengeText = document.getElementById('repeat-challenge-text');
    const btnSlowPlay = document.getElementById('btn-slow-play');

    const statusText = document.getElementById('status-text');
    const toggleHandsfree = document.getElementById('toggle-handsfree');
    const btnSpeedToggle = document.getElementById('btn-speed-toggle');
    const speedLabel = document.getElementById('speed-label');
    const btnOpenVocab = document.getElementById('btn-open-vocab');
    const btnCloseVocab = document.getElementById('btn-close-vocab');
    const btnDismissVocab = document.getElementById('btn-dismiss-vocab');
    const modalVocab = document.getElementById('modal-vocab');
    const vocabModalList = document.getElementById('vocab-modal-list');
    const vocabBadgeCount = document.getElementById('vocab-badge-count');

    const btnToggleInputMode = document.getElementById('btn-toggle-input-mode');
    const labelToggleInput = document.getElementById('label-toggle-input');
    const formTextInput = document.getElementById('form-text-input');
    const inputTextInput = document.getElementById('input-text-chat');

    // 2. State Aplikasi
    let isRecording = false;
    let isProcessing = false;
    let isAiSpeaking = false;
    let playbackSpeed = 1.0;
    let handsFreeEnabled = true;

    let mediaRecorder = null;
    let audioChunks = [];
    let audioContext = null;
    let lastAudioEnergyTime = Date.now();

    const conversationHistory = [];
    const vocabularyList = [
        { word: 'Matur nuwun', meaning: 'Terima kasih', level: 'Krama' },
        { word: 'Pangapunten', meaning: 'Permohonan maaf', level: 'Krama Alus' }
    ];

    let currentAudioPlayer = null;
    let lastSpokenAudio = { base64: null, url: null };

    // 3. Interactive Spring Gaze Tracking
    let targetGazeX = 0, targetGazeY = 0;
    let currentGazeX = 0, currentGazeY = 0;

    window.addEventListener('mousemove', (e) => {
        if (!mascotSvg) return;
        const rect = mascotSvg.getBoundingClientRect();
        const centerX = rect.left + rect.width / 2;
        const centerY = rect.top + rect.height * 0.46;

        targetGazeX = Math.max(-10, Math.min(10, (e.clientX - centerX) * 0.08));
        targetGazeY = Math.max(-7, Math.min(7, (e.clientY - centerY) * 0.08));
    });

    function animateGaze() {
        currentGazeX += (targetGazeX - currentGazeX) * 0.25;
        currentGazeY += (targetGazeY - currentGazeY) * 0.25;

        if (eyeLeftPupil && eyeRightPupil) {
            eyeLeftPupil.setAttribute('cx', currentGazeX);
            eyeLeftPupil.setAttribute('cy', currentGazeY);
            eyeRightPupil.setAttribute('cx', currentGazeX);
            eyeRightPupil.setAttribute('cy', currentGazeY);

            eyeLeftShine.setAttribute('cx', currentGazeX - 2.5);
            eyeLeftShine.setAttribute('cy', currentGazeY - 3.5);
            eyeRightShine.setAttribute('cx', currentGazeX - 2.5);
            eyeRightShine.setAttribute('cy', currentGazeY - 3.5);
        }
        requestAnimationFrame(animateGaze);
    }
    requestAnimationFrame(animateGaze);

    // 4. Natural Eye Blinking Cycle (Saben 3.6s)
    setInterval(() => {
        if (isAiSpeaking) return;
        const eyes = document.getElementById('eyes-group');
        if (!eyes) return;
        eyes.style.transformOrigin = '70px 88px';
        eyes.style.transition = 'transform 0.12s ease-in-out';
        eyes.style.transform = 'scaleY(0.1)';
        setTimeout(() => {
            eyes.style.transform = 'scaleY(1)';
        }, 130);
    }, 3600);

    // 5. Mascot Mood Switcher
    function setMascotMood(mood) {
        eyeLeftPupil.classList.remove('hidden');
        eyeRightPupil.classList.remove('hidden');
        eyeLeftShine.classList.remove('hidden');
        eyeRightShine.classList.remove('hidden');
        eyeLeftHappy.classList.add('hidden');
        eyeRightHappy.classList.add('hidden');
        eyebrowsGroup.setAttribute('opacity', '0');

        mouthNeutral.classList.add('hidden');
        mouthHappy.classList.add('hidden');
        mouthTongue.classList.add('hidden');
        mouthTalking.classList.add('hidden');

        if (mood === 'happy' || mood === 'excited') {
            eyeLeftPupil.classList.add('hidden');
            eyeRightPupil.classList.add('hidden');
            eyeLeftShine.classList.add('hidden');
            eyeRightShine.classList.add('hidden');
            eyeLeftHappy.classList.remove('hidden');
            eyeRightHappy.classList.remove('hidden');
            mouthHappy.classList.remove('hidden');
            mouthTongue.classList.remove('hidden');
        } else if (mood === 'thinking') {
            eyebrowsGroup.setAttribute('opacity', '1');
            targetGazeX = 7;
            targetGazeY = -6;
            mouthNeutral.classList.remove('hidden');
        } else if (mood === 'speaking') {
            mouthTalking.classList.remove('hidden');
        } else {
            mouthNeutral.classList.remove('hidden');
        }
    }

    // 6. Dynamic Mouth Lip-Sync during Audio Playback
    let lipSyncRaf = null;
    function startLipSync() {
        setMascotMood('speaking');
        pulseRings.style.opacity = '1';

        function step(timestamp) {
            if (!isAiSpeaking) return;
            const progress = (Math.sin(timestamp / 75) + 1) / 2; // 0..1
            const h = 4 + progress * 14;
            const w = 12 + progress * 8;
            mouthTalking.setAttribute('height', h);
            mouthTalking.setAttribute('width', w);
            mouthTalking.setAttribute('x', -w / 2);
            mouthTalking.setAttribute('y', -h / 2);
            lipSyncRaf = requestAnimationFrame(step);
        }
        lipSyncRaf = requestAnimationFrame(step);
    }

    function stopLipSync() {
        if (lipSyncRaf) cancelAnimationFrame(lipSyncRaf);
        pulseRings.style.opacity = '0';
        mouthTalking.classList.add('hidden');
        setMascotMood('happy');
    }

    // 7. Streaming Subtitle Berjalan Bareng dengan Audio AI
    function streamSubtitleWithAudio(audio, fullText, onComplete) {
        aiSubtitleContainer.classList.remove('hidden');
        btnReplaySubtitle.classList.add('hidden');
        aiSubtitleCursor.classList.remove('hidden');
        aiSubtitleText.textContent = '';

        const words = fullText.trim().split(/\s+/);
        let isDone = false;

        function updateProgress() {
            if (isDone) return;

            const duration = audio.duration;
            const current = audio.currentTime;

            if (duration && duration > 0 && !isNaN(duration)) {
                const ratio = Math.min(1.0, current / duration);
                const count = Math.min(words.length, Math.floor(ratio * words.length) + 1);
                aiSubtitleText.textContent = words.slice(0, count).join(' ');
            } else {
                const cur = aiSubtitleText.textContent.trim().split(/\s+/).filter(Boolean).length;
                if (cur < words.length) {
                    aiSubtitleText.textContent = words.slice(0, cur + 1).join(' ');
                }
            }

            if (audio.ended || (duration && current >= duration - 0.05)) {
                isDone = true;
                aiSubtitleText.textContent = fullText;
                aiSubtitleCursor.classList.add('hidden');
                btnReplaySubtitle.classList.remove('hidden');
                if (onComplete) onComplete();
                return;
            }

            requestAnimationFrame(updateProgress);
        }

        requestAnimationFrame(updateProgress);

        audio.addEventListener('ended', () => {
            if (!isDone) {
                isDone = true;
                aiSubtitleText.textContent = fullText;
                aiSubtitleCursor.classList.add('hidden');
                btnReplaySubtitle.classList.remove('hidden');
                if (onComplete) onComplete();
            }
        }, { once: true });
    }

    // 8. Putar Audio AI (Ardi untuk Koreksi, Dimas untuk Pelafalan)
    function playAudio(audioBase64, audioUrl, fullText = null) {
        if (currentAudioPlayer) {
            currentAudioPlayer.pause();
            currentAudioPlayer = null;
        }

        lastSpokenAudio = { base64: audioBase64, url: audioUrl };

        let audioSrc = null;
        if (audioBase64 && audioBase64.length > 50) {
            audioSrc = 'data:audio/mpeg;base64,' + audioBase64;
        } else if (audioUrl) {
            audioSrc = audioUrl;
        }

        if (!audioSrc) {
            if (fullText) {
                aiSubtitleContainer.classList.remove('hidden');
                aiSubtitleText.textContent = fullText;
                aiSubtitleCursor.classList.add('hidden');
            }
            return;
        }

        const audio = new Audio(audioSrc);
        audio.playbackRate = playbackSpeed;
        currentAudioPlayer = audio;
        isAiSpeaking = true;
        statusText.textContent = 'Si Swara lagi matur...';
        mascotMicLabel.textContent = 'Si Swara lagi matur...';
        mascotMicIcon.textContent = 'volume_up';
        startLipSync();

        if (fullText) {
            streamSubtitleWithAudio(audio, fullText, () => onAiAudioComplete());
        } else {
            audio.onended = () => onAiAudioComplete();
        }

        audio.onerror = () => onAiAudioComplete();
        audio.play().catch(() => onAiAudioComplete());
    }

    function onAiAudioComplete() {
        isAiSpeaking = false;
        stopLipSync();
        statusText.textContent = 'Aktif · Tutor Wicara Basa Jawa';
        mascotMicLabel.textContent = 'Ketuk Si Swara kanggo wicara';
        mascotMicIcon.textContent = 'mic';

        // Jika handsfree aktif, otomatis nyalakan kembali mic setelah jeda 450ms
        if (handsFreeEnabled && !isRecording && !isProcessing) {
            setTimeout(() => {
                startRecording();
            }, 450);
        }
    }

    // 9. Perekaman Suara (Dipicu Tombol Maskot)
    // Sesuai permintaan: Saat mic diklik, subtitle sebelumnya langsung MENGHILANG!
    async function startRecording() {
        if (isRecording || isProcessing || isAiSpeaking) return;

        // Hilangkan subtitle sebelumnya ketika tombol mic diklik
        aiSubtitleContainer.classList.add('hidden');
        aiSubtitleText.textContent = '';
        if (currentAudioPlayer) {
            currentAudioPlayer.pause();
            currentAudioPlayer = null;
        }
        isAiSpeaking = false;
        stopLipSync();

        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            audioChunks = [];

            const mimeType = MediaRecorder.isTypeSupported('audio/webm;codecs=opus') ? 'audio/webm;codecs=opus' : '';
            mediaRecorder = mimeType ? new MediaRecorder(stream, { mimeType }) : new MediaRecorder(stream);

            mediaRecorder.ondataavailable = (e) => {
                if (e.data && e.data.size > 0) audioChunks.push(e.data);
            };

            mediaRecorder.onstop = async () => {
                stream.getTracks().forEach(t => t.stop());
                if (audioContext) {
                    audioContext.close().catch(() => {});
                    audioContext = null;
                }
                const audioBlob = new Blob(audioChunks, { type: mediaRecorder.mimeType || 'audio/webm' });
                await submitAudio(audioBlob);
            };

            mediaRecorder.start(100);
            isRecording = true;

            // Efek visual merekam pada maskot
            pulseRings.style.opacity = '1';
            pillRectBorder.setAttribute('stroke', '#EF4444');
            mascotMicBadge.classList.remove('bg-primary-50', 'text-primary-700', 'border-primary-200/80');
            mascotMicBadge.classList.add('bg-red-50', 'text-red-700', 'border-red-200');
            mascotMicIcon.textContent = 'stop_circle';
            mascotMicLabel.textContent = 'Mirengaken... (Ketuk kanggo mandheg)';
            statusText.textContent = 'Kula mirengaken...';
            setMascotMood('neutral');

            setupSilenceDetector(stream);

        } catch (err) {
            console.warn('Microphone error:', err);
            mascotMicLabel.textContent = 'Mic ora diidini. Gunakna form ketik.';
            showTextInputMode();
        }
    }

    function setupSilenceDetector(stream) {
        try {
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const source = audioContext.createMediaStreamSource(stream);
            const analyser = audioContext.createAnalyser();
            analyser.fftSize = 256;
            source.connect(analyser);

            const buffer = new Uint8Array(analyser.frequencyBinCount);
            lastAudioEnergyTime = Date.now();

            function checkAudioLevel() {
                if (!isRecording) return;
                analyser.getByteFrequencyData(buffer);
                let sum = 0;
                for (let i = 0; i < buffer.length; i++) sum += buffer[i];
                const avg = sum / buffer.length;

                if (avg > 14) {
                    lastAudioEnergyTime = Date.now();
                } else if (handsFreeEnabled && Date.now() - lastAudioEnergyTime > 550) {
                    // Hening 550ms -> auto-stop tetap jalan
                    stopRecording();
                    return;
                }
                requestAnimationFrame(checkAudioLevel);
            }
            requestAnimationFrame(checkAudioLevel);
        } catch (_) {}
    }

    function stopRecording() {
        if (!isRecording || !mediaRecorder) return;
        isRecording = false;

        // Reset visual maskot
        pulseRings.style.opacity = '0';
        pillRectBorder.setAttribute('stroke', '#A79BFF');
        mascotMicBadge.classList.remove('bg-red-50', 'text-red-700', 'border-red-200');
        mascotMicBadge.classList.add('bg-primary-50', 'text-primary-700', 'border-primary-200/80');
        mascotMicIcon.textContent = 'hourglass_empty';
        mascotMicLabel.textContent = 'Si Swara lagi mikir wangsulan...';
        statusText.textContent = 'Mikir rumiyin...';
        setMascotMood('thinking');

        try {
            mediaRecorder.stop();
        } catch (_) {}
    }

    // 10. Kirim Audio / Teks ke Backend Voice Tutor
    async function submitAudio(audioBlob, mockTranscript = null) {
        isProcessing = true;
        setMascotMood('thinking');

        const formData = new FormData();
        if (audioBlob) {
            formData.append('audio', audioBlob, 'recording.webm');
        }
        if (mockTranscript) {
            formData.append('mock_transcript', mockTranscript);
        }

        const historySlice = conversationHistory.slice(-4);
        historySlice.forEach((h, idx) => {
            formData.append(`history[${idx}][role]`, h.role);
            formData.append(`history[${idx}][content]`, h.content);
        });

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch("{{ route('kuis.voice-tutor.interact') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json',
                },
                body: formData,
            });

            if (!res.ok) throw new Error('Server status ' + res.status);
            const data = await res.json();

            if (data.transkripsi_siswa) {
                conversationHistory.push({ role: 'user', content: data.transkripsi_siswa });
            }

            if (data.balasan_teks) {
                conversationHistory.push({ role: 'assistant', content: data.balasan_teks });
            }

            // Perbarui kartu tantangan tirukan lafal jika ada
            if (data.suggested_repeat) {
                repeatChallengeText.textContent = data.suggested_repeat;
                repeatChallengeCard.classList.remove('hidden');
            } else {
                repeatChallengeCard.classList.add('hidden');
            }

            // Perbarui kosakata sesi
            if (Array.isArray(data.vocabulary)) {
                data.vocabulary.forEach(v => {
                    if (!vocabularyList.some(item => item.word.toLowerCase() === v.word.toLowerCase())) {
                        vocabularyList.push(v);
                    }
                });
                renderVocabularyList();
            }

            isProcessing = false;
            setMascotMood(data.expression || 'happy');

            // Putar audio dan tampilkan subtitle yang mengalir bersamaan di container AI
            playAudio(data.audio_base64, data.audio_url, data.balasan_teks);

        } catch (err) {
            console.error('Voice tutor interact error:', err);
            isProcessing = false;
            setMascotMood('neutral');
            mascotMicLabel.textContent = 'Gagal ngirim audio. Mangga cobi malih.';
            statusText.textContent = 'Aktif · Tutor Wicara Basa Jawa';
        }
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // 11. Render Rekap Kosakata Modal
    function renderVocabularyList() {
        vocabBadgeCount.textContent = vocabularyList.length;
        vocabModalList.innerHTML = '';
        vocabularyList.forEach(item => {
            const row = document.createElement('div');
            row.className = 'p-3 rounded-2xl bg-surface-container-low border border-primary-100/60 flex items-center justify-between';
            row.innerHTML = `
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-heading text-sm font-bold text-black-900">${escapeHtml(item.word)}</span>
                        <span class="px-2 py-0.5 rounded-md bg-primary-100 text-primary-800 text-[10px] font-bold">${escapeHtml(item.level || 'Krama')}</span>
                    </div>
                    <span class="font-body text-xs text-gray-600">${escapeHtml(item.meaning)}</span>
                </div>
            `;
            vocabModalList.appendChild(row);
        });
    }

    // 12. Event Listeners Interaktif
    // Maskot Oval Interaktif berfungsi sebagai Tombol Mic Utama
    mascotBtn.addEventListener('click', () => {
        if (isRecording) {
            stopRecording();
        } else if (!isProcessing && !isAiSpeaking) {
            startRecording();
        }
    });

    mascotBtn.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            mascotBtn.click();
        }
    });

    // Replay subtitle audio
    btnReplaySubtitle.addEventListener('click', () => {
        if (lastSpokenAudio.base64 || lastSpokenAudio.url) {
            playAudio(lastSpokenAudio.base64, lastSpokenAudio.url, aiSubtitleText.textContent);
        }
    });

    toggleHandsfree.addEventListener('change', (e) => {
        handsFreeEnabled = e.target.checked;
    });

    btnSpeedToggle.addEventListener('click', () => {
        playbackSpeed = playbackSpeed === 1.0 ? 0.8 : 1.0;
        speedLabel.textContent = playbackSpeed.toFixed(1) + 'x';
        if (currentAudioPlayer) currentAudioPlayer.playbackRate = playbackSpeed;
    });

    // Tombol Putar Pelan Javanese Target
    btnSlowPlay.addEventListener('click', () => {
        const text = repeatChallengeText.textContent;
        // Panggil TTS khusus kalimat Jawa pelan
        submitAudio(null, text);
    });

    // Modal Kosakata
    btnOpenVocab.addEventListener('click', () => modalVocab.classList.remove('hidden'));
    btnCloseVocab.addEventListener('click', () => modalVocab.classList.add('hidden'));
    btnDismissVocab.addEventListener('click', () => modalVocab.classList.add('hidden'));

    // Input Teks Alternatif
    function showTextInputMode() {
        formTextInput.classList.remove('hidden');
        labelToggleInput.textContent = 'Gunakan Mikrofon';
    }
    btnToggleInputMode.addEventListener('click', () => {
        if (formTextInput.classList.contains('hidden')) {
            showTextInputMode();
        } else {
            formTextInput.classList.add('hidden');
            labelToggleInput.textContent = 'Ketik Pertanyaan';
        }
    });

    formTextInput.addEventListener('submit', (e) => {
        e.preventDefault();
        const val = inputTextInput.value.trim();
        if (!val) return;
        inputTextInput.value = '';
        submitAudio(null, val);
    });

})();
</script>
@endpush
