<?php

namespace Feature;

use Tests\TestCase;
use Typesense\Exceptions\ObjectNotFound;
use Exception;

class CurationSetsTest extends TestCase
{
    private $upsertResponse = null;
    private $curationSets = null;
    private $curationSetData = [
        'items' => [
            [
                'id' => 'rule-1',
                'rule' => [
                    'query' => 'test',
                    'match' => 'exact',
                ],
                'includes' => [
                    [
                        'id' => '123',
                        'position' => 1,
                    ],
                ],
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        if (!$this->isV30OrAbove()) {
            $this->markTestSkipped('CurationSets is only supported in Typesense v30+');
        }
        
        $this->curationSets = $this->client()->curationSets;
        $this->upsertResponse = $this->curationSets->upsert('test-curation-set', $this->curationSetData);
    }

    protected function tearDown(): void
    {
        try {
            if ($this->curationSets !== null) {
                $this->curationSets['test-curation-set']->delete();
            }
        } catch (Exception $e) {
            // Ignore cleanup errors
        }
        parent::tearDown();
    }

    public function testCanUpsertACurationSet(): void
    {
        $this->assertEquals($this->curationSetData['items'][0]['id'], $this->upsertResponse['items'][0]['id']);
        $this->assertEquals($this->curationSetData['items'][0]['rule'], $this->upsertResponse['items'][0]['rule']);
        $this->assertEquals($this->curationSetData['items'][0]['includes'], $this->upsertResponse['items'][0]['includes']);
    }

    public function testCanRetrieveAllCurationSets(): void
    {
        $returnData = $this->curationSets->retrieve();
        $this->assertIsArray($returnData);
        $this->assertGreaterThan(0, count($returnData));
        
        $created = null;
        foreach ($returnData as $curationSet) {
            if ($curationSet['name'] === 'test-curation-set') {
                $created = $curationSet;
                break;
            }
        }
        $this->assertNotNull($created);
    }

    public function testCanRetrieveASpecificCurationSet(): void
    {
        $returnData = $this->curationSets['test-curation-set']->retrieve();
        $this->assertEquals($this->curationSetData['items'][0]['id'], $returnData['items'][0]['id']);
        $this->assertEquals($this->curationSetData['items'][0]['rule'], $returnData['items'][0]['rule']);
        $this->assertEquals($this->curationSetData['items'][0]['includes'], $returnData['items'][0]['includes']);
    }

    public function testCanDeleteACurationSet(): void
    {
        $returnData = $this->curationSets['test-curation-set']->delete();
        $this->assertEquals('test-curation-set', $returnData['name']);

        $this->expectException(ObjectNotFound::class);
        $this->curationSets['test-curation-set']->retrieve();
    }
}
