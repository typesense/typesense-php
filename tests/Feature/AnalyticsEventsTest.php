<?php

namespace Feature;

use Tests\TestCase;

class AnalyticsEventsTest extends TestCase
{
    private $ruleName = 'product_queries_aggregation';

    protected function setUp(): void
    {
        parent::setUp();
        $this->client()->collections->create([
            "name" => "products",
            "fields" => [
                [
                    "name" => "title",
                    "type" => "string"
                ],
                [
                    "name" => "popularity",
                    "type" => "int32",
                    "optional" => true
                ]
            ]
        ]);
        $this->client()->analytics->rules()->upsert($this->ruleName, [
            "name" => "products_popularity",
            "type" => "counter",
            "params" => [
                "source" => [
                    "collections" => [
                        "products"
                    ],
                    "events" => [
                        [
                            "type" => "click",
                            "weight" => 1,
                            "name" => "products_click_event"
                        ]
                    ]
                ],
                "destination" => [
                    "collection" => "products",
                    "counter_field" => "popularity"
                ]
            ]
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->client()->analytics->rules()->{'product_queries_aggregation'}->delete();
    }

    public function testCanCreateAnEvent(): void
    {
        $response = $this->client()->analytics->events()->create([
            "type" => "click",
            "name" => "products_click_event",
            "data" => [
                "q" => "nike shoes",
                "doc_id" => "1024",
                "user_id" => "111112"
            ]
        ]);
        $this->assertTrue($response['ok']);
    }
}
