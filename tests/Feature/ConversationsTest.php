<?php

namespace Feature;

use Tests\ConversationsTestCase;

class ConversationsTest extends ConversationsTestCase
{
    public const RESOURCE_PATH = '/conversations';

    public function testCanRetrieveAllConversations(): void
    {
        $this->mockApiCall()->allows()->get(static::RESOURCE_PATH, [])->andReturns([]);
        $this->mockConversations()->retrieve();

        $response = $this->client()->conversations->retrieve();
        $this->assertEquals([], $response);
    }
}
