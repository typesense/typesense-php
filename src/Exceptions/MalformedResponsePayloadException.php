<?php

declare(strict_types=1);

namespace Typesense\Exceptions;

class MalformedResponsePayloadException extends TypesenseException
{
    /**
     * Constructor.
     */
    public function __construct(
        public string $context,
    ) {
        parent::__construct();
    }
}
