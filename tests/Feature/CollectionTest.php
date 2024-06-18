<?php

namespace Feature;

use Tests\TestCase;
use Typesense\Exceptions\ObjectNotFound;

class CollectionTest extends TestCase
{
    public function testCanCreateCollection(): void
    {
        $schema = $this->getSchema('books');

        $response = $this->client()->collections->create($schema);

        $this->assertEquals('books', $response['name']);
    }

    public function testCanRetrieveCollection(): void
    {
        $schema = $this->getSchema('books');
        $this->client()->collections->create($schema);

        $response = $this->client()->collections['books']->retrieve();

        $this->assertEquals('books', $response['name']);
    }

    public function testCanDeleteCollection(): void
    {
        $schema = $this->getSchema('books');
        $this->client()->collections->create($schema);

        $this->client()->collections['books']->delete();

        $this->expectException(ObjectNotFound::class);

        $this->client()->collections['books']->retrieve();
    }
}
