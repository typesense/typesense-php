<?php

namespace Tests\Feature;

use Tests\TestCase;
use Typesense\Exceptions\ObjectNotFound;
use Exception;

class SynonymSetItemsTest extends TestCase
{
    private $synonymSets = null;
    private $synonymSetData = [
        'items' => [
            [
                'id' => 'synonym-rule-1',
                'synonyms' => ['foo', 'bar', 'baz'],
                'root' => '',
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        if (!$this->isV30OrAbove()) {
            $this->markTestSkipped('SynonymSetItems is only supported in Typesense v30+');
        }

        $this->synonymSets = $this->client()->synonymSets;
        $this->synonymSets->upsert('test-synonym-set-items', $this->synonymSetData);
    }

    protected function tearDown(): void
    {
        try {
            if ($this->synonymSets !== null) {
                $this->synonymSets['test-synonym-set-items']->delete();
            }
        } catch (Exception $e) {
            // Ignore cleanup errors
        }
        parent::tearDown();
    }

    public function testCanListItemsInASynonymSet(): void
    {
        $items = $this->synonymSets['test-synonym-set-items']->items->retrieve();

        $this->assertIsArray($items);
        $this->assertGreaterThan(0, count($items));
        $this->assertEquals('foo', $items[0]['synonyms'][0]);
    }

    public function testCanUpsertRetrieveAndDeleteAnItem(): void
    {
        $upserted = $this->synonymSets['test-synonym-set-items']->items['synonym-rule-1']->upsert([
            'synonyms' => ['red', 'crimson'],
            'root' => '',
        ]);

        $this->assertEquals('synonym-rule-1', $upserted['id']);

        $fetched = $this->synonymSets['test-synonym-set-items']->items['synonym-rule-1']->retrieve();
        $this->assertEquals('red', $fetched['synonyms'][0]);

        $deletion = $this->synonymSets['test-synonym-set-items']->items['synonym-rule-1']->delete();
        $this->assertEquals('synonym-rule-1', $deletion['id']);

        $this->expectException(ObjectNotFound::class);
        $this->synonymSets['test-synonym-set-items']->items['synonym-rule-1']->retrieve();
    }

    public function testGetItemsMatchesItemsAccessor(): void
    {
        $synonymSet = $this->synonymSets['test-synonym-set-items'];

        $this->assertSame($synonymSet->items, $synonymSet->getItems());
    }

    public function testPropertiesOtherThanItemsAreUndefined(): void
    {
        $synonymSet = $this->synonymSets['test-synonym-set-items'];
        $warnings   = [];
        set_error_handler(function (int $level, string $message) use (&$warnings): bool {
            $warnings[] = $message;
            return true;
        }, E_USER_WARNING);

        try {
            $values = [$synonymSet->itmes, $synonymSet->synonymSetName, $synonymSet->apiCall];
        } finally {
            restore_error_handler();
        }

        $this->assertSame([null, null, null], $values);
        $this->assertSame([
            'Undefined property: Typesense\SynonymSet::$itmes',
            'Undefined property: Typesense\SynonymSet::$synonymSetName',
            'Undefined property: Typesense\SynonymSet::$apiCall',
        ], $warnings);
    }
}
