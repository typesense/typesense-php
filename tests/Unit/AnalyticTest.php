<?php

declare(strict_types=1);

namespace Typesense\Tests\Unit;

use Typesense\Exceptions\Client\InvalidPayloadException;
use Typesense\Exceptions\Client\ResourceAlreadyExistsException;
use Typesense\Exceptions\Client\ResourceNotFoundException;

test('it can use setup to create aggregate collection', function () {
    $created = $this->typesense->analytic->setup(
        $this->slug(),
    );

    expect($created)->toBeTrue();
});

test('it can not use setup to create aggregate collection with existing collection name', function () {
    $this->typesense->analytic->setup(
        $name = $this->slug(),
    );

    $this->typesense->analytic->setup(
        $name,
    );
})->throws(ResourceAlreadyExistsException::class);

test('it can create an analytic rule', function () {
    $this->typesense->analytic->setup(
        $collection = $this->slug(),
    );

    $rule = $this->typesense->analytic->create([
        'name' => $name = $this->slug(),
        'type' => 'popular_queries',
        'params' => [
            'source' => [
                'collections' => ['*'],
            ],
            'destination' => [
                'collection' => $collection,
            ],
            'limit' => 50,
        ],
    ]);

    expect($rule->name)->toBe($name);
});

test('it can not create an analytic rule with invalid type', function () {
    $this->typesense->analytic->setup(
        $collection = $this->slug(),
    );

    $this->typesense->analytic->create([
        'name' => $this->slug(),
        'type' => $this->slug(),
        'params' => [
            'source' => [
                'collections' => ['*'],
            ],
            'destination' => [
                'collection' => $collection,
            ],
            'limit' => 50,
        ],
    ]);
})->throws(InvalidPayloadException::class);

test('it can list all analytic rules', function () {
    $this->typesense->analytic->setup(
        $collection = $this->slug(),
    );

    $analytic = $this->typesense->analytic->create([
        'name' => $this->slug(),
        'type' => 'popular_queries',
        'params' => [
            'source' => [
                'collections' => ['*'],
            ],
            'destination' => [
                'collection' => $collection,
            ],
            'limit' => 50,
        ],
    ]);

    $analytics = $this->typesense->analytic->list();

    $names = array_column($analytics, 'name');

    expect($analytic->name)->toBeIn($names);
});

test('it can delete an existing analytic rule', function () {
    $this->typesense->analytic->setup(
        $collection = $this->slug(),
    );

    $analytic = $this->typesense->analytic->create([
        'name' => $this->slug(),
        'type' => 'popular_queries',
        'params' => [
            'source' => [
                'collections' => ['*'],
            ],
            'destination' => [
                'collection' => $collection,
            ],
            'limit' => 50,
        ],
    ]);

    $deleted = $this->typesense->analytic->delete($analytic->name);

    expect($deleted)->toBeTrue();
});

test('it can not delete a non-existent analytic rule', function () {
    $this->typesense->analytic->delete(
        $this->slug(),
    );
})->throws(ResourceNotFoundException::class);
