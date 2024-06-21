<?php

namespace Feature;

use Tests\TestCase;
use Typesense\Documents;

class DocumentsTest extends TestCase
{
    private ?Documents $testDocuments = null;
    private $document =   [
        "id" => "4",
        "title" => "Book 4",
        "description" => "Test...",
        "authors" => ["Hayden"],
        "isbn" => "1",
        "publisher" => "AwesomeBooks",
        "pages" => 9
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpCollection('books');
        $this->setUpDocuments('books');

        $this->testDocuments = $this->client()->collections['books']->documents;
    }

    public function testCanSearchForDocumentsInACollection(): void
    {
        $response = $this->testDocuments->search([
            'q'         => 'book',
            'query_by'  => 'title',
        ]);
        $this->assertEquals('book', $response['request_params']['q']);
        $this->assertGreaterThan(0, $response['hits']);
    }

    public function testCanCreateADocument(): void
    {
        $response = $this->testDocuments->create($this->document);
        $this->assertEquals($this->document, $response);
    }

    public function testCanUpsertADocument(): void
    {
        $documentWithDifferentId = [...$this->document, 'id' => '1']; // id 1 already exists in the collection

        $response = $this->testDocuments->upsert($documentWithDifferentId);
        $this->assertEquals($documentWithDifferentId, $response);
    }

    public function testCanImportJsonlDocumentsWithQueryParameter(): void
    {
        $documentWithDifferentId = [...$this->document, 'id' => '1'];
        $jsonlDocuments = join(PHP_EOL, array_map(
            fn ($item) => json_encode($item),
            [$this->document, $documentWithDifferentId]
        ));

        $response = $this->testDocuments->import($jsonlDocuments, [
            'action' => 'upsert'
        ]);
        $this->assertEquals("{\"success\":true}\n{\"success\":true}", $response);
    }

    public function testCanImportArrayOfDocuments(): void
    {
        $documentWithDifferentId = [...$this->document, 'id' => '5'];

        $response = $this->testDocuments->import([$this->document, $documentWithDifferentId]);
        $this->assertEquals(1, $response[0]['success']);
        $this->assertEquals(1, $response[1]['success']);
    }

    public function testCanExportDocumentsWithControlParameter(): void
    {
        $this->testDocuments->create($this->document);

        $response = $this->testDocuments->export([
            "filter_by" => "id:=4"
        ]);
        $this->assertEquals($this->document, json_decode($response, true));
    }

    public function testCanUpdateDocumentsByQuery(): void
    {
        $document = ['publisher' => 'Renamed Publisher'];

        $response = $this->testDocuments->update($document, [
            'filter_by' => 'publisher:=AwesomeBooks'
        ]);
        $this->assertGreaterThan(0, $response['num_updated']);
    }

    public function testCanDeleteDocumentsByQuery(): void
    {
        $response = $this->testDocuments->delete([
            'filter_by' => 'publisher:=AwesomeBooks'
        ]);
        $this->assertGreaterThan(0, $response['num_deleted']);
    }
}
