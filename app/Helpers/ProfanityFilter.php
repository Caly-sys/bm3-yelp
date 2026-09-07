<?php

namespace App\Helpers;

class ProfanityFilter
{
    /**
     * List of profane words in Indonesian and English.
     * Each word will be matched case-insensitively.
     */
    protected static array $badWords = [
        // === Indonesian curse words ===
        'anjing', 'anjg', 'anjir', 'anying', 'anj',
        'bangsat', 'bgst', 'bngst',
        'babi',
        'bajingan',
        'brengsek',
        'goblok', 'goblog', 'gblk',
        'tolol',
        'idiot',
        'bodoh',
        'dungu',
        'kampret', 'kmprt',
        'kontol', 'kntl', 'kontl',
        'memek', 'mmk',
        'ngentot', 'ngntot', 'ngtot', 'ngentod',
        'pepek',
        'jancok', 'jancuk', 'jnck', 'dancok', 'dancuk', 'cok', 'cuk',
        'asu',
        'taik', 'tai',
        'setan',
        'iblis',
        'keparat',
        'laknat',
        'sialan',
        'bedebah',
        'monyet',
        'sinting',
        'gila',
        'bejat',
        'biadab',
        'kunyuk',
        'sontoloyo',
        'perek',
        'lonte',
        'sundal',
        'pelacur',
        'jablay',

        // === English curse words ===
        'fuck', 'fck', 'fuk', 'fuq', 'fuc',
        'shit', 'sh1t', 'sht',
        'ass', 'a55',
        'asshole', 'a55hole',
        'bitch', 'b1tch', 'btch',
        'bastard', 'bstrd',
        'damn', 'dmn',
        'dick', 'd1ck',
        'cock',
        'pussy', 'pu55y',
        'cunt',
        'whore', 'wh0re',
        'slut',
        'piss',
        'crap',
        'motherfucker', 'mf', 'mthrfckr',
        'nigga', 'nigger', 'n1gga', 'n1gger',
        'retard', 'retarded',
        'stfu',
        'wtf',
    ];

    /**
     * Filter profanity from the given text.
     * Replaces bad words with asterisks (e.g., "anjing" → "a*****").
     */
    public static function filter(string $text): string
    {
        foreach (static::$badWords as $word) {
            // Build a case-insensitive regex that matches the word with optional
            // word boundaries to avoid replacing partial legitimate words for
            // short terms (<=3 chars), but catch them in longer contexts too.
            $pattern = '/\b' . preg_quote($word, '/') . '\b/iu';

            $text = preg_replace_callback($pattern, function ($match) {
                $matched = $match[0];
                $len = mb_strlen($matched);

                if ($len <= 1) {
                    return $matched;
                }

                // Keep first letter, replace rest with asterisks
                return mb_substr($matched, 0, 1) . str_repeat('*', $len - 1);
            }, $text);
        }

        return $text;
    }

    /**
     * Check if the given text contains any profanity.
     */
    public static function containsProfanity(string $text): bool
    {
        foreach (static::$badWords as $word) {
            $pattern = '/\b' . preg_quote($word, '/') . '\b/iu';
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the list of bad words found in the text.
     */
    public static function getFoundWords(string $text): array
    {
        $found = [];

        foreach (static::$badWords as $word) {
            $pattern = '/\b' . preg_quote($word, '/') . '\b/iu';
            if (preg_match($pattern, $text)) {
                $found[] = $word;
            }
        }

        return array_unique($found);
    }
}
