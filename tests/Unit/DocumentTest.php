<?php

declare(strict_types=1);

namespace Typesense\Tests\Unit;

use Typesense\Tests\Objects\TestObject;

test('it can index document', function () {
    $name = $this->slug();

    $description = $this->slug();

    $document = $this->typesense->document->index(
        'testing',
        [
            'name' => $name,
            'description' => $description,
        ],
        TestObject::class,
    );

    expect($document->name)->toBe($name);

    expect($document->description)->toBe($description);
});

test('it can upsert document', function () {
    $name = $this->slug();

    $description = $this->slug();

    $document = $this->typesense->document->index(
        'testing',
        [
            'name' => $name,
            'description' => $description,
        ],
        TestObject::class,
    );

    expect($document->name)->toBe($name);

    expect($document->description)->toBe($description);

    $name = $this->slug();

    $description = $this->slug();

    $document = $this->typesense->document->upsert(
        'testing',
        [
            'id' => $document->id,
            'name' => $name,
            'description' => $description,
        ],
        TestObject::class,
    );

    expect($document->name)->toBe($name);

    expect($document->description)->toBe($description);
});

test('it can import document', function () {
    $document = $this->typesense->document->index(
        'testing',
        [
            'name' => $this->slug(),
            'description' => $this->slug(),
        ],
        TestObject::class,
    );

    $documents = [
        [
            'id' => $document->id,
            'name' => $this->slug(),
            'description' => $this->slug(),
        ],
        [
            'name' => $this->slug(),
            'description' => $this->slug(),
        ],
        [
            'name' => $this->slug(),
            'description' => $this->slug(),
        ],
    ];

    $results = $this->typesense->document->import(
        'testing',
        $documents,
        return_id: true,
        return_doc: true,
    );

    expect($results)->toHaveCount(count($documents));

    expect($results[0]->success)->toBeFalse();

    expect($results[1]->success)->toBeTrue();

    expect($results[2]->success)->toBeTrue();
});

test('it can retrieve document', function () {
    $name = $this->slug();

    $description = $this->slug();

    $document = $this->typesense->document->index(
        'testing',
        [
            'name' => $name,
            'description' => $description,
        ],
        TestObject::class,
    );

    $document = $this->typesense->document->retrieve('testing', $document->id);

    expect($document->name)->toBe($name);

    expect($document->description)->toBe($description);
});

test('it can update document', function () {
    $name = $this->slug();

    $description = $this->slug();

    $document = $this->typesense->document->index(
        'testing',
        [
            'name' => $name,
            'description' => $description,
        ],
        TestObject::class,
    );

    $description = $this->slug();

    expect($document->description)->not()->toBe($description);

    $document = $this->typesense->document->update(
        'testing',
        $document->id,
        [
            'description' => $description,
        ],
    );

    expect($document->name)->toBe($name);

    expect($document->description)->toBe($description);
});

test('it can update documents by query', function () {
    $documents = [
        [
            'id' => '1',
            'name' => $this->slug(),
            'description' => $this->slug(),
        ],
        [
            'id' => '2',
            'name' => $this->slug(),
            'description' => $this->slug(),
        ],
        [
            'id' => '3',
            'name' => $this->slug(),
            'description' => $this->slug(),
        ],
    ];

    $imported = $this->typesense->document->import(
        'testing',
        $documents,
    );

    expect($imported)->toHaveCount(count($documents));

    $description = $this->slug();

    $updated = $this->typesense->document->updateByQuery(
        'testing',
        'id:[2,3]',
        [
            'description' => $description,
        ],
    );

    expect($updated)->toBe(2);
});

test('it can delete document', function () {
    $document = $this->typesense->document->index(
        'testing',
        [
            'name' => $this->slug(),
            'description' => $this->slug(),
        ],
        TestObject::class,
    );

    $id = $document->id;

    $document = $this->typesense->document->delete('testing', $id);

    expect($document->id)->toBe($id);
});

test('it can delete documents by query', function () {
    $documents = [
        [
            'id' => '1',
            'name' => $this->slug(),
            'description' => $this->slug(),
        ],
        [
            'id' => '2',
            'name' => $this->slug(),
            'description' => $this->slug(),
        ],
        [
            'id' => '3',
            'name' => $this->slug(),
            'description' => $this->slug(),
        ],
    ];

    $imported = $this->typesense->document->import(
        'testing',
        $documents,
    );

    expect($imported)->toHaveCount(count($documents));

    $deleted = $this->typesense->document->deleteByQuery(
        'testing',
        'id:[2,3]',
    );

    expect($deleted)->toBe(2);
});

test('it can export documents', function () {
    $documents = [
        [
            'id' => '1',
            'name' => $this->slug(),
            'description' => $this->slug(),
        ],
        [
            'id' => '2',
            'name' => $this->slug(),
            'description' => $this->slug(),
        ],
    ];

    $imported = $this->typesense->document->import(
        'testing',
        $documents,
    );

    expect($imported)->toHaveCount(count($documents));

    $exports = $this->typesense->document->export(
        'testing',
        document: TestObject::class,
    );

    expect($exports)->toHaveCount(count($documents));
});
