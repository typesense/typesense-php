<?php

namespace Feature;

use Tests\TestCase;
use Typesense\Exceptions\RequestMalformed;

class MultiSearchTest extends TestCase
{
    private $searchRequests = [
        'searches' => [
            [
                'q' => 'book 1',
            ],
            [
                'q' => 'book 2'
            ]
        ]
    ];
    private $commonSearchParams =  [
        'query_by' => 'title',
        'collection' => 'books',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpCollection('books');
        $this->setUpDocuments('books');
    }

    public function testCanPerformAMultiSearch(): void
    {
        $returnData = $this->client()->multiSearch->perform($this->searchRequests, $this->commonSearchParams);
        $this->assertEquals(2, count($returnData['results']));
    }

    public function testCanLimitNumberOfRequestsInOneMultiSearch(): void
    {
        $searchRequests = [
            'searches' => [
                ...$this->searchRequests['searches'],
                [
                    'q' => 'book 3'
                ]
            ]
        ];

        $this->expectException(RequestMalformed::class);
        $this->client()->multiSearch->perform($searchRequests, [
            "limit_multi_searches" => 2,
            ...$this->commonSearchParams
        ]);
    }
}
