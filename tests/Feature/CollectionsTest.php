<?php

namespace Feature;

use Tests\TestCase;
use Typesense\Exceptions\ObjectNotFound;

class CollectionTest extends TestCase
{
    private $createCollectionRes = null;


    protected function setUp(): void
    {
        parent::setUp();

        $schema = $this->getSchema('books');
        $this->createCollectionRes = $this->client()->collections->create($schema);
    }

    public function testCanCreateACollection(): void
    {
        $this->assertEquals('books', $this->createCollectionRes['name']);
    }

    public function testCanRetrieveACollection(): void
    {
        $response = $this->client()->collections['books']->retrieve();
        $this->assertEquals('books', $response['name']);
    }

    public function testCanUpdateACollection(): void
    {
        $update_schema = [
            'fields'    => [
                [
                    'name'  => 'isbn',
                    'drop'  => true
                ]
            ]
        ];
        $response = $this->client()->collections['books']->update($update_schema);
        $this->assertEquals('isbn', $response['fields'][0]['name']);
        $this->assertArrayHasKey('drop', $response['fields'][0]);

        $response = $this->client()->collections['books']->retrieve();
        $this->assertEquals(5, count($response['fields']));
    }

    public function testCanDeleteACollection(): void
    {
        $this->client()->collections['books']->delete();

        $this->expectException(ObjectNotFound::class);
        $this->client()->collections['books']->retrieve();
    }

    public function testCanRetrieveAllCollections(): void
    {
        $response = $this->client()->collections->retrieve();
        $this->assertEquals(1, count($response));
    }
}
