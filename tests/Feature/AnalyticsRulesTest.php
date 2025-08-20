<?php

namespace Feature;

use Tests\TestCase;
use Typesense\Exceptions\RequestMalformed;
use Exception;

class AnalyticsRulesTest extends TestCase
{
    private $ruleName = 'test__rule';
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
            $this->markTestSkipped('New Analytics API is not supported in Typesense 9.0 and below');
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
            $this->client()->analytics->rules()->create([$this->ruleConfiguration]);
        } catch (Exception $e) {
        }
    }

    protected function tearDown(): void
    {
        if ($this->isV30OrAbove()) {
            try {
                $rules = $this->client()->analytics->rules()->retrieve();
                if (is_array($rules)) {
                    foreach ($rules as $rule) {
                        if (strpos($rule['name'], 'test__') === 0) {
                            try {
                                $this->client()->analytics->rules()[$rule['name']]->delete();
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

    public function testCanCreateRulesWithAPI(): void
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

        $response = $this->client()->analytics->rules()->create($rules);
        $this->assertIsArray($response);
        
        $allRules = $this->client()->analytics->rules()->retrieve();
        $this->assertIsArray($allRules);
        
        $ruleNames = array_column($allRules, 'name');
        $this->assertContains('test_rule_1', $ruleNames);
        $this->assertContains('test_rule_2', $ruleNames);
    }

    public function testCanRetrieveARuleWithAPI(): void
    {
        $returnData = $this->client()->analytics->rules()[$this->ruleName]->retrieve();
        $this->assertEquals($this->ruleName, $returnData['name']);
        $this->assertEquals('counter', $returnData['type']);
        $this->assertEquals('test_products', $returnData['collection']);
    }

    public function testCanUpdateARuleWithAPI(): void
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

        $response = $this->client()->analytics->rules()[$this->ruleName]->update($updateParams);
        $this->assertEquals($this->ruleName, $response['name']);
        $this->assertEquals('updated_tag', $response['rule_tag']);
        $this->assertEquals(5, $response['params']['weight']);
    }

    public function testCanDeleteARuleWithAPI(): void
    {
        $returnData = $this->client()->analytics->rules()[$this->ruleName]->delete();
        $this->assertEquals($this->ruleName, $returnData['name']);

        $this->expectException(RequestMalformed::class);
        $this->client()->analytics->rules()[$this->ruleName]->retrieve();
    }

    public function testCanRetrieveAllRulesWithAPI(): void
    {
        $returnData = $this->client()->analytics->rules()->retrieve();
        $this->assertIsArray($returnData);
        $this->assertGreaterThanOrEqual(1, count($returnData));
        
        $ruleNames = array_column($returnData, 'name');
        $this->assertContains('test__rule', $ruleNames);
        $this->assertContains('test_rule_1', $ruleNames);
        $this->assertContains('test_rule_2', $ruleNames);
    }

    public function testArrayAccessCompatibility(): void
    {
        $rule = $this->client()->analytics->rules()[$this->ruleName];
        $this->assertInstanceOf('Typesense\AnalyticsRule', $rule);
        
        $this->assertTrue(isset($this->client()->analytics->rules()[$this->ruleName]));
        
        $rule = $this->client()->analytics->rules()[$this->ruleName];
        $this->assertInstanceOf('Typesense\AnalyticsRule', $rule);
    }
} 