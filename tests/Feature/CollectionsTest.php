<?php

namespace Feature;

use Tests\TestCase;
use Typesense\Collection;
use Typesense\Exceptions\ObjectNotFound;

class CollectionsTest extends TestCase
{
    private $createCollectionRes = null;
    private ?Collection $testCollection = null;


    protected function setUp(): void
    {
        parent::setUp();

        $schema = $this->getSchema('books');

        $this->createCollectionRes = $this->client()->collections->create($schema);
        $this->testCollection = $this->client()->collections['books'];
    }

    public function testCanCreateACollection(): void
    {
        $this->assertEquals('books', $this->createCollectionRes['name']);
    }

    public function testCanRetrieveACollection(): void
    {
        $response = $this->testCollection->retrieve();
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
        $response = $this->testCollection->update($update_schema);
        $this->assertEquals('isbn', $response['fields'][0]['name']);
        $this->assertArrayHasKey('drop', $response['fields'][0]);

        $response = $this->testCollection->retrieve();
        $this->assertEquals(5, count($response['fields']));
    }

    public function testCanDeleteACollection(): void
    {
        $this->testCollection->delete();

        $this->expectException(ObjectNotFound::class);
        $this->testCollection->retrieve();
    }

    public function testCanRetrieveAllCollections(): void
    {
        $response = $this->client()->collections->retrieve();
        $this->assertCount(1, $response);
    }

    public function testCanCloneACollectionSchema(): void
    {
        $response = $this->client()->collections->create(['name' => 'books_v2'], ["src_name" => "books"]);
        $this->assertEquals('books_v2', $response['name']);
        $this->assertEquals($this->createCollectionRes['fields'], $response['fields']);
    }
}
