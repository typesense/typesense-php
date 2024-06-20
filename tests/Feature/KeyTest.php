<?php

namespace Feature;

use Tests\TestCase;
use Typesense\Exceptions\ObjectNotFound;


class KeyTest extends TestCase
{
    private $keyId = null;
    private $createKeyResponse = null;


    protected function setUp(): void
    {
        parent::setUp();

        $res =  $this->client()->keys->create([
            'description' => 'Admin key.',
            'actions' => ['*'],
            'collections' => ['*']
        ]);
        $this->createKeyResponse = $res;
        $this->keyId = $res['id'];
    }

    protected function tearDown(): void
    {
        $keys = $this->client()->keys->retrieve();
        foreach ($keys['keys'] as $key) {
            $this->client()->keys[$key['id']]->delete();
        }
    }

    public function testCanCreateAKey(): void
    {
        $this->assertEquals($this->createKeyResponse['description'], 'Admin key.');
    }

    public function testCanRetrieveAKey(): void
    {
        $key = $this->client()->keys[$this->keyId]->retrieve();

        $this->assertEquals($key['id'], $this->keyId);
    }

    public function testCanDeleteAKey(): void
    {
        $key = $this->client()->keys[$this->keyId]->delete();
        $this->assertEquals($key['id'], $this->keyId);

        $this->expectException(ObjectNotFound::class);
        $key = $this->client()->keys[$this->keyId]->retrieve();
    }

    public function testCanRetrieveAllKeys(): void
    {
        $keys = $this->client()->keys->retrieve();
        $this->assertCount(1, $keys['keys']);
    }

    public function testCanGenerateScopedSearchKey(): void
    {
        // The following keys were generated and verified to work with an actual Typesense server
        // We're only verifying that the algorithm works as expected client-side
        $searchKey = "RN23GFr1s6jQ9kgSNg2O7fYcAUXU7127";
        $scopedSearchKey =
            "SC9sT0hncHFwTHNFc3U3d3psRDZBUGNXQUViQUdDNmRHSmJFQnNnczJ4VT1STjIzeyJmaWx0ZXJfYnkiOiJjb21wYW55X2lkOjEyNCJ9";

        $result = $this->client()->keys->generateScopedSearchKey($searchKey, [
            "filter_by" => "company_id:124",
        ]);
        $this->assertEquals($scopedSearchKey, $result);
    }
}
