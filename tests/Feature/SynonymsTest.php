<?php

namespace Feature;

use Tests\TestCase;
use Typesense\Exceptions\ObjectNotFound;


class SynonymsTest extends TestCase
{
    private $upsertResponse = null;
    private $synonyms = null;
    private $synonymData = [
        "synonyms" => ["blazer", "coat", "jacket"]
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpCollection('books');

        $this->synonyms = $this->client()->collections['books']->synonyms;
        $this->upsertResponse = $this->synonyms->upsert('coat-synonyms', $this->synonymData);
    }

    public function testCanUpsertASynonym(): void
    {
        $this->assertEquals('coat-synonyms', $this->upsertResponse['id']);
        $this->assertEquals($this->synonymData['synonyms'], $this->upsertResponse['synonyms']);
    }

    public function testCanRetrieveASynonymById(): void
    {
        $returnData = $this->synonyms['coat-synonyms']->retrieve();
        $this->assertEquals('coat-synonyms', $returnData['id']);
    }

    public function testCanDeleteASynonymById(): void
    {
        $returnData = $this->synonyms['coat-synonyms']->delete();
        $this->assertEquals('coat-synonyms', $returnData['id']);

        $this->expectException(ObjectNotFound::class);
        $this->synonyms['coat-synonyms']->retrieve();
    }

    public function testCanRetrieveAllSynonyms(): void
    {
        $returnData = $this->synonyms->retrieve();
        $this->assertEquals(1, count($returnData['synonyms']));
    }
}
