<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $judul }} - Sinau Jowo</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css2?family=Epilogue:ital,wght@0,400..800;1,400..800&family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
@vite('resources/js/aksara-tracing-canvas.js')
<script src="https://cdn.tailwindcss.com"></script>
<script id="tailwind-config">
tailwind.config = {
    darkMode: "class",
    theme: {
      extend: {
        "colors": {
          "surface-container-low": "#f5f2ff",
          "yellow-300": "#F6D98B",
          "secondary-fixed-dim": "#ffb2bf",
          "primary-fixed-dim": "#c6bfff",
          "tertiary-fixed": "#ffdcc4",
          "primary-700": "#5443C9",
          "surface-bright": "#fcf8ff",
          "surface-container": "#efecfd",
          "on-error-container": "#93000a",
          "surface-variant": "#e3e0f1",
          "primary-fixed": "#e4dfff",
          "green-500": "#4CAF6D",
          "orange-300": "#F7B98A",
          "on-secondary-container": "#792d40",
          "black-900": "#1E1E2A",
          "on-surface-variant": "#474554",
          "on-primary": "#ffffff",
          "on-tertiary": "#ffffff",
          "secondary": "#964356",
          "surface-container-lowest": "#ffffff",
          "on-primary-fixed": "#160066",
          "gray-50": "#F7F7FA",
          "inverse-on-surface": "#f2efff",
          "surface-container-high": "#e9e6f7",
          "primary-600": "#6C5CE8",
          "primary-500": "#7B6CF0",
          "surface-dim": "#dbd8e9",
          "on-tertiary-container": "#ffc59b",
          "tertiary-fixed-dim": "#f8ba8b",
          "primary-container": "#5443c9",
          "on-secondary-fixed": "#3f0016",
          "gray-500": "#8A8A9A",
          "primary": "#3c25b1",
          "on-primary-fixed-variant": "#402cb5",
          "outline": "#787585",
          "pink-100": "#FBD9DE",
          "background": "#fcf8ff",
          "orange-500": "#F0955A",
          "gray-200": "#E4E4EC",
          "inverse-surface": "#2f2f3c",
          "inverse-primary": "#c6bfff",
          "on-background": "#1a1a26",
          "on-secondary": "#ffffff",
          "error": "#ba1a1a",
          "primary-400": "#A79BFF",
          "pink-500": "#F08CA0",
          "surface": "#fcf8ff",
          "outline-variant": "#c8c4d6",
          "error-container": "#ffdad6",
          "on-surface": "#1a1a26",
          "color-white": "#FFFFFF",
          "on-primary-container": "#d2cbff",
          "tertiary-container": "#7d4f29",
          "on-tertiary-fixed": "#2f1500",
          "on-secondary-fixed-variant": "#792c3f",
          "tertiary": "#623814",
          "on-error": "#ffffff",
          "on-tertiary-fixed-variant": "#673d18",
          "surface-container-highest": "#e3e0f1",
          "secondary-fixed": "#ffd9de",
          "surface-tint": "#5948ce",
          "secondary-container": "#ff98ac"
        },
        "borderRadius": {
          "DEFAULT": "0.25rem",
          "lg": "0.5rem",
          "xl": "0.75rem",
          "full": "9999px"
        },
        "spacing": {
          "margin": "1.25rem",
          "space-xl": "1.75rem",
          "space-md": "1rem",
          "space-sm": "0.5rem",
          "gutter": "0.75rem",
          "gutter-desktop": "1.5rem",
          "margin-desktop": "2.5rem",
          "space-lg": "1.25rem",
          "space-xs": "0.25rem"
        },
        "fontFamily": {
          "caption": ["Manrope"],
          "heading": ["Epilogue"],
          "stat-number-sm": ["Epilogue"],
          "body": ["Manrope"],
          "display": ["Epilogue"],
          "display-mobile": ["Epilogue"],
          "label-upper": ["Manrope"],
          "stat-number": ["Epilogue"]
        }
      }
    }
};
</script>
<style>
    @font-face {
        font-family: 'Noto Sans Javanese';
        src: url('{{ asset('fonts/NotoSansJavanese.ttf') }}') format('truetype');
        font-weight: 400 700;
        font-style: normal;
        font-display: swap;
    }
    @layer base {
        html, body { margin: 0; padding: 0; }
        body { overscroll-behavior: none; }
    }
    ::-webkit-scrollbar { display: none; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24; }
    .icon-fill { font-variation-settings: 'FILL' 1, 'wght' 600, 'GRAD' 0, 'opsz' 24; }
    .font-javanese { font-family: 'Noto Sans Javanese', 'Manrope', sans-serif; }
    @keyframes wave-pulse { 0%,100% { height: 8px; } 50% { height: 40px; } }
    .wave-bar { animation: wave-pulse 1.2s ease-in-out infinite; }
    .wave-bar:nth-child(2){animation-delay:.15s} .wave-bar:nth-child(3){animation-delay:.3s}
    .wave-bar:nth-child(4){animation-delay:.45s} .wave-bar:nth-child(5){animation-delay:.2s}
    .wave-bar:nth-child(6){animation-delay:.5s} .wave-bar:nth-child(7){animation-delay:.35s}
    .wave-bar:nth-child(8){animation-delay:.1s} .wave-bar:nth-child(9){animation-delay:.4s}
    @keyframes pulse-ring { 0%{transform:scale(.95);opacity:.8} 50%{transform:scale(1.15);opacity:.3} 100%{transform:scale(.95);opacity:.8} }
    .mic-pulse-ring { animation: pulse-ring 2s cubic-bezier(.4,0,.6,1) infinite; }
    .tactile-chip { box-shadow: 0 2px 0 #d9d6e8; transition: all .15s ease-in-out; }
    .tactile-chip:active { transform: translateY(2px); box-shadow: 0 0 0 #d9d6e8; }
</style>
