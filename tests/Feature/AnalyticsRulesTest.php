<?php

namespace Feature;

use Tests\TestCase;
use Typesense\Exceptions\ObjectNotFound;

class AnalyticsRuleTest extends TestCase
{
    private $ruleName = 'product_queries_aggregation';
    private $ruleConfiguration = [
        "type" => "popular_queries",
        "params" => [
            "source" => [
                "collections" => ["products"]
            ],
            "destination" => [
                "collection" => "product_queries"
            ],
            "expand_query" => false,
            "limit" => 1000
        ]
    ];
    private $ruleUpsertResponse = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ruleUpsertResponse = $this->client()->analytics->rules()->upsert($this->ruleName, $this->ruleConfiguration);
    }

    public function testCanUpsertARule(): void
    {
        $this->assertEquals($this->ruleName, $this->ruleUpsertResponse['name']);
    }

    public function testCanRetrieveARule(): void
    {
        $returnData = $this->client()->analytics->rules()->{$this->ruleName}->retrieve();
        $this->assertEquals($returnData['name'], $this->ruleName);
    }

    public function testCanDeleteARule(): void
    {
        $returnData = $this->client()->analytics->rules()->{$this->ruleName}->delete();
        $this->assertEquals($returnData['name'], $this->ruleName);

        $this->expectException(ObjectNotFound::class);
        $this->client()->analytics->rules()->{$this->ruleName}->retrieve();
    }

    public function testCanRetrieveAllRules(): void
    {
        $returnData = $this->client()->analytics->rules()->retrieve();
        $this->assertEquals(count($returnData['rules']), 1);
    }
}
