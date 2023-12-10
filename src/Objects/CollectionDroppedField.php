<?php

declare(strict_types=1);

namespace Typesense\Objects;

class CollectionDroppedField extends TypesenseObject
{
    public string $name;

    public true $drop;

    public mixed $embed;
}
