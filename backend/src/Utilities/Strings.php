<?php

declare(strict_types=1);

namespace App\Utilities;

final class Strings
{
    /**
     * Truncate text (adding "..." if needed)
     */
    public static function truncateText(string $text, int $limit = 80, string $pad = '...'): string
    {
        mb_internal_encoding('UTF-8');

        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        $wrappedText = self::mbWordwrap($text, $limit, '{N}', true);
        $shortenedText = mb_substr(
            $wrappedText,
            0,
            strpos($wrappedText, '{N}') ?: null
        );

        // Prevent the padding string from bumping up against punctuation.
        $punctuation = ['.', ',', ';', '?', '!'];
        if (in_array(mb_substr($shortenedText, -1), $punctuation, true)) {
            $shortenedText = mb_substr($shortenedText, 0, -1);
        }

        return $shortenedText . $pad;
    }

    /**
     * UTF-8 capable replacement for wordwrap function.
     *
     * @param string $str
     * @param int $width
     * @param non-empty-string $break
     * @param bool $cut
     */
    public static function mbWordwrap(string $str, int $width = 75, string $break = "\n", bool $cut = false): string
    {
        $lines = explode($break, $str);
        foreach ($lines as &$line) {
            $line = rtrim($line);
            if (mb_strlen($line) <= $width) {
                continue;
            }
            $words = explode(' ', $line);
            $line = '';
            $actual = '';
            foreach ($words as $word) {
                if (mb_strlen($actual . $word) <= $width) {
                    $actual .= $word . ' ';
                } else {
                    if ($actual != '') {
                        $line .= rtrim($actual) . $break;
                    }
                    $actual = $word;
                    if ($cut) {
                        while (mb_strlen($actual) > $width) {
                            $line .= mb_substr($actual, 0, $width) . $break;
                            $actual = mb_substr($actual, $width);
                        }
                    }
                    $actual .= ' ';
                }
            }
            $line .= trim($actual);
        }

        return implode($break, $lines);
    }
}
