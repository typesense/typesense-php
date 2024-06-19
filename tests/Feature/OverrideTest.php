<?php

namespace Feature;

use Tests\TestCase;
use Typesense\Exceptions\ObjectNotFound;


class OverrideTest extends TestCase
{
    private $overrideUpsertRes = null;
    private $overrideId = 'customize-book';


    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpCollection('books');

        $override = [
            "rule" => [
                "query" => "book",
                "match" => "exact"
            ],
            "includes" => [
                ["id" => "422", "position" => 1],
            ],
            "excludes" => [
                ["id" => "287"]
            ]
        ];

        $returnData =  $this->client()->collections['books']->overrides->upsert($this->overrideId, $override);
        $this->overrideUpsertRes = $returnData;
    }

    public function testCanCreateAnOverride(): void
    {
        $this->assertEquals($this->overrideId, $this->overrideUpsertRes['id']);
    }

    public function testCanRetrieveAnOverride(): void
    {
        $returnData = $this->client()->collections['books']->overrides[$this->overrideId]->retrieve();
        $this->assertEquals($this->overrideId, $returnData['id']);
    }

    public function testCanDeleteAnOverride(): void
    {
        $returnData = $this->client()->collections['books']->overrides[$this->overrideId]->delete();
        $this->assertEquals($this->overrideId, $returnData['id']);

        $this->expectException(ObjectNotFound::class);
        $this->client()->collections['books']->overrides[$this->overrideId]->retrieve();
    }

    public function testCanRetrieveAllOverrides(): void
    {
        $returnData = $this->client()->collections['books']->overrides->retrieve();;
        $this->assertEquals(1, count($returnData['overrides']));
    }
}
