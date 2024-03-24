<?php

declare(strict_types=1);

namespace Typesense\Objects;

use stdClass;

class Stat extends TypesenseObject
{
    public float $delete_latency_ms;

    public float $delete_requests_per_second;

    public float $import_latency_ms;

    public float $import_requests_per_second;

    public stdClass $latency_ms;

    public float $overloaded_requests_per_second;

    public float $pending_write_batches;

    public stdClass $requests_per_second;

    public float $search_latency_ms;

    public float $search_requests_per_second;

    public float $total_requests_per_second;

    public float $write_latency_ms;

    public float $write_requests_per_second;
}
