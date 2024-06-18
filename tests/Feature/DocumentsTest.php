<?php

namespace Feature;

use Tests\TestCase;

class DocumentsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpCollection('books');
        $this->setUpDocuments('books');
    }

    public function testCanUpdateDocumentsByFilter(): void
    {
        $document = ['publisher' => 'Renamed Publisher'];

        $response = $this->client()->collections['books']->documents->update($document, [
            'filter_by' => 'publisher:=AwesomeBooks'
        ]);

        $this->assertGreaterThan(0, $response['num_updated']);
    }
}
