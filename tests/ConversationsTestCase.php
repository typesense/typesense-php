<?php

namespace Tests;

use Tests\TestCase;
use Typesense\Conversations;

abstract class ConversationsTestCase extends TestCase
{

    private Conversations $mockConversations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockConversations = new Conversations(parent::mockApiCall());
    }

    protected function mockConversations(): Conversations
    {
        return $this->mockConversations;
    }
}
