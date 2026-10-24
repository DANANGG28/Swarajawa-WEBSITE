@extends('layouts.kuis-wicara', ['judul' => 'Voice Tutor Basa Jawa — Si Swara'])

@section('konten')
<div class="w-full max-w-2xl mx-auto flex flex-col gap-4 relative" id="voice-tutor-container">
    <!-- Header Panggung Voice Tutor -->
    <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-gray-200 shadow-sm flex items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <span class="relative flex h-3 w-3">
                <span id="live-indicator-ping" class="hidden absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span id="live-indicator-dot" class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            </span>
            <div class="flex flex-col">
                <span class="font-heading text-xs sm:text-sm font-bold text-black-900 leading-tight">Si Swara</span>
                <span id="status-text" class="font-body text-[11px] sm:text-xs text-gray-500 font-medium">Aktif · Kanca Sinau Basa Jawa</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Toggle Kecepatan Audio (1.0x / 0.8x) -->
            <button id="btn-speed-toggle" type="button" aria-label="Ubah kecepatan suara"
                class="inline-flex items-center px-2.5 py-1 rounded-xl bg-gray-100 hover:bg-gray-200 text-primary-700 font-heading text-xs font-bold transition-colors">
                <span id="speed-label">1.0x</span>
            </button>

            <!-- Tombol Kosakata Sesi -->
            <button id="btn-open-vocab" type="button" aria-label="Buka rekap kosakata"
                class="inline-flex items-center gap-1 px-3 py-1 rounded-xl bg-primary-50 hover:bg-primary-100 text-primary-700 font-heading text-xs font-bold transition-colors border border-primary-200/60">
                <span class="material-symbols-outlined text-[16px]">auto_stories</span>
                <span class="hidden sm:inline">Kosakata</span>
                <span id="vocab-badge-count" class="ml-0.5 px-1.5 py-0.2 rounded-full bg-primary-600 text-white text-[10px] font-bold">2</span>
            </button>
        </div>
    </div>

    <!-- Center Stage: Maskot Kapsul Si Swara (SVG Interaktif) -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-gray-200 shadow-sm flex flex-col items-center justify-center relative overflow-hidden select-none">
        <div class="relative w-[180px] h-[210px] flex items-center justify-center">
            <!-- Cincin Gelombang Audio Konsentris di Belakang Kapsul -->
            <svg id="pulse-rings" class="absolute inset-0 w-full h-full pointer-events-none transition-opacity duration-300 opacity-0" viewBox="0 0 180 210">
                <circle id="pulse-r1" cx="90" cy="105" r="62" fill="none" stroke="#A79BFF" stroke-width="2.5" opacity="0.4" />
                <circle id="pulse-r2" cx="90" cy="105" r="74" fill="none" stroke="#7B6CF0" stroke-width="2" opacity="0.25" />
                <circle id="pulse-r3" cx="90" cy="105" r="86" fill="none" stroke="#6C5CE8" stroke-width="1.5" opacity="0.15" />
            </svg>

            <!-- Maskot Kapsul Vektor "Si Swara" -->
            <svg id="mascot-svg" class="relative z-10 w-[140px] h-[190px] drop-shadow-md cursor-pointer transition-transform duration-200" viewBox="0 0 140 190">
                <defs>
                    <linearGradient id="pill-body-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#FFFFFF" />
                        <stop offset="60%" stop-color="#F5F2FF" />
                        <stop offset="100%" stop-color="#E9E2FE" />
                    </linearGradient>
                </defs>

                <!-- Badan Kapsul Utama -->
                <rect x="10" y="10" width="120" height="168" rx="60" fill="url(#pill-body-grad)" stroke="#A79BFF" stroke-width="2.4" />

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

        <!-- Kartu Tantangan Tirukan Lafal (Repeat Challenge Card) -->
        <div id="repeat-challenge-card" class="w-full mt-3 p-3.5 sm:p-4 rounded-2xl bg-surface-container-low border border-primary-200/70 shadow-sm flex items-center justify-between gap-3 transition-all duration-200">
            <div class="flex items-start gap-3 min-w-0">
                <div class="w-8 h-8 rounded-xl bg-primary-100 text-primary-700 flex items-center justify-center shrink-0 mt-0.5">
                    <span class="material-symbols-outlined text-[18px]">record_voice_over</span>
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="font-body text-[11px] font-semibold text-gray-500">Coba tirukna ukara iki:</span>
                    <span id="repeat-challenge-text" class="font-heading text-sm sm:text-base font-bold text-primary-800 truncate">Sugeng enjang, Mas Swara</span>
                </div>
            </div>
            <button id="btn-slow-play" type="button" title="Rungokna alon (0.8x)"
                class="shrink-0 p-2 rounded-xl bg-white hover:bg-primary-50 text-primary-700 border border-primary-200 shadow-sm active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[18px]">slow_motion_video</span>
            </button>
        </div>
    </div>

    <!-- Area Transkrip Percakapan -->
    <div class="bg-white rounded-3xl p-4 sm:p-5 border border-gray-200 shadow-sm flex flex-col gap-3 min-h-[200px] max-h-[300px] overflow-y-auto" id="transcript-list">
        <!-- Balasan Awal Tutor -->
        <div class="flex items-start gap-2.5 max-w-[85%]">
            <div class="w-7 h-7 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center shrink-0 font-bold text-xs mt-0.5">
                S
            </div>
            <div class="p-3 rounded-2xl rounded-tl-sm bg-gray-50 border border-gray-200 text-black-900 font-body text-xs sm:text-sm leading-relaxed flex items-start gap-2 shadow-2xs">
                <span class="flex-1">Sugeng rawuh! Kula Si Swara, kanca sinau Basa Jawa panjenengan. Mangga wicara Basa Jawa utawa takon babagan unggah-ungguh!</span>
                <button type="button" class="btn-replay-audio shrink-0 text-primary-600 hover:text-primary-800 p-0.5" title="Putar ulang">
                    <span class="material-symbols-outlined text-[16px]">volume_up</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Dock Kontrol Audio & Rekam Ala GoPrep -->
    <div class="bg-white rounded-3xl p-4 sm:p-5 border border-gray-200 shadow-sm flex flex-col gap-3">
        <!-- Toggle Hands-Free & Mode Input -->
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                <input id="toggle-handsfree" type="checkbox" checked class="sr-only peer">
                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary-600"></div>
                <span class="font-body text-xs font-semibold text-gray-700">Mode Hands-Free (Otomatis)</span>
            </label>

            <button id="btn-toggle-input-mode" type="button"
                class="font-body text-xs font-bold text-primary-700 hover:text-primary-800 flex items-center gap-1">
                <span class="material-symbols-outlined text-[15px]">keyboard</span>
                <span id="label-toggle-input">Ketik Pertanyaan</span>
            </button>
        </div>

        <!-- Tombol Utama Mic (Diameter 76px) -->
        <div id="mic-action-container" class="flex flex-col items-center justify-center gap-2 pt-1 pb-1">
            <div class="relative flex items-center justify-center">
                <!-- Glowing Ripple Ring when recording -->
                <div id="mic-pulse-ring" class="hidden absolute w-24 h-24 rounded-full bg-red-400/30 animate-ping"></div>

                <button id="btn-main-mic" type="button" aria-label="Mulai bicara"
                    class="relative z-10 w-18 h-18 sm:w-20 sm:h-20 rounded-full bg-gradient-to-br from-primary-600 to-primary-800 hover:from-primary-700 hover:to-primary-900 text-white flex items-center justify-center shadow-lg shadow-primary-600/30 active:scale-95 transition-all">
                    <span id="mic-main-icon" class="material-symbols-outlined text-[32px] sm:text-[36px]">mic</span>
                </button>
            </div>
            <p id="mic-status-label" class="font-heading text-xs sm:text-sm font-bold text-black-900 text-center mt-1">
                Ketuk mic kanggo wicara
            </p>
        </div>

        <!-- Input Bar Teks Alternatif (Muncul jika tombol ketik ditekan / mic tidak tersedia) -->
        <form id="form-text-input" class="hidden flex items-center gap-2 pt-1">
            <input id="input-text-chat" type="text" placeholder="Ketik pitakon utawa ukara Basa Jawa ing kene..."
                class="flex-1 bg-surface-container-low border border-gray-200 rounded-2xl px-4 py-2.5 font-body text-xs sm:text-sm text-black-900 focus:outline-none focus:border-primary-500">
            <button type="submit"
                class="px-4 py-2.5 rounded-2xl bg-primary-600 hover:bg-primary-700 text-white font-heading text-xs sm:text-sm font-bold shadow-sm transition-all active:scale-95 flex items-center gap-1">
                <span>Kirim</span>
                <span class="material-symbols-outlined text-[16px]">send</span>
            </button>
        </form>
    </div>
