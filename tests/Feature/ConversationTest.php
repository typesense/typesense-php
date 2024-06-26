<?php

namespace Feature;

use Tests\ConversationsTestCase;

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

    private function endPointPath(): string
    {
        return sprintf('%s/%s', ConversationsTest::RESOURCE_PATH, $this->id);
    }
}
