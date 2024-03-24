<?php

declare(strict_types=1);

namespace Typesense\Tests\Unit;

use Typesense\Exceptions\Client\InvalidPayloadException;
use Typesense\Exceptions\Client\ResourceAlreadyExistsException;
use Typesense\Exceptions\Client\ResourceNotFoundException;

test('it can create collection', function () {
    $collectionName = $this->slug();

    $fieldName = $this->slug();

    $fieldType = 'string';

    $collection = $this->typesense->collection->create([
        'name' => $collectionName,
        'fields' => [
            [
                'name' => $fieldName,
                'type' => $fieldType,
            ],
        ],
    ]);

    expect($collection->created_at)->toBeInt();

    expect($collection->default_sorting_field)->toBeEmpty();

    expect($collection->enable_nested_fields)->toBeFalse();

    expect($collection->name)->toBe($collectionName);

    expect($collection->num_documents)->toBe(0);

    expect($collection->symbols_to_index)->toBeEmpty();

    expect($collection->token_separators)->toBeEmpty();

    expect($collection->fields)->toHaveCount(1);

    $field = $collection->fields[0];

    expect($field->facet)->toBeFalse();

    expect($field->index)->toBeTrue();

    expect($field->infix)->toBeFalse();

    expect($field->locale)->toBeEmpty();

    expect($field->name)->toBe($fieldName);

    expect($field->optional)->toBeFalse();

    expect($field->sort)->toBeFalse();

    expect($field->type)->toBe($fieldType);
});

test('it can not create collection if the name already exists', function () {
    $this->typesense->collection->create([
        'name' => $this->collectionName,
        'fields' => [
            [
                'name' => $this->slug(),
                'type' => 'string',
            ],
        ],
    ]);
})->throws(ResourceAlreadyExistsException::class);

test('it can not create collection if the name is empty', function () {
    $this->typesense->collection->create([
        'name' => '',
    ]);
})->throws(InvalidPayloadException::class);

test('it can not create collection if the fields is empty', function () {
    $this->typesense->collection->create([
        'name' => $this->slug(),
        'fields' => [],
    ]);
})->throws(InvalidPayloadException::class);

test('it can not create collection if the fields are invalid', function () {
    $this->typesense->collection->create([
        'name' => $this->slug(),
        'fields' => [
            [
                'name' => '',
                'type' => 'string',
            ],
        ],
    ]);
})->throws(InvalidPayloadException::class);

test('it can clone collection', function () {
    $target = $this->slug();

    $collection = $this->typesense
        ->collection
        ->clone($this->collectionName, $target);

    expect($collection->name)->toBe($target);
});

test('it can not clone collection if source collection does not exist', function () {
    $this->typesense
        ->collection
        ->clone($this->slug(), $this->slug());
})->throws(InvalidPayloadException::class);

test('it can not clone collection if target collection already exists', function () {
    $this->typesense
        ->collection
        ->clone($this->collectionName, $this->collectionName);
})->throws(InvalidPayloadException::class);

test('it can retrieve collection', function () {
    $collection = $this->typesense
        ->collection
        ->retrieve($this->collectionName);

    expect($collection->name)->toBe($this->collectionName);
});

test('it can not retrieve a non-existent collection', function () {
    $this->typesense
        ->collection
        ->retrieve($this->slug());
})->throws(ResourceNotFoundException::class);

test('it can list collection', function () {
    $collections = $this->typesense
        ->collection
        ->list();

    expect($collections)
        ->toBeArray()
        ->toBeGreaterThanOrEqual(1);
});

test('it can drop collection', function () {
    $name = $this->slug();

    $this->typesense->collection->create([
        'name' => $name,
        'fields' => [
            [
                'name' => $this->slug(),
                'type' => 'string',
            ],
        ],
    ]);

    $collection = $this->typesense->collection->drop($name);

    expect($collection->name)->toBe($name);
});

test('it can not drop a non-existent collection', function () {
    $this->typesense->collection->drop($this->slug());
})->throws(ResourceNotFoundException::class);

test('it can update collection', function () {
    $name = $this->slug();

    $fields = $this->typesense->collection->update($this->collectionName, [
        [
            'name' => $name,
            'type' => 'int32',
        ],
    ]);

    expect($fields)->toHaveCount(1);

    expect($fields[0]->name)->toBe($name);

    expect($fields[0]->type)->toBe('int32');

    $fields = $this->typesense->collection->update($this->collectionName, [
        [
            'name' => $name,
            'drop' => true,
        ],
    ]);

    expect($fields)->toHaveCount(1);

    expect($fields[0]->name)->toBe($name);

    expect($fields[0]->drop)->toBeTrue();
});

test('it can not update a non-existent collection', function () {
    $this->typesense->collection->update($this->slug(), [
        [
            'name' => $this->slug(),
            'type' => 'int32',
        ],
    ]);
})->throws(ResourceNotFoundException::class);

test('it can not update an existing collection field', function () {
    $this->typesense->collection->update($this->collectionName, [
        [
            'name' => 'name',
            'type' => 'int32',
        ],
    ]);
})->throws(InvalidPayloadException::class);

test('it can not set drop to "false" for an existing collection field', function () {
    $this->typesense->collection->update($this->collectionName, [
        [
            'name' => 'name',
            'drop' => false,
        ],
    ]);
})->throws(InvalidPayloadException::class);
