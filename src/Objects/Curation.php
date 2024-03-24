<?php

declare(strict_types=1);

namespace Typesense\Objects;

use stdClass;

/**
 * @phpstan-type CurationRule array{
 *     query?: string,
 *     match?: 'exact'|'contains',
 *     filter_by?: string,
 * }
 * @phpstan-type CurationExclude array{
 *     id: string,
 * }
 * @phpstan-type CurationInclude array{
 *     id: string,
 *     position: int,
 * }
 */
class Curation extends TypesenseObject
{
    public string $id;

    /**
     * @var stdClass{
     *     query?: string,
     *     match?: 'exact'|'contains',
     *     filter_by?: string,
     * }
     */
    public stdClass $rule;

    /**
     * @var array<int, CurationExclude>
     */
    public array $excludes = [];

    /**
     * @var array<int, CurationInclude>
     */
    public array $includes = [];

    public ?string $filter_by = null;

    public ?string $sort_by = null;

    public ?string $replace_query = null;

    public bool $remove_matched_tokens = true;

    public bool $filter_curated_hits = false;

    public ?int $effective_from_ts = null;

    public ?int $effective_to_ts = null;

    public bool $stop_processing = true;
}
