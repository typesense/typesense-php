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
}
