<?php

namespace Feature;

use Tests\TestCase;

class AnalyticsEventsTest extends TestCase
{
    //* there is no method for sending events
    // public function testCanCreateAnEvent(): void
    // {
    //     $returnData = $this->client()->analytics->rules()->even
    //     $this->assertEquals($returnData['name'], $this->ruleName);
    // }

    public function testNeedImplementationForAnalyticsEvents(): void
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.',
        );
    }
}
