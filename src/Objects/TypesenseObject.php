<?php

declare(strict_types=1);

namespace Typesense\Objects;

use stdClass;

abstract class TypesenseObject
{
    /**
     * Constructor.
     */
    final public function __construct(
        protected readonly stdClass $raw,
    ) {
        foreach (get_object_vars($this->raw) as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * Create object instance from raw data.
     */
    public static function from(stdClass $data): static
    {
        return new static($data);
    }
}
