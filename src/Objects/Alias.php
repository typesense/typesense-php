<?php

declare(strict_types=1);

namespace Typesense\Objects;

class Alias extends TypesenseObject
{
    public string $name;

    public string $collection_name;
}
