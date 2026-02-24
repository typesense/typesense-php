<?php

namespace Typesense;

class FilterBy
{
    public static function escapeString(string $value): string
    {
        return '`' . str_replace('`', '\\`', $value) . '`';
    }
}
