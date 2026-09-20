<?php

namespace App\Services\Aksara;

/**
 * Konversi teks Latin → Aksara Jawa (Carakan) secara murni di PHP.
 *
 * Tidak memanggil microservice/HTTP apa pun (lihat tugas fitur aksara §1).
 * Karakter Unicode Javanese mengacu pada blok U+A980..U+A9DF (Unicode 5.2).
 *
 * Mode (bisa dioverride per instance lewat withModes()):
 * - ketikPepetMode=false : huruf `x` diterjemahkan sebagai gugus konsonan "ks".
 *                          Ketika true, `x` jadi penanda pêpêt (ꦼ), mis. `sxga` → ꦱꦼꦒ.
 *                          Karakter `ê`/`ě`/`è` selalu jadi pêpêt.
 * - ignoreSpace=false     : spasi dipertahankan. Jika true, spasi dibuang.
 * - aksaraSwaraMode=true  : vokal awal kata memakai aksara swara (ꦄ ꦆ ꦈ ꦌ ꦎ).
 *                          Jika false, memakai aksara "ha" + sandhangan (ꦲ…).
 *
 * Catatan: ejaan Latin bahasa Jawa ambigu (mis. e = taling vs pêpêt), karena
 * itu manusia (guru) tetap menjadi verifikator akhir lewat live preview.
 *
 * Extension point sengaja dikosongkan (lihat tugas §8). Jangan diisi dengan
 * tebakan; verifikasi visual dulu sebelum menambah pasangan Latin → Unicode.
 */
class AksaraJawaConverter
{
    // --- Tanda & sandhangan panyigeg ---
    private const SIGN_CECAK = "\u{A981}";   // -ng

    private const SIGN_LAYAR = "\u{A982}";   // -r

    private const SIGN_WIGNYAN = "\u{A983}"; // -h

    // --- Aksara swara (vokal mandiri) ---
    private const LETTER_A = "\u{A984}";

    private const LETTER_I = "\u{A986}";

    private const LETTER_U = "\u{A988}";

    private const LETTER_E = "\u{A98C}";

    private const LETTER_O = "\u{A98E}";

    // --- Sandhangan swara ---
    private const VOWEL_TARUNG = "\u{A9B4}";

    private const VOWEL_WULU = "\u{A9B6}";

    private const VOWEL_SUKU = "\u{A9B8}";

    private const VOWEL_TALING = "\u{A9BA}";

    private const VOWEL_PEPET = "\u{A9BC}";

    // --- Pangkon / virama ---
    private const PANGKON = "\u{A9C0}";

    // --- Tanda baca ---
    private const PADA_LINGSA = "\u{A9C8}";

    private const PADA_LUNGSI = "\u{A9C9}";

    private const PADA_PANGKAT = "\u{A9C7}";

    /**
     * Extension point (sengaja kosong — tugas §8).
     *
     * @var array<string, string>
     */
    public const DIPHTHONGS = [];

    /**
     * Extension point (sengaja kosong — tugas §8).
     *
     * @var array<string, string>
     */
    public const MURDA = [];

    /**
     * Extension point (sengaja kosong — tugas §8).
     *
     * @var array<string, string>
     */
    public const SPECIAL_LATIN = [];

    /**
     * Peta konsonan Latin → aksara nglegena.
     *
     * @var array<string, string>
     */
    private const CONSONANTS = [
        'h' => "\u{A9B2}",  // ha
        'n' => "\u{A9A4}",  // na
        'c' => "\u{A995}",  // ca
        'r' => "\u{A9AB}",  // ra
        'k' => "\u{A98F}",  // ka
        'd' => "\u{A9A2}",  // da
        't' => "\u{A9A0}",  // ta
        's' => "\u{A9B1}",  // sa
        'w' => "\u{A9AE}",  // wa
        'l' => "\u{A9AD}",  // la
        'p' => "\u{A9A5}",  // pa
        'j' => "\u{A997}",  // ja
        'y' => "\u{A9AA}",  // ya
        'm' => "\u{A9A9}",  // ma
        'g' => "\u{A992}",  // ga
        'b' => "\u{A9A7}",  // ba
        'ng' => "\u{A994}", // nga
        'ny' => "\u{A99A}", // nya
        'th' => "\u{A99B}", // tta (tha)
        'dh' => "\u{A99D}", // dda (dha)
        'q' => "\u{A990}",  // ka sasak
    ];

    /**
     * Peta vokal Latin → kunci internal.
     *
     * @var array<string, string>
     */
    private const VOWELS = [
        'a' => 'a',
        'i' => 'i',
        'u' => 'u',
        'e' => 'e',
        'o' => 'o',
        'é' => 'e',
        'è' => 'pepet',
        'ê' => 'pepet',
        'ě' => 'pepet',
        'ĕ' => 'pepet',
    ];

    /**
     * Tanda baca Latin → pada Javanese.
     *
     * @var array<string, string>
     */
    private const PUNCTUATION = [
        ',' => self::PADA_LINGSA,
        ';' => self::PADA_LINGSA,
        '.' => self::PADA_LUNGSI,
        '?' => self::PADA_LUNGSI,
        '!' => self::PADA_LUNGSI,
        ':' => self::PADA_PANGKAT,
    ];

    /**
     * Sandhangan panyigeg untuk konsonan mati di akhir kata.
     *
     * @var array<string, string>
     */
    private const PANYIGEG = [
        'ng' => self::SIGN_CECAK,
        'r' => self::SIGN_LAYAR,
        'h' => self::SIGN_WIGNYAN,
    ];

