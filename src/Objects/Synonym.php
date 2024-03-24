<?php

declare(strict_types=1);

namespace Typesense\Objects;

class Synonym extends TypesenseObject
{
    public string $id;

    /**
     * @var array<int, string>
     */
    public array $synonyms;

    public string $root = '';

    public string $locale = '';

    /**
     * @var array<int, string>
     */
    public array $symbols_to_index = [];
}
