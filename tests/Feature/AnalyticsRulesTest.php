<?php

namespace Feature;

use Tests\TestCase;
use Typesense\Exceptions\ObjectNotFound;
use Exception;

class AnalyticsRulesTest extends TestCase
{
    private $ruleName = 'test_rule';
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

        if ($this->isV30OrAbove()) {
            $this->markTestSkipped('Analytics is deprecated in Typesense v30+');
        }

        $this->ruleUpsertResponse = $this->client()->analytics->rules()->upsert($this->ruleName, $this->ruleConfiguration);
    }

    protected function tearDown(): void
    {
        if (!$this->isV30OrAbove()) {
            try {
                $rules = $this->client()->analytics->rules()->retrieve();
                if (is_array($rules) && isset($rules['rules'])) {
                    foreach ($rules['rules'] as $rule) {
                        $this->client()->analytics->rules()->{$rule['name']}->delete();
                    }
                }
            } catch (Exception $e) {
            }
        }
    }

    public function testCanUpsertARule(): void
    {
        $this->assertEquals($this->ruleName, $this->ruleUpsertResponse['name']);
    }

    public function testCanRetrieveARule(): void
    {
        $returnData = $this->client()->analytics->rules()[$this->ruleName]->retrieve();
        $this->assertEquals($returnData['name'], $this->ruleName);
    }

    public function testCanDeleteARule(): void
    {
        $returnData = $this->client()->analytics->rules()[$this->ruleName]->delete();
        $this->assertEquals($returnData['name'], $this->ruleName);

        $this->expectException(ObjectNotFound::class);
        $this->client()->analytics->rules()[$this->ruleName]->retrieve();
    }

    public function testCanRetrieveAllRules(): void
    {
        $returnData = $this->client()->analytics->rules()->retrieve();
        $this->assertCount(1, $returnData['rules']);
    }
}
