<?php

declare(strict_types=1);

namespace Typesense\Tests\Unit;

use GuzzleHttp\Client;
use Symfony\Component\HttpClient\Psr18Client;
use Typesense\Objects\Collection;

test('it can use symfony http client', function () {
    $this->typesense->setHttp(
        new Psr18Client(),
    );

    $collection = $this->typesense->collection->retrieve(
        $this->collectionName,
    );

    expect($collection)->toBeInstanceOf(Collection::class);
});

test('it can use guzzle http client', function () {
    $this->typesense->setHttp(
        new Client(),
    );

    $collection = $this->typesense->collection->retrieve(
        $this->collectionName,
    );

    expect($collection)->toBeInstanceOf(Collection::class);
});
