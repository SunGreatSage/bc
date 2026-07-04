<?php
declare(strict_types=1);

namespace app\common\service;

class OperationLogContentService
{
    private const MAX_TEXT_BYTES = 60000;
    private const INITIAL_PREVIEW_BYTES = 56000;

    public static function encodeParams(array $params): string
    {
        return self::protectText(self::jsonEncode($params), 'params', self::extractQueryKeys($params));
    }

    /**
     * @param array|string|null $result
     */
    public static function encodeResult($result): string
    {
        if (is_array($result)) {
            return self::protectText(self::jsonEncode($result), 'result');
        }

        if ($result === null) {
            return '';
        }

        return self::protectText((string)$result, 'result');
    }

    private static function protectText(string $value, string $field, array $extra = []): string
    {
        $originalBytes = strlen($value);
        if ($originalBytes <= self::MAX_TEXT_BYTES) {
            return $value;
        }

        $previewLimit = self::INITIAL_PREVIEW_BYTES;
        while ($previewLimit >= 1024) {
            $preview = self::truncateUtf8($value, $previewLimit);
            $payload = array_merge($extra, [
                'truncated' => true,
                'truncated_field' => $field,
                'original_bytes' => $originalBytes,
                'preview_bytes' => strlen($preview),
                'preview' => $preview,
            ]);
            $encoded = self::jsonEncode($payload);
            if (strlen($encoded) <= self::MAX_TEXT_BYTES) {
                return $encoded;
            }
            $previewLimit = (int)floor($previewLimit * 0.8);
        }

        return self::jsonEncode(array_merge($extra, [
            'truncated' => true,
            'truncated_field' => $field,
            'original_bytes' => $originalBytes,
            'preview_bytes' => 0,
            'preview' => '',
        ]));
    }

    private static function jsonEncode(array $payload): string
    {
        $encoded = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($encoded !== false) {
            return $encoded;
        }

        return '{"truncated":true,"encode_error":"json_encode_failed"}';
    }

    private static function extractQueryKeys(array $params): array
    {
        $keys = [];
        foreach (['issue', 'qishu', 'plate_code', 'gid'] as $key) {
            if (array_key_exists($key, $params) && (is_scalar($params[$key]) || $params[$key] === null)) {
                $keys[$key] = (string)$params[$key];
            }
        }

        return $keys;
    }

    private static function truncateUtf8(string $value, int $maxBytes): string
    {
        if (strlen($value) <= $maxBytes) {
            return $value;
        }

        $preview = substr($value, 0, $maxBytes);
        while ($preview !== '' && !preg_match('//u', $preview)) {
            $preview = substr($preview, 0, -1);
        }

        return $preview;
    }
}
