<?php

declare(strict_types=1);

namespace Typesense\Tests\Objects;

use Typesense\Objects\Document;

final class TestObject extends Document
{
    public string $name;

    public string $description;
}
