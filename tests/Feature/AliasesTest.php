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
    private $upsertResponse = null;

    protected function setUp(): void
    {
        parent::setUp();

        $aliasedCollection = [
            'collection_name' => 'companies_june11'
        ];
        $this->upsertResponse = $this->client()->aliases->upsert('companies', $aliasedCollection);
    }

    protected function tearDown(): void
    {
        $aliases = $this->client()->aliases->retrieve();
        foreach ($aliases['aliases'] as $alias) {
            $this->client()->aliases[$alias['name']]->delete();
        }
    }

    public function testCanUpsertAnAlias(): void
    {
        $this->assertEquals($this->sampleAliasResponse, $this->upsertResponse);
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

    public function testCanRetrieveAllAliases(): void
    {
        $response = $this->client()->aliases->retrieve();

        $this->assertEquals(['aliases' => [0 => $this->sampleAliasResponse]], $response);
    }
}
