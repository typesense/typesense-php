<?php

declare(strict_types=1);

namespace Typesense\Tests\Unit;

use Typesense\Exceptions\Client\InvalidPayloadException;
use Typesense\Exceptions\Client\ResourceNotFoundException;

test('it can create an alias to an existing collection', function () {
    $alias = $this->typesense->alias->upsert(
        $name = $this->slug(),
        [
            'collection_name' => $this->collectionName,
        ],
    );

    expect($alias->name)->toBe($name);

    expect($alias->collection_name)->toBe($this->collectionName);
});

test('it can create an alias to a non-existent collection', function () {
    $alias = $this->typesense->alias->upsert(
        $name = $this->slug(),
        [
            'collection_name' => $collection = $this->slug(),
        ],
    );

    expect($alias->name)->toBe($name);

    expect($alias->collection_name)->toBe($collection);
});

test('it can not create an alias without collection name', function () {
    $this->typesense->alias->upsert(
        $this->slug(),
        [],
    );
})->throws(InvalidPayloadException::class);

test('it can retrieve an existing alias', function () {
    $alias = $this->typesense->alias->upsert(
        $this->slug(),
        [
            'collection_name' => $this->collectionName,
        ],
    );

    $nAlias = $this->typesense->alias->retrieve($alias->name);

    expect($nAlias->name)->toBe($alias->name);
});

test('it can not retrieve a non-existent alias', function () {
    $this->typesense->alias->retrieve(
        $this->slug(),
    );
})->throws(ResourceNotFoundException::class);

test('it can list all aliases', function () {
    $alias = $this->typesense->alias->upsert(
        $this->slug(),
        [
            'collection_name' => $this->collectionName,
        ],
    );

    $aliases = $this->typesense->alias->list();

    $names = array_column($aliases, 'name');

    expect($alias->name)->toBeIn($names);
});

test('it can delete an existing alias', function () {
    $alias = $this->typesense->alias->upsert(
        $this->slug(),
        [
            'collection_name' => $this->collectionName,
        ],
    );

    $deleted = $this->typesense->alias->delete($alias->name);

    expect($deleted)->toBeTrue();
});

test('it can not delete a non-existent alias', function () {
    $this->typesense->alias->delete(
        $this->slug(),
    );
})->throws(ResourceNotFoundException::class);
