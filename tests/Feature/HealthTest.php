<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthTest extends TestCase
{
    public function testCanRetrieveHealthInfo(): void
    {
        $response = $this->client()->health->retrieve();
        $this->assertIsBool($response['ok']);
    }
}
