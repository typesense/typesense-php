<?php

namespace Feature;

use Tests\TestCase;
use Typesense\Exceptions\ObjectNotFound;

class AliasTest extends TestCase
{
    private $sampleAliasResponse = [
        "name" => "companies",
        "collection_name" => "companies_june11",
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $aliasedCollection = [
            'collection_name' => 'companies_june11'
        ];
        $this->client()->aliases->upsert('companies', $aliasedCollection);
    }

    public function testCanRetrieveAlias(): void
    {
        $response = $this->client()->aliases['companies']->retrieve();
        $this->assertEquals($this->sampleAliasResponse, $response);
    }

    public function testCanDeleteAlias(): void
    {
        $response = $this->client()->aliases['companies']->delete();
        $this->assertEquals($this->sampleAliasResponse, $response);

        $this->expectException(ObjectNotFound::class);
        $this->client()->aliases['companies']->retrieve();
    }
}
