<?php

namespace Feature;

use Tests\TestCase;
use Typesense\Exceptions\ObjectNotFound;
use Exception;

class CurationSetItemsTest extends TestCase
{
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
            $this->markTestSkipped('CurationSetItems is only supported in Typesense v30+');
        }
        
        $this->curationSets = $this->client()->curationSets;
        $this->curationSets->upsert('test-curation-set-items', $this->curationSetData);
    }

    protected function tearDown(): void
    {
        try {
            $this->curationSets['test-curation-set-items']->delete();
        } catch (Exception $e) {
            // Ignore cleanup errors
        }
        parent::tearDown();
    }

    public function testCanListItemsInACurationSet(): void
    {
        $items = $this->curationSets['test-curation-set-items']->getItems()->retrieve();
        
        $this->assertIsArray($items);
        $this->assertGreaterThan(0, count($items));
        $this->assertEquals('123', $items[0]['includes'][0]['id']);
    }

    public function testCanUpsertRetrieveAndDeleteAnItem(): void
    {
        $upserted = $this->curationSets['test-curation-set-items']->getItems()['rule-1']->upsert([
            'id' => 'rule-1',
            'rule' => [
                'query' => 'test',
                'match' => 'exact',
            ],
            'includes' => [
                [
                    'id' => '999',
                    'position' => 1,
                ],
            ],
        ]);
        
        $this->assertEquals('rule-1', $upserted['id']);

        $fetched = $this->curationSets['test-curation-set-items']->getItems()['rule-1']->retrieve();
        $this->assertEquals('999', $fetched['includes'][0]['id']);

        $deletion = $this->curationSets['test-curation-set-items']->getItems()['rule-1']->delete();
        $this->assertEquals('rule-1', $deletion['id']);
    }
}
