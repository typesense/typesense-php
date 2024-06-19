<?php

namespace Feature;

use Tests\TestCase;

class DebugTest extends TestCase
{
    public function testCanRetrieveDebugInformation(): void
    {
        $returnData = $this->client()->debug->retrieve();
        $this->assertArrayHasKey('version', $returnData);
    }
}
