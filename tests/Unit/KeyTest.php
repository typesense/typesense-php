<?php

declare(strict_types=1);

namespace Typesense\Tests\Unit;

use Typesense\Exceptions\Client\InvalidPayloadException;
use Typesense\Exceptions\Client\ResourceNotFoundException;

test('it can create an api key', function () {
    $key = $this->typesense->key->create([
        'actions' => ['collections:*'],
        'collections' => ['*'],
        'description' => $description = $this->slug(),
        'expires_at' => $expires_at = time() + 100,
    ]);

    expect($key->actions)->toBe(['collections:*']);

    expect($key->collections)->toBe(['*']);

    expect($key->description)->toBe($description);

    expect($key->expires_at)->toBe($expires_at);

    expect($key->id)->toBeInt();

    expect($key->value)->toBeString();

    expect($key->value_prefix)->toBeNull();
});

test('it can not create an api key without actions', function () {
    $this->typesense->key->create([
        'actions' => [],
        'collections' => ['*'],
        'description' => $this->slug(),
    ]);
})->throws(InvalidPayloadException::class);

test('it can not create an api key without collections', function () {
    $this->typesense->key->create([
        'actions' => ['*'],
        'collections' => [],
        'description' => $this->slug(),
    ]);
})->throws(InvalidPayloadException::class);

test('it can create an api key without description', function () {
    $this->typesense->key->create([
        'actions' => ['*'],
        'collections' => ['*'],
    ]);
})->throws(InvalidPayloadException::class);

test('it can retrieve an existing key', function () {
    $key = $this->typesense->key->create([
        'actions' => ['*'],
        'collections' => ['*'],
        'description' => $this->slug(),
    ]);

    $nKey = $this->typesense->key->retrieve($key->id);

    expect($nKey->id)->toBe($key->id);

    expect($nKey->expires_at)->toBe($key->expires_at);
});

test('it can not retrieve a non-existent key', function () {
    $this->typesense->key->retrieve(
        mt_rand(1000000000, 2147483647),
    );
})->throws(ResourceNotFoundException::class);

test('it can list all api keys', function () {
    $key = $this->typesense->key->create([
        'actions' => ['*'],
        'collections' => ['*'],
        'description' => $this->slug(),
    ]);

    $keys = $this->typesense->key->list();

    $ids = array_column($keys, 'id');

    expect($key->id)->toBeIn($ids);
});

test('it can delete an existing key', function () {
    $key = $this->typesense->key->create([
        'actions' => ['*'],
        'collections' => ['*'],
        'description' => $this->slug(),
    ]);

    $deleted = $this->typesense->key->delete($key->id);

    expect($deleted)->toBeTrue();
});

test('it can not delete a non-existent key', function () {
    $this->typesense->key->delete(
        mt_rand(1000000000, 2147483647),
    );
})->throws(ResourceNotFoundException::class);
