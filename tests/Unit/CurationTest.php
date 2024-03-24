<?php

declare(strict_types=1);

namespace Typesense\Tests\Unit;

use Typesense\Exceptions\Client\InvalidPayloadException;
use Typesense\Exceptions\Client\ResourceNotFoundException;

test('it can create a curation', function () {
    $curation = $this->typesense->curation->upsert(
        $this->collectionName,
        $id = $this->slug(),
        [
            'rule' => [
                'query' => 'apple',
                'match' => 'contains',
            ],
            'remove_matched_tokens' => true,
        ],
    );

    expect($curation->id)->toBe($id);
});

test('it can not create a curation without required fields', function () {
    $this->typesense->curation->upsert(
        $this->collectionName,
        $this->slug(),
        [
            'rule' => [
                'query' => 'apple',
                'match' => 'contains',
            ],
        ],
    );
})->throws(InvalidPayloadException::class);

test('it can retrieve an existing curation', function () {
    $this->typesense->curation->upsert(
        $this->collectionName,
        $id = $this->slug(),
        [
            'rule' => [
                'query' => 'apple',
                'match' => 'contains',
            ],
            'remove_matched_tokens' => true,
        ],
    );

    $curation = $this->typesense->curation->retrieve(
        $this->collectionName,
        $id,
    );

    expect($curation->id)->toBe($id);
});

test('it can not retrieve a non--existent curation', function () {
    $this->typesense->curation->retrieve(
        $this->collectionName,
        $this->slug(),
    );
})->throws(ResourceNotFoundException::class);

test('it can list all curations', function () {
    $curation = $this->typesense->curation->upsert(
        $this->collectionName,
        $this->slug(),
        [
            'rule' => [
                'query' => 'apple',
                'match' => 'contains',
            ],
            'remove_matched_tokens' => true,
        ],
    );

    $curations = $this->typesense->curation->list($this->collectionName);

    $ids = array_column($curations, 'id');

    expect($curation->id)->toBeIn($ids);
});

test('it can delete an existing curation', function () {
    $curation = $this->typesense->curation->upsert(
        $this->collectionName,
        $this->slug(),
        [
            'rule' => [
                'query' => 'apple',
                'match' => 'contains',
            ],
            'remove_matched_tokens' => true,
        ],
    );

    $deleted = $this->typesense->curation->delete(
        $this->collectionName,
        $curation->id,
    );

    expect($deleted)->toBeTrue();
});

test('it can not delete a non-existent curation', function () {
    $this->typesense->curation->delete(
        $this->collectionName,
        $this->slug(),
    );
})->throws(ResourceNotFoundException::class);
