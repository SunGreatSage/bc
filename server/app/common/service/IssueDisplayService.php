<?php

declare(strict_types=1);

namespace app\common\service;

final class IssueDisplayService
{
    public static function formatDisplayQishu(string $issue = '', $drawTime = null): string
    {
        $date = self::resolveIssueDate($issue, $drawTime);
        if (!$date) {
            return '';
        }

        return str_pad((string)((int)$date->format('z') + 1), 3, '0', STR_PAD_LEFT);
    }

    private static function resolveIssueDate(string $issue, $drawTime): ?\DateTimeImmutable
    {
        if (preg_match('/^(\d{8})/', $issue, $matches)) {
            $date = \DateTimeImmutable::createFromFormat('!Ymd', $matches[1]);
            if ($date instanceof \DateTimeImmutable && $date->format('Ymd') === $matches[1]) {
                return $date;
            }
        }

        if (is_numeric($drawTime) && (int)$drawTime > 0) {
            return (new \DateTimeImmutable())->setTimestamp((int)$drawTime);
        }

        $text = trim((string)$drawTime);
        if ($text !== '') {
            try {
                return new \DateTimeImmutable($text);
            } catch (\Throwable) {
                return null;
            }
        }

        return null;
    }
}
