<?php

namespace Feature;

use Tests\TestCase;
use Typesense\Exceptions\ObjectNotFound;

class DocumentTest extends TestCase
{
    private $documentId = '1';
    private $testDocument = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpCollection('books');
        $this->setUpDocuments('books');

        $this->testDocument = $this->client()->collections['books']->documents[$this->documentId];
    }

    public function testCanRetrieveADocumentById(): void
    {
        $response = $this->testDocument->retrieve();
        $this->assertEquals($this->documentId, $response['id']);
    }

    public function testCanUpdateADocumentById(): void
    {
        $partialDocument = [
            "title" => "hello there :D",
        ];
        $response = $this->testDocument->update($partialDocument);
        $this->assertEquals("hello there :D", $response['title']);
    }

    public function testCanUpdateADocumentWithDirtyValuesById(): void
    {
        $partialDocument = [
            "title" => 1,
        ];
        $response = $this->testDocument->update(
            $partialDocument,
            [
                "dirty_values" => "coerce_or_reject",
            ]
        );
        $this->assertIsString($response['title']);
    }

    public function testCanDeleteADocumentById(): void
    {
        $response = $this->testDocument->delete();
        $this->assertEquals($this->documentId, $response['id']);

        $this->expectException(ObjectNotFound::class);
        $this->testDocument->retrieve();
    }
}
