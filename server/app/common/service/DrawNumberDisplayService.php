<?php
declare(strict_types=1);

namespace app\common\service;

class DrawNumberDisplayService
{
    /**
     * Build a stable display order for draw numbers.
     *
     * The first six normal numbers are shuffled by a deterministic seed while
     * the seventh special number stays in the seventh position.
     */
    public static function buildDisplayNumbers(array $numbers, string $issue = '', string $plateCode = ''): array
    {
        if (count($numbers) < 7) {
            return $numbers;
        }

        $originalNormal = array_slice($numbers, 0, 6);
        $normal = $originalNormal;
        $special = array_slice($numbers, 6, 1);
        $seed = self::buildSeed($issue, $plateCode, $numbers);

        for ($i = count($normal) - 1; $i > 0; $i--) {
            $seed = self::nextSeed($seed);
            $j = $seed % ($i + 1);
            if ($i === $j) {
                continue;
            }
            $tmp = $normal[$i];
            $normal[$i] = $normal[$j];
            $normal[$j] = $tmp;
        }

        if (self::numberSignature($normal) === self::numberSignature($originalNormal)) {
            $first = array_shift($normal);
            $normal[] = $first;
        }

        return array_merge($normal, $special);
    }

    private static function buildSeed(string $issue, string $plateCode, array $numbers): int
    {
        $text = $issue . '|' . $plateCode . '|' . implode(',', array_map(static function ($number): string {
            if (is_array($number)) {
                return (string)($number['num'] ?? json_encode($number, JSON_UNESCAPED_UNICODE));
            }
            return (string)$number;
        }, $numbers));

        $hash = sprintf('%u', crc32($text));
        $seed = (int)$hash;

        return $seed > 0 ? $seed : 1;
    }

    private static function nextSeed(int $seed): int
    {
        return (int)(($seed * 1664525 + 1013904223) & 0x7fffffff);
    }

    private static function numberSignature(array $numbers): string
    {
        return implode(',', array_map(static function ($number): string {
            if (is_array($number)) {
                return str_pad((string)($number['num'] ?? ''), 2, '0', STR_PAD_LEFT);
            }
            return str_pad((string)((int)$number), 2, '0', STR_PAD_LEFT);
        }, $numbers));
    }
}
