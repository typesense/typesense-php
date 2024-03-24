<?php

declare(strict_types=1);

namespace Typesense\Objects;

use stdClass;

class ImportedDocument extends TypesenseObject
{
    public bool $success;

    public ?string $id = null;

    public ?string $error = null;

    public ?int $code = null;

    public string|stdClass|null $document = null;
}
