<?php

namespace App\Helpers;

/**
 * Self-contained Arabic glyph shaper for DomPDF rendering.
 *
 * DomPDF doesn't handle Arabic contextual shaping (connecting letters).
 * This class converts Arabic Unicode text into its presentation forms
 * so that each character is the correct glyph for its position (isolated,
 * initial, medial, or final), and reverses the string for LTR rendering.
 */
class ArabicGlyphs
{
    /**
     * Map of Arabic letters to their presentation forms.
     * [isolated, final, initial, medial]
     * null = form does not exist (letter doesn't connect to the left).
     */
    private static array $chars = [
        // Hamza
        "\u{0621}" => ["\u{FE80}", null, null, null],
        // Alef with Madda
        "\u{0622}" => ["\u{FE81}", "\u{FE82}", null, null],
        // Alef with Hamza above
        "\u{0623}" => ["\u{FE83}", "\u{FE84}", null, null],
        // Waw with Hamza
        "\u{0624}" => ["\u{FE85}", "\u{FE86}", null, null],
        // Alef with Hamza below
        "\u{0625}" => ["\u{FE87}", "\u{FE88}", null, null],
        // Yeh with Hamza
        "\u{0626}" => ["\u{FE89}", "\u{FE8A}", "\u{FE8B}", "\u{FE8C}"],
        // Alef
        "\u{0627}" => ["\u{FE8D}", "\u{FE8E}", null, null],
        // Beh
        "\u{0628}" => ["\u{FE8F}", "\u{FE90}", "\u{FE91}", "\u{FE92}"],
        // Teh Marbuta
        "\u{0629}" => ["\u{FE93}", "\u{FE94}", null, null],
        // Teh
        "\u{062A}" => ["\u{FE95}", "\u{FE96}", "\u{FE97}", "\u{FE98}"],
        // Theh
        "\u{062B}" => ["\u{FE99}", "\u{FE9A}", "\u{FE9B}", "\u{FE9C}"],
        // Jeem
        "\u{062C}" => ["\u{FE9D}", "\u{FE9E}", "\u{FE9F}", "\u{FEA0}"],
        // Hah
        "\u{062D}" => ["\u{FEA1}", "\u{FEA2}", "\u{FEA3}", "\u{FEA4}"],
        // Khah
        "\u{062E}" => ["\u{FEA5}", "\u{FEA6}", "\u{FEA7}", "\u{FEA8}"],
        // Dal
        "\u{062F}" => ["\u{FEA9}", "\u{FEAA}", null, null],
        // Thal
        "\u{0630}" => ["\u{FEAB}", "\u{FEAC}", null, null],
        // Reh
        "\u{0631}" => ["\u{FEAD}", "\u{FEAE}", null, null],
        // Zain
        "\u{0632}" => ["\u{FEAF}", "\u{FEB0}", null, null],
        // Seen
        "\u{0633}" => ["\u{FEB1}", "\u{FEB2}", "\u{FEB3}", "\u{FEB4}"],
        // Sheen
        "\u{0634}" => ["\u{FEB5}", "\u{FEB6}", "\u{FEB7}", "\u{FEB8}"],
        // Sad
        "\u{0635}" => ["\u{FEB9}", "\u{FEBA}", "\u{FEBB}", "\u{FEBC}"],
        // Dad
        "\u{0636}" => ["\u{FEBD}", "\u{FEBE}", "\u{FEBF}", "\u{FEC0}"],
        // Tah
        "\u{0637}" => ["\u{FEC1}", "\u{FEC2}", "\u{FEC3}", "\u{FEC4}"],
        // Zah
        "\u{0638}" => ["\u{FEC5}", "\u{FEC6}", "\u{FEC7}", "\u{FEC8}"],
        // Ain
        "\u{0639}" => ["\u{FEC9}", "\u{FECA}", "\u{FECB}", "\u{FECC}"],
        // Ghain
        "\u{063A}" => ["\u{FECD}", "\u{FECE}", "\u{FECF}", "\u{FED0}"],
        // Tatweel (Kashida)
        "\u{0640}" => ["\u{0640}", "\u{0640}", "\u{0640}", "\u{0640}"],
        // Feh
        "\u{0641}" => ["\u{FED1}", "\u{FED2}", "\u{FED3}", "\u{FED4}"],
        // Qaf
        "\u{0642}" => ["\u{FED5}", "\u{FED6}", "\u{FED7}", "\u{FED8}"],
        // Kaf
        "\u{0643}" => ["\u{FED9}", "\u{FEDA}", "\u{FEDB}", "\u{FEDC}"],
        // Lam
        "\u{0644}" => ["\u{FEDD}", "\u{FEDE}", "\u{FEDF}", "\u{FEE0}"],
        // Meem
        "\u{0645}" => ["\u{FEE1}", "\u{FEE2}", "\u{FEE3}", "\u{FEE4}"],
        // Noon
        "\u{0646}" => ["\u{FEE5}", "\u{FEE6}", "\u{FEE7}", "\u{FEE8}"],
        // Heh
        "\u{0647}" => ["\u{FEE9}", "\u{FEEA}", "\u{FEEB}", "\u{FEEC}"],
        // Waw
        "\u{0648}" => ["\u{FEED}", "\u{FEEE}", null, null],
        // Alef Maksura
        "\u{0649}" => ["\u{FEEF}", "\u{FEF0}", null, null],
        // Yeh
        "\u{064A}" => ["\u{FEF1}", "\u{FEF2}", "\u{FEF3}", "\u{FEF4}"],
    ];