</div>

<!-- Modal Rekap Kosakata Sesi (Session Feedback ala GoPrep) -->
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
                        <span class="font-heading text-sm font-bold text-black-900">Sugeng rawuh</span>
                        <span class="px-2 py-0.5 rounded-md bg-primary-100 text-primary-800 text-[10px] font-bold">Krama</span>
                    </div>
                    <span class="font-body text-xs text-gray-600">Selamat datang</span>
                </div>
            </div>
            <div class="p-3 rounded-2xl bg-surface-container-low border border-primary-100/60 flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-heading text-sm font-bold text-black-900">Kanca</span>
                        <span class="px-2 py-0.5 rounded-md bg-primary-100 text-primary-800 text-[10px] font-bold">Ngoko</span>
                    </div>
                    <span class="font-body text-xs text-gray-600">Teman / Sahabat</span>
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
    // 1. Elemen Panggung & Kontrol
    const mascotSvg = document.getElementById('mascot-svg');
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

    const statusText = document.getElementById('status-text');
    const btnMainMic = document.getElementById('btn-main-mic');
    const micMainIcon = document.getElementById('mic-main-icon');
    const micPulseRing = document.getElementById('mic-pulse-ring');
    const micStatusLabel = document.getElementById('mic-status-label');
    const toggleHandsfree = document.getElementById('toggle-handsfree');
    const transcriptList = document.getElementById('transcript-list');
    const repeatChallengeCard = document.getElementById('repeat-challenge-card');
    const repeatChallengeText = document.getElementById('repeat-challenge-text');
    const btnSlowPlay = document.getElementById('btn-slow-play');

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
    let silenceTimer = null;
    let lastAudioEnergyTime = Date.now();

    const conversationHistory = [];
    const vocabularyList = [
        { word: 'Sugeng rawuh', meaning: 'Selamat datang', level: 'Krama' },
        { word: 'Kanca', meaning: 'Teman / Sahabat', level: 'Ngoko' }
    ];

    let currentAudioPlayer = null;

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
        // Reset element states
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
            // neutral
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

    // 7. Audio Playback (Direct Base64 Zero-Roundtrip)
    function playAudio(audioBase64, audioUrl, onComplete) {
        if (currentAudioPlayer) {
            currentAudioPlayer.pause();
            currentAudioPlayer = null;
        }

        let audioSrc = null;
        if (audioBase64 && audioBase64.length > 50) {
            audioSrc = 'data:audio/mpeg;base64,' + audioBase64;
        } else if (audioUrl) {
            audioSrc = audioUrl;
        }

        if (!audioSrc) {
            if (onComplete) onComplete();
            return;
        }

        const audio = new Audio(audioSrc);
        audio.playbackRate = playbackSpeed;
        currentAudioPlayer = audio;
        isAiSpeaking = true;
        statusText.textContent = 'Si Swara lagi matur...';
        startLipSync();

        audio.onended = () => {
            isAiSpeaking = false;
            stopLipSync();
            statusText.textContent = 'Aktif · Kanca Sinau Basa Jawa';
            if (onComplete) onComplete();

            // Jika handsfree aktif, otomatis aktifkan mic untuk giliran siswa
            if (handsFreeEnabled && !isRecording && !isProcessing) {
                setTimeout(() => {
                    startRecording();
                }, 400);
            }
        };

        audio.onerror = () => {
            isAiSpeaking = false;
            stopLipSync();
            statusText.textContent = 'Aktif · Kanca Sinau Basa Jawa';
            if (onComplete) onComplete();
        };

        audio.play().catch(() => {
            isAiSpeaking = false;
            stopLipSync();
        });
    }

    // 8. Perekaman Suara (MediaRecorder + Fast Silence VAD 550ms)
    async function startRecording() {
        if (isRecording || isProcessing || isAiSpeaking) return;

        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            audioChunks = [];

            // Setup MediaRecorder
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
            pulseRings.style.opacity = '1';
            micPulseRing.classList.remove('hidden');
            btnMainMic.classList.remove('from-primary-600', 'to-primary-800');
            btnMainMic.classList.add('from-red-500', 'to-red-700');
            micMainIcon.textContent = 'stop';
            micStatusLabel.textContent = 'Ngrungokake swara panjenengan...';
            statusText.textContent = 'Kula mirengaken...';
            setMascotMood('neutral');

            // Setup VAD hening 550ms nganggo Web Audio API
            setupSilenceDetector(stream);

        } catch (err) {
            console.warn('Microphone access denied:', err);
            micStatusLabel.textContent = 'Mic ora diidini. Mangga nggunakake form ketik ing ngisor.';
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

                // Threshold suara
                if (avg > 15) {
                    lastAudioEnergyTime = Date.now();
                } else if (handsFreeEnabled && Date.now() - lastAudioEnergyTime > 600) {
                    // Hening lebih dari 600ms -> auto stop
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
        pulseRings.style.opacity = '0';
        micPulseRing.classList.add('hidden');
        btnMainMic.classList.remove('from-red-500', 'to-red-700');
        btnMainMic.classList.add('from-primary-600', 'to-primary-800');
        micMainIcon.textContent = 'mic';
        micStatusLabel.textContent = 'Si Swara lagi mikir wangsulan...';
        statusText.textContent = 'Mikir rumiyin...';
        setMascotMood('thinking');

        try {
            mediaRecorder.stop();
        } catch (_) {}
    }

    // 9. Kirim Audio / Teks dhateng Backend Voice Tutor
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

        // Riwayat 4 obrolan pungkasan
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

            if (!res.ok) throw new Error('Server returned ' + res.status);
            const data = await res.json();

            // Tambah dialog siswa
            if (data.transkripsi_siswa) {
                appendChatBubble('siswa', data.transkripsi_siswa);
                conversationHistory.push({ role: 'user', content: data.transkripsi_siswa });
            }

            // Tambah balasan tutor
            if (data.balasan_teks) {
                appendChatBubble('tutor', data.balasan_teks, data.audio_base64, data.audio_url);
                conversationHistory.push({ role: 'assistant', content: data.balasan_teks });
            }

            // Update kartu tantangan tirukan lafal
            if (data.suggested_repeat) {
                repeatChallengeText.textContent = data.suggested_repeat;
                repeatChallengeCard.classList.remove('hidden');
            }

            // Update kosakata sesi
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
            micStatusLabel.textContent = 'Ketuk mic kanggo wicara';

            // Putar audio langsung dari memory base64
            playAudio(data.audio_base64, data.audio_url);

        } catch (err) {
            console.error('Voice tutor request failed:', err);
            isProcessing = false;
            setMascotMood('neutral');
            micStatusLabel.textContent = 'Gagal ngirim audio. Mangga cobi malih.';
            statusText.textContent = 'Aktif · Kanca Sinau Basa Jawa';
        }
    }

    // 10. Tambah Gelembung Obrolan ing Layar
    function appendChatBubble(sender, text, audioBase64 = null, audioUrl = null) {
        const bubble = document.createElement('div');
        if (sender === 'siswa') {
            bubble.className = 'flex items-start justify-end gap-2 max-w-[85%] self-end';
            bubble.innerHTML = `
                <div class="p-3 rounded-2xl rounded-tr-sm bg-primary-700 text-white font-body text-xs sm:text-sm leading-relaxed shadow-sm">
                    ${escapeHtml(text)}
                </div>
            `;
        } else {
            bubble.className = 'flex items-start gap-2.5 max-w-[85%]';
            bubble.innerHTML = `
                <div class="w-7 h-7 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center shrink-0 font-bold text-xs mt-0.5">
                    S
                </div>
                <div class="p-3 rounded-2xl rounded-tl-sm bg-gray-50 border border-gray-200 text-black-900 font-body text-xs sm:text-sm leading-relaxed flex items-start gap-2 shadow-2xs">
                    <span class="flex-1">${escapeHtml(text)}</span>
                    <button type="button" class="btn-replay-item shrink-0 text-primary-600 hover:text-primary-800 p-0.5" title="Putar ulang">
                        <span class="material-symbols-outlined text-[16px]">volume_up</span>
                    </button>
                </div>
            `;
            const btnReplay = bubble.querySelector('.btn-replay-item');
            if (btnReplay) {
                btnReplay.addEventListener('click', () => {
                    playAudio(audioBase64, audioUrl);
                });
            }
        }
        transcriptList.appendChild(bubble);
        transcriptList.scrollTop = transcriptList.scrollHeight;
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

    // 12. Event Listeners
    btnMainMic.addEventListener('click', () => {
        if (isRecording) {
            stopRecording();
        } else {
            startRecording();
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

    btnSlowPlay.addEventListener('click', () => {
        const text = repeatChallengeText.textContent;
        submitAudio(null, text);
    });

    // Replay initial greeting
    document.querySelectorAll('.btn-replay-audio').forEach(btn => {
        btn.addEventListener('click', () => {
            submitAudio(null, 'Sugeng enjang sedaya');
        });
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
