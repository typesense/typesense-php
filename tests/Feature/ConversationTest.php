<?php

namespace Tests\Feature;

use Tests\ConversationsTestCase;
use Typesense\Conversation;

class ConversationTest extends ConversationsTestCase
{
    private $id = '123';

    public function testCanRetrieveAConversation(): void
    {
        $this->mockApiCall()->allows()->get($this->endPointPath(), [])->andReturns([]);

        $response =  $this->mockConversations()[$this->id]->retrieve();
        $this->assertEquals([], $response);
    }

    public function testCanUpdateAConversation(): void
    {
        $data = [
            "ttl" => 3600
        ];
        $this->mockApiCall()->allows()->put($this->endPointPath(), $data)->andReturns([]);

        $response =  $this->mockConversations()[$this->id]->update($data);
        $this->assertEquals([], $response);
    }

    public function testCanDeleteAConversation(): void
    {
        $this->mockApiCall()->allows()->delete($this->endPointPath())->andReturns([]);

        $response =  $this->mockConversations()[$this->id]->delete();
        $this->assertEquals([], $response);
    }

    public function testPrivateFieldNamesResolveToConversations(): void
    {
        $conversations = $this->mockConversations();

        $this->assertInstanceOf(Conversation::class, $conversations->apiCall);
        $this->assertSame($conversations['apiCall'], $conversations->apiCall);
        $this->assertSame($conversations['individualConversations'], $conversations->individualConversations);
    }

    private function endPointPath(): string
    {
        return sprintf('%s/%s', "/conversations", $this->id);
    }
}