    /**
     * Diacritics (tashkeel) that should be preserved but not affect shaping.
     */
    private static array $diacritics = [
        "\u{064B}", "\u{064C}", "\u{064D}", "\u{064E}", "\u{064F}",
        "\u{0650}", "\u{0651}", "\u{0652}", "\u{0653}", "\u{0654}",
        "\u{0655}", "\u{0670}",
    ];

    /**
     * Lam-Alef ligatures: Lam + Alef-variant => ligature
     */
    private static array $lamAlef = [
        "\u{0622}" => ["\u{FEF5}", "\u{FEF6}"], // Lam + Alef with Madda
        "\u{0623}" => ["\u{FEF7}", "\u{FEF8}"], // Lam + Alef with Hamza above
        "\u{0625}" => ["\u{FEF9}", "\u{FEFA}"], // Lam + Alef with Hamza below
        "\u{0627}" => ["\u{FEFB}", "\u{FEFC}"], // Lam + Alef
    ];

    /**
     * Check if a character can connect to the next (left) character.
     */
    private static function connectsToLeft(string $char): bool
    {
        if (!isset(self::$chars[$char])) {
            return false;
        }
        // If the character has initial or medial forms, it connects to the left
        return self::$chars[$char][2] !== null;
    }

    /**
     * Check if a character is Arabic.
     */
    private static function isArabic(string $char): bool
    {
        return isset(self::$chars[$char]);
    }

    /**
     * Check if character is a diacritic.
     */
    private static function isDiacritic(string $char): bool
    {
        return in_array($char, self::$diacritics, true);
    }

