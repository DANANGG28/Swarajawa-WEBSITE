{{-- Efek suara klik untuk sisi siswa (nuansa gamifikasi) --}}
<script id="siswa-click-sound">
(function () {
    'use strict';

    var AudioCtx = window.AudioContext || window.webkitAudioContext;
    if (!AudioCtx) { return; }

    var ctx = null;
    var enabled = true;
    var selector = 'button, a[href], [role="button"], input[type="submit"], input[type="button"], input[type="reset"], summary';

    function playClick() {
        if (!enabled) { return; }

        if (!ctx) {
            try { ctx = new AudioCtx(); } catch (e) { ctx = null; }
        }
        if (!ctx) { return; }
        if (ctx.state === 'suspended') { ctx.resume().catch(function () {}); }

        var now = ctx.currentTime;
        var osc = ctx.createOscillator();
        var gain = ctx.createGain();

        osc.type = 'triangle';
        osc.frequency.setValueAtTime(760, now);
        osc.frequency.exponentialRampToValueAtTime(480, now + 0.09);

        gain.gain.setValueAtTime(0.0001, now);
        gain.gain.exponentialRampToValueAtTime(0.16, now + 0.008);
        gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.13);

        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start(now);
        osc.stop(now + 0.15);
    }

    function findTrigger(target) {
        if (!target || typeof target.closest !== 'function') { return null; }

        var el = target.closest(selector);
        if (!el) { return null; }
        if (el.disabled) { return null; }
        if (el.getAttribute('aria-disabled') === 'true') { return null; }
        if (el.hasAttribute('data-no-click-sound')) { return null; }

        return el;
    }

    document.addEventListener('pointerdown', function (event) {
        if (event.pointerType === 'mouse' && event.button !== 0) { return; }
        if (findTrigger(event.target)) { playClick(); }
    }, { passive: true, capture: true });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Enter' && event.key !== ' ' && event.key !== 'Spacebar') { return; }
        if (findTrigger(event.target)) { playClick(); }
    }, true);

    window.SiswaSound = {
        play: playClick,
        setEnabled: function (value) { enabled = !!value; },
        isEnabled: function () { return enabled; }
    };
})();
</script>
