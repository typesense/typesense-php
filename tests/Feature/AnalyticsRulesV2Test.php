<?php

namespace Feature;

use Tests\TestCase;
use Typesense\Exceptions\RequestMalformed;
use Exception;

class AnalyticsRulesV2Test extends TestCase
{
    private $ruleName = 'test_v2_rule';
    private $ruleConfiguration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ruleConfiguration = [
            "name" => $this->ruleName,
            "type" => "counter",
            "collection" => "test_products",
            "event_type" => "click",
            "rule_tag" => "test_tag",
            "params" => [
                "counter_field" => "popularity",
                "weight" => 1
            ]
        ];

        if (!$this->isV30OrAbove()) {
            $this->markTestSkipped('New Analytics API is not supported in Typesense v29.0 and below');
        }

        try {
            $this->client()->collections->create([
                'name' => 'test_products',
                'fields' => [
                    ['name' => 'company_name', 'type' => 'string'],
                    ['name' => 'num_employees', 'type' => 'int32'],
                    ['name' => 'country', 'type' => 'string', 'facet' => true],
                    ['name' => 'popularity', 'type' => 'int32', 'optional' => true]
                ],
                'default_sorting_field' => 'num_employees'
            ]);
        } catch (Exception $e) {
        }

        try {
            $this->client()->analyticsV2->rules()->create([$this->ruleConfiguration]);
        } catch (Exception $e) {
        }
    }

    protected function tearDown(): void
    {
        if (!$this->isV30OrAbove()) {
            try {
                $rules = $this->client()->analyticsV2->rules()->retrieve();
                if (is_array($rules)) {
                    foreach ($rules as $rule) {
                        if (strpos($rule['name'], 'test_v2_') === 0) {
                            try {
                                $this->client()->analyticsV2->rules()[$rule['name']]->delete();
                            } catch (Exception $e) {
                            }
                        }
                    }
                }
            } catch (Exception $e) {
            }

            try {
                $this->client()->collections['test_products']->delete();
            } catch (Exception $e) {
            }
        }
    }

    public function testCanCreateRulesWithV2API(): void
    {
        $rules = [
            [
                "name" => "test_rule_1",
                "type" => "counter",
                "collection" => "test_products",
                "event_type" => "click",
                "rule_tag" => "test_tag",
                "params" => [
                    "counter_field" => "popularity",
                    "weight" => 1
                ]
            ],
            [
                "name" => "test_rule_2",
                "type" => "counter",
                "collection" => "test_products",
                "event_type" => "conversion",
                "rule_tag" => "test_tag",
                "params" => [
                    "counter_field" => "popularity",
                    "weight" => 2
                ]
            ]
        ];

        $response = $this->client()->analyticsV2->rules()->create($rules);
        $this->assertIsArray($response);
        
        $allRules = $this->client()->analyticsV2->rules()->retrieve();
        $this->assertIsArray($allRules);
        
        $ruleNames = array_column($allRules, 'name');
        $this->assertContains('test_rule_1', $ruleNames);
        $this->assertContains('test_rule_2', $ruleNames);
    }

    public function testCanRetrieveARuleWithV2API(): void
    {
        $returnData = $this->client()->analyticsV2->rules()[$this->ruleName]->retrieve();
        $this->assertEquals($this->ruleName, $returnData['name']);
        $this->assertEquals('counter', $returnData['type']);
        $this->assertEquals('test_products', $returnData['collection']);
    }

    public function testCanUpdateARuleWithV2API(): void
    {
        $updateParams = [
            "type" => "counter",
            "collection" => "test_products",
            "event_type" => "click",
            "rule_tag" => "updated_tag",
            "params" => [
                "counter_field" => "popularity",
                "weight" => 5
            ]
        ];

        $response = $this->client()->analyticsV2->rules()[$this->ruleName]->update($updateParams);
        $this->assertEquals($this->ruleName, $response['name']);
        $this->assertEquals('updated_tag', $response['rule_tag']);
        $this->assertEquals(5, $response['params']['weight']);
    }

    public function testCanDeleteARuleWithV2API(): void
    {
        $returnData = $this->client()->analyticsV2->rules()[$this->ruleName]->delete();
        $this->assertEquals($this->ruleName, $returnData['name']);

        $this->expectException(RequestMalformed::class);
        $this->client()->analyticsV2->rules()[$this->ruleName]->retrieve();
    }

    public function testCanRetrieveAllRulesWithV2API(): void
    {
        $returnData = $this->client()->analyticsV2->rules()->retrieve();
        $this->assertIsArray($returnData);
        $this->assertGreaterThanOrEqual(1, count($returnData));
        
        $ruleNames = array_column($returnData, 'name');
        $this->assertContains('test_v2_rule', $ruleNames);
        $this->assertContains('test_rule_1', $ruleNames);
        $this->assertContains('test_rule_2', $ruleNames);
    }

    public function testArrayAccessCompatibility(): void
    {
        $rule = $this->client()->analyticsV2->rules()[$this->ruleName];
        $this->assertInstanceOf('Typesense\AnalyticsRuleV2', $rule);
        
        $this->assertTrue(isset($this->client()->analyticsV2->rules()[$this->ruleName]));
        
        $rule = $this->client()->analyticsV2->rules()[$this->ruleName];
        $this->assertInstanceOf('Typesense\AnalyticsRuleV2', $rule);
    }
} 