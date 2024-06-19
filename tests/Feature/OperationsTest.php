<?php

namespace Feature;

use Tests\TestCase;

class OperationsTest extends TestCase
{

    public function testCanCreateSnapshot(): void
    {
        $returnData = $this->client()->operations->perform("snapshot", ["snapshot_path" => "/tmp/typesense-data-snapshot"]);
        $this->assertIsBool($returnData['success']);
    }

    public function testCanReElectLeader(): void
    {
        $returnData = $this->client()->operations->perform("vote");
        $this->assertIsBool($returnData['success']);
    }
}
