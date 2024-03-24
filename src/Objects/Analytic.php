<?php

declare(strict_types=1);

namespace Typesense\Objects;

use stdClass;

/**
 * @phpstan-type RulePayload array{
 *     source: array{
 *         collections: array<int, string>,
 *     },
 *     destination: array{
 *         collection: string,
 *     },
 *     limit: int,
 * }
 */
class Analytic extends TypesenseObject
{
    public string $name;

    public string $type;

    public stdClass $params;
}
