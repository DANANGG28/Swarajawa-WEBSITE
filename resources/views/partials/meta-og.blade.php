@php
    // Default meta preview untuk tautan yang dibagikan (WhatsApp, Telegram, X, dll).
    $siteName = config('app.name', 'SINAU APP');
    $ogJudul = $ogTitle ?? (isset($judul) && $judul ? $judul.' - '.$siteName : $siteName.' - Belajar Bahasa Jawa dengan Menyenangkan');
    $ogDeskripsi = $ogDescription ?? 'Platform belajar Bahasa Jawa modern dengan pendekatan gamifikasi interaktif. Melestarikan budaya luhur dengan cara yang menyenangkan.';
    $ogGambar = $ogImage ?? asset('img/og-sinau.jpg');
    $ogTautan = $ogUrl ?? url()->current();
@endphp
<meta name="description" content="{{ $ogDeskripsi }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $ogJudul }}">
<meta property="og:description" content="{{ $ogDeskripsi }}">
<meta property="og:url" content="{{ $ogTautan }}">
<meta property="og:image" content="{{ $ogGambar }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="{{ $siteName }} — belajar Bahasa Jawa">
<meta property="og:locale" content="id_ID">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $ogJudul }}">
<meta name="twitter:description" content="{{ $ogDeskripsi }}">
<meta name="twitter:image" content="{{ $ogGambar }}">
