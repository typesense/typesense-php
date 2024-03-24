<?php

declare(strict_types=1);

namespace Typesense\Tests\Unit;

test('it can create a snapshot', function () {
    $path = sprintf('/tmp/%s', $this->slug());

    $success = $this->typesense->cluster->snapshot(
        $path,
    );

    expect($success)->toBeTrue();
});

test('it can compact the database', function () {
    $success = $this->typesense->cluster->compact();

    expect($success)->toBeTrue();
});

test('it can update slow request log', function () {
    $success = $this->typesense->cluster->updateSlowRequestLog(100);

    expect($success)->toBeTrue();
});

test('it can get metrics', function () {
    $metric = $this->typesense->cluster->metrics();

    expect($metric->typesense_memory_retained_bytes)->toBeGreaterThanOrEqual(0);
});

test('it can get stats', function () {
    $stats = $this->typesense->cluster->stats();

    expect($stats->delete_latency_ms)->toBeGreaterThanOrEqual(0);
});

test('it can get health', function () {
    $health = $this->typesense->cluster->health();

    expect($health)->toBeTrue();
});