    public function __construct(
        private readonly bool $ketikPepetMode = false,
        private readonly bool $ignoreSpace = false,
        private readonly bool $aksaraSwaraMode = true,
    ) {}

    /**
     * Salinan converter dengan mode berbeda (immutable).
     */
    public function withModes(
        ?bool $ketikPepetMode = null,
        ?bool $ignoreSpace = null,
        ?bool $aksaraSwaraMode = null,
    ): static {
        return new static(
            $ketikPepetMode ?? $this->ketikPepetMode,
            $ignoreSpace ?? $this->ignoreSpace,
            $aksaraSwaraMode ?? $this->aksaraSwaraMode,
        );
    }

    /**
     * Konversi satu teks Latin menjadi Aksara Jawa.
     */
    public function convert(string $latin): string
    {
        $tokens = $this->tokenize($this->normalizeInput($latin));
        $out = '';
        $count = count($tokens);

        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];

            if ($token['t'] === 'consonant') {
                $next = $tokens[$i + 1] ?? null;

                // Konsonan + vokal → aksara + sandhangan swara.
                if ($next !== null && $next['t'] === 'vowel') {
                    $out .= $this->consonantLetter($token['v']).$this->vowelMark($next['v']);
                    $i++;

                    continue;
                }

                // Konsonan mati.
                $following = $tokens[$i + 1] ?? null;
                $isWordFinal = $following === null || $following['t'] !== 'consonant';

                if ($isWordFinal && isset(self::PANYIGEG[$token['v']])) {
                    // ng/r/h di akhir suku kata → cecak/layar/wignyan.
                    $out .= self::PANYIGEG[$token['v']];
                } else {
                    // Gugus konsonan → pangkon (aksara berikutnya jadi pasangan).
                    $out .= $this->consonantLetter($token['v']).self::PANGKON;
                }

                continue;
            }

            if ($token['t'] === 'vowel') {
                $out .= $this->independentVowel($token['v']);

                continue;
            }

            if ($token['t'] === 'space') {
                if (! $this->ignoreSpace) {
                    $out .= ' ';
                }

                continue;
            }

            if ($token['t'] === 'punct') {
                $out .= self::PUNCTUATION[$token['v']] ?? $token['v'];

                continue;
            }

            $out .= $token['v'];
        }

        return trim($out);
    }

    /**
     * Normalisasi input Latin sebelum ditokenisasi.
     */
    private function normalizeInput(string $latin): string
    {
        // `x` sebagai pêpêt (mode ketik pêpêt) atau gugus "ks".
        $latin = $this->ketikPepetMode
            ? str_replace(['x', 'X'], 'ě', $latin)
            : str_replace(['x', 'X'], 'ks', $latin);

        return mb_strtolower($latin, 'UTF-8');
    }

    /**
     * Pecah teks menjadi token konsonan/vokal/spasi/tanda baca.
     *
     * @return array<int, array{t: string, v: string}>
     */
    private function tokenize(string $text): array
    {
        $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $total = count($chars);
        $tokens = [];

        for ($i = 0; $i < $total; $i++) {
            $ch = $chars[$i];

            if ($ch === ' ' || $ch === "\n" || $ch === "\t" || $ch === "\r") {
                $tokens[] = ['t' => 'space', 'v' => ' '];

                continue;
            }

            if (isset(self::PUNCTUATION[$ch])) {
                $tokens[] = ['t' => 'punct', 'v' => $ch];

                continue;
            }

            // Digraf dua huruf lebih dulu (ng, ny, th, dh).
            $two = $ch.($chars[$i + 1] ?? '');
            if (isset(self::CONSONANTS[$two])) {
                $tokens[] = ['t' => 'consonant', 'v' => $two];
                $i++;

                continue;
            }

            if (isset(self::VOWELS[$ch])) {
                $tokens[] = ['t' => 'vowel', 'v' => self::VOWELS[$ch]];

                continue;
            }

            if (isset(self::CONSONANTS[$ch])) {
                $tokens[] = ['t' => 'consonant', 'v' => $ch];

                continue;
            }

            // Karakter tak dikenal: teruskan apa adanya.
            $tokens[] = ['t' => 'other', 'v' => $ch];
        }

        return $tokens;
    }

    private function consonantLetter(string $consonant): string
    {
        return self::CONSONANTS[$consonant] ?? '';
    }

    /**
     * Sandhangan swara untuk vokal yang menempel pada konsonan.
     */
    private function vowelMark(string $vowel): string
    {
        return match ($vowel) {
            'i' => self::VOWEL_WULU,
            'u' => self::VOWEL_SUKU,
            'e' => self::VOWEL_TALING,
            'o' => self::VOWEL_TALING.self::VOWEL_TARUNG,
            'pepet' => self::VOWEL_PEPET,
            default => '', // 'a' inheren
        };
    }

    /**
     * Vokal mandiri di awal kata.
     */
    private function independentVowel(string $vowel): string
    {
        if ($this->aksaraSwaraMode) {
            return match ($vowel) {
                'a' => self::LETTER_A,
                'i' => self::LETTER_I,
                'u' => self::LETTER_U,
                'e' => self::LETTER_E,
                'o' => self::LETTER_O,
                'pepet' => self::LETTER_A.self::VOWEL_PEPET,
                default => '',
            };
        }

        // Tanpa aksara swara: aksara "ha" + sandhangan (ha sudah membawa vokal a).
        return "\u{A9B2}".$this->vowelMark($vowel);
    }
}
