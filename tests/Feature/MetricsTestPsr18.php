<?php

namespace Tests\Feature;

use Tests\TestCase;
use Http\Discovery\Psr18ClientDiscovery;

class MetricsTestPsr18 extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $client = (new Psr18ClientDiscovery())->find();
        $this->setUpTypesenseClient($client);
    }
    public function testCanRetrieveMetrics(): void
    {
        $returnData = $this->client()->metrics->retrieve();
        $this->assertArrayHasKey('system_memory_used_bytes', $returnData);
    }
}
