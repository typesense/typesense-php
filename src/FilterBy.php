<?php

namespace Typesense;

class FilterBy
{
    public static function escape(string|int|float|bool $value): string
    {
        return match (true) {
            is_string($value) => '`' . str_replace('`', '\\`', $value) . '`',
            is_bool($value) => $value ? 'true' : 'false',
            default => (string) $value,
        };
    }
}
