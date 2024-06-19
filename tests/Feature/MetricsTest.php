<?php

namespace Feature;

use Tests\TestCase;

class MetricsTest extends TestCase
{
    public function testCanRetrieveMetrics(): void
    {
        $returnData = $this->client()->metrics->retrieve();
        $this->assertArrayHasKey('system_memory_used_bytes', $returnData);
    }
}