    /**
     * Shape Arabic text for DomPDF rendering.
     * Converts characters to their correct presentation forms and
     * reverses the text for LTR display.
     */
    public static function shape(string $text): string
    {
        // Split into Unicode characters
        $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
        if (!$chars) {
            return $text;
        }

        // Strip diacritics for base character analysis, but keep track of them
        $baseChars = [];
        $diacriticsMap = []; // index in baseChars => array of diacritics

        foreach ($chars as $char) {
            if (self::isDiacritic($char)) {
                $lastIdx = count($baseChars) - 1;
                if ($lastIdx >= 0) {
                    $diacriticsMap[$lastIdx][] = $char;
                }
            } else {
                $baseChars[] = $char;
            }
        }

        $result = [];
        $len = count($baseChars);

        for ($i = 0; $i < $len; $i++) {
            $char = $baseChars[$i];

            if (!self::isArabic($char)) {
                $result[] = $char;
                if (isset($diacriticsMap[$i])) {
                    foreach ($diacriticsMap[$i] as $d) {
                        $result[] = $d;
                    }
                }
                continue;
            }

            // Check for Lam-Alef ligatures
            if ($char === "\u{0644}" && $i + 1 < $len && isset(self::$lamAlef[$baseChars[$i + 1]])) {
                $nextChar = $baseChars[$i + 1];
                $ligature = self::$lamAlef[$nextChar];

                // Check if previous character connects to current (Lam)
                $prevConnects = false;
                if ($i > 0) {
                    $prevChar = $baseChars[$i - 1];
                    $prevConnects = self::isArabic($prevChar) && self::connectsToLeft($prevChar);
                }

                $result[] = $prevConnects ? $ligature[1] : $ligature[0];
                if (isset($diacriticsMap[$i])) {
                    foreach ($diacriticsMap[$i] as $d) {
                        $result[] = $d;
                    }
                }
                if (isset($diacriticsMap[$i + 1])) {
                    foreach ($diacriticsMap[$i + 1] as $d) {
                        $result[] = $d;
                    }
                }
                $i++; // Skip the Alef
                continue;
            }

            // Determine connectivity
            $prevConnects = false;
            if ($i > 0) {
                $prevChar = $baseChars[$i - 1];
                // Special case: if prev was consumed by Lam-Alef, skip
                $prevConnects = self::isArabic($prevChar) && self::connectsToLeft($prevChar);
            }

            $nextIsArabic = false;
            if ($i + 1 < $len) {
                $nextIsArabic = self::isArabic($baseChars[$i + 1]);
            }

            $forms = self::$chars[$char];

            if ($prevConnects && $nextIsArabic && $forms[3] !== null) {
                // Medial form
                $result[] = $forms[3];
            } elseif ($prevConnects && $forms[1] !== null) {
                // Final form
                $result[] = $forms[1];
            } elseif ($nextIsArabic && $forms[2] !== null) {
                // Initial form
                $result[] = $forms[2];
            } else {
                // Isolated form
                $result[] = $forms[0] ?? $char;
            }

            if (isset($diacriticsMap[$i])) {
                foreach ($diacriticsMap[$i] as $d) {
                    $result[] = $d;
                }
            }
        }

        // Reverse for LTR rendering in DomPDF
        // But we need to be smart: only reverse Arabic runs, not Latin/number runs
        return self::bidiReverse($result);
    }

    /**
     * Simple bidi reversal: reverse the entire array but keep Latin/number
     * sub-sequences in their original LTR order.
     */
    private static function bidiReverse(array $chars): string
    {
        // Reverse the whole thing
        $reversed = array_reverse($chars);

        // Now find runs of non-Arabic characters and reverse them back
        $result = [];
        $ltrRun = [];

        foreach ($reversed as $char) {
            $isRtl = self::isArabicPresentation($char) || self::isDiacritic($char);

            if (!$isRtl && $char !== ' ') {
                $ltrRun[] = $char;
            } else {
                if (!empty($ltrRun)) {
                    $result = array_merge($result, array_reverse($ltrRun));
                    $ltrRun = [];
                }
                $result[] = $char;
            }
        }

        if (!empty($ltrRun)) {
            $result = array_merge($result, array_reverse($ltrRun));
        }

        return implode('', $result);
    }

    /**
     * Check if a character is in Arabic presentation forms range.
     */
    private static function isArabicPresentation(string $char): bool
    {
        $ord = mb_ord($char, 'UTF-8');
        if ($ord === false) {
            return false;
        }
        // Arabic Presentation Forms-A: FB50–FDFF
        // Arabic Presentation Forms-B: FE70–FEFF
        // Arabic: 0600–06FF
        return ($ord >= 0x0600 && $ord <= 0x06FF)
            || ($ord >= 0xFB50 && $ord <= 0xFDFF)
            || ($ord >= 0xFE70 && $ord <= 0xFEFF);
    }
}
