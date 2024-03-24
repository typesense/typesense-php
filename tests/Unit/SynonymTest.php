<?php

declare(strict_types=1);

namespace Typesense\Tests\Unit;

use Typesense\Exceptions\Client\ResourceNotFoundException;

test('it can create a multi-way synonym', function () {
    $synonym = $this->typesense->synonym->upsert(
        $this->collectionName,
        $id = $this->slug(),
        [
            'synonyms' => $synonyms = ['apple', 'banana', 'car'],
        ],
    );

    expect($synonym->id)->toBe($id);

    expect($synonym->synonyms)->toBe($synonyms);

    expect($synonym->root)->toBe('');
});

test('it can create a one-way synonym', function () {
    $synonym = $this->typesense->synonym->upsert(
        $this->collectionName,
        $id = $this->slug(),
        [
            'root' => $root = 'dog',
            'synonyms' => $synonyms = ['apple', 'banana', 'car'],
        ],
    );

    expect($synonym->id)->toBe($id);

    expect($synonym->synonyms)->toBe($synonyms);

    expect($synonym->root)->toBe($root);
});

test('it can retrieve an existing synonym', function () {
    $this->typesense->synonym->upsert(
        $this->collectionName,
        $id = $this->slug(),
        [
            'synonyms' => [$this->slug()],
        ],
    );

    $synonym = $this->typesense->synonym->retrieve(
        $this->collectionName,
        $id,
    );

    expect($synonym->id)->toBe($id);
});

test('it can not retrieve a non-existent synonym', function () {
    $this->typesense->synonym->retrieve(
        $this->collectionName,
        $this->slug(),
    );
})->throws(ResourceNotFoundException::class);

test('it can list all synonyms', function () {
    $synonym = $this->typesense->synonym->upsert(
        $this->collectionName,
        $this->slug(),
        [
            'synonyms' => [$this->slug()],
        ],
    );

    $synonyms = $this->typesense->synonym->list($this->collectionName);

    $ids = array_column($synonyms, 'id');

    expect($synonym->id)->toBeIn($ids);
});

test('it can delete an existing synonym', function () {
    $synonym = $this->typesense->synonym->upsert(
        $this->collectionName,
        $this->slug(),
        [
            'synonyms' => [$this->slug()],
        ],
    );

    $deleted = $this->typesense->synonym->delete(
        $this->collectionName,
        $synonym->id,
    );

    expect($deleted)->toBeTrue();
});

test('it can not delete a non-existent synonym', function () {
    $this->typesense->synonym->delete(
        $this->collectionName,
        $this->slug(),
    );
})->throws(ResourceNotFoundException::class);
