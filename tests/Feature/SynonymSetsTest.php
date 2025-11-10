<?php

namespace Feature;

use Tests\TestCase;
use Typesense\Exceptions\ObjectNotFound;
use Exception;

class SynonymSetsTest extends TestCase
{
    private $upsertResponse = null;
    private $synonymSets = null;
    private $synonymSetData = [
        'items' => [
            [
                'id' => 'dummy',
                'synonyms' => ['foo', 'bar', 'baz'],
                'root' => '',
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        if (!$this->isV30OrAbove()) {
            $this->markTestSkipped('SynonymSets is only supported in Typesense v30+');
        }
        
        $this->synonymSets = $this->client()->synonymSets;
        $this->upsertResponse = $this->synonymSets->upsert('test-synonym-set', $this->synonymSetData);
    }


    public function testCanUpsertASynonymSet(): void
    {
        $this->assertEquals($this->synonymSetData['items'], $this->upsertResponse['items']);
    }

    public function testCanRetrieveAllSynonymSets(): void
    {
        $returnData = $this->synonymSets->retrieve();
        $this->assertCount(1, $returnData);
    }

    public function testCanRetrieveASpecificSynonymSet(): void
    {
        $returnData = $this->synonymSets['test-synonym-set']->retrieve();
        $this->assertEquals($this->synonymSetData['items'], $returnData['items']);
    }

    public function testCanDeleteASynonymSet(): void
    {
        $returnData = $this->synonymSets['test-synonym-set']->delete();
        $this->assertEquals('test-synonym-set', $returnData['name']);

        $this->expectException(ObjectNotFound::class);
        $this->synonymSets['test-synonym-set']->retrieve();
    }
} 