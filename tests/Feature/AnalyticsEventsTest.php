<?php

namespace Feature;

use Tests\TestCase;
use Exception;

class AnalyticsEventsTest extends TestCase
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
            $this->markTestSkipped('New Analytics API is not supported in Typesense 29.0 and below');
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

    public function testCanCreateEventsWithAPI(): void
    {
        $event = [
            "name" => $this->ruleName,
            "event_type" => "click",
            "data" => [
                "doc_ids" => ["1", "2"],
                "user_id" => "test_user"
            ]
        ];

        $response = $this->client()->analytics->events()->create($event);
        $this->assertIsArray($response);
    }

    public function testCanCreateMultipleEventsWithAPI(): void
    {
        $event1 = [
            "name" => $this->ruleName,
            "event_type" => "click",
            "data" => [
                "doc_id" => "1",
                "user_id" => "test_user_1"
            ]
        ];

        $event2 = [
            "name" => $this->ruleName,
            "event_type" => "click",
            "data" => [
                "doc_id" => "2",
                "user_id" => "test_user_2"
            ]
        ];

        $response1 = $this->client()->analytics->events()->create($event1);
        $this->assertIsArray($response1);

        $response2 = $this->client()->analytics->events()->create($event2);
        $this->assertIsArray($response2);
    }

    public function testCanRetrieveEventsWithAPI(): void
    {
        $event = [
            "name" => $this->ruleName,
            "event_type" => "click",
            "data" => [
                "doc_id" => "1",
                "user_id" => "test_user"
            ]
        ];
        
        $this->client()->analytics->events()->create($event);

        $response = $this->client()->analytics->events()->retrieve([
            'user_id' => 'test_user',
            'name' => $this->ruleName,
            'n'=> 10
        ]);

        $this->assertIsArray($response);
    }

    public function testCanCreateEventWithDifferentEventTypes(): void
    {
        $clickEvent = [
            "name" => $this->ruleName,
            "event_type" => "click",
            "data" => [
                "doc_id" => "1",
                "user_id" => "test_user"
            ]
        ];

        $conversionEvent = [
            "name" => $this->ruleName,
            "event_type" => "conversion",
            "data" => [
                "doc_id" => "1",
                "user_id" => "test_user"
            ]
        ];

        $clickResponse = $this->client()->analytics->events()->create($clickEvent);
        $this->assertIsArray($clickResponse);

        $conversionResponse = $this->client()->analytics->events()->create($conversionEvent);
        $this->assertIsArray($conversionResponse);
    }
} 