<?php

declare(strict_types=1);

namespace Typesense\Objects;

use stdClass;

class Stat extends TypesenseObject
{
    public int $delete_latency_ms;

    public int $delete_requests_per_second;

    public int $import_latency_ms;

    public int $import_requests_per_second;

    public stdClass $latency_ms;

    public int $overloaded_requests_per_second;

    public int $pending_write_batches;

    public stdClass $requests_per_second;

    public int $search_latency_ms;

    public int $search_requests_per_second;

    public float $total_requests_per_second;

    public int $write_latency_ms;

    public int $write_requests_per_second;
}
